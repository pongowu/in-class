<?php 
/*
Plugin Name: Custom Post Type - Products
Description: creates support for a catalog of products, organized by brand and features
Author: Annette
Plugin URI: http://wordpress.stuffi.com
Version: 0.1
License: GPLv3
*/

/**
 * Activate Custom Post Type and admin UI
 * @since 0.1
 */

add_action('init', 'rad_register_cpt');
function rad_register_cpt(){
	register_post_type('product', array(
		'public' => true,
		'labels' => array(
			'name' => 'Products',
			'singular_name' => 'Product',
			'not_found' => 'No Products Found',
			'add_new_item' => 'Add New Product',
			),
		'has_archive' => true,
		'rewrite' => array('slug' => 'shop'),
		'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions'),
		));

	// add brand taxonomy
	register_taxonomy('brand', 'product', array(
			'hierarchical' => true, //act like a category. parent/child
			'rewrite' => array( 'slug' => 'brands' ),
			'labels' => array(
				'name' => 'Brands',
				'singular_name' => 'Brand',
				'add_new_item' => 'Add new Brand',
				'search_items' => 'Search Brands',
				'update_item' => 'Update Brand'
				),
		));

	// add features taxonomy
	register_taxonomy('feature', 'product', array(
			'hierarchical' => false, //act like a tag
			'rewrite' => array( 'slug' => 'features' ),
			'labels' => array(
				'name' => 'Features',
				'singular_name' => 'Feature',
				'add_new_item' => 'Add new Feature',
				'search_items' => 'Search Features',
				'update_item' => 'Update Feature'
				),
		));
}

/**
 * Flush Rewrite rules automagically when the plugin is activated
 *@since 0.1.
 */

function rad_flush(){
	rad_register_cpt();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'rad_flush');