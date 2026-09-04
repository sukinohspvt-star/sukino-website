<?php
/**
 * Dynamic shortcodes that pull from the theme's custom post types.
 *
 * Drop any of these into Elementor's free "Shortcode" widget on any page
 * and they render live, editor-managed content — so a page built once in
 * Elementor keeps updating itself as staff add/edit Services, Centres,
 * Team Members, Testimonials and FAQs from wp-admin, no redesign needed.
 *
 * Fields are read with ACF's get_field() when ACF is active, and fall
 * back to plain post_meta / defaults otherwise so the site still renders
 * something sensible without ACF installed.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sukino_field( $selector, $post_id, $default = '' ) {
	if ( function_exists( 'get_field' ) ) {
		$value = get_field( $selector, $post_id );
		return ( '' !== $value && null !== $value && false !== $value ) ? $value : $default;
	}
	$value = get_post_meta( $post_id, $selector, true );
	return $value ? $value : $default;
}

/**
 * [sukino_services limit="4" category="" columns="4"]
 */
function sukino_shortcode_services( $atts ) {
	$atts = shortcode_atts( array(
		'limit'    => -1,
		'category' => '',
		'columns'  => 4,
	), $atts, 'sukino_services' );

	$query_args = array(
		'post_type'      => 'sukino_service',
		'posts_per_page' => intval( $atts['limit'] ),
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	);
	if ( $atts['category'] ) {
		$query_args['tax_query'] = array(
			array(
				'taxonomy' => 'service_category',
				'field'    => 'slug',
				'terms'    => sanitize_title( $atts['category'] ),
			),
		);
	}

	$services = new WP_Query( $query_args );
	if ( ! $services->have_posts() ) {
		return '<p class="sukino-empty-state">' . esc_html__( 'Services will appear here once added in wp-admin.', 'sukino-elementor' ) . '</p>';
	}

	ob_start();
	printf( '<div class="sukino-grid sukino-services-grid" style="--sukino-cols:%d;">', intval( $atts['columns'] ) );
	while ( $services->have_posts() ) {
		$services->the_post();
		$id          = get_the_ID();
		$icon        = sukino_field( 'icon', $id );
		$short_desc  = sukino_field( 'short_description', $id, get_the_excerpt() );
		$cta_label   = sukino_field( 'cta_label', $id, __( 'Learn More', 'sukino-elementor' ) );
		?>
		<div class="sukino-card sukino-service-card">
			<?php if ( $icon ) : ?>
				<div class="sukino-card-icon"><img src="<?php echo esc_url( $icon ); ?>" alt="" loading="lazy" /></div>
			<?php elseif ( has_post_thumbnail() ) : ?>
				<div class="sukino-card-icon"><?php the_post_thumbnail( 'thumbnail' ); ?></div>
			<?php endif; ?>
			<h3 class="sukino-card-title"><?php the_title(); ?></h3>
			<p class="sukino-card-text"><?php echo esc_html( wp_trim_words( $short_desc, 24 ) ); ?></p>
			<a class="sukino-card-link" href="<?php the_permalink(); ?>"><?php echo esc_html( $cta_label ); ?> &rarr;</a>
		</div>
		<?php
	}
	echo '</div>';
	wp_reset_postdata();
	return ob_get_clean();
}
add_shortcode( 'sukino_services', 'sukino_shortcode_services' );

/**
 * [sukino_locations limit="-1" columns="3"]
 */
function sukino_shortcode_locations( $atts ) {
	$atts = shortcode_atts( array(
		'limit'   => -1,
		'columns' => 3,
	), $atts, 'sukino_locations' );

	$locations = new WP_Query( array(
		'post_type'      => 'sukino_location',
		'posts_per_page' => intval( $atts['limit'] ),
		'orderby'        => 'menu_order title',
		'order'          => 'ASC',
	) );

	if ( ! $locations->have_posts() ) {
		return '<p class="sukino-empty-state">' . esc_html__( 'Centres will appear here once added in wp-admin.', 'sukino-elementor' ) . '</p>';
	}

	ob_start();
	printf( '<div class="sukino-grid sukino-locations-grid" style="--sukino-cols:%d;">', intval( $atts['columns'] ) );
	while ( $locations->have_posts() ) {
		$locations->the_post();
		$id      = get_the_ID();
		$address = sukino_field( 'address', $id );
		$phone   = sukino_field( 'phone', $id );
		$email   = sukino_field( 'email', $id );
		$hours   = sukino_field( 'working_hours', $id, '24/7' );
		$flag    = sukino_field( 'is_flagship', $id );
		?>
		<div class="sukino-card sukino-location-card">
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="sukino-card-image"><?php the_post_thumbnail( 'medium' ); ?></div>
			<?php endif; ?>
			<?php if ( $flag ) : ?><span class="sukino-badge"><?php esc_html_e( 'Flagship Centre', 'sukino-elementor' ); ?></span><?php endif; ?>
			<h3 class="sukino-card-title"><?php the_title(); ?></h3>
			<?php if ( $address ) : ?><p class="sukino-card-address"><?php echo nl2br( esc_html( $address ) ); ?></p><?php endif; ?>
			<ul class="sukino-card-meta">
				<?php if ( $phone ) : ?><li><a href="tel:<?php echo esc_attr( sukino_tel_link( $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></li><?php endif; ?>
				<?php if ( $email ) : ?><li><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></li><?php endif; ?>
				<li><?php echo esc_html( $hours ); ?></li>
			</ul>
		</div>
		<?php
	}
	echo '</div>';
	wp_reset_postdata();
	return ob_get_clean();
}
add_shortcode( 'sukino_locations', 'sukino_shortcode_locations' );

/**
 * [sukino_team limit="-1" department="" columns="4"]
 */
function sukino_shortcode_team( $atts ) {
	$atts = shortcode_atts( array(
		'limit'      => -1,
		'department' => '',
		'columns'    => 4,
	), $atts, 'sukino_team' );

	$members = new WP_Query( array(
		'post_type'      => 'sukino_team_member',
		'posts_per_page' => intval( $atts['limit'] ),
		'orderby'        => 'menu_order title',
		'order'          => 'ASC',
	) );

	if ( ! $members->have_posts() ) {
		return '<p class="sukino-empty-state">' . esc_html__( 'Team members will appear here once added in wp-admin.', 'sukino-elementor' ) . '</p>';
	}

	ob_start();
	printf( '<div class="sukino-grid sukino-team-grid" style="--sukino-cols:%d;">', intval( $atts['columns'] ) );
	while ( $members->have_posts() ) {
		$members->the_post();
		$id           = get_the_ID();
		$department   = sukino_field( 'department', $id );
		if ( $atts['department'] && sanitize_title( $department ) !== sanitize_title( $atts['department'] ) ) {
			continue;
		}
		$designation  = sukino_field( 'designation', $id );
		$qualifications = sukino_field( 'qualifications', $id );
		$languages    = sukino_field( 'languages_spoken', $id );
		?>
		<div class="sukino-card sukino-team-card">
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="sukino-card-image sukino-card-image--round"><?php the_post_thumbnail( 'medium' ); ?></div>
			<?php endif; ?>
			<h3 class="sukino-card-title"><?php the_title(); ?></h3>
			<?php if ( $designation ) : ?><p class="sukino-card-designation"><?php echo esc_html( $designation ); ?></p><?php endif; ?>
			<?php if ( $qualifications ) : ?><p class="sukino-card-qualifications"><?php echo esc_html( $qualifications ); ?></p><?php endif; ?>
			<?php if ( $languages ) : ?><p class="sukino-card-languages">🗣 <?php echo esc_html( $languages ); ?></p><?php endif; ?>
		</div>
		<?php
	}
	echo '</div>';
	wp_reset_postdata();
	return ob_get_clean();
}
add_shortcode( 'sukino_team', 'sukino_shortcode_team' );

/**
 * [sukino_testimonials limit="6" international_only="false" columns="3"]
 */
function sukino_shortcode_testimonials( $atts ) {
	$atts = shortcode_atts( array(
		'limit'               => 6,
		'international_only'  => 'false',
		'columns'             => 3,
	), $atts, 'sukino_testimonials' );

	$meta_query = array();
	if ( filter_var( $atts['international_only'], FILTER_VALIDATE_BOOLEAN ) ) {
		$meta_query[] = array(
			'key'   => 'is_international',
			'value' => '1',
		);
	}

	$testimonials = new WP_Query( array(
		'post_type'      => 'sukino_testimonial',
		'posts_per_page' => intval( $atts['limit'] ),
		'meta_query'     => $meta_query,
		'orderby'        => 'date',
		'order'          => 'DESC',
	) );

	if ( ! $testimonials->have_posts() ) {
		return '<p class="sukino-empty-state">' . esc_html__( 'Testimonials will appear here once added in wp-admin.', 'sukino-elementor' ) . '</p>';
	}

	ob_start();
	printf( '<div class="sukino-grid sukino-testimonials-grid" style="--sukino-cols:%d;">', intval( $atts['columns'] ) );
	while ( $testimonials->have_posts() ) {
		$testimonials->the_post();
		$id       = get_the_ID();
		$name     = sukino_field( 'patient_name', $id, get_the_title() );
		$relation = sukino_field( 'relation', $id );
		$country  = sukino_field( 'country', $id );
		$rating   = intval( sukino_field( 'rating', $id, 5 ) );
		?>
		<div class="sukino-card sukino-testimonial-card">
			<div class="sukino-stars" aria-label="<?php echo esc_attr( $rating . ' out of 5' ); ?>"><?php echo esc_html( str_repeat( '★', max( 0, min( 5, $rating ) ) ) . str_repeat( '☆', 5 - max( 0, min( 5, $rating ) ) ) ); ?></div>
			<blockquote class="sukino-quote"><?php the_content(); ?></blockquote>
			<div class="sukino-testimonial-author">
				<?php if ( has_post_thumbnail() ) : ?><span class="sukino-avatar"><?php the_post_thumbnail( 'thumbnail' ); ?></span><?php endif; ?>
				<span class="sukino-author-name"><?php echo esc_html( $name ); ?></span>
				<?php if ( $relation || $country ) : ?>
					<span class="sukino-author-meta"><?php echo esc_html( trim( $relation . ( $country ? ' · ' . $country : '' ), ' ·' ) ); ?></span>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
	echo '</div>';
	wp_reset_postdata();
	return ob_get_clean();
}
add_shortcode( 'sukino_testimonials', 'sukino_shortcode_testimonials' );

/**
 * [sukino_faqs category="international-patients" limit="-1"]
 */
function sukino_shortcode_faqs( $atts ) {
	$atts = shortcode_atts( array(
		'category' => '',
		'limit'    => -1,
	), $atts, 'sukino_faqs' );

	$query_args = array(
		'post_type'      => 'sukino_faq',
		'posts_per_page' => intval( $atts['limit'] ),
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	);
	if ( $atts['category'] ) {
		$query_args['tax_query'] = array(
			array(
				'taxonomy' => 'faq_category',
				'field'    => 'slug',
				'terms'    => sanitize_title( $atts['category'] ),
			),
		);
	}

	$faqs = new WP_Query( $query_args );
	if ( ! $faqs->have_posts() ) {
		return '<p class="sukino-empty-state">' . esc_html__( 'FAQs will appear here once added in wp-admin.', 'sukino-elementor' ) . '</p>';
	}

	ob_start();
	echo '<div class="sukino-accordion">';
	$i = 0;
	while ( $faqs->have_posts() ) {
		$faqs->the_post();
		$i++;
		?>
		<details class="sukino-accordion-item" <?php echo 1 === $i ? 'open' : ''; ?>>
			<summary class="sukino-accordion-question"><?php the_title(); ?></summary>
			<div class="sukino-accordion-answer"><?php the_content(); ?></div>
		</details>
		<?php
	}
	echo '</div>';
	wp_reset_postdata();
	return ob_get_clean();
}
add_shortcode( 'sukino_faqs', 'sukino_shortcode_faqs' );

/**
 * [sukino_stat number="20+" label="Countries Served"]
 * A single reusable stat/counter box — handy for "why choose us" rows.
 */
function sukino_shortcode_stat( $atts ) {
	$atts = shortcode_atts( array(
		'number' => '',
		'label'  => '',
	), $atts, 'sukino_stat' );

	return sprintf(
		'<div class="sukino-stat"><span class="sukino-stat-number">%s</span><span class="sukino-stat-label">%s</span></div>',
		esc_html( $atts['number'] ),
		esc_html( $atts['label'] )
	);
}
add_shortcode( 'sukino_stat', 'sukino_shortcode_stat' );
