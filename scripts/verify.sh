#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"

command -v php >/dev/null 2>&1 || { echo 'FAIL: php is required for syntax checks' >&2; exit 1; }
command -v python3 >/dev/null 2>&1 || { echo 'FAIL: python3 is required for contract guards' >&2; exit 1; }

echo '== PHP syntax =='
while IFS= read -r file; do
	php -l "$file"
done < <(find . -type f -name '*.php' -not -path './vendor/*' | sort)

echo '== Repository contract guards =='
python3 tests/verify_contracts.py

echo '== PHP foundation tests =='
php tests/navigation-normalization.php
php tests/navigation-render.php
php tests/template-render.php
php tests/admin-asset-scope.php
php tests/kernel-smoke.php

if command -v node >/dev/null 2>&1; then
	echo '== JavaScript syntax =='
	node --check assets/js/navigation.js
else
	echo 'SKIP: node is unavailable; JavaScript syntax not checked'
fi

echo 'All available repository checks passed.'
