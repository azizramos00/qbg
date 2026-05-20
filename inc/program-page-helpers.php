<?php
/**
 * Shared helpers for Programs template pages and blocks.
 *
 * @package Queens_Botanical_Block
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

/**
 * theme.json customTemplates[].name for templates/programs.html
 */
const QBB_PROGRAMS_TEMPLATE_SLUG = 'programs';

/**
 * @param string $template Template slug from the editor or post meta.
 */
function qbb_is_programs_template_slug( string $template ): bool {
	if ( $template === '' ) {
		return false;
	}
	if ( $template === QBB_PROGRAMS_TEMPLATE_SLUG ) {
		return true;
	}
	$parts = explode( '/', $template );
	return end( $parts ) === QBB_PROGRAMS_TEMPLATE_SLUG;
}

/**
 * @param int $post_id Page ID.
 */
function qbb_is_programs_page( int $post_id ): bool {
	if ( $post_id <= 0 || get_post_type( $post_id ) !== 'page' ) {
		return false;
	}
	$template = get_page_template_slug( $post_id );
	if ( is_string( $template ) && qbb_is_programs_template_slug( $template ) ) {
		return true;
	}
	// Block themes may store template on the post object during render.
	$block_template = get_post_meta( $post_id, '_wp_page_template', true );
	return is_string( $block_template ) && qbb_is_programs_template_slug( $block_template );
}

/**
 * Resolve the page ID for per-page Programs blocks.
 *
 * @param WP_Block $block Block instance.
 */
function qbb_program_block_post_id( WP_Block $block ): int {
	if ( ! empty( $block->context['postId'] ) ) {
		return (int) $block->context['postId'];
	}
	$post_id = get_queried_object_id();
	return $post_id > 0 ? $post_id : 0;
}
