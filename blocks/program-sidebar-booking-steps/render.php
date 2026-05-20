<?php
/**
 * Server render: Program sidebar — Steps to booking (per-page post meta).
 *
 * @package Queens_Botanical_Block
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner blocks (unused).
 * @var WP_Block $block      Block instance.
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

$post_id = get_queried_object_id();
if ( ! $post_id || get_post_type( $post_id ) !== 'page' ) {
	return;
}

$meta    = qbb_get_program_booking_meta( $post_id );
$heading = $meta['qbb_program_booking_heading'];
$body    = $meta['qbb_program_booking_description'];
$steps   = $meta['qbb_program_booking_steps'];

$visible_steps = array();
foreach ( $steps as $step ) {
	if ( ( $step['title'] ?? '' ) !== '' || ( $step['description'] ?? '' ) !== '' ) {
		$visible_steps[] = $step;
	}
}

if ( $heading === '' && $body === '' && $visible_steps === array() ) {
	return;
}

$last_index = count( $visible_steps ) - 1;

$wrapper_attrs = get_block_wrapper_attributes(
	array(
		'class' => 'qbb-program-booking-steps',
	)
);
?>
<div <?php echo $wrapper_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="qbb-program-booking-steps__card">
		<?php if ( $heading !== '' || $body !== '' ) : ?>
			<div class="qbb-program-booking-steps__intro">
				<?php if ( $heading !== '' ) : ?>
					<p class="qbb-program-booking-steps__heading"><?php echo esc_html( $heading ); ?></p>
				<?php endif; ?>
				<?php if ( $body !== '' ) : ?>
					<p class="qbb-program-booking-steps__description"><?php echo esc_html( $body ); ?></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( $visible_steps !== array() ) : ?>
			<ol class="qbb-program-booking-steps__list" aria-label="<?php echo esc_attr( $heading !== '' ? $heading : __( 'Booking steps', 'queens-botanical-block' ) ); ?>">
				<?php
				foreach ( $visible_steps as $index => $step ) :
					$step_title = $step['title'] ?? '';
					$step_body  = $step['description'] ?? '';
					$is_last    = ( (int) $index === $last_index );
					?>
					<li class="qbb-program-booking-steps__step<?php echo $is_last ? ' qbb-program-booking-steps__step--last' : ''; ?>">
						<div class="qbb-program-booking-steps__rail" aria-hidden="true">
							<span class="qbb-program-booking-steps__dot"></span>
							<?php if ( ! $is_last ) : ?>
								<span class="qbb-program-booking-steps__line"></span>
							<?php endif; ?>
						</div>
						<div class="qbb-program-booking-steps__content">
							<?php if ( $step_title !== '' ) : ?>
								<p class="qbb-program-booking-steps__step-title"><?php echo esc_html( $step_title ); ?></p>
							<?php endif; ?>
							<?php if ( $step_body !== '' ) : ?>
								<p class="qbb-program-booking-steps__step-description"><?php echo esc_html( $step_body ); ?></p>
							<?php endif; ?>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>
	</div>
</div>
