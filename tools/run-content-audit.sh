#!/usr/bin/env bash
# Run from Local WP → Open site shell (so MySQL is reachable).
# CSV column "Keep/Update/Redirect/Delete": Delete → trash, Unpublish → draft, else → no change.
# Usage:
#   ./run-content-audit.sh              # dry-run, default CSV in Downloads
#   ./run-content-audit.sh /path/to.csv # dry-run
#   ./run-content-audit.sh /path/to.csv execute
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
THEME_DIR="$(dirname "$SCRIPT_DIR")"
PUBLIC_DIR="$(dirname "$(dirname "$(dirname "$THEME_DIR")")")"

if [[ ! -f "$PUBLIC_DIR/wp-load.php" ]]; then
	echo "Could not find WordPress root (expected wp-load.php in: $PUBLIC_DIR)" >&2
	echo "Run this script from your machine, or cd to app/public first and run:" >&2
	echo "  ALLOW_QBB_CONTENT_AUDIT=1 php wp-content/themes/queens-botanical-block-theme/tools/apply-content-audit.php \"\$CSV\" --execute" >&2
	exit 1
fi

DEFAULT_CSV="$HOME/Downloads/wordpress_content_audit(Sheet 1 - wordpress_content_aud).csv"
CSV_PATH="${1:-$DEFAULT_CSV}"
MODE="${2:-dry}"

if [[ ! -f "$CSV_PATH" ]]; then
	echo "CSV not found: $CSV_PATH" >&2
	exit 1
fi

PHP_BIN="php"
if [[ -d "$HOME/Library/Application Support/Local/lightning-services" ]]; then
	FOUND="$(find "$HOME/Library/Application Support/Local/lightning-services" -path '*/bin/darwin*/bin/php' -type f 2>/dev/null | head -1)"
	[[ -n "$FOUND" && -x "$FOUND" ]] && PHP_BIN="$FOUND"
fi

export ALLOW_QBB_CONTENT_AUDIT=1
cd "$PUBLIC_DIR"

ARGS=( "$PHP_BIN" "$SCRIPT_DIR/apply-content-audit.php" "$CSV_PATH" )
if [[ "$MODE" == "execute" ]]; then
	ARGS+=( --execute )
	echo "Applying audit (trash + draft + redirects)…" >&2
else
	echo "Dry-run only (no DB changes). Pass a second arg 'execute' to apply." >&2
fi

exec "${ARGS[@]}"
