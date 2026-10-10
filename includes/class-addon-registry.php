<?php
/** Shared addon registration contract. @package BricksQuickNav */
namespace Deckerweb\BricksQuickNav;
defined( 'ABSPATH' ) || exit;

/** Exposes built-in and external integrations through one configurable registry. */
final class AddonRegistry {
	/** @return array Validated built-in and externally registered addon definitions. */
	public static function definitions() {
		$definitions = array();
		foreach ( Integrations::base_catalog() as $id => $label ) { $definitions[ $id ] = array( 'label' => $label, 'builtin' => true ); }
		/**
		 * Register addons or adjust built-in labels; return definitions indexed by unique ID.
		 * External definitions require label, loaded (bool), and a target (page, file, capability).
		 * Optional children use the same target shape plus label; version is display metadata.
		 * @since 2.0.0
		 * @param array $definitions Built-in and external integration definitions.
		 */
		$filtered = apply_filters( 'ddw/quicknav/bricks_integrations', $definitions );
		if ( ! is_array( $filtered ) ) { return $definitions; }
		foreach ( $filtered as $id => $definition ) {
			if ( ! is_string( $id ) || '' === $id || sanitize_key( $id ) !== $id || ! is_array( $definition ) || ! is_string( $definition['label'] ?? null ) || '' === trim( $definition['label'] ) ) { unset( $filtered[ $id ] ); continue; }
			$filtered[ $id ]['builtin'] = isset( $definitions[ $id ] );
		}
		return array_replace( $definitions, $filtered );
	}

	/**
	 * Resolve a declared local admin target against native menu permissions.
	 * @param array $target Page slug, admin file and fallback capability.
	 * @return string Authorized URL, or empty string for invalid/unavailable targets.
	 */
	public static function target_url( $target ) {
		if ( ! is_array( $target ) || ! is_string( $target['page'] ?? null ) || '' === $target['page'] || sanitize_key( $target['page'] ) !== $target['page'] || ! in_array( $target['file'] ?? '', array( 'admin.php', 'options-general.php', 'themes.php', 'tools.php', 'upload.php', 'edit.php' ), true ) || ! is_string( $target['capability'] ?? null ) || '' === $target['capability'] ) { return ''; }
		$capability = $target['capability']; $registered = false;
		global $menu, $submenu;
		foreach ( array_merge( array( is_array( $menu ) ? $menu : array() ), is_array( $submenu ) ? array_values( $submenu ) : array() ) as $items ) {
			foreach ( $items as $item ) { if ( ( $item[2] ?? '' ) === $target['page'] ) { $capability = $item[1]; $registered = true; break 2; } }
		}
		if ( ! current_user_can( $capability ) || ( is_admin() && ! $registered ) ) { return ''; }
		return add_query_arg( 'page', $target['page'], admin_url( $target['file'] ) );
	}

	/**
	 * Merge external adapters into the same staged collection used by built-in adapters.
	 * @param NodeCollection $nodes Existing built-in integration definitions, modified in place.
	 * @return void Adds detected, authorized addon trees; common display settings apply later.
	 */
	public static function add( $nodes ) {
		foreach ( self::definitions() as $id => $definition ) {
			$root = 'bxqn-' . $id;
			if ( ! empty( $definition['builtin'] ) ) {
				if ( 'snnbrx' !== $id && isset( $nodes->nodes[ $root ] ) ) { $nodes->nodes[ $root ]['title'] = esc_html( $definition['label'] ); }
				continue;
			}
			if ( true !== ( $definition['loaded'] ?? false ) ) { continue; }
			$url = self::target_url( $definition['target'] ?? array() );
			if ( ! $url ) { continue; }
			$nodes->add_node( array( 'id' => $root, 'parent' => 'bxqn-group-plugins', 'title' => esc_html( $definition['label'] ), 'href' => $url ) );
			foreach ( is_array( $definition['children'] ?? null ) ? $definition['children'] : array() as $child => $target ) {
				if ( ! is_string( $child ) || sanitize_key( $child ) !== $child || ! is_array( $target ) || ! is_string( $target['label'] ?? null ) ) { continue; }
				$url = self::target_url( $target );
				if ( $url ) { $nodes->add_node( array( 'id' => $root . '-' . $child, 'parent' => $root, 'title' => esc_html( $target['label'] ), 'href' => $url ) ); }
			}
		}
	}
}
