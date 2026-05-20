<?php
/**
 * Per-page Programs sidebar “Steps to booking” fields (post meta).
 *
 * @package Queens_Botanical_Block
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

/**
 * @return array<string, string>
 */
function qbb_program_booking_meta_keys(): array {
	return array(
		'qbb_program_booking_heading'     => 'string',
		'qbb_program_booking_description' => 'string',
	);
}

/**
 * Register post meta for pages.
 */
function qbb_register_program_booking_meta(): void {
	foreach ( qbb_program_booking_meta_keys() as $key => $type ) {
		register_post_meta(
			'page',
			$key,
			array(
				'type'              => $type,
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'qbb_sanitize_program_booking_scalar',
				'auth_callback'     => static function (): bool {
					return current_user_can( 'edit_pages' );
				},
			)
		);
	}

	register_post_meta(
		'page',
		'qbb_program_booking_steps',
		array(
			'type'              => 'array',
			'single'            => true,
			'show_in_rest'      => array(
				'schema' => array(
					'type'  => 'array',
					'items' => array(
						'type'       => 'object',
						'properties' => array(
							'title'       => array( 'type' => 'string' ),
							'description' => array( 'type' => 'string' ),
						),
					),
				),
			),
			'default'           => array(),
			'sanitize_callback' => 'qbb_sanitize_program_booking_steps',
			'auth_callback'     => static function (): bool {
				return current_user_can( 'edit_pages' );
			},
		)
	);
}
add_action( 'init', 'qbb_register_program_booking_meta' );

/**
 * @param mixed  $value   Raw value.
 * @param string $key     Meta key.
 * @param string $subtype Object subtype.
 * @return string
 */
function qbb_sanitize_program_booking_scalar( $value, string $key, string $subtype ): string {
	unset( $key, $subtype );
	return is_string( $value ) ? sanitize_text_field( trim( $value ) ) : '';
}

/**
 * @param mixed $value Raw steps array.
 * @return array<int, array{title: string, description: string}>
 */
function qbb_sanitize_program_booking_steps( $value ): array {
	if ( ! is_array( $value ) ) {
		return array();
	}

	$out = array();
	foreach ( $value as $step ) {
		if ( ! is_array( $step ) ) {
			continue;
		}
		$title = isset( $step['title'] ) ? sanitize_text_field( (string) $step['title'] ) : '';
		$desc  = isset( $step['description'] ) ? sanitize_textarea_field( (string) $step['description'] ) : '';
		if ( $title === '' && $desc === '' ) {
			continue;
		}
		$out[] = array(
			'title'       => $title,
			'description' => $desc,
		);
	}

	return $out;
}

/**
 * @param int $post_id Post ID.
 * @return array{
 *   qbb_program_booking_heading: string,
 *   qbb_program_booking_description: string,
 *   qbb_program_booking_steps: array<int, array{title: string, description: string}>
 * }
 */
function qbb_get_program_booking_meta( int $post_id ): array {
	$heading = get_post_meta( $post_id, 'qbb_program_booking_heading', true );
	$body    = get_post_meta( $post_id, 'qbb_program_booking_description', true );
	$steps   = get_post_meta( $post_id, 'qbb_program_booking_steps', true );

	if ( ! is_array( $steps ) ) {
		$steps = array();
	}

	$normalized_steps = array();
	foreach ( $steps as $step ) {
		if ( ! is_array( $step ) ) {
			continue;
		}
		$normalized_steps[] = array(
			'title'       => isset( $step['title'] ) && is_string( $step['title'] ) ? $step['title'] : '',
			'description' => isset( $step['description'] ) && is_string( $step['description'] ) ? $step['description'] : '',
		);
	}

	return array(
		'qbb_program_booking_heading'     => is_string( $heading ) ? $heading : '',
		'qbb_program_booking_description' => is_string( $body ) ? $body : '',
		'qbb_program_booking_steps'       => $normalized_steps,
	);
}

/**
 * Enqueue document sidebar panel for booking steps fields.
 */
function qbb_enqueue_program_booking_editor_panel(): void {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || $screen->base !== 'post' || $screen->post_type !== 'page' ) {
		return;
	}

	$asset = get_template_directory() . '/blocks/program-sidebar-booking-steps/editor-panel.asset.php';
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
		'queens-botanical-block-program-booking-panel',
		get_template_directory_uri() . '/blocks/program-sidebar-booking-steps/editor-panel.js',
		array_merge( $deps, array( 'queens-botanical-block-program-sidebar-helpers' ) ),
		$ver,
		true
	);
}
add_action( 'enqueue_block_editor_assets', 'qbb_enqueue_program_booking_editor_panel' );
