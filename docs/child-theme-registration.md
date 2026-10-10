# Bricks QuickNav child-theme registration

[Deutsch](child-theme-registration-de.md) · [Addon registration](addon-registration.md)

Use `ddw/quicknav/bricks_child_theme` for the currently active Bricks child theme. There is one theme entry before Add-ons. By default its name comes from theme metadata and its target is the active child theme's functions.php. That editor shortcut is omitted when file editing or the user's theme-editing capability is unavailable. SNN-BRX instead uses its enabled Code Snippets feature, otherwise its settings page, under its custom native admin name or the concise default SNN-BRX. These native SNN pages do not depend on permission to edit theme files.

```php
add_filter( 'ddw/quicknav/bricks_child_theme', function ( $definition, $theme ) {
    if ( 'my-bricks-child' !== $theme->get_stylesheet() ) {
        return $definition;
    }
    $definition['label'] = 'My Child Theme';
    $definition['target'] = array(
        'page'       => 'my-child-settings',
        'file'       => 'admin.php',
        'capability' => 'manage_options',
    );
    $definition['children']['logs'] = array(
        'label'      => 'Logs',
        'page'       => 'my-child-logs',
        'file'       => 'admin.php',
        'capability' => 'manage_options',
    );
    return $definition;
}, 10, 2 );
```

Register the filter from your child theme or plugin before toolbar composition. Replace the example identities/slugs with your actual registered pages. The callback may run repeatedly; use it only to return navigation metadata. Returning false omits this theme entry. Returning the original definition leaves other child themes unchanged. Never install, activate, execute snippets, or modify settings from this filter.

Admin-page targets follow the same page/file/capability contract as addon registration. In admin requests, pages must actually be registered and their native capability takes precedence. Frontend requests use the declared capability and conservative destination checks. To keep the functions.php editor target, use `array( 'file' => 'theme-editor.php' )`; no arbitrary file path is accepted. Every child target is checked independently. A permitted custom administrative page can remain available when file editors are disabled.

The stable root ID is bxqn-child-theme, or bxqn-snnbrx for SNN-BRX. Registered children receive bxqn-child-theme-registered-KEY or bxqn-snnbrx-registered-KEY. The root stays outside Add-ons and gets a first-level Lucide Git Branch icon; its submenu remains text-based. Theme visibility is configured under Toolbar & Content → Child Theme. Addon direct-link preferences do not move the theme back into Add-ons.
