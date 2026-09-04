<?php
/**
 * "Centres" (locations) custom post type — Bangalore (Koramangala,
 * Whitefield), Kochi, Coimbatore, and any future city. Rendered via the
 * [sukino_locations] shortcode.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sukino_register_locations_cpt() {
	register_post_type( 'sukino_location', array(
		'labels' => array(
			'name'          => __( 'Centres', 'sukino-elementor' ),
			'singular_name' => __( 'Centre', 'sukino-elementor' ),
			'add_new_item'  => __( 'Add New Centre', 'sukino-elementor' ),
			'edit_item'     => __( 'Edit Centre', 'sukino-elementor' ),
			'all_items'     => __( 'All Centres', 'sukino-elementor' ),
			'menu_name'     => __( 'Centres', 'sukino-elementor' ),
		),
		'public'        => true,
		'has_archive'   => 'centres',
		'rewrite'       => array( 'slug' => 'centre' ),
		'menu_icon'     => 'dashicons-location',
		'menu_position' => 6,
		'supports'      => array( 'title', 'editor', 'thumbnail', 'revisions' ),
		'show_in_rest'  => true,
	) );

	register_taxonomy( 'location_city', 'sukino_location', array(
		'labels' => array(
			'name'          => __( 'Cities', 'sukino-elementor' ),
			'singular_name' => __( 'City', 'sukino-elementor' ),
		),
		'hierarchical'  => true,
		'public'        => true,
		'show_in_rest'  => true,
		'rewrite'       => array( 'slug' => 'city' ),
	) );
}
add_action( 'init', 'sukino_register_locations_cpt' );
