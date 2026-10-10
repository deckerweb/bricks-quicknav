<?php
/** Navigation adapter for the active SNN-BRX child theme. @package BricksQuickNav */
namespace Deckerweb\BricksQuickNav;
defined( 'ABSPATH' ) || exit;

/** Uses SNN's feature and menu registration rather than guessing enabled pages. */
final class Snn {
	/** @return bool Whether SNN's native code-snippet module is enabled. */
	public static function snippets_enabled() {
		return self::active() && ( ! function_exists( 'snn_feature_enabled' ) || snn_feature_enabled( 'snn-custom-codes-snippets' ) );
	}
	/** @return bool Whether SNN's main menu callback belongs to the active Bricks child theme. */
	public static function active() {
		if ( ! is_child_theme() || ! Builder::active() ) { return false; }
		$theme = wp_get_theme();
		if ( function_exists( 'snn_add_menu_page' ) ) {
			$function = new \ReflectionFunction( 'snn_add_menu_page' );
			$file = $function->getFileName();
			$directory = realpath( get_stylesheet_directory() );
			return $directory && $file && 0 === strpos( realpath( $file ), $directory . DIRECTORY_SEPARATOR );
		}
		return in_array( $theme->get( 'Name' ), array( 'SNN BRX - Advanced Bricks Builder Child Theme', 'SNN-BRX - Bricks Builder Child Theme' ), true );
	}

	/** @return string Native custom admin name, or the concise default SNN-BRX name. */
	public static function label() {
		$title = get_option( 'snn_menu_title', '' );
		return is_string( $title ) && '' !== trim( $title ) && ! in_array( $title, array( 'SNN Settings', __( 'SNN Settings', 'snn' ) ), true ) ? $title : 'SNN-BRX';
	}

	/** @return string Authorized active child-theme functions.php editor URL, or empty string. */
	public static function child_theme_url() {
		if ( ! is_child_theme() || ! current_user_can( 'edit_themes' ) || ! wp_is_file_mod_allowed( 'theme_editor' ) || ( defined( 'DISALLOW_FILE_EDIT' ) && DISALLOW_FILE_EDIT ) || ! is_file( get_stylesheet_directory() . '/functions.php' ) ) { return ''; }
		return add_query_arg( array( 'file' => 'functions.php', 'theme' => get_stylesheet() ), is_multisite() ? network_admin_url( 'theme-editor.php' ) : admin_url( 'theme-editor.php' ) );
	}

	/**
	 * Resolve enabled native SNN features with compatibility for its original route names.
	 * @return array Labels and route metadata keyed by SNN feature IDs.
	 */
	private static function features() {
		if ( function_exists( 'snn_get_features' ) ) {
			$features = snn_get_features();
			$features = is_array( $features ) ? $features : array();
		} else {
			$features = array();
			foreach ( array(
				'snn-custom-codes-snippets' => __( 'Code Snippets', 'bricks-quicknav' ), 'snn-other-settings' => __( 'Other Settings', 'bricks-quicknav' ),
				'editor-settings' => __( 'Editor Settings', 'bricks-quicknav' ), 'snn-security' => __( 'Security Settings', 'bricks-quicknav' ),
				'snn-custom-post-types' => __( 'Post Types', 'bricks-quicknav' ), 'snn-custom-fields' => __( 'Custom Fields', 'bricks-quicknav' ),
				'snn-taxonomies' => __( 'Taxonomies', 'bricks-quicknav' ), 'login-settings' => __( 'Login Settings', 'bricks-quicknav' ),
				'snn-404-logs' => __( '404 Logs', 'bricks-quicknav' ), 'snn-search-logs' => __( 'Search Logs', 'bricks-quicknav' ),
				'snn-301-redirects' => __( '301 Redirects', 'bricks-quicknav' ), 'smtp-settings' => __( 'Mail SMTP Settings', 'bricks-quicknav' ),
				'snn-mail-logs' => __( 'Mail Logs', 'bricks-quicknav' ), 'snn-media-settings' => __( 'Media Settings', 'bricks-quicknav' ),
				'snn-cookie-settings' => __( 'Cookie Settings', 'bricks-quicknav' ), 'snn-accessibility-settings' => __( 'Accessibility Settings', 'bricks-quicknav' ),
				'snn-block-editor-settings' => __( 'Block Editor Settings', 'bricks-quicknav' ),
			) as $slug => $label ) { $features[ $slug ] = array( 'label' => $label ); }
		}
		return $features;
	}

	/**
	 * Add enabled SNN pages and safe read-only snippet shortcuts.
	 * @param NodeCollection $nodes Staged navigation collection.
	 * @return void Adds the original SNN root and its current authorized destinations.
	 */
	public static function add( $nodes ) {
		if ( ! self::active() || ! current_user_can( 'manage_options' ) ) { return; }
		$nodes->add_node( array( 'id' => 'bxqn-snnbrx', 'title' => esc_html( self::label() ), 'href' => admin_url( self::snippets_enabled() ? 'admin.php?page=snn-custom-codes-snippets' : 'admin.php?page=snn-settings' ), 'parent' => 'bxqn-group-child-theme' ) );
		$seen = array();
		global $submenu;
		foreach ( $submenu['snn-settings'] ?? array() as $menu ) {
			if ( ! is_string( $menu[2] ?? null ) || 'snn-settings' === $menu[2] || ! current_user_can( $menu[1] ) ) { continue; }
			$slug = $menu[2];
			if ( sanitize_key( $slug ) !== $slug ) { continue; }
			$nodes->add_node( array( 'id' => 'bxqn-snnbrx-' . $slug, 'title' => esc_html( wp_strip_all_tags( $menu[0] ) ), 'href' => add_query_arg( 'page', $slug, admin_url( 'admin.php' ) ), 'parent' => 'bxqn-snnbrx' ) );
			$seen[ $slug ] = true;
		}
		foreach ( self::features() as $slug => $feature ) {
			if ( ! is_string( $slug ) || sanitize_key( $slug ) !== $slug || 'snn-settings' === $slug || isset( $seen[ $slug ] ) ) { continue; }
			if ( function_exists( 'snn_feature_enabled' ) && ! snn_feature_enabled( $slug ) ) { continue; }
			// Admin requests use registered pages exclusively; the frontend uses SNN's enabled registry.
			if ( is_admin() && isset( $submenu['snn-settings'] ) ) { continue; }
			if ( 'snn-role-management' === $slug && ! current_user_can( 'manage_snn_roles' ) ) { continue; }
			$label = $feature['label'] ?? $slug;
			if ( ! is_string( $label ) ) { continue; }
			$nodes->add_node( array( 'id' => 'bxqn-snnbrx-' . $slug, 'parent' => 'bxqn-snnbrx', 'title' => esc_html( $label ), 'href' => add_query_arg( 'page', $slug, admin_url( 'admin.php' ) ) ) );
		}
		// SNN's image optimization screen is registered under Media, outside its main registry.
		foreach ( $submenu['upload.php'] ?? array() as $item ) {
			if ( 'snn-image-optimization' === ( $item[2] ?? '' ) && current_user_can( $item[1] ) ) {
				$nodes->add_node( array( 'id' => 'bxqn-snnbrx-snn-image-optimization', 'parent' => 'bxqn-snnbrx', 'title' => esc_html( wp_strip_all_tags( $item[0] ) ), 'href' => admin_url( 'upload.php?page=snn-image-optimization' ) ) );
			}
		}
		if ( ! is_admin() && function_exists( 'snn_image_optimization_page' ) && current_user_can( 'upload_files' ) ) {
			$nodes->add_node( array( 'id' => 'bxqn-snnbrx-snn-image-optimization', 'parent' => 'bxqn-snnbrx', 'title' => esc_html__( 'Image optimization', 'bricks-quicknav' ), 'href' => admin_url( 'upload.php?page=snn-image-optimization' ) ) );
		}
		foreach ( array( 'docs' => array( __( 'Documentation', 'bricks-quicknav' ), 'https://sinanisler.com/snn-brx/' ), 'ghdiscussions' => array( __( 'GitHub Discussions', 'bricks-quicknav' ), 'https://github.com/sinanisler/snn-brx-child-theme/discussions' ) ) as $key => $link ) {
			$nodes->add_node( array( 'id' => 'bxqn-snnbrx-' . $key, 'parent' => 'bxqn-snnbrx', 'title' => esc_html( $link[0] ), 'href' => $link[1], 'meta' => array( 'target' => '_blank', 'rel' => 'noopener noreferrer' ) ) );
		}
		$root = 'bxqn-snnbrx-snn-custom-codes-snippets';
		if ( ! isset( $nodes->nodes[ $root ] ) ) { return; }
		$tabs = array( 'frontend' => __( 'Frontend Head PHP/HTML', 'bricks-quicknav' ), 'footer' => __( 'Frontend Footer PHP/HTML', 'bricks-quicknav' ), 'admin' => __( 'Admin Head PHP/HTML', 'bricks-quicknav' ), 'functions' => __( 'Direct PHP (functions.php style)', 'bricks-quicknav' ), 'error_logs' => __( 'Error Logs', 'bricks-quicknav' ) );
		foreach ( $tabs as $tab => $label ) {
			$url = add_query_arg( array( 'page' => 'snn-custom-codes-snippets', 'tab' => $tab ), admin_url( 'admin.php' ) );
			$nodes->add_node( array( 'id' => 'bxqn-snnbrx-snippets-' . $tab, 'parent' => $root, 'title' => esc_html( $label ), 'href' => $url ) );
			if ( isset( $nodes->nodes['bxqn-group-hook-snnbrx'] ) ) { $nodes->add_node( array( 'id' => 'bxqn-child-theme-snnbrx-' . $tab, 'parent' => 'bxqn-group-hook-snnbrx', 'title' => esc_html( $label ), 'href' => $url ) ); }
		}
		if ( ! function_exists( 'snn_snippet_modern_id' ) || ! function_exists( 'snn_snippet_edit_url' ) || ! post_type_exists( 'snn_code_snippet' ) ) { return; }
		$nodes->add_node( array( 'id' => 'bxqn-snnbrx-snippets-new', 'parent' => $root, 'title' => esc_html__( 'New Snippet', 'bricks-quicknav' ), 'href' => admin_url( 'admin.php?page=snn-custom-codes-snippets&view=new' ) ) );
	}

	/**
	 * Add modern SNN content only when its visible navigation is requested.
	 * @param NodeCollection $nodes Staged collection with the native SNN snippet root.
	 * @return void Adds bounded, individually authorized snippet shortcuts.
	 */
	public static function content( $nodes ) {
		$settings = Settings::get();
		$root = 'bxqn-snnbrx-snn-custom-codes-snippets';
		if ( ! isset( $nodes->nodes[ $root ] ) || ! function_exists( 'snn_snippet_edit_url' ) || ! post_type_exists( 'snn_code_snippet' ) || ! Integrations::enabled( 'snnbrx', $settings ) ) { return; }
		$posts = get_posts( array( 'post_type' => 'snn_code_snippet', 'posts_per_page' => $settings['limit'], 'post_status' => array( 'publish', 'private', 'draft' ), 'orderby' => 'modified', 'order' => 'DESC' ) );
		foreach ( $posts as $post ) {
			if ( ! current_user_can( 'edit_post', $post->ID ) ) { continue; }
			$url = snn_snippet_edit_url( 'snn-code-' . $post->ID );
			if ( ! is_string( $url ) || ! $url ) { continue; }
			$nodes->add_node( array( 'id' => 'bxqn-snnbrx-snippet-' . $post->ID, 'parent' => $root, 'title' => esc_html( trim( $post->post_title ) ?: __( '(no title)', 'bricks-quicknav' ) ), 'href' => esc_url( $url ) ) );
		}
	}
}
