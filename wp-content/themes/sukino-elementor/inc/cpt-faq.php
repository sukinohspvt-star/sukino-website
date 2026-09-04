<?php
/**
 * "FAQs" custom post type with a category taxonomy so the same FAQ pool
 * can be filtered per page, e.g. [sukino_faqs category="international-patients"]
 * on the International Patients & Family page and [sukino_faqs category="general"]
 * elsewhere.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sukino_register_faq_cpt() {
	register_post_type( 'sukino_faq', array(
		'labels' => array(
			'name'          => __( 'FAQs', 'sukino-elementor' ),
			'singular_name' => __( 'FAQ', 'sukino-elementor' ),
			'add_new_item'  => __( 'Add New FAQ', 'sukino-elementor' ),
			'edit_item'     => __( 'Edit FAQ', 'sukino-elementor' ),
			'all_items'     => __( 'FAQs', 'sukino-elementor' ),
			'menu_name'     => __( 'FAQs', 'sukino-elementor' ),
		),
		'public'        => true,
		'has_archive'   => false,
		'rewrite'       => array( 'slug' => 'faq' ),
		'menu_icon'     => 'dashicons-editor-help',
		'menu_position' => 9,
		'supports'      => array( 'title', 'editor', 'page-attributes' ),
		'show_in_rest'  => true,
	) );

	register_taxonomy( 'faq_category', 'sukino_faq', array(
		'labels' => array(
			'name'          => __( 'FAQ Categories', 'sukino-elementor' ),
			'singular_name' => __( 'FAQ Category', 'sukino-elementor' ),
		),
		'hierarchical'  => true,
		'public'        => true,
		'show_in_rest'  => true,
		'rewrite'       => array( 'slug' => 'faq-category' ),
	) );
}
add_action( 'init', 'sukino_register_faq_cpt' );
