<?php
/**
 * Plugin Name: Bricks QuickNav
 * Plugin URI: https://github.com/deckerweb/bricks-quicknav
 * Description: Quick access to Bricks content, templates, settings, add-ons, and resources from the WordPress toolbar.
 * Version: 2.0.2
 * Requires at least: 6.7
 * Requires PHP: 8.1
 * Author: David Decker – DECKERWEB
 * Author URI: https://github.com/deckerweb
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: bricks-quicknav
 * Domain Path: /languages/
 * Update URI: https://github.com/deckerweb/bricks-quicknav
 * GitHub Plugin URI: https://github.com/deckerweb/bricks-quicknav
 *
 * Copyright © 2025–2026 David Decker – DECKERWEB.
 * SPDX-License-Identifier: GPL-2.0-or-later
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'DDW_BXQN_VERSION', '2.0.2' );
define( 'DDW_BXQN_FILE', __FILE__ );
define( 'DDW_BXQN_DIR', __DIR__ );

require_once __DIR__ . '/includes/class-settings.php';
require_once __DIR__ . '/includes/class-builder.php';
require_once __DIR__ . '/includes/class-snn.php';
require_once __DIR__ . '/includes/class-public-addons.php';
require_once __DIR__ . '/includes/class-premium-targets.php';
require_once __DIR__ . '/includes/class-integrations.php';
require_once __DIR__ . '/includes/class-addon-registry.php';
require_once __DIR__ . '/includes/class-child-theme.php';
require_once __DIR__ . '/includes/class-menu-icons.php';
require_once __DIR__ . '/includes/trait-icons.php';
require_once __DIR__ . '/includes/trait-resources.php';
require_once __DIR__ . '/includes/trait-integration-menus.php';
require_once __DIR__ . '/includes/trait-bricks-menus.php';
require_once __DIR__ . '/includes/class-node-collection.php';
require_once __DIR__ . '/includes/class-toolbar.php';
require_once __DIR__ . '/includes/class-plugin.php';
require_once __DIR__ . '/includes/class-updates.php';
require_once __DIR__ . '/includes/class-activation.php';

// The bootstrap elects a compatible Library before loading its PHP 8 runtime.
require_once __DIR__ . '/includes/deckerweb-plugin-library/bootstrap.php';
deckerweb_library_register_v2( __FILE__, array(), __DIR__ . '/includes/deckerweb-plugin-library' );

add_action( 'plugins_loaded', array( '\Deckerweb\BricksQuickNav\Plugin', 'boot' ) );

// Run after the component's -100 activation handoff, including first activation.
add_action( 'activate_plugin', array( '\Deckerweb\BricksQuickNav\Activation', 'prepare' ), -90, 2 );
