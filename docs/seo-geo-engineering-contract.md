# SEO/GEO Engineering Contract

## 1. Purpose

Developer-side SEO/GEO friendliness is a **baseline engineering requirement** for Graha Selang Site Core.

This contract distinguishes:

- **in-scope engineering**: structure, crawlability, semantics, performance, stable URLs, integration boundaries and machine-readable readiness;
- **out-of-scope operations**: keyword campaigns, content production calendars, backlinks, ranking monitoring, GSC/GA4/GBP operations, monthly reporting and similar recurring marketing work.

The plugin must not confuse “SEO operations are excluded” with “SEO-friendly engineering is excluded.”

## 2. Core principles

Every indexable public page should be:

- reachable by normal `<a href>` links;
- useful when JavaScript fails;
- server-rendered for primary content;
- semantically structured;
- represented by one stable canonical URL;
- internally connected to its parent/related entities;
- fast and visually stable;
- accessible to humans and ordinary crawlers;
- free of hidden/cloaked keyword text.

## 3. Semantic HTML contract

Use page-appropriate HTML:

- one main `<main>` landmark;
- meaningful `<header>`, `<nav>`, `<footer>`, `<article>`, `<section>` as appropriate;
- one clear H1 for the primary page topic;
- logical H2/H3 hierarchy without using headings only for visual sizing;
- real lists for lists;
- tables for genuinely tabular specifications;
- `<dl>` for compact name/value specifications when more appropriate;
- buttons for actions and anchors for navigation;
- meaningful link text;
- form labels and error relationships.

Do not produce duplicate H1s from template chrome and content accidentally.

## 4. Route/canonical contract

- one public canonical route per content entity;
- no duplicate product-hub routes;
- no alternate UI-version routes;
- trailing-slash behavior follows WordPress canonical behavior consistently;
- internal links point directly to the canonical final URL;
- legacy paths either remain valid or have explicit one-hop redirects;
- avoid parameters as alternate indexable copies of archives;
- pagination must use normal crawlable URLs/links where pagination exists;
- do not generate canonical tags in the plugin when the authoritative SEO provider already does so.

Known migration concern: `/products/` and `/products-2/` currently coexist publicly. They must not survive launch as competing indexable hubs.

## 5. Metadata ownership

Preferred ownership order:

1. configured SEO provider;
2. WordPress core/native content defaults;
3. a documented Graha fallback only where no owner exists.

The plugin must not output a second:

- `<title>` owner;
- meta description;
- canonical link;
- robots meta;
- Open Graph/Twitter metadata set;
- schema graph.

When integration with Rank Math/Yoast/another provider is needed, use supported hooks/data rather than proxying or recreating its admin UI.

## 6. Indexability policy

Indexable by default only when a route contains unique, useful public content and is intended for discovery.

Utility/private/transactional surfaces should follow WordPress/SEO-provider best practices and site configuration. The Graha plugin must not accidentally make internal search results, temporary previews, debug routes or empty generated views into SEO landing pages.

Do not blanket-noindex entire content families without an explicit policy.

## 7. Sitemap contract

Use WordPress core or the configured SEO provider as sitemap authority.

The Graha plugin should not build a parallel sitemap engine. `graha_product`, `graha_product_category`, and `graha_product_brand` must be registered normally so the authoritative sitemap owner can discover them, or be integrated through supported provider filters where required.

## 8. Breadcrumbs

Deep content should support visible, accessible breadcrumbs:

- product: Home → Products/category → Product;
- category: Home → Products → Category hierarchy;
- brand: Home → Brands/Products → Brand;
- article: Home → Articles → optional category → Article;
- service/topic landing: Home → appropriate hub → Page when hierarchy exists.

Use one breadcrumb data source. If the SEO provider supplies authoritative breadcrumb data/output, integrate or defer rather than print two breadcrumb systems.

Breadcrumb links must be real anchors and match canonical routes.

## 9. Internal linking architecture

Required structural pathways:

- Home → major product categories/brands/services/articles;
- Products hub → categories/brands/products;
- Category → products + parent/child category pathways;
- Brand → products and relevant categories;
- Product → category/brand + explicitly related products/resources;
- Article → explicitly relevant products/categories/services when editorially configured;
- Services/topic landings → relevant product categories/contact;
- every important page → clear contact/consultation path where commercially appropriate.

Do not generate large keyword-heavy link clouds. Internal links should be visible and useful.

## 10. Product semantics

Technical product information should be structured rather than buried in repeated prose where data exists.

Render populated `graha_product` taxonomy/meta with clear labels. Typical capability fields include material, reinforcement, size, pressure, temperature, standard and application, but never fabricate missing values.

Native `graha_product` records are the Product entity/data authority. Avoid a second product schema graph; schema output remains with the configured authoritative SEO owner.

## 11. Article/content semantics

Use native Post titles, excerpts, dates, featured images and categories/tags. Avoid hard-coded SEO title/meta text inside article body templates.

Current public evidence shows at least one product page where “Keyword utama / Meta Title / Meta Description” appears in visible content. The new presentation must keep editorial SEO control data out of the rendered body unless it is intentionally written as user-facing content.

## 12. GEO readiness

“GEO” here means making public information clear and extractable by modern search/answer systems through normal web standards—not building crawler-specific hidden content.

Engineering requirements:

- clear entity identity and contact/about relationships;
- explicit product/category/brand relationships;
- concise visible summaries before deep detail where editorial content provides them;
- semantic technical specifications;
- descriptive headings;
- useful visible FAQ sections only when approved content exists;
- stable URLs and canonical entity pages;
- consistent factual data across reused surfaces;
- source/download links where approved technical resources exist;
- no content hidden solely for AI crawlers;
- no fake citations, unverifiable claims or automatically invented specifications.

Do not promise ranking or AI-answer inclusion from markup alone.

## 13. Structured data

Goal: **one authoritative graph**, not maximum markup quantity.

Likely entity families include Organization/LocalBusiness, WebSite, BreadcrumbList, Product and Article, but ownership depends on the configured SEO stack.

Rules:

- product schema remains SEO-provider/native integration owned rather than being duplicated by templates;
- SEO provider Organization/Article/Breadcrumb schema remains provider-owned when active;
- Graha plugin may expose clean fields/relationships to supported hooks;
- do not hard-code duplicate JSON-LD into every template;
- FAQ schema must not be emitted merely because a visual accordion exists; content and provider policy must justify it.

## 14. Images/media

- use WordPress responsive image APIs;
- include width/height or aspect ratio to reduce CLS;
- lazy-load below-the-fold media;
- do not lazy-load the likely LCP hero image blindly;
- alt text comes from meaningful content/editor data;
- decorative images may use empty alt;
- file names are not an authoritative alt-text fallback;
- no mass “SEO alt repair” job that invents product claims.

## 15. Core Web Vitals/performance

Architecture must support strong CWV:

- minimal global CSS/JS;
- conditional component assets;
- no unnecessary frontend framework/hydration;
- responsive optimized images;
- stable reserved media space;
- avoid layout shifts from sticky headers, fonts or async components;
- preload only truly critical assets;
- use native lazy loading where suitable;
- paginate large product/article archives;
- do not render all 96+ products into one page payload;
- keep third-party scripts outside plugin ownership unless required by scope.

Performance targets should be measured on representative staging pages, not guessed from source size alone.

## 16. Redirect/migration SEO

Before launch:

- crawl/export all current public URLs;
- classify KEEP / REDIRECT / RETIRE;
- preserve strong existing URLs whenever content intent remains;
- create one-to-one redirects to the closest equivalent when routes change;
- no multi-hop chains;
- no blanket redirect of all removed URLs to Home;
- verify status codes, canonicals and internal links after migration;
- preserve product/category/article slugs when practical;
- handle `/products-2/` explicitly.

See `docs/legacy-migration-contract.md`.

## 17. Content hygiene

Templates must not leak:

- editor notes;
- keyword target notes;
- “Meta Title:” labels;
- prompt text;
- migration markers;
- placeholder technical claims;
- duplicate hidden headings;
- staging-only copy.

Graceful empty states are better than invented content.

## 18. Optional AI-era files/features

Do not add `llms.txt`, AI crawler controls, vector indexes, answer-engine feeds or custom “GEO schema” as baseline requirements. They lack sufficient stable necessity for this developer scope.

If later requested, treat each as a separate evidence-based feature and document ownership/security/cache implications.

## 19. Definition of done for a page family

A page family is not complete until:

- route is canonical and migration status is known;
- primary content is server-rendered;
- heading/landmark semantics are valid;
- important links are crawlable anchors;
- mobile/keyboard behavior works;
- metadata/schema ownership has no duplication;
- empty states do not become thin indexable junk;
- images do not create avoidable CLS;
- WordPress/native product data ownership remains authoritative;
- no visible SEO-control notes leak into body content.