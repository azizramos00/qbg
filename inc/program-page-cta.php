<?php
/**
 * Per-page Programs sidebar CTA fields (post meta).
 *
 * @package Queens_Botanical_Block
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

/**
 * Meta keys for the Programs sidebar CTA card.
 *
 * @return array<string, string>
 */
function qbb_program_cta_meta_keys(): array {
	return array(
		'qbb_program_cta_heading'          => 'string',
		'qbb_program_cta_description'      => 'string',
		'qbb_program_cta_primary_label'    => 'string',
		'qbb_program_cta_primary_url'      => 'string',
		'qbb_program_cta_secondary_label'  => 'string',
		'qbb_program_cta_secondary_url'    => 'string',
	);
}

/**
 * Register post meta for pages.
 */
function qbb_register_program_cta_meta(): void {
	foreach ( qbb_program_cta_meta_keys() as $key => $type ) {
		register_post_meta(
			'page',
			$key,
			array(
				'type'              => $type,
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'qbb_sanitize_program_cta_meta',
				'auth_callback'     => static function (): bool {
					return current_user_can( 'edit_pages' );
				},
			)
		);
	}

	register_post_meta(
		'page',
		'qbb_program_cta_secondary_enabled',
		array(
			'type'              => 'boolean',
			'single'            => true,
			'show_in_rest'      => true,
			'default'           => false,
			'sanitize_callback' => static function ( $value ): bool {
				return (bool) $value;
			},
			'auth_callback'     => static function (): bool {
				return current_user_can( 'edit_pages' );
			},
		)
	);
}
add_action( 'init', 'qbb_register_program_cta_meta' );

/**
 * @param mixed  $value   Raw value.
 * @param string $key     Meta key.
 * @param string $subtype Object subtype.
 * @return string
 */
function qbb_sanitize_program_cta_meta( $value, string $key, string $subtype ): string {
	unset( $subtype );

	$value = is_string( $value ) ? trim( $value ) : '';

	if ( str_ends_with( $key, '_url' ) && $value !== '' ) {
		return esc_url_raw( $value );
	}

	return sanitize_text_field( $value );
}

/**
 * Whether the secondary CTA button is enabled for a page.
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function qbb_program_cta_secondary_enabled( int $post_id ): bool {
	$flag = get_post_meta( $post_id, 'qbb_program_cta_secondary_enabled', true );
	if ( $flag === true || $flag === '1' || $flag === 1 ) {
		return true;
	}

	// Pages saved before the toggle existed: show second button when a URL is set.
	$legacy_url = get_post_meta( $post_id, 'qbb_program_cta_secondary_url', true );
	return is_string( $legacy_url ) && $legacy_url !== '';
}

/**
 * @param int $post_id Post ID.
 * @return array<string, string|bool>
 */
function qbb_get_program_cta_meta( int $post_id ): array {
	$out = array();
	foreach ( array_keys( qbb_program_cta_meta_keys() ) as $key ) {
		$stored = get_post_meta( $post_id, $key, true );
		$out[ $key ] = is_string( $stored ) ? $stored : '';
	}
	$out['qbb_program_cta_secondary_enabled'] = qbb_program_cta_secondary_enabled( $post_id );
	return $out;
}

/**
 * Enqueue document sidebar panel for Programs CTA fields.
 */
function qbb_enqueue_program_cta_editor_panel(): void {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || $screen->base !== 'post' || $screen->post_type !== 'page' ) {
		return;
	}

	$asset = get_template_directory() . '/blocks/program-sidebar-cta/editor-panel.asset.php';
	$deps  = array( 'wp-plugins', 'wp-edit-post', 'wp-components', 'wp-data', 'wp-element', 'wp-i18n' );
	$ver   = '1.0.0';
	if ( is_readable( $asset ) ) {
		$asset_config = require $asset;
		$deps         = $asset_config['dependencies'];
		$ver          = $asset_config['version'];
	}

	wp_enqueue_script(
		'queens-botanical-block-program-sidebar-helpers',
		get_template_directory_uri() . '/blocks/program-sidebar/editor-helpers.js',
		array(),
		'1.0.0',
		true
	);

	wp_enqueue_script(
		'queens-botanical-block-program-cta-panel',
		get_template_directory_uri() . '/blocks/program-sidebar-cta/editor-panel.js',
		array_merge( $deps, array( 'queens-botanical-block-program-sidebar-helpers' ) ),
		$ver,
		true
	);
}
add_action( 'enqueue_block_editor_assets', 'qbb_enqueue_program_cta_editor_panel' );
