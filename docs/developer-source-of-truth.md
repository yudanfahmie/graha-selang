# Developer Source of Truth

## 1. Authority

This document is the canonical developer-facing requirements source for `yudanfahmie/graha-selang`.

A developer implementing Graha Selang should not need to inspect `yudanfahmie/project-9901` for normal work. Raw project material is provenance only and remains read-only.

Order of authority when requirements conflict:

1. explicit instructions from the repository owner;
2. this document and current canonical repository documentation;
3. `docs/page-matrix.csv`, `docs/runtime-service-map.csv` and `docs/prune-matrix.csv`;
4. current public Graha Selang site only as migration/evidence context;
5. pinned Gloskin baseline only for reusable engineering patterns;
6. raw Graha Selang source only for historical audit/new normalization, never a routine implementation dependency.

Do not invent missing business data. Values marked pending remain configurable or gracefully absent until approved input arrives.

## 2. Owner-confirmed scope

The repository scope is **developer-only**.

The product is a **pure WordPress plugin acting as website builder/presentation layer**. The plugin should be capable of delivering the complete visitor-facing website presentation while leaving platform-owned data/business logic with WordPress and WooCommerce.

Developer-side SEO/GEO friendliness is included. This means the plugin must build a semantic, crawlable, stable, fast and internally connected website structure. It does not mean the plugin owns recurring SEO operations.

Operational SEO/marketing remains outside the plugin: content campaigns, backlink work, ranking monitoring, GSC/GA4/GBP operations, monthly reporting, media placement and similar recurring work.

## 3. Engineering baseline

The current quality baseline is:

- repository: `yudanfahmie/gloskin-site-core`
- observed baseline commit at preparation time: `e36039034533d3debb51ae6092e74a311c87d55a`

Reuse its engineering principles:

- canonical developer docs before code;
- one composition root;
- one owner per concern;
- native WordPress/WooCommerce ownership;
- small request-oriented services;
- single asset owner;
- no speculative recovery/migration framework;
- strong responsive/accessibility behavior;
- staging and verification discipline;
- no raw-source dependency after normalization.

Do **not** copy Gloskin-specific medical domains, routes, content models, branding, CSS, JS, media or page copy.

## 4. Business/site context

Current public-site evidence identifies Graha Selang/PT Graha Selang Perkasa as an industrial hose supplier/importer/distributor serving industrial and equipment use cases. The public site currently presents hydraulic hose, industrial hose, CNG/high-pressure gas hose, ducting hose, fittings/accessories, multiple brands, service capabilities and educational articles.

Current public evidence also exposes service themes such as:

- hose crimping and assembly;
- custom fitting/coupling/flange work;
- hose-selection consultation;
- repair/replacement assessment.

Treat those as migration evidence. Exact commercial claims and factual values must remain content-owned, not template-owned.

## 5. Non-negotiable ownership boundaries

### Graha Selang Site Core owns

- global public shell;
- header/navigation/footer presentation;
- responsive layout primitives and design tokens;
- page-family template selection;
- reusable cards, grids, galleries, breadcrumbs, filters and CTA presentation where required;
- native WordPress page/post presentation;
- WooCommerce visual/presentation integration;
- provider-safe form placement/integration;
- developer-side SEO/GEO structure;
- minimal presentation settings required by the above.

### WordPress owns

- Pages;
- Posts/articles;
- Media Library attachments;
- users/capabilities;
- standard options/meta infrastructure;
- standard permalink and rewrite infrastructure.

### WooCommerce owns

- products/variations;
- product CRUD/admin;
- product categories;
- product attributes/specifications where modeled there;
- product images;
- product brand taxonomy when the installed Woo stack supplies it;
- SKU, price and stock when commerce fields are used;
- cart/checkout/order/account/payment behavior if enabled.

Graha Selang Site Core may query and present Woo data. It must not introduce a parallel product catalog, duplicate product admin, duplicate cart/checkout, or independent payment/order logic.

### SEO provider owns

When an installed SEO provider such as Rank Math/Yoast is configured to own metadata/schema/sitemaps, it is authoritative for those outputs. Graha Selang Site Core must provide clean data/HTML and compatible integration surfaces without printing competing canonicals, meta descriptions or duplicate schema graphs.

### External form provider owns

- form submission;
- captcha/anti-spam;
- mail delivery;
- autoresponses;
- submission storage when supported.

The plugin owns placement, layout and compatible success/error presentation only.

## 6. Content model

Prefer the smallest native model that meets the site.

Recommended v1:

- native Page: Home;
- native Page: About (`/about-us/` unless an approved migration changes it);
- native Page: Products presentation hub if needed around Woo (`/products/`);
- native Page: Services hub (`/layanan-kami/`);
- native Page: Contact (`/contact-us/`);
- native Pages: approved high-value evergreen/topic landing pages that are not product records;
- native Posts: educational articles/news/guides;
- WooCommerce Products: product records;
- WooCommerce product categories: product taxonomy;
- Woo/approved product-brand taxonomy: brand taxonomy;
- Woo attributes/meta: structured technical specifications.

Do not add a custom product CPT or custom database table.

Services currently appear small enough for native Page content/sections. Introduce a service CPT only after a concrete editing/relationship requirement demonstrates that Pages are insufficient.

## 7. Normalized route families

Canonical route families to support/preserve during redesign:

- `/` — Homepage;
- `/about-us/` — About/company;
- `/products/` — canonical product discovery/archive surface;
- `/product-category/{slug}/` — Woo product-category archives;
- `/brand/{slug}/` — brand archives if this is the installed/approved brand taxonomy permalink;
- `/product/{slug}/` — Woo product detail;
- `/layanan-kami/` — services hub;
- `/articles/` — article hub/current public archive;
- `/blog/{post-slug}/` — observed article detail permalink family, unless exact legacy inventory proves another rule;
- `/contact-us/` — contact;
- approved evergreen SEO/topic landing pages on stable native Page permalinks;
- Woo-managed cart/checkout/account endpoints only if commerce is enabled.

`docs/page-matrix.csv` is the canonical family matrix.

### Known legacy ambiguity

The current public site exposes both `/products/` and `/products-2/`. The redesign must not keep two competing product hubs. Treat `/products/` as the intended canonical hub unless migration evidence/owner instruction says otherwise; `/products-2/` requires explicit redirect classification before launch.

Do not casually rename indexed routes to prettier alternatives. Preserve or redirect deliberately.

## 8. Information architecture

Expected primary information architecture:

- Products
- Brands or product-brand discovery where approved
- Services
- Articles/Insights
- About
- Contact / Consultation

Home should expose the major product families, key brands, service capability, trust/company context, useful articles and a clear consultation/WhatsApp/contact path.

Navigation must use one normalized tree for desktop and mobile. Do not create separate persistence or different IA for each viewport.

## 9. Page-family requirements

### 9.1 Homepage

Support:

1. semantic global header/navigation;
2. clear H1/value proposition;
3. key product-family discovery;
4. major/featured brand discovery when content exists;
5. service summary;
6. company/trust summary;
7. useful application/industry or educational pathways where approved;
8. article preview;
9. consultation/contact CTA;
10. semantic global footer.

Primary content must be server-rendered and useful without JavaScript.

### 9.2 About

Support approved company overview, history, capabilities, vision/mission if provided, industries served, brand/supply context, locations/contact CTA and trust evidence.

Do not hard-code “since 2016”, addresses or other facts into templates; content belongs to WordPress.

### 9.3 Products hub

The canonical products hub is a presentation layer over WooCommerce. Support product discovery by category/brand and optional approved search/filter behavior using Woo-native queries/APIs.

Do not create a second product registry.

### 9.4 Product category archive

Support:

- category H1/name;
- useful approved category introduction when present;
- breadcrumb context;
- product grid/list;
- pagination;
- optional subcategory navigation;
- optional semantic technical/application guidance owned as term/page content;
- internal links to related brands/articles/services where explicitly configured.

Do not inject generic SEO paragraphs into every category from template code.

### 9.5 Brand archive

If the installed Woo stack has an authoritative product-brand taxonomy, present that taxonomy. Support brand identity/intro, product listing, related category pathways and useful content when data exists.

Do not register a competing `brand` taxonomy if Woo/another approved commerce extension already owns brands.

### 9.6 Product detail

WooCommerce is authoritative. Presentation should support:

- product title;
- gallery/media;
- category and brand context;
- concise product summary;
- structured technical specifications/attributes;
- standards/certification fields only when actually supplied;
- applications/usage information;
- downloadable/external technical resources only when approved data exists;
- inquiry/WhatsApp/contact action or Woo purchasing action according to deployment configuration;
- related products/content.

Never invent working pressure, temperature, material, standard, certification, size range or compatibility.

### 9.7 Services

Support the current service themes and future approved content using native Pages/sections first. Each service block/detail should be linkable, semantic and able to carry its own approved explanatory content and CTA.

### 9.8 Articles

Use native WordPress Posts. Support article hub, category/tag context when useful, pagination, semantic article template, author/date when intentionally exposed, breadcrumbs, related product/category/service links based on explicit data, and accessible empty states.

No automated keyword-content generator belongs in this plugin.

### 9.9 Contact

Support approved location/contact data, separate contact purposes where needed (for example retail/grosir), WhatsApp/tel/email links, map/embed data and an external form integration area.

Public evidence currently contains inconsistent location wording across pages. Do not hard-code NAP until approved values are supplied/confirmed.

### 9.10 Evergreen SEO/topic landings

Existing indexed educational/commercial landing pages may be valuable independent URLs. Preserve them as native Pages when they have distinct user intent and useful content. Do not collapse every keyword landing page into a product/category URL merely to simplify templates.

At the same time, avoid near-duplicate thin pages. Migration review decides KEEP/REDIRECT/RETIRE per URL.

## 10. Developer-side SEO/GEO requirements

The website structure must be SEO/GEO friendly by construction:

- clean, stable, human-readable canonical routes;
- server-rendered primary text and links;
- semantic landmarks and heading hierarchy;
- one clear H1/page topic;
- breadcrumbs on deep content;
- crawlable anchor-based internal links;
- sensible hub → category/brand → product relationships;
- article → relevant entity/product links when editorially configured;
- semantic product specifications using tables or definition lists where appropriate;
- accessible images with WordPress-managed alt text;
- no duplicate public route families for the same content;
- metadata/schema integration that yields to the configured owner;
- no duplicate canonical/schema output;
- performance and layout stability that support Core Web Vitals;
- consistent organization/entity/contact data surfaces;
- useful visible FAQs only when approved content exists;
- no hidden AI/GEO keyword blocks or speculative crawler hacks.

See `docs/seo-geo-engineering-contract.md`.

## 11. Responsive/accessibility requirements

- mobile, tablet and desktop layouts must be first-class;
- navigation/drawers must support keyboard, escape/backdrop close and focus management;
- visible focus states;
- semantic buttons/links;
- no hover-only critical interaction;
- reduced-motion support;
- product grids/tables must remain usable on narrow viewports;
- images must preserve dimensions/aspect ratio to limit layout shift;
- form and Woo states must have accessible labels/messages.

## 12. Performance requirements

- one asset owner;
- conditional loading by page/component need;
- no global loading of unused feature bundles;
- no duplicate frontend library already provided by WordPress/Woo when avoidable;
- responsive image APIs and explicit dimensions;
- lazy-load non-critical media;
- avoid client-side rendering for primary indexable content;
- deterministic asset versioning;
- keep JavaScript small and progressive-enhancement oriented.

## 13. Explicit exclusions

Do not implement in v1 unless later explicitly approved:

- custom product database/admin;
- custom cart/checkout/order/payment system;
- custom mail transport;
- generic migration/recovery/repair framework;
- custom analytics/GSC dashboard;
- backlink/media/social management;
- keyword/rank monitoring;
- AI content generator;
- Rank Math/Yoast proxy admin;
- custom sitemap engine when WordPress/SEO provider already owns it;
- speculative `llms.txt`/AI-crawler subsystem presented as a ranking guarantee;
- custom database tables;
- raw-project-file browser inside WordPress;
- wholesale Gloskin/Morgen code copy.

## 14. Missing data rule

When business data is missing or conflicting:

1. do not guess;
2. keep the content field/provider editable;
3. render a graceful fallback/omit the section;
4. record a genuinely implementation-blocking input in `docs/implementation-inputs.md`;
5. update canonical docs once owner-approved data arrives.