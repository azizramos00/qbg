<?php
/**
 * Server render: Program sidebar CTA (per-page post meta).
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

$meta = qbb_get_program_cta_meta( $post_id );

$heading = $meta['qbb_program_cta_heading'];
$body    = $meta['qbb_program_cta_description'];
$primary_label = $meta['qbb_program_cta_primary_label'];
$primary_url   = $meta['qbb_program_cta_primary_url'];
$secondary_label   = $meta['qbb_program_cta_secondary_label'];
$secondary_url     = $meta['qbb_program_cta_secondary_url'];
$secondary_enabled = ! empty( $meta['qbb_program_cta_secondary_enabled'] );

if ( $heading === '' && $body === '' && $primary_url === '' && $secondary_url === '' ) {
	return;
}

$wrapper_attrs = get_block_wrapper_attributes(
	array(
		'class' => 'qbb-program-sidebar-cta',
	)
);
?>
<div <?php echo $wrapper_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="qbb-program-sidebar-cta__card">
		<?php if ( $heading !== '' ) : ?>
			<p class="qbb-program-sidebar-cta__heading"><?php echo esc_html( $heading ); ?></p>
		<?php endif; ?>

		<?php if ( $body !== '' ) : ?>
			<p class="qbb-program-sidebar-cta__body"><?php echo esc_html( $body ); ?></p>
		<?php endif; ?>

		<?php if ( $primary_url !== '' || $secondary_url !== '' ) : ?>
			<div class="qbb-program-sidebar-cta__buttons wp-block-buttons">
				<?php if ( $primary_url !== '' ) : ?>
					<div class="wp-block-button">
						<a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( $primary_url ); ?>">
							<?php echo esc_html( $primary_label !== '' ? $primary_label : __( 'Learn more', 'queens-botanical-block' ) ); ?>
						</a>
					</div>
				<?php endif; ?>
				<?php if ( $secondary_enabled && $secondary_url !== '' ) : ?>
					<div class="wp-block-button is-style-outline">
						<a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( $secondary_url ); ?>">
							<?php echo esc_html( $secondary_label !== '' ? $secondary_label : __( 'Learn more', 'queens-botanical-block' ) ); ?>
						</a>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</div>
