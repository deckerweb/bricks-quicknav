<?php
/** Preserved Bricks QuickNav navigation definitions. @package BricksQuickNav */
namespace Deckerweb\BricksQuickNav;
defined( 'ABSPATH' ) || exit;

/** Defines Resources navigation without changing editor layout. */
trait Resources {
	/**
	 * Add group node for footer items (Links & About)
	 *
	 * @since 1.0.0
	 * @param NodeCollection $wp_admin_bar Staged toolbar definitions.
	 * @return void Adds navigation definitions to the staged collection.
	 */
	private function add_footer_group( $wp_admin_bar ) {
		
		if ( ! Settings::get()['footer'] ) return;
		
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
	 * @param NodeCollection $wp_admin_bar Staged toolbar definitions.
	 * @return void Adds navigation definitions to the staged collection.
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
		
		/**
		
		 * Customize navigation definitions; return the modified array.
		
		 * @since 1.0.0
		
		 * @param array $links Existing navigation definitions.
		
		 */
		
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

		/**

		 * Customize navigation definitions; return the modified array.

		 * @since 1.0.0

		 * @param array $resources Existing navigation definitions.

		 */

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
	 * @param NodeCollection $wp_admin_bar Staged toolbar definitions.
	 * @return void Adds navigation definitions to the staged collection.
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
		
		/**
		
		 * Customize navigation definitions; return the modified array.
		
		 * @since 1.0.0
		
		 * @param array $links Existing navigation definitions.
		
		 */
		
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
	 * @param NodeCollection $wp_admin_bar Staged toolbar definitions.
	 * @return void Adds navigation definitions to the staged collection.
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
}
