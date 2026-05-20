<?php
/**
 * Server render: Program description (per-page post meta).
 *
 * @package Queens_Botanical_Block
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner blocks (unused).
 * @var WP_Block $block      Block instance.
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

$post_id = qbb_program_block_post_id( $block );
if ( $post_id <= 0 ) {
	return;
}

$inner = qbb_render_program_description_inner( $post_id );
if ( $inner === '' ) {
	return;
}

qbb_mark_program_description_rendered();

$wrapper_attrs = get_block_wrapper_attributes(
	array(
		'class' => 'qbb-program-description',
	)
);
?>
<div <?php echo $wrapper_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php echo $inner; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</div>
