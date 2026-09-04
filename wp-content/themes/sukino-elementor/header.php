<?php
/**
 * Header: top contact bar (phone/WhatsApp/email + International Patients
 * CTA) and the primary navigation. All values come from the Customizer
 * (Appearance > Customize > Sukino Contact & Social) so non-developers can
 * update them without editing code or the Elementor design.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="profile" href="https://gmpg.org/xfn/11" />
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">

	<div class="sukino-topbar">
		<div class="sukino-container sukino-topbar-inner">
			<p class="sukino-topbar-text"><?php echo esc_html( get_theme_mod( 'sukino_topbar_text', "India's first continuum-of-care provider — trusted by families across 20+ countries." ) ); ?></p>
			<div class="sukino-topbar-links">
				<a href="tel:<?php echo esc_attr( sukino_tel_link( get_theme_mod( 'sukino_phone', '+91 80 4718 4718' ) ) ); ?>">📞 <?php echo esc_html( get_theme_mod( 'sukino_phone', '+91 80 4718 4718' ) ); ?></a>
				<a href="mailto:<?php echo esc_attr( get_theme_mod( 'sukino_email', 'care@sukino.com' ) ); ?>">✉ <?php echo esc_html( get_theme_mod( 'sukino_email', 'care@sukino.com' ) ); ?></a>
				<a class="sukino-topbar-cta" href="<?php echo esc_url( home_url( '/international-patients-family/' ) ); ?>"><?php esc_html_e( 'International Patients', 'sukino-elementor' ); ?></a>
			</div>
		</div>
	</div>

	<header id="masthead" class="sukino-header">
		<div class="sukino-container sukino-header-inner">
			<div class="sukino-branding">
				<?php if ( has_custom_logo() ) : the_custom_logo(); else : ?>
					<a class="sukino-site-title" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
				<?php endif; ?>
			</div>

			<nav id="site-navigation" class="sukino-primary-navigation" aria-label="<?php esc_attr_e( 'Primary Menu', 'sukino-elementor' ); ?>">
				<?php
				wp_nav_menu( array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'sukino-menu',
					'fallback_cb'    => false,
				) );
				?>
			</nav>

			<button class="sukino-menu-toggle" aria-controls="site-navigation" aria-expanded="false">
				<span></span><span></span><span></span>
				<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'sukino-elementor' ); ?></span>
			</button>
		</div>
	</header>

	<div id="content" class="site-content">
