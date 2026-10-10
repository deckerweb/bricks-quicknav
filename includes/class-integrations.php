<?php
/** Per-addon navigation configuration. @package BricksQuickNav */
namespace Deckerweb\BricksQuickNav;
defined( 'ABSPATH' ) || exit;

/** Describes retained integrations and applies website-scoped display preferences. */
final class Integrations {
	/** @return array Supported addon labels indexed by stable root IDs. */
	public static function base_catalog() {
		$catalog = array(
			'core-framework' => 'Core Framework', 'automatic-css' => 'Automatic.CSS', 'frames' => 'Frames', 'windpress' => 'WindPress', 'brixies' => 'Brixies',
			'snnbrx' => Snn::active() ? Snn::label() : 'SNN-BRX', 'yabewebfont' => 'Yabe Webfont', 'gutenbricks' => 'Gutenbricks', 'bricksforge' => 'Bricksforge', 'bricksextras' => 'BricksExtras',
			'bricksultimate' => 'Bricks Ultimate', 'bricksable' => 'Bricksable', 'swissknifeb' => 'Swiss Knife for Bricks', 'maxaddons' => 'Max Addons',
			'flexaddons' => 'Bricks Flex Addons', 'belementmanager' => 'Bricks Element Manager', 'beasydash' => 'Bricks Easy Dashboard', 'badmindashboard' => 'Bricks Admin Dashboard', 'btemplatesync' => 'Bricks Remote Sync',
			'snippet-code-snippets' => 'Code Snippets', 'snippet-advanced-scripts' => 'Advanced Scripts', 'snippet-scripts-organizer' => 'Scripts Organizer', 'snippet-wpcodebox' => 'WPCodeBox', 'snippet-fluentsnippets' => 'FluentSnippets', 'snippet-wpcode-lite' => 'WPCode Lite',
		);
		foreach ( PublicAddons::definitions() as $id => $definition ) { $catalog[ $id ] = $definition['label']; }
		return $catalog;
	}

	/** @return array All registered integration labels keyed by stable ID. */
	public static function catalog() {
		$catalog = array();
		foreach ( AddonRegistry::definitions() as $id => $definition ) { $catalog[ $id ] = $definition['label']; }
		return $catalog;
	}

	/**
	 * Resolve an addon visibility choice with existing integrations enabled by default.
	 * @param string $id Addon identifier.
	 * @param array $settings Effective website settings.
	 * @return bool Whether the addon navigation is enabled.
	 */
	public static function enabled( $id, $settings ) { return $settings['integrations'][ $id ] ?? true; }

	/**
	 * Describe detected integration destinations without making external requests.
	 * @return array Available and unavailable integration rows for the settings page.
	 */
	public static function rows() {
		$nodes = ( new Toolbar() )->integration_nodes()->nodes;
		if ( ! function_exists( 'get_plugins' ) ) { require_once ABSPATH . 'wp-admin/includes/plugin.php'; }
		$installed = get_plugins();
		$rows = array();
		$definitions = AddonRegistry::definitions();
		$premium_versions = PremiumTargets::versions();
		$public_installed = PublicAddons::installed();
		foreach ( self::catalog() as $id => $label ) {
			$node = $nodes[ 'bxqn-' . $id ] ?? null;
			$version = $premium_versions[ $id ] ?? ''; $present = false;
			if ( 'snnbrx' === $id && Snn::active() ) { $version = wp_get_theme()->get( 'Version' ); }
			foreach ( $installed as $metadata ) {
				$normalize = static function ( $text ) { return strtolower( preg_replace( '/[^a-z0-9]/i', '', $text ) ); };
				if ( $normalize( $label ) === $normalize( $metadata['Name'] ) ) { $present = true; $version = $metadata['Version']; break; }
			}
			$public = $public_installed[ $id ] ?? null;
			if ( $public ) { $version = $public['Version']; $present = true; }
			if ( empty( $definitions[ $id ]['builtin'] ) ) { $present = true === ( $definitions[ $id ]['loaded'] ?? false ); $version = is_string( $definitions[ $id ]['version'] ?? null ) ? $definitions[ $id ]['version'] : ''; }
			$rows[ $id ] = array( 'label' => $label, 'version' => $version, 'legacy' => ! empty( $definitions[ $id ]['builtin'] ) && ! isset( PublicAddons::definitions()[ $id ] ), 'pending' => false, 'status' => $node ? 'active' : ( $public && ! $public['verified'] ? 'unverified' : ( $present ? 'inactive' : 'missing' ) ), 'links' => $node && ! empty( $node['href'] ) && NodeCollection::allowed( $node['href'] ) ? array( array( 'url' => $node['href'] ) ) : array() );
		}
		return $rows;
	}

	/**
	 * Translate discovery states consistently for the host UI.
	 * @param string $status Detected addon state.
	 * @return string Translated state label.
	 */
	public static function status_label( $status ) {
		if ( 'unverified' === $status ) { return __( 'Version not verified', 'bricks-quicknav' ); }
		if ( 'active' === $status ) { return __( 'Active', 'bricks-quicknav' ); }
		return 'inactive' === $status ? __( 'Installed but inactive', 'bricks-quicknav' ) : __( 'Not installed', 'bricks-quicknav' );
	}

	/**
	 * Apply group, addon, direct-link, and submenu settings to staged definitions.
	 * @param NodeCollection $nodes Detected navigation collection, modified in place.
	 * @param array $settings Effective settings.
	 * @return void Removes disabled trees and moves requested addon roots.
	 */
	public static function configure( $nodes, $settings ) {
		$remove = array();
		if ( Snn::active() && ! self::enabled( 'snnbrx', $settings ) ) { $remove[] = 'bxqn-child-theme'; }
		foreach ( array( 'code' => 'customcode', 'frameworks' => 'frameworks', 'addons' => 'plugins' ) as $group => $suffix ) {
			if ( ! in_array( $group, $settings['groups'], true ) ) { $remove[] = 'bxqn-group-' . $suffix; }
		}
		foreach ( self::catalog() as $id => $label ) {
			$root = 'bxqn-' . $id;
			if ( ! isset( $nodes->nodes[ $root ] ) ) { continue; }
			if ( ! self::enabled( $id, $settings ) ) { $remove[] = $root; continue; }
			// Group visibility always wins over direct-link preferences.
			if ( in_array( $nodes->nodes[ $root ]['parent'] ?? '', $remove, true ) ) { $remove[] = $root; continue; }
			if ( 'snnbrx' !== $id && ! empty( $settings['integration_direct'][ $id ] ) ) { $nodes->nodes[ $root ]['parent'] = 'ddw-bricks-quicknav'; }
			if ( 'snnbrx' !== $id && ! $settings['integration_children'] ) {
				foreach ( $nodes->nodes as $child => $node ) { if ( ( $node['parent'] ?? '' ) === $root ) { $remove[] = $child; } }
			}
		}
		do {
			$before = count( $remove );
			foreach ( $nodes->nodes as $id => $node ) {
				if ( in_array( $node['parent'] ?? '', $remove, true ) && ! in_array( $id, $remove, true ) ) { $remove[] = $id; }
			}
		} while ( count( $remove ) !== $before );
		foreach ( $remove as $id ) { unset( $nodes->nodes[ $id ] ); }
		foreach ( $nodes->nodes as $node ) {
			if ( 'bxqn-group-plugins' === ( $node['parent'] ?? '' ) && NodeCollection::allowed( $node['href'] ?? '' ) ) {
				$nodes->add_node( array( 'id' => 'bxqn-addons', 'parent' => 'ddw-bricks-quicknav', 'title' => esc_html__( 'Add-ons', 'bricks-quicknav' ), 'href' => '#' ) );
				$nodes->nodes['bxqn-group-plugins']['parent'] = 'bxqn-addons';
				break;
			}
		}
	}
}
