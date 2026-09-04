<?php
/**
 * "Our Team" custom post type — doctors, therapists, nurses and
 * specialists. Rendered via the [sukino_team] shortcode.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sukino_register_team_member_cpt() {
	register_post_type( 'sukino_team_member', array(
		'labels' => array(
			'name'          => __( 'Team Members', 'sukino-elementor' ),
			'singular_name' => __( 'Team Member', 'sukino-elementor' ),
			'add_new_item'  => __( 'Add New Team Member', 'sukino-elementor' ),
			'edit_item'     => __( 'Edit Team Member', 'sukino-elementor' ),
			'all_items'     => __( 'Our Team', 'sukino-elementor' ),
			'menu_name'     => __( 'Our Team', 'sukino-elementor' ),
		),
		'public'        => true,
		'has_archive'   => 'our-team',
		'rewrite'       => array( 'slug' => 'team' ),
		'menu_icon'     => 'dashicons-groups',
		'menu_position' => 7,
		'supports'      => array( 'title', 'editor', 'thumbnail', 'revisions' ),
		'show_in_rest'  => true,
	) );
}
add_action( 'init', 'sukino_register_team_member_cpt' );
