# Operational Requirements Contract

## Purpose

This document converts the Graha Selang redesign brief into implementation-facing operational requirements. It is canonical for day-to-day development. Developers must not reopen the raw DOCX to discover routine scope.

The project is a **controlled rebuild of an existing indexed WordPress site**, delivered as a Graha Selang WordPress presentation/site-builder plugin. It is not a blank-slate redesign and it is not a simple visual reskin.

## 1. Baseline URL inventory

The brief freezes the legacy scope baseline at **96 existing URLs**, classified as:

| Classification | Count | Implementation meaning |
|---|---:|---|
| Product / series | 68 | Preserve/migrate as product or product-series intent under the authoritative Woo/content owner. |
| Hub | 18 | Preserve/migrate as useful discovery/entity/topic hubs. |
| Application | 4 | Preserve/migrate as distinct application-intent pages. |
| Merge + permanent redirect | 5 | Consolidate content and redirect the legacy URL to the closest retained destination. |
| Retire | 1 | Remove intentionally with correct not-found/gone handling; never redirect blindly to Home. |
| **Total legacy inventory** | **96** | Every baseline URL must be reconciled before launch. |

Therefore the brief describes **90 retained content-intent URLs** (`68 + 18 + 4`) plus **6 legacy-action URLs** (`5 redirect + 1 retire`). This count is a migration baseline, not permission to manufacture exactly 90 WordPress records if the live environment proves that some records are aliases or taxonomy-owned surfaces.

A fresh production crawl is still mandatory because the live site can change after the brief. The implementation team must reconcile the current crawl against this 96-URL baseline and explain every delta. It must never silently drop URLs merely because the live count differs.

See `docs/scope-inventory.csv` and `docs/legacy-migration-contract.md`.

## 2. Controlled-rebuild rule

Rebuild:

- frontend presentation;
- design system;
- template/component implementation;
- navigation/discovery UX;
- product information presentation;
- RFQ experience;
- performance/accessibility quality.

Preserve or deliberately migrate:

- domain/public identity;
- valuable existing URLs;
- valid content and product records;
- backlinks and search equity through route preservation/redirects;
- analytics/tracking continuity at implementation level;
- SEO-provider ownership/history where still valid.

Do not reset permalinks, product records, taxonomy, analytics or indexation simply because the frontend is rebuilt.

## 3. Product positioning and homepage hierarchy

The brief requires **six visible product groups**, but they are not equal-priority cards.

### Anchor categories — highest emphasis

1. **Selang Hidrolik / Hydraulic Hose — MORGEN-led positioning**
2. **Industrial Hose & Assembly — HAMMER value line + SUNFLEX premium line**

### Core supporting groups

3. **Ducting Hose** — including PU/PVC discovery where approved
4. **PVC Spiral / Spring / Suction Hose**
5. **Fittings, Couplings & Accessories** — including camlock/fitting pathways where applicable

### Specialist group

6. **CNG / high-pressure gas hose assembly**

Engineering rules:

- the two anchor categories receive the strongest homepage hierarchy;
- the three supporting groups remain in the core discovery grid;
- CNG remains visibly discoverable as a specialist card/strip rather than being buried;
- product names, claims and technical differentiation remain content-owned and must be verified;
- do not turn brand positioning into unverified technical superiority claims.

The headline category name for the industrial-hose anchor is **Industrial Hose & Assembly**. Large-bore capability belongs inside that category/application architecture rather than becoming an arbitrary duplicate top-level catalog.

## 4. Four discovery entry doors

The information architecture must support four connected ways to enter the catalog:

1. **By product**
2. **By application / industry**
3. **By brand**
4. **By specification need**

One indexable page should have one primary search/user intent. Do not create near-duplicate pages solely to repeat the same products under keyword variants.

The specification path may use a selector/filter/decision tree, but primary products and navigation must remain discoverable through normal server-rendered links.

## 5. Template / presentation families

The brief requires reusable presentation coverage for the following surfaces. These are **presentation families**, not necessarily separate PHP files or separate persistent WordPress Pages:

- Homepage
- Product/archive hub
- Product category/hub
- Product detail
- Application page
- Brand archive/page
- About/company
- Service hub/detail
- Technical RFQ
- Guide/article
- Legal/trust
- Search/results
- 404/not-found

Implementation may share composition/components between these families. The acceptance target is behavior/coverage, not a forced file count.

## 6. Technical product experience

Product/category/application presentation must be able to support, when approved data exists:

- semantic technical specification table or definition list;
- brand and category context;
- structured size/diameter information;
- working pressure / burst pressure;
- temperature range;
- material/tube/cover/reinforcement;
- standards/certifications only when verified;
- media compatibility;
- bend radius or similar product-specific data where supplied;
- application/industry fit;
- compatible fittings/couplings/connections;
- approved technical resources/datasheet links;
- related products;
- inquiry/RFQ CTA.

Missing technical data is omitted, never inferred. Templates must work for rich and sparse records.

### Product selector/filter

A product selector/filter/decision tree is part of the requested technical discovery experience. It should help users narrow by available structured data such as product family, application, media, size, pressure or connection **only when those attributes are actually present and normalized**.

Rules:

- server-rendered baseline catalog remains usable without JS;
- filters are progressive enhancement and use crawl-safe query behavior;
- do not generate uncontrolled indexable filter combinations;
- no shadow product database;
- WooCommerce/native taxonomy/attributes remain source of truth.

## 7. Brand behavior

The brief calls for a visible brand layer and specifically distinguishes:

- HAMMER as a value/economical line;
- SUNFLEX as a premium line for higher technical requirements;
- MORGEN as a key hydraulic-hose anchor.

This is a presentation/content requirement, not permission to invent specifications or authorization status. Technical differentiation must be supported by approved product facts, datasheets or use cases.

The implementation must reuse the actual authoritative brand taxonomy/provider in the target Woo stack; never register a second competing brand taxonomy just to reproduce the brief.

## 8. Applications and specialist architecture

The site must support application-led discovery and specialist pathways. The brief/audit identifies themes including:

- mining;
- cement / bulk material;
- marine;
- dredging / slurry;
- drilling;
- oil & gas;
- MRO / plant maintenance;
- CNG / high-pressure gas.

The migration baseline contains **4 application URLs**; do not automatically create eight new indexable application pages from the theme list above. The exact retained four are resolved from the canonical URL inventory/current crawl. Additional specialist clusters require distinct useful content and owner approval, not programmatic doorway generation.

## 9. Services

The retained canonical services surface is `/layanan-kami/`.

Required service/capability themes include, where approved:

- crimping and hose assembly;
- custom fitting/coupling/flange work;
- hose selection consultation;
- repair/replacement assessment;
- workshop/assembly capability and proof.

The legacy `/services/` surface is a confirmed consolidation candidate and must not remain a competing service hub. See migration contract.

## 10. Technical RFQ and lead routing

The RFQ is a **technical inquiry experience**, not merely a generic name/email/message form.

Required behavior:

- context-sensitive RFQ entry from product/category/application pages;
- dynamic fields appropriate to the inquiry/application;
- support for an attachment/file upload when the selected form provider can securely handle it;
- capture the originating/source URL or entity context;
- distinguish buyer/end-user inquiry from reseller/cooperation intent where required;
- route/label the lead according to approved business rules;
- provide contextual WhatsApp as a parallel/progressive contact path where configured;
- emit approved conversion events without embedding an analytics management system in the plugin;
- accessible validation, errors and success state;
- spam protection via the form provider;
- explicit file/privacy/retention policy before production file upload is enabled.

### Ownership boundary

Prefer an approved form provider for submission transport, CAPTCHA/spam handling, file storage, mail delivery and retention. `FormAdapter`/presentation code may provide context, conditional configuration and styling.

If the chosen provider cannot satisfy dynamic RFQ/upload/routing requirements, stop and document an architecture decision before building a custom backend. Do not silently create a second mail/submission database.

## 11. Contact and conversion priority

The Contact page should prioritize **buyer technical consultation / RFQ**. Reseller/supplier cooperation may exist as a secondary routed intent, but should not displace the primary buyer path.

Required conversion pathways should be consistent across:

- product detail;
- category/hub;
- application;
- service;
- Contact/RFQ;
- contextual WhatsApp.

## 12. About, trust and proof

About/company presentation must support approved:

- company overview/history;
- vision and mission as content on the retained About surface unless a distinct URL is specifically justified;
- workshop/store/office information;
- brands supplied;
- service/capability proof;
- relevant project/application proof;
- technical review/testing/inspection proof only when verified.

Do not hard-code importer/distributor/authorization/certification claims without approved evidence.

## 13. Legal/trust surfaces

Current placeholder legal links must not ship as `href="#"` or dead controls.

Before launch:

- every footer legal link must resolve to a real approved page, provider surface or be intentionally removed;
- privacy handling must cover RFQ/form/file upload behavior;
- cookie/consent UI is only implemented where the actual tracking stack/legal policy requires it;
- no fake Terms/Privacy/Cookies pages generated from template filler.

## 14. Language and public-copy quality

Primary UI language is Indonesian.

Implementation requirements:

- document language should be `id-ID` or the correct WordPress locale output for the Indonesian site;
- do not leak English template strings such as `Archives: Products`, `Read more` or `Product Categories` when the surrounding public UI is Indonesian;
- technical English synonyms may appear naturally where they are part of product terminology;
- exactly one intended H1 per page;
- do not expose editorial controls such as `Keyword utama`, `Meta Title`, `Meta Description`, prompts or SEO notes in public body content.

## 15. Homepage/LCP requirement

The rebuild must fix first-viewport hierarchy and avoid repeating the current heavy-hero pattern.

Target behavior:

- clear HTML H1/value proposition in the first viewport;
- two primary CTA paths where design/content calls for them (technical consultation/RFQ and product discovery);
- visible trust/proof cue;
- controlled hero height;
- hero/LCP image not lazy-loaded when it is the actual LCP candidate;
- responsive source sizing/dimensions;
- no carousel dependency for the primary value proposition unless measured evidence justifies it.

## 16. Performance / Core Web Vitals

The brief treats mobile field performance as a launch requirement, not a cosmetic score.

Engineering must address:

- TTFB/hosting limitation discovery separately from theme work;
- LCP asset priority;
- conditional CSS/JS by template/component;
- removal of duplicated/unused frontend libraries;
- responsive images and explicit dimensions;
- font payload/control;
- layout stability;
- interaction responsiveness;
- archive/product query performance.

A perfect Lighthouse number is not the business acceptance goal. Real-user CWV and a materially improved field experience are the goal.

## 17. Accessibility

Target WCAG AA-level practical behavior for the implemented UI:

- keyboard-operable navigation, filters, selector and forms;
- visible focus;
- correct landmark/heading semantics;
- accessible names;
- sufficient contrast;
- minimum practical touch target around 44px for primary mobile controls;
- no hover-only critical interaction;
- horizontal-scroll/alternative layout for wide technical tables;
- reduced-motion support;
- sticky mobile CTA must not obscure content or controls.

## 18. SEO/GEO engineering and migration

Developer scope includes:

- stable IA;
- canonical route preservation;
- one canonical/meta/schema owner;
- clean semantic content;
- breadcrumbs/internal linking;
- Product/Article/Organization data compatibility with the active provider;
- sitemap correctness through the authoritative provider;
- redirect implementation/verification;
- crawl-diff before/after launch;
- production noindex/robots checks;
- preserving analytics tags/event hooks across rebuild;
- avoiding duplicate/cannibalized pages.

Operational SEO campaigns, backlink acquisition, ongoing rank reporting, editorial calendars and recurring GSC work remain outside plugin code.

## 19. Analytics and conversion continuity

The plugin must not become an analytics product, but the rebuild must not destroy the existing measurement layer.

Implementation must provide stable data/event hooks for approved events such as:

- RFQ start/submit/success;
- WhatsApp CTA;
- phone/email contact;
- product-resource/download click where required;
- selector/filter use where explicitly approved.

Exact event names/IDs belong to deployment configuration and analytics ownership.

## 20. Launch, rollback and handoff

Required launch engineering includes:

- staging-first deployment;
- backup/rollback plan coordinated with hosting/deployment owner;
- pre-launch and post-launch crawl comparison;
- redirect/canonical/schema/sitemap verification;
- check that production is not accidentally `noindex`;
- analytics/tag continuity verification;
- representative mobile/desktop/a11y/CWV checks;
- defect warranty expectation of 30 days as stated in project planning;
- a 30/60/90 monitoring/handoff plan for the operational SEO/analytics team without making that recurring operation plugin responsibility.

## 21. Seven implementation waves

The normalized roadmap is:

- **Wave 0 — Preservation:** backup coordination, crawl, URL/action map, analytics/search-provider continuity, rollback baseline.
- **Wave 1 — Design system:** responsive components, templates, accessibility baseline, prototype states.
- **Wave 2 — P0 foundation:** shell, Home, anchor product discovery, About, Services, Contact/RFQ foundation.
- **Wave 3 — Product/hub build:** product/category/brand technical templates, selector/filter, first major migration batches.
- **Wave 4 — Applications/specialists/content:** application pages, remaining hubs/products, guides/articles, specialist discovery.
- **Wave 5 — Migration QA:** redirect, canonical, schema, sitemap, crawl-diff, performance/a11y/security regression.
- **Wave 6 — Launch:** deploy, tracking verification, sitemap/indexation checks, rollback readiness, handoff.

Do not skip Wave 0 to start homepage styling.

## 22. Acceptance principle

A page is not complete merely because it renders. It must have:

- correct content owner;
- correct URL/migration decision;
- correct template family;
- appropriate internal links;
- correct RFQ/conversion path where relevant;
- one intended H1 and semantic structure;
- provider-safe metadata/schema behavior;
- responsive/accessibility behavior;
- no invented technical/business data;
- tested performance behavior representative of the page family.
