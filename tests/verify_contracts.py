#!/usr/bin/env python3
from pathlib import Path
import re
import sys

ROOT = Path(__file__).resolve().parents[1]
production_files = [ROOT / 'graha-selang.php']
production_files += sorted((ROOT / 'src').glob('*.php'))
production_files += sorted((ROOT / 'assets').rglob('*.css'))
production_files += sorted((ROOT / 'assets').rglob('*.js'))

texts = {path: path.read_text(encoding='utf-8') for path in production_files}
php_text = '\n'.join(text for path, text in texts.items() if path.suffix == '.php')
runtime_text = '\n'.join(texts.values())

def fail(message):
    print(f'FAIL: {message}')
    sys.exit(1)

def check(condition, message):
    if not condition:
        fail(message)
    print(f'PASS: {message}')

check(php_text.count('final class Kernel') == 1, 'exactly one Kernel composition root')

kernel = texts[ROOT / 'src' / 'Kernel.php']
owners = re.findall(r'new\s+([A-Z][A-Za-z0-9_]+)\s*\(', kernel)
check(len(owners) == len(set(owners)), 'Kernel composes each owner once')
check(1 <= len(owners) <= 8, f'bootable owner count within limit ({len(owners)}/8)')
check(set(owners) == {'AssetService', 'NavigationService', 'TemplateService', 'AdminService'}, 'only justified Wave 1 owners are active')

admin = texts[ROOT / 'src' / 'AdminService.php']
check(php_text.count('add_menu_page(') == 1, 'exactly one Graha root admin menu registration')
check(admin.count('add_submenu_page(') == 1, 'Ringkasan is registered as the only current child page')
check("const MENU_SLUG           = 'graha-selang-content';" in admin, 'canonical admin slug preserved')
check(re.search(r"add_menu_page\(.*?'dashicons-admin-site-alt3',\s*3\s*\)", admin, re.S) is not None, 'default admin menu position is 3')
check("const OVERVIEW_CAPABILITY = 'edit_pages';" in admin, 'Ringkasan capability is explicit')
check("add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );" in admin, 'AdminService owns admin enqueue hook')
check("in_array( $hook_suffix, $this->overview_hooks, true )" in admin, 'admin asset enqueue is screen-scoped')

asset = texts[ROOT / 'src' / 'AssetService.php']
check(php_text.count('final class AssetService') == 1, 'exactly one first-party asset owner')
for path, text in texts.items():
    if path.suffix == '.php' and path.name != 'AssetService.php':
        check(re.search(r'\bwp_(?:register|enqueue)_(?:style|script)\s*\(', text) is None, f'no direct asset registry/enqueue outside AssetService: {path.relative_to(ROOT)}')
check("add_action( 'wp_enqueue_scripts', array( $this, 'register_public_assets' ), 5 );" in asset, 'public assets are registered centrally')
register_method = asset.split('public function register_public_assets()', 1)[1].split('public function enqueue_foundation()', 1)[0]
check('wp_enqueue_' not in register_method, 'public registration hook does not globally enqueue assets')
check('admin_enqueue_scripts' not in asset, 'AssetService does not globally hook admin enqueue')
check("const TOKENS_STYLE         = 'graha-selang-tokens';" in asset, 'AssetService owns one explicit token stylesheet')
check("const SHELL_STYLE          = 'graha-selang-shell';" in asset, 'AssetService owns conditional shell styling')

navigation = texts[ROOT / 'src' / 'NavigationService.php']
check(php_text.count('register_nav_menus(') == 1, 'one native WordPress navigation location registration')
check("const PRIMARY_LOCATION = 'graha-primary';" in navigation, 'one canonical primary navigation location')
check('get_nav_menu_locations()' in navigation and 'wp_get_nav_menu_items(' in navigation, 'navigation reads native WordPress menu ownership')
check("return '';" in navigation, 'navigation has safe empty rendering behavior')

template = texts[ROOT / 'src' / 'TemplateService.php']
check(php_text.count('final class TemplateService') == 1, 'exactly one TemplateService presentation owner')
check('graha_selang_prepare_page' in template and 'graha_selang_render_page' in template, 'TemplateService exposes opt-in presentation hooks')
check('function render_breadcrumbs' in template and php_text.count('function render_breadcrumbs') == 1, 'one reusable visible breadcrumb renderer')
check(template.count("'product_archive'") == 1 and template.count("'not_found'") == 1 and template.count("'technical_rfq'") == 1, 'TemplateService covers representative documented presentation families')
check("__( 'Beranda', 'graha-selang' )" in template, 'breadcrumb renderer uses Indonesian native Home label')
check('return $ready >= 4;' in template, 'Home renderer enforces four explicit substantial sections')
check('application/ld+json' not in template.lower(), 'breadcrumb renderer emits no schema graph')

css_texts = {path: text for path, text in texts.items() if path.suffix == '.css'}
tokens = css_texts[ROOT / 'assets' / 'css' / 'tokens.css']
required_tokens = [
    '--graha-font-sans:', '--graha-type-md:', '--graha-weight-bold:', '--graha-leading-copy:',
    '--graha-space-4:', '--graha-content-max:', '--graha-breakpoint-navigation:', '--graha-color-text:',
    '--graha-radius-md:', '--graha-shadow-soft:', '--graha-control-min:', '--graha-focus-width:', '--graha-motion-fast:',
    '--graha-card-min:', '--graha-logo-max:', '--graha-border-width:', '--graha-link-underline-offset:',
    '--graha-media-ratio-default:', '--graha-z-skip-link:', '--graha-motion-reduced:'
]
for token in required_tokens:
    check(token in tokens, f'central token exists: {token[:-1]}')
for path, text in css_texts.items():
    if path.name not in {'tokens.css', 'admin-overview.css'}:
        check(re.search(r'#[0-9a-fA-F]{3,8}\b', text) is None, f'no one-off hex colors outside token/admin native layers: {path.relative_to(ROOT)}')
media_values = set(re.findall(r'@media\s*\(\s*(?:min|max)-width:\s*(\d+rem)\s*\)', '\n'.join(css_texts.values())))
check(media_values.issubset({'48rem', '64rem'}), f'component media queries use approved breakpoint literals ({sorted(media_values)})')

forbidden = {
    'custom database writes': [r'\$wpdb\b', r'\bdbDelta\s*\(', r'CREATE\s+TABLE'],
    'unapproved custom content types': [r'\bregister_post_type\s*\(', r'\bregister_taxonomy\s*\('],
    'unauthenticated mutation endpoints': [r'wp_ajax_nopriv_', r'\bregister_rest_route\s*\('],
    'custom mail transport': [r'\bwp_mail\s*\(', r'\bmail\s*\('],
    'custom payment/order implementation': [r'\bWC_Order\b', r'woocommerce_(?:checkout|payment|order)'],
    'duplicate SEO output': [r'rel=["\']canonical["\']', r'application/ld\+json', r'<meta\s', r'\bwp_head\b'],
    'route takeover before Wave 0': [r'\btemplate_include\b', r'\btemplate_redirect\b', r'\badd_rewrite_rule\s*\(', r'\bwp_(?:safe_)?redirect\s*\('],
}
for label, patterns in forbidden.items():
    for pattern in patterns:
        check(re.search(pattern, php_text, re.I) is None, f'no {label}: {pattern}')

check(re.search(r'\b(?:gloskin|morgen)\b', runtime_text, re.I) is None, 'no Gloskin/Morgen runtime identifiers')
check('redirect-matrix.csv' not in runtime_text, 'no fabricated redirect matrix dependency in runtime')
check(re.search(r'\b(?:rank_math|wpseo|yoast)\b', php_text, re.I) is None, 'no SEO provider assumption')
check(re.search(r'\b(?:cf7|contact_form_7|gravityforms|wpforms)\b', php_text, re.I) is None, 'no form provider assumption')
check(re.search(r'lorem\s+ipsum', runtime_text, re.I) is None, 'no lorem ipsum in production runtime')
check('MigrationService' not in php_text and 'manifest.json' not in php_text, 'one-shot migration runtime is not fabricated before bundle prerequisites')

contract = (ROOT / 'docs' / 'approved-next-bundle-contract.md').read_text(encoding='utf-8')
traceability = (ROOT / 'docs' / 'requirement-traceability.csv').read_text(encoding='utf-8')
implementation_plan = (ROOT / 'docs' / 'implementation-plan.md').read_text(encoding='utf-8')
verification_contract = (ROOT / 'docs' / 'verification-contract.md').read_text(encoding='utf-8')
check('One-shot migration scope' in contract and 'Production public-page quality' in contract, 'approved next-bundle contract canonicalizes page quality and one-shot migration')
for requirement_id in range(32, 41):
    check(f'REQ-{requirement_id:03d}' in traceability, f'traceability includes REQ-{requirement_id:03d}')
check('approved-next-bundle-contract.md' in implementation_plan, 'implementation plan references approved next-bundle contract')
check('One-shot migration assertions' in verification_contract and 'Homepage production assertions' in verification_contract, 'verification contract covers Homepage and one-shot migration gates')
ajax_contract = (ROOT / 'docs' / 'migration-admin-ajax-contract.md').read_text(encoding='utf-8')
check('authenticated WordPress admin AJAX' in ajax_contract and 'wp_ajax_nopriv_' in ajax_contract, 'migration AJAX contract requires authenticated admin AJAX and forbids nopriv')
check('Migration admin AJAX assertions' in verification_contract, 'verification contract covers lightweight migration AJAX behavior')
check('migration-admin-ajax-contract.md' in implementation_plan, 'implementation plan references migration AJAX contract')

print('All repository contract guards passed.')
