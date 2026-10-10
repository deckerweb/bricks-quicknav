<?php
/** Authorized staging of toolbar definitions. @package BricksQuickNav */
namespace Deckerweb\BricksQuickNav;
defined( 'ABSPATH' ) || exit;

/** Collects definitions before pruning forbidden or orphaned navigation. */
final class NodeCollection {
	/** @var array Nodes indexed by their stable menu IDs. */
	public $nodes = array();

	/**
	 * Add or update a node with native WP_Admin_Bar merge semantics.
	 * @param array $node Toolbar node definition.
	 * @return void Stages a node without changing the live toolbar.
	 */
	public function add_node( $node ) {
		if ( ! is_array( $node ) || empty( $node['id'] ) ) { return; }
		$this->nodes[ $node['id'] ] = array_merge( $this->nodes[ $node['id'] ] ?? array(), $node );
	}

	/**
	 * Stage a group using native toolbar grouping.
	 * @param array $node Toolbar group definition.
	 * @return void Adds a group marker to the collection.
	 */
	public function add_group( $node ) { $node['group'] = true; $this->add_node( $node ); }

	/**
	 * Match registered admin destinations to their actual capability.
	 * @param string $url Destination URL.
	 * @return bool Whether WordPress allows the current user to reach this destination.
	 */
	public static function allowed( $url ) {
		if ( ! $url || '#' === $url ) { return true; }
		$url = html_entity_decode( $url, ENT_QUOTES, 'UTF-8' );
		if ( 0 !== strpos( $url, admin_url() ) && 0 !== strpos( $url, network_admin_url() ) ) { return true; }
		parse_str( (string) wp_parse_url( $url, PHP_URL_QUERY ), $query );
		$file = basename( (string) wp_parse_url( $url, PHP_URL_PATH ) );
		if ( ! empty( $query['page'] ) ) {
			global $menu, $submenu;
			$lists = array_merge( array( is_array( $menu ) ? $menu : array() ), is_array( $submenu ) ? array_values( $submenu ) : array() );
			foreach ( $lists as $items ) {
				foreach ( $items as $item ) {
					if ( ( $item[2] ?? '' ) === $query['page'] ) { return current_user_can( $item[1] ); }
				}
			}
			// Frontend requests have no registered admin menu: conservative fallback.
			return current_user_can( 'manage_options' );
		}
		if ( 'post.php' === $file && ! empty( $query['post'] ) ) { return current_user_can( 'edit_post', absint( $query['post'] ) ); }
		if ( in_array( $file, array( 'edit.php', 'post-new.php' ), true ) ) {
			$object = get_post_type_object( $query['post_type'] ?? 'post' );
			return $object && current_user_can( 'post-new.php' === $file ? $object->cap->create_posts : $object->cap->edit_posts );
		}
		if ( 'edit-tags.php' === $file ) {
			$tax = get_taxonomy( $query['taxonomy'] ?? '' );
			return $tax && current_user_can( $tax->cap->manage_terms );
		}
		if ( 'theme-editor.php' === $file ) { return current_user_can( 'edit_themes' ) && ! ( defined( 'DISALLOW_FILE_EDIT' ) && DISALLOW_FILE_EDIT ) && ! ( defined( 'DISALLOW_FILE_MODS' ) && DISALLOW_FILE_MODS ); }
		if ( 'site-health.php' === $file ) { return current_user_can( 'view_site_health_checks' ); }
		return current_user_can( 'manage_options' );
	}

	/**
	 * Render the authorized collection, dropping descendants of rejected nodes.
	 * @param \WP_Admin_Bar $bar Live toolbar.
	 * @return void Adds only destinations with available ancestors.
	 */
	public function render( $bar ) {
		foreach ( $this->nodes as $id => $node ) {
			if ( ! self::allowed( $node['href'] ?? '' ) ) { unset( $this->nodes[ $id ] ); }
		}
		do {
			$changed = false;
			foreach ( $this->nodes as $id => $node ) {
				$parent = $node['parent'] ?? '';
				if ( $parent && ! isset( $this->nodes[ $parent ] ) && ! $bar->get_node( $parent ) ) { unset( $this->nodes[ $id ] ); $changed = true; }
			}
		} while ( $changed );
		foreach ( $this->nodes as $node ) {
			if ( ! empty( $node['group'] ) ) { $bar->add_group( $node ); } else { $bar->add_node( $node ); }
		}
	}
}
