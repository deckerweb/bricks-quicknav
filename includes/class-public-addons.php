<?php
/** Source-verified public addon navigation. @package BricksQuickNav */
namespace Deckerweb\BricksQuickNav;
defined( 'ABSPATH' ) || exit;

/** Resolves installed addon identity and uses its native admin menu permissions. */
final class PublicAddons {
	/** @return array Reviewed addon identities, versions, and native admin destinations. */
	public static function definitions() {
		return array(
			'sityn-enhancer' => array( 'label' => 'Sityn Enhancer for Bricks', 'domain' => 'sityn-enhancer-for-bricks-builder', 'version' => '1.2', 'menu' => 'sityn-enhancer-for-bricks-builder', 'file' => 'admin.php', 'capability' => 'manage_options' ),
			'webhook-forms' => array( 'label' => 'Webhook for Bricks Forms', 'domain' => 'webhook-for-bricks-forms', 'version' => '2.0.6', 'menu' => 'webhook_for_forms', 'file' => 'admin.php', 'capability' => 'manage_options' ),
			'reviso' => array( 'label' => 'Reviso', 'domain' => 'reviso-for-bricks', 'version' => '1.7.1', 'menu' => 'reviso-for-bricks', 'file' => 'admin.php', 'capability' => 'manage_options' ),
			'email-reports' => array( 'label' => 'Email Reports for Bricks Forms', 'domain' => 'email-reports-for-bricks-builder-form-submissions', 'version' => '1.0.0', 'menu' => 'bwsr-settings', 'file' => 'admin.php', 'capability' => 'manage_options' ),
			'zehoro-toolkit' => array( 'label' => 'Zehoro Toolkit', 'domain' => 'zehoro-toolkit', 'version' => '1.37.0', 'menu' => 'zehoro-dashboard', 'file' => 'admin.php', 'capability' => 'manage_options' ),
			'geo-targeting' => array( 'label' => 'Geo Targeting for Bricks', 'domain' => 'geo-targeting-for-bricks-builder', 'version' => '1.5.1', 'menu' => 'bgeo-settings', 'file' => 'admin.php', 'capability' => 'edit_theme_options' ),
			'custom-animations' => array( 'label' => 'Custom Animations for Bricks', 'domain' => 'custom-animations-for-bricks', 'version' => '1.2.0', 'menu' => 'bricks-custanimforbx-animations', 'file' => 'admin.php', 'capability' => 'manage_options' ),
			'yabe-bricksbender' => array( 'label' => 'Yabe Bricksbender', 'domain' => 'yabe-bricksbender', 'version' => '2.0.1', 'menu' => 'bricksbender', 'file' => 'admin.php', 'capability' => 'manage_options' ),
			'uichemy' => array( 'label' => 'UiChemy', 'domain' => 'uichemy', 'version' => '5.2.5', 'menu' => 'uichemy', 'file' => 'admin.php', 'capability' => 'manage_options' ),
			'bricks-import-export' => array( 'label' => 'Bricks Import & Export', 'domain' => 'bricks-ie', 'version' => '1.1.5', 'menu' => 'bricks-import-export', 'file' => 'admin.php', 'capability' => 'manage_options' ),
			'bricks-api-bridge' => array( 'label' => 'Bricks API Bridge', 'domain' => 'bricks-api-bridge', 'version' => '1.5.0', 'menu' => 'bricks-mcp-connect', 'file' => 'admin.php', 'capability' => 'manage_options' ),
			'bricks-mailerlite' => array( 'label' => 'Bricks MailerLite', 'domain' => 'bricks-mailerlite', 'version' => '1.0.0', 'menu' => 'bricks-mailerlite', 'file' => 'options-general.php', 'capability' => 'manage_options' ),
		);
	}

	/**
	 * Describe installed copies by their real headers, including renamed directories.
	 * @return array Metadata keyed by supported integration ID; no remote requests.
	 */
	public static function installed() {
		if ( ! function_exists( 'get_plugins' ) ) { require_once ABSPATH . 'wp-admin/includes/plugin.php'; }
		$active = (array) get_option( 'active_plugins', array() );
		if ( is_multisite() ) { $active = array_merge( $active, array_keys( (array) get_site_option( 'active_sitewide_plugins', array() ) ) ); }
		$result = array();
		foreach ( get_plugins() as $file => $plugin ) {
			foreach ( self::definitions() as $id => $definition ) {
				if ( ( $plugin['TextDomain'] ?? '' ) !== $definition['domain'] ) { continue; }
				$plugin['active'] = in_array( $file, $active, true );
				$plugin['verified'] = $plugin['Version'] === $definition['version'];
				$plugin['file'] = $file;
				$result[ $id ] = $plugin;
			}
		}
		return $result;
	}

	/**
	 * Add detected addons using their registered menu tree or a reviewed frontend URL.
	 * @param NodeCollection $nodes Staged toolbar definitions.
	 * @return void Adds authorized native destinations for verified active addon versions.
	 */
	public static function add( $nodes ) {
		$installed = self::installed();
		global $menu, $submenu;
		$lists = array_merge( array( is_array( $menu ) ? $menu : array() ), is_array( $submenu ) ? array_values( $submenu ) : array() );
		foreach ( self::definitions() as $id => $definition ) {
			if ( empty( $installed[ $id ]['active'] ) || empty( $installed[ $id ]['verified'] ) ) { continue; }
			$capability = $definition['capability']; $registered = false;
			foreach ( $lists as $items ) {
				foreach ( $items as $item ) {
					if ( ( $item[2] ?? '' ) === $definition['menu'] ) { $capability = $item[1]; $registered = true; break 2; }
				}
			}
			if ( ! current_user_can( $capability ) || ( is_admin() && ! $registered ) ) { continue; }
			$root = 'bxqn-' . $id;
			$nodes->add_node( array( 'id' => $root, 'parent' => 'bxqn-group-plugins', 'title' => esc_html( $definition['label'] ), 'href' => add_query_arg( 'page', $definition['menu'], admin_url( $definition['file'] ) ) ) );
			foreach ( $submenu[ $definition['menu'] ] ?? array() as $item ) {
				$slug = $item[2] ?? '';
				if ( ! is_string( $slug ) || $slug === $definition['menu'] || sanitize_key( $slug ) !== $slug || ! current_user_can( $item[1] ) ) { continue; }
				$nodes->add_node( array( 'id' => $root . '-' . $slug, 'parent' => $root, 'title' => esc_html( wp_strip_all_tags( $item[0] ) ), 'href' => add_query_arg( 'page', $slug, admin_url( 'admin.php' ) ) ) );
			}
		}
	}
}
