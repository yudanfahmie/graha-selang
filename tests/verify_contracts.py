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

entry = texts[PLUGIN / 'graha-selang.php']
kernel = texts[SRC / 'Kernel.php']
lifecycle = texts[SRC / 'SiteLifecycleService.php']
admin = texts[SRC / 'AdminService.php']
assets = texts[SRC / 'AssetService.php']
nav = texts[SRC / 'NavigationService.php']
template = texts[SRC / 'TemplateService.php']
model = texts[SRC / 'ProductContentService.php']
migration = texts[SRC / 'ProductCatalogMigration.php']
bundle = texts[SRC / 'ProductCatalogBundle.php']

check(len(re.findall(r'final\s+class\s+Kernel\b', php_text)) == 1, 'exactly one Kernel composition root')
owners = set(re.findall(r'new\s+([A-Za-z]+(?:Service|Adapter))\s*\(', kernel))
check(1 <= len(owners) <= 8, f'bootable owner count within limit ({len(owners)}/8)')
check(owners == {'AdminService', 'AssetService', 'NavigationService', 'TemplateService', 'ProductContentService', 'SiteLifecycleService'}, 'only approved six bootable owners remain active')
check('ProductCatalogMigration' not in kernel and 'ProductCatalogBundle' not in kernel, 'migration helpers remain non-bootable')

check('register_activation_hook' in entry and "array( 'GrahaSelang\\\\Kernel', 'activate' )" in entry, 'real activation hook delegates through Kernel')
check('register_deactivation_hook' in entry and "array( 'GrahaSelang\\\\Kernel', 'deactivate' )" in entry, 'real deactivation hook delegates through Kernel')
check('public static function activate()' in kernel and 'register_content_model()' in kernel and 'SiteLifecycleService' in kernel, 'Kernel activation registers product rewrites then delegates lifecycle')
check("const VERSION_OPTION = 'graha_selang_site_schema_version';" in lifecycle and 'SCHEMA_VERSION' in lifecycle, 'lifecycle has explicit schema option/version')
check("add_action( 'admin_init'" in lifecycle and "current_user_can( 'manage_options' )" in lifecycle, 'already-active upgrades are admin-only and authorized')
for slug in ('home', 'about-us', 'layanan-kami', 'contact-us', 'request-quote'):
    check(slug in lifecycle, f'lifecycle provisions canonical Page slug: {slug}')
check("get_option( 'fresh_site'" in lifecycle and "get_option( 'show_on_front'" in lifecycle and "get_option( 'page_on_front'" in lifecycle, 'front-page assignment distinguishes fresh/default from configured installs')
check('structure_is_complete' in lifecycle, 'incomplete provisioning remains retryable instead of claiming schema completion')
check(lifecycle.count('flush_rewrite_rules( false )') == 2, 'rewrite flush appears only in activation and schema-upgrade paths')
deactivate = re.search(r'public function deactivate\(\)\s*\{([^}]*)\}', lifecycle, re.S)
check(deactivate is not None and 'flush_rewrite_rules' not in deactivate.group(1), 'deactivation does not flush rewrites')

check("const POST_TYPE         = 'graha_product';" in model, 'graha_product is the native product CPT')
check("'has_archive'        => 'products'" in model, 'native product archive is /products/')
check("'rewrite'            => array( 'slug' => 'product', 'with_front' => false )" in model, 'native product single base is /product/{slug}/')
check("const CATEGORY_TAXONOMY = 'graha_product_category';" in model and "'slug' => 'product-category'" in model, 'native product category route is explicit')
check("const BRAND_TAXONOMY    = 'graha_product_brand';" in model and "'slug' => 'brand'" in model, 'native brand route is explicit')
check(model.count('register_post_type(') == 1 and model.count('register_taxonomy(') == 2, 'one native product CPT and two product taxonomies are registered')
for path, text in texts.items():
    if path.suffix == '.php' and path != SRC / 'ProductContentService.php':
        check('register_post_type(' not in text and 'register_taxonomy(' not in text, f'no duplicate product content-model registration: {path.relative_to(ROOT)}')
for forbidden in ('WC_Product_Simple', 'wc_get_product', 'wc_get_page_id', 'manage_woocommerce', 'edit_products'):
    check(forbidden not in runtime_text, f'no Woo-only runtime primitive: {forbidden}')

check(php_text.count('add_menu_page(') == 1, 'exactly one Graha root admin menu registration')
check("const MENU_SLUG" in admin and "'graha-selang-content'" in admin, 'canonical admin slug preserved')
check(re.search(r"add_menu_page\([\s\S]*?self::MENU_SLUG[\s\S]*?\n\s*3\s*\n\s*\)", admin) is not None, 'default admin menu position is 3')
check(admin.count('add_submenu_page(') == 5, 'AdminService retains Ringkasan, native product links, and conditional migration child')
check('edit.php?post_type=graha_product' in admin and 'graha_product_category' in admin and 'graha_product_brand' in admin, 'AdminService links native product/category/brand screens')
check('should_show_menu()' in admin and 'MIGRATION_SLUG' in admin, 'migration child remains gated by bundle state')
check("const MIGRATION_CAPABILITY  = 'edit_posts';" in admin or "const MIGRATION_CAPABILITY = 'edit_posts';" in admin, 'migration capability remains native WordPress capability')
check("'wp_ajax_' . self::MIGRATION_AJAX" in admin and 'wp_ajax_nopriv_' not in admin, 'migration exposes authenticated AJAX only')
check('check_ajax_referer' in admin and 'current_user_can( self::MIGRATION_CAPABILITY )' in admin, 'migration AJAX enforces capability plus nonce')
check("require_once __DIR__ . '/ProductCatalogMigration.php';" in admin and 'get_migration()' in admin, 'AdminService lazy-loads migration helper')

check(php_text.count('final class AssetService') == 1, 'exactly one first-party asset owner')
for path, text in texts.items():
    if path.suffix == '.php' and path.name != 'AssetService.php':
        check(re.search(r'\bwp_(?:register|enqueue)_(?:style|script)\s*\(', text) is None, f'no direct asset registry/enqueue outside AssetService: {path.relative_to(ROOT)}')
check('ADMIN_MIGRATION_STYLE' in assets and 'ADMIN_MIGRATION_SCRIPT' in assets, 'migration screen assets remain owned by AssetService')
check('wp_localize_script' in assets and 'admin-ajax.php' in assets, 'migration JavaScript receives scoped AJAX configuration')

check('register_nav_menus' in nav and 'wp_get_nav_menu_items' in nav, 'NavigationService keeps native WordPress menu ownership')
check('fallback_tree()' in nav and '/products/' in nav and '/layanan-kami/' in nav and '/about-us/' in nav and '/request-quote/' in nav and '/contact-us/' in nav, 'NavigationService has canonical no-menu fallback')
check('! $menu_id' in nav and 'return $this->fallback_tree();' in nav, 'fallback is used only when no native menu is assigned')
check('register_nav_menus' not in php_text.replace(nav, ''), 'navigation ownership is not duplicated')

check("add_filter( 'the_content'" in template and "add_action( 'wp_enqueue_scripts'" in template, 'TemplateService retains native presentation integration')
check("add_filter( 'template_include'" in template and 'resolve_native_template' in template and 'templates/front-page.php' in template, 'TemplateService owns narrow front-page document resolution')
check('native_home_is_ready' not in template, 'Home is no longer gated behind full product/destination readiness')
check('is_front_page()' in template and '$this->assets->enqueue_shell()' in template, 'front page always prepares Graha shell assets')
check('request-quote' in template and 'about-us' in template and 'layanan-kami' in template and 'contact-us' in template, 'Home resolves canonical bootstrap destinations')
check("PRODUCT_POST_TYPE = 'graha_product'" in template and '_graha_home_group' in template, 'Home reads native graha_product Home grouping')
check(re.search(r"'numberposts'\s*=>\s*80", template) is not None, 'native Home product query remains bounded')
check("'meta_query'" not in template[template.find('private function get_native_home_groups'):template.find('private function render_native_home_content')], 'Home product discovery is not gated by migration provenance')
check('migration-source/' not in template, 'public presentation never reads repository archive bundle')
check('graha-priority-grid' in texts[PLUGIN / 'assets/css/foundation.css'], 'shared foundation preserves unequal Home hierarchy primitive')
check('Gunakan katalog atau konsultasi teknis' in texts[PLUGIN / 'templates/native-home.php'], 'Home has deliberate zero-product state')

tokens_css = texts[PLUGIN / 'assets/css/tokens.css']
foundation_css = texts[PLUGIN / 'assets/css/foundation.css']
check('--graha-focus-color' in tokens_css and '--graha-focus-ring' in tokens_css, 'centralized focus-ring tokens are declared once in tokens.css')
check('var(--graha-focus-color)' in foundation_css and 'var(--graha-focus-ring)' in foundation_css, 'the single focus-visible rule consumes the centralized focus tokens')
on_dark_focus = re.search(r'\.graha-hero,\s*\.graha-cta-panel:not\(\.graha-cta-panel--tint\),\s*\.graha-site-footer,\s*\.graha-page-header\s*{[^}]*--graha-focus-color:\s*var\(--graha-color-inverse\)', foundation_css)
check(on_dark_focus is not None, 'dark surfaces (hero/cta-panel/footer/page-header) redeclare one centralized on-dark focus treatment')
for banned in ('Segera hadir', 'sedang disiapkan'):
    check(banned not in runtime_text, f'no staging/internal copy ships to production: "{banned}"')
front = texts[PLUGIN / 'templates/front-page.php']
check('wp_head()' in front and 'wp_footer()' in front and 'language_attributes()' in front and 'body_class(' in front, 'front-page document preserves WordPress document hooks')

for path, text in texts.items():
    if path != SRC / 'TemplateService.php':
        check('template_include' not in text, f'front-page template ownership is not duplicated: {path.relative_to(ROOT)}')
for token in ('template_redirect', 'add_rewrite_rule', 'wp_redirect', 'wp_safe_redirect'):
    check(token not in php_text, f'no custom route/redirect engine: {token}')
for label, patterns in {
    'custom database writes': [r'\$wpdb\b', r'\bdbDelta\s*\(', r'CREATE\s+TABLE'],
    'unauthenticated mutation endpoints': [r'wp_ajax_nopriv_', r'\bregister_rest_route\s*\('],
    'custom mail transport': [r'\bwp_mail\s*\(', r'\bmail\s*\('],
    'custom payment/order implementation': [r'\bWC_Order\b', r'woocommerce_(?:checkout|payment|order)'],
    'duplicate SEO output': [r'rel=["\']canonical["\']', r'application/ld\+json', r'<meta\s+name=["\'](?:description|robots)["\']'],
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
for need in ('wp_insert_post', 'wp_update_post', 'get_post', 'add_option', 'update_option', '_graha_source_identity', "POST_TYPE = 'graha_product'"):
    check(need in migration, f'migration native/idempotency primitive preserved: {need}')
check("'post_status'=>'draft'" in migration, 'new identity-only products remain draft')
consumed = re.search(r"'status'\s*=>\s*'consumed'", migration)
cleanup_pos = migration.find('->cleanup(')
check(consumed is not None and cleanup_pos > consumed.start(), 'logical consumed state is persisted before cleanup call')
check('Source identity collision' in migration and 'source identity lain' in migration, 'source identity collision guards remain explicit')

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
for requirement_id in range(32, 43):
    check(f'REQ-{requirement_id:03d}' in traceability, f'traceability includes REQ-{requirement_id:03d}')
check('SiteLifecycleService' in traceability and 'request-quote' in traceability, 'traceability records lifecycle/bootstrap contract')

check('setInterval(' not in texts[PLUGIN / 'assets/js/admin-migration.js'], 'migration screen has no polling loop')
check("addEventListener('click'" in texts[PLUGIN / 'assets/js/admin-migration.js'], 'migration work starts only from explicit user action')
check('button.disabled = true' in texts[PLUGIN / 'assets/js/admin-migration.js'], 'migration UI suppresses double-click while request runs')

print('Repository contract guards passed.')
