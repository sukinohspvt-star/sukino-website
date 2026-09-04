<?php
/**
 * Single post / single CPT item fallback template. Elementor's own
 * Theme Builder (Pro) can override this per post type if installed;
 * without Pro, this renders a clean single view using the same field
 * helpers as the shortcodes so Service/Centre/Team detail pages stay
 * on-brand even outside the page builder.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();
	$post_type = get_post_type();
	?>
	<main id="primary" class="site-main sukino-container sukino-single">
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<header class="sukino-single-header">
				<h1 class="sukino-page-title"><?php the_title(); ?></h1>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<div class="sukino-single-image"><?php the_post_thumbnail( 'large' ); ?></div>
			<?php endif; ?>

			<?php if ( 'sukino_service' === $post_type ) : ?>
				<?php $features = function_exists( 'get_field' ) ? get_field( 'key_features' ) : array(); ?>
				<?php if ( $features ) : ?>
					<ul class="sukino-feature-list">
						<?php foreach ( $features as $row ) : ?>
							<li><?php echo esc_html( $row['feature'] ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			<?php elseif ( 'sukino_location' === $post_type ) : ?>
				<?php
				$address = sukino_field( 'address', get_the_ID() );
				$phone   = sukino_field( 'phone', get_the_ID() );
				$email   = sukino_field( 'email', get_the_ID() );
				$map     = sukino_field( 'map_embed_url', get_the_ID() );
				?>
				<div class="sukino-location-detail">
					<?php if ( $address ) : ?><p><strong><?php esc_html_e( 'Address:', 'sukino-elementor' ); ?></strong> <?php echo nl2br( esc_html( $address ) ); ?></p><?php endif; ?>
					<?php if ( $phone ) : ?><p><strong><?php esc_html_e( 'Phone:', 'sukino-elementor' ); ?></strong> <a href="tel:<?php echo esc_attr( sukino_tel_link( $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></p><?php endif; ?>
					<?php if ( $email ) : ?><p><strong><?php esc_html_e( 'Email:', 'sukino-elementor' ); ?></strong> <a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></p><?php endif; ?>
					<?php if ( $map ) : ?>
						<div class="sukino-map-embed"><iframe src="<?php echo esc_url( $map ); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="<?php the_title_attribute(); ?>"></iframe></div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="sukino-single-content">
				<?php the_content(); ?>
			</div>
		</article>
	</main>
	<?php
endwhile;

get_footer();
