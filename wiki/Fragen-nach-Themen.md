# Fragen nach Themen

[English](FAQ-by-topic)

### Wo sind die Einstellungen?

Unter Bricks → QuickNav oder ohne aktives Bricks unter Einstellungen → Bricks QuickNav. Persönliche Vorlieben findest du unter Benutzer → Profil.

### Warum fehlt die Toolbar?

Aktiviere die native Toolbar in WordPress oder Bricks und prüfe QuickNav-Sichtbarkeit, Berechtigung, Benutzer-ID-Beschränkung und persönliche Vorlieben. QuickNav erzwingt keine Toolbar.

### Können Redakteure QuickNav verwenden?

Ein Administrator kann eine passende Toolbar-Berechtigung wählen. Jedes Ziel verlangt weiterhin die eigenen WordPress- und Bricks-Rechte. Die Toolbar-Freigabe vergibt keine Verwaltungsrechte.

### Was passiert ohne Bricks?

Die Navigation pausiert sicher, während die Einstellungen erreichbar bleiben. Aktiviere Bricks oder ein Bricks-Child-Theme, um die Navigation wieder zu verwenden.

### Entfallen Ressourcen und Addon-Links?

Nein. Bestehende Ressourcensammlungen und Integrationen bleiben erhalten. Addon-Links erscheinen nur bei erkannten aktiven Integrationen und sind einzeln konfigurierbar.

### Funktionieren Konfigurationskonstanten weiterhin?

BXQN_NAME_IN_ADMINBAR, BXQN_ICON, BXQN_MENU_POSITION, BXQN_NUMBER_TEMPLATES, BXQN_COMPACT_MODE, BXQN_VIEW_CAPABILITY, BXQN_ENABLED_USERS und BXQN_DISABLE_FOOTER überschreiben Einstellungen. Gesperrte Felder zeigen die Konstante. BXQN_ADMINBAR_IN_BUILDER hat keine Wirkung mehr, da die Toolbar-Erzwingung entfernt wurde.

### Welche Daten werden gelöscht?

Standardmäßig bleiben Website-Einstellungen und persönliche Vorlieben bei der Deinstallation erhalten. Wenn die Website ausdrücklich zustimmt, werden diese QuickNav-Werte gelöscht. Bricks-Inhalte, Benutzerkonten, andere Plugins und über die Library installierte Plugins bleiben erhalten.

### Wie können Addon- und Child-Theme-Entwickler ihre Navigation ergänzen?

Addon-Definitionen verwenden ddw/quicknav/bricks_integrations, das aktive Child Theme verwendet ddw/quicknav/bricks_child_theme. Bezeichnungen, native Ziele und optionale Unterseiten lassen sich angeben. QuickNav prüft weiterhin die Seitenregistrierung und Benutzerrechte. Siehe [Addon-Beispiel](https://github.com/deckerweb/bricks-quicknav/blob/main/docs/addon-registration-de.md) und [Child-Theme-Beispiel](https://github.com/deckerweb/bricks-quicknav/blob/main/docs/child-theme-registration-de.md).

### Website-Einstellungen und persönliche Vorlieben

Öffne Bricks → QuickNav. Ohne aktives Bricks findest du die Seite unter Einstellungen → Bricks QuickNav. Wähle Toolbar-Name, Icon, Position, Sichtbarkeit, kompaktes Menü, Anzahl, Sortierung und Menübereiche. Persönliche Sichtbarkeit und Anzahl findest du unter Benutzer → Profil; sie gelten für die aktuelle Website.

### Bricks-Inhalte

Wähle öffentliche Inhaltstypen, die in Bricks aktiviert sind. Angezeigt werden mit Bricks bearbeitete Inhalte, die du bearbeiten darfst. Unveröffentlichte Inhalte sind optional und standardmäßig ausgeschaltet. WordPress- und Bricks-Berechtigungen gelten gemeinsam. Die Template-Typen verwenden dieselbe begrenzte Template-Auswahl; erhöhe Einträge je Gruppe für weitere Templates.

### Addon-Verknüpfungen

Bestehende Framework-, Snippet-Manager- und Bricks-Addon-Integrationen bleiben verfügbar. Schalte jede Integration einzeln ein, zeige sie direkt im Hauptmenü und wähle, ob Untereinträge erscheinen. Die Schalter ändern ausschließlich die QuickNav-Navigation. Sie installieren, aktivieren, deaktivieren oder konfigurieren keine anderen Plugins. Inaktive Integrationen erzeugen keine Links. Registrierte Menüberechtigungen werden geprüft; im Frontend gelten konservative Verwaltungsrechte. Aktive Bricks-Addons erscheinen unter einem sichtbaren Add-ons-Menü, auch wenn nur eines verfügbar ist. Ein ausdrücklich gewählter Direktlink verschiebt das Addon auf die erste Menüebene. Drittanbieter-Adapter verwenden den gemeinsamen Filter ddw/quicknav/bricks_integrations; siehe Anleitung zur Addon-Registrierung. [Registrierungsanleitung](https://github.com/deckerweb/bricks-quicknav/blob/main/docs/addon-registration-de.md).

### Premium-Addon-Versionen

GutenBricks 1.x und 2.x verwenden ihre jeweiligen Einstellungs- und Lizenzziele. BricksUltimate berücksichtigt White-Label-Menünamen und Slugs. Bricksforge 4 verwendet aktuelle Werkzeugziele und Protokolle. Swiss-Knife-Verknüpfungen berücksichtigen lizenzabhängig verfügbare Seiten. BricksExtras behält seine native Einstellungsnavigation.

### SNN-BRX Child Theme

Die bisherigen SNN-BRX-Verknüpfungen unterstützen den aktuellen Theme-Namen und umbenannte Ordner. Aktivierte native Admin-Seiten, die Bildoptimierung unter Medien, bestehende Snippet-Tabs, Fehlerprotokolle und Bearbeitungslinks für moderne Snippets sind erreichbar. Ausgeschaltete SNN-Module erzeugen keine toten Links. QuickNav aktiviert oder führt keine Snippets aus. SNN-BRX ist ein Child Theme und erscheint separat vor Add-ons, mit seinem angepassten nativen Namen oder SNN-BRX. Hauptziel sind die Code-Snippets, wenn aktiv, sonst die SNN-Einstellungen. Andere Child Themes verlinken auf ihre functions.php, sofern Datei-Editor und Benutzerrechte dies erlauben. Die Child-Theme-Sichtbarkeit liegt unter Toolbar & Inhalte. Entwickler verwenden ddw/quicknav/bricks_child_theme; siehe [Registrierungsanleitung](https://github.com/deckerweb/bricks-quicknav/blob/main/docs/child-theme-registration-de.md).

### Native Editoren und Ressourcen

QuickNav berücksichtigt die native Toolbar-Sichtbarkeit in Bricks, Gutenberg und Site Editor. Das Plugin erzwingt keine Toolbar mehr, verschiebt keine Editor-Bereiche und lässt das WordPress-Logo sichtbar. Bestehende Dokumentations-, Community-, Produkt- und Autorenlinks bleiben standardmäßig verfügbar. Die erste Menüebene verwendet einheitliche lokale Lucide-Linien-Icons; tiefere Untermenüs verwenden Text. Die KI Einrichtung (Bricks AI) liegt unter Einstellungen.

### Updates und Plugin-Auswahl

Der enthaltene deckerweb Updater prüft dieses öffentliche GitHub-Repository über den WordPress-Updateablauf. Ein GitHub-Token ist nicht nötig. Die deckerweb Library verwendet einen lokalen Katalog; der optionale Online-Katalog ist standardmäßig ausgeschaltet. Externe Abfragen übertragen das angefragte Repository/Paket sowie übliche HTTP-Verbindungsdaten. Es gibt keine Telemetrie.

### Multisite und Daten

Die Netzwerkaktivierung stellt die Navigation auf den einzelnen Websites bereit; im Netzwerk-Admin gibt es keine QuickNav-Toolbar oder Einstellungsseite. Website-Einstellungen und persönliche Vorlieben bleiben getrennt. Deaktivierung erhält Daten. Deinstallation erhält Einstellungen standardmäßig; die ausdrücklich gewählte Löschoption entfernt nur die QuickNav-Option und Benutzerpräferenzen der jeweiligen Website. Die gemeinsame Library bereinigt temporäre Daten erst beim letzten installierten Host. Andere Plugins und sämtliche Bricks-Inhalte bleiben erhalten.
