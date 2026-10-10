<?php
/** Toolbar composition with preserved menu IDs. @package BricksQuickNav */
namespace Deckerweb\BricksQuickNav;
defined( 'ABSPATH' ) || exit;

/** Builds bounded content lists and retains existing integration/resource definitions. */
final class Toolbar {
	use Icons, Resources, IntegrationMenus, BricksMenus;
	/** @var int Request-local count of detected snippet plugins. */
	private static $has_snippets_plugin = 0;
	/** @var string Metadata used in preserved about links. */
	private static $version = '2.0.0';
	/** @var string Public plugin name. */
	private static $name = 'Bricks QuickNav';
	/** @var string Public plugin repository. */
	private static $plugin_url = 'https://github.com/deckerweb/bricks-quicknav';
	/** @var string Author website. */
	private static $author_url = 'https://deckerweb.de/';
	/** @var string Public repository. */
	private static $github_url = 'https://github.com/deckerweb/bricks-quicknav';

	/** @return void Registers navigation once at the configured priority. */
	public static function boot() {
		static $instance;
		if ( $instance ) { return; }
		$instance = new self();
		add_action( 'admin_bar_menu', array( $instance, 'render' ), Settings::get()['position'] );
	}

	/**
	 * Evaluate host visibility without treating it as a destination permission.
	 * @param array $settings Effective settings.
	 * @return bool Whether the current context may show QuickNav.
	 */
	public static function visible( $settings ) {
		return Builder::active() && ! is_network_admin() && ! is_user_admin() && is_user_logged_in()
			&& is_admin_bar_showing() && ! empty( $settings[ is_admin() ? 'backend' : 'frontend' ] )
			&& current_user_can( $settings['capability'] )
			&& ( empty( $settings['users'] ) || in_array( get_current_user_id(), $settings['users'], true ) );
	}

	/** @return bool Whether reduced native navigation is enabled. */
	private function is_compact_mode(): bool { return (bool) Settings::get()['compact']; }
	/** @return bool Whether the existing MA fonts integration owns font management. */
	private function has_snippet_ma_customfonts(): bool { return class_exists( 'MA_CustomFonts' ); }

	/**
	 * Delegate preserved font queries to the bounded API adapter.
	 * @param string $type Registered post type.
	 * @return array Authorized content objects.
	 */
	private function get_bricks_template_type( $type ) { return Builder::posts( $type, Settings::get() ); }

	/**
	 * Collect existing addon definitions without rendering or remote requests.
	 * @param bool $content Whether to include authorized SNN snippet content.
	 * @return NodeCollection Detected code, framework, and addon nodes.
	 */
	public function integration_nodes( $content = false ) {
		$nodes = new NodeCollection();
		$this->get_snippets_plugins();
		$this->add_customcode_group( $nodes );
		$this->add_framework_group( $nodes );
		$this->add_plugin_support_group( $nodes );
		PublicAddons::add( $nodes );
		PremiumTargets::configure( $nodes );
		AddonRegistry::add( $nodes );
		if ( $content ) { Snn::content( $nodes ); }
		return $nodes;
	}

	/**
	 * Compose navigation while leaving native toolbar visibility unchanged.
	 * @param \WP_Admin_Bar $bar WordPress toolbar instance.
	 * @return void Adds eligible navigation only.
	 */
	public function render( $bar ) {
		$s = Settings::get();
		if ( ! self::visible( $s ) || class_exists( 'DDW_Bricks_QuickNav' ) ) { return; }
		$nodes = new NodeCollection();
		$icon = 'none' === $s['icon'] ? '' : $this->get_icon( 'yellow' === $s['icon'] ? 'bricks-yellow' : ( in_array( $s['icon'], array( 'brick', 'bricks' ), true ) ? 'bricks-brick' : 'bricks-b' ) );
		$nodes->add_node( array( 'id' => 'ddw-bricks-quicknav', 'title' => $icon . '<span class="ab-label">' . esc_html( $s['name'] ) . '</span>', 'href' => current_user_can( 'manage_options' ) ? Plugin::url() : '#', 'meta' => array( 'class' => 'has-icon', 'menu_title' => 'Bricks QuickNav' ) ) );
		$nodes->add_group( array( 'id' => 'bxqn-group-templates', 'parent' => 'ddw-bricks-quicknav' ) );
		$nodes->add_group( array( 'id' => 'tfqn-group-hookplace', 'parent' => 'ddw-bricks-quicknav' ) );
		if ( in_array( 'content', $s['groups'], true ) ) {
			foreach ( array_intersect_key( Builder::content_types(), array_flip( $s['post_types'] ) ) as $type => $object ) {
				$this->content( $nodes, $type, 'page' === $type ? 'bxqn-pages' : 'bxqn-content-' . $type, 'page' === $type ? __( 'Pages (Bricks)', 'bricks-quicknav' ) : $object->labels->name, $s );
			}
		}
		if ( in_array( 'templates', $s['groups'], true ) ) { $this->content( $nodes, 'bricks_template', 'bxqn-templates', __( 'Templates', 'bricks-quicknav' ), $s ); }
		if ( in_array( 'types', $s['groups'], true ) && post_type_exists( 'bricks_template' ) && current_user_can( get_post_type_object( 'bricks_template' )->cap->edit_posts ) ) {
			$nodes->add_node( array( 'id' => 'bxqn-types', 'parent' => 'bxqn-group-templates', 'title' => esc_html__( 'All Template Types', 'bricks-quicknav' ), 'href' => admin_url( 'edit.php?post_type=bricks_template' ) ) );
			$templates = Builder::posts( 'bricks_template', $s );
			foreach ( Builder::template_types() as $type => $label ) {
				$id = 'bxqn-type-' . sanitize_key( $type );
				$nodes->add_node( array( 'id' => $id, 'parent' => 'bxqn-types', 'title' => esc_html( $label ), 'href' => add_query_arg( array( 'post_type' => 'bricks_template', 'template_type' => $type ), admin_url( 'edit.php' ) ) ) );
				$items = array_filter( $templates, static function ( $post ) use ( $type ) { return get_post_meta( $post->ID, '_bricks_template_type', true ) === $type; } );
				$this->post_groups( $nodes, $items, $id, $id . '-' );
			}
		}
		if ( in_array( 'taxonomies', $s['groups'], true ) ) {
			foreach ( array( 'template_bundle' => __( 'Template Bundles', 'bricks-quicknav' ), 'template_tag' => __( 'Template Tags', 'bricks-quicknav' ) ) as $tax => $label ) {
				$object = get_taxonomy( $tax );
				if ( ! $object || ! current_user_can( $object->cap->manage_terms ) ) { continue; }
				$id = 'template_bundle' === $tax ? 'bxqn-bundles' : 'bxqn-tags';
				$nodes->add_node( array( 'id' => $id, 'parent' => 'bxqn-group-templates', 'title' => esc_html( $label ), 'href' => add_query_arg( array( 'taxonomy' => $tax, 'post_type' => 'bricks_template' ), admin_url( 'edit-tags.php' ) ) ) );
				foreach ( Builder::terms( $tax ) as $term ) { $nodes->add_node( array( 'id' => ( 'template_bundle' === $tax ? 'bxqn-bundle-' : 'bxqn-tag-' ) . $term->term_id, 'parent' => $id, 'title' => esc_html( $term->name ), 'href' => add_query_arg( array( 'post_type' => 'bricks_template', $tax => $term->slug ), admin_url( 'edit.php' ) ) ) ); }
			}
		}
		if ( in_array( 'settings', $s['groups'], true ) && current_user_can( 'manage_options' ) ) { $this->add_settings_group( $nodes ); }
		if ( current_user_can( 'manage_options' ) ) {
			$addons = $this->integration_nodes( true );
			Integrations::configure( $addons, $s );
			foreach ( $addons->nodes as $node ) { $nodes->add_node( $node ); }
			$nodes->add_node( array( 'id' => 'bxqn-own-settings', 'parent' => 'ddw-bricks-quicknav', 'title' => esc_html__( 'QuickNav Settings', 'bricks-quicknav' ), 'href' => Plugin::url() ) );
		}
		if ( ! current_user_can( 'manage_options' ) ) { ChildTheme::add( $nodes ); }
		$this->add_footer_group( $nodes );
		MenuIcons::apply( $nodes );
		$nodes->render( $bar );
	}

	/**
	 * Add one registered content type and its eligible posts.
	 * @param NodeCollection $nodes Staged menu collection.
	 * @param string $type Registered content type.
	 * @param string $id Stable parent node ID.
	 * @param string $label Translated parent label.
	 * @param array $settings Effective content settings.
	 * @return void Adds a bounded content group and capability-checked creation link.
	 */
	private function content( $nodes, $type, $id, $label, $settings ) {
		$object = get_post_type_object( $type );
		if ( ! $object || ! current_user_can( $object->cap->edit_posts ) ) { return; }
		$nodes->add_node( array( 'id' => $id, 'parent' => 'bxqn-group-templates', 'title' => esc_html( $label ), 'href' => add_query_arg( 'post_type', $type, admin_url( 'edit.php' ) ) ) );
		if ( current_user_can( $object->cap->create_posts ) ) {
			$url = add_query_arg( 'post_type', $type, admin_url( 'post-new.php' ) );
			$nodes->add_node( array( 'id' => 'bxqn-pages-addnew-' . $type, 'parent' => $id, 'title' => esc_html__( 'Add New', 'bricks-quicknav' ), 'href' => $url ) );
			if ( 'bricks_template' === $type ) { $nodes->add_node( array( 'id' => 'bxqn-wpnewcontent-new-template', 'parent' => 'new-content', 'title' => esc_html__( 'Template (Bricks)', 'bricks-quicknav' ), 'href' => $url ) ); }
		}
		$this->post_groups( $nodes, Builder::posts( $type, $settings ), $id, 'page' === $type ? 'bxqn-page-' : ( 'bricks_template' === $type ? 'bxqn-template-' : $id . '-' ) );
		if ( 'page' === $type ) {
			$nodes->add_group( array( 'id' => 'bxqn-group-pages-hook', 'parent' => $id ) );
			if ( class_exists( 'DDW_Builder_List_Pages' ) || defined( 'DDW_BLP_VERSION' ) ) { $nodes->add_node( array( 'id' => 'bxqn-blp-filter-bricks-pages', 'parent' => 'bxqn-group-pages-hook', 'title' => esc_html__( 'Filter Bricks Pages', 'bricks-quicknav' ), 'href' => admin_url( 'edit.php?post_type=page&builder=bricks' ) ) ); }
		}
	}

	/**
	 * Group existing eligible content without additional queries or changing item IDs.
	 * @param NodeCollection $nodes Staged menu collection.
	 * @param array $posts Authorized content selection.
	 * @param string $parent Content menu ID.
	 * @param string $prefix Existing item ID prefix.
	 * @return void Adds nonempty status groups and muted headings.
	 */
	private function post_groups( $nodes, $posts, $parent, $prefix ) {
		$buckets = array( 'published' => array(), 'unpublished' => array() );
		foreach ( $posts as $post ) { $buckets[ 'publish' === $post->post_status ? 'published' : 'unpublished' ][] = $post; }
		foreach ( $buckets as $bucket => $items ) {
			$group = $parent . '-' . $bucket;
			$content = new NodeCollection();
			foreach ( $items as $post ) { $this->post_node( $content, $post, $prefix . $post->ID, $group ); }
			if ( ! $content->nodes ) { continue; }
			$nodes->add_group( array( 'id' => $group, 'parent' => $parent ) );
			$nodes->add_node( array( 'id' => $group . '-heading', 'parent' => $group, 'title' => esc_html( 'published' === $bucket ? __( 'Published', 'bricks-quicknav' ) : __( 'Unpublished', 'bricks-quicknav' ) ), 'meta' => array( 'class' => 'bxqn-status-heading' ) ) );
			foreach ( $content->nodes as $node ) { $nodes->add_node( $node ); }
		}
	}

	/**
	 * Add an individually authorized builder shortcut.
	 * @param NodeCollection $nodes Staged menu collection.
	 * @param \WP_Post $post Content object.
	 * @param string $id Stable node ID.
	 * @param string $parent Parent node ID.
	 * @return void Adds a shortcut only when Bricks returns a usable destination.
	 */
	private function post_node( $nodes, $post, $id, $parent ) {
		$url = Builder::edit_url( $post->ID );
		if ( ! $url ) { return; }
		$title = esc_html( trim( $post->post_title ) ?: __( '(no title)', 'bricks-quicknav' ) );
		if ( 'publish' !== $post->post_status ) { $status = get_post_status_object( $post->post_status ); $title .= ' <span class="bxqn-status-label">(' . esc_html( $status ? $status->label : $post->post_status ) . ')</span>'; }
		$nodes->add_node( array( 'id' => $id, 'parent' => $parent, 'title' => $title, 'href' => esc_url( $url ) ) );
	}
}
