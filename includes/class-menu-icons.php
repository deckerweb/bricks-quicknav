<?php
/** Consistent first-level toolbar icons. @package BricksQuickNav */
namespace Deckerweb\BricksQuickNav;
defined( 'ABSPATH' ) || exit;

/** Uses bundled Lucide line icons; deeper menu levels remain text-only. */
final class MenuIcons {
	/** @return array Stable first-level IDs mapped to approved Lucide artwork. */
	public static function mapping() {
		return array( 'bxqn-pages' => 'file-text', 'bxqn-templates' => 'panels-top-left', 'bxqn-types' => 'layout-list', 'bxqn-bundles' => 'package', 'bxqn-tags' => 'tags', 'bxqn-custom-fonts' => 'type', 'bxqn-ma-customfonts' => 'type', 'bxqn-sidebars' => 'panel-left', 'bxqn-settings' => 'settings', 'bxqn-systeminfo' => 'info', 'bxqn-child-theme' => 'git-branch', 'bxqn-snnbrx' => 'git-branch', 'bxqn-addons' => 'puzzle', 'bxqn-own-settings' => 'settings', 'bxqn-links' => 'external-link', 'bxqn-community-links' => 'users', 'bxqn-about' => 'circle-user-round', 'bxqn-formsubmissions' => 'inbox' );
	}

	/**
	 * Select a fitting generic symbol when a dynamic first-level entry has no fixed artwork.
	 * @param string $id Stable node ID.
	 * @return string Bundled Lucide symbol name.
	 */
	private static function symbol( $id ) {
		$map = self::mapping();
		if ( isset( $map[ $id ] ) ) { return $map[ $id ]; }
		if ( 0 === strpos( $id, 'bxqn-content-' ) ) { return 'file-text'; }
		if ( false !== strpos( $id, 'snippet' ) || false !== strpos( $id, 'customcode' ) ) { return 'code'; }
		if ( false !== strpos( $id, '-mode' ) ) { return 'triangle-alert'; }
		if ( in_array( $id, array( 'bxqn-core-framework', 'bxqn-automatic-css', 'bxqn-frames', 'bxqn-windpress', 'bxqn-brixies' ), true ) ) { return 'layers'; }
		return 'puzzle';
	}

	/**
	 * Load local approved SVGs once per request; no network or icon fonts.
	 * @param string $symbol Approved symbol name.
	 * @return string Decorative inline icon markup, hidden from assistive technology.
	 */
	private static function markup( $symbol ) {
		static $cache = array();
		if ( ! isset( $cache[ $symbol ] ) ) {
			$path = DDW_BXQN_DIR . '/assets/lucide/' . $symbol . '.svg';
			$svg = is_readable( $path ) ? file_get_contents( $path ) : '';
			$cache[ $symbol ] = '<span class="bxqn-menu-icon" aria-hidden="true">' . $svg . '</span> ';
		}
		return $cache[ $symbol ];
	}

	/**
	 * Apply one line icon per first-level item and remove legacy decorative submenu icons.
	 * @param NodeCollection $nodes Complete staged navigation, modified in place.
	 * @return void Normalizes titles while preserving labels, destinations, and the root logo.
	 */
	public static function apply( $nodes ) {
		foreach ( $nodes->nodes as $id => &$node ) {
			if ( ! empty( $node['group'] ) || 'ddw-bricks-quicknav' === $id || ! isset( $node['title'] ) ) { continue; }
			$title = preg_replace( '~<span\b[^>]*class=["\'][^"\']*\bicon-svg\b[^"\']*["\'][^>]*>.*?</span>\s*~s', '', $node['title'] );
			$parent = $node['parent'] ?? ''; $visited = array();
			while ( ! empty( $nodes->nodes[ $parent ]['group'] ) && ! isset( $visited[ $parent ] ) ) { $visited[ $parent ] = true; $parent = $nodes->nodes[ $parent ]['parent'] ?? ''; }
			$node['title'] = 'ddw-bricks-quicknav' === $parent ? self::markup( self::symbol( $id ) ) . $title : $title;
		}
		unset( $node );
	}
}
