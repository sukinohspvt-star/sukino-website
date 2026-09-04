<?php
/**
 * Sukino Elementor theme bootstrap.
 *
 * Loads theme setup, custom post types, shortcodes, the international
 * patient enquiry workflow and the Customizer settings that drive the
 * sitewide floating WhatsApp button and top contact bar.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SUKINO_THEME_VERSION', '1.0.0' );
define( 'SUKINO_THEME_DIR', get_template_directory() );
define( 'SUKINO_THEME_URI', get_template_directory_uri() );

require SUKINO_THEME_DIR . '/inc/theme-setup.php';
require SUKINO_THEME_DIR . '/inc/customizer.php';
require SUKINO_THEME_DIR . '/inc/cpt-service.php';
require SUKINO_THEME_DIR . '/inc/cpt-location.php';
require SUKINO_THEME_DIR . '/inc/cpt-team-member.php';
require SUKINO_THEME_DIR . '/inc/cpt-testimonial.php';
require SUKINO_THEME_DIR . '/inc/cpt-faq.php';
require SUKINO_THEME_DIR . '/inc/cpt-enquiry.php';
require SUKINO_THEME_DIR . '/inc/shortcodes.php';
require SUKINO_THEME_DIR . '/inc/enquiry-form.php';

/**
 * On (re)activation: register CPTs (already hooked on init) then flush
 * rewrite rules once, and auto-assign a menu named "Primary Menu" (created
 * by the demo content import) to the primary nav location so the site is
 * usable immediately after import without a manual Appearance > Menus step.
 */
function sukino_theme_activate() {
	sukino_register_services_cpt();
	sukino_register_locations_cpt();
	sukino_register_team_member_cpt();
	sukino_register_testimonial_cpt();
	sukino_register_faq_cpt();
	sukino_register_enquiry_cpt();
	flush_rewrite_rules();

	$existing_locations = get_nav_menu_locations();
	if ( empty( $existing_locations['primary'] ) ) {
		$menu = get_term_by( 'name', 'Primary Menu', 'nav_menu' );
		if ( $menu ) {
			$existing_locations['primary'] = $menu->term_id;
			set_theme_mod( 'nav_menu_locations', $existing_locations );
		}
	}
}
add_action( 'after_switch_theme', 'sukino_theme_activate' );
