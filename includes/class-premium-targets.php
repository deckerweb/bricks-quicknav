<?php
/** Version-aware destinations derived from supplied addon menu contracts. @package BricksQuickNav */
namespace Deckerweb\BricksQuickNav;
defined( 'ABSPATH' ) || exit;

/** Corrects navigation for GutenBricks generations and configurable premium menus. */
final class PremiumTargets {
	/**
	 * Resolve supplied addon versions without loading or copying their implementation.
	 * @return array Current detected premium versions keyed by existing integration ID.
	 */
	public static function versions() {
		$versions = array();
		foreach ( array( 'gutenbricks' => array( 'GUTENBRICKS_V2_VERSION', 'GUTENBRICKS_VERSION' ), 'bricksextras' => array( 'BRICKSEXTRAS_VERSION' ), 'swissknifeb' => array( 'SWISS_BRICKS_VERSION' ) ) as $id => $constants ) {
			foreach ( $constants as $constant ) { if ( defined( $constant ) && is_scalar( constant( $constant ) ) ) { $versions[ $id ] = (string) constant( $constant ); break; } }
		}
		if ( ! function_exists( 'get_plugins' ) ) { require_once ABSPATH . 'wp-admin/includes/plugin.php'; }
		$active = array_merge( (array) get_option( 'active_plugins', array() ), is_multisite() ? array_keys( (array) get_site_option( 'active_sitewide_plugins', array() ) ) : array() );
		foreach ( get_plugins() as $file => $plugin ) {
			if ( ! in_array( $file, $active, true ) ) { continue; }
			foreach ( array( 'bricksultimate' => 'bricksultimate', 'bricksforge' => 'bricksforge', 'gutenbricks' => 'gutenbricks', 'bricksextras' => 'bricksextras', 'swissknifeb' => 'swiss-knife-bricks' ) as $id => $domain ) {
				if ( ( $plugin['TextDomain'] ?? '' ) === $domain ) { $versions[ $id ] = $plugin['Version']; }
			}
		}
		return $versions;
	}

	/**
	 * Correct staged destinations while preserving established IDs and resource links.
	 * @param NodeCollection $nodes Detected addon navigation, modified in place.
	 * @return void Adapts real generation-specific routes and disabled license-dependent pages.
	 */
	public static function configure( $nodes ) {
		if ( isset( $nodes->nodes['bxqn-gutenbricks'] ) && defined( 'GUTENBRICKS_V2_VERSION' ) ) {
			$nodes->nodes['bxqn-gutenbricks']['href'] = admin_url( 'admin.php?page=gutenbricks-settings' );
			foreach ( array( 'bundles', 'gutenberg-settings', 'client-experience', 'integration' ) as $tab ) {
				$id = 'bxqn-gutenbricks-' . $tab;
				if ( isset( $nodes->nodes[ $id ] ) ) { $nodes->nodes[ $id ]['href'] = admin_url( 'admin.php?page=gutenbricks-settings#' . $tab ); }
			}
			$nodes->nodes['bxqn-gutenbricks-bundles']['title'] = esc_html__( 'General Settings', 'bricks-quicknav' );
			$nodes->nodes['bxqn-gutenbricks-license']['href'] = admin_url( 'admin.php?page=gutenbricks-manage-license' );
			$nodes->add_node( array( 'id' => 'bxqn-gutenbricks-block-manager', 'parent' => 'bxqn-gutenbricks', 'title' => esc_html__( 'Blocks', 'bricks-quicknav' ), 'href' => admin_url( 'admin.php?page=gutenbricks-settings#block-manager' ) ) );
		}
		if ( isset( $nodes->nodes['bxqn-bricksultimate'] ) ) {
			$options = get_option( 'buwl', array() );
			$slug = is_array( $options ) && is_string( $options['menu_slug'] ?? null ) && sanitize_key( $options['menu_slug'] ) === $options['menu_slug'] && '' !== $options['menu_slug'] ? $options['menu_slug'] : 'bricksultimate';
			foreach ( $nodes->nodes as $id => &$node ) {
				$url = html_entity_decode( $node['href'] ?? '', ENT_QUOTES, 'UTF-8' );
				parse_str( (string) wp_parse_url( $url, PHP_URL_QUERY ), $query );
				if ( ( $query['page'] ?? '' ) === 'bricksultimate' ) { $node['href'] = esc_url( add_query_arg( 'page', $slug, $url ) ); }
			}
			unset( $node );
			if ( is_array( $options ) && ! empty( $options['menu_name'] ) && is_string( $options['menu_name'] ) ) { $nodes->nodes['bxqn-bricksultimate']['title'] = esc_html( $options['menu_name'] ); }
		}
		if ( isset( $nodes->nodes['bxqn-bricksforge'] ) && defined( 'BRICKSFORGE_VERSION' ) && version_compare( BRICKSFORGE_VERSION, '4.0', '>=' ) ) {
			$nodes->add_node( array( 'id' => 'bxqn-bricksforge-logs', 'parent' => 'bxqn-bricksforge', 'title' => esc_html__( 'Logs', 'bricks-quicknav' ), 'href' => admin_url( 'admin.php?page=bricksforge#/logs' ) ) );
			$id = 'bxqn-bricksforge-extensions-backendDesigner';
			if ( isset( $nodes->nodes[ $id ] ) ) { $nodes->nodes[ $id ]['href'] = admin_url( 'admin.php?page=bricksforge#/backenddesigner' ); }
			$tools = get_option( 'brf_activated_tools', array() );
			foreach ( array( 'permissions' => 21, 'whitelabel' => 22, 'global-classes' => 23 ) as $route => $tool ) {
				if ( ! is_array( $tools ) || ! in_array( $tool, $tools ) ) { unset( $nodes->nodes[ 'bxqn-bricksforge-extensions-' . $route ] ); }
			}
			if ( is_array( $tools ) && in_array( 5, $tools ) ) { $nodes->add_node( array( 'id' => 'bxqn-bricksforge-extensions-popups', 'parent' => 'bxqn-bricksforge-extensions', 'title' => esc_html__( 'Popups', 'bricks-quicknav' ), 'href' => admin_url( 'admin.php?page=bricksforge#/popups' ) ) ); }
		}
		if ( isset( $nodes->nodes['bxqn-bricksextras'] ) && class_exists( '\\BricksExtras\\Plugin' ) && version_compare( \BricksExtras\Plugin::VERSION, '1.7.6', '>=' ) ) {
			foreach ( array( 'api_keys' => __( 'API keys', 'bricks-quicknav' ), 'ai_skills' => __( 'AI Skills', 'bricks-quicknav' ), 'support' => __( 'Support', 'bricks-quicknav' ) ) as $tab => $title ) {
				$nodes->add_node( array( 'id' => 'bxqn-bricksextras-' . $tab, 'parent' => 'bxqn-bricksextras', 'title' => esc_html( $title ), 'href' => admin_url( 'admin.php?page=bricksextras_menu&tab=' . $tab ) ) );
			}
		}
		if ( isset( $nodes->nodes['bxqn-swissknifeb'] ) && defined( 'SWISS_BRICKS_VERSION' ) && version_compare( SWISS_BRICKS_VERSION, '1.1.1', '>=' ) ) {
			if ( 'valid' !== get_option( 'swiss_knifeb_license_status' ) ) {
				unset( $nodes->nodes['bxqn-swissknifeb-swiss-bricks-scripts'], $nodes->nodes['bxqn-swissknifeb-swiss-bricks-dumper'] );
			} elseif ( 'no' !== get_option( 'swissb_scripts_manager' ) ) {
				$nodes->add_node( array( 'id' => 'bxqn-swissknifeb-swiss-bricks-scripts', 'parent' => 'bxqn-swissknifeb', 'title' => esc_html__( 'Scripts Manager', 'bricks-quicknav' ), 'href' => admin_url( 'admin.php?page=swiss-bricks-scripts' ) ) );
			}
		}
	}
}
