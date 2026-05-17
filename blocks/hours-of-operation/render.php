<?php
/**
 * Server render: Hours of operation block.
 *
 * @package Queens_Botanical_Block
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner blocks (unused).
 * @var WP_Block $block      Block instance.
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

$show_heading       = ! empty( $attributes['showHeading'] );
$show_today_summary = ! empty( $attributes['showTodaySummary'] );
$highlight_today    = ! empty( $attributes['highlightToday'] );
$heading            = isset( $attributes['heading'] ) && is_string( $attributes['heading'] ) ? $attributes['heading'] : '';
$raw_schedule       = isset( $attributes['schedule'] ) && is_array( $attributes['schedule'] ) ? $attributes['schedule'] : array();

/**
 * @param string $value Raw.
 * @return string HH:MM or empty.
 */
$sanitize_hhmm = static function ( string $value ): string {
	$value = trim( $value );
	if ( preg_match( '/^([01]\d|2[0-3]):([0-5]\d)$/', $value ) ) {
		return $value;
	}
	return '';
};

/**
 * @param mixed $row Raw row from attributes.
 * @return array{dayIndex: int, closed: bool, openTime: string, closeTime: string}
 */
$normalize_row = static function ( $row ) use ( $sanitize_hhmm ): array {
	$day = 1;
	if ( is_array( $row ) && isset( $row['dayIndex'] ) ) {
		$day = max( 1, min( 7, (int) $row['dayIndex'] ) );
	}

	$closed = false;
	if ( is_array( $row ) && array_key_exists( 'closed', $row ) ) {
		$closed = (bool) $row['closed'];
	}

	$open  = '';
	$close = '';
	if ( is_array( $row ) ) {
		$open  = isset( $row['openTime'] ) && is_string( $row['openTime'] ) ? $sanitize_hhmm( $row['openTime'] ) : '';
		$close = isset( $row['closeTime'] ) && is_string( $row['closeTime'] ) ? $sanitize_hhmm( $row['closeTime'] ) : '';
	}

	return array(
		'dayIndex'  => $day,
		'closed'    => $closed,
		'openTime'  => $open,
		'closeTime' => $close,
	);
};

$merged = array();
foreach ( $raw_schedule as $row ) {
	$n = $normalize_row( $row );
	$merged[ $n['dayIndex'] ] = $n;
}

$schedule = array();
for ( $d = 1; $d <= 7; $d++ ) {
	if ( isset( $merged[ $d ] ) ) {
		$schedule[] = $merged[ $d ];
		continue;
	}
	$schedule[] = array(
		'dayIndex'  => $d,
		'closed'    => true,
		'openTime'  => '',
		'closeTime' => '',
	);
}

/**
 * @param string $hhmm HH:MM.
 * @return string Localized time for display (not escaped).
 */
$format_time = static function ( string $hhmm ) use ( $sanitize_hhmm ): string {
	$hhmm = $sanitize_hhmm( $hhmm );
	if ( $hhmm === '' ) {
		return '';
	}
	$ts = strtotime( '1970-01-01 ' . $hhmm . ':00' );
	if ( ! is_int( $ts ) || $ts < 0 ) {
		return $hhmm;
	}
	return wp_date( get_option( 'time_format', 'g:i a' ), $ts );
};

// 2024-01-01 is a Monday — anchor for ISO weekday names (N = 1 … 7).
$monday_noon = strtotime( '2024-01-01 12:00:00' );

/**
 * @param int $day_index ISO weekday 1 (Mon) … 7 (Sun).
 * @return array{full: string, abbr: string}
 */
$weekday_labels = static function ( int $day_index ) use ( $monday_noon ): array {
	if ( $monday_noon === false ) {
		return array( 'full' => '', 'abbr' => '' );
	}
	$ts = strtotime( '+' . ( $day_index - 1 ) . ' days', $monday_noon );
	if ( ! is_int( $ts ) ) {
		return array( 'full' => '', 'abbr' => '' );
	}
	return array(
		'full' => wp_date( 'l', $ts ),
		'abbr' => wp_date( 'D', $ts ),
	);
};

/**
 * First upcoming opening after a given ISO weekday (1 = Mon … 7 = Sun), searching the weekly schedule.
 *
 * @param array<int, array<string, mixed>> $schedule Normalized rows with dayIndex.
 * @param int                               $after_day_n Start search from the *next* calendar day.
 * @return array{dayIndex: int, openTime: string}|null
 */
$find_next_opening = static function ( array $schedule, int $after_day_n ) use ( $sanitize_hhmm ): ?array {
	for ( $step = 1; $step <= 7; $step++ ) {
		$n = ( ( $after_day_n - 1 + $step ) % 7 ) + 1;
		foreach ( $schedule as $row ) {
			if ( (int) $row['dayIndex'] !== $n ) {
				continue;
			}
			if ( ! empty( $row['closed'] ) ) {
				continue;
			}
			$o = $sanitize_hhmm( (string) ( $row['openTime'] ?? '' ) );
			if ( $o === '' ) {
				continue;
			}
			return array(
				'dayIndex' => $n,
				'openTime' => $o,
			);
		}
	}
	return null;
};

$today_n   = (int) wp_date( 'N' );
$today_row = null;
foreach ( $schedule as $row ) {
	if ( (int) $row['dayIndex'] === $today_n ) {
		$today_row = $row;
		break;
	}
}

$class_attr       = isset( $attributes['className'] ) && is_string( $attributes['className'] ) ? $attributes['className'] : '';
$is_header_inline = str_contains( $class_attr, 'is-style-qbb-hours-header-inline' );

$wrapper_classes = array( 'qbb-hours' );
if ( ! $is_header_inline ) {
	$wrapper_classes[] = 'qbb-hours--garden';
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => implode( ' ', $wrapper_classes ),
	)
);

if ( $is_header_inline ) {
	$line_primary = '';

	if ( ! is_array( $today_row ) ) {
		$line_primary = __( 'Check hours', 'queens-botanical-block' );
	} elseif ( ! empty( $today_row['closed'] ) ) {
		$line_primary = __( 'Closed today', 'queens-botanical-block' );
		$next         = $find_next_opening( $schedule, $today_n );
		if ( null !== $next ) {
			$line_primary = sprintf(
				/* translators: 1: abbreviated weekday (e.g. Thu), 2: opening time (site time format). */
				__( 'Closed today • Opens %1$s at %2$s', 'queens-botanical-block' ),
				$weekday_labels( (int) $next['dayIndex'] )['abbr'],
				$format_time( (string) $next['openTime'] )
			);
		}
	} else {
		$o = $sanitize_hhmm( (string) $today_row['openTime'] );
		$c = $sanitize_hhmm( (string) $today_row['closeTime'] );
		if ( $o === '' || $c === '' ) {
			$line_primary = __( 'Hours posted soon', 'queens-botanical-block' );
		} else {
			$tz  = wp_timezone();
			$now = new DateTimeImmutable( 'now', $tz );
			$ymd = $now->format( 'Y-m-d' );

			$open_dt = DateTimeImmutable::createFromFormat( 'Y-m-d H:i:s', $ymd . ' ' . $o . ':00', $tz );
			$close_dt = DateTimeImmutable::createFromFormat( 'Y-m-d H:i:s', $ymd . ' ' . $c . ':00', $tz );

			if ( ! $open_dt instanceof DateTimeImmutable || ! $close_dt instanceof DateTimeImmutable ) {
				$line_primary = sprintf(
					/* translators: 1: opening time, 2: closing time (site time format). */
					__( 'Open today %1$s - %2$s', 'queens-botanical-block' ),
					$format_time( $o ),
					$format_time( $c )
				);
			} else {
				if ( $close_dt <= $open_dt ) {
					$close_dt = $close_dt->modify( '+1 day' );
				}
				if ( $now < $open_dt ) {
					$line_primary = sprintf(
						/* translators: %s: opening time (site time format). */
						__( 'Opens today at %s', 'queens-botanical-block' ),
						$format_time( $o )
					);
				} elseif ( $now > $close_dt ) {
					$line_primary = __( 'Closed today', 'queens-botanical-block' );
					$next         = $find_next_opening( $schedule, $today_n );
					if ( null !== $next ) {
						$line_primary = sprintf(
							/* translators: 1: abbreviated weekday (e.g. Thu), 2: opening time (site time format). */
							__( 'Closed today • Opens %1$s at %2$s', 'queens-botanical-block' ),
							$weekday_labels( (int) $next['dayIndex'] )['abbr'],
							$format_time( (string) $next['openTime'] )
						);
					}
				} else {
					$line_primary = sprintf(
						/* translators: 1: opening time, 2: closing time (site time format). */
						__( 'Open today %1$s - %2$s', 'queens-botanical-block' ),
						$format_time( $o ),
						$format_time( $c )
					);
				}
			}
		}
	}
	?>
<div <?php echo $wrapper_attributes; ?>>
	<div class="qbb-hours__inline-status" role="status">
		<p class="qbb-hours__inline qbb-hours__inline--primary"><?php echo esc_html( $line_primary ); ?></p>
	</div>
</div>
	<?php
	return;
}

?>
<div <?php echo $wrapper_attributes; ?>>
	<?php if ( $show_heading ) : ?>
		<?php
		$title = $heading !== '' ? $heading : __( 'Hours', 'queens-botanical-block' );
		?>
		<h3 class="qbb-hours__heading wp-block-heading has-x-large-font-size"><?php echo esc_html( $title ); ?></h3>
	<?php endif; ?>

	<?php if ( $show_today_summary && is_array( $today_row ) ) : ?>
		<?php
		$today_label = wp_date( 'l' );
		$o           = $sanitize_hhmm( (string) $today_row['openTime'] );
		$c           = $sanitize_hhmm( (string) $today_row['closeTime'] );
		?>
		<div class="qbb-hours__today" role="status">
			<div class="qbb-hours__today-top">
				<span class="qbb-hours__today-kicker"><?php esc_html_e( 'Today', 'queens-botanical-block' ); ?></span>
				<span class="qbb-hours__today-wday"><?php echo esc_html( $today_label ); ?></span>
			</div>
			<p class="qbb-hours__today-detail">
				<?php
				if ( ! empty( $today_row['closed'] ) ) {
					esc_html_e( 'Closed', 'queens-botanical-block' );
				} elseif ( $o === '' || $c === '' ) {
					esc_html_e( 'Hours not set', 'queens-botanical-block' );
				} else {
					echo esc_html(
						sprintf(
							/* translators: 1: open time, 2: close time */
							__( '%1$s – %2$s', 'queens-botanical-block' ),
							$format_time( $o ),
							$format_time( $c )
						)
					);
				}
				?>
			</p>
		</div>
	<?php endif; ?>

	<div class="qbb-hours__list" role="list">
		<?php foreach ( $schedule as $row ) : ?>
			<?php
			$day_index = (int) $row['dayIndex'];
			$is_today  = $highlight_today && $day_index === $today_n;
			$labels    = $weekday_labels( $day_index );
			$row_class = 'qbb-hours__row' . ( $is_today ? ' is-today' : '' );
			?>
			<div class="<?php echo esc_attr( $row_class ); ?>" role="listitem">
				<p class="qbb-hours__day">
					<span class="qbb-hours__day-abbr" aria-hidden="true"><?php echo esc_html( $labels['abbr'] ); ?></span>
					<span class="qbb-hours__sr-only"><?php echo esc_html( $labels['full'] ); ?></span>
				</p>
				<p class="qbb-hours__times">
					<?php
					if ( ! empty( $row['closed'] ) ) {
						esc_html_e( 'Closed', 'queens-botanical-block' );
					} else {
						$o = $sanitize_hhmm( (string) $row['openTime'] );
						$c = $sanitize_hhmm( (string) $row['closeTime'] );
						if ( $o === '' || $c === '' ) {
							esc_html_e( '—', 'queens-botanical-block' );
						} else {
							echo esc_html(
								sprintf(
									/* translators: 1: open time, 2: close time */
									__( '%1$s – %2$s', 'queens-botanical-block' ),
									$format_time( $o ),
									$format_time( $c )
								)
							);
						}
					}
					?>
				</p>
			</div>
		<?php endforeach; ?>
	</div>
</div>
