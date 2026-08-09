# Developer Source of Truth

## 1. Authority

This is the canonical developer-facing requirements source for `yudanfahmie/graha-selang`.

Normal developers should not need `yudanfahmie/project-9901`. Authority order:

1. explicit repository-owner instruction;
2. this document + `docs/operational-requirements.md` + `docs/admin-information-architecture.md`;
3. `docs/scope-inventory.csv` + `docs/requirement-traceability.csv`;
4. remaining canonical repository docs/matrices;
5. current public site only as migration evidence;
6. Gloskin only as engineering-pattern provenance;
7. raw Graha brief only for historical audit/new normalization.

Do not invent missing business/technical facts.

## 2. Product and scope

Graha Selang Site Core is a **developer-only WordPress plugin acting as website builder/presentation layer** for a controlled rebuild of the existing Graha Selang site.

Developer-side SEO/GEO friendliness, accessibility, performance, technical product discovery, migration-safe routing, RFQ presentation/integration and a coherent Graha-specific admin wrapper are part of the product.

Recurring SEO campaigns, backlinks, ranking/reporting, content operations, routine GSC/GA4/GTM/GBP management, social/media campaigns and ads are not plugin responsibilities.

## 3. Frozen operational inventory

The brief baseline contains **96 legacy URLs**:

- 68 product/series;
- 18 hubs;
- 4 applications;
- 5 merge + permanent redirect;
- 1 retire.

The correct interpretation is **90 retained content-intent URLs + 6 legacy-action URLs**. The live site can change, so production implementation must reconcile a fresh crawl against this baseline. Every delta needs an explicit explanation; no baseline/current URL is silently dropped.

This number is independent from template-family count. See `scope-inventory.csv`, `template-matrix.csv` and `page-matrix.csv`.

## 4. Controlled-rebuild principle

Rebuild presentation, templates, UX, RFQ, responsive/accessibility and performance. Preserve or deliberately migrate domain identity, valuable routes, valid WordPress/native product records, search equity, provider ownership and measurement continuity.

Do not reset the site merely because frontend code is replaced.

## 5. Engineering baseline

Use the strongest engineering principles established in `yudanfahmie/gloskin-site-core`:

- canonical docs before implementation;
- one composition root;
- one owner per concern;
- native WordPress ownership;
- one asset owner;
- request-oriented loading;
- no speculative repair/migration framework;
- responsive/accessibility verification;
- raw-source independence.

Do not copy Gloskin medical models, routes, UI assets, CSS/JS or content.

## 6. Ownership boundaries

### Graha plugin owns

- registration of the native `graha_product` content type plus product-category and brand taxonomies;
- public shell/header/nav/footer presentation;
- page-family selection and presentation contexts;
- reusable site UI;
- technical product presentation/selector/filter UI;
- application/brand/service/article/contact/RFQ presentation;
- asset loading;
- developer-side SEO/GEO structure and provider compatibility;
- conversion-event integration hooks;
- minimal site presentation/integration settings;
- registration/presentation of the single Graha-specific admin wrapper and its plugin-owned child screens.

### WordPress owns

Pages, Posts, Media, `graha_product` records, native product category/brand terms, users/capabilities, core settings/meta and normal permalink/rewrite infrastructure. Standard WordPress CRUD/admin screens remain authoritative; the Graha plugin does not build a second product CRUD system.

### Native Graha product model

`ProductContentService` registers `graha_product`, `graha_product_category`, and `graha_product_brand`. Product persistence uses standard WordPress posts, terms, post meta, term meta, and Media Library relationships. WooCommerce is not required to activate, manage, migrate, or render Graha products.

### SEO provider owns

Canonical/title/meta/robots/schema/sitemap/breadcrumb output where the configured provider is authoritative. The Graha plugin must not emit a competing graph/output layer.

### Form provider owns by default

Submission transport, spam/captcha, upload storage/retention, email/autoresponse and submission records. Graha owns RFQ context, presentation and compatible routing/configuration integration. If provider capability cannot meet the mandatory RFQ behavior, an architecture decision is required before first-party backend work.

## 7. Admin-side information architecture

All **plugin-owned custom admin pages** must live beneath one top-level WordPress admin parent:

- visible label: **Graha Selang Content**;
- slug: `graha-selang-content`;
- target visible location: the second admin sidebar item, immediately after Dashboard;
- default WordPress position argument: `3`;
- canonical owner: `AdminService`.

Every Graha-specific custom admin menu page uses this parent. Do not create sibling root menus such as Graha Settings, Graha RFQ, Graha SEO, Graha Products or other plugin-owned roots.

This grouping rule does **not** replace native WordPress CRUD. WordPress Pages/Posts/Media and the native `graha_product`/product-category/brand screens remain standard WordPress screens; the Graha wrapper may link to them but must not proxy or clone their CRUD. SEO-provider and form-provider screens likewise remain with their authoritative owners.

Menu placement is not authorization. Each child page enforces its own least-privilege capability; state-changing actions also require nonce verification, validation/sanitization and native persistence. Graha admin assets load only on Graha-owned screens.

Use normal WordPress menu APIs. If another plugin causes a menu-position collision on the target environment, only a narrow ordering adjustment for `graha-selang-content` is permitted so it remains immediately after `index.php`; unrelated admin-menu ordering must remain intact.

See `docs/admin-information-architecture.md`.

## 8. Product hierarchy

Homepage/discovery keeps six visible groups with unequal hierarchy:

**Anchors**

1. Hydraulic Hose / Selang Hidrolik — MORGEN-led
2. Industrial Hose & Assembly — HAMMER value + SUNFLEX premium

**Core supporting**

3. Ducting Hose
4. PVC Spiral/Spring/Suction Hose
5. Fittings/Couplings/Accessories

**Specialist**

6. CNG/high-pressure gas hose assembly

Large-bore is a capability/application within Industrial Hose & Assembly, not an automatic duplicate top-level catalog.

Brand/technical claims must be evidence-backed.

## 9. Discovery architecture

Support four entry doors:

- by product;
- by application/industry;
- by brand;
- by specification need.

One indexable URL = one primary intent. Avoid doorway/keyword-variant duplication.

The specification path may use progressive selector/filter logic backed by native structured taxonomy/meta. Primary catalog and links remain server-rendered/crawlable without JS.

## 10. Route/content model

Use native owners first:

- Home/About/Services/Contact/RFQ/legal/evergreen landing surfaces: native Pages or provider output as appropriate;
- articles/guides: native Posts;
- products: `graha_product`;
- product archive: `/products/`;
- product singles: `/product/{slug}/`;
- product categories: `graha_product_category` at `/product-category/{slug}/`;
- brands: `graha_product_brand` at `/brand/{slug}/`;
- technical specs: approved registered product meta/native terms;
- application pages: preserve the 4 retained application-intent URLs using native Pages unless deployment evidence proves a better native owner.

Known retained public route families include `/`, `/about-us/`, `/products/`, `/product/…/`, `/product-category/…/`, `/brand/…/`, `/layanan-kami/`, `/articles/`, `/blog/…/`, `/contact-us/` and approved evergreen/application routes.

Do not create custom product tables, shadow catalogs, or virtual route engines merely to mirror the brief.

## 11. Required presentation families

Presentation coverage must exist for Home, archive, category, product, application, brand, About, Service, RFQ, Guide/Article, Legal/Trust, Search and 404. Families may share implementation components/files.

## 12. Technical product contract

When data exists, templates support semantic specs, size/diameter, pressure, temperature, material/construction, standards, media compatibility, application, compatible fittings/connections, approved resources/datasheets and related discovery.

No technical field is fabricated to make a template look complete.

The selector/filter/decision tree is a mandatory experience requirement, but it must use authoritative structured values and crawl-safe query behavior.

## 13. Application/specialist contract

The brief identifies mining, cement/bulk, marine, dredging/slurry, drilling, oil & gas, MRO and CNG themes. The legacy baseline has **4 retained application URLs**. Do not turn all themes into eight programmatic pages automatically; additions require distinct content/intent approval.

## 14. RFQ contract

The technical RFQ must support context-sensitive entry, dynamic technical fields, secure provider-owned file upload where enabled, source URL/entity context, buyer vs reseller/cooperation routing where configured, contextual WhatsApp and approved conversion-event hooks.

Contact page prioritizes buyer technical RFQ; cooperation/reseller intent is secondary/routed.

See `operational-requirements.md` and `content-data-contracts.md`.

## 15. Services/trust/legal

Retain `/layanan-kami/` as the service hub. `/services/` is a consolidation/redirect concern, not a second hub.

Services support crimping/assembly, custom fitting/coupling/flange, product-selection consultation and repair/replacement assessment where approved.

About/trust surfaces support verified workshop/capability/project evidence. Do not invent certification/importer/authorization claims.

Legal footer links may not ship as `#`; they must resolve to approved legal/provider surfaces or be removed intentionally.

## 16. Public-language/semantic contract

Primary public UI is Indonesian. Use correct Indonesian document locale, one intended H1, semantic landmarks and translated UI strings. Technical English terminology may remain where natural.

Never expose internal SEO controls/prompts as public body content.

## 17. SEO/GEO engineering contract

Required by construction:

- stable canonical routes;
- server-rendered primary content/links;
- one H1 and logical headings;
- breadcrumbs on deep surfaces;
- product/category/brand/application relationships through real anchors;
- one metadata/schema owner;
- semantic technical specs;
- sitemap correctness through authoritative provider;
- crawl-safe filters;
- no duplicate/cannibalized URLs;
- no hidden keyword/GEO blocks;
- Core Web Vitals-minded asset/media behavior.

## 18. Performance/accessibility contract

Treat mobile field performance as a launch concern. Optimize LCP/hero loading, conditional assets, responsive images, font/library payload and layout stability. Distinguish server/TTFB problems from frontend work.

Practical WCAG AA target: keyboard support, visible focus, proper labels/names, contrast, reduced motion, no hover-only controls, responsive spec tables, ~44px primary mobile targets, sticky CTA that never obscures content.

## 19. Migration contract

Every baseline/current URL receives KEEP, REDIRECT or RETIRE before launch. Validate one-hop redirects, final internal links, canonicals, sitemap and crawl-diff.

Known duplicate concerns include `/products-2/` vs `/products/` and `/services/` vs `/layanan-kami/`.

## 20. Launch contract

Staging-first. Preserve measurement integration, verify production not `noindex`, run pre/post crawl comparison, verify redirects/canonical/schema/sitemap, test representative route families, keep rollback ready, and hand off a 30/60/90 monitoring plan to the operations team.

## 21. Missing-data rule

When a business/provider value is missing or conflicting:

1. do not guess;
2. keep native/provider data editable;
3. omit/fallback gracefully;
4. record production-blocking input in `implementation-inputs.md`;
5. update canonical docs when approved data arrives.