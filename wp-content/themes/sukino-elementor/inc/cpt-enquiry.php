<?php
/**
 * "International Patient Enquiries" custom post type — private, admin-only
 * inbox that stores every submission from the [sukino_international_patient_form]
 * shortcode so staff can follow up even if the notification email is missed.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sukino_register_enquiry_cpt() {
	register_post_type( 'sukino_ip_enquiry', array(
		'labels' => array(
			'name'          => __( 'IP Enquiries', 'sukino-elementor' ),
			'singular_name' => __( 'Enquiry', 'sukino-elementor' ),
			'all_items'     => __( 'International Patient Enquiries', 'sukino-elementor' ),
			'menu_name'     => __( 'IP Enquiries', 'sukino-elementor' ),
			'view_item'     => __( 'View Enquiry', 'sukino-elementor' ),
		),
		'public'             => false,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'menu_icon'          => 'dashicons-airplane',
		'menu_position'      => 25,
		'capability_type'    => 'page',
		'capabilities'       => array(
			'create_posts' => 'do_not_allow',
		),
		'map_meta_cap'       => true,
		'supports'           => array( 'title' ),
		'show_in_rest'       => false,
	) );
}
add_action( 'init', 'sukino_register_enquiry_cpt' );

/**
 * Replace the default meta box with a read-only summary of the enquiry
 * fields, since this post type is admin-created-only (via the front-end
 * form) and has no editor support.
 */
function sukino_enquiry_meta_box() {
	add_meta_box(
		'sukino_enquiry_details',
		__( 'Enquiry Details', 'sukino-elementor' ),
		'sukino_render_enquiry_meta_box',
		'sukino_ip_enquiry',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'sukino_enquiry_meta_box' );

function sukino_render_enquiry_meta_box( $post ) {
	$fields = array(
		'sukino_enquiry_email'            => __( 'Email', 'sukino-elementor' ),
		'sukino_enquiry_phone'            => __( 'Phone / WhatsApp', 'sukino-elementor' ),
		'sukino_enquiry_country'          => __( 'Country', 'sukino-elementor' ),
		'sukino_enquiry_patient_relation' => __( 'Relation to Patient', 'sukino-elementor' ),
		'sukino_enquiry_service'          => __( 'Service of Interest', 'sukino-elementor' ),
		'sukino_enquiry_preferred_centre' => __( 'Preferred Centre', 'sukino-elementor' ),
		'sukino_enquiry_arrival'          => __( 'Expected Arrival in India', 'sukino-elementor' ),
		'sukino_enquiry_message'          => __( 'Message', 'sukino-elementor' ),
		'sukino_enquiry_submitted'        => __( 'Submitted', 'sukino-elementor' ),
	);
	echo '<table class="form-table"><tbody>';
	foreach ( $fields as $key => $label ) {
		$value = get_post_meta( $post->ID, $key, true );
		echo '<tr><th style="width:220px;">' . esc_html( $label ) . '</th><td>' . nl2br( esc_html( $value ) ) . '</td></tr>';
	}
	echo '</tbody></table>';
}
