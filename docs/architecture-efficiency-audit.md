# Architecture Efficiency Audit

## Decision

Build Graha Selang as a **small modular WordPress plugin**, not by cloning Gloskin or Morgen and deleting unwanted modules.

The architecture must preserve the quality lessons of Gloskin while starting clean for Graha Selang’s domain.

## 1. Target shape

Use:

1. one tiny plugin entrypoint;
2. one composition root (`Kernel`);
3. a small set of explicit internal owners;
4. native WordPress/WooCommerce storage and routing;
5. request-profile loading;
6. provider adapters for optional integrations;
7. complexity only after a demonstrated requirement.

Do not build network microservices. “Service” in this repository means an internal class/module with one clear responsibility.

## 2. V1 service budget

Maximum: eight first-party bootable owners.

Recommended set:

- `Kernel` — composition only;
- `TemplateService` — template routing/presentation contexts;
- `AssetService` — single frontend asset registry/loading owner;
- `NavigationService` — one normalized navigation tree;
- `WooCommerceAdapter` — all Woo availability/query/presentation reads;
- `SeoService` — technical SEO/GEO integration boundary and structural helpers;
- `FormAdapter` — external form rendering/fallback;
- `AdminService` — only minimal presentation/global settings/editor enhancements.

Activation/deactivation/rewrite flushing should remain narrow bootstrap/lifecycle callbacks unless future complexity justifies a dedicated owner. Do not create a ninth service merely to match Gloskin’s class list.

## 3. Why this is smaller than Gloskin

Gloskin needed first-party treatment, clinic and doctor content types and relationships. Graha Selang’s core product domain is already naturally owned by WooCommerce, while company/services/articles can use native WordPress.

Therefore Graha Selang should **not** introduce a first-party product content service just to imitate the baseline repository.

The simplest correct architecture is better than structural symmetry.

## 4. Composition root contract

`Kernel` may:

- hold plugin version/path/url constants or receive them;
- register explicit services;
- call each owner’s registration method for the relevant request profile;
- centralize optional dependency construction.

`Kernel` must not:

- query products;
- build page data;
- save settings;
- generate SEO copy;
- render templates;
- mutate Woo state;
- act as a service locator exposed to templates;
- become a `System` god object.

## 5. Template/data-context contract

Templates receive **small page-specific contexts**. Do not build one global payload containing all products, brands, categories, articles, settings and navigation for every request.

Examples:

- Home context: featured categories/brands/products/services/articles actually needed;
- Product category context: current term + paginated products + configured supporting content;
- Product context: current Woo product + presentation-safe related data;
- Article context: current post + explicit related links;
- Contact context: approved contact settings + form integration availability.

WordPress/Woo global objects may be used through documented supported APIs when they are the native owner; avoid hidden cross-template queries.

## 6. Asset ownership

Exactly one first-party `AssetService` owns registration/enqueue of Graha Selang CSS/JS.

Rules:

- conditional load by request/component;
- no duplicate asset registry;
- no queue snapshot/suspend/restore machinery;
- no post-hoc asset “repair” service;
- no globally enqueued carousel/gallery/filter libraries when not instantiated;
- reuse WordPress/Woo frontend dependencies where suitable;
- deterministic versioning.

## 7. Navigation ownership

One `NavigationService` normalizes a WordPress menu or explicit fallback into a shared tree consumed by desktop and mobile renderers.

Do not:

- persist separate mobile/desktop menus;
- create a UI-version navigation registry;
- create a custom route/menu database;
- silently synthesize SEO navigation unrelated to visible UX.

## 8. WooCommerce boundary

All Woo-specific dependency checks and presentation reads live behind `WooCommerceAdapter` or supported Woo template/hook usage.

The adapter may expose normalized reads such as:

- product/category/brand queries;
- product attributes/specifications;
- archive/single/cart/account URLs;
- cart status if commerce is enabled;
- supported related-product data;
- taxonomy mapping/availability.

It must not own:

- product CRUD;
- order/payment/customer state;
- a parallel search index;
- duplicate brand/category persistence;
- independent cart behavior.

## 9. SEO/GEO boundary

`SeoService` is an **engineering integration boundary**, not an SEO campaign engine.

It may own:

- structural breadcrumb helpers/data;
- consistent semantic context for templates;
- provider-detection/integration guards;
- fallback title/canonical behavior only where WordPress/provider does not already own it and only when documented;
- noindex safeguards for plugin-created utility surfaces if any exist;
- schema-ready normalized entity data passed to an authoritative provider when integration requires it.

It must not own:

- keyword tracking;
- backlink operations;
- content generation;
- ranking reports;
- GSC/GA4/GBP management;
- a duplicate sitemap engine;
- duplicate canonical/meta/schema output.

## 10. Persistence contract

V1 target:

- zero custom database tables;
- native Pages/Posts/Media;
- Woo products/categories/attributes/brands;
- registered post/term meta only when necessary;
- at most one small Graha global settings option if a native Page/menu/settings location cannot express the value cleanly.

Do not add generic transaction, lock, rollback, readback or cache-invalidation layers around normal WordPress writes.

## 11. Routing contract

Prefer:

- WordPress Page permalinks;
- WordPress Post permalinks;
- Woo product/category/brand rewrites;
- normal WordPress rewrite behavior.

Avoid:

- virtual route engines;
- request claiming/proxy query flags;
- parallel routing table in options;
- hard-coded redirect logic scattered across templates.

Legacy redirects should live in the deployment’s canonical redirect owner (approved redirect plugin/server/SEO provider) or one narrow documented implementation—not in multiple layers.

## 12. Caching

Do not prebuild a custom cache layer.

Use WordPress/Woo caching semantics first. Add a cache only after profiling identifies a real expensive query, and make the same owner responsible for invalidation.

## 13. Admin UI

Prefer native WordPress/Woo edit screens, taxonomy screens, menus and Settings API.

Do not build a general Graha admin framework. A small settings page is acceptable only for genuinely global presentation/integration settings that have no better native owner.

## 14. Security

For first-party mutations:

- capability checks;
- nonce checks;
- validate/sanitize on input;
- native persistence API;
- escape on output;
- no public unauthenticated AJAX without an explicit threat model.

No security hardening layer should compensate for unclear ownership.

## 15. Performance architecture

Performance starts with architecture:

- small request contexts;
- conditional assets;
- native server rendering;
- responsive WordPress images;
- no duplicate frontend frameworks;
- no unnecessary client hydration;
- pagination rather than unbounded product/article loading;
- no heavyweight page-builder dependency introduced by default.

## 16. Architecture change threshold

A new service, storage layer, custom route, cache, AJAX endpoint or dependency requires documentation of:

1. concrete requirement;
2. why native WordPress/Woo cannot satisfy it;
3. canonical ownership;
4. request/load impact;
5. persistence/security implications;
6. SEO/migration implications where routes/content change;
7. verification coverage.

If those cannot be stated, do not add the complexity.