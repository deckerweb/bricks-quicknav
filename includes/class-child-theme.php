<?php
/** Active child-theme navigation and extension contract. @package BricksQuickNav */
namespace Deckerweb\BricksQuickNav;
defined( 'ABSPATH' ) || exit;

/** Separates child themes from addons and checks each native destination. */
final class ChildTheme {
	/** @return array|false Active child-theme definition, or false when unavailable/disabled. */
	public static function definition() {
		if ( ! is_child_theme() ) { return false; }
		$snn = Snn::active();
		$definition = array( 'label' => $snn ? Snn::label() : wp_get_theme()->get( 'Name' ), 'target' => $snn ? array( 'page' => Snn::snippets_enabled() ? 'snn-custom-codes-snippets' : 'snn-settings', 'file' => 'admin.php', 'capability' => 'manage_options' ) : array( 'file' => 'theme-editor.php' ), 'children' => array() );
		/**
		 * Customize the active child theme's label, authorized target and optional children.
		 * Return false to omit its navigation. Page targets use the addon target contract;
		 * theme-editor.php always resolves to the active child theme's functions.php.
		 * @since 2.0.0
		 * @param array $definition Label, target and children for the active child theme.
		 * @param \WP_Theme $theme Active child-theme metadata; scope your adapter to its identity.
		 */
		$filtered = apply_filters( 'ddw/quicknav/bricks_child_theme', $definition, wp_get_theme() );
		if ( false === $filtered ) { return false; }
		return is_array( $filtered ) && is_string( $filtered['label'] ?? null ) && '' !== trim( $filtered['label'] ) ? $filtered : $definition;
	}

	/**
	 * Resolve an editor or registered admin page without granting additional permissions.
	 * @param array $target Native admin target definition.
	 * @return string Authorized destination URL, or empty string.
	 */
	private static function url( $target ) {
		if ( ! is_array( $target ) ) { return ''; }
		if ( 'theme-editor.php' === ( $target['file'] ?? '' ) ) { return Snn::child_theme_url(); }
		return AddonRegistry::target_url( $target );
	}

	/**
	 * Stage the active theme before Add-ons, retaining SNN's enabled native subtree.
	 * @param NodeCollection $nodes Shared staged integration collection, modified in place.
	 * @return void Adds one theme root and authorized extension children.
	 */
	public static function add( $nodes ) {
		$nodes->add_group( array( 'id' => 'bxqn-group-child-theme', 'parent' => 'ddw-bricks-quicknav' ) );
		$definition = self::definition(); $snn = Snn::active(); $id = $snn ? 'snnbrx' : 'child-theme';
		if ( ! $definition || ! Integrations::enabled( $id, Settings::get() ) ) { return; }
		$url = self::url( $definition['target'] ?? array() );
		if ( ! $url ) { return; }
		if ( $snn ) { Snn::add( $nodes ); }
		$root = 'bxqn-' . $id;
		$nodes->add_node( array( 'id' => $root, 'parent' => 'bxqn-group-child-theme', 'title' => esc_html( $definition['label'] ), 'href' => $url ) );
		foreach ( is_array( $definition['children'] ?? null ) ? $definition['children'] : array() as $key => $target ) {
			if ( ! is_string( $key ) || '' === $key || sanitize_key( $key ) !== $key || ! is_array( $target ) || ! is_string( $target['label'] ?? null ) ) { continue; }
			$url = self::url( $target );
			if ( $url ) { $nodes->add_node( array( 'id' => $root . '-registered-' . $key, 'parent' => $root, 'title' => esc_html( $target['label'] ), 'href' => $url ) ); }
		}
	}
}
