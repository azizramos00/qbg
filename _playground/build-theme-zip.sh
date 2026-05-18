#!/usr/bin/env bash
# Regenerate the Playground theme zip (run from repo root or anywhere).
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
NAME="$(basename "$ROOT")"
PARENT="$(dirname "$ROOT")"
OUT="$ROOT/_playground/queens-botanical-block-theme.zip"

cd "$PARENT"
zip -r "$OUT" "$NAME" \
	-x "$NAME/.git/*" \
	-x "$NAME/_playground/queens-botanical-block-theme.zip" \
	-x "$NAME/_playground/demo-content.xml" \
	-x "*/.DS_Store" \
	-x "$NAME/node_modules/*"

echo "Wrote $OUT"
