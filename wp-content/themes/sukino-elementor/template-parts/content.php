<?php
/**
 * Generic card used by index.php / archive.php for blog posts and CPT
 * archives when browsed directly (Elementor pages typically use the
 * [sukino_*] shortcodes instead of these raw archives).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'sukino-card sukino-post-card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a class="sukino-card-image" href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'medium' ); ?></a>
	<?php endif; ?>
	<h2 class="sukino-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
	<div class="sukino-card-text"><?php the_excerpt(); ?></div>
	<a class="sukino-card-link" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read More', 'sukino-elementor' ); ?> &rarr;</a>
</article>
