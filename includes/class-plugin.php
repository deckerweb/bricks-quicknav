<?php
/**
 * Host integration. @package BricksQuickNav
 * Settings adapted from Breakdance QuickNav 2.0.0 and Oxygen QuickNav 2.0.0, © 2025–2026 David Decker – DECKERWEB.
 * GPL-2.0-or-later. https://github.com/deckerweb/breakdance-quicknav/tree/v2.0.0
 * https://github.com/deckerweb/oxygen-quicknav/tree/v2.0.0
 */
namespace Deckerweb\BricksQuickNav;

defined( 'ABSPATH' ) || exit;

/** Coordinates toolbar rendering, diagnostics, and website/user settings. */
final class Plugin {
	/** @return void Registers one instance after all active plugins load. */
	public static function boot() {
		static $instance;
		if ( ! $instance ) { $instance = new self(); }
	}

	/** @return void Registers hooks without querying content or making remote requests. */
	public function __construct() {
		add_action( 'init', array( $this, 'translations' ), 5 );
		add_action( 'init', array( '\Deckerweb\BricksQuickNav\Updates', 'register' ), 20 );
		add_action( 'init', array( '\Deckerweb\BricksQuickNav\Toolbar', 'boot' ), 20 );
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 100 );
		add_action( 'admin_notices', array( $this, 'builder_notice' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'assets' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( DDW_BXQN_FILE ), array( $this, 'action_links' ) );
		add_filter( 'plugin_row_meta', array( $this, 'row_meta' ), 10, 2 );
		add_filter( 'debug_information', array( $this, 'diagnostics' ) );
		add_filter( 'parent_file', array( $this, 'template_parent' ), 5 );
		add_action( 'show_user_profile', array( $this, 'profile' ) );
		add_action( 'edit_user_profile', array( $this, 'profile' ) );
		add_action( 'personal_options_update', array( $this, 'save_profile' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_profile' ) );
	}

	/** @return void Makes packaged host translations available from init onward. */
	public function translations() {
		load_plugin_textdomain( 'bricks-quicknav', false, dirname( plugin_basename( DDW_BXQN_FILE ) ) . '/languages' );
	}

	/** @return string Website-scoped settings URL. */
	public static function url() {
		return admin_url( ( Builder::active() ? 'admin.php' : 'options-general.php' ) . '?page=bricks-quicknav' );
	}

	/**
	 * Keep Bricks template taxonomy screens under the existing theme menu.
	 * @param string $parent Current WordPress menu parent.
	 * @return string Bricks parent for its registered template taxonomies, otherwise unchanged.
	 */
	public function template_parent( $parent ) {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		return Builder::active() && $screen && in_array( $screen->taxonomy, array( 'template_bundle', 'template_tag' ), true ) ? 'bricks' : $parent;
	}

	/** @return void Adds a native settings page even when Bricks is inactive. */
	public function admin_menu() {
		if ( Builder::active() ) {
			add_submenu_page( 'bricks', 'Bricks QuickNav', 'QuickNav', 'manage_options', 'bricks-quicknav', array( $this, 'page' ) );
		} else {
			add_options_page( 'Bricks QuickNav', 'Bricks QuickNav', 'manage_options', 'bricks-quicknav', array( $this, 'page' ) );
		}
	}

	/** @return void Explains inactive navigation within the website's native plugin screen. */
	public function builder_notice() {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen || 'plugins' !== $screen->id || is_network_admin() || ! current_user_can( 'activate_plugins' ) || Builder::active() ) { return; }
		echo '<div class="notice notice-info"><p><strong>Bricks QuickNav:</strong> ' . esc_html__( 'Bricks is not active. QuickNav settings remain available; toolbar navigation resumes when a supported builder is active.', 'bricks-quicknav' );
		if ( current_user_can( 'manage_options' ) ) { echo ' <a href="' . esc_url( self::url() ) . '">' . esc_html__( 'Settings', 'bricks-quicknav' ) . '</a>'; }
		echo '</p></div>';
	}

	/** @return void Registers one sanitized website option with the WordPress Settings API. */
	public function register_settings() {
		register_setting( 'ddw_bxqn', Settings::OPTION, array( 'type' => 'array', 'sanitize_callback' => array( $this, 'save_settings' ), 'default' => Settings::defaults() ) );
	}

	/**
	 * Preserve stored defaults for fields locked by deployment constants.
	 * @param mixed $input Submitted website settings.
	 * @return array Validated website settings with existing locked values retained.
	 */
	public function save_settings( $input ) {
		$input = is_array( $input ) ? $input : array();
		$old = get_option( Settings::OPTION, Settings::defaults() );
		$old = is_array( $old ) ? array_merge( Settings::defaults(), $old ) : Settings::defaults();
		foreach ( array( 'integrations', 'integration_children', 'integration_direct' ) as $key ) { if ( ! array_key_exists( $key, $input ) ) { $input[ $key ] = $old[ $key ]; } }
		foreach ( Settings::constant_map() as $key => $constant ) { if ( defined( $constant ) ) { $input[ $key ] = $old[ $key ]; } }
		return Settings::sanitize( $input );
	}

	/**
	 * Check the user's toolbar visibility independently of target permissions.
	 * @param array $settings Effective settings.
	 * @return bool Whether QuickNav may render in the current website context.
	 */
	private function visible( $settings ) {
		return ! is_network_admin() && ! is_user_admin() && is_user_logged_in() && is_admin_bar_showing()
			&& ! empty( $settings[ is_admin() ? 'backend' : 'frontend' ] )
			&& current_user_can( $settings['capability'] )
			&& ( empty( $settings['users'] ) || in_array( get_current_user_id(), $settings['users'], true ) );
	}

	/**
	 * Load local UI assets only on the host page or an eligible visible toolbar.
	 * @param string $hook Optional WordPress admin screen hook suffix.
	 * @return void Enqueues scoped CSS and settings-only accessible dialog JS.
	 */
	public function assets( $hook = '' ) {
		$page = in_array( $hook, array( 'settings_page_bricks-quicknav', 'bricks_page_bricks-quicknav' ), true );
		if ( ! $page && ( ! $this->visible( Settings::get() ) || ! Builder::active() ) ) { return; }
		// Test packages can share the plugin version; changed CSS must still invalidate caches.
		$css_version = DDW_BXQN_VERSION . '-' . substr( hash_file( 'sha256', DDW_BXQN_DIR . '/assets/quicknav.css' ), 0, 10 );
		wp_enqueue_style( 'ddw-bxqn', plugins_url( 'assets/quicknav.css', DDW_BXQN_FILE ), array(), $css_version );
		if ( $page ) { wp_enqueue_script( 'ddw-bxqn-settings', plugins_url( 'assets/settings.js', DDW_BXQN_FILE ), array(), DDW_BXQN_VERSION, true ); wp_enqueue_script( 'ddw-bxqn-dialog', plugins_url( 'assets/dialog.js', DDW_BXQN_FILE ), array(), DDW_BXQN_VERSION, true ); }
	}

	/**
	 * Prepend the settings action in the website plugin list.
	 * @param array $links Existing action links.
	 * @return array Settings followed by existing actions when authorized.
	 */
	public function action_links( $links ) {
		if ( ! is_network_admin() && current_user_can( 'manage_options' ) ) { array_unshift( $links, '<a href="' . esc_url( self::url() ) . '">' . esc_html__( 'Settings', 'bricks-quicknav' ) . '</a>' ); }
		return $links;
	}

	/**
	 * Add neutral documentation and support links without personal URL parameters.
	 * @param array $links Existing metadata links.
	 * @param string $file Listed plugin basename.
	 * @return array Unchanged links or the augmented host row.
	 */
	public function row_meta( $links, $file ) {
		if ( plugin_basename( DDW_BXQN_FILE ) !== $file ) { return $links; }
		$links[] = '<a href="https://github.com/deckerweb/bricks-quicknav" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Documentation', 'bricks-quicknav' ) . '</a>';
		$links[] = '<a href="https://ko-fi.com/deckerweb" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Support this plugin', 'bricks-quicknav' ) . '</a>';
		$links[] = '<a href="https://eepurl.com/gbAUUn" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Newsletter', 'bricks-quicknav' ) . '</a>';
		return apply_filters( 'ddw-bxqn/pluginrow-meta', $links );
	}





	/**
	 * Report versions and effective configuration without private content.
	 * @param array $info Existing Site Health sections.
	 * @return array Host diagnostics appended to the existing sections.
	 */
	public function diagnostics( $info ) {
		$fields = array(
			'version' => array( 'label' => __( 'Version', 'bricks-quicknav' ), 'value' => DDW_BXQN_VERSION ),
			'builder' => array( 'label' => 'Bricks', 'value' => defined( 'BRICKS_VERSION' ) ? BRICKS_VERSION : __( 'Not active', 'bricks-quicknav' ) ),
			'library' => array( 'label' => __( 'Bundled Library', 'bricks-quicknav' ), 'value' => '1.1.0' ),
			'updater' => array( 'label' => 'deckerweb Updater', 'value' => Updates::$error ? __( 'Not loaded', 'bricks-quicknav' ) : '2.2.0' ),
			'overrides' => array( 'label' => __( 'Configuration overrides', 'bricks-quicknav' ), 'value' => implode( ', ', array_filter( Settings::constant_map(), 'defined' ) ) ?: __( 'None', 'bricks-quicknav' ) ),
		);
		$info['bricks-quicknav'] = array( 'label' => 'Bricks QuickNav', 'fields' => $fields );
		return $info;
	}

	/** @return void Renders the authorized settings page with local structured history. */
	public function page() {
		if ( ! current_user_can( 'manage_options' ) ) { wp_die( esc_html__( 'You do not have permission to manage these settings.', 'bricks-quicknav' ) ); }
		$s = Settings::get( false );
		echo '<div class="wrap bxqn-settings"><header class="bxqn-header"><img src="' . esc_url( add_query_arg( 'ver', substr( hash_file( 'sha256', DDW_BXQN_DIR . '/assets/icon.svg' ), 0, 10 ), plugins_url( 'assets/icon.svg', DDW_BXQN_FILE ) ) ) . '" width="52" height="52" alt=""><div><h1>Bricks QuickNav</h1><p>' . esc_html__( 'Fewer clicks. More Bricks.', 'bricks-quicknav' ) . '</p></div></header><hr class="wp-header-end">';
		// WordPress already renders Settings API notices for options-general.php pages.
		global $parent_file;
		if ( 'options-general.php' !== $parent_file ) { settings_errors(); }
		if ( ! Builder::active() ) { echo '<div class="notice notice-info inline"><p>' . esc_html__( 'Bricks is not active. QuickNav settings remain available; toolbar navigation resumes when a supported builder is active.', 'bricks-quicknav' ) . '</p></div>'; }
		if ( version_compare( PHP_VERSION, '8.1', '<' ) || Updates::$error ) { echo '<div class="notice notice-warning inline"><p>' . esc_html__( 'Navigation remains available. The bundled Library needs PHP 8.0; the updater integration needs PHP 8.1 and a compatible deckerweb Updater. Update manually while a component is unavailable.', 'bricks-quicknav' ) . '</p></div>'; }
		echo '<nav class="nav-tab-wrapper bxqn-tabs" aria-label="' . esc_attr__( 'Settings sections', 'bricks-quicknav' ) . '">';
		foreach ( array( 'toolbar' => __( 'Toolbar & Content', 'bricks-quicknav' ), 'addons' => __( 'Add-ons', 'bricks-quicknav' ), 'access' => __( 'Access & Data', 'bricks-quicknav' ), 'system' => __( 'System information', 'bricks-quicknav' ) ) as $id => $label ) { echo '<a class="nav-tab" id="bxqn-tab-' . esc_attr( $id ) . '" href="#bxqn-panel-' . esc_attr( $id ) . '">' . esc_html( $label ) . '</a>'; }
		echo '</nav><form id="bxqn-settings-form" method="post" action="options.php">';
		settings_fields( 'ddw_bxqn' );
		echo '<section class="bxqn-panel" id="bxqn-panel-toolbar" aria-labelledby="bxqn-tab-toolbar"><h2>' . esc_html__( 'Display & Content', 'bricks-quicknav' ) . '</h2><p>' . esc_html__( 'These defaults apply to this website. Personal toolbar preferences are available in your user profile.', 'bricks-quicknav' ) . '</p><table class="form-table" role="presentation">';
		$this->field( 'name', __( 'Toolbar label', 'bricks-quicknav' ), 'text', $s );
		$this->field( 'icon', __( 'Icon', 'bricks-quicknav' ), array( 'auto' => __( 'Bricks icon', 'bricks-quicknav' ), 'yellow' => __( 'Original yellow icon', 'bricks-quicknav' ), 'brick' => __( 'Brick icon', 'bricks-quicknav' ), 'none' => __( 'No icon', 'bricks-quicknav' ) ), $s );
		foreach ( array( 'backend' => __( 'Show in the Admin Dashboard', 'bricks-quicknav' ), 'frontend' => __( 'Show on the website', 'bricks-quicknav' ), 'footer' => __( 'Show Links & About', 'bricks-quicknav' ) ) as $key => $label ) { $this->field( $key, $label, 'checkbox', $s ); }
		echo '</table><h3>' . esc_html__( 'Content lists', 'bricks-quicknav' ) . '</h3><table class="form-table" role="presentation">';
		$this->field( 'compact', __( 'Compact menu', 'bricks-quicknav' ), 'checkbox', $s );
		$this->field( 'position', __( 'Toolbar position', 'bricks-quicknav' ), 'number', $s );
		$this->field( 'limit', __( 'Items per group', 'bricks-quicknav' ), 'number', $s );
		$this->field( 'orderby', __( 'Sort by', 'bricks-quicknav' ), array( 'modified' => __( 'Recently modified', 'bricks-quicknav' ), 'title' => __( 'Title', 'bricks-quicknav' ) ), $s );
		$this->field( 'drafts', __( 'Include unpublished content you can edit', 'bricks-quicknav' ), 'checkbox', $s );
		echo '<tr><th scope="row" id="bxqn-content-types-label">' . esc_html__( 'Content types', 'bricks-quicknav' ) . '</th><td><fieldset class="bxqn-choices" aria-labelledby="bxqn-content-types-label"><input type="hidden" name="ddw_bxqn_settings[post_types][]" value="">';
		foreach ( Builder::content_types() as $type => $object ) { $this->choice( 'post_types', $type, $object->labels->name, $s['post_types'] ); }
		echo '</fieldset></td></tr></table><h3>' . esc_html__( 'Navigation groups', 'bricks-quicknav' ) . '</h3><table class="form-table" role="presentation"><tr><th scope="row" id="bxqn-menu-groups-label">' . esc_html__( 'Menu groups', 'bricks-quicknav' ) . '</th><td><fieldset class="bxqn-choices" aria-labelledby="bxqn-menu-groups-label"><input type="hidden" name="ddw_bxqn_settings[groups][]" value="">';
		foreach ( array( 'content' => __( 'Content', 'bricks-quicknav' ), 'templates' => __( 'Templates', 'bricks-quicknav' ), 'types' => __( 'Template types', 'bricks-quicknav' ), 'taxonomies' => __( 'Template bundles and tags', 'bricks-quicknav' ), 'code' => __( 'Code', 'bricks-quicknav' ), 'frameworks' => __( 'Frameworks', 'bricks-quicknav' ), 'settings' => __( 'Bricks Settings', 'bricks-quicknav' ), 'addons' => __( 'Add-ons', 'bricks-quicknav' ) ) as $key => $label ) { $this->choice( 'groups', $key, $label, $s['groups'] ); }
		echo '<p class="description">' . esc_html__( 'Template-type lists use the same bounded template selection as the main template list. Increase the item limit to include more templates.', 'bricks-quicknav' ) . '</p>';

		echo '<p class="description">' . esc_html__( 'Only groups available in the active builder are displayed.', 'bricks-quicknav' ) . '</p></fieldset></td></tr></table>';
		if ( is_child_theme() ) {
			$id = Snn::active() ? 'snnbrx' : 'child-theme';
			echo '<h3>' . esc_html__( 'Child Theme', 'bricks-quicknav' ) . '</h3><p>' . esc_html( Snn::active() ? Snn::label() : wp_get_theme()->get( 'Name' ) ) . '</p><input type="hidden" name="ddw_bxqn_settings[integrations][' . esc_attr( $id ) . ']" value="0"><label><input type="checkbox" name="ddw_bxqn_settings[integrations][' . esc_attr( $id ) . ']" value="1"' . checked( Integrations::enabled( $id, $s ), true, false ) . '> ' . esc_html__( 'Show child theme navigation', 'bricks-quicknav' ) . '</label><p class="description">' . esc_html__( 'Normal child themes open functions.php when the file editor is permitted. SNN-BRX opens its active Code Snippets feature, otherwise its settings. Each destination requires its own permissions.', 'bricks-quicknav' ) . '</p>';
		}
		echo '</section>';
		echo '<section class="bxqn-panel" id="bxqn-panel-addons" aria-labelledby="bxqn-tab-addons">';
		$this->integration_settings( $s );
		echo '</section>';
		echo '<section class="bxqn-panel" id="bxqn-panel-access" aria-labelledby="bxqn-tab-access"><h2>' . esc_html__( 'Access', 'bricks-quicknav' ) . '</h2><table class="form-table" role="presentation">';
		$this->field( 'capability', __( 'Required toolbar capability', 'bricks-quicknav' ), 'text', $s );
		$this->field( 'users', __( 'Restrict to user IDs', 'bricks-quicknav' ), 'text', $s );
		echo '</table><p>' . esc_html__( 'Leave user IDs empty to allow all eligible users. Separate IDs with commas. WordPress and Bricks permissions always apply to each destination.', 'bricks-quicknav' ) . '</p><h2>' . esc_html__( 'Data', 'bricks-quicknav' ) . '</h2><table class="form-table" role="presentation">';
		$this->field( 'delete_data', __( 'Delete QuickNav settings when uninstalling', 'bricks-quicknav' ), 'checkbox', $s );
		echo '</table><p>' . esc_html__( 'Disabled by default. When enabled, uninstall removes this website’s QuickNav settings and personal QuickNav preferences. Bricks content and other plugins’ data are always retained. Network uninstall applies each website’s own choice.', 'bricks-quicknav' ) . '</p>';
		echo '</section>';
		echo '<section class="bxqn-panel" id="bxqn-panel-system" aria-labelledby="bxqn-tab-system"><h2>' . esc_html__( 'Detected components', 'bricks-quicknav' ) . '</h2><dl class="bxqn-diagnostics">';
		foreach ( $this->diagnostics( array() )['bricks-quicknav']['fields'] as $field ) { echo '<dt>' . esc_html( $field['label'] ) . '</dt><dd>' . esc_html( $field['value'] ) . '</dd>'; }
		echo '</dl></section><div class="bxqn-savebar"><span id="bxqn-unsaved-status" role="status" aria-live="polite" data-saved="' . esc_attr__( 'All changes saved', 'bricks-quicknav' ) . '" data-dirty="' . esc_attr__( 'Unsaved changes', 'bricks-quicknav' ) . '">' . esc_html__( 'Save changes to apply your settings.', 'bricks-quicknav' ) . '</span>';
		submit_button( __( 'Save Changes', 'bricks-quicknav' ), 'primary', 'submit', false );
		echo '</div></form><footer class="bxqn-footer" aria-label="' . esc_attr__( 'Plugin information', 'bricks-quicknav' ) . '"><div><strong>Bricks QuickNav</strong> <span>' . esc_html__( 'Version', 'bricks-quicknav' ) . ' ' . esc_html( DDW_BXQN_VERSION ) . '</span> · <a href="' . esc_url( 'https://github.com/deckerweb/bricks-quicknav/wiki/' . ( 0 === strpos( determine_locale(), 'de' ) ? 'Aenderungsverlauf' : 'Changelog' ) ) . '" data-bxqn-open="bxqn-history">' . esc_html__( 'Changelog', 'bricks-quicknav' ) . '</a> · <a href="https://github.com/deckerweb/bricks-quicknav">' . esc_html__( 'Documentation', 'bricks-quicknav' ) . '</a><p>' . esc_html__( 'Fewer clicks. More Bricks.', 'bricks-quicknav' ) . '</p></div><div><span>© 2025–2026 <a href="https://github.com/deckerweb" target="_blank" rel="noopener noreferrer">David Decker – DECKERWEB</a></span><a href="https://github.com/deckerweb/bricks-quicknav" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Plugin website', 'bricks-quicknav' ) . '</a></div></footer>';
		echo '<dialog hidden id="bxqn-history" aria-labelledby="bxqn-history-title"><form method="dialog"><button type="submit" class="button bxqn-close" data-bxqn-close>' . esc_html__( 'Close', 'bricks-quicknav' ) . '</button></form><h2 id="bxqn-history-title">' . esc_html__( 'Changelog', 'bricks-quicknav' ) . '</h2>';
		$history = require DDW_BXQN_DIR . '/includes/plugin-history.php';
		echo wp_kses_post( $history( 0 === strpos( determine_locale(), 'de' ) ) ); // All source values escaped in the renderer.
		echo '</dialog></div>';
	}

	/** @param array $settings Website settings. @return void Displays addon availability and site-scoped navigation switches. */
	private function integration_settings( $settings ) {
		echo '<h2 id="bxqn-integrations">' . esc_html__( 'Third-party integrations', 'bricks-quicknav' ) . '</h2><p>' . esc_html__( 'Choose which addon shortcuts QuickNav displays on this website. These switches do not activate, deactivate or configure the addons themselves. Missing addons never produce toolbar links.', 'bricks-quicknav' ) . '</p><table class="form-table" role="presentation">';
		$this->field( 'integration_children', __( 'Show addon submenus', 'bricks-quicknav' ), 'checkbox', $settings );
		echo '</table><div class="bxqn-integration-list">';
		$groups = array( 'installed' => array(), 'missing' => array(), 'pending' => array() );
		foreach ( Integrations::rows() as $id => $row ) { if ( 'snnbrx' === $id ) { continue; } $groups[ $row['pending'] ? 'pending' : ( 'missing' === $row['status'] ? 'missing' : 'installed' ) ][ $id ] = $row; }
		echo '<h3>' . esc_html__( 'Installed add-ons', 'bricks-quicknav' ) . '</h3>';
		$this->integration_rows( $groups['installed'], $settings );
		foreach ( array( 'missing' => __( 'Not installed', 'bricks-quicknav' ), 'pending' => __( 'Package verification pending', 'bricks-quicknav' ) ) as $group => $label ) {
			if ( ! $groups[ $group ] ) { continue; }
			echo '<details class="bxqn-addon-details"><summary>' . esc_html( $label ) . ' (' . count( $groups[ $group ] ) . ')</summary>';
			$this->integration_rows( $groups[ $group ], $settings );
			echo '</details>';
		}
		echo '</div>';
	}

	/** @param array $rows Discovery rows. @param array $settings Website settings. @return void Outputs escaped addon controls. */
	private function integration_rows( $rows, $settings ) {
		foreach ( $rows as $id => $row ) {
			$control = 'bxqn-integration-' . $id;
			echo '<div class="bxqn-integration" role="group" aria-labelledby="bxqn-addon-label-' . esc_attr( $id ) . '"><div><strong id="bxqn-addon-label-' . esc_attr( $id ) . '">' . esc_html( $row['label'] ) . '</strong>';
			if ( $row['version'] ) { echo ' <span>' . esc_html( $row['version'] ) . '</span>'; }
			echo '<p class="description">' . esc_html( Integrations::status_label( $row['status'] ) ) . '</p>';
			if ( ! $row['pending'] && 'no-menu' !== $row['status'] ) {
				echo '<input type="hidden" name="ddw_bxqn_settings[integrations][' . esc_attr( $id ) . ']" value="0"><label for="' . esc_attr( $control ) . '"><input type="checkbox" id="' . esc_attr( $control ) . '" name="ddw_bxqn_settings[integrations][' . esc_attr( $id ) . ']" value="1"' . checked( Integrations::enabled( $id, $settings ), true, false ) . '> ' . esc_html__( 'Show QuickNav links for this addon', 'bricks-quicknav' ) . '</label>';
			}
			if ( ! $row['pending'] && 'no-menu' !== $row['status'] && ! in_array( $id, array( 'ai', 'migration-mode' ), true ) ) {
				echo '<input type="hidden" name="ddw_bxqn_settings[integration_direct][' . esc_attr( $id ) . ']" value="0"><label for="bxqn-direct-' . esc_attr( $id ) . '"><input type="checkbox" id="bxqn-direct-' . esc_attr( $id ) . '" name="ddw_bxqn_settings[integration_direct][' . esc_attr( $id ) . ']" value="1"' . checked( ! empty( $settings['integration_direct'][ $id ] ), true, false ) . '> ' . esc_html__( 'Show directly in the main QuickNav menu', 'bricks-quicknav' ) . '</label>';
			}
			echo '</div><div>';
			if ( $row['legacy'] ) { echo '<span class="description">' . esc_html__( 'Existing integration retained', 'bricks-quicknav' ) . '</span>'; }
			if ( $row['links'] ) { echo '<p><a href="' . esc_url( $row['links'][0]['url'] ) . '">' . esc_html__( 'Open addon admin page', 'bricks-quicknav' ) . '</a></p>'; }
			echo '</div></div>';
		}
	}

	/**
	 * Render an accessible native setting control with visible constant overrides.
	 * @param string $key Setting key.
	 * @param string $label Translated field label.
	 * @param string|array $kind Control type or choices.
	 * @param array $settings Effective website settings.
	 * @return void Outputs an escaped form row.
	 */
	private function field( $key, $label, $kind, $settings ) {
		$constant = Settings::constant_map()[ $key ] ?? '';
		$locked = $constant && defined( $constant );
		$id = 'bxqn-' . $key;
		$name = Settings::OPTION . '[' . $key . ']';
		$value = 'users' === $key ? implode( ', ', $settings[ $key ] ) : $settings[ $key ];
		echo '<tr><th scope="row"><label for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label></th><td>';
		if ( is_array( $kind ) ) {
			echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '"' . disabled( $locked, true, false ) . '>';
			foreach ( $kind as $option => $text ) { echo '<option value="' . esc_attr( $option ) . '"' . selected( $value, $option, false ) . '>' . esc_html( $text ) . '</option>'; }
			echo '</select>';
		} elseif ( 'checkbox' === $kind ) {
			echo '<input type="checkbox" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="1"' . checked( $value, true, false ) . disabled( $locked, true, false ) . '>';
		} else { echo '<input class="regular-text" type="' . esc_attr( $kind ) . '" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '"' . ( 'number' === $kind ? ( 'position' === $key ? ' min="1" max="9999"' : ' min="1" max="100"' ) : '' ) . disabled( $locked, true, false ) . '>'; }
		if ( $locked ) { echo '<p class="description">' . esc_html__( 'Set by configuration:', 'bricks-quicknav' ) . ' <code>' . esc_html( $constant ) . '</code></p>'; }
		echo '</td></tr>';
	}

	/**
	 * Render a selection in a multi-choice setting.
	 * @param string $key Setting key.
	 * @param string $value Choice value.
	 * @param string $label Choice label.
	 * @param array $selected Selected choice values.
	 * @return void Outputs one escaped checkbox label.
	 */
	private function choice( $key, $value, $label, $selected ) {
		echo '<label class="bxqn-choice"><input type="checkbox" name="' . esc_attr( Settings::OPTION . '[' . $key . '][]' ) . '" value="' . esc_attr( $value ) . '"' . checked( in_array( $value, $selected, true ), true, false ) . '><span>' . esc_html( $label ) . '</span></label>';
	}

	/**
	 * Render personal, website-scoped toolbar preferences on a user profile.
	 * @param \WP_User $user Profile user.
	 * @return void Outputs controls; personal settings cannot expand site permissions.
	 */
	public function profile( $user ) {
		if ( is_network_admin() || is_user_admin() || ! current_user_can( 'edit_user', $user->ID ) ) { return; }
		$prefs = get_user_option( Settings::USER_OPTION, $user->ID );
		$prefs = is_array( $prefs ) ? $prefs : array();
		echo '<h2>Bricks QuickNav</h2><p>' . esc_html__( 'Personal preferences for this website. Website permissions and configuration overrides still apply.', 'bricks-quicknav' ) . '</p>';
		wp_nonce_field( 'bxqn-profile-' . $user->ID, 'bxqn_profile_nonce' );
		echo '<table class="form-table" role="presentation">';
		foreach ( array( 'backend' => __( 'Admin Dashboard toolbar', 'bricks-quicknav' ), 'frontend' => __( 'Website toolbar', 'bricks-quicknav' ) ) as $key => $label ) {
			echo '<tr><th><label for="bxqn-personal-' . esc_attr( $key ) . '">' . esc_html( $label ) . '</label></th><td><select id="bxqn-personal-' . esc_attr( $key ) . '" name="bxqn_preferences[' . esc_attr( $key ) . ']"><option value="inherit">' . esc_html__( 'Use website default', 'bricks-quicknav' ) . '</option><option value="hide"' . selected( $prefs[ $key ] ?? '', 'hide', false ) . '>' . esc_html__( 'Hide for me', 'bricks-quicknav' ) . '</option></select></td></tr>';
		}
		echo '<tr><th><label for="bxqn-personal-limit">' . esc_html__( 'Items per group', 'bricks-quicknav' ) . '</label></th><td><input type="number" min="0" max="100" id="bxqn-personal-limit" name="bxqn_preferences[limit]" value="' . esc_attr( $prefs['limit'] ?? 0 ) . '"><p class="description">' . esc_html__( '0 uses the website default.', 'bricks-quicknav' ) . '</p></td></tr></table>';
	}

	/**
	 * Save authorized, nonce-verified personal preferences for this website only.
	 * @param int $id Profile user ID.
	 * @return void Saves validated values or leaves existing preferences untouched.
	 */
	public function save_profile( $id ) {
		if ( is_network_admin() || is_user_admin() || ! current_user_can( 'edit_user', $id ) || ! isset( $_POST['bxqn_profile_nonce'] ) || ! is_string( $_POST['bxqn_profile_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bxqn_profile_nonce'] ) ), 'bxqn-profile-' . $id ) ) { return; }
		$input = isset( $_POST['bxqn_preferences'] ) && is_array( $_POST['bxqn_preferences'] ) ? map_deep( wp_unslash( $_POST['bxqn_preferences'] ), 'sanitize_text_field' ) : array();
		$prefs = array( 'limit' => is_scalar( $input['limit'] ?? null ) ? min( 100, absint( $input['limit'] ) ) : 0 );
		foreach ( array( 'backend', 'frontend' ) as $key ) { $prefs[ $key ] = 'hide' === ( $input[ $key ] ?? '' ) ? 'hide' : 'inherit'; }
		update_user_option( $id, Settings::USER_OPTION, $prefs, false );
	}
}
