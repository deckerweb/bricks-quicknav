<?php
/*
Plugin Name:        Bricks QuickNav
Plugin URI:         https://github.com/deckerweb/bricks-quicknav
Description:        Adds a quick-access navigator (aka QuickNav) to the WordPress Admin Bar (Toolbar). It allows easy access to Bricks Templates and Pages edited with Bricks, along with some other essential settings.
Project:            Code Snippet: DDW Bricks QuickNav
Version:            1.0.0
Author:             David Decker – DECKERWEB
Author URI:         https://deckerweb.de/
Text Domain:        bricks-quicknav
Domain Path:        /languages/
License:            GPL-2.0-or-later 
License URI:        https://www.gnu.org/licenses/gpl-2.0.html
Requires WP:        6.7
Requires PHP:       7.4
Update URI:         https://github.com/deckerweb/bricks-quicknav/
GitHub Plugin URI:  https://github.com/deckerweb/bricks-quicknav
Primary Branch:     main
Copyright:          © 2025, David Decker – DECKERWEB

TESTED WITH:
Product			Versions
--------------------------------------------------------------------------------------------------------------
PHP 			8.0, 8.3
WordPress		6.8 ... 6.8.1
Bricks Builder	1.12.4 ... 2.0.0 Alpha
--------------------------------------------------------------------------------------------------------------

VERSION HISTORY:
Date        Version     Description
--------------------------------------------------------------------------------------------------------------
2025-05-12	1.0.0	    Initial release
2025-05-01	0.5.0       Internal test version
2025-04-30	0.0.0	    Development start
--------------------------------------------------------------------------------------------------------------
*/

/** Prevent direct access */
if ( ! defined( 'ABSPATH' ) ) exit;  // Exit if accessed directly.

if ( ! class_exists( 'DDW_Bricks_QuickNav' ) ) :

class DDW_Bricks_QuickNav {

	/** Class constants & variables */
	private static $version;
	private static $name;
	private static $plugin_url;
	private static $author_url;
	private static $github_url;
	
	private const NUMBER_OF_TEMPLATES = 20;
	
	private static $menu_position = 999;  // default: 999
	private static $has_snippets_plugin = 0;
	private static $compact_mode = FALSE;

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init',                        [ $this, 'show_quicknav' ], 20 );  // this will add the Admin Bar items
		add_action( 'admin_enqueue_scripts',       [ $this, 'enqueue_admin_bar_styles' ] );  // for Admin
		add_action( 'wp_enqueue_scripts',          [ $this, 'enqueue_admin_bar_styles' ] );  // for front-end
		add_action( 'enqueue_block_editor_assets', [ $this, 'adminbar_block_editor_fullscreen' ] );  // for Block Editor
		add_action( 'init',                        [ $this, 'maybe_add_adminbar_in_builder' ], 20 );
		
		add_filter( 'parent_file',                 [ $this, 'bricks_template_parent' ], 5 );
		
		$plugin_data      = get_file_data( __FILE__, [ 'name' => 'Plugin Name', 'version' => 'Version', 'plugin_url' => 'Plugin URI', 'author_url' => 'Author URI', 'github_url' => 'GitHub Plugin URI' ] );
		self::$version    = $plugin_data[ 'version' ];
		self::$name       = $plugin_data[ 'name' ];
		self::$plugin_url = $plugin_data[ 'plugin_url' ];
		self::$author_url = $plugin_data[ 'author_url' ];
		self::$github_url = $plugin_data[ 'github_url' ];
		
		add_filter( 'debug_information',           [ $this, 'site_health_debug_info' ], 9 );
	}
	
	/**
	 * Show the QuickNav menu, respect user-defined menu position.
	 *
	 * @since 1.0.0
	 */
	public function show_quicknav() {
		
		/** If user has defined a menu position, use that */
		if ( defined( 'BXQN_MENU_POSITION' ) ) self::$menu_position = BXQN_MENU_POSITION;
		
		/** Finally, start the show :) */
		add_action( 'admin_bar_menu', [ $this, 'add_admin_bar_menu' ], intval( self::$menu_position ) );
	}
	
	/**
	 * Is 'Compact Mode' active?
	 *   Disable some (maybe) "not so important" nodes to show fewer items.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function is_compact_mode(): bool {
		
		self::$compact_mode = ( defined( 'BXQN_COMPACT_MODE' ) ) ? (bool) BXQN_COMPACT_MODE : self::$compact_mode;
			
		return self::$compact_mode;
	}
	
	/**
	 * Check if Snippet "MA Custom Fonts" is active or not.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function has_snippet_ma_customfonts(): bool {
		return class_exists( 'MA_CustomFonts' );
	}
	
	/**
	 * Get specific Admin Color scheme colors we need. Covers all 9 default
	 *	 color schemes coming with a default WordPress install.
	 *   (helper function)
	 *
	 * @since 1.0.0
	 *
	 * @return array  Array of color scheme parameters.
	 */
	private function get_scheme_colors() {
		
		$scheme_colors = [
			'fresh' => [
				'bg'    => '#1d2327',
				'base'  => 'rgba(240,246,252,.6)',
				'hover' => '#72aee6',
			],
			'light' => [
				'bg'    => '#e5e5e5',
				'base'  => '#999',
				'hover' => '#04a4cc',
			],
			'modern' => [
				'bg'    => '#1e1e1e',
				'base'  => '#f3f1f1',
				'hover' => '#33f078',
			],
			'blue' => [
				'bg'    => '#52accc',
				'base'  => '#e5f8ff',
				'hover' => '#fff',
			],
			'coffee' => [
				'bg'    => '#59524c',
				'base'  => 'hsl(27.6923076923,7%,95%)',
				'hover' => '#c7a589',
			],
			'ectoplasm' => [
				'bg'    => '#523f6d',
				'base'  => '#ece6f6',
				'hover' => '#a3b745',
			],
			'midnight' => [
				'bg'    => '#363b3f',
				'base'  => 'hsl(206.6666666667,7%,95%)',
				'hover' => '#e14d43',
			],
			'ocean' => [
				'bg'    => '#738e96',
				'base'  => '#f2fcff',
				'hover' => '#9ebaa0',
			],
			'sunrise' => [
				'bg'    => '#cf4944',
				'base'  => 'hsl(2.1582733813,7%,95%)',
				'hover' => 'rgb(247.3869565217,227.0108695652,211.1130434783)',
			],
		];
		
		/** No filter currently b/c of sanitizing issues with the above CSS values */
		//$scheme_colors = (array) apply_filters( 'ddw-bxqn/wp-scheme-colors', $scheme_colors );
		
		return $scheme_colors;
	}
	
	/**
	 * Enqueue custom styles for the admin bar.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_admin_bar_styles() {
		
		/**
		 * Depending on user color scheme get proper base and hover color values for the main item (svg) icon.
		 */
		$user_color_scheme = get_user_option( 'admin_color' );
		$user_color_scheme = ( is_admin() || is_network_admin() ) ? $user_color_scheme : 'fresh';
		$admin_scheme      = $this->get_scheme_colors();
		
		$base_color  = $admin_scheme[ $user_color_scheme ][ 'base' ];
		$hover_color = $admin_scheme[ $user_color_scheme ][ 'hover' ];
		
		$inline_css = sprintf(
			'
			/* for icons */
			#wpadminbar .has-icon .icon-svg svg {
				display: inline-block;
				margin-bottom: 3px;				
				vertical-align: middle;
				width: 16px;
				height: 16px;
			}
			
			.bricks_page_bricksforge #wpadminbar .has-icon .icon-svg svg {
				position: inherit;
				top: 3px;
			}
			
			#wpadminbar .has-icon .wp-admin-bar-arrow {
				margin-top: 2px;
			}
			
			/* for separator :has(a[target="_blank"]) :first-of-type:has(a[target="_blank"]) */
			ul.ab-submenu li.has-separator:first-of-type {
				border-top: 1px dashed rgba(255, 255, 255, 0.33);
				padding-top: 5px;
			}
			'
		);
		
		if ( is_admin_bar_showing() ) {
			wp_add_inline_style( 'admin-bar', $inline_css );
		}
	}

	/**
	 * Whether the current user is allowed to use the Builder context of Bricks.
	 *
	 * @link https://wordpress.stackexchange.com/a/190298/14380
	 * @link https://www.isitwp.com/display-theme-information-with-get_theme_data/
	 *
	 * @since 1.0.0
	 *
	 * @return bool  True if the active theme is Bricks or a child theme of
	 *               Bricks AND if `bricks_is_builder` function exists AND if
	 *               current user can use builder OR false otherwise.
	 */
	private function user_can_use_bricks_builder(): bool {
		$parent_theme = wp_get_theme( get_template() );
		return ( 'Bricks' === $parent_theme->get( 'Name' ) && function_exists( 'bricks_is_builder' ) && \Bricks\Capabilities::current_user_can_use_builder() );
	}
	
	/**
	 * Get SVG code for specified icon.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $icon  Key for specified icon type.
	 * @return string $svg   SVG code for icon.
	 */
	private function get_icon( $icon ) {
		
		/** Check icon type */
		switch ( sanitize_key( $icon ) ) {
			case 'bricks-b':
				$svg = '<span class="icon-svg ab-icon"><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd"><path d="M7.94514768,0 L8.35021097,0.253164557 L8.35021097,7.29113924 C9.77919139,6.34598684 11.3600476,5.87341772 13.092827,5.87341772 C15.5907298,5.87341772 17.6610326,6.74542025 19.3037975,8.48945148 C20.9240587,10.2334827 21.7341772,12.382547 21.7341772,14.9367089 C21.7341772,17.5021225 20.9184329,19.6511868 19.2869198,21.3839662 C17.6441549,23.1279975 15.579478,24 13.092827,24 C10.9212268,24 9.06470532,23.2236365 7.52320675,21.6708861 L7.52320675,23.5780591 L3,23.5780591 L3,0.556962025 L7.94514768,0 Z M12.2320675,10.4472574 C11.0393752,10.4472574 10.0436046,10.8523166 9.24472574,11.6624473 C8.44584692,12.4950815 8.0464135,13.5864911 8.0464135,14.9367089 C8.0464135,16.2869266 8.44584692,17.3727104 9.24472574,18.1940928 C10.0323527,19.0154753 11.0281234,19.4261603 12.2320675,19.4261603 C13.5035225,19.4261603 14.5330481,18.9985978 15.3206751,18.1434599 C16.0970503,17.2995738 16.4852321,16.2306675 16.4852321,14.9367089 C16.4852321,13.6427502 16.0914245,12.5682181 15.3037975,11.7130802 C14.5161705,10.8691941 13.4922707,10.4472574 12.2320675,10.4472574 Z" fill="currentcolor" fill-rule="nonzero"></path></g></svg></span> ';
				break;
			case 'bricks-yellow':
				$svg = '<span class="icon-svg ab-icon"><svg clip-rule="evenodd" fill-rule="evenodd" stroke-linejoin="round" stroke-miterlimit="2" viewBox="0 0 772 772" xmlns="http://www.w3.org/2000/svg"><path d="m0 0h772v772h-772z" fill="#ffd53e"/><path d="m0 0h772v772h-772z" fill="none"/><path d="m25.188 11.344.75.469v13.031c2.645-1.75 5.572-2.625 8.781-2.625 4.625 0 8.458 1.614 11.5 4.844 3 3.229 4.5 7.208 4.5 11.937 0 4.75-1.511 8.729-4.531 11.938-3.042 3.229-6.865 4.843-11.469 4.843-4.021 0-7.459-1.437-10.313-4.312v3.531h-8.375v-42.625zm7.937 19.344c-2.208 0-4.052.749-5.531 2.25-1.479 1.541-2.219 3.562-2.219 6.062s.74 4.51 2.219 6.031c1.458 1.521 3.302 2.282 5.531 2.282 2.354 0 4.26-.792 5.719-2.375 1.437-1.563 2.156-3.542 2.156-5.938s-.729-4.385-2.187-5.969c-1.459-1.562-3.355-2.343-5.688-2.343z" fill="#212121" fill-rule="nonzero" transform="matrix(11.2518 0 0 11.2518 10.4726 8.36287)"/></svg></span> ';
				break;
			case 'bricks-brick':
				$svg = '<span class="icon-svg ab-icon"><svg height="80" viewBox="0 0 80 80" width="80" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><path d="m40 40h40v40h-40z" fill="#fe9"/><path d="m0 40h40v40h-40z" fill="#ffd53e"/><path d="m0 0h40v40h-40z" fill="#ffe66d"/></g></svg></span> ';
				break;
			case 'add':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M11 11V5H13V11H19V13H13V19H11V13H5V11H11Z"></path></svg></span> ';
				break;
			case 'page':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M20 22H4C3.44772 22 3 21.5523 3 21V3C3 2.44772 3.44772 2 4 2H20C20.5523 2 21 2.44772 21 3V21C21 21.5523 20.5523 22 20 22ZM19 20V4H5V20H19ZM7 6H11V10H7V6ZM7 12H17V14H7V12ZM7 16H17V18H7V16ZM13 7H17V9H13V7Z"></path></svg></span> ';
				break;
			case 'template':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M5 8V20H19V8H5ZM5 6H19V4H5V6ZM20 22H4C3.44772 22 3 21.5523 3 21V3C3 2.44772 3.44772 2 4 2H20C20.5523 2 21 2.44772 21 3V21C21 21.5523 20.5523 22 20 22ZM7 10H11V14H7V10ZM7 16H17V18H7V16ZM13 11H17V13H13V11Z"></path></svg></span> ';
				break;
			case 'filter':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M10 18H14V16H10V18ZM3 6V8H21V6H3ZM6 13H18V11H6V13Z"></path></svg></span> ';
				break;
			case 'bundle':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M3 10H2V4.00293C2 3.44903 2.45531 3 2.9918 3H21.0082C21.556 3 22 3.43788 22 4.00293V10H21V20.0015C21 20.553 20.5551 21 20.0066 21H3.9934C3.44476 21 3 20.5525 3 20.0015V10ZM19 10H5V19H19V10ZM4 5V8H20V5H4ZM9 12H15V14H9V12Z"></path></svg></span> ';
				break;
			case 'tag':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M10.9042 2.10025L20.8037 3.51446L22.2179 13.414L13.0255 22.6063C12.635 22.9969 12.0019 22.9969 11.6113 22.6063L1.71184 12.7069C1.32131 12.3163 1.32131 11.6832 1.71184 11.2926L10.9042 2.10025ZM11.6113 4.22157L3.83316 11.9997L12.3184 20.485L20.0966 12.7069L19.036 5.28223L11.6113 4.22157ZM13.7327 10.5855C12.9516 9.80448 12.9516 8.53815 13.7327 7.7571C14.5137 6.97606 15.78 6.97606 16.5611 7.7571C17.3421 8.53815 17.3421 9.80448 16.5611 10.5855C15.78 11.3666 14.5137 11.3666 13.7327 10.5855Z"></path></svg></span> ';
				break;
			case 'code':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12L18.3431 17.6569L16.9289 16.2426L21.1716 12L16.9289 7.75736L18.3431 6.34315L24 12ZM2.82843 12L7.07107 16.2426L5.65685 17.6569L0 12L5.65685 6.34315L7.07107 7.75736L2.82843 12ZM9.78845 21H7.66009L14.2116 3H16.3399L9.78845 21Z"></path></svg></span> ';
				break;
			case 'snippets':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M4 18V14.3C4 13.4716 3.32843 12.8 2.5 12.8H2V11.2H2.5C3.32843 11.2 4 10.5284 4 9.7V6C4 4.34315 5.34315 3 7 3H8V5H7C6.44772 5 6 5.44772 6 6V10.1C6 10.9858 5.42408 11.7372 4.62623 12C5.42408 12.2628 6 13.0142 6 13.9V18C6 18.5523 6.44772 19 7 19H8V21H7C5.34315 21 4 19.6569 4 18ZM20 14.3V18C20 19.6569 18.6569 21 17 21H16V19H17C17.5523 19 18 18.5523 18 18V13.9C18 13.0142 18.5759 12.2628 19.3738 12C18.5759 11.7372 18 10.9858 18 10.1V6C18 5.44772 17.5523 5 17 5H16V3H17C18.6569 3 20 4.34315 20 6V9.7C20 10.5284 20.6716 11.2 21.5 11.2H22V12.8H21.5C20.6716 12.8 20 13.4716 20 14.3Z"></path></svg></span> ';
				break;
			case 'css3':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M2.8 14H4.83961L4.2947 16.7245L10.0393 18.8787L17.2665 16.4697L18.3604 11H3.4L3.8 9H18.7604L19.5604 5H4.6L5 3H22L19 18L10 21L2 18L2.8 14Z"></path></svg></span> ';
				break;
			case 'cf':
				$svg = '<span class="icon-svg"><svg width="18" height="14" viewBox="0 0 18 14" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M17.4353 5.86029H10.2935V9.73973H17.4353V5.86029Z"/><path d="M6.80752 0C3.04749 0 0 3.05087 0 6.81508C0 10.5793 3.04749 13.6302 6.80752 13.6302H10.299V9.75072H6.80752C5.1906 9.75072 3.87513 8.43928 3.87513 6.81508C3.87513 5.19636 5.18512 3.87944 6.80752 3.87944H17.4408V0H6.80752Z"/></svg></span> ';
				break;
			case 'acss':
				$svg = '<span class="icon-svg"><svg width="50" height="38" viewBox="0 0 50 38" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M50 25.8533L10.404 28.8145L21.9245 8.85699L31.4833 25.412L36.3849 25.0452L21.9245 0L0 37.9745L33.9819 29.7395L36.034 33.3121L17.5811 37.9745H43.849L38.4689 28.655L50 25.8533Z"/></svg></span> ';
				break;
			case 'frames':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M21 20C21 20.5523 20.5523 21 20 21H4C3.44772 21 3 20.5523 3 20V4C3 3.44772 3.44772 3 4 3H20C20.5523 3 21 3.44772 21 4V20ZM11 5H5V19H11V5ZM19 13H13V19H19V13ZM19 5H13V11H19V5Z"></path></svg></span> ';
				break;
			case 'windpress':
				$svg = '<span class="icon-svg"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512 512" fill="currentColor" xml:space="preserve"><g><path fill="currentColor" d="M176,384H16c-8.832,0-16,7.168-16,16c0,8.832,7.168,16,16,16h160c8.832,0,16,7.2,16,16s-7.168,16-16,16 c-8.832,0-16,7.168-16,16c0,8.832,7.168,16,16,16c26.464,0,48-21.536,48-48S202.464,384,176,384z" /></g><g><path d="M240,256c-8.832,0-16,7.168-16,16c0,8.832,7.168,16,16,16c8.832,0,16,7.2,16,16s-7.168,16-16,16H16 c-8.832,0-16,7.168-16,16c0,8.832,7.168,16,16,16h224c26.464,0,48-21.536,48-48S266.464,256,240,256z" /></g><g><path d="M288,32C164.288,32,64,132.288,64,256c0,10.88,1.056,21.536,2.56,32h128.192c-1.792-4.992-2.752-10.4-2.752-16 c0-26.464,21.536-48,48-48c44.096,0,80,35.904,80,80c0,44.128-35.904,80-80,80h-0.416C249.76,397.408,256,413.92,256,432 c0,16.032-4.864,30.944-13.024,43.456c14.56,2.976,29.6,4.544,45.024,4.544c123.712,0,224-100.288,224-224S411.712,32,288,32z" /></g></svg></span> ';
				break;
			case 'warning':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22ZM12 20C16.4183 20 20 16.4183 20 12C20 7.58172 16.4183 4 12 4C7.58172 4 4 7.58172 4 12C4 16.4183 7.58172 20 12 20ZM11 15H13V17H11V15ZM11 7H13V13H11V7Z"></path></svg></span> ';
				break;
			case 'inbox':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M4.02381 3.78307C4.12549 3.32553 4.5313 3 5 3H19C19.4687 3 19.8745 3.32553 19.9762 3.78307L21.9762 12.7831C21.992 12.8543 22 12.927 22 13V20C22 20.5523 21.5523 21 21 21H3C2.44772 21 2 20.5523 2 20V13C2 12.927 2.00799 12.8543 2.02381 12.7831L4.02381 3.78307ZM5.80217 5L4.24662 12H9C9 13.6569 10.3431 15 12 15C13.6569 15 15 13.6569 15 12H19.7534L18.1978 5H5.80217ZM16.584 14C15.8124 15.7659 14.0503 17 12 17C9.94968 17 8.1876 15.7659 7.41604 14H4V19H20V14H16.584Z"></path></svg></span> ';
				break;
			case 'font':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M10 6V21H8V6H2V4H16V6H10ZM18 14V21H16V14H13V12H21V14H18Z"></path></svg></span> ';
				break;
			case 'sidebars':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M3 3H21C21.5523 3 22 3.44772 22 4V20C22 20.5523 21.5523 21 21 21H3C2.44772 21 2 20.5523 2 20V4C2 3.44772 2.44772 3 3 3ZM9 5V19H20V5H9Z"></path></svg></span> ';
				break;
			case 'settings':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M5.32943 3.27158C6.56252 2.8332 7.9923 3.10749 8.97927 4.09446C10.1002 5.21537 10.3019 6.90741 9.5843 8.23385L20.293 18.9437L18.8788 20.3579L8.16982 9.64875C6.84325 10.3669 5.15069 10.1654 4.02952 9.04421C3.04227 8.05696 2.7681 6.62665 3.20701 5.39332L5.44373 7.63C6.02952 8.21578 6.97927 8.21578 7.56505 7.63C8.15084 7.04421 8.15084 6.09446 7.56505 5.50868L5.32943 3.27158ZM15.6968 5.15512L18.8788 3.38736L20.293 4.80157L18.5252 7.98355L16.7574 8.3371L14.6361 10.4584L13.2219 9.04421L15.3432 6.92289L15.6968 5.15512ZM8.97927 13.2868L10.3935 14.7011L5.09018 20.0044C4.69966 20.3949 4.06649 20.3949 3.67597 20.0044C3.31334 19.6417 3.28744 19.0699 3.59826 18.6774L3.67597 18.5902L8.97927 13.2868Z"></path></svg></span> ';
				break;
			case 'info':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22ZM12 20C16.4183 20 20 16.4183 20 12C20 7.58172 16.4183 4 12 4C7.58172 4 4 7.58172 4 12C4 16.4183 7.58172 20 12 20ZM11 7H13V9H11V7ZM11 11H13V17H11V11Z"></path></svg></span> ';
				break;
			case 'gear':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 1L21.5 6.5V17.5L12 23L2.5 17.5V6.5L12 1ZM12 3.311L4.5 7.65311V16.3469L12 20.689L19.5 16.3469V7.65311L12 3.311ZM12 16C9.79086 16 8 14.2091 8 12C8 9.79086 9.79086 8 12 8C14.2091 8 16 9.79086 16 12C16 14.2091 14.2091 16 12 16ZM12 14C13.1046 14 14 13.1046 14 12C14 10.8954 13.1046 10 12 10C10.8954 10 10 10.8954 10 12C10 13.1046 10.8954 14 12 14Z"></path></svg></span> ';
				break;
			case 'plugin':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M13 18V20H19V22H13C11.8954 22 11 21.1046 11 20V18H8C5.79086 18 4 16.2091 4 14V7C4 6.44772 4.44772 6 5 6H8V2H10V6H14V2H16V6H19C19.5523 6 20 6.44772 20 7V14C20 16.2091 18.2091 18 16 18H13ZM8 16H16C17.1046 16 18 15.1046 18 14V11H6V14C6 15.1046 6.89543 16 8 16ZM18 8H6V9H18V8ZM12 14.5C11.4477 14.5 11 14.0523 11 13.5C11 12.9477 11.4477 12.5 12 12.5C12.5523 12.5 13 12.9477 13 13.5C13 14.0523 12.5523 14.5 12 14.5Z"></path></svg></span> ';
				break;
			case 'learn':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M8 4C8 5.10457 7.10457 6 6 6 4.89543 6 4 5.10457 4 4 4 2.89543 4.89543 2 6 2 7.10457 2 8 2.89543 8 4ZM5 16V22H3V10C3 8.34315 4.34315 7 6 7 6.82059 7 7.56423 7.32946 8.10585 7.86333L10.4803 10.1057 12.7931 7.79289 14.2073 9.20711 10.5201 12.8943 9 11.4587V22H7V16H5ZM10 5H19V14H10V16H14.3654L17.1889 22H19.3993L16.5758 16H20C20.5523 16 21 15.5523 21 15V4C21 3.44772 20.5523 3 20 3H10V5Z"></path></svg></span> ';
				break;
			case 'collection':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M13.0607 8.11097L14.4749 9.52518C17.2086 12.2589 17.2086 16.691 14.4749 19.4247L14.1214 19.7782C11.3877 22.5119 6.95555 22.5119 4.22188 19.7782C1.48821 17.0446 1.48821 12.6124 4.22188 9.87874L5.6361 11.293C3.68348 13.2456 3.68348 16.4114 5.6361 18.364C7.58872 20.3166 10.7545 20.3166 12.7072 18.364L13.0607 18.0105C15.0133 16.0578 15.0133 12.892 13.0607 10.9394L11.6465 9.52518L13.0607 8.11097ZM19.7782 14.1214L18.364 12.7072C20.3166 10.7545 20.3166 7.58872 18.364 5.6361C16.4114 3.68348 13.2456 3.68348 11.293 5.6361L10.9394 5.98965C8.98678 7.94227 8.98678 11.1081 10.9394 13.0607L12.3536 14.4749L10.9394 15.8891L9.52518 14.4749C6.79151 11.7413 6.79151 7.30911 9.52518 4.57544L9.87874 4.22188C12.6124 1.48821 17.0446 1.48821 19.7782 4.22188C22.5119 6.95555 22.5119 11.3877 19.7782 14.1214Z"></path></svg></span> ';
				break;
			case 'links':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M10 6V8H5V19H16V14H18V20C18 20.5523 17.5523 21 17 21H4C3.44772 21 3 20.5523 3 20V7C3 6.44772 3.44772 6 4 6H10ZM21 3V11H19L18.9999 6.413L11.2071 14.2071L9.79289 12.7929L17.5849 5H13V3H21Z"></path></svg></span> ';
				break;
			case 'community':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M17 6C16.4477 6 16 5.55228 16 5C16 4.44772 16.4477 4 17 4C17.5523 4 18 4.44772 18 5C18 5.55228 17.5523 6 17 6ZM17 8C18.6569 8 20 6.65685 20 5C20 3.34315 18.6569 2 17 2C15.3431 2 14 3.34315 14 5C14 6.65685 15.3431 8 17 8ZM7 3C4.79086 3 3 4.79086 3 7V9H5V7C5 5.89543 5.89543 5 7 5H10V3H7ZM17 21C19.2091 21 21 19.2091 21 17V15H19V17C19 18.1046 18.1046 19 17 19H14V21H17ZM8 13C8 12.4477 7.55228 12 7 12C6.44772 12 6 12.4477 6 13C6 13.5523 6.44772 14 7 14C7.55228 14 8 13.5523 8 13ZM10 13C10 14.6569 8.65685 16 7 16C5.34315 16 4 14.6569 4 13C4 11.3431 5.34315 10 7 10C8.65685 10 10 11.3431 10 13ZM17 11C15.8954 11 15 11.8954 15 13H13C13 10.7909 14.7909 9 17 9C19.2091 9 21 10.7909 21 13H19C19 11.8954 18.1046 11 17 11ZM5 21C5 19.8954 5.89543 19 7 19C8.10457 19 9 19.8954 9 21H11C11 18.7909 9.20914 17 7 17C4.79086 17 3 18.7909 3 21H5Z"></path></svg></span> ';
				break;
			case 'about':
				$svg = '<span class="icon-svg"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M17.841 15.659L18.017 15.836L18.1945 15.659C19.0732 14.7803 20.4978 14.7803 21.3765 15.659C22.2552 16.5377 22.2552 17.9623 21.3765 18.841L18.0178 22.1997L14.659 18.841C13.7803 17.9623 13.7803 16.5377 14.659 15.659C15.5377 14.7803 16.9623 14.7803 17.841 15.659ZM12 14V16C8.68629 16 6 18.6863 6 22H4C4 17.6651 7.44784 14.1355 11.7508 14.0038L12 14ZM12 1C15.315 1 18 3.685 18 7C18 10.2397 15.4357 12.8776 12.225 12.9959L12 13C8.685 13 6 10.315 6 7C6 3.76034 8.56434 1.12237 11.775 1.00414L12 1ZM12 3C9.78957 3 8 4.78957 8 7C8 9.21043 9.78957 11 12 11C14.2104 11 16 9.21043 16 7C16 4.78957 14.2104 3 12 3Z"></path></svg></span> ';
				break;
			default:
				$svg = '';
		}  // end switch
		
		return $svg;
	}
	
	/**
	 * Get node data for Title & Meta; set "link" icon for external links.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $url     URL of the admin page or external link.
	 * @param  string $label   Label for title of the node.
	 * @return array  $output  Array containing meta & title parameters of node.
	 */
	private function get_node_data( $url, $label = '' ) {
		
		$output = [];
		$output[ 'meta' ]  = ( strpos( $url, '.php' ) !== FALSE ) ? [] : [ 'class' => 'has-icon', 'target' => '_blank', 'rel' => 'nofollow noopener noreferrer' ];
		$output[ 'title' ] = ( strpos( $url, '.php' ) !== FALSE ) ? esc_html( $label ) : $this->get_icon( 'links' ) . esc_html( $label );
		
		return $output;
	}
	
	/**
	 * Number of templates/pages to query for. Can be tweaked via constant.
	 *
	 * @since 1.0.0
	 *
	 * @return int  Number of templates.
	 */
	private function number_of_templates() {
			
		$number_of_templates = defined( 'BXQN_NUMBER_TEMPLATES' ) ? (int) BXQN_NUMBER_TEMPLATES : self::NUMBER_OF_TEMPLATES;
		
		return $number_of_templates;
	}
	
	/**
	 * Get items of a Bricks template type. (Helper function)
	 *
	 * @since 1.0.0
	 *
	 * @uses get_posts()
	 *
	 * @param string $post_type  Slug of post type to query for.
	 * @return obj               Object of WP_Query with the supplied arguments
	 *                           via get_posts().
	 */
	private function get_bricks_template_type( $post_type ) {
		
		/** only Bricks-edited pages have the key: '_bricks_editor_mode' */
		$pages_meta_query = ( 'page' === $post_type ) ? [ 'key' => '_bricks_editor_mode', 'value' => 'bricks' ] : [];
		
		$args = [
			'post_type'      => sanitize_key( $post_type ),
			'posts_per_page' => absint( $this->number_of_templates() ),
			//'post_status'    => 'publish',
			'orderby'        => 'modified',
			'order'          => 'DESC',
			'meta_query'     => [ $pages_meta_query ],  // optional
		];
		
		apply_filters( 'ddw-bxqn/get-template-type', $args, $post_type );
		
		return get_posts( $args );
	}
	
	/**
	 * Get items of a Bricks template type. (Helper function)
	 *
	 * @since 1.0.0
	 *
	 * @uses get_terms()
	 *
	 * @param string $post_type  Slug of post type to query for.
	 * @return obj               WP_Terms_Query object with the supplied
	 *                           arguments via get_terms().
	 */
	private function get_bricks_template_terms( $tax ) {
		
		$args = [
			'taxonomy' => sanitize_key( $tax ),
		];
		
		apply_filters( 'ddw-bxqn/get-template-terms', $args, $tax );
		
		return get_terms( $args );
	}
	
	/**
	 * Adds the main Bricks menu and its submenus to the Admin Bar.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Admin_Bar $wp_admin_bar  The WP_Admin_Bar instance.
	 */
	public function add_admin_bar_menu( $wp_admin_bar ) {
		
		$enabled_users = defined( 'BXQN_ENABLED_USERS' ) ? (array) BXQN_ENABLED_USERS : [];
		
		/** Optional: let only defined user IDs access the plugin */
		if ( defined( 'BXQN_ENABLED_USERS' ) && ! in_array( get_current_user_id(), $enabled_users ) ) return;
		
		/** Don't do anything if Bricks Builder plugin is NOT active */
		if ( ! defined( 'BRICKS_VERSION' ) ) return;
		
		$bxqn_permission = ( defined( 'BXQN_VIEW_CAPABILITY' ) ) ? BXQN_VIEW_CAPABILITY : 'activate_plugins';
		
		if ( ! current_user_can( sanitize_key( $bxqn_permission ) ) ) return;
		
		$shown_icon = $this->get_icon( 'bricks-b' );
		
		if ( defined( 'BXQN_ICON' ) && 'yellow' === sanitize_key( BXQN_ICON ) ) {
			$shown_icon = $this->get_icon( 'bricks-yellow' );
		} elseif ( defined( 'BXQN_ICON' ) && ( 'brick' === sanitize_key( BXQN_ICON ) || 'bricks' === sanitize_key( BXQN_ICON ) ) ) {
			$shown_icon = $this->get_icon( 'bricks-brick' );
		}
		
		$bxqn_name = defined( 'BXQN_NAME_IN_ADMINBAR' ) ? esc_html( BXQN_NAME_IN_ADMINBAR ) : esc_html__( 'Bricks', 'bricks-quicknav' );
		
		$title_html = $shown_icon . '<span class="ab-label">' . $bxqn_name . '</span>';
		
		/** Add the parent menu item with an icon (main node) */
		$wp_admin_bar->add_node( [
			'id'    => 'ddw-bricks-quicknav',
			'title' => $title_html,
			'href'  => esc_url( admin_url( 'admin.php?page=bricks' ) ),
			'meta'  => [ 'class' => 'has-icon' ],
		] );

		/** Add submenus (all group nodes!) */
		$this->add_templates_group( $wp_admin_bar );
		$this->add_customcode_group( $wp_admin_bar );
		$this->add_framework_group( $wp_admin_bar );
		$this->add_settings_group( $wp_admin_bar );
		$this->add_plugin_support_group( $wp_admin_bar );
		$this->add_footer_group( $wp_admin_bar );
	}

	/**
	 * Generate "Add New" node within a group for given post type.
	 *
	 * @since 1.0.0
	 *
	 * @param string $post_type     Post Type ID to add the node for.
	 * @param string $parent_node   ID of parent node to hook into.
	 * @param object $wp_admin_bar  WP_Admin_Bar object.
	 */
	private function add_addnew_node( $post_type, $parent_node, $wp_admin_bar ) {
		
		$post_type = sanitize_key( $post_type );
		
		$wp_admin_bar->add_group( [
			'id'     => 'bxqn-group-' . $post_type . '-new',
			'parent' => sanitize_key( $parent_node ),
		] );
		
		$wp_admin_bar->add_node( [
			'id'     => 'bxqn-pages-addnew-' . $post_type,
			'title'  => $this->get_icon( 'add' ) . esc_html__( 'Add New', 'bricks-quicknav' ),
			'href'   => esc_url( admin_url( 'post-new.php?post_type=' . $post_type ) ),
			'parent' => 'bxqn-group-' . $post_type . '-new',
			'meta'   => [ 'class' => 'has-icon' ],
		] );
	}
	
	/**
	 * Add group node for BD-edited Pages and all BD Template types.
	 *
	 * @since 1.0.0
	 */
	private function add_templates_group( $wp_admin_bar ) {
		$wp_admin_bar->add_group( [
			'id'     => 'bxqn-group-templates',
			'parent' => 'ddw-bricks-quicknav',
		] );
		
		$this->add_pages_submenu( $wp_admin_bar );
		$this->add_templates_submenu( $wp_admin_bar );
		$this->add_template_types_submenu( $wp_admin_bar );
		$this->add_template_tax_submenu( $wp_admin_bar );
	}
	
	/**
	 * Add Bricks-edited Pages submenu (just regular WordPress Pages).
	 *
	 * @since 1.0.0
	 */
	private function add_pages_submenu( $wp_admin_bar ) {
		$wp_admin_bar->add_node( [
			'id'     => 'bxqn-pages',
			'title'  => esc_html__( 'Pages (Bricks)', 'bricks-quicknav' ),
			'href'   => esc_url( admin_url( 'edit.php?post_type=page' ) ),
			'parent' => 'bxqn-group-templates',
		] );

		$this->add_addnew_node( 'page', 'bxqn-pages', $wp_admin_bar );
		
		$wp_admin_bar->add_group( [ 'id' => 'bxqn-group-pages-hook', 'parent' => 'bxqn-pages' ] );
		
		$brx_pages = $this->get_bricks_template_type( 'page' );	
		
		if ( $brx_pages ) {
			foreach ( $brx_pages as $brx_page ) {
				$edit_link = get_permalink( intval( $brx_page->ID ) ) . '?bricks=run';
		
				$wp_admin_bar->add_node( [
					'id'     => 'bxqn-page-' . intval( $brx_page->ID ),
					'title'  => $this->get_icon( 'page' ) . esc_html( $brx_page->post_title ),
					'href'   => esc_url( $edit_link ),
					'parent' => 'bxqn-pages',
					'meta'   => [ 'class' => 'has-icon' ],
				] );
			}  // end foreach
		}  // end if
		
		if ( class_exists( 'DDW_Builder_List_Pages' ) ) {
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-blp-filter-bricks-pages',
				'title'  => $this->get_icon( 'filter' ) . esc_html__( 'Filter Bricks Pages', 'bricks-quicknav' ),
				'href'   => esc_url( admin_url( 'edit.php?post_type=page&builder=bricks' ) ),
				'parent' => 'bxqn-group-pages-hook',
				'meta'   => [ 'class' => 'has-icon' ],
			] );
		}  // end if
	}
	
	/**
	 * Add Bricks Templates submenu.
	 *
	 * @since 1.0.0
	 */
	private function add_templates_submenu( $wp_admin_bar ) {
		$wp_admin_bar->add_node( [
			'id'     => 'bxqn-templates',
			'title'  => esc_html__( 'Templates', 'bricks-quicknav' ),
			'href'   => esc_url( admin_url( 'edit.php?post_type=bricks_template' ) ),
			'parent' => 'bxqn-group-templates',
		] );

		$this->add_addnew_node( 'bricks_template', 'bxqn-templates', $wp_admin_bar );

		$templates = $this->get_bricks_template_type( 'bricks_template' );
		
		if ( $templates ) {
			foreach ( $templates as $template ) {
				$edit_link = get_permalink( intval( $template->ID ) ) . '?bricks=run';
		
				$wp_admin_bar->add_node( [
					'id'     => 'bxqn-template-' . intval( $template->ID ),
					'title'  => $this->get_icon( 'template' ) . esc_html( $template->post_title ),
					'href'   => esc_url( $edit_link ),
					'parent' => 'bxqn-templates',
					'meta'   => [ 'class' => 'has-icon' ],
				] );
			}  // end foreach
		}  // end if
		
		$wp_admin_bar->add_node( [
			'id'     => 'bxqn-wpnewcontent-new-template',
			'title'  => esc_html__( 'Template (Bricks)', 'bricks-quicknav' ),
			'href'   => esc_url( admin_url( 'post-new.php?post_type=bricks_template' ) ),
			'parent' => 'new-content',
		] );
	}

	/**
	 * Add Bricks Template Types submenu.
	 *
	 * @since 1.0.0
	 */
	private function add_template_types_submenu( $wp_admin_bar ) {
		$wp_admin_bar->add_node( [
			'id'     => 'bxqn-types',
			'title'  => esc_html__( 'All Template Types', 'bricks-quicknav' ),
			'href'   => esc_url( admin_url( 'edit.php?post_type=bricks_template' ) ),
			'parent' => 'bxqn-group-templates',
		] );

		$template_types = $this->get_bricks_template_type( 'bricks_template' );
		$bricks_types   = ( class_exists( '\Bricks\Setup') ) ? \Bricks\Setup::$control_options[ 'templateTypes' ] : [];
		
		if ( $template_types ) {
			foreach ( $bricks_types as $type => $label ) {
				$args = [
					'post_type'      => 'bricks_template',
					'posts_per_page' => absint( $this->number_of_templates() ),
					'orderby'        => 'modified',
					'order'          => 'DESC',
					'meta_query'     => [ [ 'key' => '_bricks_template_type', 'value' => $type ] ],
				];
			
				$specific_templates = get_posts( $args );
				
				$wp_admin_bar->add_node( [
					'id'     => 'bxqn-type-' . $type,
					'title'  => esc_html( $label ),
					'href'   => esc_url( admin_url( 'edit.php?post_type=bricks_template&template_type=' . $type ) ),
					'parent' => 'bxqn-types',
				] );
			
				foreach ( $specific_templates as $specific_template ) {
					$edit_link = get_permalink( intval( $specific_template->ID ) ) . '?bricks=run';
			
					$wp_admin_bar->add_node( [
						'id'     => 'bxqn-type-' . $type . '-' .intval( $specific_template->ID ),
						'title'  => $this->get_icon( 'template' ) . esc_html( $specific_template->post_title ),
						'href'   => esc_url( $edit_link ),
						'parent' => 'bxqn-type-' . $type,
						'meta'   => [ 'class' => 'has-icon' ],
					] );
				}  // end foreach
			}  // end foreach
		}  // end if
	}
	
	/**
	 * Add Bricks Template Taxonomies submenu (Tags & Bundles).
	 *
	 * @since 1.0.0
	 */
	private function add_template_tax_submenu( $wp_admin_bar ) {
		
		/** Bundles (taxonomy: 'template_bundle' ) */
		$wp_admin_bar->add_node( [
			'id'     => 'bxqn-bundles',
			'title'  => esc_html__( 'Template Bundles', 'bricks-quicknav' ),
			'href'   => esc_url( admin_url( 'edit-tags.php?taxonomy=template_bundle&post_type=bricks_template' ) ),
			'parent' => 'bxqn-group-templates',
		] );
		
		$bundles = $this->get_bricks_template_terms( 'template_bundle' );
		
		if ( $bundles ) {
			foreach ( $bundles as $bundle ) {
				$wp_admin_bar->add_node( [
					'id'     => 'bxqn-bundle-' . intval( $bundle->term_id ),
					'title'  => $this->get_icon( 'bundle' ) . esc_html( $bundle->name ),
					'href'   => esc_url( admin_url( 'edit.php?post_type=bricks_template&template_bundle=' . $bundle->slug ) ),
					'parent' => 'bxqn-bundles',
					'meta'   => [ 'class' => 'has-icon' ],
				] );
			}  // end foreach
		}  // end if
		
		/** Tags (taxonomy: 'template_tag' ) */
		$wp_admin_bar->add_node( [
			'id'     => 'bxqn-tags',
			'title'  => esc_html__( 'Template Tags', 'bricks-quicknav' ),
			'href'   => esc_url( admin_url( 'edit-tags.php?taxonomy=template_tag&post_type=bricks_template' ) ),
			'parent' => 'bxqn-group-templates',
		] );
		
		$tags = $this->get_bricks_template_terms( 'template_tag' );
		
		if ( $tags ) {
			foreach ( $tags as $tag ) {
				$wp_admin_bar->add_node( [
					'id'     => 'bxqn-tag-' . intval( $tag->term_id ),
					'title'  => $this->get_icon( 'tag' ) . esc_html( $tag->name ),
					'href'   => esc_url( admin_url( 'edit.php?post_type=bricks_template&template_tag=' . $tag->slug ) ),
					'parent' => 'bxqn-tags',
					'meta'   => [ 'class' => 'has-icon' ],
				] );
			}  // end foreach
		}  // end if
	}

	/**
	 * Filterable array of supported code snippets manager plugins.
	 *
	 * @since 1.0.0
	 *
	 * @return array $plugins  Array of supported plugins.
	 */
	private function get_snippets_plugins() {
		
		$plugins = [
			/** Plugin: Code Snippets (free/ Premium) */
			'code-snippets' => [
				'is-active'  => defined( 'CODE_SNIPPETS_VERSION' ),
				'capability' => 'manage_options',
				'admin-slug' => 'admin.php?page=snippets',
				'add-new'    => 'admin.php?page=add-snippet',
				'label'      => 'Code Snippets',
			],
			
			/** Plugin: Advanced Scripts (Premium) */
			'advanced-scripts' => [
				'is-active'  => defined( 'EPXADVSC_VER' ),
				'capability' => 'manage_options',
				'admin-slug' => 'tools.php?page=advanced-scripts',
				'add-new'    => 'tools.php?page=advanced-scripts&parent=0&edit=0',
				'label'      => 'Advanced Scripts',
			],
			
			/** Plugin: Scripts Organizer (Premium) */
			'scripts-organizer' => [
				'is-active'  => defined( 'SCORG_PLUGINVERSION' ),
				'capability' => 'manage_options',
				'admin-slug' => 'edit.php?post_type=scorg',
				'add-new'    => 'post-new.php?post_type=scorg',
				'label'      => 'Scripts Organizer',
			],
			
			/** Plugin: WPCodeBox (Premium) */
			'wpcodebox' => [
				'is-active'  => defined( 'WPCODEBOX_VERSION' ),
				'capability' => 'manage_options',
				'admin-slug' => 'admin.php?page=wpcb_menu_page_php',
				'add-new'    => '',
				'label'      => 'WPCodeBox',
			],
			
			/** Plugin: FluentSnippets (free - wordpress.org) */
			'fluentsnippets' => [
				'is-active'  => defined( 'FLUENT_SNIPPETS_PLUGIN_VERSION' ),
				'capability' => 'manage_options',
				'admin-slug' => 'admin.php?page=fluent-snippets',
				'add-new'    => '',
				'label'      => 'FluentSnippets',
			],
			
			/** Plugin: WPCode Lite (free) */
			'wpcode-lite' => [
				'is-active'  => class_exists( 'WPCode_Admin_Bar_Info_Lite' ),
				'capability' => 'manage_options',
				'admin-slug' => 'admin.php?page=wpcode-snippet-manager',
				'add-new'    => 'admin.php?page=wpcode-snippet-manager&custom=1',
				'label'      => 'WPCode Lite',
			],
		];
		
		$plugins = apply_filters( 'ddw-bxqn/snippet-plugins', $plugins );
		
		$count_active = 0;
		
		if ( $plugins ) {
			foreach ( $plugins as $plugin ) {
				if ( ! $plugin[ 'is-active' ] ) continue;				
				$count_active++;
			}
		}
		
		self::$has_snippets_plugin = $count_active;
		
		/** Return the array, filterable */
		return $plugins;
	}
	
	/**
	 * Whether a snippets manager plugin is active or not.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function has_snippets_plugin(): bool {
		if ( self::$has_snippets_plugin >= 1 ) return TRUE;
		return FALSE;
	}
	
	/**
	 * Whether "SNN BRX Child Theme" by Sinan Isler is active or not.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function is_snn_brx_child(): bool {
		if ( 'SNN BRX - Advanced Bricks Builder Child Theme' === wp_get_theme( get_stylesheet() )->get( 'Name' ) ) return TRUE;
		elseif ( 'snn-brx-child-theme' === get_stylesheet_directory() ) return TRUE;
		return FALSE;
	}
	
	/**
	 * Add group node for Custom Code.
	 *
	 * @since 1.0.0
	 */
	private function add_customcode_group( $wp_admin_bar ) {
		$wp_admin_bar->add_group( [
			'id'     => 'bxqn-group-customcode',
			'parent' => 'ddw-bricks-quicknav',
		] );
		
		$this->add_customcode_submenu( $wp_admin_bar );
	}
	
	/**
	 * Add Custom Code submenu.
	 *
	 * @since 1.0.0
	 */
	private function add_customcode_submenu( $wp_admin_bar ) {
		
		/** Prepare ID & hook place */
		$id_custom_code_main  = 'bxqn-bricks-customcode';
		$id_hook_code_plugins = $id_custom_code_main;	// 'bxqn-hook-code-plugins';
		
		if ( is_child_theme() && $this->has_snippets_plugin() ) {
			$id_custom_code_main  = 'bxqn-child-theme';
			$id_hook_code_plugins = 'bxqn-hook-code-plugins';
		} elseif ( is_child_theme() && ! $this->has_snippets_plugin() ) {
			$id_custom_code_main = 'bxqn-child-theme';
		} elseif ( ! is_child_theme() && $this->has_snippets_plugin() ) {
			$id_custom_code_main = 'bxqn-snippet-manager';
		}
		
		$file_name   = 'functions.php';
		$file_handle = 'theme-editor.php?file=' . $file_name . '&amp;theme=' . get_stylesheet();
		
		$theme_url = is_multisite() ? esc_url( network_admin_url( $file_handle ) ) : esc_url( admin_url( $file_handle ) );
		if ( $this->is_snn_brx_child() ) $theme_url = esc_url( admin_url( 'admin.php?page=snn-custom-codes-snippets' ) );

		/** If a Bricks child theme is active */		
		if ( is_child_theme() &&  current_user_can( 'edit_themes' ) ) {
			if ( ! ( defined( 'DISALLOW_FILE_EDIT' ) && DISALLOW_FILE_EDIT )
				|| ! ( defined( 'DISALLOW_FILE_MODS' ) && DISALLOW_FILE_MODS )
			) {	
				$wp_admin_bar->add_node( [
					'id'     => $id_custom_code_main,
					'title'  => $this->get_icon( 'code' ) . esc_html__( 'Edit Child Theme', 'bricks-quicknav' ),
					'href'   => $theme_url,
					'parent' => 'bxqn-group-customcode',
					'meta'   => [ 'class' => 'has-icon' ],
				] );
			
				if ( ! $this->is_snn_brx_child() ) {
					$wp_admin_bar->add_node( [
						'id'     => 'bxqn-functions-php',
						'title'  => esc_html__( 'functions.php Code', 'bricks-quicknav' ),
						'href'   => $theme_url,
						'parent' => $id_custom_code_main,
					] );
				} else {
					$wp_admin_bar->add_group( [
						'id'     => 'bxqn-group-hook-snnbrx',
						'parent' => 'bxqn-child-theme',
					] );
				}  // end if SNN Child Theme
			}  // end if FILE EDITS
		}  // end if Child Theme general
		
		/** If a supported snippets manager plugin is active */
		$snippet_plugins = $this->get_snippets_plugins();

		if ( $snippet_plugins ) {
			foreach ( $snippet_plugins as $snippet_plugin ) {
				if ( $snippet_plugin[ 'is-active' ] && current_user_can( $snippet_plugin[ 'capability' ] ) ) {
					$wp_admin_bar->add_node( [
						'id'     => $id_hook_code_plugins,
						'title'  => $this->get_icon( 'snippets' ) . $snippet_plugin[ 'label' ],
						'href'   => esc_url( admin_url( $snippet_plugin[ 'admin-slug' ] ) ),
						'parent' => 'bxqn-group-customcode',
						'meta'   => [ 'class' => 'has-icon' ],
					] );
					
					$wp_admin_bar->add_node( [
						'id'     => 'bxqn-snippets-all',
						'title'  => esc_html__( 'All Snippets', 'bricks-quicknav' ),
						'href'   => esc_url( admin_url( $snippet_plugin[ 'admin-slug' ] ) ),
						'parent' => $id_hook_code_plugins,
					] );
					
					if ( ! empty( $snippet_plugin[ 'add-new' ] ) ) {
						$wp_admin_bar->add_node( [
							'id'     => 'bxqn-snippets-new',
							'title'  => esc_html__( 'New Snippet', 'bricks-quicknav' ),
							'href'   => esc_url( admin_url( $snippet_plugin[ 'add-new' ] ) ),
							'parent' => $id_hook_code_plugins,
						] );
					}  // end if
				}  // end if
			}  // end foreach
		}  // end if
		
		/** Bricks: Custom Code specific stuff */
		$wp_admin_bar->add_group( [
			'id'     => 'bxqn-group-bricks-coding',
			'parent' => $id_custom_code_main,
			'meta'   => [ 'class' => 'ab-sub-secondary' ],
		] );
		
		$wp_admin_bar->add_node( [
			'id'     => 'bxqn-bricks-custom-code',
			'title'  => esc_html__( 'Bricks: Custom Code', 'bricks-quicknav' ),
			'href'   => esc_url( admin_url( 'admin.php?page=bricks-settings#tab-custom-code' ) ),
			'parent' => 'bxqn-group-bricks-coding',
		] );
		
		$wp_admin_bar->add_node( [
			'id'     => 'bxqn-bricks-code-review',
			'title'  => esc_html__( 'Code Review', 'bricks-quicknav' ),
			'href'   => esc_url( admin_url( 'admin.php?page=bricks-settings&code-review=all#tab-custom-code' ) ),
			'parent' => 'bxqn-group-bricks-coding',
		] );
		
		$wp_admin_bar->add_node( [
			'id'     => 'bxqn-bricks-code-signatures',
			'title'  => esc_html__( 'Code Signatures', 'bricks-quicknav' ),
			'href'   => esc_url( admin_url( 'admin.php?page=bricks-settings#tab-custom-code' ) ),
			'parent' => 'bxqn-group-bricks-coding',
		] );
	}
	
	/**
	 * Add group node for Bricks-specific Frameworks.
	 *
	 * @since 1.0.0
	 */
	private function add_framework_group( $wp_admin_bar ) {
		$wp_admin_bar->add_group( [
			'id'     => 'bxqn-group-frameworks',
			'parent' => 'ddw-bricks-quicknav',
		] );
		
		$this->add_frameworks_submenu( $wp_admin_bar );
	}
	
	/**
	 * Add Frameworks submenu.
	 *   NOTE: This is optional only for any supported Framework and its
	 *         directly related resources.
	 *
	 * @since 1.0.0
	 */
	private function add_frameworks_submenu( $wp_admin_bar ) {
		
		/** Core Framework (free & Premium) */
		if ( defined( 'CORE_FRAMEWORK_VERSION' ) ) {
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-active-framework',
				'title'  => $this->get_icon( 'cf' ) . esc_html__( 'Core Framework', 'bricks-quicknav' ),
				'href'   => esc_url( admin_url( 'admin.php?page=core-framework' ) ),
				'parent' => 'bxqn-group-frameworks',
				'meta'   => [ 'class' => 'has-icon' ],
			] );
			
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-active-framework-settings',
				'title'  => esc_html__( 'Settings', 'bricks-quicknav' ),
				'href'   => esc_url( admin_url( 'admin.php?page=core-framework' ) ),
				'parent' => 'bxqn-active-framework',
			] );
			
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-active-framework-webapp',
				'title'  => $this->get_icon( 'links' ) . esc_html__( 'Web App', 'bricks-quicknav' ),
				'href'   => 'https://coreframework.com/app',
				'parent' => 'bxqn-active-framework',	//'bxqn-group-active-framework-resources',
				'meta'   => [ 'class' => 'has-icon', 'target' => '_blank', 'rel' => 'nofollow noopener noreferrer' ],
			] );
			
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-active-framework-docs',
				'title'  => $this->get_icon( 'links' ) . esc_html__( 'Documentation', 'bricks-quicknav' ),
				'href'   => 'https://docs.coreframework.com/builder-integrations/bricks-builder',
				'parent' => 'bxqn-active-framework',	//'bxqn-group-active-framework-resources',
				'meta'   => [ 'class' => 'has-icon', 'target' => '_blank', 'rel' => 'nofollow noopener noreferrer' ],
			] );
		}
		
		/** Automatic.CSS Framework (Premium) */
		if ( defined( 'ACSS_PLUGIN_FILE' ) ) {
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-active-framework',
				'title'  => $this->get_icon( 'acss' ) . 'Automatic.CSS',
				'href'   => esc_url( admin_url( 'admin.php?page=automatic-css' ) ),
				'parent' => 'bxqn-group-frameworks',
				'meta'   => [ 'class' => 'has-icon' ],
			] );
			
			$automaticcss_links = [
				'welcome'       => [ 'title' => __( 'Getting Started', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=automatic-css&tab=welcome' ) ) ],
				'import-export' => [ 'title' => __( 'Import/ Export', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=automatic-css&tab=import-export' ) ) ],
				'license'       => [ 'title' => __( 'License', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=automatic-css&tab=license' ) ) ],
			];
			
			if ( class_exists( '\Yabe\AcssPurger\Plugin' ) ) $automaticcss_links[ 'acss-purger' ] = [ 'title' => __( 'ACSS Purger', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=acss_purger' ) ) ];
			
			$automaticcss_links[ 'cheat-sheet' ] = [ 'title' => __( 'Cheat Sheet', 'bricks-quicknav' ), 'url' => 'https://automaticcss.com/cheat-sheet/' ];
			$automaticcss_links[ 'docs' ]        = [ 'title' => __( 'Documentation', 'bricks-quicknav' ), 'url' => 'https://automaticcss.com/docs/' ];
			$automaticcss_links[ 'circle' ]      = [ 'title' => __( 'Circle Community', 'bricks-quicknav' ), 'url' => 'https://community.automaticcss.com/c/bricks/' ];
			
			foreach ( $automaticcss_links as $acss_id => $acss_info ) {
				$wp_admin_bar->add_node( [
					'id'     => 'bxqn-active-framework-' . $acss_id,
					'title'  => $this->get_node_data( $acss_info[ 'url' ], $acss_info[ 'title' ] )[ 'title' ],
					'href'   => $acss_info[ 'url' ],
					'parent' => 'bxqn-active-framework',
					'meta'   => $this->get_node_data( $acss_info[ 'url' ] )[ 'meta' ],
				] );
			}  // end foreach
		}
		
		/** Frames (via Automatic.CSS) (Premium) */
		if ( defined( 'FRAMES_PLUGIN_FILE' ) ) {
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-framework-addon',
				'title'  => $this->get_icon( 'frames' ) . 'Frames',
				'href'   => esc_url( admin_url( 'admin.php?page=frames&tab=welcome' ) ),
				'parent' => 'bxqn-group-frameworks',
				'meta'   => [ 'class' => 'has-icon' ],
			] );
			
			$frames_links = [
				'welcome'    => [ 'title' => __( 'Getting Started', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=frames&tab=welcome' ) ) ],
				'license'    => [ 'title' => __( 'License', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=frames&tab=license' ) ) ],
				'layouts'    => [ 'title' => __( 'Layouts', 'bricks-quicknav' ), 'url' => 'https://getframes.io/layouts/' ],
				'components' => [ 'title' => __( 'Components', 'bricks-quicknav' ), 'url' => 'https://getframes.io/components/' ],
				'docs'       => [ 'title' => __( 'Documentation', 'bricks-quicknav' ), 'url' => 'https://getframes.io/docs/' ],
				'circle'     => [ 'title' => __( 'Circle Community', 'bricks-quicknav' ), 'url' => 'https://community.automaticcss.com/c/general-discussion-frames/' ],
			];
			
			foreach ( $frames_links as $frs_id => $frs_info ) {
				$wp_admin_bar->add_node( [
					'id'     => 'bxqn-framework-addon-' . $frs_id,
					'title'  => $this->get_node_data( $frs_info[ 'url' ], $frs_info[ 'title' ] )[ 'title' ],
					'href'   => $frs_info[ 'url' ],
					'parent' => 'bxqn-framework-addon',
					'meta'   => $this->get_node_data( $frs_info[ 'url' ] )[ 'meta' ],
				] );
			}  // end foreach
		}  // end if
		
		/** WindPress (for Tailwind CSS in WordPress) (free & Premium) */
		if ( class_exists( '\WindPress\WindPress\Plugin' ) ) {
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-active-framework',
				'title'  => $this->get_icon( 'windpress' ) . 'WindPress',
				'href'   => esc_url( admin_url( 'admin.php?page=windpress' ) ),
				'parent' => 'bxqn-group-frameworks',
				'meta'   => [ 'class' => 'has-icon' ],
			] );
			
			$windpress_links = [
				'files'         => [ 'title' => __( 'Files', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=windpress#/files' ) ) ],
				'logs'          => [ 'title' => __( 'Logs', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=windpress#/logs' ) ) ],
				'settings'      => [ 'title' => __( 'General Settings', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=windpress#/general' ) ) ],
				'performance'   => [ 'title' => __( 'Performance', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=windpress#/performance' ) ) ],
				'integrations'  => [ 'title' => __( 'Integrations', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=windpress#/integrations' ) ) ],
				'tailwinddocs'  => [ 'title' => 'Tailwind CSS', 'url' => 'https://tailwindcss.com/docs' ],
				'docs'          => [ 'title' => __( 'Documentation', 'bricks-quicknav' ), 'url' => 'https://wind.press/docs?utm_source=bricks-quicknav&utm_medium=adminbar' ],
				'ghdiscussions' => [ 'title' => __( 'GitHub Discussions', 'bricks-quicknav' ), 'url' => 'https://github.com/wind-press/windpress/discussions' ],
			];
			
			foreach ( $windpress_links as $wndpr_id => $wndpr_info ) {
				$wp_admin_bar->add_node( [
					'id'     => 'bxqn-active-framework-' . $wndpr_id,
					'title'  => $this->get_node_data( $wndpr_info[ 'url' ], $wndpr_info[ 'title' ] )[ 'title' ],
					'href'   => $wndpr_info[ 'url' ],
					'parent' => 'bxqn-active-framework',
					'meta'   => $this->get_node_data( $wndpr_info[ 'url' ] )[ 'meta' ],
				] );
			}  // end foreach
		}  // end if
		
		/** Brixies.co (Premium) */
		$brixies = get_option( 'bricks_theme_styles' );
		if ( isset( $brixies[ 'brixies_theme_cf' ] ) || isset( $brixies[ 'brixies_theme_acss' ] ) || isset( $brixies[ 'general_styles' ] ) ) {
			$brixies_layouts    = isset( $brixies[ 'brixies_theme_cf' ] ) ? 'https://brixies.co/cf-library/' : 'https://brixies.co/library/';
			$brixies_components = isset( $brixies[ 'brixies_theme_cf' ] ) ? 'https://brixies.co/cf-components/' : 'https://brixies.co/acss-components/';
			
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-framework-addon',
				'title'  => $this->get_icon( 'frames' ) . 'Brixies',
				'href'   => $brixies_layouts,
				'parent' => 'bxqn-group-frameworks',
				'meta'   => [ 'class' => 'has-icon', 'target' => '_blank', 'rel' => 'nofollow noopener noreferrer' ],
			] );
			
			$brixies_links = [
				'layouts'    => [ 'title' => __( 'Get Layouts', 'bricks-quicknav' ), 'url' => $brixies_layouts ],
				'components' => [ 'title' => __( 'Get Components', 'bricks-quicknav' ), 'url' => $brixies_components ],
				'resources'  => [ 'title' => __( 'Resources', 'bricks-quicknav' ), 'url' => 'https://brixies.co/getting-started/' ],
				'changelog'  => [ 'title' => __( 'Changelog', 'bricks-quicknav' ), 'url' => 'https://brixies.co/changelog/' ],
				'fbgroup'    => [ 'title' => __( 'Facebook Group', 'bricks-quicknav' ), 'url' => 'https://www.facebook.com/groups/brixies' ],
			];
			
			foreach ( $brixies_links as $brix_id => $brix_info ) {
				$wp_admin_bar->add_node( [
					'id'     => 'bxqn-framework-addon-' . $brix_id,
					'title'  => $this->get_node_data( $brix_info[ 'url' ], $brix_info[ 'title' ] )[ 'title' ],
					'href'   => $brix_info[ 'url' ],
					'parent' => 'bxqn-framework-addon',
					'meta'   => $this->get_node_data( $brix_info[ 'url' ] )[ 'meta' ],
				] );
			}  // end foreach
		}  // end if
	}
	
	/**
	 * Add group node for actions & settings.
	 *
	 * @since 1.0.0
	 */
	private function add_settings_group( $wp_admin_bar ) {
		$wp_admin_bar->add_group( [
			'id'     => 'bxqn-group-settings',
			'parent' => 'ddw-bricks-quicknav',
		] );
		
		$this->add_actions_submenu( $wp_admin_bar );
		$this->add_settings_submenu( $wp_admin_bar );
	}
	
	/**
	 * Add actions submenu.
	 *   NOTE: This for any stuff that is not page/ template/ settings related.
	 *
	 * @since 1.0.0
	 */
	private function add_actions_submenu( $wp_admin_bar ) {
		
		$mode = class_exists( '\Bricks\Maintenance' ) ? \Bricks\Maintenance::get_mode() : FALSE;		
		
		$mode_setting = $mode ? get_option( 'bricks_global_settings' )[ 'maintenanceMode' ] : 'not-set';
		$mode_label = ( 'not-set' !== $mode_setting )
						? ( ( 'comingSoon' === $mode_setting ) ? __( 'Coming Soon Mode', 'bricks-quicknav' ) : __( 'Maintenance Mode', 'bricks-quicknav' ) )
						: '';
		
		$template = class_exists( '\Bricks\Database' ) ? \Bricks\Database::get_setting( 'maintenanceTemplate' ) : '';
		$template = ( '' !== $template ) ? $template : '';
		
		/** Coming Soon/ Maintenance Mode */
		if ( $mode && ! $this->is_compact_mode() ) {
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-' . strtolower( $mode_setting ) . '-mode',
				'title'  => $this->get_icon( 'warning' ) . esc_html( $mode_label ),
				'href'   => esc_url( admin_url( 'admin.php?page=bricks-settings#tab-maintenance' ) ),
				'parent' => 'bxqn-group-settings',
				'meta'   => [ 'class' => 'has-icon' ],
			] );
			
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-' . strtolower( $mode_setting ) . '-tweak',
				'title'  => esc_html__( 'Tweak Mode', 'bricks-quicknav' ),
				'href'   => esc_url( admin_url( 'admin.php?page=bricks-settings#tab-maintenance' ) ),
				'parent' => 'bxqn-' . strtolower( $mode_setting ) . '-mode',
			] );
			
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-' . strtolower( $mode_setting ) . '-template',
				'title'  => esc_html__( 'Edit Template', 'bricks-quicknav' ),
				'href'   => esc_url( get_permalink( $template ) . '?bricks=run' ),
				'parent' => 'bxqn-' . strtolower( $mode_setting ) . '-mode',
			] );
		}
		
		/** Bricks Form Submissions */
		if ( ! $this->is_compact_mode() ) {
			$bricks_submissions = get_option( 'bricks_global_settings' ) ? get_option( 'bricks_global_settings', [] ) : FALSE;
			if ( isset( $bricks_submissions[ 'saveFormSubmissions' ] ) ) {
				$wp_admin_bar->add_node( [
					'id'     => 'bxqn-formsubmissions',
					'title'  => $this->get_icon( 'inbox' ) . esc_html__( 'Form Submissions', 'bricks-quicknav' ),
					'href'   => esc_url( admin_url( 'admin.php?page=bricks-form-submissions' ) ),
					'parent' => 'bxqn-group-settings',
					'meta'   => [ 'class' => 'has-icon' ],                                                                                                                                         
				] );
			}  // end if Bricks Form Submissions
		}  // end if Compact Mode
			
		/** Bricks Custom Fonts */
		if ( ! $this->is_compact_mode() && ! $this->has_snippet_ma_customfonts() ) {
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-custom-fonts',
				'title'  => $this->get_icon( 'font' ) . esc_html__( 'Custom Fonts', 'bricks-quicknav' ),
				'href'   => esc_url( admin_url( 'edit.php?post_type=bricks_fonts' ) ),
				'parent' => 'bxqn-group-settings',
				'meta'   => [ 'class' => 'has-icon' ],                                                                                                                                         
			] );
			
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-all-custom-fonts',
				'title'  => esc_html__( 'All Fonts', 'bricks-quicknav' ),
				'href'   => esc_url( admin_url( 'post-new.php?post_type=bricks_fonts' ) ),
				'parent' => 'bxqn-custom-fonts',
			] );
			
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-new-custom-font',
				'title'  => esc_html__( 'New Font', 'bricks-quicknav' ),
				'href'   => esc_url( admin_url( 'post-new.php?post_type=bricks_fonts' ) ),
				'parent' => 'bxqn-custom-fonts',
			] );
			
			$wp_admin_bar->add_node( [ 'id' => 'bxqn-webfontloader', ] );
			
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-wpnewcontent-new-custom-font',
				'title'  => esc_html__( 'Custom Font (Bricks)', 'bricks-quicknav' ),
				'href'   => esc_url( admin_url( 'post-new.php?post_type=bricks_fonts' ) ),
				'parent' => 'new-content',
			] );
				
			$custom_fonts = $this->get_bricks_template_type( 'bricks_fonts' );
			
			if ( $custom_fonts ) {
				$wp_admin_bar->add_group( [
					'id'     => 'bxqn-group-customfonts',
					'parent' => 'bxqn-custom-fonts',
				] );
				
				foreach ( $custom_fonts as $custom_font ) {
					$wp_admin_bar->add_node( [
						'id'     => 'bxqn-custom-font-' . intval( $custom_font->ID ),
						'title'  => $this->get_icon( 'font' ) . esc_html( $custom_font->post_title ),
						'href'   => esc_url( get_edit_post_link( intval( $custom_font->ID ), 'work' ) ),
						'parent' => 'bxqn-group-customfonts',
						'meta'   => [ 'class' => 'has-icon' ],
					] );
				}  // end foreach
			}  // end if Bricks Custom Fonts (items)
		}  // end if Compact Mode
		
		/** for Snippet MA Custom Fonts */
		if ( $this->has_snippet_ma_customfonts() && ! $this->is_compact_mode() ) {
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-ma-customfonts',
				'title'  => $this->get_icon( 'font' ) . esc_html__( 'MA Custom Fonts', 'bricks-quicknav' ),
				'href'   => esc_url( admin_url( 'themes.php?page=ma-customfonts' ) ),
				'parent' => 'bxqn-group-settings',
				'meta'   => [ 'class' => 'has-icon' ],                                                                                                                                         
			] );
			
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-ma-customfonts-preview',
				'title'  => esc_html__( 'Fonts Preview', 'bricks-quicknav' ),
				'href'   => esc_url( admin_url( 'themes.php?page=ma-customfonts' ) ),
				'parent' => 'bxqn-ma-customfonts',                                                                                                                                      
			] );
			
			$wp_admin_bar->add_node( [ 'id' => 'bxqn-webfontloader', ] );
			
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-ma-customfonts-snippet',
				'title'  => $this->get_icon( 'links' ) . esc_html__( 'Snippet Documentation', 'bricks-quicknav' ),
				'href'   => 'https://www.altmann.de/en/blog-en/code-snippet-custom-fonts/',
				'parent' => 'bxqn-ma-customfonts',
				'meta'   => [ 'class' => 'has-icon', 'target' => '_blank', 'rel' => 'nofollow noopener noreferrer' ],
			] );
		}
		
		/** Optionally hooked: MA Web Font Loader */
		$wp_admin_bar->add_node( [
			'id'     => 'bxqn-webfontloader',
			'title'  => $this->get_icon( 'links' ) . esc_html__( 'Web Font Loader', 'bricks-quicknav' ),
			'href'   => 'https://webfontloader.altmann.de/',
			'parent' => $this->has_snippet_ma_customfonts() ? 'bxqn-ma-customfonts' : 'bxqn-custom-fonts',
			'meta'   => [ 'class' => 'has-icon', 'target' => '_blank', 'rel' => 'nofollow noopener noreferrer' ],
		] );
		
		/** Sidebars */
		if ( ! $this->is_compact_mode() ) {
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-sidebars',
				'title'  => $this->get_icon( 'sidebars' ) . esc_html__( 'Sidebars', 'bricks-quicknav' ),
				'href'   => esc_url( admin_url( 'admin.php?page=bricks-sidebars' ) ),
				'parent' => 'bxqn-group-settings',
				'meta'   => [ 'class' => 'has-icon' ],
			] );
		}  // end if Compact Mode
	}

	/**
	 * Add Bricks Settings submenu (with parent node).
	 *
	 * @since 1.0.0
	 */
	private function add_settings_submenu( $wp_admin_bar ) {
		
		$wp_admin_bar->add_node( [
			'id'     => 'bxqn-settings',
			'title'  => $this->get_icon( 'settings' ) . esc_html__( 'Settings', 'bricks-quicknav' ),
			'href'   => esc_url( admin_url( 'admin.php?page=bricks-settings' ) ),
			'parent' => 'bxqn-group-settings',
			'meta'   => [ 'class' => 'has-icon bxqn-settings-separator' ],
		] );
		
		if ( version_compare( BRICKS_VERSION, '2.0-alpha', '>=' ) ) {
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-elements-manager',
				'title'  => esc_html__( 'Elements Manager', 'bricks-quicknav' ),
				'href'   => esc_url( admin_url( 'admin.php?page=bricks-elements' ) ),
				'parent' => 'bxqn-settings',
			] );
		}  // end if

		$settings_submenus = [
			'general'        => __( 'General Settings', 'bricks-quicknav' ),
			'builder-access' => __( 'Builder Access', 'bricks-quicknav' ),
			'templates'      => __( 'Templates', 'bricks-quicknav' ),
			'builder'        => __( 'Builder', 'bricks-quicknav' ),
			'performance'    => __( 'Performance', 'bricks-quicknav' ),
			'maintenance'    => __( 'Maintenance', 'bricks-quicknav' ),
			'api-keys'       => __( 'API Keys', 'bricks-quicknav' ),
			'custom-code'    => __( 'Custom Code', 'bricks-quicknav' ),
		];
		
		/** Make settings array filterable */
		apply_filters( 'ddw-bxqn/settings-links', $settings_submenus );
		
		foreach ( $settings_submenus as $tab => $title ) {
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-settings-' . sanitize_key( $tab ),
				'title'  => esc_html( $title ),
				'href'   => esc_url( admin_url( 'admin.php?page=bricks-settings#tab-' . urlencode( $tab ) ) ),
				'parent' => 'bxqn-settings',
			] );
		}  // end foreach
		
		if ( current_user_can( 'activate_plugins' ) ) {
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-settings-license',
				'title'  => esc_html__( 'License', 'bricks-quicknav' ),
				'href'   => esc_url( admin_url( 'admin.php?page=bricks-license' ) ),
				'parent' => 'bxqn-settings',
			] );
		}  // end if
		
		/** Info items (not in 'Compact Mode') */
		if ( ! $this->is_compact_mode() ) {
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-systeminfo',
				'title'  => $this->get_icon( 'info' ) . esc_html__( 'System Information', 'bricks-quicknav' ),
				'href'   => esc_url( admin_url( 'admin.php?page=bricks-system-information' ) ),
				'parent' => 'bxqn-group-settings',
				'meta'   => [ 'class' => 'has-icon' ],
			] );
			
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-systeminfo-bricks',
				'title'  => esc_html__( 'by Bricks', 'bricks-quicknav' ),
				'href'   => esc_url( admin_url( 'admin.php?page=bricks-system-information' ) ),
				'parent' => 'bxqn-systeminfo',
			] );
			
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-systeminfo-sitehealth',
				'title'  => esc_html__( 'by Site Health', 'bricks-quicknav' ),
				'href'   => esc_url( admin_url( 'site-health.php?tab=debug' ) ),
				'parent' => 'bxqn-systeminfo',
			] );
			
			if ( defined( 'SYSTEM_DASHBOARD_VERSION' ) ) {
				$wp_admin_bar->add_node( [
					'id'     => 'bxqn-systeminfo-system-dashboard',
					'title'  => esc_html__( 'by System Dashboard', 'bricks-quicknav' ),
					'href'   => esc_url( admin_url( 'index.php?page=system-dashboard' ) ),
					'parent' => 'bxqn-systeminfo',
				] );
			}  // end if System Dashboard
		}  // end if Compact Mode
	}

	/**
	 * Add group node for plugin support.
	 *
	 * @since 1.0.0
	 */
	private function add_plugin_support_group( $wp_admin_bar ) {
		$wp_admin_bar->add_group( [
			'id'     => 'bxqn-group-plugins',
			'parent' => 'ddw-bricks-quicknav',
		] );
		
		$this->maybe_add_plugin_submenus( $wp_admin_bar );
	}
	
	/**
	 * Add submenus for supported plugins - if they are active.
	 *
	 * @since 1.0.0
	 */
	private function maybe_add_plugin_submenus( $wp_admin_bar ) {
		
		/** Child Theme: SNN BRX (free) --> it's "plugin-like"! */
		if ( 'SNN BRX - Advanced Bricks Builder Child Theme' === wp_get_theme( get_stylesheet() )->get( 'Name' ) ) {
			$snnbrx_title = get_option( 'snn_menu_title', 'SNN Settings' );
			
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-snnbrx',
				'title'  => $this->get_icon( 'gear' ) . $snnbrx_title,
				'href'   => esc_url( admin_url( 'admin.php?page=snn-settings' ) ),
				'parent' => 'bxqn-group-plugins',
				'meta'   => [ 'class' => 'has-icon' ],
			] );
			
			$snnbrx_links = [
				'snn-custom-codes-snippets'  => [ 'title' => __( 'Code Snippets', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=snn-custom-codes-snippets' ) ) ],
				'snn-other-settings'         => [ 'title' => __( 'Other Settings', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=snn-other-settings' ) ) ],
				'editor-settings'            => [ 'title' => __( 'Editor Settings', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=editor-settings' ) ) ],
				'snn-security'               => [ 'title' => __( 'Security Settings', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=snn-security' ) ) ],
				'snn-custom-post-types'      => [ 'title' => __( 'Post Types', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=snn-custom-post-types' ) ) ],
				'snn-custom-fields'          => [ 'title' => __( 'Custom Fields', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=snn-custom-fields' ) ) ],
				'snn-taxonomies'             => [ 'title' => __( 'Taxonomies', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=snn-taxonomies' ) ) ],
				'login-settings'             => [ 'title' => __( 'Login Settings', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=login-settings' ) ) ],
				'snn-404-logs'               => [ 'title' => __( '404 Logs', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=snn-404-logs' ) ) ],
				'snn-search-logs'            => [ 'title' => __( 'Search Logs', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=snn-search-logs' ) ) ],
				'snn-301-redirects'          => [ 'title' => __( '301 Redirects', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=snn-301-redirects' ) ) ],
				'smtp-settings'              => [ 'title' => __( 'Mail SMTP Settings', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=smtp-settings' ) ) ],
				'snn-mail-logs'              => [ 'title' => __( 'Mail Logs', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=snn-mail-logs' ) ) ],
				'snn-media-settings'         => [ 'title' => __( 'Media Settings', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=snn-media-settings' ) ) ],
				'snn-cookie-settings'        => [ 'title' => __( 'Cookie Settings', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=snn-cookie-settings' ) ) ],
				'snn-role-management'        => [ 'title' => __( 'Role Management', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=snn-role-management' ) ) ],
				'snn-ai-settings'            => [ 'title' => __( 'AI Settings', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=snn-ai-settings' ) ) ],
				'snn-accessibility-settings' => [ 'title' => __( 'Accessibility Settings', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=snn-accessibility-settings' ) ) ],
				'snn-block-editor-settings'  => [ 'title' => __( 'Block Editor Settings', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=snn-block-editor-settings' ) ) ],
				'docs'                       => [ 'title' => __( 'Documentation', 'bricks-quicknav' ), 'url' => 'https://sinanisler.com/snn-brx/' ],
				'ghdiscussions'              => [ 'title' => __( 'GitHub Discussions', 'bricks-quicknav' ), 'url' => 'https://github.com/sinanisler/snn-brx-child-theme/discussions' ],
			];
			
			foreach ( $snnbrx_links as $snnbrx_id => $snnbrx_info ) {
				$wp_admin_bar->add_node( [
					'id'     => 'bxqn-snnbrx-' . $snnbrx_id,
					'title'  => $this->get_node_data( $snnbrx_info[ 'url' ], $snnbrx_info[ 'title' ] )[ 'title' ],
					'href'   => $snnbrx_info[ 'url' ],
					'parent' => 'bxqn-snnbrx',
					'meta'   => $this->get_node_data( $snnbrx_info[ 'url' ] )[ 'meta' ],
				] );
			}  // end foreach
			
			$snn_snippets_tabs = [
				'frontend'   => __( 'Frontend Head PHP/HTML', 'bricks-quicknav' ),
				'footer'     => __( 'Frontend Footer PHP/HTML', 'bricks-quicknav' ),
				'admin'      => __( 'Admin Head PHP/HTML', 'bricks-quicknav' ),
				'functions'  => __( 'Direct PHP (functions.php style)', 'bricks-quicknav' ),
				'error_logs' => __( 'Error Logs', 'bricks-quicknav' ),
			];
			
			foreach( $snn_snippets_tabs as $snn_stab => $snn_slabel ) {
				/** For the "plugin" hook place */
				$wp_admin_bar->add_node( [
					'id'     => 'bxqn-snnbrx-snippets-' . $snn_stab,
					'title'  => esc_html( $snn_slabel ),
					'href'   => esc_url( admin_url( 'admin.php?page=snn-custom-codes-snippets&tab=' . $snn_stab ) ),
					'parent' => 'bxqn-snnbrx-snn-custom-codes-snippets',
				] );
				
				/** For the child theme hook place --> @see $this->add_customcode_submenu() */
				$wp_admin_bar->add_node( [
					'id'     => 'bxqn-child-theme-snnbrx-' . $snn_stab,
					'title'  => esc_html( $snn_slabel ),
					'href'   => esc_url( admin_url( 'admin.php?page=snn-custom-codes-snippets&tab=' . $snn_stab ) ),
					'parent' => 'bxqn-group-hook-snnbrx',
				] );
			}  // end foreach
		}  // end if
		
		/** Plugin: Add Yabe Webfont (free & Pro) */
		if ( class_exists( '\Yabe\Webfont\Plugin' ) ) {
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-yabewebfont',
				'title'  => $this->get_icon( 'plugin' ) . esc_html__( 'Yabe Webfont', 'bricks-quicknav' ),
				'href'   => esc_url( admin_url( 'themes.php?page=yabe_webfont' ) ),
				'parent' => 'bxqn-group-plugins',
				'meta'   => [ 'class' => 'has-icon' ],
			] );
			
			$yabe_links = [
				'all-fonts'     => [ 'title' => __( 'All Fonts', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'themes.php?page=yabe_webfont#/fonts/index' ) ) ],
				'add-new'       => [ 'title' => __( 'Add New', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'themes.php?page=yabe_webfont#/fonts/create/custom' ) ) ],
				'import-google' => [ 'title' => __( 'Import Google Fonts', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'themes.php?page=yabe_webfont#/fonts/create/google-fonts' ) ) ],
				'settings'      => [ 'title' => __( 'Settings', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'themes.php?page=yabe_webfont#/settings' ) ) ],
				'migrations'    => [ 'title' => __( 'Migrations', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'themes.php?page=yabe_webfont#/migrations' ) ) ],
				'docs'          => [ 'title' => __( 'Documentation', 'bricks-quicknav' ), 'url' => 'https://webfont.yabe.land/docs?utm_source=wordpress-plugins&utm_medium=bricks-quicknav-plugin' ],
				'fbgroup'       => [ 'title' => __( 'Facebook Group', 'bricks-quicknav' ), 'url' => 'https://www.facebook.com/groups/1142662969627943' ],
			];
			
			foreach ( $yabe_links as $yabe_id => $yabe_info ) {
				$wp_admin_bar->add_node( [
					'id'     => 'bxqn-yabewebfont-' . $yabe_id,
					'title'  => $this->get_node_data( $yabe_info[ 'url' ], $yabe_info[ 'title' ] )[ 'title' ],
					'href'   => $yabe_info[ 'url' ],
					'parent' => 'bxqn-yabewebfont',
					'meta'   => $this->get_node_data( $yabe_info[ 'url' ] )[ 'meta' ],
				] );
			}  // end foreach
		}  // end if
		
		/** Plugin: GutenBricks (Premium) */
		if ( defined( 'GUTENBRICKS_VERSION' ) ) {
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-gutenbricks',
				'title'  => $this->get_icon( 'plugin' ) . 'GutenBricks',
				'href'   => esc_url( admin_url( 'admin.php?page=gutenbricks' ) ),
				'parent' => 'bxqn-group-plugins',
				'meta'   => [ 'class' => 'has-icon' ],
			] );
			
			$gutenbricks_tabs = [
				'bundles'            => __( 'Blocks', 'bricks-quicknav' ),
				'gutenberg-settings' => __( 'Gutenberg Settings', 'bricks-quicknav' ),
				'client-experience'  => __( 'Client Experience', 'bricks-quicknav' ),
				'integration'        => __( 'Integrations', 'bricks-quicknav' ),
				'license'            => __( 'License', 'bricks-quicknav' ),
			];
			
			foreach ( $gutenbricks_tabs as $gb_tab => $gb_tab_label ) {
				$wp_admin_bar->add_node( [
					'id'     => 'bxqn-gutenbricks-' . $gb_tab,
					'title'  => esc_html( $gb_tab_label ),
					'href'   => esc_url( admin_url( 'admin.php?page=gutenbricks&tab=' . $gb_tab ) ),
					'parent' => 'bxqn-gutenbricks',
				] );
			}  // end foreach
		}  // end if
		
		/** Plugin: Bricksforge (Premium) */
		if ( class_exists( 'Bricksforge' ) ) {
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-bricksforge',
				'title'  => $this->get_icon( 'plugin' ) . 'Bricksforge',
				'href'   => esc_url( admin_url( 'admin.php?page=bricksforge' ) ),
				'parent' => 'bxqn-group-plugins',
				'meta'   => [ 'class' => 'has-icon' ],
			] );
			
			$bricksforge_links = [
				'general'    => [ 'title' => __( 'General', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=bricksforge#/' ) ) ],
				'elements'   => [ 'title' => __( 'Elements', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=bricksforge#elements' ) ) ],
				'extensions' => [ 'title' => __( 'Extensions', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=bricksforge#tools' ) ) ],
				'license'    => [ 'title' => __( 'License', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=bricksforge#license' ) ) ],
				'docs'       => [ 'title' => __( 'Documentation', 'bricks-quicknav' ), 'url' => 'https://docs.bricksforge.io/' ],
				'forum'      => [ 'title' => __( 'Forum', 'bricks-quicknav' ), 'url' => 'https://forum.bricksforge.io/' ],
				'fbgroup'    => [ 'title' => __( 'Facebook Group', 'bricks-quicknav' ), 'url' => 'https://www.facebook.com/groups/bricksforge' ],
			];
			
			foreach ( $bricksforge_links as $brf_id => $brf_info ) {
				$wp_admin_bar->add_node( [
					'id'     => 'bxqn-bricksforge-' . $brf_id,
					'title'  => $this->get_node_data( $brf_info[ 'url' ], $brf_info[ 'title' ] )[ 'title' ],
					'href'   => $brf_info[ 'url' ],
					'parent' => 'bxqn-bricksforge',
					'meta'   => $this->get_node_data( $brf_info[ 'url' ] )[ 'meta' ],
				] );
			}  // end foreach
			
			$bricksforge_features[ 'whitelabel' ]     = __( 'White Label', 'bricks-quicknav' );
			$bricksforge_features[ 'permissions' ]    = __( 'Builder Customizer', 'bricks-quicknav' );
			$bricksforge_features[ 'global-classes' ] = __( 'Global Classes', 'bricks-quicknav' );
			
			$brf_features = get_option( 'brf_activated_tools' );
			if ( $brf_features ) {
				if ( in_array( 4, $brf_features ) ) $bricksforge_features[ 'maintenance' ] = __( 'Maintenance', 'bricks-quicknav' );
				if ( in_array( 9, $brf_features ) ) $bricksforge_features[ 'backendDesigner' ] = __( 'Backend Designer', 'bricks-quicknav' );
				if ( in_array( 13, $brf_features ) ) $bricksforge_features[ 'emailDesigner' ] = __( 'Email Designer', 'bricks-quicknav' );
				if ( in_array( 18, $brf_features ) ) $bricksforge_features[ 'apiQueryBuilder' ] = __( 'API Query Builder', 'bricks-quicknav' );
				if ( in_array( 16, $brf_features ) ) $bricksforge_features[ 'pageTransitions' ] = __( 'Page Transitions', 'bricks-quicknav' );
				if ( in_array( 17, $brf_features ) ) $bricksforge_features[ 'adminPages' ] = __( 'Admin Pages', 'bricks-quicknav' );
			}  // end if
			
			foreach ( $bricksforge_features as $brfeat_tab => $brfeat_tab_label ) {
				$wp_admin_bar->add_node( [
					'id'     => 'bxqn-bricksforge-extensions-' . $brfeat_tab,
					'title'  => esc_html( $brfeat_tab_label ),
					'href'   => esc_url( admin_url( 'admin.php?page=bricksforge#' . $brfeat_tab ) ),
					'parent' => 'bxqn-bricksforge-extensions',
				] );
			}  // end foreach
		}  // end if
		
		/** Plugin: BricksExtras (Premium) */
		if ( defined( 'BRICKSEXTRAS_BASE' ) ) {
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-bricksextras',
				'title'  => $this->get_icon( 'plugin' ) . 'BricksExtras',
				'href'   => esc_url( admin_url( 'admin.php?page=bricksextras_menu' ) ),
				'parent' => 'bxqn-group-plugins',
				'meta'   => [ 'class' => 'has-icon' ],
			] );
			
			$bricksextras_links = [
				'settings'   => [ 'title' => __( 'Elements & Features', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=bricksextras_menu&tab=settings' ) ) ],
				'conditions' => [ 'title' => __( 'Conditions', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=bricksextras_menu&tab=conditions' ) ) ],
				'misc'       => [ 'title' => __( 'Misc', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=bricksextras_menu&tab=misc' ) ) ],
				'changelog'  => [ 'title' => __( 'Changelog', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=bricksextras_menu&tab=changelog' ) ) ],
				'license'    => [ 'title' => __( 'License', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=bricksextras_menu&tab=license' ) ) ],
				'docs'       => [ 'title' => __( 'Documentation', 'bricks-quicknav' ), 'url' => 'https://bricksextras.com/docs/?utm_source=wordpress-plugins&utm_medium=bricks-quicknav-plugin' ],
				'fbgroup'    => [ 'title' => __( 'Facebook Group', 'bricks-quicknav' ), 'url' => 'https://www.facebook.com/groups/bricksextras/' ],
			];
			
			foreach ( $bricksextras_links as $bex_id => $bex_info ) {
				$wp_admin_bar->add_node( [
					'id'     => 'bxqn-bricksextras-' . $bex_id,
					'title'  => $this->get_node_data( $bex_info[ 'url' ], $bex_info[ 'title' ] )[ 'title' ],
					'href'   => $bex_info[ 'url' ],
					'parent' => 'bxqn-bricksextras',
					'meta'   => $this->get_node_data( $bex_info[ 'url' ] )[ 'meta' ],
				] );
			}  // end foreach
		}  // end if
		
		/** Plugin: BricksUltimate (Premium) */
		if ( class_exists( 'BricksUltimate\Plugin' ) ) {
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-bricksultimate',
				'title'  => $this->get_icon( 'plugin' ) . 'BricksUltimate',
				'href'   => esc_url( admin_url( 'admin.php?page=bricksultimate' ) ),
				'parent' => 'bxqn-group-plugins',
				'meta'   => [ 'class' => 'has-icon' ],
			] );
			
			$bu_options = get_option( 'bu_settings', [] );
				
			$bricksultimate_links = [
				'general'       => [ 'title' => __( 'General Elements', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=bricksultimate&tab=elements' ) ) ],
				'misc-settings' => [ 'title' => __( 'Misc Settings', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=bricksultimate&tab=misc' ) ) ],
				'import-export' => [ 'title' => __( 'Import/ Export', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=bricksultimate&tab=impexp' ) ) ],
				'license'       => [ 'title' => __( 'License', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=bricksultimate&tab=license' ) ) ],
			];

			if ( ! isset( $bu_options[ 'wl_tab' ] ) ) $bricksultimate_links[ 'whitelabel' ] = [ 'title' => __( 'White Label', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=bricksultimate&tab=whitelabel' ) ) ];
			
			$bricksultimate_links[ 'docs' ]    = [ 'title' => __( 'Documentation', 'bricks-quicknav' ), 'url' => 'https://www.bricksultimate.com/' ];
			$bricksultimate_links[ 'fbgroup' ] = [ 'title' => __( 'Facebook Group', 'bricks-quicknav' ), 'url' => 'https://www.facebook.com/groups/bultimate' ];
			
			foreach ( $bricksultimate_links as $bu_id => $bu_info ) {
				$wp_admin_bar->add_node( [
					'id'     => 'bxqn-bricksultimate-' . $bu_id,
					'title'  => $this->get_node_data( $bu_info[ 'url' ], $bu_info[ 'title' ] )[ 'title' ],
					'href'   => $bu_info[ 'url' ],
					'parent' => 'bxqn-bricksultimate',
					'meta'   => $this->get_node_data( $bu_info[ 'url' ] )[ 'meta' ],
				] );
			}  // end foreach
		}  // end if
		
		/** Plugin: Bricksable (free) */
		if ( function_exists( 'bricksable' ) ) {
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-bricksable',
				'title'  => $this->get_icon( 'plugin' ) . 'Bricksable',
				'href'   => esc_url( admin_url( 'admin.php?page=bricksable_settings' ) ),
				'parent' => 'bxqn-group-plugins',
				'meta'   => [ 'class' => 'has-icon' ],
			] );
			
			$bricksable_links = [
				'general'                => [ 'title' => __( 'General Settings', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=bricksable_settings&tab=general' ) ) ],
				'elements'               => [ 'title' => __( 'Bricksable Elements', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=bricksable_settings&tab=elements' ) ) ],
				'bricksbuilder_elements' => [ 'title' => __( 'Bricks Builder Elements', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=bricksable_settings&tab=bricksbuilder_elements' ) ) ],
				'bricksbuilder'          => [ 'title' => __( 'Save Messages', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=bricksable_settings&tab=bricksbuilder' ) ) ],
				'misc'                   => [ 'title' => __( 'Misc Settings', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=bricksable_settings&tab=misc' ) ) ],
				'docs'                   => [ 'title' => __( 'Documentation', 'bricks-quicknav' ), 'url' => 'https://docs.bricksable.com/?utm_source=wordpress-plugins&utm_medium=bricks-quicknav-plugin' ],
				'fbgroup'                => [ 'title' => __( 'Facebook Group', 'bricks-quicknav' ), 'url' => 'https://www.facebook.com/groups/bricksable' ],
			];
			
			foreach ( $bricksable_links as $babl_id => $babl_info ) {
				$wp_admin_bar->add_node( [
					'id'     => 'bxqn-bricksable-' . $babl_id,
					'title'  => $this->get_node_data( $babl_info[ 'url' ], $babl_info[ 'title' ] )[ 'title' ],
					'href'   => $babl_info[ 'url' ],
					'parent' => 'bxqn-bricksable',
					'meta'   => $this->get_node_data( $babl_info[ 'url' ] )[ 'meta' ],
				] );
			}  // end foreach
		}  // end if
		
		/** Plugin: Swiss Knife Bricks (Premium) */
		if ( defined( 'SWISS_BRICKS_VERSION' ) ) {
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-swissknifeb',
				'title'  => $this->get_icon( 'plugin' ) . esc_html__( 'Swiss Knife Bricks', 'bricks-quicknav' ),
				'href'   => esc_url( admin_url( 'admin.php?page=swiss-bricks' ) ),
				'parent' => 'bxqn-group-plugins',
				'meta'   => [ 'class' => 'has-icon' ],
			] );

			$swissknifeb_tabs = ( 'yes' === get_option( 'swissb_scripts_manager' ) ) ? [ 'swiss-bricks-scripts' => __( 'Scripts Manager', 'bricks-quicknav' ) ] : [];
			$swissknifeb_tabs[ 'swiss-bricks' ]         = __( 'Features', 'bricks-quicknav' );
			$swissknifeb_tabs[ 'swiss-bricks-dumper' ]  = __( 'Manage Settings', 'bricks-quicknav' );
			$swissknifeb_tabs[ 'swiss-bricks-license' ] = __( 'License', 'bricks-quicknav' );
			
			foreach ( $swissknifeb_tabs as $skb_tab => $skb_tab_label ) {
				$wp_admin_bar->add_node( [
					'id'     => 'bxqn-swissknifeb-' . $skb_tab,
					'title'  => esc_html( $skb_tab_label ),
					'href'   => esc_url( admin_url( 'admin.php?page=' . $skb_tab ) ),
					'parent' => 'bxqn-swissknifeb',
				] );
			}  // end foreach
		}  // end if
		
		/** Plugin: Max Addons (free) - currently only free version supported */
		if ( defined( 'MAB_VER' ) ) {
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-maxaddons',
				'title'  => $this->get_icon( 'plugin' ) . 'Max Addons',
				'href'   => esc_url( admin_url( 'admin.php?page=mab-settings' ) ),
				'parent' => 'bxqn-group-plugins',
				'meta'   => [ 'class' => 'has-icon' ],
			] );
		}  // end if
		
		/** Plugin: Bricks Element Manager (free) */
		if ( defined( 'BELM_VERSION' ) ) {
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-belementmanager',
				'title'  => $this->get_icon( 'plugin' ) . esc_html__( 'Element Manager', 'bricks-quicknav' ),
				'href'   => esc_url( admin_url( 'admin.php?page=element-manager' ) ),
				'parent' => 'bxqn-group-plugins',
				'meta'   => [ 'class' => 'has-icon' ],
			] );
		}  // end if
		
		/** Plugin: EasyDash for Bricks (free) */
		if ( class_exists( 'EDBB_Plugin' ) ) {
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-beasydash',
				'title'  => $this->get_icon( 'plugin' ) . 'EasyDash',
				'href'   => esc_url( admin_url( 'admin.php?page=easydash-bricks-settings' ) ),
				'parent' => 'bxqn-group-plugins',
				'meta'   => [ 'class' => 'has-icon' ],
			] );
		}  // end if
		
		/** Plugin: Bricks Admin Dashboard (Premium) */
		if ( class_exists( 'BricksAdminDashboard' ) ) {
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-badmindashboard',
				'title'  => $this->get_icon( 'plugin' ) . esc_html__( 'Bricks Admin Dashboard', 'bricks-quicknav' ),
				'href'   => esc_url( admin_url( 'admin.php?page=bricks-admin-dashboard' ) ),
				'parent' => 'bxqn-group-plugins',
				'meta'   => [ 'class' => 'has-icon' ],
			] );
		}  // end if
		
		/** Plugin: Bricks Remote Template Sync (free) */
		if ( defined( 'BRICKS_REMOTE_SYNC_VERSION' ) ) {
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-btemplatesync',
				'title'  => $this->get_icon( 'plugin' ) . esc_html__( 'Remote Template Sync', 'bricks-quicknav' ),
				'href'   => esc_url( admin_url( 'admin.php?page=bricks-remote-template-sync' ) ),
				'parent' => 'bxqn-group-plugins',
				'meta'   => [ 'class' => 'has-icon' ],
			] );
		}  // end if
	}
	
	/**
	 * Add group node for footer items (Links & About)
	 *
	 * @since 1.0.0
	 */
	private function add_footer_group( $wp_admin_bar ) {
		
		if ( defined( 'BXQN_DISABLE_FOOTER' ) && 'yes' === BXQN_DISABLE_FOOTER ) return $wp_admin_bar;
		
		$wp_admin_bar->add_group( [
			'id'     => 'bxqn-group-footer',
			'parent' => 'ddw-bricks-quicknav',
			'meta'   => [ 'class' => 'ab-sub-secondary' ],
		] );
		
		$this->add_links_submenu( $wp_admin_bar );
		$this->add_community_submenu( $wp_admin_bar );
		$this->add_about_submenu( $wp_admin_bar );
	}
	
	/**
	 * Add Links submenu
	 *
	 * @since 1.0.0
	 */
	private function add_links_submenu( $wp_admin_bar ) {
		
		$wp_admin_bar->add_node( [
			'id'     => 'bxqn-links',
			'title'  => $this->get_icon( 'links' ) . esc_html__( 'Links', 'bricks-quicknav' ),
			'href'   => '#',
			'parent' => 'bxqn-group-footer',
			'meta'   => [ 'class' => 'has-icon' ],
		] );

		$links = [
			'bricks-hq' => [
				'title' => __( 'Bricks HQ', 'bricks-quicknav' ),
				'url'   => 'https://bricksbuilder.io/',
			],
			'bricks-product-resources' => [
				'title' => __( 'Bricks Product Resources', 'bricks-quicknav' ),
				'url'   => 'https://bricksbuilder.io/changelog/',
			],
			'bricks-academy' => [
				'title' => __( 'Bricks Academy (Docs)', 'bricks-quicknav' ),
				'url'   => 'https://academy.bricksbuilder.io/',
			],
			'bricks-youtube' => [
				'title' => __( 'Bricks YouTube Channel', 'bricks-quicknav' ),
				'url'   => 'https://www.youtube.com/c/bricksbuilder/videos',
			],
			'bricks-forum' => [
				'title' => __( 'Bricks Forum', 'bricks-quicknav' ),
				'url'   => 'https://forum.bricksbuilder.io/',
			],
			'bricks-fb-group' => [
				'title' => __( 'Bricks FB Group', 'bricks-quicknav' ),
				'url'   => 'https://www.facebook.com/groups/brickscommunity',
			],
		];
		
		$links = apply_filters( 'ddw-bxqn/links/bricks', $links );
		
		$resources = [
			'changelog' => [
				'title' => __( 'Changelog', 'bricks-quicknav' ),
				'url'   => 'https://bricksbuilder.io/changelog/',
			],
			'roadmap' => [
				'title' => __( 'Roadmap', 'bricks-quicknav' ),
				'url'   => 'https://bricksbuilder.io/roadmap/',
			],
			'idea-board' => [
				'title' => __( 'Idea Board', 'bricks-quicknav' ),
				'url'   => 'https://bricksbuilder.io/ideas/',
			],
			'experts' => [
				'title' => __( 'Experts', 'bricks-quicknav' ),
				'url'   => 'https://bricksbuilder.io/experts',
			],
			'showcase' => [
				'title' => __( 'Showcase', 'bricks-quicknav' ),
				'url'   => 'https://bricksbuilder.io/showcase/',
			],
		];

		$resources = apply_filters( 'ddw-bxqn/links/product-resources', $resources );
		
		foreach ( $links as $id => $info ) {
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-link-' . sanitize_key( $id ),
				'title'  => esc_html( $info[ 'title' ] ),
				'href'   => esc_url( $info[ 'url' ] ),
				'parent' => 'bxqn-links',
				'meta'   => [ 'target' => '_blank', 'rel' => 'nofollow noopener noreferrer' ],
			] );
		}  // end foreach
		
		foreach ( $resources as $id => $info ) {
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-presource-' . sanitize_key( $id ),
				'title'  => esc_html( $info[ 'title' ] ),
				'href'   => esc_url( $info[ 'url' ] ),
				'parent' => 'bxqn-link-bricks-product-resources',
				'meta'   => [ 'target' => '_blank', 'rel' => 'nofollow noopener noreferrer' ],
			] );
		}  // end foreach
	}

	/**
	 * Add community submenu
	 *
	 * @since 1.0.0
	 */
	private function add_community_submenu( $wp_admin_bar ) {
		
		$string_docs      = __( 'Documentation', 'bricks-quicknav' );
		$string_fbgroup   = __( 'Facebook Group', 'bricks-quicknav' );
		$string_circle    = __( 'Circle Community', 'bricks-quicknav' );
		$string_ghdiscuss = __( 'GitHub Discussions', 'bricks-quicknav' );
		$string_youtube   = __( 'YouTube Tutorials', 'bricks-quicknav' );
		
		$wp_admin_bar->add_node( [
			'id'     => 'bxqn-community-links',
			'title'  => $this->get_icon( 'community' ) . esc_html__( 'Community', 'bricks-quicknav' ),
			'href'   => '#',
			'parent' => 'bxqn-group-footer',
			'meta'   => [ 'class' => 'has-icon' ],
		] );
		
		$links = [
			/** Add-On plugins */
			'addons' => [
				'label' => _x( 'Add-On Plugins', 'Section label for Bricks community links collection', 'bricks-quicknav' ),
				'icon'  => 'plugin',
				'items' => [
					'bricksextras' => [
						'title'   => 'BricksExtras',
						'url'     => 'https://bricksextras.com/',
						'docs'    => 'https://bricksextras.com/docs/',
						'fbgroup' => 'https://www.facebook.com/groups/bricksextras',
					],
					'bricksforge' => [
						'title'   => 'Bricksforge',
						'url'     => 'https://bricksforge.io/',
						'docs'    => 'https://docs.bricksforge.io/',
						'fbgroup' => 'https://www.facebook.com/groups/bricksforge',
					],
					'advancedthemer' => [
						'title'   => 'Advanced Themer',
						'url'     => 'https://advancedthemer.com/',
						'fbgroup' => 'https://www.facebook.com/groups/advancedthemercommunity',
					],
					'bricksable' => [
						'title'   => 'Bricksable',
						'url'     => 'https://bricksable.com/',
						'docs'    => 'https://docs.bricksable.com/',
						'fbgroup' => 'https://www.facebook.com/groups/bricksable',
					],
					'maxaddons' => [
						'title'   => 'Max Addons',
						'url'     => 'https://wpbricksaddons.com/',
						'docs'    => 'https://wpbricksaddons.com/docs/',
						'fbgroup' => 'https://www.facebook.com/groups/335236455357790',
					],
				],
			],
			
			/** CSS Frameworks */
			'frameworks' => [
				'label' => _x( 'Frameworks', 'Section label for Bricks community links collection', 'bricks-quicknav' ),
				'icon'  => 'css3',
				'items' => [
					'automaticcss' => [
						'title'  => 'Automatic.CSS',
						'url'    => 'https://automaticcss.com/',
						'docs'   => 'https://automaticcss.com/docs/',
						'circle' => 'https://community.automaticcss.com/c/bricks/',
					],
					'coreframework' => [
						'title'   => __( 'Core Framework', 'bricks-quicknav' ),
						'url'     => 'https://coreframework.com/',
						'docs'    => 'https://docs.coreframework.com/',
						'fbgroup' => 'https://www.facebook.com/groups/coreframework',
					],
					'fancyframework' => [
						'title'   => __( 'Fancy Framework', 'bricks-quicknav' ),
						'url'     => 'https://fancyframework.com/',
						'fbgroup' => 'https://www.facebook.com/groups/fancybricks',
					],
				],
			],
			
			/** Layouts, Sections, Components, Parts etc. */
			'layouts' => [
				'label' => _x( 'Layouts, Sections etc.', 'Section label for Bricks community links collection', 'bricks-quicknav' ),
				'icon'  => 'frames',
				'items' => [
					'brixies' => [
						'title'   => __( 'Brixies Library', 'bricks-quicknav' ),
						'url'     => 'https://brixies.co/',
						'fbgroup' => 'https://www.facebook.com/groups/brixies',
					],
					'bricksmaven' => [
						'title'   => 'Bricks Maven',
						'url'     => 'https://bricksmaven.com/',
						'fbgroup' => 'https://www.facebook.com/groups/bricksmaven',
					],
					'frames' => [
						'title'  => __( 'Frames Layouts & Components', 'bricks-quicknav' ),
						'url'    => 'https://getframes.io/',
						'docs'   => 'https://getframes.io/docs/',
						'circle' => 'https://community.automaticcss.com/c/general-discussion-frames/',
					],
					'bricks-sections' => [
						'title' => __( 'Bricks Sections', 'bricks-quicknav' ),
						'url'   => 'https://brickssections.com/',
					],
				],
			],
			
			/** Tutorials */
			'tutorials' => [
				'label' => _x( 'Tutorials', 'Section label for Bricks community links collection', 'bricks-quicknav' ),
				'icon'  => 'learn',
				'items' => [
					'brickslabs' => [
						'title'   => __( 'BricksLabs Tutorials', 'bricks-quicknav' ),
						'url'     => 'https://brickslabs.com/tutorials-list/',
						'fbgroup' => 'https://www.facebook.com/groups/brickslabs',
					],
					'learn-bricksbuilder' => [
						'title'   => __( 'Learn Bricks Builder', 'bricks-quicknav' ),
						'url'     => 'https://learnbricksbuilder.com/',
						'fbgroup' => 'https://www.facebook.com/groups/wpcrue',
					],
					'kg-pagebuilding-101' => [
						'title' => __( 'Page Building 101 by Kevin Geary', 'bricks-quicknav' ),
						'url'   => 'https://www.youtube.com/playlist?list=PLBpy-YllkBawiMQNVh8ZBXIz8QW_vUjiV',
					],
				],
			],
			
			/** Other link collections & directories */
			'collections' => [
				'label' => _x( 'Collections', 'Section label for Bricks community links collection', 'bricks-quicknav' ),
				'icon'  => 'collection',
				'items' => [
					'bricksdirectory' => [
						'title' => __( 'Bricks Directory', 'bricks-quicknav' ),
						'url'   => 'https://bricksdirectory.com/',
					],
					'brickslinks' => [
						'title' => __( 'Bricks Links', 'bricks-quicknav' ),
						'url'   => 'https://brickslinks.start.me/home',	// https://brickslinks.com/
					],
					'bricksbible' => [
						'title' => __( 'Bricks Bible', 'bricks-quicknav' ),
						'url'   => 'https://bricksbible.com/',
					],
				],
			],
			
			/** Misc. */
			'misc' => [
				'label' => _x( 'More stuff', 'Section label for Bricks community links collection', 'bricks-quicknav' ),
				'icon'  => 'gear',
				'items' => [
					'bricksbee' => [
						'title' => __( 'BricksBee Components & Templates', 'bricks-quicknav' ),
						'url'   => 'https://bricksbee.com/',
					],
					'bricks-codex' => [
						'title' => __( 'Bricks Code Snippets', 'bricks-quicknav' ),
						'url'   => 'https://sinanisler.com/codex-topic/bricks-builder/',
					],
					'built-with-bricks' => [
						'title' => __( 'Built with Bricks', 'bricks-quicknav' ),
						'url'   => 'https://www.davefoy.com/p/build-with-bricks/',
					],
					'snn-brx-child' => [
						'title'     => __( 'SNN BRX Child Theme', 'bricks-quicknav' ),
						'url'       => 'https://sinanisler.com/snn-brx/',
						'youtube'   => 'https://www.youtube.com/playlist?list=PLEEetjPxkno-c1se45EUmt4jfIUE3yQ-f',
						'ghdiscuss' => 'https://github.com/sinanisler/snn-brx-child-theme/discussions',
					],
				],
			],
		];
		
		$links = apply_filters( 'ddw-bxqn/links/community', $links );
		
		if ( $links ) {
			foreach ( $links as $section_id => $section_data ) {
				$section_id = sanitize_key( $section_id );
				
				$wp_admin_bar->add_node( [
					'id'     => 'bxqn-links-' . $section_id,
					'title'  => $this->get_icon( sanitize_key( $section_data[ 'icon' ] ) ) . esc_html( $section_data[ 'label' ] ),
					'href'   => '#',
					'parent' => 'bxqn-community-links',
					'meta'   => [ 'class' => 'has-icon' ],
					//'meta'   => [ 'target' => '_blank', 'rel' => 'nofollow noopener noreferrer' ],
				] );
				
				foreach ( $section_data[ 'items' ] as $link_id => $link_data ) {
					$link_id = sanitize_key( $link_id );
					
					$wp_admin_bar->add_node( [
						'id'     => 'bxqn-links-' . $section_id . '-' . $link_id,
						'title'  => esc_html( $link_data[ 'title' ] ),
						'href'   => esc_url( $link_data[ 'url' ] ),
						'parent' => 'bxqn-links-' . $section_id,
						'meta'   => [ 'target' => '_blank', 'rel' => 'nofollow noopener noreferrer' ],
					] );
					
					if ( isset( $link_data[ 'docs' ] ) ) {
						$wp_admin_bar->add_node( [
							'id'     => 'bxqn-links-' . $section_id . '-' . $link_id . '-docs',
							'title'  => esc_html( $string_docs ),
							'href'   => esc_url( $link_data[ 'docs' ] ),
							'parent' => 'bxqn-links-' . $section_id . '-' . $link_id,
							'meta'   => [ 'target' => '_blank', 'rel' => 'nofollow noopener noreferrer' ],
						] );
					}  // end if
					
					if ( isset( $link_data[ 'fbgroup' ] ) ) {
						$wp_admin_bar->add_node( [
							'id'     => 'bxqn-links-' . $section_id . '-' . $link_id . '-fbgroup',
							'title'  => esc_html( $string_fbgroup ),
							'href'   => esc_url( $link_data[ 'fbgroup' ] ),
							'parent' => 'bxqn-links-' . $section_id . '-' . $link_id,
							'meta'   => [ 'target' => '_blank', 'rel' => 'nofollow noopener noreferrer' ],
						] );
					}  // end if
					
					if ( isset( $link_data[ 'circle' ] ) ) {
						$wp_admin_bar->add_node( [
							'id'     => 'bxqn-links-' . $section_id . '-' . $link_id . '-circle',
							'title'  => esc_html( $string_circle ),
							'href'   => esc_url( $link_data[ 'circle' ] ),
							'parent' => 'bxqn-links-' . $section_id . '-' . $link_id,
							'meta'   => [ 'target' => '_blank', 'rel' => 'nofollow noopener noreferrer' ],
						] );
					}  // end if
					
					if ( isset( $link_data[ 'youtube' ] ) ) {
						$wp_admin_bar->add_node( [
							'id'     => 'bxqn-links-' . $section_id . '-' . $link_id . '-youtube',
							'title'  => esc_html( $string_youtube ),
							'href'   => esc_url( $link_data[ 'youtube' ] ),
							'parent' => 'bxqn-links-' . $section_id . '-' . $link_id,
							'meta'   => [ 'target' => '_blank', 'rel' => 'nofollow noopener noreferrer' ],
						] );
					}  // end if
					
					if ( isset( $link_data[ 'ghdiscuss' ] ) ) {
						$wp_admin_bar->add_node( [
							'id'     => 'bxqn-links-' . $section_id . '-' . $link_id . '-ghdiscussions',
							'title'  => esc_html( $string_ghdiscuss ),
							'href'   => esc_url( $link_data[ 'ghdiscuss' ] ),
							'parent' => 'bxqn-links-' . $section_id . '-' . $link_id,
							'meta'   => [ 'target' => '_blank', 'rel' => 'nofollow noopener noreferrer' ],
						] );
					}  // end if
				}  // end foreach (links within sections)
			}  // end foreach (sections)
		}  // end if
	}
	
	/**
	 * Add About submenu
	 *
	 * @since 1.0.0
	 */
	private function add_about_submenu( $wp_admin_bar ) {
		
		$wp_admin_bar->add_node( [
			'id'     => 'bxqn-about',
			'title'  => $this->get_icon( 'about' ) . esc_html__( 'About', 'bricks-quicknav' ),
			'href'   => '#',
			'parent' => 'bxqn-group-footer',
			'meta'   => [ 'class' => 'has-icon' ],
		] );

		$about_links = [
			'author' => [
				'title' => __( 'Author: David Decker', 'bricks-quicknav' ),
				'url'   => self::$author_url,
			],
			/* 'plugin-website' => [
				'title' => __( 'Plugin Website', 'bricks-quicknav' ),
				'url'   => self::$plugin_url,
			], */
			'github' => [
				'title' => __( 'Plugin on GitHub', 'bricks-quicknav' ),
				'url'   => self::$github_url,
			],
			'kofi' => [
				'title' => __( 'Buy Me a Coffee', 'bricks-quicknav' ),
				'url'   => 'https://ko-fi.com/deckerweb',
			],
		];

		foreach ( $about_links as $id => $info ) {
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-about-' . sanitize_key( $id ),
				'title'  => esc_html( $info[ 'title' ] ),
				'href'   => esc_url( $info[ 'url' ] ),
				'parent' => 'bxqn-about',
				'meta'   => [ 'target' => '_blank', 'rel' => 'nofollow noopener noreferrer' ],
			] );
		}  // end foreach
	}
	
	/**
	 * Optionally show the Admin Bar also within the Builder context.
	 *
	 * @inspired by BricksLabs Bricks Navigator
	 *
	 * @since 1.0.0
	 */
	public function maybe_add_adminbar_in_builder() {
		
		if ( defined( 'BXQN_ADMINBAR_IN_BUILDER' ) && 'yes' !== BXQN_ADMINBAR_IN_BUILDER ) return;
		
		/** if this is not the outer frame, abort */
		if ( ! bricks_is_builder_main() || ! $this->user_can_use_bricks_builder() ) return;
	
		add_filter( 'show_admin_bar', '__return_true' );
		add_action( 'wp_head', [ $this, 'builder_adminbar_styles' ] );  // Builder-context (front-end)
	}
	
	/**
	 * For showing the Admin Bar in Builder context, add the needed CSS styles.
	 *
	 * @inspired by BricksLabs Bricks Navigator
	 *
	 * @since 1.0.0
	 */
	public function builder_adminbar_styles() {
		echo
		'
			<style>
			/* for Admin Bar */
			body.admin-bar #bricks-toolbar {
				top: var(--wp-admin--admin-bar--height);
			}
	
			/ for Bricks Builder */
			#bricks-structure {
				top: calc(40px + var(--wp-admin--admin-bar--height));
			}
			
			/* for QuickNav icons */
			#wpadminbar .has-icon .icon-svg svg {
				display: inline-block;
				margin-bottom: 3px;
				vertical-align: middle;
				width: 16px;
				height: 16px;
			}
			</style>
		';
	}
	
	/**
	 * When editing Bricks Templates in WordPress Admin (not in Builder itself),
	 *   show the correct admin menu parent.
	 *
	 * @since 1.0.0
	 *
	 * @global string $GLOBALS[ 'submenu_file' ]
	 *
	 * @param  string $parent_file  The filename of the parent menu.
	 * @return string $parent_file  The tweaked filename of the parent menu.
	 */
	public function bricks_template_parent( $parent_file ) {
	
		if ( 'bricks_template' === get_current_screen()->post_type ) {
			$GLOBALS[ 'submenu_file' ] = 'edit.php?post_type=bricks_template';
			$parent_file = 'bricks';
		}
	
		return $parent_file;
	}
	
	/**
	 * Show the Admin Bar also in Block Editor full screen mode.
	 *
	 * @since 1.0.0
	 */
	public function adminbar_block_editor_fullscreen() {
		
		if ( ! is_admin_bar_showing() ) return;
		
		/**
		 * Depending on user color scheme get proper bg color value for admin bar.
		 */
		$user_color_scheme = get_user_option( 'admin_color' );
		$admin_scheme      = $this->get_scheme_colors();
		
		$bg_color = $admin_scheme[ $user_color_scheme ][ 'bg' ];
		
		$inline_css_block_editor = sprintf(
			'
				@media (min-width: 600px) {
					body.is-fullscreen-mode .block-editor__container {
						top: var(--wp-admin--admin-bar--height);
					}
				}
				
				@media (min-width: 782px) {
					body.js.is-fullscreen-mode #wpadminbar {
						display: block;
					}
				
					body.is-fullscreen-mode .block-editor__container {
						min-height: calc(100vh - var(--wp-admin--admin-bar--height));
					}
				
					body.is-fullscreen-mode .edit-post-layout .editor-post-publish-panel {
						top: var(--wp-admin--admin-bar--height);
					}
					
					.edit-post-fullscreen-mode-close.components-button {
						background: %s;
					}
					
					.edit-post-fullscreen-mode-close.components-button::before {
						box-shadow: none;
					}
				}
				
				@media (min-width: 783px) {
					.is-fullscreen-mode .interface-interface-skeleton {
						top: var(--wp-admin--admin-bar--height);
					}
				}
			',
			sanitize_hex_color( $bg_color )
		);
		
		wp_add_inline_style( 'wp-block-editor', $inline_css_block_editor );
		
		$inline_css_edit_site = sprintf(
			'
				body.is-fullscreen-mode .edit-site {
					top: var(--wp-admin--admin-bar--height);
				}
				
				body.is-fullscreen-mode .edit-site-layout__canvas-container {
					top: calc( var(--wp-admin--admin-bar--height) * -1 );
				}
				
				.edit-site-editor__view-mode-toggle .edit-site-editor__view-mode-toggle-icon img,
				.edit-site-editor__view-mode-toggle .edit-site-editor__view-mode-toggle-icon svg {
						background: %s;
				}
			',
			sanitize_hex_color( $bg_color )
		);
		
		wp_add_inline_style( 'wp-edit-site', $inline_css_edit_site );
		
		add_action( 'admin_bar_menu', [ $this, 'remove_adminbar_nodes' ], 999 );
	}
	
	/**
	 * Remove Admin Bar nodes.
	 *
	 * @since 1.0.0
	 */
	public function remove_adminbar_nodes( $wp_admin_bar ) {
		$wp_admin_bar->remove_node( 'wp-logo' );  
	}
	
	/**
	 * Add additional plugin related info to the Site Health Debug Info section.
	 *
	 * @link https://make.wordpress.org/core/2019/04/25/site-health-check-in-5-2/
	 *
	 * @since 1.0.0
	 *
	 * @param  array $debug_info  Array holding all Debug Info items.
	 * @return array $debug_info  Modified array of Debug Info.
	 */
	public function site_health_debug_info( $debug_info ) {
	
		$string_undefined = esc_html_x( 'Undefined', 'Site Health Debug info', 'bricks-quicknav' );
		$string_enabled   = esc_html_x( 'Enabled', 'Site Health Debug info', 'bricks-quicknav' );
		$string_disabled  = esc_html_x( 'Disabled', 'Site Health Debug info', 'bricks-quicknav' );
		$string_value     = ' – ' . esc_html_x( 'value', 'Site Health Debug info', 'bricks-quicknav' ) . ': ';
		$string_version   = defined( 'BRICKS_VERSION' ) ? BRICKS_VERSION : '';
	
		/** Add our Debug info */
		$debug_info[ 'bricks-quicknav' ] = [
			'label'  => self::$name . ' (' . esc_html__( 'Plugin', 'bricks-quicknav' ) . ')',
			'fields' => [
	
				/** Various values */
				'bxqn_plugin_version' => [
					'label' => esc_html__( 'Plugin version', 'bricks-quicknav' ),
					'value' => self::$version,
				],
				'bxqn_install_type' => [
					'label' => esc_html__( 'WordPress Install Type', 'bricks-quicknav' ),
					'value' => ( is_multisite() ? esc_html__( 'Multisite install', 'bricks-quicknav' ) : esc_html__( 'Single Site install', 'bricks-quicknav' ) ),
				],
	
				/** Bricks QuickNav constants */
				'BXQN_VIEW_CAPABILITY' => [
					'label' => 'BXQN_VIEW_CAPABILITY',
					'value' => ( ! defined( 'BXQN_VIEW_CAPABILITY' ) ? $string_undefined : ( BXQN_VIEW_CAPABILITY ? $string_enabled . $string_value . sanitize_key( BXQN_VIEW_CAPABILITY ) : $string_disabled ) ),
				],
				'BXQN_ENABLED_USERS' => [
					'label' => 'BXQN_ENABLED_USERS',
					'value' => ( ! defined( 'BXQN_ENABLED_USERS' ) ? $string_undefined : ( BXQN_ENABLED_USERS ? $string_enabled . $string_value . implode( ', ', array_map( 'absint', BXQN_ENABLED_USERS ) ) : $string_disabled ) ),
				],
				'BXQN_MENU_POSITION' => [
					'label' => 'BXQN_MENU_POSITION',
					'value' => ( ! defined( 'BXQN_MENU_POSITION' ) ? $string_undefined : ( BXQN_MENU_POSITION ? $string_enabled . $string_value . intval( self::$menu_position ) : $string_disabled ) ),
				],
				'BXQN_NAME_IN_ADMINBAR' => [
					'label' => 'BXQN_NAME_IN_ADMINBAR',
					'value' => ( ! defined( 'BXQN_NAME_IN_ADMINBAR' ) ? $string_undefined : ( BXQN_NAME_IN_ADMINBAR ? $string_enabled . $string_value . esc_html( BXQN_NAME_IN_ADMINBAR )  : $string_disabled ) ),
				],
				'BXQN_ICON' => [
					'label' => 'BXQN_ICON',
					'value' => ( ! defined( 'BXQN_ICON' ) ? $string_undefined : ( BXQN_ICON ? $string_enabled . $string_value . sanitize_key( BXQN_ICON ) : $string_disabled ) ),
				],
				'BXQN_NUMBER_TEMPLATES' => [
					'label' => 'BXQN_NUMBER_TEMPLATES',
					'value' => ( ! defined( 'BXQN_NUMBER_TEMPLATES' ) ? $string_undefined : ( BXQN_NUMBER_TEMPLATES ? $string_enabled . $string_value . absint( BXQN_NUMBER_TEMPLATES ) : $string_disabled ) ),
				],
				'BXQN_DISABLE_FOOTER' => [
					'label' => 'BXQN_DISABLE_FOOTER',
					'value' => ( ! defined( 'BXQN_DISABLE_FOOTER' ) ? $string_undefined : ( 'yes' === BXQN_DISABLE_FOOTER ? $string_enabled : $string_disabled ) ),
				],
				'BXQN_COMPACT_MODE' => [
					'label' => 'BXQN_COMPACT_MODE',
					'value' => ( ! defined( 'BXQN_COMPACT_MODE' ) ? $string_undefined : ( BXQN_COMPACT_MODE ? $string_enabled : $string_disabled ) ),
				],
				'BXQN_ADMINBAR_IN_BUILDER' => [
					'label' => 'BXQN_ADMINBAR_IN_BUILDER',
					'value' => ( ! defined( 'BXQN_ADMINBAR_IN_BUILDER' ) ? $string_undefined : ( 'yes' === BXQN_ADMINBAR_IN_BUILDER ? $string_enabled : $string_disabled ) ),
				],
				'bxqn_brx_version' => [
					'label' => esc_html__( 'Bricks Builder Version', 'bricks-quicknav' ),
					'value' => ( ! defined( 'BRICKS_VERSION' ) ? esc_html__( 'Bricks Theme not installed', 'bricks-quicknav' ) : $string_version ),
				],
			],  // end array 'fields'
		];  // end array $debug_info[ 'bricks-quicknav' ]
	
		if ( is_child_theme() ) {
			$debug_info[ 'bricks-quicknav' ][ 'fields' ][ 'bxqn_child_theme_data' ] = [
				'label' => esc_html__( 'Bricks with active Child Theme', 'bricks-quicknav' ),
				'value' => esc_html__( 'Name', 'bricks-quicknav' ). ': ' . wp_get_theme( get_stylesheet() )->get( 'Name' ) . ' (' . get_stylesheet() . ') / ' . esc_html__( 'Version', 'bricks-quicknav' ) . ': ' . wp_get_theme( get_stylesheet() )->get( 'Version' ) . ' / ' . esc_html__( 'Author', 'bricks-quicknav' ) . ': ' . wp_get_theme( get_stylesheet() )->get( 'Author' ),
			];
		}
		
		/** Return modified Debug Info array */
		return $debug_info;
	}
	
}  // end of class

new DDW_Bricks_QuickNav();

endif;


if ( ! function_exists( 'ddw_bxqn_pluginrow_meta' ) ) :
	
add_filter( 'plugin_row_meta', 'ddw_bxqn_pluginrow_meta', 10, 2 );
/**
 * Add plugin related links to plugin page.
 *
 * @param  array  $ddwp_meta  (Default) Array of plugin meta links.
 * @param  string $ddwp_file  File location of plugin.
 * @return array  $ddwp_meta  (Modified) Array of plugin links/ meta.
 */
function ddw_bxqn_pluginrow_meta( $ddwp_meta, $ddwp_file ) {
 
	 if ( ! current_user_can( 'install_plugins' ) ) return $ddwp_meta;
 
	 /** Get current user */
	 $user = wp_get_current_user();
	 
	 /** Build Newsletter URL */
	 $url_nl = sprintf(
		 'https://deckerweb.us2.list-manage.com/subscribe?u=e09bef034abf80704e5ff9809&amp;id=380976af88&amp;MERGE0=%1$s&amp;MERGE1=%2$s',
		 esc_attr( $user->user_email ),
		 esc_attr( $user->user_firstname )
	 );
	 
	 /** List additional links only for this plugin */
	 if ( $ddwp_file === trailingslashit( dirname( plugin_basename( __FILE__ ) ) ) . basename( __FILE__ ) ) {
		 $ddwp_meta[] = sprintf(
			 '<a class="button button-inline" href="https://ko-fi.com/deckerweb" target="_blank" rel="nofollow noopener noreferrer" title="%1$s">❤ <b>%1$s</b></a>',
			 esc_html_x( 'Donate', 'Plugins page listing', 'bricks-quicknav' )
		 );
 
		 $ddwp_meta[] = sprintf(
			 '<a class="button-primary" href="%1$s" target="_blank" rel="nofollow noopener noreferrer" title="%2$s">⚡ <b>%2$s</b></a>',
			 $url_nl,
			 esc_html_x( 'Join our Newsletter', 'Plugins page listing', 'bricks-quicknav' )
		 );
	 }  // end if
 
	 return apply_filters( 'ddw-bxqn/pluginrow-meta', $ddwp_meta );
 
 }  // end function
 
 endif;