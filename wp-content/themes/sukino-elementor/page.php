<?php
/**
 * Default page template. Elementor takes over the_content() output for
 * any page built with it, so this stays intentionally minimal — the
 * design lives entirely in Elementor, not in PHP markup.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<main id="primary" class="site-main sukino-page">
		<?php
		/*
		 * the_content() is filtered by Elementor to output the page's
		 * Elementor-built layout when it was edited with Elementor, so no
		 * special-casing is needed here — this template works for both
		 * Elementor pages and plain WordPress editor pages.
		 */
		?>
		<?php if ( ! is_front_page() && ! get_post_meta( get_the_ID(), '_elementor_edit_mode', true ) ) : ?>
			<header class="sukino-page-header sukino-container">
				<h1 class="sukino-page-title"><?php the_title(); ?></h1>
			</header>
		<?php endif; ?>
		<div class="sukino-page-content">
			<?php the_content(); ?>
		</div>
	</main>
	<?php
endwhile;

get_footer();
