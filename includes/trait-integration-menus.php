<?php
/** Preserved Bricks QuickNav navigation definitions. @package BricksQuickNav */
namespace Deckerweb\BricksQuickNav;
defined( 'ABSPATH' ) || exit;

/** Defines IntegrationMenus navigation without changing editor layout. */
trait IntegrationMenus {
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
		
		/**
		
		 * Customize navigation definitions; return the modified array.
		
		 * @since 1.0.0
		
		 * @param array $plugins Existing navigation definitions.
		
		 */
		
		$filtered = apply_filters( 'ddw-bxqn/snippet-plugins', $plugins );
		$plugins = is_array( $filtered ) ? array_filter( $filtered, static function ( $row ) { return is_array( $row ) && isset( $row['is-active'], $row['capability'], $row['label'], $row['admin-slug'] ) && is_string( $row['capability'] ) && is_string( $row['label'] ) && is_string( $row['admin-slug'] ); } ) : $plugins;
		
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
		return Snn::active();
	}

	/**
	 * Add group node for Custom Code.
	 * @return void Adds the native code group to the staged collection.
	 *
	 * @since 1.0.0
	 * @param NodeCollection $wp_admin_bar Staged toolbar definitions.
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
	 * @param NodeCollection $wp_admin_bar Staged toolbar definitions.
	 * @return void Adds navigation definitions to the staged collection.
	 */
	private function add_customcode_submenu( $wp_admin_bar ) {
		
		/** If a supported snippets manager plugin is active */
		$snippet_plugins = $this->get_snippets_plugins();

		if ( $snippet_plugins ) {
			foreach ( $snippet_plugins as $snippet_id => $snippet_plugin ) {
				if ( $snippet_plugin[ 'is-active' ] && current_user_can( $snippet_plugin[ 'capability' ] ) ) {
					$id_hook_code_plugins = 'bxqn-snippet-' . sanitize_key( $snippet_id );
					$wp_admin_bar->add_node( [
						'id'     => $id_hook_code_plugins,
						'title'  => $this->get_icon( 'snippets' ) . esc_html( $snippet_plugin[ 'label' ] ),
						'href'   => esc_url( admin_url( $snippet_plugin[ 'admin-slug' ] ) ),
						'parent' => 'bxqn-group-customcode',
						'meta'   => [ 'class' => 'has-icon' ],
					] );
					
					$wp_admin_bar->add_node( [
						'id'     => $id_hook_code_plugins . '-all',
						'title'  => esc_html__( 'All Snippets', 'bricks-quicknav' ),
						'href'   => esc_url( admin_url( $snippet_plugin[ 'admin-slug' ] ) ),
						'parent' => $id_hook_code_plugins,
					] );
					
					if ( ! empty( $snippet_plugin[ 'add-new' ] ) ) {
						$wp_admin_bar->add_node( [
							'id'     => $id_hook_code_plugins . '-new',
							'title'  => esc_html__( 'New Snippet', 'bricks-quicknav' ),
							'href'   => esc_url( admin_url( $snippet_plugin[ 'add-new' ] ) ),
							'parent' => $id_hook_code_plugins,
						] );
					}  // end if
				}  // end if
			}  // end foreach
		}  // end if
		
		/** Bricks: Custom Code specific stuff */
		$id_custom_code_main = 'bxqn-bricks-customcode';
		$wp_admin_bar->add_node( array(
			'id' => $id_custom_code_main, 'parent' => 'bxqn-group-customcode',
			'title' => esc_html__( 'Bricks: Custom Code', 'bricks-quicknav' ),
			'href' => admin_url( 'admin.php?page=bricks-settings#tab-custom-code' ),
		) );
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
	 * @param NodeCollection $wp_admin_bar Staged toolbar definitions.
	 * @return void Adds navigation definitions to the staged collection.
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
	 * @param NodeCollection $wp_admin_bar Staged toolbar definitions.
	 * @return void Adds navigation definitions to the staged collection.
	 */
	private function add_frameworks_submenu( $wp_admin_bar ) {
		
		/** Core Framework (free & Premium) */
		if ( defined( 'CORE_FRAMEWORK_VERSION' ) ) {
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-core-framework',
				'title'  => $this->get_icon( 'cf' ) . esc_html__( 'Core Framework', 'bricks-quicknav' ),
				'href'   => esc_url( admin_url( 'admin.php?page=core-framework' ) ),
				'parent' => 'bxqn-group-frameworks',
				'meta'   => [ 'class' => 'has-icon' ],
			] );
			
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-core-framework-settings',
				'title'  => esc_html__( 'Settings', 'bricks-quicknav' ),
				'href'   => esc_url( admin_url( 'admin.php?page=core-framework' ) ),
				'parent' => 'bxqn-core-framework',
			] );
			
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-core-framework-webapp',
				'title'  => $this->get_icon( 'links' ) . esc_html__( 'Web App', 'bricks-quicknav' ),
				'href'   => 'https://coreframework.com/app',
				'parent' => 'bxqn-core-framework',	//'bxqn-group-active-framework-resources',
				'meta'   => [ 'class' => 'has-icon', 'target' => '_blank', 'rel' => 'nofollow noopener noreferrer' ],
			] );
			
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-core-framework-docs',
				'title'  => $this->get_icon( 'links' ) . esc_html__( 'Documentation', 'bricks-quicknav' ),
				'href'   => 'https://docs.coreframework.com/builder-integrations/bricks-builder',
				'parent' => 'bxqn-core-framework',	//'bxqn-group-active-framework-resources',
				'meta'   => [ 'class' => 'has-icon', 'target' => '_blank', 'rel' => 'nofollow noopener noreferrer' ],
			] );
		}
		
		/** Automatic.CSS Framework (Premium) */
		if ( defined( 'ACSS_PLUGIN_FILE' ) ) {
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-automatic-css',
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
					'id'     => 'bxqn-automatic-css-' . $acss_id,
					'title'  => $this->get_node_data( $acss_info[ 'url' ], $acss_info[ 'title' ] )[ 'title' ],
					'href'   => $acss_info[ 'url' ],
					'parent' => 'bxqn-automatic-css',
					'meta'   => $this->get_node_data( $acss_info[ 'url' ] )[ 'meta' ],
				] );
			}  // end foreach
		}
		
		/** Frames (via Automatic.CSS) (Premium) */
		if ( defined( 'FRAMES_PLUGIN_FILE' ) ) {
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-frames',
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
					'id'     => 'bxqn-frames-' . $frs_id,
					'title'  => $this->get_node_data( $frs_info[ 'url' ], $frs_info[ 'title' ] )[ 'title' ],
					'href'   => $frs_info[ 'url' ],
					'parent' => 'bxqn-frames',
					'meta'   => $this->get_node_data( $frs_info[ 'url' ] )[ 'meta' ],
				] );
			}  // end foreach
		}  // end if
		
		/** WindPress (for Tailwind CSS in WordPress) (free & Premium) */
		if ( class_exists( '\WindPress\WindPress\Plugin' ) ) {
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-windpress',
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
					'id'     => 'bxqn-windpress-' . $wndpr_id,
					'title'  => $this->get_node_data( $wndpr_info[ 'url' ], $wndpr_info[ 'title' ] )[ 'title' ],
					'href'   => $wndpr_info[ 'url' ],
					'parent' => 'bxqn-windpress',
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
				'id'     => 'bxqn-brixies',
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
					'id'     => 'bxqn-brixies-' . $brix_id,
					'title'  => $this->get_node_data( $brix_info[ 'url' ], $brix_info[ 'title' ] )[ 'title' ],
					'href'   => $brix_info[ 'url' ],
					'parent' => 'bxqn-brixies',
					'meta'   => $this->get_node_data( $brix_info[ 'url' ] )[ 'meta' ],
				] );
			}  // end foreach
		}  // end if
	}

	/**
	 * Add group node for plugin support.
	 *
	 * @since 1.0.0
	 * @param NodeCollection $wp_admin_bar Staged toolbar definitions.
	 * @return void Adds navigation definitions to the staged collection.
	 */
	private function add_plugin_support_group( $wp_admin_bar ) {
		ChildTheme::add( $wp_admin_bar );
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
	 * @param NodeCollection $wp_admin_bar Staged toolbar definitions.
	 * @return void Adds navigation definitions to the staged collection.
	 */
	private function maybe_add_plugin_submenus( $wp_admin_bar ) {
		


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
				'bundles'            => [ 'title' => __( 'Blocks', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=gutenbricks&tab=bundles' ) ) ],
				'gutenberg-settings' => [ 'title' => __( 'Gutenberg Settings', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=gutenbricks&tab=gutenberg-settings' ) ) ],
				'client-experience'  => [ 'title' => __( 'Client Experience', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=gutenbricks&tab=client-experience' ) ) ],
				'integration'        => [ 'title' => __( 'Integrations', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=gutenbricks&tab=integration' ) ) ],
				'license'            => [ 'title' => __( 'License', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=gutenbricks&tab=license' ) ) ],
				'docs'               => [ 'title' => __( 'Documentation', 'bricks-quicknav' ), 'url' => 'https://docs.gutenbricks.com/' ],
				'fbgroup'            => [ 'title' => __( 'Facebook Group', 'bricks-quicknav' ), 'url' => 'https://www.facebook.com/groups/wiredwp' ],
			];
			
			foreach ( $gutenbricks_tabs as $gb_id => $gb_info ) {
				$wp_admin_bar->add_node( [
					'id'     => 'bxqn-gutenbricks-' . $gb_id,
					'title'  => $this->get_node_data( $gb_info[ 'url' ], $gb_info[ 'title' ] )[ 'title' ],
					'href'   => $gb_info[ 'url' ],
					'parent' => 'bxqn-gutenbricks',
					'meta'   => $this->get_node_data( $gb_info[ 'url' ] )[ 'meta' ],
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
				'elements'   => [ 'title' => __( 'Elements', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=bricksforge#/elements' ) ) ],
				'extensions' => [ 'title' => __( 'Extensions', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=bricksforge#/tools' ) ) ],
				'license'    => [ 'title' => __( 'License', 'bricks-quicknav' ), 'url' => esc_url( admin_url( 'admin.php?page=bricksforge#/license' ) ) ],
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
			if ( is_array( $brf_features ) ) {
				if ( in_array( 4, $brf_features ) ) $bricksforge_features[ 'maintenance' ] = __( 'Maintenance', 'bricks-quicknav' );
				if ( in_array( 9, $brf_features ) ) $bricksforge_features[ 'backendDesigner' ] = __( 'Backend Designer', 'bricks-quicknav' );
				if ( in_array( 11, $brf_features ) ) $bricksforge_features[ 'formSubmissions' ] = __( 'Form Submissions', 'bricks-quicknav' );
				if ( in_array( 13, $brf_features ) ) $bricksforge_features[ 'emailDesigner' ] = __( 'Email Designer', 'bricks-quicknav' );
				if ( in_array( 18, $brf_features ) ) $bricksforge_features[ 'apiQueryBuilder' ] = __( 'API Query Builder', 'bricks-quicknav' );
				if ( in_array( 16, $brf_features ) ) $bricksforge_features[ 'pageTransitions' ] = __( 'Page Transitions', 'bricks-quicknav' );
				if ( in_array( 17, $brf_features ) ) $bricksforge_features[ 'adminPages' ] = __( 'Admin Pages', 'bricks-quicknav' );
			}  // end if
			
			foreach ( $bricksforge_features as $brfeat_tab => $brfeat_tab_label ) {
				$brfeat_url = ( 'formSubmissions' === $brfeat_tab ) ? esc_url( admin_url( 'admin.php?page=brf-form-submissions#/' ) ) : esc_url( admin_url( 'admin.php?page=bricksforge#/' . $brfeat_tab ) );
				
				$wp_admin_bar->add_node( [
					'id'     => 'bxqn-bricksforge-extensions-' . $brfeat_tab,
					'title'  => esc_html( $brfeat_tab_label ),
					'href'   => $brfeat_url,	// esc_url( admin_url( 'admin.php?page=bricksforge#/' . $brfeat_tab ) ),
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
		
		/** Plugin: Flex Addons (free??) - currently only free alpha/dev version supported */
		if ( function_exists( 'bfa_add_menu_pages' ) ) {
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-flexaddons',
				'title'  => $this->get_icon( 'plugin' ) . 'Flex Addons',
				'href'   => esc_url( admin_url( 'admin.php?page=flex-addons' ) ),
				'parent' => 'bxqn-group-plugins',
				'meta'   => [ 'class' => 'has-icon' ],
			] );
			
			$wp_admin_bar->add_node( [
				'id'     => 'bxqn-flexaddons-settings',
				'title'  => esc_html__( 'Settings', 'bricks-quicknav' ),
				'href'   => esc_url( admin_url( 'admin.php?page=flex-addons-settings' ) ),
				'parent' => 'bxqn-flexaddons',
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
}
