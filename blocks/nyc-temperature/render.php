<?php
/**
 * Server render: NYC temperature (weather.gov observations, KNYC).
 *
 * @package Queens_Botanical_Block
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Unused.
 * @var WP_Block $block      Block instance.
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

$unit                = isset( $attributes['unit'] ) && $attributes['unit'] === 'celsius' ? 'celsius' : 'fahrenheit';
$show_location_label = ! isset( $attributes['showLocationLabel'] ) || ! empty( $attributes['showLocationLabel'] );
$show_attribution    = ! isset( $attributes['showAttribution'] ) || ! empty( $attributes['showAttribution'] );

$cache_key = 'qbb_nyc_knyc_obs_v2';

$cached = get_transient( $cache_key );
$celsius = false;

if ( is_array( $cached ) ) {
	if ( isset( $cached['c'] ) && is_numeric( $cached['c'] ) ) {
		$celsius = (float) $cached['c'];
	} elseif ( ! empty( $cached['e'] ) ) {
		$celsius = null;
	}
}

if ( false === $celsius ) {
	$response = wp_remote_get(
		'https://api.weather.gov/stations/KNYC/observations/latest',
		array(
			'timeout' => 10,
			'headers' => array(
				'Accept'     => 'application/geo+json',
				'User-Agent' => 'QueensBotanicalBlockTheme/1.0 (+https://queensbotanical.org)',
			),
		)
	);

	$fetched_c = null;
	if ( ! is_wp_error( $response ) ) {
		$code = wp_remote_retrieve_response_code( $response );
		$body = wp_remote_retrieve_body( $response );
		if ( 200 === $code && is_string( $body ) && $body !== '' ) {
			$data = json_decode( $body, true );
			if ( is_array( $data ) && isset( $data['properties']['temperature']['value'] ) ) {
				$val = $data['properties']['temperature']['value'];
				if ( is_numeric( $val ) ) {
					$fetched_c = (float) $val;
				}
			}
		}
	}

	if ( null !== $fetched_c ) {
		$celsius = $fetched_c;
		set_transient( $cache_key, array( 'c' => $fetched_c ), 600 );
	} else {
		$celsius = null;
		set_transient( $cache_key, array( 'e' => 1 ), 120 );
	}
}

$display = '';
if ( is_numeric( $celsius ) ) {
	$c = (float) $celsius;
	if ( 'celsius' === $unit ) {
		$display = (string) (int) round( $c ) . '°C';
	} else {
		$f       = ( $c * 9 / 5 ) + 32;
		$display = (string) (int) round( $f ) . '°F';
	}
} else {
	$display = '—';
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'qbb-nyc-temp',
	)
);

?>
<div <?php echo $wrapper_attributes; ?>>
	<?php if ( $show_location_label ) : ?>
		<p class="qbb-nyc-temp__label"><?php esc_html_e( 'Flushing, NY', 'queens-botanical-block' ); ?></p>
	<?php endif; ?>
	<p class="qbb-nyc-temp__value" role="status"><?php echo esc_html( $display ); ?></p>
	<?php if ( $show_attribution ) : ?>
		<p class="qbb-nyc-temp__attr">
			<a href="https://www.weather.gov/documentation/services-web-api" rel="external noreferrer noopener" target="_blank"><?php esc_html_e( 'National Weather Service', 'queens-botanical-block' ); ?></a>
		</p>
	<?php endif; ?>
</div>
