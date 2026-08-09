# Legacy Migration Contract

## Purpose

Graha Selang is a redesign of an existing indexed WordPress/WooCommerce site. Repository preparation must therefore treat URL/content migration as part of developer correctness, not as an afterthought.

This document defines the engineering contract. It does not perform DNS/hosting/SEO operations.

## 1. Principle

**Redesign does not reset public identity.**

Keep a legacy URL when its content intent remains valid and the native content owner can support it. Redirect only when the information architecture materially changes or a duplicate/obsolete surface is intentionally retired.

## 2. Pre-implementation inventory

Before template/routing work is considered production-ready, export/crawl:

- all indexable Pages;
- Posts/articles and their permalink family;
- Woo product URLs;
- product-category URLs;
- brand taxonomy URLs;
- pagination surfaces;
- attachment/media URLs only where externally linked/indexed relevance matters;
- existing redirects;
- obvious duplicate/legacy routes;
- sitemap URLs from the authoritative provider;
- high-value external technical-resource links that content depends on.

Record HTTP status and final destination for each current URL.

## 3. Classification

Every legacy public URL receives one decision:

- `KEEP` — same canonical route remains;
- `REDIRECT` — route changes; one-hop redirect to closest equivalent;
- `RETIRE` — content truly removed with no meaningful equivalent; use appropriate gone/not-found behavior rather than redirecting everything to Home;
- `REVIEW` — conflict/ownership unknown, blocks launch classification but not plugin architecture.

## 4. Known observed routes

At preparation time public evidence includes:

- `/`
- `/about-us/`
- `/products/`
- `/products-2/`
- `/product/{slug}/`
- `/product-category/{slug}/`
- `/brand/{slug}/`
- `/layanan-kami/`
- `/articles/`
- `/blog/{post-slug}/`
- `/contact-us/`
- standalone evergreen/product-topic landing pages.

This is not guaranteed to be the complete crawl. Implementation must obtain the full deployment inventory before launch.

## 5. Initial route intentions

Unless owner/deployment evidence says otherwise:

- `/` → KEEP;
- `/about-us/` → KEEP;
- `/products/` → KEEP as canonical product hub/archive presentation;
- `/products-2/` → REVIEW with expected REDIRECT to `/products/` if content intent is duplicate;
- existing `/product/{slug}/` → KEEP where product remains;
- existing `/product-category/{slug}/` → KEEP where category remains;
- existing `/brand/{slug}/` → KEEP if the same authoritative taxonomy remains;
- `/layanan-kami/` → KEEP;
- `/articles/` → KEEP;
- `/blog/{post-slug}/` → KEEP when that is the actual current post permalink;
- `/contact-us/` → KEEP;
- useful evergreen landing URLs → KEEP unless content consolidation has an explicit redirect plan.

## 6. Redirect owner

Use exactly one operational redirect owner for production, selected during deployment:

- server/hosting configuration;
- an approved redirect/SEO plugin;
- or one narrowly scoped first-party redirect layer if neither platform option is available.

Do not scatter redirects across `.htaccess`, plugin hooks, JavaScript, meta refresh and multiple plugins.

The Graha presentation plugin should not become a generic redirect manager.

## 7. Redirect quality rules

- one hop only where practical;
- 301/308 permanent semantics for confirmed permanent migrations according to deployment stack;
- destination must match user intent;
- never redirect every missing URL to Home;
- avoid regex/wildcards until the URL set proves the rule is safe;
- preserve useful path specificity;
- update internal links to final URLs so users/crawlers do not traverse redirects;
- verify query parameters important to commerce/tracking are not accidentally broken by redirect rules.

## 8. Duplicate surfaces

Known concern: `/products/` vs `/products-2/`.

Do not render two indexable product discovery pages with substantially the same purpose. Once migration inventory confirms duplication:

1. choose canonical owner/route;
2. migrate unique useful content if any;
3. redirect duplicate URL;
4. remove duplicate navigation/internal links;
5. confirm sitemap only lists the canonical route;
6. confirm no second canonical/meta/schema owner remains.

## 9. Content-owner migration

When moving content into native owners:

- product records stay Woo products;
- categories stay Woo product categories;
- brands stay the approved Woo brand taxonomy;
- articles stay Posts;
- fixed/evergreen pages stay Pages;
- media stays WordPress Media Library.

Do not convert legacy content into plugin-specific shadow records just to simplify templates.

## 10. Product/category migration safeguards

- preserve product slugs when products are continuing;
- preserve category hierarchy/slugs when still valid;
- do not silently move products to `Uncategorized`;
- do not create new categories merely from keyword variants;
- brand taxonomy must be identified before rewriting brand archives;
- verify product image/attribute/description associations after template changes.

## 11. Article migration safeguards

Public evidence currently shows an `/articles/` hub with article detail URLs under `/blog/`.

Do not change the post permalink structure merely for visual consistency. If the deployment later chooses a new permalink, every indexed article requires an explicit redirect.

## 12. NAP/contact migration

Because public pages currently show inconsistent location/address wording, template migration must not propagate conflicting values globally.

Before launch choose one approved canonical contact/location dataset and update all reused site surfaces from that owner.

## 13. Verification

For each representative and high-value migrated URL verify:

- HTTP status;
- final URL;
- canonical output owner;
- title/H1 topic consistency;
- breadcrumb/internal-link destination;
- mobile render;
- structured data owner/no duplication;
- no soft-404 content;
- no redirect chain/loop;
- old URL behavior if redirected.

## 14. Migration artifact

Implementation phase should create a machine-readable redirect inventory, for example `docs/redirect-matrix.csv`, once the full deployment crawl is available.

Do not fabricate that file during repository preparation without the actual full URL inventory.