<?php
/**
 * Queens Botanical Block Theme — block theme setup.
 *
 * @package Queens_Botanical_Block
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

/**
 * Component styles. Tokens must load before components that use var(--qbb-*).
 *
 * @return array<string, array{path: string, deps: string[]}>
 */
function qbb_style_components(): array {
	return array(
		'queens-botanical-block-tokens' => array(
			'path' => 'assets/css/components/tokens.css',
			'deps' => array( 'queens-botanical-block' ),
		),
		'queens-botanical-block-layout' => array(
			'path' => 'assets/css/components/layout.css',
			'deps' => array( 'queens-botanical-block', 'queens-botanical-block-tokens' ),
		),
		'queens-botanical-block-header' => array(
			'path' => 'assets/css/components/header.css',
			'deps' => array( 'queens-botanical-block', 'queens-botanical-block-tokens' ),
		),
		'queens-botanical-block-footer' => array(
			'path' => 'assets/css/components/footer.css',
			'deps' => array( 'queens-botanical-block', 'queens-botanical-block-tokens' ),
		),
		'queens-botanical-block-home' => array(
			'path' => 'assets/css/components/home.css',
			'deps' => array( 'queens-botanical-block', 'queens-botanical-block-tokens' ),
		),
		'queens-botanical-block-cards' => array(
			'path' => 'assets/css/components/cards.css',
			'deps' => array( 'queens-botanical-block', 'queens-botanical-block-tokens' ),
		),
	);
}

/**
 * Theme setup.
 */
function qbb_setup(): void {
	load_theme_textdomain( 'queens-botanical-block', get_template_directory() . '/languages' );

	add_theme_support(
		'custom-logo',
		array(
			'height'      => 240,
			'width'       => 240,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	add_theme_support( 'editor-styles' );
	add_editor_style( 'style.css' );
	foreach ( qbb_style_components() as $def ) {
		add_editor_style( $def['path'] );
	}
}
add_action( 'after_setup_theme', 'qbb_setup' );

/**
 * Use toggle + aria-expanded on the responsive “open menu” control (core defaults to open-only).
 * Lets one button open/close the overlay; pairs with header.css (hide inner close, icon swap).
 *
 * @param string $block_content Block HTML.
 * @param array  $block        Parsed block.
 */
function qbb_navigation_responsive_toggle_open_button( string $block_content, array $block ): string {
	if ( 'core/navigation' !== ( $block['blockName'] ?? '' ) ) {
		return $block_content;
	}
	if ( ! str_contains( $block_content, 'wp-block-navigation__responsive-container-open' ) ) {
		return $block_content;
	}
	return str_replace(
		'data-wp-on--click="actions.openMenuOnClick"',
		'data-wp-on--click="actions.toggleMenuOnClick" data-wp-bind--aria-expanded="state.isMenuOpen"',
		$block_content
	);
}
add_filter( 'render_block', 'qbb_navigation_responsive_toggle_open_button', 10, 2 );

/**
 * Replace %QBB_THEME_URI% in Custom HTML blocks (footer image src paths).
 *
 * @param string $block_content Block HTML.
 * @param array  $block        Parsed block.
 */
function qbb_render_theme_asset_placeholder( string $block_content, array $block ): string {
	if ( ! str_contains( $block_content, '%QBB_THEME_URI%' ) ) {
		return $block_content;
	}
	return str_replace( '%QBB_THEME_URI%', esc_url( get_template_directory_uri() ), $block_content );
}
add_filter( 'render_block', 'qbb_render_theme_asset_placeholder', 9, 2 );

/**
 * Current year for footer colophon (shortcode: [qbb_year]).
 */
function qbb_shortcode_year(): string {
	return (string) (int) gmdate( 'Y' );
}
add_shortcode( 'qbb_year', 'qbb_shortcode_year' );

/**
 * Footer script (reduced-motion toggle).
 */
function qbb_enqueue_footer_script(): void {
	wp_enqueue_script(
		'queens-botanical-block-footer',
		get_template_directory_uri() . '/assets/js/footer.js',
		array(),
		wp_get_theme()->get( 'Version' ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}
add_action( 'wp_enqueue_scripts', 'qbb_enqueue_footer_script' );

/**
 * Allow SVG uploads in the Media Library.
 *
 * SVG files can include scripts; only upload SVGs from trusted sources, or use a
 * sanitization plugin (e.g. Safe SVG) on sites with untrusted authors.
 *
 * @param array $mimes Allowed upload MIME types.
 */
function qbb_allow_svg_mime_types( array $mimes ): array {
	if ( ! current_user_can( 'upload_files' ) ) {
		return $mimes;
	}

	$mimes['svg']  = 'image/svg+xml';
	$mimes['svgz'] = 'image/svg+xml';

	return $mimes;
}
add_filter( 'upload_mimes', 'qbb_allow_svg_mime_types' );

/**
 * Fix MIME detection for .svg on hosts that reject the upload after upload_mimes.
 *
 * @param array|false $data       File data; may not be an array on all code paths.
 * @param string      $file       Full path to the temp upload file.
 * @param string      $filename   Original file name.
 * @param string[]    $mimes      MIME types.
 * @param string|false $real_mime finfo MIME (often text/plain for SVG).
 * @return array|false
 */
function qbb_fix_svg_filetype( $data, $file, $filename, $mimes, $real_mime = null ) {
	$extension = strtolower( pathinfo( (string) $filename, PATHINFO_EXTENSION ) );

	if ( 'svg' !== $extension ) {
		return $data;
	}

	if ( ! is_array( $data ) ) {
		$data = array();
	}

	$data['ext']  = 'svg';
	$data['type'] = 'image/svg+xml';

	return $data;
}
add_filter( 'wp_check_filetype_and_ext', 'qbb_fix_svg_filetype', 10, 5 );

/**
 * Let SVG uploads succeed in the block editor when finfo reports text/plain or similar.
 * Core clears ext/type when $real_mime does not match; this restores them if the file is SVG markup.
 *
 * @param array|false  $data       File data from previous filters.
 * @param string       $file       Full path to the temp upload file.
 * @param string       $filename   Original file name.
 * @param string[]     $mimes      MIME types.
 * @param string|false $real_mime  Detected MIME type.
 * @return array|false
 */
function qbb_force_svg_when_content_is_svg( $data, $file, $filename, $mimes, $real_mime = null ) {
	if ( strtolower( pathinfo( (string) $filename, PATHINFO_EXTENSION ) ) !== 'svg' ) {
		return $data;
	}

	if ( ! is_string( $file ) || $file === '' || ! is_readable( $file ) ) {
		return $data;
	}

	$sample = file_get_contents( $file, false, null, 0, 2048 );
	if ( ! is_string( $sample ) ) {
		return $data;
	}

	$sample = ltrim( $sample );
	if ( stripos( $sample, '<svg' ) === false && stripos( $sample, '<?xml' ) === false ) {
		return $data;
	}

	$proper = '';
	if ( is_array( $data ) && isset( $data['proper_filename'] ) && is_string( $data['proper_filename'] ) ) {
		$proper = $data['proper_filename'];
	}

	return array(
		'ext'             => 'svg',
		'type'            => 'image/svg+xml',
		'proper_filename' => $proper,
	);
}
add_filter( 'wp_check_filetype_and_ext', 'qbb_force_svg_when_content_is_svg', 99, 5 );

/**
 * If no custom logo is set, show site icon, then theme default logo.svg.
 *
 * @param string $block_content Rendered block HTML.
 * @param array  $block         Block data.
 */
function qbb_site_logo_fallback( string $block_content, array $block ): string {
	if ( ( $block['blockName'] ?? '' ) !== 'core/site-logo' ) {
		return $block_content;
	}

	if ( has_custom_logo() || str_contains( $block_content, '<img' ) ) {
		return $block_content;
	}

	$logo_url = get_site_icon_url( 192 );

	if ( ! $logo_url ) {
		$default_path = get_template_directory() . '/assets/images/logo.svg';
		if ( is_readable( $default_path ) ) {
			$logo_url = get_template_directory_uri() . '/assets/images/logo.svg';
		}
	}

	if ( ! $logo_url ) {
		return $block_content;
	}

	$home = home_url( '/' );
	$alt  = get_bloginfo( 'name', 'display' );

	return sprintf(
		'<div class="wp-block-site-logo"><a href="%1$s" class="custom-logo-link" rel="home" aria-label="%2$s"><img src="%3$s" alt="%4$s" class="custom-logo qbb-site-logo-fallback" width="72" height="72" loading="eager" decoding="async" /></a></div>',
		esc_url( $home ),
		esc_attr( $alt ),
		esc_url( $logo_url ),
		esc_attr( $alt )
	);
}
add_filter( 'render_block', 'qbb_site_logo_fallback', 10, 2 );

/**
 * Front-end styles: theme root stylesheet + per-component CSS.
 */
function qbb_enqueue_styles(): void {
	$ver = wp_get_theme()->get( 'Version' );

	wp_enqueue_style(
		'queens-botanical-block',
		get_stylesheet_uri(),
		array(),
		$ver
	);

	foreach ( qbb_style_components() as $handle => $def ) {
		wp_enqueue_style(
			$handle,
			get_template_directory_uri() . '/' . $def['path'],
			$def['deps'],
			$ver
		);
	}
}
add_action( 'wp_enqueue_scripts', 'qbb_enqueue_styles' );

/**
 * Register block pattern categories.
 */
function qbb_register_pattern_categories(): void {
	register_block_pattern_category(
		'queens-botanical-block',
		array(
			'label' => __( 'Queens Botanical Block', 'queens-botanical-block' ),
		)
	);
}
add_action( 'init', 'qbb_register_pattern_categories' );

/**
 * Enable post Categories for Pages (default: categories apply only to posts).
 * After deploy, visit Settings → Permalinks once and save to flush rewrite rules if URLs look wrong.
 */
function qbb_register_categories_for_pages(): void {
	register_taxonomy_for_object_type( 'category', 'page' );
}
add_action( 'init', 'qbb_register_categories_for_pages', 0 );

/**
 * Register theme blocks (block.json under /blocks).
 */
function qbb_register_theme_blocks(): void {
	$blocks = array(
		'hours-of-operation',
		'nyc-temperature',
	);
	foreach ( $blocks as $slug ) {
		$dir = get_template_directory() . '/blocks/' . $slug;
		if ( is_readable( $dir . '/block.json' ) ) {
			register_block_type( $dir );
		}
	}
}
add_action( 'init', 'qbb_register_theme_blocks' );

/**
 * Block styles — Garden card (Group), matches QBG card treatment; tweak in assets/css/components/cards.css.
 */
function qbb_register_block_styles(): void {
	register_block_style(
		'core/group',
		array(
			'name'  => 'qbb-garden-card',
			'label' => __( 'Garden card', 'queens-botanical-block' ),
		)
	);
	register_block_style(
		'queens-botanical-block/hours-of-operation',
		array(
			'name'  => 'qbb-hours-header-inline',
			'label' => __( 'Header: Open today (inline)', 'queens-botanical-block' ),
		)
	);
}
add_action( 'init', 'qbb_register_block_styles' );

/**
 * 301 redirects from content audit CSV (tools/apply-content-audit.php).
 * Option `qbb_content_audit_redirects`: map of request path (no slashes) => absolute URL.
 */
function qbb_content_audit_maybe_redirect(): void {
	if ( is_admin() || wp_doing_ajax() || wp_doing_cron() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}

	$map = get_option( 'qbb_content_audit_redirects', array() );
	if ( ! is_array( $map ) || $map === array() ) {
		return;
	}

	$raw = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( (string) $_SERVER['REQUEST_URI'] ) ) : '';
	$path = wp_parse_url( $raw, PHP_URL_PATH );
	if ( ! is_string( $path ) ) {
		return;
	}

	$path = trim( $path, '/' );
	if ( $path === '' || ! isset( $map[ $path ] ) ) {
		return;
	}

	$target = $map[ $path ];
	if ( ! is_string( $target ) || $target === '' ) {
		return;
	}

	wp_safe_redirect( $target, 301 );
	exit;
}
add_action( 'template_redirect', 'qbb_content_audit_maybe_redirect', 0 );
