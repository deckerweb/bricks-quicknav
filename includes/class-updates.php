<?php
/**
 * Host integration. @package BricksQuickNav
 * Adapted from Oxygen QuickNav 2.0.0, © 2025–2026 David Decker – DECKERWEB.
 * GPL-2.0-or-later. https://github.com/deckerweb/oxygen-quicknav/tree/v2.0.0
 */
namespace Deckerweb\BricksQuickNav;

defined( 'ABSPATH' ) || exit;

/** Owns the GitHub updater and rejects mismatched or incomplete host packages. */
final class Updates {
	/** @var bool Whether a component could not register safely. */
	public static $error = false;

	/** @return void Registers pinned update hooks after host translations load. */
	public static function register() {
		static $registered = false;
		if ( $registered ) { return; }
		if ( version_compare( PHP_VERSION, '8.1', '<' ) ) { self::$error = true; return; }
		$updater_class = require DDW_BXQN_DIR . '/includes/deckerweb-github-release-updater-v2.php';
		if ( ! is_string( $updater_class ) || ! class_exists( $updater_class, false ) || ! defined( $updater_class . '::IMPLEMENTATION_VERSION' ) || version_compare( constant( $updater_class . '::IMPLEMENTATION_VERSION' ), '2.2.1', '<' ) || ! defined( $updater_class . '::SUPPORTS_HOST_TRANSLATIONS' ) || ! constant( $updater_class . '::SUPPORTS_HOST_TRANSLATIONS' ) ) { self::$error = true; return; }
		if ( ! defined( $updater_class . '::SUPPORTS_CATALOG_PROVIDER' ) || ! constant( $updater_class . '::SUPPORTS_CATALOG_PROVIDER' ) || ! function_exists( 'deckerweb_library_updater_options_v1' ) ) { self::$error = true; return; }
		$artwork_version = substr( hash( 'sha256', hash_file( 'sha256', DDW_BXQN_DIR . '/assets/icon.svg' ) . hash_file( 'sha256', DDW_BXQN_DIR . '/assets/banner-en-1544x500.png' ) ), 0, 10 );
		$banner_language = strpos( get_user_locale(), 'de' ) === 0 ? 'de' : 'en';
		try {
			$updater = new $updater_class(
				DDW_BXQN_FILE, 'https://github.com/deckerweb/bricks-quicknav', 'Bricks QuickNav',
				__( 'Quick access to Bricks content, templates, settings, add-ons, and resources from the WordPress toolbar.', 'bricks-quicknav' ),
				array( 'icons' => array( 'default' => add_query_arg( 'ver', $artwork_version, plugins_url( 'assets/icon-256x256.png', DDW_BXQN_FILE ) ) ), 'banners' => array( 'low' => add_query_arg( 'ver', $artwork_version, plugins_url( 'assets/banner-' . $banner_language . '-772x250.png', DDW_BXQN_FILE ) ), 'high' => add_query_arg( 'ver', $artwork_version, plugins_url( 'assets/banner-' . $banner_language . '-1544x500.png', DDW_BXQN_FILE ) ) ) ),
				array_merge( deckerweb_library_updater_options_v1( DDW_BXQN_FILE, 'https://github.com/deckerweb/bricks-quicknav' ), array( 'translate' => require DDW_BXQN_DIR . '/includes/updater-translations.php' ) )
			);
			$updater->register();
			$registered = true;
			add_filter( 'upgrader_source_selection', array( __CLASS__, 'validate_update_source' ), 30, 4 );
			add_filter( 'plugins_api', array( __CLASS__, 'information' ), 30, 3 );
			add_filter( 'http_request_args', array( __CLASS__, 'request_limits' ), 20, 2 );
		} catch ( \Throwable $error ) { self::$error = true; }
	}

	/**
	 * Bound repository metadata without touching unrelated HTTP requests.
	 * @param array $args WordPress request arguments.
	 * @param string $url Request URL.
	 * @return array Scoped request limits.
	 */
	public static function request_limits( $args, $url ) {
		if ( 'https://api.github.com/repos/deckerweb/bricks-quicknav/releases/latest' === $url ) {
			$args['sslverify'] = true; $args['timeout'] = 6; $args['redirection'] = 2; $args['limit_response_size'] = 1024 * 1024;
		}
		return $args;
	}

	/**
	 * Render local categorized release history in WordPress plugin information.
	 * @param object|false|\WP_Error $result Existing response.
	 * @param string $action Plugin API action.
	 * @param object $args Requested plugin information arguments.
	 * @return object|false|\WP_Error Augmented own-plugin response or original value.
	 */
	public static function information( $result, $action, $args ) {
		if ( 'plugin_information' !== $action || 'bricks-quicknav' !== ( $args->slug ?? '' ) || ! is_object( $result ) || is_wp_error( $result ) ) { return $result; }
		$history = require DDW_BXQN_DIR . '/includes/plugin-history.php';
		$result->sections['changelog'] = $history( 0 === strpos( determine_locale(), 'de' ) );
		return $result;
	}

	/**
	 * Validate an offered candidate after updater directory normalization.
	 *
	 * @param string|WP_Error $source Extracted plugin directory or previous failure.
	 * @param string $remote_source Original extracted download directory.
	 * @param WP_Upgrader $upgrader Native upgrader performing the operation.
	 * @param array $hook_extra Per-package plugin/update context, including bulk variants.
	 * @return string|WP_Error Original source on success, or a localized rejection before replacement.
	 */
	public static function validate_update_source( $source, $remote_source, $upgrader, $hook_extra ) {
		if ( is_wp_error( $source ) || ! is_string( $source ) || ! is_array( $hook_extra )
			|| ( $hook_extra['plugin'] ?? '' ) !== plugin_basename( DDW_BXQN_FILE )
			|| ( isset( $hook_extra['type'] ) && 'plugin' !== $hook_extra['type'] )
			|| ( isset( $hook_extra['action'] ) && 'update' !== $hook_extra['action'] )
			|| ( ! isset( $hook_extra['type'], $hook_extra['action'] ) && ! ( $upgrader instanceof \Plugin_Upgrader && true === $upgrader->bulk ) ) ) {
			return $source;
		}
		global $wp_filesystem;
		if ( ! $wp_filesystem ) {
			return new \WP_Error( 'bxqn_update_filesystem', __( 'Could not access the update filesystem.', 'bricks-quicknav' ) );
		}
		$root = untrailingslashit( $source );
		if ( $wp_filesystem->is_file( $root . '/bricks-quicknav/bricks-quicknav.php' ) ) {
			$root .= '/bricks-quicknav';
		}
		$main = $root . '/bricks-quicknav.php';
		$size = $wp_filesystem->size( $main );
		if ( ! is_numeric( $size ) || $size < 1 || $size > 256 * 1024 ) {
			return new \WP_Error( 'bxqn_update_identity', __( 'The update package is not Bricks QuickNav.', 'bricks-quicknav' ) );
		}
		$contents = $wp_filesystem->get_contents( $main );
		$headers = array();
		foreach ( array( 'Plugin Name', 'Update URI', 'Requires at least', 'Requires PHP', 'Requires CP', 'Version' ) as $header ) {
			preg_match( '/^[ \t\/*#@]*' . preg_quote( $header, '/' ) . ':[ \t]*(.+)$/mi', is_string( $contents ) ? substr( $contents, 0, 8192 ) : '', $match );
			$headers[ $header ] = isset( $match[1] ) ? trim( $match[1] ) : '';
		}
		if ( 'Bricks QuickNav' !== $headers['Plugin Name'] || 'https://github.com/deckerweb/bricks-quicknav' !== $headers['Update URI'] || ! preg_match( '/^\d+\.\d+\.\d+(?:-[0-9A-Za-z.-]+)?$/D', $headers['Version'] ) ) {
			return new \WP_Error( 'bxqn_update_identity', __( 'The update package is not Bricks QuickNav.', 'bricks-quicknav' ) );
		}
		global $wp_version;
		if ( ! preg_match( '/^\d+(?:\.\d+){1,2}$/D', $headers['Requires PHP'] ) || version_compare( PHP_VERSION, $headers['Requires PHP'], '<' ) || ! preg_match( '/^\d+(?:\.\d+){1,2}$/D', $headers['Requires at least'] ) || ( ! function_exists( 'classicpress_version' ) && version_compare( $wp_version, $headers['Requires at least'], '<' ) ) ) {
			return new \WP_Error( 'bxqn_update_compatibility', __( 'The update package requires a newer WordPress or PHP version.', 'bricks-quicknav' ) );
		}
		if ( function_exists( 'classicpress_version' ) && ( ! preg_match( '/^\d+(?:\.\d+){1,2}$/D', $headers['Requires CP'] ) || version_compare( classicpress_version(), $headers['Requires CP'], '<' ) ) ) {
			return new \WP_Error( 'bxqn_update_compatibility', __( 'The update package requires a newer ClassicPress version.', 'bricks-quicknav' ) );
		}
		$updates = get_site_transient( 'update_plugins' );
		$offer = is_object( $updates ) ? ( $updates->response[ plugin_basename( DDW_BXQN_FILE ) ] ?? null ) : null;
		$version = is_object( $offer ) ? ( $offer->new_version ?? $offer->version ?? '' ) : '';
		if ( ! is_string( $version ) || '' === $version || $version !== $headers['Version'] || ! version_compare( $headers['Version'], DDW_BXQN_VERSION, '>' ) ) {
			return new \WP_Error( 'bxqn_update_version', __( 'The update package does not match the offered newer version.', 'bricks-quicknav' ) );
		}
		foreach ( array( 'includes/class-child-theme.php', 'includes/class-addon-registry.php', 'includes/class-menu-icons.php', 'assets/lucide/LICENSE', 'assets/lucide/git-branch.svg', 'assets/lucide/triangle-alert.svg', 'assets/lucide/circle-user-round.svg', 'assets/lucide/layout-list.svg', 'assets/lucide/panels-top-left.svg', 'assets/lucide/inbox.svg', 'assets/lucide/puzzle.svg', 'assets/lucide/sparkles.svg', 'assets/lucide/file-text.svg', 'assets/lucide/package.svg', 'assets/lucide/settings.svg', 'assets/lucide/type.svg', 'assets/lucide/layers.svg', 'assets/lucide/code.svg', 'assets/lucide/info.svg', 'assets/lucide/panel-left.svg', 'assets/lucide/external-link.svg', 'assets/lucide/tags.svg', 'assets/lucide/folder.svg', 'assets/lucide/users.svg', 'includes/class-settings.php', 'includes/class-builder.php', 'includes/class-integrations.php', 'includes/class-snn.php', 'includes/class-public-addons.php', 'includes/class-premium-targets.php', 'includes/class-plugin.php', 'includes/class-updates.php', 'includes/class-activation.php', 'includes/class-toolbar.php', 'includes/class-node-collection.php', 'includes/trait-icons.php', 'includes/trait-resources.php', 'includes/trait-integration-menus.php', 'includes/trait-bricks-menus.php', 'includes/plugin-history.php', 'includes/updater-translations.php', 'includes/deckerweb-github-release-updater-v2.php', 'includes/history.json', 'includes/deckerweb-plugin-library/bootstrap.php', 'includes/deckerweb-plugin-library/compatibility.json', 'uninstall.php', 'assets/INTER-LICENSE.txt', 'assets/banner-de-772x250.png', 'assets/banner-de-1544x500.png', 'assets/banner-en-772x250.png', 'assets/banner-en-1544x500.png', 'assets/icon.svg', 'assets/icon-256x256.png', 'assets/quicknav.css', 'assets/dialog.js', 'assets/settings.js', 'languages/bricks-quicknav-de_DE.mo', 'languages/bricks-quicknav-de_DE_formal.mo' ) as $file ) {
			if ( ! $wp_filesystem->is_file( $root . '/' . $file ) ) {
				return new \WP_Error( 'bxqn_update_incomplete', __( 'The update package is missing required plugin files.', 'bricks-quicknav' ) );
			}
		}
		$manifest_file = $root . '/includes/deckerweb-plugin-library/compatibility.json';
		if ( $wp_filesystem->size( $manifest_file ) > 64 * 1024 ) {
			return new \WP_Error( 'bxqn_update_incomplete', __( 'The update package is missing required plugin files.', 'bricks-quicknav' ) );
		}
		$manifest = json_decode( (string) $wp_filesystem->get_contents( $manifest_file ), true );
		if ( ! is_array( $manifest ) || 2 !== ( $manifest['protocol'] ?? null ) || ! is_array( $manifest['files'] ?? null ) || ! is_array( $manifest['hashes'] ?? null ) || count( $manifest['files'] ) > 200 ) {
			return new \WP_Error( 'bxqn_update_incomplete', __( 'The update package is missing required plugin files.', 'bricks-quicknav' ) );
		}
		$required = array( 'bootstrap.php', 'runtime.php', 'lifecycle.php', 'version.php', 'src/Library.php', 'src/Catalog.php', 'src/Package.php', 'src/Requirements.php', 'src/I18n.php', 'src/History.php', 'catalog.json', 'languages/de_DE.mo', 'languages/de_DE_formal.mo' );
		if ( count( array_filter( $manifest['files'], 'is_string' ) ) !== count( $manifest['files'] )
			|| ! is_string( $manifest['version'] ?? null ) || ! is_string( $manifest['requires_php'] ?? null ) || ! is_string( $manifest['requires_wp'] ?? null )
			|| array_diff( $required, $manifest['files'] ) || ! preg_match( '/^\d+\.\d+\.\d+$/D', $manifest['version'] ?? '' )
			|| ! preg_match( '/^\d+(?:\.\d+){1,2}$/D', $manifest['requires_php'] ?? '' )
			|| ! preg_match( '/^\d+(?:\.\d+){1,2}$/D', $manifest['requires_wp'] ?? '' )
			 ) {
			return new \WP_Error( 'bxqn_update_incomplete', __( 'The update package is missing required plugin files.', 'bricks-quicknav' ) );
		}
		$total = 0;
		foreach ( $manifest['files'] as $file ) {
			$expected = is_string( $file ) ? ( $manifest['hashes'][ $file ] ?? '' ) : '';
			if ( ! is_string( $file ) || ! preg_match( '~^[a-zA-Z0-9_/.-]+$~D', $file ) || false !== strpos( $file, '..' ) || ! is_string( $expected ) || ! preg_match( '/^[a-f0-9]{64}$/D', $expected ) ) {
				return new \WP_Error( 'bxqn_update_incomplete', __( 'The update package is missing required plugin files.', 'bricks-quicknav' ) );
			}
			$path = $root . '/includes/deckerweb-plugin-library/' . $file;
			$size = $wp_filesystem->size( $path );
			$total += is_numeric( $size ) ? (int) $size : 0;
			if ( $total > 16 * 1024 * 1024 || ! $wp_filesystem->is_file( $path ) || ! is_numeric( $size ) || $size > 2 * 1024 * 1024 || ! hash_equals( $expected, hash( 'sha256', (string) $wp_filesystem->get_contents( $path ) ) ) ) {
				return new \WP_Error( 'bxqn_update_incomplete', __( 'The update package is missing required plugin files.', 'bricks-quicknav' ) );
			}
		}
		return $source;
	}

}
