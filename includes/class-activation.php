<?php
/** Host-specific activation contract. @package BricksQuickNav */
namespace Deckerweb\BricksQuickNav;
defined( 'ABSPATH' ) || exit;

/** Keeps QuickNav settings available without loosening another catalog plugin's requirements. */
final class Activation {
	/**
	 * Adapt the elected Library guard after its cold activation handoff.
	 * @param string $file Plugin being activated.
	 * @param bool $network Whether activation is network-wide.
	 * @return void Replaces only the elected component callback with a scoped delegate.
	 */
	public static function prepare( $file, $network ) {
		$runtime = $GLOBALS['deckerweb_library_runtime_v1'] ?? null;
		if ( ! is_object( $runtime ) || ! is_callable( array( $runtime, 'activation_guard' ) )
			|| 0 !== strpos( get_class( $runtime ), 'Deckerweb\\PluginLibrary\\' ) ) { return; }
		remove_action( 'activate_plugin', array( $runtime, 'activation_guard' ), 0 );
		add_action( 'activate_plugin', array( __CLASS__, 'guard' ), 0, 2 );
	}

	/**
	 * Delegate every other plugin to the currently elected Library's unchanged guard.
	 * @param string $file Plugin being activated.
	 * @param bool $network Whether activation is network-wide.
	 * @return void Allows this host to pause safely; preserves other activation restrictions.
	 */
	public static function guard( $file, $network ) {
		if ( plugin_basename( DDW_BXQN_FILE ) === $file ) { return; }
		$runtime = $GLOBALS['deckerweb_library_runtime_v1'] ?? null;
		if ( is_object( $runtime ) && is_callable( array( $runtime, 'activation_guard' ) ) ) { $runtime->activation_guard( $file, $network ); }
	}
}
