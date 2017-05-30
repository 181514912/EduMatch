<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Edugorilla_Taxonomy_Listing_Categories
 *
 * @class Edugorilla_Taxonomy_Listing_Categories
 * @package lead-gen-master/frontend/taxonomies
 * @author EduGorilla Tech Team
 */
class Edugorilla_Taxonomy_Listing_Categories {
	/**
	 * Initialize taxonomy
	 *
	 * @access public
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'definition' ), 12 );
		add_action( 'parent_file', array( __CLASS__, 'menu' ) );
	}

	public static function get_listing_post_types( $include_abstract = false, $with_labels = false ) {
		$listings_types = array();

		$post_types = get_post_types( array(), 'objects' ); // in this moment, all disabled post types should be removed

		if ( ! empty( $post_types ) ) {
			foreach ( $post_types as $post_type ) {
				if ( $post_type->show_in_menu === 'listings' ) {
					if ( $with_labels ) {
						$post_type_obj = get_post_type_object( $post_type->name );
						$listings_types[ $post_type->name ] = esc_attr( $post_type_obj->labels->singular_name );
					} else {
						$listings_types[] = $post_type->name;
					}
				}
			}
		}

		// Sort alphabetically
		if( $with_labels ) {
			asort( $listings_types );
		} else {
			sort( $listings_types );
		}

		if ( $include_abstract ) {
			array_unshift( $listings_types, 'listing' );
		}

		return $listings_types;
	}

	/**
	 * Widget definition
	 *
	 * @access public
	 * @return void
	 */
	public static function definition() {
		$labels = array(
			'name'              => __( 'Categories', 'edugorilla' ),
			'singular_name'     => __( 'Category', 'edugorilla' ),
			'search_items'      => __( 'Search Categories', 'edugorilla' ),
			'all_items'         => __( 'All Categories', 'edugorilla' ),
			'parent_item'       => __( 'Parent Category', 'edugorilla' ),
			'parent_item_colon' => __( 'Parent Category:', 'edugorilla' ),
			'edit_item'         => __( 'Edit Category', 'edugorilla' ),
			'update_item'       => __( 'Update Category', 'edugorilla' ),
			'add_new_item'      => __( 'Add New Category', 'edugorilla' ),
			'new_item_name'     => __( 'New Category', 'edugorilla' ),
			'menu_name'         => __( 'Categories', 'edugorilla' ),
            'not_found'         => __( 'No categories found.', 'edugorilla' ),
		);
		//unregister_taxonomy( 'edu_categories' );
		register_taxonomy( 'edu_categories', self::get_listing_post_types(), array(
			'labels'            => $labels,
			'hierarchical'      => true,
			'query_var'         => 'listing-category',
			'rewrite'           => array( 'slug' => _x( 'listing-category', 'URL slug', 'edugorilla' ) ),
			'public'            => true,
			'show_ui'           => true,
			'show_in_menu'	    => 'edugorilla',
            'show_in_nav_menus' => true,
			'show_in_rest'      => true,
            'meta_box_cb'       => false,
			'show_admin_column' => true,
		) );
	}

	/**
	 * Set active menu for taxonomy location
	 *
	 * @access public
	 * @return string
	 */
	public static function menu( $parent_file ) {
		global $current_screen;
		$taxonomy = $current_screen->taxonomy;

		if ( 'edu_categories' == $taxonomy ) {
			return 'edugorilla';
		}

		return $parent_file;
	}
	
}


Edugorilla_Taxonomy_Listing_Categories::init();
