<?php
/**
 * Generic archive template covering the Services, Centres and Team
 * post-type archives and any taxonomy archive. Most editors will instead
 * build a custom Elementor page using [sukino_services], [sukino_locations]
 * or [sukino_team] for full design control — this template is the
 * sensible default if that archive URL is visited directly.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="primary" class="site-main sukino-container sukino-archive">
	<header class="sukino-archive-header">
		<h1 class="sukino-page-title"><?php the_archive_title(); ?></h1>
		<?php the_archive_description( '<div class="sukino-archive-description">', '</div>' ); ?>
	</header>

	<?php if ( have_posts() ) : ?>
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
