<?php
/**
 * Customizer settings for sitewide contact details used by the top bar,
 * footer, and the floating WhatsApp button — so front-desk staff can
 * update phone/WhatsApp/email/social links without touching Elementor.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sukino_customize_register( $wp_customize ) {
	$wp_customize->add_section( 'sukino_contact', array(
		'title'    => __( 'Sukino Contact & Social', 'sukino-elementor' ),
		'priority' => 30,
	) );

	$fields = array(
		'sukino_phone'          => array( 'label' => __( 'Main Phone Number', 'sukino-elementor' ), 'default' => '+91 80 4718 4718' ),
		'sukino_whatsapp'       => array( 'label' => __( 'WhatsApp Number (with country code, digits only)', 'sukino-elementor' ), 'default' => '919591945233' ),
		'sukino_email'          => array( 'label' => __( 'Contact Email', 'sukino-elementor' ), 'default' => 'care@sukino.com' ),
		'sukino_ip_email'       => array( 'label' => __( 'International Patients Email', 'sukino-elementor' ), 'default' => 'international@sukino.com' ),
		'sukino_topbar_text'    => array( 'label' => __( 'Top Bar Announcement', 'sukino-elementor' ), 'default' => 'India\'s first continuum-of-care provider — trusted by families across 20+ countries.' ),
		'sukino_facebook'       => array( 'label' => __( 'Facebook URL', 'sukino-elementor' ), 'default' => 'https://facebook.com/sukinohealthcare' ),
		'sukino_instagram'      => array( 'label' => __( 'Instagram URL', 'sukino-elementor' ), 'default' => 'https://instagram.com/sukinohealthcare' ),
		'sukino_linkedin'       => array( 'label' => __( 'LinkedIn URL', 'sukino-elementor' ), 'default' => 'https://linkedin.com/company/sukinohealthcare' ),
		'sukino_youtube'        => array( 'label' => __( 'YouTube URL', 'sukino-elementor' ), 'default' => 'https://youtube.com/@sukinohealthcare' ),
	);

	foreach ( $fields as $id => $args ) {
		$wp_customize->add_setting( $id, array(
			'default'           => $args['default'],
			'sanitize_callback' => 'sanitize_text_field',
		) );
		$wp_customize->add_control( $id, array(
			'label'   => $args['label'],
			'section' => 'sukino_contact',
			'type'    => 'text',
		) );
	}

	$wp_customize->add_setting( 'sukino_show_whatsapp_button', array(
		'default'           => true,
		'sanitize_callback' => 'wp_validate_boolean',
	) );
	$wp_customize->add_control( 'sukino_show_whatsapp_button', array(
		'label'   => __( 'Show floating WhatsApp button', 'sukino-elementor' ),
		'section' => 'sukino_contact',
		'type'    => 'checkbox',
	) );
}
add_action( 'customize_register', 'sukino_customize_register' );
