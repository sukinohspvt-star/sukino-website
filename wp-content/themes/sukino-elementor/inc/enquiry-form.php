<?php
/**
 * International Patient & Family enquiry form.
 *
 * [sukino_international_patient_form] renders a plain HTML form (works
 * with the free version of Elementor's Shortcode widget — no Elementor
 * Pro Forms dependency). Submissions are verified with a nonce, sanitized,
 * stored as a private "IP Enquiry" post so staff always have a record in
 * wp-admin, and emailed to the international patients desk plus an
 * auto-reply confirmation to the family.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sukino_shortcode_ip_form( $atts ) {
	$atts = shortcode_atts( array(
		'heading' => __( 'Plan Your Care Journey to India', 'sukino-elementor' ),
	), $atts, 'sukino_international_patient_form' );

	$services = get_posts( array(
		'post_type'      => 'sukino_service',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	) );
	$locations = get_posts( array(
		'post_type'      => 'sukino_location',
		'posts_per_page' => -1,
		'orderby'        => 'menu_order title',
		'order'          => 'ASC',
	) );

	ob_start();

	if ( isset( $_GET['sukino_enquiry'] ) ) {
		if ( 'success' === $_GET['sukino_enquiry'] ) {
			echo '<div class="sukino-form-notice sukino-form-notice--success">' . esc_html__( 'Thank you. Your enquiry has been received — our International Patients desk will reach out within one business day.', 'sukino-elementor' ) . '</div>';
		} elseif ( 'error' === $_GET['sukino_enquiry'] ) {
			echo '<div class="sukino-form-notice sukino-form-notice--error">' . esc_html__( 'Something went wrong. Please check the required fields and try again, or WhatsApp us directly.', 'sukino-elementor' ) . '</div>';
		}
	}
	?>
	<form class="sukino-ip-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<?php if ( $atts['heading'] ) : ?><h3 class="sukino-ip-form-heading"><?php echo esc_html( $atts['heading'] ); ?></h3><?php endif; ?>
		<input type="hidden" name="action" value="sukino_ip_enquiry" />
		<?php wp_nonce_field( 'sukino_ip_enquiry', 'sukino_ip_enquiry_nonce' ); ?>
		<input type="text" name="sukino_website" class="sukino-hp-field" tabindex="-1" autocomplete="off" aria-hidden="true" />

		<div class="sukino-form-row">
			<label for="sukino_full_name"><?php esc_html_e( 'Full Name', 'sukino-elementor' ); ?> *</label>
			<input type="text" id="sukino_full_name" name="full_name" required />
		</div>

		<div class="sukino-form-row sukino-form-row--half">
			<div>
				<label for="sukino_email"><?php esc_html_e( 'Email', 'sukino-elementor' ); ?> *</label>
				<input type="email" id="sukino_email" name="email" required />
			</div>
			<div>
				<label for="sukino_phone"><?php esc_html_e( 'Phone / WhatsApp', 'sukino-elementor' ); ?> *</label>
				<input type="tel" id="sukino_phone" name="phone" required />
			</div>
		</div>

		<div class="sukino-form-row sukino-form-row--half">
			<div>
				<label for="sukino_country"><?php esc_html_e( 'Country', 'sukino-elementor' ); ?> *</label>
				<input type="text" id="sukino_country" name="country" required />
			</div>
			<div>
				<label for="sukino_relation"><?php esc_html_e( 'Relation to Patient', 'sukino-elementor' ); ?></label>
				<input type="text" id="sukino_relation" name="patient_relation" placeholder="<?php esc_attr_e( 'e.g. Son, Daughter, Self', 'sukino-elementor' ); ?>" />
			</div>
		</div>

		<div class="sukino-form-row sukino-form-row--half">
			<div>
				<label for="sukino_service"><?php esc_html_e( 'Service of Interest', 'sukino-elementor' ); ?></label>
				<select id="sukino_service" name="service">
					<option value=""><?php esc_html_e( 'Select a service', 'sukino-elementor' ); ?></option>
					<?php foreach ( $services as $service ) : ?>
						<option value="<?php echo esc_attr( $service->post_title ); ?>"><?php echo esc_html( $service->post_title ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div>
				<label for="sukino_centre"><?php esc_html_e( 'Preferred Centre', 'sukino-elementor' ); ?></label>
				<select id="sukino_centre" name="preferred_centre">
					<option value=""><?php esc_html_e( 'No preference', 'sukino-elementor' ); ?></option>
					<?php foreach ( $locations as $location ) : ?>
						<option value="<?php echo esc_attr( $location->post_title ); ?>"><?php echo esc_html( $location->post_title ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>

		<div class="sukino-form-row">
			<label for="sukino_arrival"><?php esc_html_e( 'Expected Arrival in India (approx.)', 'sukino-elementor' ); ?></label>
			<input type="text" id="sukino_arrival" name="arrival" placeholder="<?php esc_attr_e( 'e.g. Within 2 weeks', 'sukino-elementor' ); ?>" />
		</div>

		<div class="sukino-form-row">
			<label for="sukino_message"><?php esc_html_e( 'Tell us about the patient\'s condition and how we can help', 'sukino-elementor' ); ?></label>
			<textarea id="sukino_message" name="message" rows="4"></textarea>
		</div>

		<button type="submit" class="sukino-btn sukino-btn--primary"><?php esc_html_e( 'Submit Enquiry', 'sukino-elementor' ); ?></button>
		<p class="sukino-form-privacy"><?php esc_html_e( 'Your information is used only to respond to your enquiry and is never shared with third parties.', 'sukino-elementor' ); ?></p>
	</form>
	<?php
	return ob_get_clean();
}
add_shortcode( 'sukino_international_patient_form', 'sukino_shortcode_ip_form' );

function sukino_handle_ip_enquiry() {
	if ( ! isset( $_POST['sukino_ip_enquiry_nonce'] ) || ! wp_verify_nonce( $_POST['sukino_ip_enquiry_nonce'], 'sukino_ip_enquiry' ) ) {
		wp_safe_redirect( add_query_arg( 'sukino_enquiry', 'error', wp_get_referer() ?: home_url( '/' ) ) );
		exit;
	}

	// Honeypot: bots fill every field, humans never see this one.
	if ( ! empty( $_POST['sukino_website'] ) ) {
		wp_safe_redirect( add_query_arg( 'sukino_enquiry', 'success', wp_get_referer() ?: home_url( '/' ) ) );
		exit;
	}

	$full_name = isset( $_POST['full_name'] ) ? sanitize_text_field( wp_unslash( $_POST['full_name'] ) ) : '';
	$email     = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$phone     = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	$country   = isset( $_POST['country'] ) ? sanitize_text_field( wp_unslash( $_POST['country'] ) ) : '';

	if ( ! $full_name || ! is_email( $email ) || ! $phone || ! $country ) {
		wp_safe_redirect( add_query_arg( 'sukino_enquiry', 'error', wp_get_referer() ?: home_url( '/' ) ) );
		exit;
	}

	$relation = isset( $_POST['patient_relation'] ) ? sanitize_text_field( wp_unslash( $_POST['patient_relation'] ) ) : '';
	$service  = isset( $_POST['service'] ) ? sanitize_text_field( wp_unslash( $_POST['service'] ) ) : '';
	$centre   = isset( $_POST['preferred_centre'] ) ? sanitize_text_field( wp_unslash( $_POST['preferred_centre'] ) ) : '';
	$arrival  = isset( $_POST['arrival'] ) ? sanitize_text_field( wp_unslash( $_POST['arrival'] ) ) : '';
	$message  = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	$post_id = wp_insert_post( array(
		'post_type'   => 'sukino_ip_enquiry',
		'post_title'  => sprintf( '%s (%s) — %s', $full_name, $country, date_i18n( 'd M Y H:i' ) ),
		'post_status' => 'publish',
	) );

	if ( $post_id && ! is_wp_error( $post_id ) ) {
		update_post_meta( $post_id, 'sukino_enquiry_email', $email );
		update_post_meta( $post_id, 'sukino_enquiry_phone', $phone );
		update_post_meta( $post_id, 'sukino_enquiry_country', $country );
		update_post_meta( $post_id, 'sukino_enquiry_patient_relation', $relation );
		update_post_meta( $post_id, 'sukino_enquiry_service', $service );
		update_post_meta( $post_id, 'sukino_enquiry_preferred_centre', $centre );
		update_post_meta( $post_id, 'sukino_enquiry_arrival', $arrival );
		update_post_meta( $post_id, 'sukino_enquiry_message', $message );
		update_post_meta( $post_id, 'sukino_enquiry_submitted', current_time( 'mysql' ) );
	}

	$notify_to = get_theme_mod( 'sukino_ip_email', get_option( 'admin_email' ) );
	$subject   = sprintf( '[Sukino] New International Patient Enquiry — %s (%s)', $full_name, $country );
	$body      = "A new International Patients & Family enquiry has been submitted:\n\n"
		. "Name: {$full_name}\n"
		. "Email: {$email}\n"
		. "Phone/WhatsApp: {$phone}\n"
		. "Country: {$country}\n"
		. "Relation to patient: {$relation}\n"
		. "Service of interest: {$service}\n"
		. "Preferred centre: {$centre}\n"
		. "Expected arrival: {$arrival}\n\n"
		. "Message:\n{$message}\n";
	wp_mail( $notify_to, $subject, $body );

	$reply_subject = __( 'We\'ve received your enquiry — Sukino Healthcare', 'sukino-elementor' );
	$reply_body    = sprintf(
		"Dear %s,\n\nThank you for reaching out to Sukino Healthcare's International Patients & Family desk. We have received your enquiry and a care coordinator will contact you within one business day to discuss the next steps, including a personalised care plan, travel guidance and accommodation options for your family.\n\nIf your matter is urgent, please WhatsApp us at %s.\n\nWarm regards,\nSukino Healthcare — International Patients Team",
		$full_name,
		get_theme_mod( 'sukino_whatsapp', '+91 95919 45233' )
	);
	wp_mail( $email, $reply_subject, $reply_body );

	$redirect = ! empty( $_POST['_wp_http_referer'] ) ? wp_unslash( $_POST['_wp_http_referer'] ) : home_url( '/' );
	wp_safe_redirect( add_query_arg( 'sukino_enquiry', 'success', $redirect ) . '#sukino-enquiry-form' );
	exit;
}
add_action( 'admin_post_nopriv_sukino_ip_enquiry', 'sukino_handle_ip_enquiry' );
add_action( 'admin_post_sukino_ip_enquiry', 'sukino_handle_ip_enquiry' );
