# FAQ by topic

[Deutsch](Fragen-nach-Themen)

### Where are the settings?

Bricks → QuickNav, or Settings → Bricks QuickNav when Bricks is inactive. Personal preferences are in Users → Profile.

### Why is the toolbar missing?

Enable the native toolbar in WordPress or Bricks and check QuickNav visibility, the capability, the user-ID restriction, and personal preferences. QuickNav does not force the toolbar.

### Can editors use QuickNav?

An administrator can choose a suitable toolbar capability. Each destination still requires its own WordPress and Bricks permissions. Allowing the toolbar does not grant administrative privileges.

### What happens without Bricks?

Navigation pauses safely while settings remain available. Activate Bricks or a Bricks child theme to resume navigation.

### Are resources and addon links removed?

No. Existing resource collections and integrations remain. Addon links appear only for detected active integrations and can be configured individually.

### Do configuration constants still work?

BXQN_NAME_IN_ADMINBAR, BXQN_ICON, BXQN_MENU_POSITION, BXQN_NUMBER_TEMPLATES, BXQN_COMPACT_MODE, BXQN_VIEW_CAPABILITY, BXQN_ENABLED_USERS, and BXQN_DISABLE_FOOTER override settings. Locked fields show the constant. BXQN_ADMINBAR_IN_BUILDER has no effect because toolbar forcing was removed.

### Which data is deleted?

By default, uninstall preserves website settings and personal preferences. If that website opts in, those QuickNav values are deleted. Bricks content, user accounts, other plugins, and Library-installed plugins are retained.

### How can addon and child-theme developers connect?

Use ddw/quicknav/bricks_integrations for addon definitions, and ddw/quicknav/bricks_child_theme for the active child theme. Labels, native destinations and optional child pages are configurable. QuickNav still checks page registration and user permissions. See the [addon example](https://github.com/deckerweb/bricks-quicknav/blob/main/docs/addon-registration.md) and [child-theme example](https://github.com/deckerweb/bricks-quicknav/blob/main/docs/child-theme-registration.md).

### Website and personal settings

Open Bricks → QuickNav. Without active Bricks, open Settings → Bricks QuickNav. Set the toolbar label, icon, position, visibility, compact mode, list length, sorting, and navigation groups. Personal visibility and list-length preferences are in Users → Profile and apply to the current website.

### Bricks content

Choose public post types enabled in Bricks. Only Bricks-edited content and posts you can edit are listed. Unpublished content is optional and off by default. WordPress permissions and Bricks permissions both apply. Template-type lists share the bounded template selection; increase Items per group to include more templates.

### Add-on shortcuts

Existing framework, snippet manager, and Bricks addon integrations remain available. Enable each integration, place it directly in the main menu, and show or hide addon submenus. These controls change only QuickNav navigation. They do not install, activate, deactivate, or configure third-party plugins. Inactive integrations create no links. Registered admin-menu capabilities are checked; the frontend uses conservative management permissions. Active Bricks addons are grouped under a visible Add-ons menu, even when only one is available. An explicit direct-link choice moves that addon to the first menu level. Third-party adapters use the shared ddw/quicknav/bricks_integrations filter; see the addon registration guide. [Registration guide](https://github.com/deckerweb/bricks-quicknav/blob/main/docs/addon-registration.md).

### Premium addon versions

GutenBricks 1.x and 2.x use their own settings and license destinations. BricksUltimate white-label menu names and slugs are respected. Bricksforge 4 navigation includes current tool routes and logs. Swiss Knife shortcuts respect license-dependent page availability. BricksExtras retains its native settings navigation.

### SNN-BRX child theme

The original SNN-BRX shortcuts support its current theme name and renamed folders. Enabled native admin pages, the media image optimization screen, legacy snippet tabs, error logs, and modern snippet edit links are available. Disabled SNN modules produce no dead shortcuts. QuickNav never enables or executes snippets. SNN-BRX is a child theme and appears separately before Add-ons, using its configured native name or SNN-BRX. Its main target is Code Snippets when enabled, otherwise SNN settings. Other child themes link to their functions.php only when the file editor and user permissions allow it. Child-theme visibility is under Toolbar & Content. Developers can use ddw/quicknav/bricks_child_theme; see the [registration guide](https://github.com/deckerweb/bricks-quicknav/blob/main/docs/child-theme-registration.md).

### Native editors and resources

QuickNav respects native toolbar visibility in Bricks, Gutenberg, and the Site Editor. It no longer forces toolbars or changes editor layout, and leaves the WordPress logo visible. Existing documentation, community, product-resource, and author links remain available by default. First-level navigation uses consistent bundled Lucide line icons; deeper submenus use text. Bricks AI setup is under Settings.

### Updates and plugin discovery

The bundled deckerweb Updater checks this public GitHub repository through the WordPress update workflow. No GitHub token is required. The deckerweb Library uses a bundled offline catalog. Its optional online catalog is off by default. External requests transfer the requested repository/package and standard HTTP connection information; there is no telemetry.

### Multisite and data

Network activation applies navigation to individual websites; there is no QuickNav toolbar or settings page in Network Admin. Website settings and personal preferences remain separate. Deactivation keeps data. Uninstall keeps settings by default; an explicit website-scoped deletion choice removes only that website’s QuickNav option and user preferences. The shared Library cleans temporary data only when the last installed host is removed. Other plugins and all Bricks content remain untouched.
