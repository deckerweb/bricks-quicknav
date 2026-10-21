<?php
/** Scoped uninstall; Bricks and other plugins' data always remain. @package BricksQuickNav */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) { exit; }

delete_site_transient( 'ddw_ghru_' . substr( md5( 'https://github.com/deckerweb/bricks-quicknav' ), 0, 24 ) );
require_once __DIR__ . '/includes/deckerweb-plugin-library/lifecycle.php';
deckerweb_library_uninstall_v3( __DIR__ . '/bricks-quicknav.php' );

/**
 * Remove opted-in website data with all helper state confined to this operation.
 * @return void Preserves other plugins, shared users, and the caller's blog context.
 */
( static function () {
	/**
	 * Remove one website's settings only when that website opted in.
	 * @return void Deletes QuickNav preferences, never Bricks content or shared accounts.
	 */
	$remove_site = static function () {
		$settings = get_option( 'ddw_bxqn_settings', array() );
		if ( ! empty( $settings['delete_data'] ) ) {
			delete_option( 'ddw_bxqn_settings' );
			$meta_key = $GLOBALS['wpdb']->get_blog_prefix( get_current_blog_id() ) . 'ddw_bxqn_preferences';
			delete_metadata( 'user', 0, $meta_key, '', true );
		}
	};
	if ( ! is_multisite() ) { $remove_site(); return; }
	$offset = 0;
	do {
		$sites = get_sites( array( 'fields' => 'ids', 'number' => 100, 'offset' => $offset ) );
		foreach ( $sites as $site ) {
			switch_to_blog( $site );
			try { $remove_site(); } finally { restore_current_blog(); }
		}
		$offset += 100;
	} while ( count( $sites ) === 100 );
} )();
