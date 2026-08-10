#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"
command -v php >/dev/null 2>&1 || { echo 'FAIL: php is required for syntax checks' >&2; exit 1; }
command -v python3 >/dev/null 2>&1 || { echo 'FAIL: python3 is required for contract guards' >&2; exit 1; }
echo '== PHP syntax =='
while IFS= read -r file; do php -l "$file"; done < <(find . -type f -name '*.php' -not -path './vendor/*' | sort)
echo '== Repository contract guards =='
python3 tests/verify_contracts.py
python3 tests/visual-foundation-contract.py
python3 tests/hero-illustration-contract.py
echo '== PHP foundation and migration tests =='
php tests/navigation-normalization.php
php tests/navigation-render.php
php tests/template-render.php
php tests/native-presentation.php
php tests/static-page-shell.php
php tests/admin-asset-scope.php
php tests/product-content-service.php
php tests/site-lifecycle.php
php tests/bootstrap-pages.php
php tests/migration-admin.php
php tests/migration-runtime.php
php tests/migration-immutability.php
php tests/kernel-smoke.php
php tests/version-consistency.php
php tests/brand-identity.php
php tests/frontend-stabilization.php
php tests/homepage-copy.php
php tests/home-product-storytelling.php
php tests/home-discovery-capability.php
php tests/admin-presentation-status.php
if command -v node >/dev/null 2>&1; then
  echo '== JavaScript syntax =='
  node --check plugin/graha-selang-site-core/assets/js/navigation.js
  node --check plugin/graha-selang-site-core/assets/js/admin-migration.js
else
  echo 'SKIP: node is unavailable; JavaScript syntax not checked'
fi
echo 'All available repository checks passed.'
