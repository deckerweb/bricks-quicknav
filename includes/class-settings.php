<?php
/**
 * Host integration. @package BricksQuickNav
 * Adapted from Oxygen QuickNav 2.0.0, © 2025–2026 David Decker – DECKERWEB.
 * GPL-2.0-or-later. https://github.com/deckerweb/oxygen-quicknav/tree/v2.0.0
 */
namespace Deckerweb\BricksQuickNav;

defined( 'ABSPATH' ) || exit;

/** Validates settings, resolves legacy constants, and stores personal preferences. */
final class Settings {
	const OPTION = 'ddw_bxqn_settings';
	const USER_OPTION = 'ddw_bxqn_preferences';

	/** @return array Default website configuration; no database writes. */
	public static function defaults() {
		return array(
			'name' => 'Bricks', 'icon' => 'auto', 'limit' => 20, 'orderby' => 'modified',
			'backend' => true, 'frontend' => true, 'footer' => true, 'drafts' => false,
			'capability' => 'activate_plugins', 'users' => array(), 'post_types' => array( 'page' ),
			'groups' => array( 'content', 'templates', 'types', 'taxonomies', 'settings', 'code', 'frameworks', 'addons' ),
			'compact' => false, 'position' => 999, 'delete_data' => false, 'integrations' => array(), 'integration_direct' => array(), 'integration_children' => true,
		);
	}

	/**
	 * Validate an option array against the supported configuration.
	 * @param mixed $input Submitted or stored configuration.
	 * @return array Complete sanitized website configuration.
	 */
	public static function sanitize( $input ) {
		$input = is_array( $input ) ? $input : array();
		$out = self::defaults();
		foreach ( array( 'backend', 'frontend', 'footer', 'drafts', 'delete_data', 'integration_children', 'compact' ) as $key ) {
			$out[ $key ] = ! empty( $input[ $key ] );
		}
		$out['name'] = isset( $input['name'] ) && is_scalar( $input['name'] ) ? sanitize_text_field( (string) $input['name'] ) : 'Bricks';
		if ( '' === $out['name'] ) { $out['name'] = 'Bricks'; }
		$out['name'] = function_exists( 'mb_substr' ) ? mb_substr( $out['name'], 0, 40 ) : substr( $out['name'], 0, 40 );
		$out['position'] = isset( $input['position'] ) && is_scalar( $input['position'] ) ? min( 9999, max( 1, absint( $input['position'] ) ) ) : 999;
		$out['limit'] = isset( $input['limit'] ) && is_scalar( $input['limit'] ) ? min( 100, max( 1, absint( $input['limit'] ) ) ) : 20;
		$out['icon'] = in_array( $input['icon'] ?? '', array( 'auto', 'yellow', 'brick', 'bricks', 'none' ), true ) ? $input['icon'] : 'auto';
		$out['orderby'] = in_array( $input['orderby'] ?? '', array( 'modified', 'title' ), true ) ? $input['orderby'] : 'modified';
		$out['capability'] = isset( $input['capability'] ) && is_string( $input['capability'] ) ? sanitize_key( $input['capability'] ) : 'activate_plugins';
		if ( '' === $out['capability'] ) { $out['capability'] = 'activate_plugins'; }
		$users = $input['users'] ?? array();
		if ( is_string( $users ) ) { $users = preg_split( '/[\s,;]+/', $users ); }
		$out['users'] = is_array( $users ) ? array_values( array_unique( array_filter( array_map( 'absint', array_filter( $users, 'is_scalar' ) ) ) ) ) : array();
		$types = get_post_types( array( 'public' => true ), 'names' );
		$out['post_types'] = is_array( $input['post_types'] ?? null ) ? array_values( array_intersect( array_filter( $input['post_types'], 'is_string' ), $types ) ) : array( 'page' );
		$out['groups'] = is_array( $input['groups'] ?? null ) ? array_values( array_intersect( array_filter( $input['groups'], 'is_string' ), self::defaults()['groups'] ) ) : self::defaults()['groups'];
		foreach ( array( 'integrations', 'integration_direct' ) as $map ) {
		foreach ( is_array( $input[ $map ] ?? null ) ? array_slice( $input[ $map ], 0, 100, true ) : array() as $id => $enabled ) {
			if ( is_string( $id ) && sanitize_key( $id ) === $id && in_array( $enabled, array( true, false, 1, 0, '1', '0' ), true ) ) { $out[ $map ][ $id ] = (bool) $enabled; }
		}
		}
		return $out;
	}

	/**
	 * Resolve website values, user preferences, and immutable legacy overrides.
	 * @param bool $personal Whether to apply the current user's site-scoped preferences.
	 * @return array Effective settings, with constants taking final precedence.
	 */
	public static function get( $personal = true ) {
		$saved = get_option( self::OPTION, array() );
		$out = self::sanitize( array_merge( self::defaults(), is_array( $saved ) ? $saved : array() ) );
		if ( $personal ) {
			$prefs = get_user_option( self::USER_OPTION );
			if ( is_array( $prefs ) ) {
				foreach ( array( 'backend', 'frontend' ) as $key ) {
					if ( isset( $prefs[ $key ] ) && 'hide' === $prefs[ $key ] ) { $out[ $key ] = false; }
				}
				if ( isset( $prefs['limit'] ) && is_scalar( $prefs['limit'] ) && ! empty( $prefs['limit'] ) ) { $out['limit'] = min( 100, max( 1, absint( $prefs['limit'] ) ) ); }
			}
		}
		$map = self::constant_map();
		foreach ( $map as $key => $constant ) {
			if ( ! defined( $constant ) ) { continue; }
			$value = constant( $constant );
			if ( 'footer' === $key ) { $out['footer'] = 'yes' !== $value; }
			elseif ( 'users' === $key ) {
				$out['users'] = ( is_array( $value ) || is_scalar( $value ) ) ? array_values( array_filter( array_map( 'absint', array_filter( (array) $value, 'is_scalar' ) ) ) ) : array( -1 );
				// An explicitly empty legacy allowlist continues to deny every user.
				if ( empty( $out['users'] ) ) { $out['users'] = array( -1 ); }
			} elseif ( 'compact' === $key ) { $out['compact'] = (bool) $value; }
			elseif ( 'position' === $key ) { $out['position'] = is_scalar( $value ) ? min( 9999, max( 1, absint( $value ) ) ) : 999; }
			elseif ( 'limit' === $key ) { $out['limit'] = is_scalar( $value ) ? min( 100, max( 1, absint( $value ) ) ) : 20; }
			elseif ( is_scalar( $value ) ) {
				if ( 'icon' === $key ) { $out[ $key ] = in_array( $value, array( 'yellow', 'brick', 'bricks', 'none' ), true ) ? $value : 'auto'; }
				elseif ( 'capability' === $key ) { $out[ $key ] = sanitize_key( (string) $value ); }
				else { $out[ $key ] = sanitize_text_field( (string) $value ); }
			}
		}
		return $out;
	}

	/** @return array Setting keys mapped to supported legacy constants. */
	public static function constant_map() {
		return array( 'name' => 'BXQN_NAME_IN_ADMINBAR', 'icon' => 'BXQN_ICON', 'limit' => 'BXQN_NUMBER_TEMPLATES', 'capability' => 'BXQN_VIEW_CAPABILITY', 'users' => 'BXQN_ENABLED_USERS', 'footer' => 'BXQN_DISABLE_FOOTER', 'compact' => 'BXQN_COMPACT_MODE', 'position' => 'BXQN_MENU_POSITION' );
	}
}
