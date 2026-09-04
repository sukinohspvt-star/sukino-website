<?php
/**
 * "Services" custom post type — Post-Hospital Rehabilitation, Home
 * Healthcare, Palliative Care, End-of-Life Care, etc. Editors manage these
 * from wp-admin; the [sukino_services] shortcode renders them dynamically
 * inside any Elementor page.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sukino_register_services_cpt() {
	register_post_type( 'sukino_service', array(
		'labels' => array(
			'name'               => __( 'Services', 'sukino-elementor' ),
			'singular_name'      => __( 'Service', 'sukino-elementor' ),
			'add_new_item'       => __( 'Add New Service', 'sukino-elementor' ),
			'edit_item'          => __( 'Edit Service', 'sukino-elementor' ),
			'all_items'          => __( 'All Services', 'sukino-elementor' ),
			'menu_name'          => __( 'Services', 'sukino-elementor' ),
		),
		'public'       => true,
		'has_archive'  => 'services',
		'rewrite'      => array( 'slug' => 'service' ),
		'menu_icon'    => 'dashicons-heart',
		'menu_position' => 5,
		'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes' ),
		'show_in_rest' => true,
	) );

	register_taxonomy( 'service_category', 'sukino_service', array(
		'labels' => array(
			'name'          => __( 'Service Categories', 'sukino-elementor' ),
			'singular_name' => __( 'Service Category', 'sukino-elementor' ),
		),
		'hierarchical'  => true,
		'public'        => true,
		'show_in_rest'  => true,
		'rewrite'       => array( 'slug' => 'service-category' ),
	) );
}
add_action( 'init', 'sukino_register_services_cpt' );
