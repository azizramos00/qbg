<?php
/**
 * Per-page Programs intro description (post meta).
 *
 * @package Queens_Botanical_Block
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

/**
 * @var bool
 */
$GLOBALS['qbb_program_description_rendered'] = false;

/**
 * Mark description output as rendered (avoids duplicate fallback injection).
 */
function qbb_mark_program_description_rendered(): void {
	$GLOBALS['qbb_program_description_rendered'] = true;
}

/**
 * @return bool
 */
function qbb_program_description_was_rendered(): bool {
	return ! empty( $GLOBALS['qbb_program_description_rendered'] );
}

/**
 * Register post meta for pages.
 */
function qbb_register_program_description_meta(): void {
	register_post_meta(
		'page',
		'qbb_program_description',
		array(
			'type'              => 'string',
			'single'            => true,
			'show_in_rest'      => true,
			'default'           => '',
			'sanitize_callback' => static function ( $value ): string {
				return is_string( $value ) ? sanitize_textarea_field( trim( $value ) ) : '';
			},
			'auth_callback'     => static function ( bool $allowed, string $meta_key, int $post_id ): bool {
				unset( $allowed, $meta_key );
				return current_user_can( 'edit_post', $post_id );
			},
		)
	);
}
add_action( 'init', 'qbb_register_program_description_meta' );

/**
 * @param int $post_id Post ID.
 * @return string
 */
function qbb_get_program_description( int $post_id ): string {
	$stored = get_post_meta( $post_id, 'qbb_program_description', true );
	return is_string( $stored ) ? $stored : '';
}

/**
 * @param int $post_id Post ID.
 * @return string Inner HTML or empty string.
 */
function qbb_render_program_description_inner( int $post_id ): string {
	if ( $post_id <= 0 || ! qbb_is_programs_page( $post_id ) ) {
		return '';
	}

	$description = qbb_get_program_description( $post_id );
	if ( $description === '' ) {
		return '';
	}

	return sprintf(
		'<div class="qbb-program-description__inner"><p class="qbb-program-description__text">%s</p></div>',
		esc_html( $description )
	);
}

/**
 * @param int $post_id Post ID.
 * @return string Full block HTML or empty string.
 */
function qbb_render_program_description_html( int $post_id ): string {
	$inner = qbb_render_program_description_inner( $post_id );
	if ( $inner === '' ) {
		return '';
	}

	qbb_mark_program_description_rendered();

	return sprintf(
		'<div class="wp-block-queens-botanical-block-program-description qbb-program-description">%s</div>',
		$inner
	);
}

/**
 * Fallback: prepend description before the body group when the template block is missing.
 *
 * @param string $content Block HTML.
 * @param array  $block   Parsed block.
 * @return string
 */
function qbb_prepend_program_description_before_body( string $content, array $block ): string {
	if ( ( $block['blockName'] ?? '' ) !== 'core/group' ) {
		return $content;
	}

	$class = $block['attrs']['className'] ?? '';
	if ( ! is_string( $class ) || ! str_contains( $class, 'site-programs__body' ) ) {
		return $content;
	}

	if ( qbb_program_description_was_rendered() ) {
		return $content;
	}

	$post_id = get_queried_object_id();
	$inner   = qbb_render_program_description_html( $post_id );
	if ( $inner === '' ) {
		return $content;
	}

	$section = sprintf(
		'<div class="wp-block-group site-container--full-width site-programs__description is-layout-flow">%s</div>',
		$inner
	);

	return $section . $content;
}
add_filter( 'render_block', 'qbb_prepend_program_description_before_body', 10, 2 );

/**
 * Enqueue document sidebar panel for program description.
 */
function qbb_enqueue_program_description_editor_panel(): void {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || $screen->base !== 'post' || $screen->post_type !== 'page' ) {
		return;
	}

	$asset = get_template_directory() . '/blocks/program-description/editor-panel.asset.php';
	$deps  = array( 'wp-plugins', 'wp-edit-post', 'wp-components', 'wp-data', 'wp-element', 'wp-i18n' );
	$ver   = '1.0.1';
	if ( is_readable( $asset ) ) {
		$asset_config = require $asset;
		$deps         = $asset_config['dependencies'];
		$ver          = $asset_config['version'];
	}

	wp_enqueue_script(
		'queens-botanical-block-program-sidebar-helpers',
		get_template_directory_uri() . '/blocks/program-sidebar/editor-helpers.js',
		array(),
		'1.0.1',
		true
	);

	wp_enqueue_script(
		'queens-botanical-block-program-description-panel',
		get_template_directory_uri() . '/blocks/program-description/editor-panel.js',
		array_merge( $deps, array( 'queens-botanical-block-program-sidebar-helpers' ) ),
		$ver,
		true
	);
}
add_action( 'enqueue_block_editor_assets', 'qbb_enqueue_program_description_editor_panel' );
