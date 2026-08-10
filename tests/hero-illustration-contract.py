#!/usr/bin/env python3
from pathlib import Path
import re
import sys
import xml.etree.ElementTree as ET

ROOT = Path(__file__).resolve().parents[1]
PLUGIN = ROOT / 'plugin' / 'graha-selang-site-core'
ILLUSTRATIONS = PLUGIN / 'assets' / 'images' / 'illustrations'
CSS = PLUGIN / 'assets' / 'css'
SRC = PLUGIN / 'src'
PARTIAL = PLUGIN / 'templates' / 'parts' / 'home-hero.php'

EXPECTED = (
    'hero-industrial-system.svg',
    'hydraulic-hose.svg',
    'industrial-hose.svg',
    'ducting-hose.svg',
    'pvc-hose.svg',
    'fittings-couplings.svg',
    'cng-hose.svg',
    'technical-services.svg',
)


def check(condition, message):
    if not condition:
        print(f'FAIL: {message}', file=sys.stderr)
        raise SystemExit(1)
    print(f'PASS: {message}')


check(ILLUSTRATIONS.is_dir(), 'canonical illustration directory exists')
check(tuple(sorted(path.name for path in ILLUSTRATIONS.glob('*.svg'))) == tuple(sorted(EXPECTED)), 'canonical illustration directory contains exactly the eight approved SVGs')

for filename in EXPECTED:
    path = ILLUSTRATIONS / filename
    check(path.is_file(), f'canonical SVG exists: {filename}')
    raw = path.read_text(encoding='utf-8')
    try:
        root = ET.fromstring(raw)
    except ET.ParseError as exc:
        check(False, f'valid XML/SVG: {filename} ({exc})')
    tag = root.tag.split('}', 1)[-1]
    check(tag == 'svg', f'root element is svg: {filename}')
    check(bool(root.attrib.get('viewBox')), f'viewBox is explicit: {filename}')
    check(bool(root.attrib.get('width')) and bool(root.attrib.get('height')), f'intrinsic SVG dimensions are explicit: {filename}')

    local_tags = [node.tag.split('}', 1)[-1].lower() for node in root.iter()]
    for forbidden in ('script', 'foreignobject', 'image', 'text', 'animate', 'animatetransform', 'animatemotion', 'set'):
        check(forbidden not in local_tags, f'forbidden SVG element absent ({forbidden}): {filename}')
    check('base64' not in raw.lower(), f'no base64 payload: {filename}')
    for node in root.iter():
        for attr_name, value in node.attrib.items():
            local_attr = attr_name.split('}', 1)[-1].lower()
            if local_attr in ('href', 'src'):
                check(not re.match(r'(?i)^https?://', value.strip()), f'no external href/src reference: {filename}')
    check(path.stat().st_size < 16000, f'SVG remains small/reviewable: {filename}')

assets = (SRC / 'AssetService.php').read_text(encoding='utf-8')
templates = (SRC / 'TemplateService.php').read_text(encoding='utf-8')
partial = PARTIAL.read_text(encoding='utf-8')
home_css = (CSS / 'home.css').read_text(encoding='utf-8')
entry = (PLUGIN / 'graha-selang.php').read_text(encoding='utf-8')
kernel = (SRC / 'Kernel.php').read_text(encoding='utf-8')

check("ILLUSTRATION_RELATIVE_PATH = 'assets/images/illustrations/'" in assets, 'AssetService retains canonical illustration ownership')
check("const HOME_STYLE" in assets and "'graha-selang-home'" in assets, 'AssetService owns the Home stylesheet handle')
check("assets/css/home.css" in assets and 'public function enqueue_home()' in assets, 'AssetService registers and exposes the Home-only enqueue path')
check(re.search(r'is_front_page\(\)\s*\)\s*\{\s*\$this->assets->enqueue_home\(\)', templates) is not None, 'real front page uses the Home asset path')
check(re.search(r"is_singular\( 'page' \)\s*\)\s*\{\s*\$this->assets->enqueue_shell\(\)", templates) is not None, 'non-front singular Pages keep shell-only assets')
check("hero-industrial-system.svg" in templates, 'TemplateService resolves the canonical Hero illustration')
check("templates/parts/home-hero.php" in templates, 'TemplateService delegates Hero markup to the dedicated partial')

check(PARTIAL.is_file(), 'dedicated Home Hero partial exists')
check(partial.count('<h1') == 1, 'Hero partial contains exactly one intended H1')
check('graha-home-hero__illustration' in partial and '<img' in partial, 'Hero uses a normal external image element')
check('width="800"' in partial and 'height="640"' in partial, 'Hero image has explicit intrinsic dimensions')
check('loading="eager"' in partial and 'fetchpriority="high"' in partial, 'Hero image is eager and high priority')
check('decoding="async"' in partial, 'Hero image uses explicit decoding behavior')
check('alt=""' in partial, 'Hero illustration is decorative beside equivalent text')
check('graha-hero__group-list' not in partial and 'Enam kelompok produk' not in partial, 'Hero no longer contains the six-category checklist')
check(partial.count('graha_render_button(') <= 2, 'Hero exposes at most two CTA render calls')

check((CSS / 'home.css').is_file(), 'Home-specific stylesheet exists now that Home composition is material')
check('@import' not in home_css, 'Home stylesheet introduces no CSS import dependency')
check(re.search(r'https?://', home_css) is None, 'Home stylesheet introduces no external media/UI URL')
check('#' not in re.sub(r'/\*.*?\*/', '', home_css, flags=re.S), 'Home stylesheet adds no literal color palette')
check('graha-home-hero' in home_css and 'graha-site-main--home' in home_css, 'Home stylesheet is scoped to Home/Hero composition')
check('animation' not in home_css.lower() and 'parallax' not in home_css.lower(), 'Home Hero adds no animation/parallax system')

public_assets = '\n'.join(
    path.read_text(encoding='utf-8')
    for path in list((PLUGIN / 'assets' / 'css').glob('*.css'))
    + list((PLUGIN / 'assets' / 'js').glob('*.js'))
)
for marker in (
    'cdn.jsdelivr.net/npm/bootstrap',
    'unpkg.com/react',
    'unpkg.com/vue',
    'cdn.tailwindcss.com',
    'elementor/assets',
):
    check(marker not in public_assets.lower(), f'no external UI framework asset introduced: {marker}')

check('Version: 0.7.7' in entry, 'plugin header version is 0.7.7')
check("const VERSION = '0.7.7'" in kernel, 'Kernel runtime version is 0.7.7')

print('Hero illustration contract checks passed.')
