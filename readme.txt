=== Bricks QuickNav ===
Contributors: deckerweb
Tags: bricks, toolbar, navigation
Requires at least: 6.7
Tested up to: 7.1
Requires PHP: 8.1
Stable tag: 2.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Quick access to Bricks content, templates, settings, add-ons, and resources from the WordPress toolbar. Bricks QuickNav is an independent plugin.

== Description ==
Version **2.0.1** · WordPress **6.7+** · PHP **8.1+**. Bricks must be the active parent theme for navigation. Includes deckerweb Library 1.1.0 and Updater 2.2.0. Without Bricks, navigation pauses safely and settings remain available. Bricks itself has its own requirements.

= Website and personal settings =
Open Bricks → QuickNav. Without active Bricks, open Settings → Bricks QuickNav. Set the toolbar label, icon, position, visibility, compact mode, list length, sorting, and navigation groups. Personal visibility and list-length preferences are in Users → Profile and apply to the current website.

= Bricks content =
Choose public post types enabled in Bricks. Only Bricks-edited content and posts you can edit are listed. Unpublished content is optional and off by default. WordPress permissions and Bricks permissions both apply. Template-type lists share the bounded template selection; increase Items per group to include more templates.

= Add-on shortcuts =
Existing framework, snippet manager, and Bricks addon integrations remain available. Enable each integration, place it directly in the main menu, and show or hide addon submenus. These controls change only QuickNav navigation. They do not install, activate, deactivate, or configure third-party plugins. Inactive integrations create no links. Registered admin-menu capabilities are checked; the frontend uses conservative management permissions. Active Bricks addons are grouped under a visible Add-ons menu, even when only one is available. An explicit direct-link choice moves that addon to the first menu level. Third-party adapters use the shared ddw/quicknav/bricks_integrations filter; see the addon registration guide. [Registration guide](docs/addon-registration.md).

= Premium addon versions =
GutenBricks 1.x and 2.x use their own settings and license destinations. BricksUltimate white-label menu names and slugs are respected. Bricksforge 4 navigation includes current tool routes and logs. Swiss Knife shortcuts respect license-dependent page availability. BricksExtras retains its native settings navigation.

= SNN-BRX child theme =
The original SNN-BRX shortcuts support its current theme name and renamed folders. Enabled native admin pages, the media image optimization screen, legacy snippet tabs, error logs, and modern snippet edit links are available. Disabled SNN modules produce no dead shortcuts. QuickNav never enables or executes snippets. SNN-BRX is a child theme and appears separately before Add-ons, using its configured native name or SNN-BRX. Its main target is Code Snippets when enabled, otherwise SNN settings. Other child themes link to their functions.php only when the file editor and user permissions allow it. Child-theme visibility is under Toolbar & Content. Developers can use ddw/quicknav/bricks_child_theme; see the [registration guide](docs/child-theme-registration.md).

= Native editors and resources =
QuickNav respects native toolbar visibility in Bricks, Gutenberg, and the Site Editor. It no longer forces toolbars or changes editor layout, and leaves the WordPress logo visible. Existing documentation, community, product-resource, and author links remain available by default. First-level navigation uses consistent bundled Lucide line icons; deeper submenus use text. Bricks AI setup is under Settings.

= Updates and plugin discovery =
The bundled deckerweb Updater checks this public GitHub repository through the WordPress update workflow. No GitHub token is required. The deckerweb Library uses a bundled offline catalog. Its optional online catalog is off by default. External requests transfer the requested repository/package and standard HTTP connection information; there is no telemetry.

= Multisite and data =
Network activation applies navigation to individual websites; there is no QuickNav toolbar or settings page in Network Admin. Website settings and personal preferences remain separate. Deactivation keeps data. Uninstall keeps settings by default; an explicit website-scoped deletion choice removes only that website’s QuickNav option and user preferences. The shared Library cleans temporary data only when the last installed host is removed. Other plugins and all Bricks content remain untouched.

== Installation ==
Install the plugin ZIP through Plugins → Add Plugin → Upload Plugin. Activate it and open its settings page. Before upgrading from 1.0.0, review your constants and filters. Native toolbar visibility after the upgrade is controlled by WordPress and Bricks.

== Frequently Asked Questions ==
= Where are the settings? =
Bricks → QuickNav, or Settings → Bricks QuickNav when Bricks is inactive. Personal preferences are in Users → Profile.

= Why is the toolbar missing? =
Enable the native toolbar in WordPress or Bricks and check QuickNav visibility, the capability, the user-ID restriction, and personal preferences. QuickNav does not force the toolbar.

= Can editors use QuickNav? =
An administrator can choose a suitable toolbar capability. Each destination still requires its own WordPress and Bricks permissions. Allowing the toolbar does not grant administrative privileges.

= What happens without Bricks? =
Navigation pauses safely while settings remain available. Activate Bricks or a Bricks child theme to resume navigation.

= Are resources and addon links removed? =
No. Existing resource collections and integrations remain. Addon links appear only for detected active integrations and can be configured individually.

= Do configuration constants still work? =
BXQN_NAME_IN_ADMINBAR, BXQN_ICON, BXQN_MENU_POSITION, BXQN_NUMBER_TEMPLATES, BXQN_COMPACT_MODE, BXQN_VIEW_CAPABILITY, BXQN_ENABLED_USERS, and BXQN_DISABLE_FOOTER override settings. Locked fields show the constant. BXQN_ADMINBAR_IN_BUILDER has no effect because toolbar forcing was removed.

= Which data is deleted? =
By default, uninstall preserves website settings and personal preferences. If that website opts in, those QuickNav values are deleted. Bricks content, user accounts, other plugins, and Library-installed plugins are retained.

= How can addon and child-theme developers connect? =
Use ddw/quicknav/bricks_integrations for addon definitions, and ddw/quicknav/bricks_child_theme for the active child theme. Labels, native destinations and optional child pages are configurable. QuickNav still checks page registration and user permissions. See the [addon example](docs/addon-registration.md) and [child-theme example](docs/child-theme-registration.md).

== Changelog ==
= 2.0.1 =
* Improved: Updated deckerweb Library 1.1.0 and Updater 2.2.0 with verified catalog packages and Git Updater conflict notices.
* Fixed: Changelog dialogs remain closed under foreign dialog styles and retain native closing and documentation fallbacks.
* Misc: Requires PHP 8.1 or newer; WordPress 6.7 remains supported.

= 2.0.0 =
* Improved: All displayed Bricks content types and template lists use muted Published/Unpublished headings; unpublished items show their specific status in parentheses.
* Improved: New QuickNav series artwork with centered icon, German and English banners, and 512/1024-pixel icon exports.
* Improved: Separate optional child-theme navigation before Add-ons, native SNN-BRX naming and feature destinations, and documented addon/child-theme extension filters.
* Improved: Consistent Lucide icons on the first menu level, a visible Add-ons submenu, and shared addon registration. Bricks AI setup is grouped under Settings.
* Fixed: Compact 16-pixel toolbar icons aligned with labels; stylesheet updates invalidate caches even when test packages share the same plugin version.
* New: Website settings and personal toolbar preferences in user profiles.
* New: Individual addon shortcuts, direct links, and optional addon submenus.
* New: Additional Bricks content types, sorting, and optional unpublished content.
* New: Navigation for twelve additional public Bricks addons with individually configurable links.
* Improved: Version-specific GutenBricks 1/2 links, BricksUltimate white-label menus, Bricksforge 4 routes, and license-aware Swiss Knife shortcuts.
* Improved: Navigation respects content permissions and reuses bounded content queries.
* Improved: SNN-BRX supports the current theme, enabled admin modules, and modern snippet links.
* Fixed: Query and settings filters work; All Fonts opens the overview; multiple framework and snippet menus coexist.
* Fixed: Builder URLs retain existing query parameters; inactive Bricks does not interrupt WordPress.
* Misc: Native toolbar visibility replaces forced toolbar and editor layout changes. The WordPress logo remains visible. Existing resources and links remain available.
* Misc: Includes deckerweb Library 0.9.0 and deckerweb Updater 2.1.0.

= 1.0.0 =
* New: Initial release with Bricks content, settings, integrations, and resource navigation.

== Upgrade Notice ==
= 2.0.1 =
Requires PHP 8.1 or newer. Updates deckerweb Library and Updater.
= 2.0.0 =
Uses native toolbar visibility. Existing constants, resources, and integrations remain supported.
