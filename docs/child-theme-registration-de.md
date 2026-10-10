# Child Themes für Bricks QuickNav registrieren

[English](child-theme-registration.md) · [Addon-Registrierung](addon-registration-de.md)

`ddw/quicknav/bricks_child_theme` gilt für das aktuell aktive Bricks-Child-Theme. Sein einzelner Menüeintrag steht vor Add-ons. Standardmäßig stammen der Name aus den Theme-Metadaten und das Ziel aus der functions.php des aktiven Child Themes. Ohne erlaubten Datei-Editor oder Theme-Bearbeitungsrecht entfällt dieser Editor-Link. SNN-BRX verwendet stattdessen seine aktiven Code-Snippets, ansonsten seine Einstellungsseite. Als Name dient der angepasste native Admin-Name oder standardmäßig SNN-BRX. Diese nativen SNN-Seiten benötigen keine Erlaubnis zum Bearbeiten von Theme-Dateien.

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

Registriere den Filter im Child Theme oder einem Plugin vor dem Aufbau der Toolbar. Ersetze Beispiel-Identitäten und Slugs durch Deine tatsächlich registrierten Seiten. Der Callback kann mehrfach laufen; er liefert ausschließlich Navigationsmetadaten. Die Rückgabe false lässt den Theme-Eintrag weg. Die unveränderte Definition lässt andere Child Themes unverändert. Über diesen Filter werden keine Plugins installiert oder aktiviert, keine Snippets ausgeführt und keine Einstellungen geändert.

Admin-Ziele verwenden denselben Seiten-/Datei-/Berechtigungsvertrag wie die Addon-Registrierung. Im Admin müssen Seiten tatsächlich registriert sein; ihre native Berechtigung hat Vorrang. Im Frontend gelten die deklarierte Berechtigung und konservative Zielprüfungen. Für den functions.php-Editor bleibt das Ziel `array( 'file' => 'theme-editor.php' )`; beliebige Dateipfade werden nicht angenommen. Jede Unterseite wird einzeln geprüft. Eine erlaubte eigene Verwaltungsseite kann auch bei gesperrten Datei-Editoren verfügbar bleiben.

Die stabile Haupt-ID lautet bxqn-child-theme, bei SNN-BRX bxqn-snnbrx. Registrierte Unterseiten erhalten bxqn-child-theme-registered-KEY beziehungsweise bxqn-snnbrx-registered-KEY. Der Theme-Eintrag bleibt außerhalb von Add-ons und verwendet auf der ersten Ebene das Lucide-Git-Branch-Icon; seine Untermenüs bleiben textbasiert. Seine Sichtbarkeit wird unter Toolbar & Inhalte → Child Theme eingestellt. Addon-Direktlink-Vorlieben verschieben das Theme nicht zurück zu Add-ons.
