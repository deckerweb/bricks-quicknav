# Bricks QuickNav addon registration

[Deutsch](addon-registration-de.md)

Register a detected addon with `ddw/quicknav/bricks_integrations`. Use a unique lowercase ID, a plain-text label, a boolean `loaded` condition based on your own plugin, and your real local admin target. Add optional version metadata and child targets. Register the filter before QuickNav builds its navigation (for example from your plugin bootstrap); callbacks may run several times and must not perform writes, queries, or remote requests.

```php
add_filter( 'ddw/quicknav/bricks_integrations', function ( $definitions ) {
    $definitions['my-bricks-addon'] = array(
        'label'   => 'My Bricks Addon',
        'loaded'  => defined( 'MY_BRICKS_ADDON_VERSION' ),
        'version' => defined( 'MY_BRICKS_ADDON_VERSION' ) ? MY_BRICKS_ADDON_VERSION : '',
        'target'  => array(
            'page'       => 'my-bricks-addon-settings',
            'file'       => 'admin.php',
            'capability' => 'manage_options',
        ),
        'children' => array(
            'logs' => array(
                'label'      => 'Logs',
                'page'       => 'my-bricks-addon-logs',
                'file'       => 'admin.php',
                'capability' => 'manage_options',
            ),
        ),
    );
    return $definitions;
} );
```

Use the actual slugs your addon registers, not the example slugs. Targets must use a simple page slug and one of `admin.php`, `options-general.php`, `themes.php`, `tools.php`, `upload.php`, or `edit.php`. Full URLs, query strings and external targets are not accepted by this registration contract. In admin requests, the target must exist in WordPress's registered menu; its native capability takes precedence. On the frontend the declared capability is checked, together with QuickNav's conservative destination checks. The addon area remains available only to users who can manage options.

Registered addons participate in the same QuickNav settings, grouped placement, direct-link override, submenu visibility and permission pruning as built-in integrations. An active grouped addon appears under Add-ons even when it is the only one. Direct first-level entries receive the generic Lucide puzzle icon; grouped addon entries and deeper menus use text.

The stable node IDs are `bxqn-my-bricks-addon` and `bxqn-my-bricks-addon-logs`. Do not claim another addon's ID. Built-in labels can be adjusted through the same registry; retain their IDs and use QuickNav settings to disable their navigation. Existing WordPress toolbar nodes, content and third-party settings are not modified by registration.

For active child themes, use the separate [child-theme filter](child-theme-registration.md). SNN-BRX is a child theme and does not belong to the Add-ons menu.
