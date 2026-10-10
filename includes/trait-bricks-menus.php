<?php
/** Preserved Bricks QuickNav navigation definitions. @package BricksQuickNav */
namespace Deckerweb\BricksQuickNav;
defined( 'ABSPATH' ) || exit;

/** Defines BricksMenus navigation without changing editor layout. */
trait BricksMenus {
	/**
	 * Add group node for actions & settings.
	 *
	 * @since 1.0.0
	 * @param NodeCollection $wp_admin_bar Staged toolbar definitions.
	 * @return void Adds navigation definitions to the staged collection.
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
	 * @param NodeCollection $wp_admin_bar Staged toolbar definitions.
	 * @return void Adds navigation definitions to the staged collection.
	 */
	private function add_actions_submenu( $wp_admin_bar ) {
		
		$mode = class_exists( '\Bricks\Maintenance' ) ? \Bricks\Maintenance::get_mode() : FALSE;		
		
		$mode_setting = $mode ? ( get_option( 'bricks_global_settings', array() )['maintenanceMode'] ?? 'not-set' ) : 'not-set';
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
				'href'   => esc_url( Builder::edit_url( absint( $template ) ) ),
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
				'href'   => esc_url( admin_url( 'edit.php?post_type=bricks_fonts' ) ),
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
	 * @param NodeCollection $wp_admin_bar Staged toolbar definitions.
	 * @return void Adds navigation definitions to the staged collection.
	 */
	private function add_settings_submenu( $wp_admin_bar ) {
		if ( version_compare( BRICKS_VERSION, '2.4', '>=' ) ) {
			$wp_admin_bar->add_node( array( 'id' => 'bxqn-ai', 'parent' => 'bxqn-settings', 'title' => esc_html__( 'Bricks AI', 'bricks-quicknav' ), 'href' => admin_url( 'admin.php?page=bricks-ai' ) ) );
		}
		
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
		/**
		 * Customize navigation definitions; return the modified array.
		 * @since 1.0.0
		 * @param array $settings_submenus Existing navigation definitions.
		 */
		$settings_submenus = apply_filters( 'ddw-bxqn/settings-links', $settings_submenus );
		if ( ! is_array( $settings_submenus ) ) { $settings_submenus = array(); }
		
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
}
