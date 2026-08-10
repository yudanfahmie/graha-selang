#!/usr/bin/env python3
from pathlib import Path
import re
import sys

ROOT = Path(__file__).resolve().parents[1]
PLUGIN = ROOT / 'plugin' / 'graha-selang-site-core'
CSS = PLUGIN / 'assets' / 'css'
SRC = PLUGIN / 'src'


def check(condition, message):
    if not condition:
        print(f'FAIL: {message}', file=sys.stderr)
        raise SystemExit(1)
    print(f'PASS: {message}')


tokens = (CSS / 'tokens.css').read_text(encoding='utf-8')
foundation = (CSS / 'foundation.css').read_text(encoding='utf-8')
assets = (SRC / 'AssetService.php').read_text(encoding='utf-8')
entry = (PLUGIN / 'graha-selang.php').read_text(encoding='utf-8')
kernel = (SRC / 'Kernel.php').read_text(encoding='utf-8')

semantic_tokens = (
    '--graha-color-canvas',
    '--graha-color-surface',
    '--graha-color-surface-soft',
    '--graha-color-surface-brand-soft',
    '--graha-color-surface-raised',
    '--graha-color-surface-contrast',
    '--graha-color-border-brand-soft',
    '--graha-color-brand-glow',
    '--graha-color-on-surface',
    '--graha-color-on-contrast',
)
for token in semantic_tokens:
    check(token in tokens, f'semantic visual token exists: {token}')

check('--graha-color-primary: #0000b8' in tokens, 'approved Graha primary blue remains unchanged')
check('--graha-color-primary-dark: #1a2670' in tokens, 'approved deep navy remains unchanged')
check('--graha-color-primary-tint: #eaecf9' in tokens, 'approved pale-blue tint remains unchanged')
allowed_hex = {
    '#0000b8', '#000091', '#1a2670', '#eaecf9', '#ffffff', '#1b2230', '#565f6e',
    '#d6dae3', '#b7bdc9', '#f4f5f8', '#eceef3', '#c7cbe6',
}
actual_hex = {value.lower() for value in re.findall(r'#[0-9a-fA-F]{6}\b', tokens)}
check(actual_hex <= allowed_hex, f'no unrelated hex accent palette introduced ({sorted(actual_hex)})')

for selector in (
    '.graha-section {',
    '.graha-section__inner',
    '.graha-section--default',
    '.graha-section--soft',
    '.graha-section--brand-soft',
    '.graha-section--contrast',
    '.graha-section--compact',
    '.graha-section--major',
):
    check(selector in foundation, f'section surface primitive exists: {selector}')
check('padding-block: var(--graha-space-8)' in foundation, 'normal section rhythm uses the existing space-8 token')
check('padding-block: var(--graha-space-9)' in foundation, 'major section rhythm uses the existing space-9 token')
check('--graha-container-limit:var(--graha-content-wide)' in foundation.replace(' ', ''), 'existing 90rem wide-container contract remains functional')

check('.graha-card__visual {' in foundation and 'aspect-ratio:4 / 3' in foundation, 'category card illustration slot reserves a known aspect ratio')
check('.graha-card__visual--illustration' in foundation, 'category illustration modifier exists')
check('object-fit:contain' in foundation, 'card illustration media scales without cropping')
check('var(--graha-color-surface-raised)' in foundation, 'card foundation consumes semantic raised-surface ownership')
check('translateY(-2px)' in foundation and 'var(--graha-shadow-soft)' in foundation, 'card motion/elevation remains restrained')

check(':focus-visible' in foundation and 'var(--graha-focus-color)' in foundation and 'var(--graha-focus-ring)' in foundation, 'central focus-visible contract remains active')
check('.graha-section--contrast' in foundation and '--graha-focus-color: var(--graha-color-inverse)' in foundation, 'contrast section owns an explicit on-dark focus treatment')
check('.graha-button--outline:visited' in foundation and '.graha-section--contrast .graha-button--outline:visited' in foundation, 'outline button visited state is deliberate on light and contrast surfaces')
check('@media (prefers-reduced-motion:reduce)' in foundation and '--graha-motion-reduced' in foundation, 'reduced-motion contract remains active')

check("ILLUSTRATION_RELATIVE_PATH = 'assets/images/illustrations/'" in assets, 'AssetService owns the future illustration directory')
check('public function illustration_url(' in assets and 'public function illustration_path(' in assets, 'AssetService exposes narrow Phase C illustration helpers')
check('basename(' in assets, 'illustration helper reduces input to a safe basename')
check((CSS / 'home.css').is_file(), 'Phase C adds Home CSS only after substantial Home-specific composition exists')

for path in CSS.glob('*.css'):
    text = path.read_text(encoding='utf-8')
    check('@import' not in text, f'no CSS import dependency introduced: {path.name}')
    check(re.search(r'https?://', text) is None, f'no external stylesheet/media URL introduced: {path.name}')

check('Version: 0.8.0' in entry, 'plugin header version is 0.8.0')
check("const VERSION = '0.8.0'" in kernel, 'Kernel runtime version is 0.8.0')

print('Visual foundation contract checks passed.')
