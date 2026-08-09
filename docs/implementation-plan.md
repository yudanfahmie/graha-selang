# Implementation Plan

## 1. Goal

Implement a production-ready Graha Selang WordPress website presentation as a standalone plugin, using this repository as the sole normal developer requirements source.

This document intentionally stops at implementation planning. Repository preparation does **not** include production plugin code.

## 2. Baseline constraints

- developer-only scope;
- pure WordPress plugin/site-builder presentation layer;
- native WordPress/WooCommerce ownership;
- one composition root;
- maximum eight bootable first-party owners in v1;
- no custom database tables;
- no duplicate product/catalog/admin stack;
- developer-side SEO/GEO structure is required;
- operational SEO/marketing is excluded;
- legacy indexed URLs must be preserved or explicitly redirected;
- no routine dependency on `project-9901`.

## 3. Phase 0 — canonical handoff

Status: repository preparation.

Deliverables:

- contribution rules;
- developer source of truth;
- architecture contract;
- service map;
- content/data contracts;
- SEO/GEO engineering contract;
- legacy migration contract;
- page matrix;
- prune matrix;
- source/provenance notes;
- verification contract;
- implementation input list.

Exit condition: a developer can understand scope/ownership/architecture without raw project files.

## 4. Phase 1 — deployment discovery and migration inventory

Before writing route-sensitive production code, inspect the actual target WordPress environment.

Capture:

- WordPress/PHP versions;
- active theme (plugin must not depend on bespoke theme behavior without documenting it);
- WooCommerce version/configuration;
- product/category counts and hierarchy;
- actual brand taxonomy/provider and permalink;
- SEO provider/version;
- form provider;
- current permalink settings;
- current sitemap/robots/canonical ownership;
- full current URL crawl/export;
- existing redirect owner/rules;
- approved logo/fonts/brand tokens/media;
- approved canonical NAP/contact data;
- whether cart/checkout is enabled or inquiry/catalog behavior is intended.

Create `docs/redirect-matrix.csv` only from real crawl data.

Exit condition: no route/provider ownership is guessed.

## 5. Phase 2 — plugin foundation

Create a small plugin package, recommended path:

`plugin/graha-selang-site-core/`

Foundation:

- tiny plugin entrypoint;
- namespace/prefix owned by Graha Selang;
- `Kernel` composition root;
- explicit service registration;
- request-profile loading;
- activation/deactivation callbacks only for necessary rewrite flush/version handling;
- no generic migrations.

Add baseline architecture tests/static checks early.

Exit condition: plugin activates/deactivates cleanly with Woo/SEO/form dependencies optional.

## 6. Phase 3 — shell/navigation/assets

Implement:

- semantic document shell;
- global header;
- one shared navigation tree;
- accessible desktop disclosures and mobile drawer;
- footer;
- design tokens;
- single AssetService;
- responsive primitives;
- focus/reduced-motion behavior.

Primary navigation must remain functional without JS; JS enhances disclosures/drawer rather than owning crawlable links.

Exit condition: shell works across representative native Page/Post/Woo surfaces.

## 7. Phase 4 — WooCommerce presentation

Implement through Woo-supported templates/hooks/APIs:

- canonical Products hub/archive presentation;
- category archives;
- authoritative brand taxonomy archives;
- product single;
- optional Woo search/filter UI only if required and using native queries;
- optional cart/account chrome if commerce is active;
- structured technical-spec presentation from populated attributes/meta;
- related product/category/brand pathways.

Do not build product CRUD or duplicate catalog storage.

Exit condition: Woo admin/data remains authoritative and native commerce behavior is not broken.

## 8. Phase 5 — WordPress content families

Implement presentation for:

- Home;
- About;
- Services;
- Contact;
- Articles hub/single;
- approved evergreen/topic landing pages;
- external form integration/fallback.

Use native Pages/Posts. Introduce no CPT unless a demonstrated requirement passes the architecture-change threshold.

Exit condition: all canonical page families render with deliberate empty states and no invented factual content.

## 9. Phase 6 — SEO/GEO engineering integration

Implement/verify:

- stable canonical route behavior;
- semantic landmarks/headings;
- visible breadcrumbs on deep content;
- internal hub/detail links;
- provider-safe metadata/schema ownership;
- sitemap discoverability through authoritative provider;
- semantic product specs;
- clean article markup;
- no visible editorial SEO-control notes;
- entity/contact consistency from one approved data owner;
- Core Web Vitals-minded assets/media.

Do not build campaign/keyword/backlink/reporting tooling.

Exit condition: `docs/seo-geo-engineering-contract.md` checks pass on representative pages.

## 10. Phase 7 — legacy migration/redirect integration

Using the real crawl:

- classify every meaningful current URL KEEP/REDIRECT/RETIRE;
- resolve `/products-2/` duplication;
- preserve product/category/brand/article slugs where appropriate;
- implement redirects in the selected single redirect owner;
- update internal links to final URLs;
- verify canonicals/sitemaps after migration.

Exit condition: no important legacy route is silently dropped and no redirect chain/loop exists.

## 11. Phase 8 — staging readiness

Test representative pages:

- Home;
- About;
- Products hub;
- large category archive + pagination;
- brand archive;
- product with rich specs;
- product with sparse specs;
- Services;
- Articles hub;
- article single;
- Contact/form unavailable and available states;
- evergreen landing;
- optional cart/account surfaces if active;
- 404 and search behavior;
- redirected legacy URL.

Validate mobile/tablet/desktop, keyboard behavior, PHP notices, console errors, Woo compatibility, SEO/schema duplication and major CWV regressions.

## 12. Phase 9 — launch readiness

Before production:

- approved factual contact/NAP values present;
- approved brand/media assets present;
- no staging copy/media leaks;
- redirect matrix executed/verified;
- sitemap/canonical ownership verified;
- analytics/SEO operational teams may attach their tools outside this plugin without plugin conflict;
- no raw project files/secrets in repository/package;
- plugin package reproducible from source.

## 13. Implementation order principle

Do **not** start by polishing the homepage in isolation.

Correct order:

`ownership/provider discovery → plugin foundation → shell → Woo/content families → SEO/GEO structure → migration → staging polish`

This prevents visual work from locking in wrong routes/data ownership.

## 14. Change management

Any implementation discovery that materially changes the model must update canonical docs in the same commit. Examples:

- actual brand taxonomy differs;
- SEO provider owns breadcrumbs differently;
- existing services require structured records/CPT;
- commerce is definitively catalog-only or transactional;
- permalink migration changes route contract;
- a new global setting/service is needed.

Do not keep such decisions only in implementation code or chat.