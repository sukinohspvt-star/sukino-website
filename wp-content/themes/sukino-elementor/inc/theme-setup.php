<?php
/**
 * Core theme setup: supports, menus, sidebars, asset enqueue.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sukino_setup() {
	load_theme_textdomain( 'sukino-elementor', SUKINO_THEME_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'custom-logo', array(
		'height'      => 80,
		'width'       => 240,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
	) );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );

	// Elementor compatibility.
	add_theme_support( 'elementor' );
	add_theme_support( 'elementor-pro' );

	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'sukino-elementor' ),
		'footer'  => __( 'Footer Menu', 'sukino-elementor' ),
	) );
}
add_action( 'after_setup_theme', 'sukino_setup' );

function sukino_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Footer Column 1', 'sukino-elementor' ),
		'id'            => 'footer-1',
		'before_widget' => '<div class="footer-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4 class="footer-widget-title">',
		'after_title'   => '</h4>',
	) );
	register_sidebar( array(
		'name'          => __( 'Footer Column 2', 'sukino-elementor' ),
		'id'            => 'footer-2',
		'before_widget' => '<div class="footer-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4 class="footer-widget-title">',
		'after_title'   => '</h4>',
	) );
	register_sidebar( array(
		'name'          => __( 'Footer Column 3', 'sukino-elementor' ),
		'id'            => 'footer-3',
		'before_widget' => '<div class="footer-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4 class="footer-widget-title">',
		'after_title'   => '</h4>',
	) );
	register_sidebar( array(
		'name'          => __( 'Footer Column 4', 'sukino-elementor' ),
		'id'            => 'footer-4',
		'before_widget' => '<div class="footer-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4 class="footer-widget-title">',
		'after_title'   => '</h4>',
	) );
}
add_action( 'widgets_init', 'sukino_widgets_init' );

function sukino_enqueue_assets() {
	wp_enqueue_style(
		'sukino-google-fonts',
		'https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'sukino-style', get_stylesheet_uri(), array(), SUKINO_THEME_VERSION );
	wp_enqueue_style( 'sukino-theme', SUKINO_THEME_URI . '/assets/css/theme.css', array( 'sukino-style' ), SUKINO_THEME_VERSION );
	wp_enqueue_script( 'sukino-theme', SUKINO_THEME_URI . '/assets/js/theme.js', array(), SUKINO_THEME_VERSION, true );

	wp_localize_script( 'sukino-theme', 'sukinoSettings', array(
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
	) );
}
add_action( 'wp_enqueue_scripts', 'sukino_enqueue_assets' );

/**
 * Elementor renders its own <title> markup for pages built with it; make
 * sure archive/blog templates degrade gracefully with a sensible excerpt
 * length and "Read more" link, since those are the only templates this
 * theme renders outside of Elementor.
 */
function sukino_excerpt_length( $length ) {
	return 30;
}
add_filter( 'excerpt_length', 'sukino_excerpt_length' );

function sukino_excerpt_more( $more ) {
	return '&hellip;';
}
add_filter( 'excerpt_more', 'sukino_excerpt_more' );

/**
 * Helper: format a phone number for tel: links (strip spaces/dashes).
 */
function sukino_tel_link( $number ) {
	return preg_replace( '/[^\d+]/', '', (string) $number );
}

/**
 * Helper: format a phone number for wa.me WhatsApp links (digits only).
 */
function sukino_whatsapp_link( $number, $message = '' ) {
	$digits = preg_replace( '/[^\d]/', '', (string) $number );
	$url    = 'https://wa.me/' . $digits;
	if ( $message ) {
		$url .= '?text=' . rawurlencode( $message );
	}
	return $url;
}
