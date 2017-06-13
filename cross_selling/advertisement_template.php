<?php
/**
 * Created by PhpStorm.
 * User: ananth
 * Date: 14/6/17
 * Time: 12:23 AM
 */

function create_cross_sell_templates() {
	create_SMS_postTypes();
	create_email_postTypes();
}

function create_SMS_postTypes() {
	$labels = array(
		'name'               => 'Edugorilla Cross Sell SMS',
		'singular_name'      => 'EduCrossSMS',
		'menu_name'          => 'EduCrossSell',
		'name_admin_bar'     => 'PromotionalCrossSell',
		'add_new'            => 'Add New SMS Template',
		'add_new_item'       => 'Add New Template',
		'new_item'           => 'New Template',
		'edit'               => 'Edit',
		'edit_item'          => 'Edit Template',
		'view'               => 'View',
		'view_item'          => 'View Template',
		'all_items'          => 'All SMS Templates',
		'search_items'       => 'Search Templates',
		'parent'             => 'Parent SMS Template',
		'parent_item_colon'  => 'Parent Templates',
		'not_found'          => 'No templates found.',
		'not_found_in_trash' => 'No templates found in Trash.'
	);

	$args = array(
		'labels'        => $labels,
		'public'        => true,
		'rewrite'       => array( 'slug' => 'recipe' ),
		'has_archive'   => true,
		'menu_position' => 2,
		'menu_icon'     => 'dashicons-carrot',
		'taxonomies'    => array( 'post_tag', 'category', 'location' ),
		'supports'      => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields', 'comments' )
	);
	register_post_type( 'cross_sell_sms', $args );
}

function create_email_postTypes() {
	$labels = array(
		'name'               => 'Edugorilla Cross Sell Email',
		'singular_name'      => 'EduCrossEmail',
		'menu_name'          => 'EduCrossSellEmail',
		'name_admin_bar'     => 'PromotionalCrossSellEmail',
		'add_new'            => 'Add New Email Template',
		'add_new_item'       => 'Add New Email',
		'new_item'           => 'New Template',
		'edit'               => 'Edit',
		'edit_item'          => 'Edit Template',
		'view'               => 'View',
		'view_item'          => 'View Template',
		'all_items'          => 'All Email Templates',
		'search_items'       => 'Search Templates',
		'parent'             => 'Parent Email Template',
		'parent_item_colon'  => 'Parent Templates',
		'not_found'          => 'No templates found.',
		'not_found_in_trash' => 'No templates found in Trash.'
	);

	$args = array(
		'labels'        => $labels,
		'public'        => true,
		'rewrite'       => array( 'slug' => 'recipe' ),
		'has_archive'   => true,
		'menu_position' => 2,
		'menu_icon'     => 'dashicons-carrot',
		'taxonomies'    => array( 'post_tag', 'category', 'location' ),
		'supports'      => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields', 'comments' )
	);
	register_post_type( 'cross_sell_email', $args );
}


add_action( 'init', 'create_cross_sell_templates' );

?>