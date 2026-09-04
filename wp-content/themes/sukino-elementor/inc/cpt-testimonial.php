<?php
/**
 * "Testimonials" custom post type. Includes an is_international flag so
 * the International Patients page can pull only testimonials from
 * overseas families via [sukino_testimonials international_only="true"].
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sukino_register_testimonial_cpt() {
	register_post_type( 'sukino_testimonial', array(
		'labels' => array(
			'name'          => __( 'Testimonials', 'sukino-elementor' ),
			'singular_name' => __( 'Testimonial', 'sukino-elementor' ),
			'add_new_item'  => __( 'Add New Testimonial', 'sukino-elementor' ),
			'edit_item'     => __( 'Edit Testimonial', 'sukino-elementor' ),
			'all_items'     => __( 'Testimonials', 'sukino-elementor' ),
			'menu_name'     => __( 'Testimonials', 'sukino-elementor' ),
		),
		'public'        => true,
		'has_archive'   => false,
		'rewrite'       => array( 'slug' => 'testimonial' ),
		'menu_icon'     => 'dashicons-format-quote',
		'menu_position' => 8,
		'supports'      => array( 'title', 'editor', 'thumbnail', 'revisions' ),
		'show_in_rest'  => true,
	) );
}
add_action( 'init', 'sukino_register_testimonial_cpt' );
