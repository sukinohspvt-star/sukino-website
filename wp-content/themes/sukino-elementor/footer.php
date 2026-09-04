<?php
/**
 * Footer: widgetized columns (Appearance > Widgets), footer menu, socials,
 * and a floating WhatsApp click-to-chat button — the fastest channel for
 * international families in a different timezone to reach the care team.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
	</div><!-- #content -->

	<footer id="colophon" class="sukino-footer">
		<div class="sukino-container sukino-footer-columns">
			<?php for ( $i = 1; $i <= 4; $i++ ) : ?>
				<?php if ( is_active_sidebar( 'footer-' . $i ) ) : ?>
					<div class="sukino-footer-column">
						<?php dynamic_sidebar( 'footer-' . $i ); ?>
					</div>
				<?php endif; ?>
			<?php endfor; ?>
		</div>

		<div class="sukino-container sukino-footer-bottom">
			<nav class="sukino-footer-menu" aria-label="<?php esc_attr_e( 'Footer Menu', 'sukino-elementor' ); ?>">
				<?php
				wp_nav_menu( array(
					'theme_location' => 'footer',
					'container'      => false,
					'menu_class'     => 'sukino-footer-menu-list',
					'fallback_cb'    => false,
				) );
				?>
			</nav>

			<div class="sukino-social-links">
				<?php
				$socials = array(
					'sukino_facebook'  => 'Facebook',
					'sukino_instagram' => 'Instagram',
					'sukino_linkedin'  => 'LinkedIn',
					'sukino_youtube'   => 'YouTube',
				);
				foreach ( $socials as $mod => $label ) :
					$url = get_theme_mod( $mod );
					if ( $url ) :
						?>
						<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $label ); ?></a>
						<?php
					endif;
				endforeach;
				?>
			</div>

			<p class="sukino-copyright">&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'All rights reserved.', 'sukino-elementor' ); ?></p>
		</div>
	</footer>

	<?php if ( get_theme_mod( 'sukino_show_whatsapp_button', true ) && get_theme_mod( 'sukino_whatsapp' ) ) : ?>
		<a
			class="sukino-whatsapp-fab"
			href="<?php echo esc_url( sukino_whatsapp_link( get_theme_mod( 'sukino_whatsapp' ), __( 'Hello, I would like to know more about care options at Sukino Healthcare.', 'sukino-elementor' ) ) ); ?>"
			target="_blank"
			rel="noopener noreferrer"
			aria-label="<?php esc_attr_e( 'Chat with us on WhatsApp', 'sukino-elementor' ); ?>"
		>
			<span aria-hidden="true">💬</span>
		</a>
	<?php endif; ?>

</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
