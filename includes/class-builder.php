<?php
/** Bricks API adapter and request-local content queries. @package BricksQuickNav */
namespace Deckerweb\BricksQuickNav;
defined( 'ABSPATH' ) || exit;

/** Keeps theme APIs, content eligibility, and bounded queries in one place. */
final class Builder {
	/** @return bool Whether the active parent theme exposes the supported Bricks API. */
	public static function active() {
		return defined( 'BRICKS_VERSION' ) && function_exists( 'bricks_is_builder' )
			&& is_callable( array( '\Bricks\Capabilities', 'current_user_can_use_builder' ) );
	}

	/** @return array Public content type objects supported by the active Bricks theme. */
	public static function content_types() {
		$objects = get_post_types( array( 'public' => true ), 'objects' );
		unset( $objects['attachment'], $objects['bricks_template'], $objects['bricks_fonts'] );
		if ( self::active() && is_callable( array( '\Bricks\Helpers', 'get_supported_post_types' ) ) ) {
			$types = \Bricks\Helpers::get_supported_post_types();
			return is_array( $types ) ? array_intersect_key( $objects, $types ) : array();
		}
		return $objects;
	}

	/**
	 * Check both object permissions and Bricks builder permissions.
	 * @param int $id Content ID.
	 * @return bool Whether the current user can edit this content in Bricks.
	 */
	public static function can_edit( $id ) {
		$post = get_post( $id );
		if ( ! self::active() || ! $post || ! current_user_can( 'edit_post', $id ) || 'trash' === $post->post_status ) { return false; }
		if ( 'bricks_template' !== $post->post_type && ! isset( self::content_types()[ $post->post_type ] ) ) { return false; }
		return \Bricks\Capabilities::current_user_can_use_builder( $id );
	}

	/**
	 * Use Bricks' own URL API with a query-safe fallback.
	 * @param int $id Content ID.
	 * @return string Authorized builder URL, or empty string for an unavailable destination.
	 */
	public static function edit_url( $id ) {
		if ( ! self::can_edit( $id ) ) { return ''; }
		if ( is_callable( array( '\Bricks\Helpers', 'get_builder_edit_link' ) ) ) {
			$url = \Bricks\Helpers::get_builder_edit_link( $id );
			return is_string( $url ) ? $url : '';
		}
		$url = get_permalink( $id );
		return $url ? add_query_arg( 'bricks', 'run', $url ) : '';
	}

	/** @return array Public template-type labels from the active Bricks setup. */
	public static function template_types() {
		if ( ! class_exists( '\Bricks\Setup' ) ) { return array(); }
		if ( is_callable( array( '\Bricks\Setup', 'get_control_options' ) ) ) {
			$options = \Bricks\Setup::get_control_options();
			return is_array( $options['templateTypes'] ?? null ) ? $options['templateTypes'] : array();
		}
		$vars = get_class_vars( '\Bricks\Setup' );
		return is_array( $vars['control_options']['templateTypes'] ?? null ) ? $vars['control_options']['templateTypes'] : array();
	}

	/**
	 * Query a visible content list once per request and effective configuration.
	 * @param string $type Registered content type.
	 * @param array $settings Effective website/user configuration.
	 * @return array Authorized posts; request cache never crosses users or websites.
	 */
	public static function posts( $type, $settings ) {
		static $cache = array();
		$object = get_post_type_object( $type );
		if ( ! $object || ! current_user_can( $object->cap->edit_posts ) ) { return array(); }
		$args = array(
			'post_type' => $type, 'posts_per_page' => $settings['limit'],
			'post_status' => $settings['drafts'] ? array( 'publish', 'draft', 'pending', 'private', 'future' ) : 'publish',
			'orderby' => $settings['orderby'], 'order' => 'title' === $settings['orderby'] ? 'ASC' : 'DESC',
			'no_found_rows' => true, 'suppress_filters' => false,
		);
		if ( 'bricks_template' !== $type && 'bricks_fonts' !== $type ) {
			$args['meta_query'] = array( array( 'key' => '_bricks_editor_mode', 'value' => 'bricks' ) );
		}
		/**
		 * Customize content query arguments; return the modified array.
		 * @since 1.0.0
		 * @param array $args Content query arguments.
		 * @param string $type Registered post type.
		 */
		$filtered = apply_filters( 'ddw-bxqn/get-template-type', $args, $type );
		$args = is_array( $filtered ) ? $filtered : $args;
		$args['post_type'] = $type;
		$args['posts_per_page'] = min( 100, max( 1, absint( $args['posts_per_page'] ?? $settings['limit'] ) ) );
		$key = get_current_blog_id() . ':' . get_current_user_id() . ':' . md5( serialize( $args ) );
		if ( isset( $cache[ $key ] ) ) { return $cache[ $key ]; }
		$posts = get_posts( $args );
		$cache[ $key ] = array_values( array_filter( $posts, static function ( $post ) use ( $type ) {
			return $post instanceof \WP_Post && current_user_can( 'edit_post', $post->ID )
				&& ( 'bricks_fonts' === $type || self::can_edit( $post->ID ) );
		} ) );
		return $cache[ $key ];
	}

	/**
	 * Load taxonomy terms safely without querying unavailable taxonomies.
	 * @param string $taxonomy Bricks taxonomy slug.
	 * @return array Terms visible to a user allowed to manage this taxonomy.
	 */
	public static function terms( $taxonomy ) {
		$object = get_taxonomy( $taxonomy );
		if ( ! $object || ! current_user_can( $object->cap->manage_terms ) ) { return array(); }
		$args = array( 'taxonomy' => $taxonomy, 'number' => 100 );
		/**
		 * Customize term query arguments; return the modified array.
		 * @since 1.0.0
		 * @param array $args Term query arguments.
		 * @param string $taxonomy Taxonomy slug.
		 */
		$filtered = apply_filters( 'ddw-bxqn/get-template-terms', $args, $taxonomy );
		$args = is_array( $filtered ) ? $filtered : $args;
		$args['taxonomy'] = $taxonomy;
		$args['number'] = min( 100, max( 1, absint( $args['number'] ?? 100 ) ) );
		$terms = get_terms( $args );
		return is_wp_error( $terms ) ? array() : $terms;
	}
}
