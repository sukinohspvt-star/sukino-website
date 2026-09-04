<?php
/**
 * Fallback template: blog listing and any query WordPress can't match to
 * a more specific template. Pages themselves are handled by page.php and
 * built visually in Elementor.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="primary" class="site-main sukino-container sukino-archive">
	<?php if ( have_posts() ) : ?>
		<?php if ( is_home() && ! is_front_page() ) : ?>
			<h1 class="sukino-page-title"><?php single_post_title(); ?></h1>
		<?php endif; ?>

		<div class="sukino-grid sukino-post-grid" style="--sukino-cols:3;">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content', get_post_type() );
			endwhile;
			?>
		</div>

		<div class="sukino-pagination">
			<?php the_posts_pagination(); ?>
		</div>
	<?php else : ?>
		<p><?php esc_html_e( 'Nothing found.', 'sukino-elementor' ); ?></p>
	<?php endif; ?>
</main>
<?php
get_footer();
