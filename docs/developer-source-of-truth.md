# Developer Source of Truth

## 1. Authority

This is the canonical developer-facing requirements source for `yudanfahmie/graha-selang`.

Normal developers should not need `yudanfahmie/project-9901`. Authority order:

1. explicit repository-owner instruction;
2. this document + `docs/operational-requirements.md`;
3. `docs/scope-inventory.csv` + `docs/requirement-traceability.csv`;
4. remaining canonical repository docs/matrices;
5. current public site only as migration evidence;
6. Gloskin only as engineering-pattern provenance;
7. raw Graha brief only for historical audit/new normalization.

Do not invent missing business/technical facts.

## 2. Product and scope

Graha Selang Site Core is a **developer-only WordPress plugin acting as website builder/presentation layer** for a controlled rebuild of the existing Graha Selang site.

Developer-side SEO/GEO friendliness, accessibility, performance, technical product discovery, migration-safe routing and RFQ presentation/integration are part of the product.

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

Rebuild presentation, templates, UX, RFQ, responsive/accessibility and performance. Preserve or deliberately migrate domain identity, valuable routes, valid WordPress/Woo records, search equity, provider ownership and measurement continuity.

Do not reset the site merely because frontend code is replaced.

## 5. Engineering baseline

Use the strongest engineering principles established in `yudanfahmie/gloskin-site-core`:

- canonical docs before implementation;
- one composition root;
- one owner per concern;
- native WordPress/Woo ownership;
- one asset owner;
- request-oriented loading;
- no speculative repair/migration framework;
- responsive/accessibility verification;
- raw-source independence.

Do not copy Gloskin medical models, routes, UI assets, CSS/JS or content.

## 6. Ownership boundaries

### Graha plugin owns

- public shell/header/nav/footer presentation;
- page-family selection and presentation contexts;
- reusable site UI;
- technical product presentation/selector/filter UI;
- Woo presentation integration;
- application/brand/service/article/contact/RFQ presentation;
- asset loading;
- developer-side SEO/GEO structure and provider compatibility;
- conversion-event integration hooks;
- minimal site presentation settings.

### WordPress owns

Pages, Posts, Media, users/capabilities, core settings/meta and normal permalink/rewrite infrastructure.

### WooCommerce owns

Products/variations, product CRUD/admin, categories, attributes, product media, authoritative brand taxonomy when provided by the Woo stack, SKU/price/stock, and cart/checkout/order/account/payment behavior when enabled.

### SEO provider owns

Canonical/title/meta/robots/schema/sitemap/breadcrumb output where the configured provider is authoritative. The Graha plugin must not emit a competing graph/output layer.

### Form provider owns by default

Submission transport, spam/captcha, upload storage/retention, email/autoresponse and submission records. Graha owns RFQ context, presentation and compatible routing/configuration integration. If provider capability cannot meet the mandatory RFQ behavior, an architecture decision is required before first-party backend work.

## 7. Product hierarchy

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

## 8. Discovery architecture

Support four entry doors:

- by product;
- by application/industry;
- by brand;
- by specification need.

One indexable URL = one primary intent. Avoid doorway/keyword-variant duplication.

The specification path may use progressive selector/filter logic backed by Woo/native structured attributes. Primary catalog and links remain server-rendered/crawlable without JS.

## 9. Route/content model

Use native owners first:

- Home/About/Services/Contact/RFQ/legal/evergreen landing surfaces: native Pages or provider output as appropriate;
- articles/guides: native Posts;
- products: Woo Products;
- product categories: Woo categories;
- brands: installed approved Woo brand taxonomy;
- technical specs: Woo attributes or registered product meta owned with Woo content;
- application pages: preserve the 4 retained application-intent URLs using native Pages unless deployment evidence proves a better native owner.

Known retained public route families include `/`, `/about-us/`, `/products/`, `/product/…/`, `/product-category/…/`, `/brand/…/`, `/layanan-kami/`, `/articles/`, `/blog/…/`, `/contact-us/` and approved evergreen/application routes.

Do not create CPTs/custom tables merely to mirror the brief.

## 10. Required presentation families

Presentation coverage must exist for Home, archive, category, product, application, brand, About, Service, RFQ, Guide/Article, Legal/Trust, Search and 404. Families may share implementation components/files.

## 11. Technical product contract

When data exists, templates support semantic specs, size/diameter, pressure, temperature, material/construction, standards, media compatibility, application, compatible fittings/connections, approved resources/datasheets and related discovery.

No technical field is fabricated to make a template look complete.

The selector/filter/decision tree is a mandatory experience requirement, but it must use authoritative structured values and crawl-safe query behavior.

## 12. Application/specialist contract

The brief identifies mining, cement/bulk, marine, dredging/slurry, drilling, oil & gas, MRO and CNG themes. The legacy baseline has **4 retained application URLs**. Do not turn all themes into eight programmatic pages automatically; additions require distinct content/intent approval.

## 13. RFQ contract

The technical RFQ must support context-sensitive entry, dynamic technical fields, secure provider-owned file upload where enabled, source URL/entity context, buyer vs reseller/cooperation routing where configured, contextual WhatsApp and approved conversion-event hooks.

Contact page prioritizes buyer technical RFQ; cooperation/reseller intent is secondary/routed.

See `operational-requirements.md` and `content-data-contracts.md`.

## 14. Services/trust/legal

Retain `/layanan-kami/` as the service hub. `/services/` is a consolidation/redirect concern, not a second hub.

Services support crimping/assembly, custom fitting/coupling/flange, product-selection consultation and repair/replacement assessment where approved.

About/trust surfaces support verified workshop/capability/project evidence. Do not invent certification/importer/authorization claims.

Legal footer links may not ship as `#`; they must resolve to approved legal/provider surfaces or be removed intentionally.

## 15. Public-language/semantic contract

Primary public UI is Indonesian. Use correct Indonesian document locale, one intended H1, semantic landmarks and translated UI strings. Technical English terminology may remain where natural.

Never expose internal SEO controls/prompts as public body content.

## 16. SEO/GEO engineering contract

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

## 17. Performance/accessibility contract

Treat mobile field performance as a launch concern. Optimize LCP/hero loading, conditional assets, responsive images, font/library payload and layout stability. Distinguish server/TTFB problems from frontend work.

Practical WCAG AA target: keyboard support, visible focus, proper labels/names, contrast, reduced motion, no hover-only controls, responsive spec tables, ~44px primary mobile targets, sticky CTA that never obscures content.

## 18. Migration contract

Every baseline/current URL receives KEEP, REDIRECT or RETIRE before launch. Validate one-hop redirects, final internal links, canonicals, sitemap and crawl-diff.

Known duplicate concerns include `/products-2/` vs `/products/` and `/services/` vs `/layanan-kami/`.

## 19. Launch contract

Staging-first. Preserve measurement integration, verify production not `noindex`, run pre/post crawl comparison, verify redirects/canonical/schema/sitemap, test representative route families, keep rollback ready, and hand off a 30/60/90 monitoring plan to the operations team.

## 20. Missing-data rule

When a business/provider value is missing or conflicting:

1. do not guess;
2. keep native/provider data editable;
3. omit/fallback gracefully;
4. record production-blocking input in `implementation-inputs.md`;
5. update canonical docs when approved data arrives.