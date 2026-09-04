<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="primary" class="site-main sukino-container sukino-404">
	<h1><?php esc_html_e( 'Page Not Found', 'sukino-elementor' ); ?></h1>
	<p><?php esc_html_e( "The page you're looking for doesn't exist. It may have moved.", 'sukino-elementor' ); ?></p>
	<p>
		<a class="sukino-btn sukino-btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to Home', 'sukino-elementor' ); ?></a>
	</p>
</main>
<?php
get_footer();
