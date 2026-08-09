#!/usr/bin/env python3
from pathlib import Path
import hashlib
import json
import re
import sys

ROOT = Path(__file__).resolve().parents[1]
PLUGIN = ROOT / 'plugin' / 'graha-selang-site-core'
SRC = PLUGIN / 'src'

def fail(message):
    print(f'FAIL: {message}', file=sys.stderr)
    raise SystemExit(1)

def check(condition, message):
    if not condition:
        fail(message)
    print(f'PASS: {message}')

check(PLUGIN.is_dir(), 'deployable plugin directory exists')
check((PLUGIN / 'graha-selang.php').is_file(), 'plugin entrypoint exists under deployable plugin directory')
for stale in ('graha-selang.php', 'src', 'assets', 'templates', 'migration-runtime'):
    check(not (ROOT / stale).exists(), f'no stale root runtime path: {stale}')
check((ROOT / 'migration-source').is_dir(), 'repository migration source archive remains at repo root')
check(not (PLUGIN / 'migration-source').exists(), 'repository migration source is excluded from deployable plugin')
check((PLUGIN / 'migration-runtime' / 'product-catalog-v1').is_dir(), 'disposable migration runtime remains inside deployable plugin')
cpanel = (ROOT / '.cpanel.yml').read_text(encoding='utf-8')
check('plugin/graha-selang-site-core/.' in cpanel, 'cPanel deploy source is deployable plugin only')
check('/wp-content/plugins/graha-selang-site-core/' in cpanel, 'cPanel deploy destination remains graha-selang-site-core')
check('plugin/gloskin-site-core' not in cpanel, 'cPanel deployment contains no stale gloskin plugin source')

production_files = [PLUGIN / 'graha-selang.php']
production_files += sorted(SRC.glob('*.php'))
production_files += sorted((PLUGIN / 'templates').glob('*.php'))
production_files += sorted((PLUGIN / 'assets').rglob('*.css'))
production_files += sorted((PLUGIN / 'assets').rglob('*.js'))
texts = {path: path.read_text(encoding='utf-8') for path in production_files}
php_text = '\n'.join(text for path, text in texts.items() if path.suffix == '.php')
runtime_text = '\n'.join(texts.values())

kernel = texts[SRC / 'Kernel.php']
admin = texts[SRC / 'AdminService.php']
assets = texts[SRC / 'AssetService.php']
nav = texts[SRC / 'NavigationService.php']
template = texts[SRC / 'TemplateService.php']
migration = texts[SRC / 'ProductCatalogMigration.php']
bundle = texts[SRC / 'ProductCatalogBundle.php']

check(len(re.findall(r'final\s+class\s+Kernel\b', php_text)) == 1, 'exactly one Kernel composition root')
owners = re.findall(r'new\s+([A-Za-z]+(?:Service|Adapter))\s*\(', kernel)
check(1 <= len(set(owners)) <= 8, f'bootable owner count within limit ({len(set(owners))}/8)')
check(set(owners) == {'AdminService', 'AssetService', 'NavigationService', 'TemplateService'}, 'only existing four bootable owners remain active')
check('ProductCatalogMigration' not in kernel and 'ProductCatalogBundle' not in kernel, 'migration helpers are not bootable Kernel owners')

check(php_text.count('add_menu_page(') == 1, 'exactly one Graha root admin menu registration')
check("const MENU_SLUG" in admin and "'graha-selang-content'" in admin, 'canonical admin slug preserved')
check(re.search(r"add_menu_page\([\s\S]*?self::MENU_SLUG[\s\S]*?\n\s*3\s*\n\s*\)", admin) is not None, 'default admin menu position is 3')
check(admin.count('add_submenu_page(') == 2, 'AdminService has Ringkasan plus one conditional migration child registration')
check('should_show_menu()' in admin and 'MIGRATION_SLUG' in admin, 'migration child is gated by bundle state')
check("const MIGRATION_CAPABILITY = 'manage_woocommerce';" in admin, 'migration capability is explicit and Woo-scoped')
check("'wp_ajax_' . self::MIGRATION_AJAX" in admin and 'wp_ajax_nopriv_' not in admin, 'migration exposes authenticated AJAX only')
check('check_ajax_referer' in admin and 'current_user_can( self::MIGRATION_CAPABILITY )' in admin, 'migration AJAX enforces capability plus nonce')
check("if ( function_exists( 'is_admin' ) && is_admin() )" in admin, 'migration AJAX hook is admin-only')
check("require_once __DIR__ . '/ProductCatalogMigration.php';" in admin and 'get_migration()' in admin, 'AdminService lazy-loads migration helper')

check(php_text.count('final class AssetService') == 1, 'exactly one first-party asset owner')
for path, text in texts.items():
    if path.suffix == '.php' and path.name != 'AssetService.php':
        check(re.search(r'\bwp_(?:register|enqueue)_(?:style|script)\s*\(', text) is None, f'no direct asset registry/enqueue outside AssetService: {path.relative_to(ROOT)}')
check('ADMIN_MIGRATION_STYLE' in assets and 'ADMIN_MIGRATION_SCRIPT' in assets, 'migration screen assets remain owned by AssetService')
check('wp_localize_script' in assets and 'admin-ajax.php' in assets, 'migration JavaScript receives only scoped AJAX configuration')
check('admin_enqueue_scripts' not in assets, 'AssetService does not globally hook admin enqueue')

check('register_nav_menus' in nav and 'wp_get_nav_menu_items' in nav, 'NavigationService uses native WordPress menu ownership')
check('register_nav_menus' not in php_text.replace(nav, ''), 'navigation ownership is not duplicated')

check("add_filter( 'the_content'" in template and "add_action( 'wp_enqueue_scripts'" in template, 'TemplateService advances native presentation without route takeover')
for forbidden in ('template_include', 'template_redirect', 'add_rewrite_rule', 'register_post_type(', 'register_taxonomy('):
    check(forbidden not in template, f'TemplateService does not own {forbidden}')
for family in ('home','product_archive','product_category','product_single','application','brand','about','service','technical_rfq','article','legal','search','not_found'):
    check(f"'{family}'" in template, f'TemplateService retains family: {family}')
check('_graha_source_identity' in template and '_graha_home_group' in template, 'native Home reads migrated/native product provenance')
check(re.search(r"'numberposts'\s*=>\s*80", template) is not None, 'native Home product query is bounded')
check('migration-source/' not in template, 'public presentation never reads repository archive bundle')
check('contact-us' in template and 'layanan-kami' in template and "wc_get_page_id( 'shop' )" in template, 'native Home activation requires real native shop/services/contact destinations')
check('graha-priority-grid' in texts[PLUGIN / 'assets/css/foundation.css'], 'shared foundation preserves unequal Home hierarchy primitive')

for label, patterns in {
    'custom database writes': [r'\$wpdb\b', r'\bdbDelta\s*\(', r'CREATE\s+TABLE'],
    'unapproved custom content types': [r'\bregister_post_type\s*\(', r'\bregister_taxonomy\s*\('],
    'unauthenticated mutation endpoints': [r'wp_ajax_nopriv_', r'\bregister_rest_route\s*\('],
    'custom mail transport': [r'\bwp_mail\s*\(', r'\bmail\s*\('],
    'custom payment/order implementation': [r'\bWC_Order\b', r'woocommerce_(?:checkout|payment|order)'],
    'duplicate SEO output': [r'rel=["\']canonical["\']', r'application/ld\+json', r'<meta\s', r'\bwp_head\b'],
    'route takeover before Wave 0': [r'\btemplate_include\b', r'\btemplate_redirect\b', r'\badd_rewrite_rule\s*\(', r'\bwp_(?:safe_)?redirect\s*\('],
}.items():
    for pattern in patterns:
        check(re.search(pattern, php_text, re.I) is None, f'no {label}: {pattern}')

check(re.search(r'\bgloskin\b', runtime_text, re.I) is None, 'no Gloskin runtime leakage')
for path, text in texts.items():
    if path != SRC / 'TemplateService.php':
        check(re.search(r'\bmorgen\b', text, re.I) is None, f'no Morgen runtime identifier outside approved Home grouping: {path.relative_to(ROOT)}')

check("RUNTIME_RELATIVE_PATH = 'migration-runtime/product-catalog-v1'" in bundle, 'one fixed plugin-local runtime bundle path is explicit')
check('migration-source' not in bundle and 'migration-source' not in migration, 'production migration runtime never reads repository archive copy')
check('hash_file' in bundle and "'sha256'" in bundle, 'full AJAX-time payload checksum validation exists')
check('validate_manifest_structure' in bundle and 'read_header' in bundle, 'cheap menu detection validates manifest structure without payload hashing')
for token in ('RecursiveDirectoryIterator', 'FilesystemIterator', 'scandir(', 'glob('):
    check(token not in bundle, f'cleanup/detection avoids recursive or broad scanning: {token}')
for need in ('WC_Product_Simple', 'wc_get_product', 'add_option', 'update_option', '_graha_source_identity'):
    check(need in migration, f'migration uses native/idempotency primitive: {need}')
consumed = re.search(r"'status'\s*=>\s*'consumed'", migration)
cleanup_pos = migration.find('->cleanup(')
check(consumed is not None and cleanup_pos > consumed.start(), 'logical consumed state is persisted before cleanup call')
check('Source identity collision' in migration and 'source identity lain' in migration, 'source identity collision guards are explicit')

archive = ROOT / 'migration-source/product-catalog-v1'
runtime = PLUGIN / 'migration-runtime/product-catalog-v1'
for base in (archive, runtime):
    check((base / 'manifest.json').is_file() and (base / 'products.json').is_file(), f'bundle copy present: {base.relative_to(ROOT)}')
manifest_a = json.loads((archive / 'manifest.json').read_text(encoding='utf-8'))
manifest_r = json.loads((runtime / 'manifest.json').read_text(encoding='utf-8'))
check(manifest_a == manifest_r, 'archive/runtime manifests are identical at commit time')
check((archive / 'products.json').read_bytes() == (runtime / 'products.json').read_bytes(), 'archive/runtime product payloads are identical at commit time')
check(manifest_a.get('expected_records') == 44 and manifest_a.get('files') == ['products.json'], 'manifest has explicit payload list and reliable 44-record count')
sha = hashlib.sha256((runtime / 'products.json').read_bytes()).hexdigest()
check(manifest_a.get('checksums', {}).get('products.json') == sha, 'manifest checksum matches products payload')
data = json.loads((runtime / 'products.json').read_text(encoding='utf-8'))
products = data.get('products', [])
check(len(products) == 44, 'bundle contains exactly 44 conservative product identity records')
ids = [p.get('source_id') for p in products]
check(len(ids) == len(set(ids)), 'bundle source identities are unique')
allowed = {'source_id','name','slug','source_url','home_group'}
for product in products:
    check(set(product) == allowed, f'product record carries only approved identity/presentation fields: {product.get("source_id", "unknown")}')
    check(str(product.get('source_id', '')).startswith('graha-public-product:') and product.get('name') and product.get('slug'), 'product record has deterministic stable identity/name/slug')
counts = {key: sum(1 for p in products if p.get('home_group') == key) for key in ('hydraulic_anchor','industrial_anchor','ducting_support','pvc_support','fittings_support','cng_specialist')}
check(counts == {'hydraulic_anchor':15,'industrial_anchor':11,'ducting_support':5,'pvc_support':2,'fittings_support':10,'cng_specialist':1}, f'Home group counts preserve hierarchy ({counts})')

content_contract = (ROOT / 'docs/content-data-contracts.md').read_text(encoding='utf-8')
for meta in ('_graha_source_identity','_graha_source_bundle','_graha_source_url','_graha_home_group'):
    check(meta in content_contract, f'content-data contract documents migration provenance field: {meta}')
traceability = (ROOT / 'docs/requirement-traceability.csv').read_text(encoding='utf-8')
for requirement_id in range(32, 41):
    check(f'REQ-{requirement_id:03d}' in traceability, f'traceability includes REQ-{requirement_id:03d}')

check('setInterval(' not in texts[PLUGIN / 'assets/js/admin-migration.js'], 'migration screen has no polling loop')
check("addEventListener('click'" in texts[PLUGIN / 'assets/js/admin-migration.js'], 'migration work starts only from explicit user action')
check('button.disabled = true' in texts[PLUGIN / 'assets/js/admin-migration.js'], 'migration UI suppresses double-click while request runs')

print('Repository contract guards passed.')
