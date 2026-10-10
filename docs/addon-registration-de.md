# Addons für Bricks QuickNav registrieren

[English](addon-registration.md)

Ein erkanntes Addon wird über `ddw/quicknav/bricks_integrations` registriert. Verwende eine eindeutige ID in Kleinbuchstaben, eine Bezeichnung als einfachen Text, eine boolesche `loaded`-Bedingung anhand Deines Plugins und dessen echtes lokales Admin-Ziel. Versionsangabe und Unterseiten sind optional. Registriere den Filter vor dem Aufbau der QuickNav-Navigation, beispielsweise beim Start Deines Plugins. Der Callback kann mehrfach laufen und darf keine Schreiboperationen, Abfragen oder externen Anfragen ausführen.

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

Ersetze die Beispiel-Slugs durch tatsächlich von Deinem Addon registrierte Seiten. Ziele benötigen einen einfachen Seiten-Slug und eine der Dateien `admin.php`, `options-general.php`, `themes.php`, `tools.php`, `upload.php` oder `edit.php`. Vollständige URLs, Query-Strings und externe Ziele nimmt diese Schnittstelle nicht an. Im Admin muss das Ziel im registrierten WordPress-Menü vorhanden sein; dessen native Berechtigung hat Vorrang. Im Frontend werden die angegebene Berechtigung und QuickNavs konservative Zielprüfungen angewendet. Der Addon-Bereich bleibt Benutzern vorbehalten, die Optionen verwalten dürfen.

Registrierte Addons verwenden dieselben QuickNav-Einstellungen, Gruppierung, Direktlink-Ausnahme, Untermenü-Anzeige und Berechtigungsprüfungen wie die eingebauten Integrationen. Ein aktives gruppiertes Addon erscheint auch allein unter Add-ons. Direkte Einträge auf der ersten Ebene erhalten das generische Lucide-Puzzle-Icon; gruppierte Addon-Einträge und tiefere Menüs verwenden Text.

Die stabilen Menü-IDs lauten `bxqn-my-bricks-addon` und `bxqn-my-bricks-addon-logs`. Verwende keine ID eines anderen Addons. Bezeichnungen eingebauter Integrationen lassen sich über dieselbe Registrierung ändern; ihre IDs bleiben erhalten. Zum Abschalten ihrer Navigation dienen die QuickNav-Einstellungen. Vorhandene WordPress-Toolbar-Einträge, Inhalte und fremde Einstellungen werden durch die Registrierung nicht verändert.

Für aktive Child Themes dient der separate [Child-Theme-Filter](child-theme-registration-de.md). SNN-BRX ist ein Child Theme und gehört nicht in das Add-ons-Menü.
