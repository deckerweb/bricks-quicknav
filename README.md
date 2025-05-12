# Bricks QuickNav – for Bricks Builder

![Bricks QuickNav plugin banner](https://repository-images.githubusercontent.com/981974040/34ef63be-6f83-4a99-9004-1e5b11e16b9b)

The **Bricks QuickNav** plugin adds a quick-access navigator to the WordPress Admin Bar (Toolbar). It allows easy access to Bricks Builder Templates, Headers, Footers, and (regular WordPress) Pages edited with Bricks, along with lots of other essential settings and stuff like plugin integrations and so on.

* Contributors: [David Decker](https://github.com/deckerweb), [contributors](https://github.com/deckerweb/bricks-quicknav/graphs/contributors)
* Tags: bricks, bricks builder, quicknav, admin bar, toolbar, site builder, administrators
* Requires at least: 6.7
* Requires PHP: 7.4
* Stable tag: [main](https://github.com/deckerweb/bricks-quicknav/releases/latest)
* Donate link: https://paypal.me/deckerweb
* License: GPL v2 or later

---

[Support Project](#support-the-project) | [Installation](#installation) | [Updates](#updates) | [Description](#description) | [FAQ](#frequently-asked-questions) | [Custom Tweaks](#custom-tweaks) | [Changelog](#changelog) | [Plugin Scope / Disclaimer](#plugin-scope--disclaimer)

---

## Support the Project

If you find this project helpful, consider showing your support by buying me a coffee! Your contribution helps me keep developing and improving this plugin.

Enjoying the plugin? Feel free to treat me to a cup of coffee ☕🙂 through the following options:

- [![ko-fi](https://ko-fi.com/img/githubbutton_sm.svg)](https://ko-fi.com/W7W81BNTZE)
- [Buy me a coffee](https://buymeacoffee.com/daveshine)
- [PayPal donation](https://paypal.me/deckerweb)
- [Join my **newsletter** for DECKERWEB WordPress Plugins](https://eepurl.com/gbAUUn)

---

## Installation

#### **Quick Install – as Plugin**
[![Download Plugin](https://raw.githubusercontent.com/deckerweb/bricks-quicknav/refs/heads/main/assets/button-download-plugin.png)](https://github.com/deckerweb/bricks-quicknav/releases/latest/download/bricks-quicknav.zip)

1. **Download ZIP:** [**bricks-quicknav.zip**](https://github.com/deckerweb/bricks-quicknav/releases/latest/download/bricks-quicknav.zip)
2. Upload via WordPress Plugins > Add New > Upload Plugin
3. Once activated, you’ll see the **Bricks** menu item in the Admin Bar.

#### **Alternative: Use as Code Snippet**
[![Download Code Snippet](https://raw.githubusercontent.com/deckerweb/bricks-quicknav/refs/heads/main/assets/button-download-snippet.png)](https://github.com/deckerweb/bricks-quicknav/releases/latest/download/ddw-bricks-quicknav.code-bricks.json)  
1. Below, download the appropriate snippet version
2. Activate or deactivate in your snippets plugin

[**Download .json**](https://github.com/deckerweb/bricks-quicknav/releases/latest/download/ddw-bricks-quicknav.code-snippets.json) version for: _Code Snippets_ (free & Pro), _Advanced Scripts_ (Premium), _Scripts Organizer_ (Premium)  
➔ just use their elegant script import features 
➔ in _Scripts Organizer_ use the "Code Snippets Import" 

For all other snippet manager plugins just use our plugin's main .php file [`bricks-quicknav.php`](https://github.com/deckerweb/bricks-quicknav/blob/master/bricks-quicknav.php) and use its content as snippet (bevor saving your snippet: please check for your plugin if the opening `<?php` tag needs to be removed or not!).

➔ Please decide for one of both alternatives!

#### Minimum Requirements 
* WordPress version 6.7 or higher
* PHP version 7.4 or higher (better 8.3+)
* MySQL version 8.0 or higher / OR MariaDB 10.1 or higher
* Administrator user with capability `manage_options` and `activate_plugins`

### Tested Compatibility
- **Bricks**: 1.12.4 ... 2.0.0 Alpha
- **WordPress**: 6.7.2 ... 6.8.1
- **PHP**: 8.0 – 8.3

---

## Updates 

#### For Plugin Version:

1) Alternative 1: Just download a new [ZIP file](https://github.com/deckerweb/bricks-quicknav/releases/latest/download/bricks-quicknav.zip) (see above), upload and override existing version. Done.

2) Alternative 2: Use the (free) [**_Git Updater_ plugin**](https://git-updater.com/) and get updates automatically.

3) Alternative 3: Upcoming! – In future I will built-in our own deckerweb updater. This is currently being worked on for my plugins. Stay tuned!

#### For Code Snippet Version:

Just manually: Download the latest Snippet version (see above) and import it in your favorite snippets manager plugin. – You can delete the old snippet; then just activate the new one. Done.

---

## Description 

### How this Plugin Works 

1. **Pages and Bricks Templates (including all its types)**: Displays up to 20 items (by default), ordered by the last modified date (descending). The "Pages" menu only shows pages built with Bricks by checking the `_bricks_editor_mode` custom field.
2. **Settings**: Direct links to all relevant Bricks settings & sections.
3. **Child Themes functions.php** (if child is used), including the special "plugin-like" _SNN BRX_ child theme; plus support for 6 typical code snippets plugins (to organize custom code & scripts)
4. Lots of typical & popular **Bricks-specific add-on plugins** with their settings or actions
5. **Additional Links**: Includes links to lots of official resources like the Bricks website and Facebook group. Some may contain affiliate links.
6. **Community Links**: Collected and hand-picked community links grouped by topic.
7. **About**: Includes links to the plugin author.
8. Optionally show **Admin Bar** also **within Bricks Builder** itself (Builder context)
9. Show **Admin Bar** also **in Block Editor** full screen mode. (Not there in WP default but this plugin here changes that!)


### List of Integrations 
* Child Themes:
	- Bricks Child Themes in general
	- [SNN BRX Child Theme (by Sinan Isler)](https://github.com/sinanisler/snn-brx-child-theme) (free)

* Plugins:
	- Bricksforge (Premium)
	- BricksExtras (Premium)
	- BricksUltimate (Premium)
	- Bricksable (free)
	- EasyDash (free)
	- Bricks Admin Dashboard (Premium)
	- Swiss Knife Bricks (Premium)
	- Bricks Element Manager (free)
	- Yabe Webfonts (free + Premium)
	- [Builder List Pages](https://github.com/deckerweb/builder-list-pages) (free)
	- Max Addons (free Version only, currently)
	- [Bricks Remote Template Sync](https://github.com/tweschke/bricks-remote-template-sync) (free)
	- [Flex Addons](https://github.com/TimpCreative/bricks-flex-addons) (free currently; dev/alpha)

* Code Snippets:
	- [MA Custom Fonts](https://www.altmann.de/en/blog-en/code-snippet-custom-fonts/) (free)

* Frameworks:
	- Automatic.CSS (Premium)
	- Add-On: Frames (Premium)
	- Add-On: ACSS Purger (free Plugin)
	- Core Framework (free & Premium)
	- Add-On: Brixies.co (Premium library)
	- WindPress (free & Premium)

* Other Plugins:
	- System Dashboard (free)

* Snippets Manager Plugins:
	- Code Snippets (free & Premium)
	- Advanced Scripts (Premium)
	- Scripts Organizer (Premium)
	- WPCodeBox (Premium)
	- FluentSnippets (free)
	- WPCode Lite (free)

---

## Frequently Asked Questions 

### How can I change / tweak things?
Please see here under [**Custom Tweaks via Constants**](#custom-tweaks-via-constants) what is possible!

### Why no settings page? 
That overcomplicates things – setting up a few constants is easy and fast. Also, needed code for settings makes plugin more "bloated" and even less fitting for the alternative use as a code snippet. Use of code snippet is only possible with a "one-file-plugin"! And remember, this is a plugin geared towards Administrators who are normally able to add a little line of code if needed.

### Why can't certain strings not be translated?
To keep translation files as small and clean as possible not all strings need translations, especially those product names like "Automatic.CSS" or "Bricksforge" and so on. These product and/ or company names are as is and **not** declared with regular translation functions.

### Why is this functionality not baked into _Bricks Builder_ itself?
I don't know. Not everything needs to be built-in. That's what plugins are for: those who _need_ this functionality can install and use them. Also, _Bricks QuickNav_ just goes far and beyond, it adds the things, I wanted and/or needed _myself_. So, that's just what you get here for free.

### Why did you create this plugin?
Because I needed (and wanted!) it myself for the sites I maintain. [Read the backstory here ...](#plugins-backstory)

### Why is this plugin not on wordpress.org plugin repository?
Because the restrictions there for plugin authors are becoming more and more. It would be possible but I don't want that anymore. The same for limited support forums for plugin authors on .org. I have decided to leave this whole thing behind me.

--- 

## Custom Tweaks via Constants 

### Default capability (aka permission) 
The intended usage of this plugin is for Administrator users only. Therefore the default capability to see the new Admin Bar node is set to `activate_plugins`. You can change this via constant 
in `wp-config.php` or via a Code Snippets plugin, for example:
```
define( 'BXQN_VIEW_CAPABILITY', 'edit_posts' );
```


### Restrict to defined user IDs only 
You can define an array of user IDs (can also be only _one_ ID) and that way restrict showing the Bricks Admin Bar item only for those users. Define that via `wp-config.php` or via a Code Snippets plugin:
```
define( 'BXQN_ENABLED_USERS', [ 1, 500, 867 ] );
```
This would enable only for the users with the IDs 1, 500 and 867. Note the square brackets around, and no single quotes, just the ID numbers.

For example you are one of many admin users (role `administrator`) but _only you_ want to show it _for yourself_. Given you have user ID 1:
```
define( 'BXQN_ENABLED_USERS', [ 1 ] );
```
That way only you can see it, the other admins can't!


### Name of main menu item 
The default is just "Bricks" – catchy and short. However, if you don't enjoy "Bricks" you can tweak that also via the constant `BXQN_NAME_IN_ADMINBAR` – define that also via `wp-config.php` or via a Code Snippets plugin:
```
define( 'BXQN_NAME_IN_ADMINBAR', 'BRX' );
```


### Position of main menu item 
You can define an integer value to adjust the menu position of the main menu item. Define that via `wp-config.php` or via a Code Snippets plugin:
```
define( 'BXQN_MENU_POSITION', 81 );
```
This example will place it somewhere after the default `+ New` item. Note: the default value is `999` which puts the item quite on the middle or right side of the Admin Bar.


### Default icon of main menu item 
The bold "b" default logo icon is awesome but at the same time a bit too normal? Who knows. – At least within the Admin Bar? Therefore, I offer TWO alternative icons for you to use and be happy with: the yellow Bricks "b" icon and the bricked icon with the 3 brick stones. You can tweak that via a constant in `wp-config.php` or via a Code Snippets plugin:
```
/** Bricks yellow "b" icon */
define( 'BXQN_ICON', 'yellow' );

/** Bricks "bricked" icon (3 brick stones) */
define( 'BXQN_ICON', 'brick' );
```


### Adjust the number of displayed Templates/ Pages 
The default number of displayed Templates/ Pages is 20. That means up to 20 items, starting from latest (newest) down to older ones. You can adjust that value via constant in `wp-config.php` or via a Code Snippets plugin:
```
define( 'BXQN_NUMBER_TEMPLATES', 5 );
```
In that example it would only display up to 5 items. NOTE: just add the number, no quotes around it.


### Disable footer items (_Links_, _Community_ and _About_)
To disable these menu items, just use another constant in `wp-config.php` or via a Code Snippets plugin:
```
define( 'BXQN_DISABLE_FOOTER', 'yes' );
```


### Use "Compact Mode"
This mode just disables a few Admin Bar items to free up some precious space (for example, not displaying the link for "Sidebars").
```
define( 'BXQN_COMPACT_MODE', TRUE );
```
Note: Just use `TRUE` or `FALSE`, no quotes around it.


### Use the Admin Bar also _WITHIN_ the Builder itself 
Yes, it's possible, just add this constant declaration in `wp-config.php` or via a Code Snippets plugin:
```
define( 'BXQN_ADMINBAR_IN_BUILDER', 'yes' );
```

---

## Changelog 

### [The Releases](https://github.com/deckerweb/bricks-quicknav/releases)

### 🎉 v1.0.0 – 2025-05-12
* Initial release
* Includes support for Bricks Builder 1.12.x as well as Bricks 2.0-alpha
* Includes support for 13 Bricks specific plugins/ Add-Ons, plus 1 general plugin, plus 1 "plugin-like" Code Snippet
* Includes support for 3 Frameworks, plus 3 "Add-Ons" (Libraries)
* Includes support for Bricks Child Themes; specifically also for "SNN BRX Bricks Child Theme" (third-party)
* Installable and updateable via [Git Updater plugin](https://git-updater.com/)
* Includes `.pot` file, plus packaged German translations, including new `l10n.php` files!


---

## Plugin's Backstory 

_I needed and wanted this plugin (Bricks QuickNav) myself for a long time so I developed it. I work with Bricks Builder since summer of 2022 and there was the lovely BricksLabs Bricks Navigator already there. However, I wanted a lot of things differently so it became clear I needed to get active somehow. When I started coding and testing and playing around it became clear to me I wanted a full featured and full integrated "thing". So my take on this "Bricks Admin Bar Thing" is different than every other such plugin, in that way what it lists, integrates and supports. And since I like to share my stuff with the wider Bricks Community I decided to give it away for free. So my hope is, that you will enjoy it as well (the finished plugin)._

–– David Decker, plugin developer, in May of 2025

---

## Plugin Scope / Disclaimer

This plugin comes as is. I have no intention to add support for every little detail / third-party plugin / library etc. Its main focus is support for the template types and Oxygen 6+ settings. Plugin support is added where it makes sense for the daily work of an Administrator and Site Builder.

_Disclaimer 1:_ So far I will support the plugin for breaking errors to keep it working. Otherwise support will be very limited. Also, it will NEVER be released to WordPress.org Plugin Repository for a lot of reasons. Furthermore, I will ONLY add support for direct Oxygen 6+ add-on plugins. And I can only add support if I would own a license myself (for testing etc.). Therefore, if there might be Oxygen 6+ plugins you want me to add integration for, please open an issue on the plugin page on GitHub so we might discuss that. (Thanks in advance!)

_Disclaimer 2:_ All of the above might change. I do all this stuff only in my spare time.

_Most of all:_ Have fun building great Oxygen 6+ powered sites!!! ;-)

---

Official _Bricks_ company / product logo icons: © bricksbuilder.io

Icon used in promo graphics: [© Remix Icon](https://remixicon.com/)

Readme & Plugin Copyright: © 2025, David Decker – DECKERWEB.de