# Verification Contract

## Purpose

This document defines what future implementation must prove. It prevents “looks correct” from becoming the only acceptance criterion.

## 1. Repository invariants

Verify:

- canonical docs remain internally consistent;
- raw project files are absent;
- no secrets/generated archives/debug dumps are committed;
- implementation decisions that change contracts update docs in the same commit.

## 2. Architecture invariants

Static/test assertions should cover:

- exactly one composition root;
- no `System` mega-class/service locator;
- no more than eight first-party bootable owners without documented approval;
- one first-party asset owner;
- one navigation data tree for desktop/mobile;
- optional Woo/SEO/form dependencies resolved centrally in their owner/adapter;
- no duplicate product/content persistence;
- no custom database tables in v1;
- no generic migration/recovery/telemetry subsystem;
- no public unauthenticated AJAX unless explicitly threat-modeled.

## 3. WordPress/Woo ownership

Verify:

- Pages/Posts remain native editable content;
- products/categories/attributes/brands remain Woo/approved taxonomy data;
- product template changes do not create separate product CRUD;
- cart/checkout/account behavior remains Woo-owned when enabled;
- no custom order/payment/customer logic;
- product/category/brand queries use supported APIs and pagination.

## 4. Route coverage

Representative route tests must cover:

- `/`;
- `/about-us/`;
- `/products/`;
- at least one parent and child `/product-category/.../` when hierarchy exists;
- at least one `/brand/{slug}/` when brand taxonomy exists;
- rich and sparse `/product/{slug}/`;
- `/layanan-kami/`;
- `/articles/`;
- one `/blog/{slug}/` article;
- `/contact-us/`;
- one evergreen landing Page;
- WordPress 404;
- search if retained;
- optional cart/checkout/account if enabled;
- at least one redirected legacy URL after migration is configured.

Do not assume route counts are fixed; data is dynamic.

## 5. Legacy migration assertions

Once real redirect inventory exists, verify:

- every `REDIRECT` legacy URL reaches the intended final URL in one hop where practical;
- no redirect loop;
- no blanket removed-page → Home pattern;
- `/products-2/` classification is resolved;
- internal links do not point at redirected URLs;
- sitemap contains canonical URLs only;
- continued product/category/article slugs still resolve;
- retired pages return appropriate status/content rather than soft 404.

## 6. SEO/GEO structural assertions

Representative pages must satisfy:

- server-rendered primary content;
- exactly one intended H1;
- logical heading hierarchy;
- semantic main/nav/footer/article landmarks as appropriate;
- important navigation/discovery uses anchors with hrefs;
- deep pages have one breadcrumb system;
- no duplicate canonical tag owner;
- no duplicate meta description owner;
- no duplicate schema graph for the same entities;
- current route is consistent with canonical/internal links;
- pagination is crawlable when present;
- no visible `Keyword utama`, `Meta Title`, `Meta Description`, prompts or editor control notes unless intentionally user-facing;
- no hidden AI/GEO keyword blocks;
- product specs render only populated values;
- organization/contact data comes from one approved dataset.

## 7. Accessibility assertions

Test keyboard-only use for:

- desktop navigation/submenus;
- mobile drawer open/close/focus/escape/backdrop;
- search/filter controls if present;
- product gallery if interactive;
- forms/provider messages;
- pagination;
- carousels only if used.

Check:

- visible focus;
- no hover-only critical control;
- reduced-motion behavior;
- meaningful button/link names;
- form labels/errors;
- media alt semantics;
- responsive spec tables or alternative narrow layout.

## 8. Performance assertions

On representative staging pages inspect:

- CSS/JS actually needed for that page family;
- duplicate libraries;
- image dimensions/srcset/sizes;
- hero/LCP loading strategy;
- lazy loading below fold;
- layout shift from header/fonts/images;
- archive pagination and payload size;
- no client-side-only primary content;
- PHP/database query behavior on large product archive.

Do not add a custom cache merely because a synthetic score is imperfect. Profile the actual bottleneck.

## 9. Content/fallback assertions

Verify:

- missing product specs are omitted cleanly;
- missing brand logo does not break archive;
- missing approved contact field does not cause invented placeholder fact;
- empty category/archive state is intentional;
- unavailable form provider shows useful contact fallback;
- missing WooCommerce produces a deliberate admin/public fallback rather than fatal error where plugin activation is allowed without Woo;
- no staging stock image impersonates a real product/brand/location.

## 10. Security assertions

For first-party admin mutations verify:

- capability;
- nonce;
- validation/sanitization;
- native persistence path;
- contextual escaping;
- no secrets in HTML/JS/source;
- no unnecessary public AJAX/REST mutation.

## 11. Regression exclusions

Static scans should guard against accidental introduction of:

- `gloskin_` runtime namespaces/identifiers;
- `morgen-`/`mg6-` runtime identifiers;
- custom product manager/database;
- Technical Library/PDF preview systems;
- generic CASE/PROD migrations;
- telemetry/diagnosis bundles;
- custom mail transport;
- duplicate SEO-provider proxy/admin;
- hidden SEO/GEO content generation.

Documentation may mention these names as provenance/exclusions.

## 12. Definition of implementation-ready

Repository preparation is complete when the canonical docs answer:

- what the product is;
- what it owns/does not own;
- how routes/content are modeled;
- how SEO/GEO engineering is handled;
- how legacy migration is handled;
- what baseline patterns are retained/pruned;
- what implementation sequence to follow;
- what tests must prove;
- what remaining deployment/client inputs may not be invented.

Production implementation is complete only after these contracts pass on staging/target environment.