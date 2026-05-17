<?php
/**
 * Apply page actions from a content-audit CSV (one row per page).
 *
 * Reads the column **Keep/Update/Redirect/Delete** and only does three things:
 *
 * | Cell contains     | Effect                          |
 * |-------------------|---------------------------------|
 * | **Delete**        | Move page to trash              |
 * | **Unpublish** …   | Set status to draft             |
 * | Anything else*    | No change (Keep / Update / etc.)|
 *
 * *Includes empty cells, Redirect, Keep/Update, etc. — no DB write.
 * Rows with **Delete?** are skipped (ambiguous).
 *
 *   cd ~/Local\ Sites/queensbotanical/app/public
 *   ALLOW_QBB_CONTENT_AUDIT=1 php wp-content/themes/queens-botanical-block-theme/tools/apply-content-audit.php \
 *     "/path/to/audit.csv" [--execute] [--trash-slugs=slug1,slug2]
 *
 * @package Queens_Botanical_Block
 */

declare(strict_types=1);

if ( PHP_SAPI !== 'cli' ) {
	exit( 'CLI only.' );
}

$csv_path         = '';
$execute          = in_array( '--execute', $argv, true );
$extra_trash_slugs = array();

foreach ( array_slice( $argv, 1 ) as $arg ) {
	if ( str_starts_with( $arg, '--trash-slugs=' ) ) {
		$part = substr( $arg, strlen( '--trash-slugs=' ) );
		foreach ( explode( ',', $part ) as $s ) {
			$s = trim( $s );
			if ( $s !== '' ) {
				$extra_trash_slugs[] = $s;
			}
		}
		continue;
	}
	if ( $arg === '--execute' ) {
		continue;
	}
	if ( $csv_path === '' && is_readable( $arg ) ) {
		$csv_path = $arg;
	}
}

if ( $csv_path === '' || ! is_readable( $csv_path ) ) {
	fwrite( STDERR, "Usage: ALLOW_QBB_CONTENT_AUDIT=1 php apply-content-audit.php /path/to/audit.csv [--execute] [--trash-slugs=slug1,slug2]\n" );
	exit( 1 );
}

$is_wp_cli = defined( 'WP_CLI' ) && WP_CLI;
if ( '1' !== getenv( 'ALLOW_QBB_CONTENT_AUDIT' ) && ! $is_wp_cli ) {
	fwrite( STDERR, "Set ALLOW_QBB_CONTENT_AUDIT=1 for this one-shot script (or run via `wp eval-file` where WP_CLI is defined).\n" );
	exit( 1 );
}

$public = dirname( __DIR__, 4 );
if ( ! is_readable( $public . '/wp-load.php' ) ) {
	fwrite( STDERR, "Could not find wp-load.php in {$public}\n" );
	exit( 1 );
}

require_once $public . '/wp-load.php';

/**
 * @return array{id?: positive-int, path?: string}|null
 */
function qbb_audit_parse_url( string $url ): ?array {
	$url = trim( $url );
	if ( $url === '' ) {
		return null;
	}
	if ( preg_match( '/[?&]page_id=(\d+)/', $url, $m ) ) {
		return array( 'id' => (int) $m[1] );
	}
	if ( preg_match( '#queensbotanical\.(?:org|local)(/[^?#]*)#', $url, $m ) ) {
		$path = trim( $m[1], '/' );
		return array( 'path' => $path );
	}
	return null;
}

function qbb_audit_resolve_page_id( array $parsed ): ?int {
	if ( isset( $parsed['id'] ) ) {
		$p = get_post( $parsed['id'] );
		return ( $p instanceof WP_Post && $p->post_type === 'page' ) ? (int) $p->ID : null;
	}
	if ( isset( $parsed['path'] ) && $parsed['path'] !== '' ) {
		$page = get_page_by_path( $parsed['path'], OBJECT, 'page' );
		if ( $page instanceof WP_Post ) {
			return (int) $page->ID;
		}
		$pid = url_to_postid( home_url( '/' . $parsed['path'] . '/' ) );
		return $pid > 0 ? $pid : null;
	}
	return null;
}

/**
 * Map the audit action cell to: keep | delete | unpublish | skip
 *
 * @return string 'keep'|'delete'|'unpublish'|'skip'
 */
function qbb_audit_normalize_action( string $raw ): string {
	$raw = str_replace( array( "\xc2\xa0", "\xe2\x80\xaf" ), ' ', $raw );
	$a   = strtolower( trim( $raw ) );
	$a   = preg_replace( '/\s+/u', ' ', $a ) ?? $a;
	if ( $a === '' ) {
		return 'keep';
	}
	if ( str_contains( $a, 'delete?' ) ) {
		return 'skip';
	}
	if ( $a === 'delete' ) {
		return 'delete';
	}
	if ( str_contains( $a, 'unpublish' ) ) {
		return 'unpublish';
	}
	return 'keep';
}

$raw_csv = file_get_contents( $csv_path );
if ( $raw_csv === false ) {
	fwrite( STDERR, "Could not read CSV.\n" );
	exit( 1 );
}
$utf8 = mb_convert_encoding( $raw_csv, 'UTF-8', 'Windows-1252, UTF-8' );
$handle = fopen( 'php://memory', 'rb+' );
if ( $handle === false ) {
	exit( 1 );
}
fwrite( $handle, $utf8 );
rewind( $handle );

$header = fgetcsv( $handle );
if ( $header === false ) {
	exit( 1 );
}
// Row 0 is title row "wordpress_content_audit"; real header is row 2 in file — skip until we see "URL".
$cols = null;
while ( ( $row = fgetcsv( $handle ) ) !== false ) {
	if ( isset( $row[2] ) && trim( (string) $row[2] ) === 'URL' ) {
		$cols = array_flip( $row );
		break;
	}
}
if ( $cols === null || ! isset( $cols['URL'], $cols['Keep/Update/Redirect/Delete'] ) ) {
	fwrite( STDERR, "Could not find header row with URL and Keep/Update/Redirect/Delete columns.\n" );
	exit( 1 );
}

$url_i    = $cols['URL'];
$action_i = $cols['Keep/Update/Redirect/Delete'];

$seen_ids = array();
$stats    = array(
	'keep'             => 0,
	'skip'             => 0,
	'delete_dry'       => 0,
	'delete_done'      => 0,
	'unpublish_dry'    => 0,
	'unpublish_done'   => 0,
	'unresolved'       => 0,
	'already_trashed'  => 0,
);

while ( ( $row = fgetcsv( $handle ) ) !== false ) {
	$url_raw = isset( $row[ $url_i ] ) ? trim( (string) $row[ $url_i ] ) : '';
	if ( $url_raw === '' ) {
		continue;
	}
	$action_raw = isset( $row[ $action_i ] ) ? (string) $row[ $action_i ] : '';
	$verb       = qbb_audit_normalize_action( $action_raw );

	if ( $verb === 'keep' ) {
		++$stats['keep'];
		continue;
	}
	if ( $verb === 'skip' ) {
		++$stats['skip'];
		fwrite( STDOUT, "[SKIP ambiguous Delete?] {$url_raw}\n" );
		continue;
	}

	$parsed = qbb_audit_parse_url( $url_raw );
	if ( $parsed === null ) {
		++$stats['unresolved'];
		fwrite( STDOUT, "[UNRESOLVED URL] {$url_raw}\n" );
		continue;
	}
	$page_id = qbb_audit_resolve_page_id( $parsed );
	if ( $page_id === null ) {
		++$stats['unresolved'];
		fwrite( STDOUT, "[NO PAGE] {$url_raw}\n" );
		continue;
	}
	if ( isset( $seen_ids[ $page_id ] ) ) {
		fwrite( STDOUT, "[DUPLICATE] Page ID {$page_id} appears twice in CSV — skipped second row\n" );
		continue;
	}
	$seen_ids[ $page_id ] = true;

	$post = get_post( $page_id );
	if ( ! $post instanceof WP_Post ) {
		continue;
	}

	if ( $verb === 'delete' ) {
		if ( $post->post_status === 'trash' ) {
			++$stats['already_trashed'];
			fwrite( STDOUT, "[SKIP trash] already trashed ID {$page_id} {$post->post_title}\n" );
			continue;
		}
		++$stats['delete_dry'];
		fwrite( STDOUT, "[TRASH] ID {$page_id} {$post->post_status} {$post->post_title}\n" );
		if ( $execute ) {
			$ok = wp_trash_post( $page_id );
			if ( $ok ) {
				++$stats['delete_done'];
			} else {
				fwrite( STDOUT, "[FAILED trash] ID {$page_id} — check capabilities / plugins\n" );
			}
		}
		continue;
	}

	if ( $verb === 'unpublish' ) {
		if ( in_array( $post->post_status, array( 'draft', 'pending', 'private', 'trash' ), true ) ) {
			fwrite( STDOUT, "[SKIP unpublish] already non-public ID {$page_id} ({$post->post_status})\n" );
			continue;
		}
		++$stats['unpublish_dry'];
		fwrite( STDOUT, "[UNPUBLISH->draft] ID {$page_id} was {$post->post_status} {$post->post_title}\n" );
		if ( $execute ) {
			wp_update_post(
				array(
					'ID'          => $page_id,
					'post_status' => 'draft',
				)
			);
			++$stats['unpublish_done'];
		}
	}
}

fclose( $handle );

if ( $execute && $extra_trash_slugs !== array() ) {
	foreach ( array_unique( $extra_trash_slugs ) as $slug ) {
		$page = get_page_by_path( $slug, OBJECT, 'page' );
		if ( ! $page instanceof WP_Post ) {
			fwrite( STDOUT, "[EXTRA TRASH] no page for slug \"{$slug}\"\n" );
			continue;
		}
		if ( $page->post_status === 'trash' ) {
			fwrite( STDOUT, "[EXTRA TRASH] skip \"{$slug}\" (already trashed)\n" );
			continue;
		}
		$ok = wp_trash_post( (int) $page->ID );
		if ( $ok ) {
			fwrite( STDOUT, "[EXTRA TRASH] ID {$page->ID} \"{$slug}\" -> trash\n" );
			++$stats['delete_done'];
		} else {
			fwrite( STDOUT, "[EXTRA TRASH FAILED] \"{$slug}\" ID {$page->ID}\n" );
		}
	}
}

fwrite( STDOUT, "\n--- Summary ---\n" );
foreach ( $stats as $k => $v ) {
	if ( $v > 0 ) {
		fwrite( STDOUT, "{$k}: {$v}\n" );
	}
}
if ( ! $execute ) {
	fwrite( STDOUT, "\nDry-run only. Re-run with --execute to apply **Delete** (trash) and **Unpublish** (draft).\n" );
}
