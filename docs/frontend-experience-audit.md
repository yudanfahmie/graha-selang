# Frontend Experience and Page Coverage Audit

## Scope and audit boundary

This document is a repository-only implementation blueprint. It records the current presentation state at repository HEAD `88da3dc9ee7447619ffb39e344b5230ab10855b4` and proposes the smallest maintainable next implementation sequence. It does not approve unknown production routes, business facts, product specifications, taxonomy assignments, legal destinations, contact details, or RFQ provider behavior.

Gloskin is used only as a maturity reference for composition principles. No Gloskin branding, healthcare content, route names, domain models, colors, or copy are transferable to Graha Selang.

## 1. Executive diagnosis

Graha Selang already has the correct architectural foundation for a branded WordPress presentation layer: one `TemplateService` coordinator, one `AssetService`, native `graha_product` ownership, a normalized navigation tree, a plugin-owned shell, reusable composition helpers, responsive primitives, self-hosted Instrument Sans, and branded product archive/single/category/brand surfaces.

The main frontend weakness is no longer absence of a design system; it is **composition depth and route-family completion**. The current Home has a deliberate first viewport and the required unequal product hierarchy, but nearly every post-hero module is placed inside one shared wide container and differentiated mainly by vertical spacing plus a top border. That structure makes multiple conceptually different sections read as one long white content stack.

The Home contract is also only partially represented. It currently exposes Product and Technical Consultation as discovery doors, but the four-door brief requires Product, Application / Industry, Brand, and Specification Need. Applications, brands, capabilities, industries, company proof, and article/editorial discovery are not yet composed as distinct Home modules. Missing destinations must not be fabricated; these sections must reduce or omit until authoritative routes/data exist.

The product route families are materially ahead of the general page families. `/products/`, `/product/{slug}/`, `/product-category/{slug}/`, and `/brand/{slug}/` are connected to native WordPress queries and branded presentation. By contrast, `application`, `article`, `search`, and `not_found` exist in `TemplateService::FAMILIES` without equivalent complete route/template ownership. Every unmapped singular Page currently falls back to the `legal` family, which is not an appropriate long-term classification for future application, evergreen/topic, or service-detail Pages.

## 2. Current Home anatomy

### Document and global shell

- `templates/front-page.php` owns the WordPress document shell for `is_front_page()` and preserves `wp_head()`, `wp_body_open()`, and `wp_footer()`.
- `TemplateService::render_front_page_shell()` composes the branded main region and then includes `templates/shell.php`.
- `templates/shell.php` owns the skip link, global header, main content, and global footer.
- `templates/parts/header.php` renders the plugin-owned Graha wordmark, normalized primary navigation, and Request Quote CTA only when a real published RFQ destination exists.

### Actual first viewport

The actual first-viewport hero is built directly in `TemplateService::render_front_page_shell()`, outside `templates/native-home.php`.

Current hero anatomy:

- eyebrow: Graha Selang;
- one H1 describing industrial and hydraulic hose solutions;
- lead constrained to approximately 42 characters per line;
- primary Product Catalog CTA;
- secondary Request Quote CTA when the published Page exists;
- desktop `3fr / 2fr` text-to-visual split at the wide breakpoint;
- right-side `graha-media-frame--pattern` containing a title and six-item text/check list.

The right side is therefore not a true industrial visual asset. It is a gradient/pattern panel plus repeated product-group labels.

### Post-hero Home container

After the hero, `render_front_page_shell()` opens one:

`graha-container graha-container--wide graha-stack--large`

and places the complete `native-home.php` output inside it. This means all post-hero Home modules share the same page-width ownership rather than each section owning a full-width surface with its own inner container.

### Current `native-home.php` sequence

1. Optional existing front-page editor content.
2. Discovery/orientation section: Product and Technical Consultation only.
3. Product section: six approved groups in `graha-priority-grid`.
4. Company/proof section: Services and About pathways.
5. Closing consultation CTA.

The product section correctly preserves the approved hierarchy:

- Hydraulic Hose / MORGEN — anchor;
- Industrial Hose & Assembly / HAMMER + SUNFLEX — anchor;
- Ducting Hose — support;
- PVC Spiral / Spring / Suction Hose — support;
- Fittings / Couplings / Accessories — support;
- CNG / High-pressure Gas Hose — specialist.

Each group can currently render up to six product-name links. With six groups this can become text-dense even though the visual weight of anchor/support/specialist groups is correctly unequal.

## 3. Current visual-system diagnosis

### What is already sound

`assets/css/tokens.css` already provides a coherent Graha system rather than an arbitrary palette:

- Graha primary blue and hover blue;
- deep navy;
- approved pale-blue tint;
- white and industrial neutral surfaces;
- border strengths;
- soft/elevated shadows;
- responsive spacing scale;
- 50rem / 80rem / 90rem content widths;
- focus tokens;
- restrained motion tokens.

`assets/css/fonts.css` correctly self-hosts Instrument Sans in Latin and Latin Extended variable-font subsets with `font-display: swap`. `AssetService` preloads the primary Latin subset. This should remain unchanged.

`foundation.css` already contains useful composition primitives: container, grid, split, priority grid, section heading, buttons, discovery cards, feature cards, trust strip, sparse state, CTA panel, focus treatment, responsive collapse, and reduced-motion behavior.

### Why the current Home still feels flat

The issue is not insufficient colors. `shell.css` gives `.graha-page-section` vertical padding and adds a border between adjacent sections, but it does not give Home sections distinct full-width surface ownership. Most of the Home therefore reads as:

`white canvas -> spacing -> border -> white canvas -> spacing -> border`

The current hero has a strong contrast surface, and the closing CTA has another contrast surface, but the modules between them have little tonal rhythm or compositional change. The next design pass should extend the existing blue/navy/neutral system semantically rather than introduce unrelated accent colors.

### Proposed semantic tonal extension

Preserve all existing brand tokens and add semantic aliases/derived roles only as needed:

- `canvas` — page background;
- `surface` — default white content surface;
- `surface-soft` — neutral soft section;
- `surface-brand-soft` — pale Graha-blue section;
- `surface-raised` — white/elevated card surface;
- `surface-contrast` — deep navy section;
- `border-brand-soft` — restrained brand-derived separator;
- `brand-glow` — low-opacity Graha-blue decorative light treatment.

Implementation values should derive from the current primary/navy/tint/neutral tokens and pass contrast review. No unrelated accent color is justified by current brand evidence.

Recommended Home rhythm is alternating and restrained rather than colorful: contrast hero -> neutral buyer orientation -> brand-soft anchors -> white support -> soft discovery -> brand-soft capabilities -> white applications -> soft brands -> controlled contrast proof -> contrast/brand closing CTA.

Use existing `--graha-space-8` and `--graha-space-9` as the primary desktop section rhythm, reducing deliberately on mobile. Preserve current soft/elevated shadows as the two-level shadow hierarchy rather than adding multiple decorative shadow families.

## 4. Hero gap analysis

### Current strengths

- one intended H1;
- readable lead width;
- two meaningful conversion paths;
- deliberate desktop split;
- responsive single-column fallback;
- dark-on-brand treatment with centralized on-dark focus rules;
- no carousel, dashboard imitation, stock image, or external library dependency.

### Current gaps

- the right visual is a repeating pattern plus six text rows, not a bespoke industrial asset;
- the six-item list repeats information introduced again in the product hierarchy below;
- the first viewport is still text-heavy on both columns;
- the visual does not communicate hose construction, assembly, connection, flow, or technical industrial character;
- the graphic language is generic enough that it could belong to a non-industrial B2B site.

### Target hero contract

The target should remain modern, elegant, technical, industrial, precise, and premium B2B. Keep a controlled two-column layout, one H1, one concise lead, and no more than two CTAs. Do not add excessive badges, fake dashboards, random gradients, or oversized meaningless typography.

Replace the checklist panel with one bespoke `hero-industrial-system.svg`: a clean line/duotone technical composition showing an abstract hose/assembly system, fittings/connections, controlled flow paths, and industrial geometry. It must remain illustrative rather than claiming a specific product specification. If the illustration is redundant with the textual proposition, render it decoratively with empty alt text.

The business conversion hierarchy should allow Technical Consultation / RFQ to become the dominant action when its approved destination/provider is production-ready, with Product Discovery as the secondary action. Until then, the existing real-destination omission rule remains authoritative.

## 5. Card and illustration gap

### Existing card primitives

- `.graha-card` — retain as the structural category/showcase base.
- `.graha-card--anchor` / `--specialist` — retain; they correctly preserve unequal hierarchy.
- `.graha-discovery-card` — retain for discovery doors.
- `.graha-feature-card` — retain/extend for capabilities.
- `.graha-product-card` — retain for native catalog records.
- `.graha-trust-strip` — retain as the compact proof/trust primitive.

Do not create a separate generic component for every Home section.

### Target small card hierarchy

1. **Category Showcase Card** — specialized `.graha-card` with optional illustration/media slot; anchor/support/specialist modifiers remain.
2. **Discovery Card** — existing `.graha-discovery-card` for real crawlable entry doors.
3. **Capability Card** — existing `.graha-feature-card` with optional industrial UI/illustration support.
4. **Product Card** — existing `.graha-product-card` for actual `graha_product` records.
5. **Proof / Trust Item** — existing trust-strip pattern or one compact modifier, not another general card system.

Category cards should stop behaving primarily as long product-name lists. Where native product data exists, show a small representative subset (for example two or three real links) and preserve a real broader destination only when an authoritative category/archive route exists. Do not synthesize category URLs from the approved Home labels.

### Existing SVG capability

The current codebase has two distinct SVG uses:

- **small UI icons**: inline `box`, `gear`, `tag`, `chat`, `check`, and `arrow` paths in `composition-helpers.php`;
- **brand assets**: committed Graha logo/wordmark SVG files under `assets/images/`.

There is currently no illustrative SVG family and no `assets/images/illustrations/` directory.

## 6. Footer gap

The current footer is a credible foundation. It already contains:

- pre-footer Request Quote CTA when the route exists;
- brand name and concise positioning line;
- Explore links for real Product/Services/About destinations;
- Consultation links for real RFQ/Contact destinations;
- copyright bottom row.

The target footer should become more complete without fabricating facts:

- keep the strong pre-footer CTA;
- use the canonical Graha wordmark in the brand block, with text fallback;
- retain one concise positioning line;
- add product/discovery navigation only for real destinations;
- add services/company navigation only for real destinations;
- expose technical consultation paths only when configured;
- add legal/trust links only after approved destinations exist;
- allow one subtle decorative industrial line/pattern asset, not a factual workshop/product image;
- keep a restrained bottom copyright/meta row.

Do not hard-code phone, email, address, WhatsApp, certification, authorization status, or legal URLs. Missing data remains omitted.

## 7. Brief and page-coverage diagnosis

The frozen brief remains a migration baseline, not a Page creation target:

- 68 product/series intents;
- 18 hub intents;
- 4 application intents;
- 5 merge/redirect intents;
- 1 retire intent;
- 96 total legacy intents;
- 90 retained content intents.

The detailed family status is maintained in `docs/page-coverage-audit.csv`.

### Current strongest coverage

Native product ownership is explicit and connected:

- `/products/` -> `ProductPresentation::identify_family()` -> `product_archive`;
- `/product/{slug}/` -> `product_single`;
- `/product-category/{slug}/` -> `product_category`;
- `/brand/{slug}/` -> `brand`.

About and Services Hub are also explicitly mapped singular Page families, and both have family-specific supporting composition around editor-owned content.

### Declared family is not equivalent to implemented route

`TemplateService::FAMILIES` contains `application`, `article`, `search`, and `not_found`, but current route resolution does not connect each of those to a full branded document/template path.

`STATIC_PAGE_FAMILIES` maps only:

- `about-us` -> `about`;
- `layanan-kami` -> `service`;
- `contact-us` -> `contact`;
- `request-quote` -> `technical_rfq`.

Every other singular Page currently returns the `legal` family from `family_for_page_slug()`. That generic fallback is acceptable only as a temporary branded wrapper for unknown Pages; it is **not** the future route classification for applications, evergreen/topic pages, or approved service details. Once the crawl/approval resolves those routes, they need explicit family selection without creating a generic virtual route engine.

### Environment blockers remain real

The repository still lacks the fresh production crawl and exact 4 application routes. It also lacks approved provider/contact/legal inputs required to finish RFQ, Contact, and Legal/Trust behavior. The audit therefore does not invent destinations or convert specialist themes into new pages.

## 8. Target Home architecture

Target: **10 meaningful sections**, each owning its section surface and inner container rather than living inside one post-hero wrapper.

| # | Section | Primary data dependency | Target behavior |
|---|---|---|---|
| 01 | Hero | ALWAYS SAFE | One H1, concise proposition, maximum two real CTAs, bespoke decorative industrial SVG. |
| 02 | Buyer orientation / trust | ALWAYS SAFE | Explain how to enter the site without unsupported proof claims; factual badges require later approval. |
| 03 | Two anchor product families | REAL NATIVE DATA REQUIRED | Give Hydraulic/MORGEN and Industrial/HAMMER+SUNFLEX greater area and hierarchy; real products only. |
| 04 | Supporting families + CNG specialist | REAL NATIVE DATA REQUIRED | Three support groups plus deliberate specialist treatment; gracefully sparse. |
| 05 | Four discovery entry doors | APPROVED PAGE REQUIRED | Product may render immediately; Application/Industry, Brand, and Specification Need render only with real authoritative destinations/data. |
| 06 | Technical services / capabilities | APPROVED BUSINESS FACT REQUIRED | Use approved capability facts and real Services destination; no inferred workshop claims. |
| 07 | Applications / industries | APPROVED PAGE REQUIRED | Use only reconciled application routes; do not turn the thematic list into doorway pages. |
| 08 | Brand discovery | REAL NATIVE DATA REQUIRED | Read real `graha_product_brand` terms/membership; no inferred authorization or ranking claims. |
| 09 | Company / proof | APPROVED BUSINESS FACT REQUIRED | Show approved company/workshop/project/trust evidence; omit unavailable proof. |
| 10 | Technical consultation / RFQ | APPROVED PAGE REQUIRED | Strong close using published RFQ/contact routes; provider-specific behavior stays configuration-owned. |

A section whose dependency is absent must either reduce to the subset supported by real data or disappear. Empty production-looking shells are not acceptable.

## 9. Target visual language

The visual direction should be **modern industrial precision**, not generic SaaS.

- Preserve Graha blue as the primary identity and navy as the contrast anchor.
- Use pale-blue and neutral-gray surfaces for rhythm rather than introducing unrelated accents.
- Use full-width section surfaces with inner 80/90rem containers.
- Keep borders thin and technical; use brand-soft borders only to signal important categories.
- Use the existing soft shadow for default raised surfaces and elevated shadow only on meaningful hover/priority states.
- Use restrained gradients only from existing blue/navy/tint values.
- Maintain Instrument Sans and the current type scale; do not create a second typography system.
- Keep interaction movement small (1–2px lift or equivalent) and honor reduced motion.
- Allow technical line motifs in section backgrounds at low opacity, but never make decoration compete with product/navigation content.

## 10. SVG illustration contract

### Proposed inventory only

Store future assets under:

`plugin/graha-selang-site-core/assets/images/illustrations/`

Proposed family:

- `hero-industrial-system.svg`
- `hydraulic-hose.svg`
- `industrial-hose.svg`
- `ducting-hose.svg`
- `pvc-hose.svg`
- `fittings-couplings.svg`
- `cng-hose.svg`
- `technical-services.svg`

### Style contract

- bespoke line/duotone industrial illustration;
- one consistent technical 3/4 or restrained orthographic perspective;
- consistent stroke weight, corner treatment, and detail density;
- Graha primary blue, navy, controlled pale-blue fills, and neutral/white only;
- no cartoon language;
- no downloaded generic icon/SVG pack;
- no rasterized images embedded in SVG;
- no base64 payloads;
- no `<script>`;
- no `foreignObject`.

### Geometry and loading

Recommended conventions:

- hero: `viewBox="0 0 1200 800"`, target aspect ratio 3:2;
- category illustrations: `viewBox="0 0 640 480"`, target aspect ratio 4:3;
- capability illustration: `viewBox="0 0 800 600"`, target aspect ratio 4:3.

Commit explicit SVG viewBox dimensions and reserve CSS aspect ratio so illustration loading does not shift layout.

If `hero-industrial-system.svg` becomes the actual LCP candidate, load it eagerly and with high fetch priority, with explicit intrinsic/display dimensions. Below-fold category/capability illustrations should load lazily and decode asynchronously.

Hero/category illustration images should normally be decorative because adjacent headings/cards already carry the meaning; use empty alt text. Only provide meaningful alt text when an illustration contains information not otherwise expressed in adjacent semantic HTML.

`AssetService::image_url()` and `image_path()` already own committed image URLs/paths and can address nested `illustrations/...` files. Do not create another asset service.

## 11. Page-family implementation gaps

### Applications

The exact four retained application URLs are not established by current repository evidence. `application` is declared but no Page slug mapping connects an actual Page to that family. Keep this blocked by crawl; do not invent slugs.

### Service details

Only `/layanan-kami/` is approved as the retained service hub. Detail Pages are optional when distinct approved content exists. Current generic Pages fall to `legal`; do not add detail routes until approval demonstrates a real need.

### Articles

Native Posts receive foundation/content enhancement, but `resolve_native_template()` deliberately leaves Post document ownership to the active theme. There is no equivalent dedicated branded article document composition, and no explicit `/articles/` hub ownership in the runtime. Article Single is therefore partial; Articles Hub is missing.

### Evergreen / topic Pages

These belong to native Pages only after migration classifies a real URL as KEEP. Current unmapped Page -> `legal` behavior must not be mistaken for topic-family implementation.

### RFQ and Contact

Both have explicit mapped Page families and safe branded fallbacks, but required provider/NAP/routing/upload/WhatsApp details remain unresolved deployment inputs. Their presentation shells exist; production experience is partial.

### Legal / Trust

The generic `legal` family exists, but no approved legal route or content is supplied. Do not create fake legal Pages or links.

### Search and 404

Both families are declared in `FAMILIES`, but neither has current template resolution/document-shell ownership. They require narrow native WordPress condition handling in a later page-family pass.

### Redirect and retire

The 5 redirect and 1 retire baseline rows are migration/HTTP actions, not Page records. They remain dependent on the current crawl and final single redirect/status owner.

## 12. Proposed code architecture

Keep the implementation lean.

1. **AssetService remains the only asset owner.** Register any future illustration and optional Home stylesheet through it.
2. **Keep `assets/images/illustrations/` as the single source-controlled bespoke illustration directory.**
3. **Consider `assets/css/home.css` only when Home-specific surfaces/illustration layouts materially improve isolation.** Do not create it merely for file-count symmetry. If introduced, enqueue it conditionally on the front page through `AssetService`.
4. **Make the dedicated Home composition file the owner of Home modules.** `templates/native-home.php` already exists; evolve it toward full-width section composition rather than moving more Home markup into `TemplateService`. A future rename/reorganization is optional, not required for correctness.
5. **TemplateService remains routing/context/presentation coordinator.** It should identify real WordPress contexts, collect safe context, and delegate composition rather than become a giant Home/page markup file.
6. **ProductPresentation remains the narrow native product-route adapter.** Do not duplicate product querying/routing inside Home templates.
7. **WordPress remains content owner.** Pages, Posts, Media, `graha_product`, terms, and registered meta stay authoritative.
8. **Connect new page families only when real routes are known.** Use native conditionals and explicit family mapping; do not introduce a virtual route engine.
9. **No new bootable service is justified by this audit.**

### Transferable Gloskin maturity principles

The mature Gloskin implementation demonstrates several engineering principles that are appropriate to transfer without copying its domain:

- dedicated page-family composition files;
- a Home template composed from distinct semantic modules;
- full-width section surfaces with inner containers;
- alternating soft/default/contrast surface rhythm;
- small reusable rendering primitives;
- conditional omission of data-driven sections;
- route-specific context composition separated from rendering.

Graha should adopt those principles using its existing blue/navy system, native Graha product model, approved routes, and industrial content only.

## 13. Performance and accessibility contract

Every implementation pass must preserve:

- server-rendered primary content;
- normal crawlable anchors for important destinations;
- navigation usable without JavaScript, with JS only enhancing disclosure behavior;
- one intended H1;
- visible centralized keyboard focus treatment;
- WCAG-practical contrast on white, soft, brand-soft, and contrast surfaces;
- no horizontal overflow from cards, SVGs, or technical tables;
- `prefers-reduced-motion` support;
- explicit SVG viewBox/dimensions/aspect ratio to prevent layout shift;
- eager/high-priority hero illustration only when it is the real LCP candidate;
- lazy below-fold illustrations;
- responsive image/Media Library ownership for factual raster media;
- no external frontend framework, icon library, or animation dependency;
- current AssetService version/cache-busting architecture.

Home CSS, if separated, must be conditionally loaded only where needed and must depend on the existing tokens/foundation rather than re-declaring a second design system.

## 14. Atomic implementation roadmap

### PASS 1 — Visual surfaces and illustration contract

Extend existing semantic tokens and section-surface primitives; establish the source-controlled illustration directory contract. No route/data changes. Independently verify contrast, focus, responsive spacing, and reduced motion.

### PASS 2 — Bespoke illustrations and hero reconstruction

Add the approved SVG family and replace the current pattern/checklist hero visual with the industrial illustration. Preserve one H1, real CTAs, explicit dimensions, LCP behavior, and mobile composition.

### PASS 3 — Home section architecture

Recompose Home into the 10-section journey with full-width surfaces + inner containers. Extend the existing category/discovery/capability primitives, reduce product-name density, and omit every unsupported route/data-dependent module.

### PASS 4 — Footer reconstruction

Upgrade the footer brand/discovery/consultation composition using only real destinations and approved facts. Add legal/trust only when approved. Add only decorative industrial pattern treatment.

### PASS 5 — Page-family and route-presentation coverage

After crawl/provider approvals, connect application/topic/service-detail/article/search/404 contexts to the correct explicit family compositions. Resolve the generic `legal` fallback problem for known non-legal Pages. Keep redirects/retire with their single migration/HTTP owner.

Each pass must be independently reviewable and verified. Do not combine these five passes into one implementation commit.

## 15. Explicit non-goals

This audit does not authorize:

- PHP, CSS, or JavaScript changes in this phase;
- plugin version changes;
- creation of WordPress Pages;
- product-data or taxonomy mutation;
- invention of the four application slugs;
- invention of product specifications, certifications, business claims, NAP, WhatsApp, legal links, or authorization status;
- a new asset service, route engine, design-system service, or generic service layer;
- React, Vue, Tailwind, Bootstrap, Elementor dependency, external icon frameworks, or animation libraries;
- a new custom database;
- copying Gloskin branding, content, routes, models, or visual identity;
- implementation of the visual changes described above before the corresponding atomic pass is approved.

## 16. Phase B accepted implementation contract

Phase A diagnosis remains historical and binding. Phase B approves and implements only PASS 1 visual foundation; the Hero, final Home composition, Footer, and page-family passes remain later work.

Accepted implementation contract for release `0.7.4`:

- semantic surface roles live in `assets/css/tokens.css`: `--graha-color-canvas`, `--graha-color-surface-soft`, `--graha-color-surface-brand-soft`, `--graha-color-surface-raised`, `--graha-color-surface-contrast`, `--graha-color-border-brand-soft`, and `--graha-color-brand-glow`, plus explicit on-canvas/on-surface/on-contrast foreground roles. They are aliases/derivations of the existing approved Graha palette; primary blue, deep navy, tint, white and neutral literals remain unchanged;
- the reusable full-width band is `.graha-section`; the outer section owns surface, vertical rhythm, and optional decorative context while `.graha-section__inner` is paired with the existing `.graha-container` and width modifiers for horizontal alignment. Foundation modifiers are `--default`, `--soft`, `--brand-soft`, `--contrast`, `--compact`, `--major`, and optional `--brand-glow`;
- normal/major spacing uses the existing space-8/space-9 tokens and reduces at the existing mobile breakpoint; no device-specific layout table is introduced;
- existing category, discovery, capability, and trust primitives consume semantic raised surfaces, precise borders, and restrained hover lift/soft shadow. `.graha-card__visual` / `--illustration` reserves a 4:3 contained external-image/SVG slot for Phase C without creating artwork now;
- `home.css` is deliberately **not added in Phase B**. No new Home-only composition exists yet, so an extra stylesheet/request would only be placeholder overhead. If later Home work introduces material Home-only styling, `AssetService` remains the only valid loader and conditional ownership must be added then;
- future bespoke SVGs remain under `assets/images/illustrations/`. `AssetService::ILLUSTRATION_RELATIVE_PATH`, `illustration_url()`, and `illustration_path()` are the canonical narrow path helpers; no second asset service or embedded/base64 SVG transport is introduced;
- contrast sections deliberately own foreground, muted text, eyebrow, outline-button, and focus behavior; the existing centralized focus-visible and reduced-motion contracts remain authoritative;
- no Phase C illustration asset, Hero reconstruction, 10-section Home composition, Footer redesign, route expansion, data mutation, taxonomy change, or external UI dependency is included in this release.

## 17. Phase C accepted implementation contract

Phase A diagnosis and the accepted Phase B foundation remain historical and binding. Phase C implements PASS 2 only: the bespoke illustration family and premium first-viewport Hero. The downstream `native-home.php` journey, Footer reconstruction, and page-family/route work remain later passes.

Accepted implementation contract for release `0.7.5`:

- the canonical source-controlled illustration family now lives at `plugin/graha-selang-site-core/assets/images/illustrations/` and consists of `hero-industrial-system.svg`, `hydraulic-hose.svg`, `industrial-hose.svg`, `ducting-hose.svg`, `pvc-hose.svg`, `fittings-couplings.svg`, `cng-hose.svg`, and `technical-services.svg`;
- all eight assets use one restrained technical line/duotone grammar: deep-navy structural strokes, Graha-blue system paths/highlights, pale-blue supporting geometry, and white controlled surfaces. They remain text-free, animation-free, raster-free, and externally self-contained; the Hero is an abstract engineered hose-routing system rather than a fake specification/CAD sheet;
- `templates/parts/home-hero.php` is the actual Hero markup owner. `TemplateService::render_front_page_shell()` prepares only real Product/RFQ destinations plus the canonical Hero illustration URL, then delegates the first-viewport presentation to that partial; the former six-group checklist is removed from the Hero;
- `assets/css/home.css` now has a justified Home-only role. `AssetService::HOME_STYLE` registers it as a dependency of the existing shell chain, `AssetService::enqueue_home()` is the only public loader, and `TemplateService::prepare_native_presentation()` selects that path only for `is_front_page()`; normal Pages continue to use shell-only assets;
- `hero-industrial-system.svg` uses an `800 × 640` (`5:4`) intrinsic/viewBox contract. The Hero partial emits it as a normal external `<img>` with explicit dimensions, `loading="eager"`, `fetchpriority="high"`, and `decoding="async"` so the above-fold image is discoverable early and does not introduce avoidable layout shift;
- the Hero illustration is decorative reinforcement beside equivalent textual meaning and therefore renders with `alt=""` and no focusable SVG behavior. The Hero preserves one intended H1, at most two real-destination CTAs, the centralized on-dark focus treatment, responsive single-column fallback, and the existing reduced-motion contract;
- supporting category/capability SVGs keep the established 4:3 card-media geometry but are deliberately not wired into the final Home category architecture yet. No Phase D 10-section reconstruction, Footer redesign, route expansion, product/data mutation, taxonomy change, contact/RFQ invention, or external UI dependency is part of this release.
