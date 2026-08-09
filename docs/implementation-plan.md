# Implementation Plan

## 1. Goal

Build the production Graha Selang presentation as a standalone WordPress plugin using this repository as the sole normal developer requirements source.

Repository preparation does **not** include production plugin code.

## 2. Non-negotiable baseline

- developer-only scope;
- WordPress plugin/site-builder presentation layer with a native Graha product content model;
- 96-URL migration baseline: 68 product/series + 18 hub + 4 application + 5 redirect + 1 retire;
- one Kernel / <=8 bootable first-party owners;
- native WordPress ownership;
- `graha_product` + native product-category/brand taxonomies own Graha product content;
- one Graha admin wrapper: **Graha Selang Content** immediately after Dashboard;
- all plugin-owned admin pages are submenus of that wrapper;
- no custom DB or duplicate catalog;
- developer-side SEO/GEO required;
- technical selector/filter and technical RFQ required;
- operational SEO/marketing excluded;
- no raw-project dependency for normal development.

## 3. Repository preparation — complete contract layer

Canonical preparation contains contribution rules, source of truth, operational requirements, admin information architecture, scope inventory, traceability, architecture/service/data/template/page/migration/SEO contracts, implementation plan, verification and blocking inputs.

Exit condition: developer can explain URL counts, product hierarchy, admin hierarchy, special RFQ/discovery requirements, owners, migration logic and acceptance tests without raw brief.

## 4. Wave 0 — Preservation and deployment discovery

Before route-sensitive code:

- capture target WP/PHP versions and active theme;
- verify native product CPT/taxonomy route behavior against current public inventory;
- identify SEO provider and output ownership;
- identify form provider and RFQ/upload/routing capability;
- capture permalink settings, sitemap/robots/canonicals and redirect owner;
- crawl/export the current public site;
- reconcile current crawl against 96 baseline;
- create final redirect/url matrix with zero unexplained rows;
- identify current analytics/tag implementation for continuity;
- capture approved NAP/contact/legal/brand/product inputs;
- define backup and rollback process with deployment owner;
- inspect the target WordPress admin menu for position collisions that could affect the required Graha wrapper placement.

Exit condition: route/provider owners are known, migration matrix has no unidentified baseline row, and admin-menu collision behavior is understood.

## 5. Wave 1 — Design system, plugin foundation and admin shell

Implement:

- plugin entrypoint + Kernel + explicit services;
- native `ProductContentService` registration for `graha_product`, category and brand taxonomies;
- `AdminService` foundation;
- top-level admin parent **Graha Selang Content** with slug `graha-selang-content`;
- parent rendered immediately after Dashboard, using menu position `3` by default and only a narrow order correction if the target environment causes a collision;
- all plugin-owned custom admin screens registered as children of that parent;
- native product/category/brand WordPress screens linked beneath the parent without custom CRUD;
- no separate Graha root menus;
- screen-scoped admin assets only;
- design tokens/responsive primitives;
- semantic shell/header/nav/footer;
- one navigation tree desktop/mobile;
- accessibility baseline;
- single conditional AssetService;
- representative template prototypes for all families in `template-matrix.csv`;
- Indonesian public UI/i18n baseline.

Do not move/clone standard WordPress CRUD, SEO-provider or form-provider screens into custom Graha implementations. Link to authoritative screens if useful.

Exit: plugin activates cleanly; the admin wrapper appears once immediately after Dashboard; no other Graha root menu exists; representative shell works on Page/Post/`graha_product` routes with no fatal optional dependency.

## 6. Wave 2 — P0 foundation

Implement highest-leverage public experience:

- Home with two anchor groups, three supporting groups and CNG specialist;
- four discovery entry doors;
- About;
- `/products/` canonical discovery foundation;
- `/layanan-kami/` Services;
- Contact + technical RFQ foundation;
- buyer-first conversion hierarchy;
- contextual WhatsApp hooks;
- initial event integration hooks;
- homepage/LCP treatment;
- only the minimal Graha-owned admin child screens genuinely required by the implemented configuration, under `Graha Selang Content`.

Do not create empty admin submenu placeholders for future features.

Exit: primary site journey works mobile/desktop, primary content is server-rendered/crawlable, and configuration needed by P0 is accessible through the single admin wrapper without duplicating native/provider CRUD.

## 7. Wave 3 — Product/hub technical build

Implement:

- native `graha_product` archive/category/product presentation;
- native `graha_product_brand` presentation;
- semantic technical specs;
- compatible fittings/resources relationships;
- crawl-safe selector/filter/decision tree using real native taxonomy/meta;
- rich/sparse product states;
- first migration batches across product/series + hubs;
- RFQ source context from products/categories.

Exit: product discovery supports product/brand/specification journeys without a shadow catalog.

## 8. Wave 4 — Applications, specialists and content completion

Implement/migrate:

- exactly the 4 retained application URLs from reconciled inventory;
- approved specialist pathways/themes without doorway-page generation;
- remaining hubs/products;
- Articles/Guide templates;
- approved evergreen/topic surfaces;
- legal/trust destinations;
- application-specific RFQ behavior;
- approved workshop/project/trust proof presentation.

Exit: 90 retained brief-baseline intents are mapped to final canonical owners/routes or owner-approved changed classification.

## 9. Wave 5 — Migration QA

Verify:

- all baseline/current URLs accounted for;
- 5 brief redirect decisions + 1 retire decision resolved;
- redirect one-hop/no-loop;
- internal links target final routes;
- canonical/meta/schema ownership;
- sitemap contents;
- no visible SEO-control notes;
- one H1/semantic structure;
- Indonesian UI strings;
- RFQ upload/routing/privacy/security;
- selector crawl behavior;
- WCAG/keyboard/mobile;
- asset/LCP/CWV regressions;
- admin wrapper hierarchy, capability checks and screen-scoped assets;
- production-package/release checks;
- crawl-diff against Wave 0.

Exit: zero migration REVIEW rows and verification contract passes on staging.

## 10. Wave 6 — Launch and handoff

- deploy staging-approved package;
- verify production not accidentally noindex;
- verify final redirects/canonical/schema/sitemap;
- verify analytics/tag/event continuity;
- post-launch crawl comparison;
- verify representative URLs/404/search/RFQ on production;
- verify final WordPress admin sidebar placement and all Graha child-page authorization;
- keep rollback ready;
- package developer documentation;
- begin 30-day defect warranty;
- hand a 30/60/90 monitoring plan to SEO/analytics operations (not plugin-owned recurring work).

## 11. Implementation order rule

Do **not** start with isolated homepage polish.

Correct sequence:

`Wave 0 preservation → Wave 1 system/admin shell → Wave 2 P0 → Wave 3 product/hub → Wave 4 application/content → Wave 5 migration QA → Wave 6 launch`

If target-environment data required for Wave 0 is unavailable, do not fabricate it. Record the blocker in `implementation-inputs.md` and proceed only with environment-independent foundation work that does not freeze unknown routes/providers.

## 12. Change management

Any material discovery changing counts, route ownership, provider capability, RFQ transport, brand/category membership, SEO ownership, services model, admin menu architecture or architecture budget must update canonical docs/matrices in the same coherent commit.

Do not hide requirement decisions only in code, chat or deployment memory.

## 13. Approved next-bundle execution order

`docs/approved-next-bundle-contract.md` is canonical for the newly approved page-quality/design/breadcrumb and one-shot migration requirements.

While Wave 0 remains blocked, use this safe execution order without falsely advancing the overall wave state:

1. canonicalize the approved next-bundle requirements;
2. finish the environment-independent portion of Wave 1 with `TemplateService`, semantic shell, centralized tokens, reusable breadcrumbs, presentation-family prototypes, accessibility and conditional asset use;
3. build/activate the production Homepage only when real approved content and real crawlable native destinations are available;
4. keep the one-shot migration mechanism lightweight and run heavy validation/import only through explicit authenticated AJAX per `migration-admin-ajax-contract.md`; never create fake bundle content to make the mechanism appear complete;
5. complete production inner-page families after real route/data/provider information is available.

The one-shot content-import mechanism is distinct from the 96-URL redirect/reconciliation workstream. It must not replace or bypass Wave 0 URL reconciliation.

Wave 1 may not be marked complete until activation, admin placement, representative Page/Post/product behavior, and the other Wave 1 exit conditions are verified in a real WordPress runtime.
