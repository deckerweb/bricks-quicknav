# Bricks QuickNav

![Bricks QuickNav](https://raw.githubusercontent.com/deckerweb/bricks-quicknav/main/assets-github/approved/banner-de.png)

[English](README.md)

Schneller Zugriff auf Bricks-Inhalte, Templates, Einstellungen, Addons und Ressourcen über die WordPress-Toolbar. Bricks QuickNav ist ein eigenständiges Plugin.

Version **2.0.1** · WordPress **6.7+** · PHP **8.1+**. Für die Navigation muss Bricks als übergeordnetes Theme aktiv sein. Enthält deckerweb Library 1.1.0 und Updater 2.2.0. Ohne Bricks pausiert die Navigation sicher; die Einstellungen bleiben erreichbar. Bricks hat eigene Mindestanforderungen.

[GitHub Wiki](https://github.com/deckerweb/bricks-quicknav/wiki) · [FAQ](wiki/Fragen-nach-Themen.md)

- [Über das Plugin](#about)
- [Auf einen Blick](#overview)
- [Installation und erste Schritte](#installation)
- [Funktionen](#features)
- [FAQ](#faq)
- [Änderungsverlauf](#changelog)
- [Projekt und Unterstützung](#support)

<a id="about"></a>

## Über das Plugin

Schneller Zugriff auf Bricks-Inhalte, Templates, Einstellungen, Addons und Ressourcen über die WordPress-Toolbar. Bricks QuickNav ist ein eigenständiges Plugin.

<a id="overview"></a>

## Auf einen Blick

- Website-Einstellungen und persönliche Vorlieben
- Bricks-Inhalte
- Addon-Verknüpfungen
- Premium-Addon-Versionen
- SNN-BRX Child Theme
- Native Editoren und Ressourcen
- Updates und Plugin-Auswahl
- Multisite und Daten

<a id="installation"></a>

## Installation und erste Schritte

Installiere das Plugin-ZIP über Plugins → Installieren → Plugin hochladen. Aktiviere das Plugin und öffne die Einstellungsseite. Vor dem Upgrade von 1.0.0 eigene Konstanten und Filter prüfen. Die native Toolbar-Sichtbarkeit wird nach dem Upgrade von WordPress beziehungsweise Bricks bestimmt.

<a id="features"></a>

## Funktionen

### Website-Einstellungen und persönliche Vorlieben

Öffne Bricks → QuickNav. Ohne aktives Bricks findest du die Seite unter Einstellungen → Bricks QuickNav. Wähle Toolbar-Name, Icon, Position, Sichtbarkeit, kompaktes Menü, Anzahl, Sortierung und Menübereiche. Persönliche Sichtbarkeit und Anzahl findest du unter Benutzer → Profil; sie gelten für die aktuelle Website.

### Bricks-Inhalte

Wähle öffentliche Inhaltstypen, die in Bricks aktiviert sind. Angezeigt werden mit Bricks bearbeitete Inhalte, die du bearbeiten darfst. Unveröffentlichte Inhalte sind optional und standardmäßig ausgeschaltet. WordPress- und Bricks-Berechtigungen gelten gemeinsam. Die Template-Typen verwenden dieselbe begrenzte Template-Auswahl; erhöhe Einträge je Gruppe für weitere Templates.

### Addon-Verknüpfungen

Bestehende Framework-, Snippet-Manager- und Bricks-Addon-Integrationen bleiben verfügbar. Schalte jede Integration einzeln ein, zeige sie direkt im Hauptmenü und wähle, ob Untereinträge erscheinen. Die Schalter ändern ausschließlich die QuickNav-Navigation. Sie installieren, aktivieren, deaktivieren oder konfigurieren keine anderen Plugins. Inaktive Integrationen erzeugen keine Links. Registrierte Menüberechtigungen werden geprüft; im Frontend gelten konservative Verwaltungsrechte. Aktive Bricks-Addons erscheinen unter einem sichtbaren Add-ons-Menü, auch wenn nur eines verfügbar ist. Ein ausdrücklich gewählter Direktlink verschiebt das Addon auf die erste Menüebene. Drittanbieter-Adapter verwenden den gemeinsamen Filter ddw/quicknav/bricks_integrations; siehe Anleitung zur Addon-Registrierung. [Registrierungsanleitung](docs/addon-registration-de.md).

### Premium-Addon-Versionen

GutenBricks 1.x und 2.x verwenden ihre jeweiligen Einstellungs- und Lizenzziele. BricksUltimate berücksichtigt White-Label-Menünamen und Slugs. Bricksforge 4 verwendet aktuelle Werkzeugziele und Protokolle. Swiss-Knife-Verknüpfungen berücksichtigen lizenzabhängig verfügbare Seiten. BricksExtras behält seine native Einstellungsnavigation.

### SNN-BRX Child Theme

Die bisherigen SNN-BRX-Verknüpfungen unterstützen den aktuellen Theme-Namen und umbenannte Ordner. Aktivierte native Admin-Seiten, die Bildoptimierung unter Medien, bestehende Snippet-Tabs, Fehlerprotokolle und Bearbeitungslinks für moderne Snippets sind erreichbar. Ausgeschaltete SNN-Module erzeugen keine toten Links. QuickNav aktiviert oder führt keine Snippets aus. SNN-BRX ist ein Child Theme und erscheint separat vor Add-ons, mit seinem angepassten nativen Namen oder SNN-BRX. Hauptziel sind die Code-Snippets, wenn aktiv, sonst die SNN-Einstellungen. Andere Child Themes verlinken auf ihre functions.php, sofern Datei-Editor und Benutzerrechte dies erlauben. Die Child-Theme-Sichtbarkeit liegt unter Toolbar & Inhalte. Entwickler verwenden ddw/quicknav/bricks_child_theme; siehe [Registrierungsanleitung](docs/child-theme-registration-de.md).

### Native Editoren und Ressourcen

QuickNav berücksichtigt die native Toolbar-Sichtbarkeit in Bricks, Gutenberg und Site Editor. Das Plugin erzwingt keine Toolbar mehr, verschiebt keine Editor-Bereiche und lässt das WordPress-Logo sichtbar. Bestehende Dokumentations-, Community-, Produkt- und Autorenlinks bleiben standardmäßig verfügbar. Die erste Menüebene verwendet einheitliche lokale Lucide-Linien-Icons; tiefere Untermenüs verwenden Text. Die KI Einrichtung (Bricks AI) liegt unter Einstellungen.

### Updates und Plugin-Auswahl

Der enthaltene deckerweb Updater prüft dieses öffentliche GitHub-Repository über den WordPress-Updateablauf. Ein GitHub-Token ist nicht nötig. Die deckerweb Library verwendet einen lokalen Katalog; der optionale Online-Katalog ist standardmäßig ausgeschaltet. Externe Abfragen übertragen das angefragte Repository/Paket sowie übliche HTTP-Verbindungsdaten. Es gibt keine Telemetrie.

### Multisite und Daten

Die Netzwerkaktivierung stellt die Navigation auf den einzelnen Websites bereit; im Netzwerk-Admin gibt es keine QuickNav-Toolbar oder Einstellungsseite. Website-Einstellungen und persönliche Vorlieben bleiben getrennt. Deaktivierung erhält Daten. Deinstallation erhält Einstellungen standardmäßig; die ausdrücklich gewählte Löschoption entfernt nur die QuickNav-Option und Benutzerpräferenzen der jeweiligen Website. Die gemeinsame Library bereinigt temporäre Daten erst beim letzten installierten Host. Andere Plugins und sämtliche Bricks-Inhalte bleiben erhalten.

<a id="faq"></a>

## FAQ

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

Addon-Definitionen verwenden ddw/quicknav/bricks_integrations, das aktive Child Theme verwendet ddw/quicknav/bricks_child_theme. Bezeichnungen, native Ziele und optionale Unterseiten lassen sich angeben. QuickNav prüft weiterhin die Seitenregistrierung und Benutzerrechte. Siehe [Addon-Beispiel](docs/addon-registration-de.md) und [Child-Theme-Beispiel](docs/child-theme-registration-de.md).

<a id="changelog"></a>

## Änderungsverlauf

### 2.0.1 · 21. Oktober 2026

- **Verbessert:** Aktualisierte deckerweb Library 1.1.0 und Updater 2.2.0 mit geprüften Katalogpaketen und Hinweisen auf Git-Updater-Konflikte.
- **Behoben:** Änderungsverlauf-Dialoge bleiben bei fremden Dialog-Styles geschlossen und behalten native Schließ- und Dokumentations-Fallbacks.
- **Sonstiges:** Benötigt PHP 8.1 oder neuer; WordPress 6.7 bleibt unterstützt.

### 2.0.0 · 10. Oktober 2026

- **Verbessert:** Alle angezeigten Bricks-Inhaltstypen und Template-Listen erhalten dezente Überschriften für veröffentlichten und nicht veröffentlichten Inhalt; unveröffentlichte Einträge zeigen ihren konkreten Status in Klammern.
- **Verbessert:** Neue Grafiken der QuickNav-Serie mit zentriertem Icon, deutschen und englischen Bannern sowie Icon-Exporten in 512/1024 Pixeln.
- **Verbessert:** Separate optionale Child-Theme-Navigation vor Add-ons, native SNN-BRX-Bezeichnung und Feature-Ziele sowie dokumentierte Addon-/Child-Theme-Erweiterungsfilter.
- **Verbessert:** Einheitliche Lucide-Icons auf der ersten Menüebene, ein sichtbares Add-ons-Untermenü und gemeinsame Addon-Registrierung. Die KI Einrichtung (Bricks AI) liegt unter Einstellungen.
- **Behoben:** Kompakte 16-Pixel-Toolbar-Icons auf einer Linie mit den Bezeichnungen; CSS-Änderungen erneuern den Cache auch bei Testpaketen mit gleicher Plugin-Version.
- **Neu:** Website-Einstellungen und persönliche Toolbar-Vorlieben im Benutzerprofil.
- **Neu:** Einzeln wählbare Addon-Verknüpfungen, Direktlinks und optionale Addon-Untermenüs.
- **Neu:** Weitere Bricks-Inhaltstypen, Sortierung und optional unveröffentlichte Inhalte.
- **Neu:** Navigation für zwölf weitere öffentliche Bricks-Addons mit einzeln konfigurierbaren Links.
- **Verbessert:** Versionsabhängige GutenBricks-1/2-Links, BricksUltimate-White-Label-Menüs, Bricksforge-4-Ziele und lizenzabhängige Swiss-Knife-Verknüpfungen.
- **Verbessert:** Navigation berücksichtigt Inhaltsberechtigungen und verwendet begrenzte Inhaltsabfragen wieder.
- **Verbessert:** SNN-BRX unterstützt das aktuelle Theme, eingeschaltete Admin-Module und moderne Snippet-Verknüpfungen.
- **Behoben:** Abfrage- und Einstellungsfilter funktionieren; Alle Schriften öffnet die Übersicht; mehrere Framework- und Snippet-Menüs bestehen nebeneinander.
- **Behoben:** Builder-URLs erhalten vorhandene Parameter; inaktives Bricks unterbricht WordPress nicht.
- **Sonstiges:** Die native Toolbar-Anzeige ersetzt erzwungene Toolbar- und Editor-Layout-Anpassungen. Das WordPress-Logo bleibt sichtbar. Bestehende Ressourcen und Links bleiben verfügbar.
- **Sonstiges:** Enthält deckerweb Library 0.9.0 und deckerweb Updater 2.1.0.

### 1.0.0 · 12. Mai 2025

- **Neu:** Erstveröffentlichung mit Navigation zu Bricks-Inhalten, Einstellungen, Integrationen und Ressourcen.

<a id="support"></a>

## Projekt und Unterstützung

Entwickelt und herausgegeben von David Decker – DECKERWEB. QuickNav bietet Verknüpfungen zu bestehenden Bricks- und WordPress-Funktionen. Das Plugin ersetzt den Builder nicht. Normale Probleme über [Issues](https://github.com/deckerweb/bricks-quicknav/issues) melden. Sicherheitslücken privat über [GitHub](https://github.com/deckerweb/bricks-quicknav/security/advisories/new) melden. [Entwicklung unterstützen](https://ko-fi.com/deckerweb).

GPL v2 oder höher. Einstellungsdarstellung und Lebenszyklus aus David Deckers [Breakdance QuickNav 2.0.0](https://github.com/deckerweb/breakdance-quicknav/tree/v2.0.0) und dessen Oxygen-QuickNav-Referenz übernommen, GPL-2.0-or-later. Seriengrafiken: eigene Vektorgrafiken von deckerweb. Inter-Schrift ist in Pfade umgewandelt; die SIL-OFL-1.1-Lizenz liegt unter assets/INTER-LICENSE.txt bei. Dieses Plugin lädt keine Schriften. Bricks- und Addon-Marken gehören ihren jeweiligen Eigentümern. Toolbar-Icons: Lucide (ISC/MIT); mitgelieferte Lizenz und festgehaltener Quellstand unter assets/lucide.

© 2025–2026 David Decker – DECKERWEB.
