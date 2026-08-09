# Implementation Inputs

These are deployment/client inputs, not unresolved architecture questions. Do not guess them.

## Current Wave 0 discovery status

Repository-only execution on **2026-08-09** did not provide target WordPress admin/server access, a fresh current-site crawl/export, or verified deployment provider configuration. Wave 0 production discovery therefore remains incomplete for route, redirect, SEO/form-provider, analytics, RFQ transport/upload, contact/legal, and admin-collision decisions.

Safe implementation boundary for this state:

- do not create/finalize `docs/redirect-matrix.csv` without real crawl evidence;
- do not freeze unknown public routes beyond the approved native product routes and `/request-quote/`, contact data, event IDs, RFQ recipients, or upload policy;
- continue only environment-independent work that preserves native WordPress ownership and provider-safe boundaries;
- verify the admin parent placement/collision behavior on the actual target WordPress admin before calling the admin-shell requirement complete.

## Site bootstrap decision

Fresh activation now has an owner-approved structural bootstrap contract:

- provision or reuse native Pages at `home`, `about-us`, `layanan-kami`, `contact-us`, and `request-quote`;
- never overwrite meaningful existing Page title/content/status;
- on a genuinely fresh/default WordPress install, assign the provisioned published Home as the static front page;
- preserve a valid existing static front-page choice and preserve established non-fresh posts-front configuration;
- render a Graha front-page shell and deterministic fallback navigation without waiting for product migration or native menu assignment;
- keep form-provider-specific RFQ fields, upload, recipients, routing, privacy, and analytics configuration unresolved until approved inputs exist.

## Product migration input packaged 2026-08-09

A one-shot **44-record current-public product identity bundle** is now packaged in `migration-source/product-catalog-v1/` with an identical disposable copy in `plugin/graha-selang-site-core/migration-runtime/product-catalog-v1/`.

This input is deliberately narrower than the frozen migration baseline and does not close Wave 0:

- it contains only product identity/title, deterministic target slug, directly verified source URL where available, and one approved Home presentation group;
- it does not invent or import price, stock, SKU, technical specifications, certification, media, category membership, or brand membership;
- it is not the fresh full-site crawl and does not reconcile the 68 product/series baseline or the remaining 96-URL scope;
- target execution/verification/cleanup remains pending until a real WordPress runtime is available.

The source/archive copy is permanent repository evidence. Only the fixed plugin-local runtime copy may be consumed and cleaned after verified import.

## Native product ownership decision

Graha product ownership is now explicit and does not depend on WooCommerce:

- CPT `graha_product`;
- archive `/products/`;
- single `/product/{slug}/`;
- taxonomy `graha_product_category` at `/product-category/{slug}/`;
- taxonomy `graha_product_brand` at `/brand/{slug}/`.

The current 44-record bundle does not assign category or brand terms because those memberships are not part of the verified bundle data.

## Required during Wave 0 / before production

| Input | Why it matters | Safe behavior until supplied |
|---|---|---|
| Fresh full current-site crawl/export | Reconcile live state against 96-URL baseline | Do not finalize redirect matrix without it |
| Exact row mapping for current 68/18/4-equivalent content and 5 redirect/1 retire baseline | Prevent silent route loss | Use `scope-inventory.csv` count contract and mark reconciliation incomplete |
| Approved canonical NAP/company data | Legacy contact wording conflicts | One editable owner; no hard-coded guess |
| Target WordPress/PHP versions | Compatibility | Use supported APIs and verify release target |
| SEO provider + ownership settings | Prevent duplicate canonical/meta/schema/sitemap/breadcrumb | Provider-safe integration |
| Form provider + RFQ conditional/upload/routing capability | Mandatory technical RFQ | `/request-quote/` Page may render safe orientation/CTA; architecture decision if provider insufficient |
| RFQ upload allowed file types/size/count + retention/privacy policy | Security/legal | Upload disabled until approved |
| Buyer vs reseller/cooperation routing rules | Correct lead handling | Present intent safely but do not invent recipient/routing |
| Approved WhatsApp/contact targets | Contextual CTA | Omit/configurable fallback rather than guess |
| Analytics/tag owner, current tag method, approved conversion-event names | Measurement continuity | Stable integration hooks; no reporting product |
| Approved legal/privacy/terms/cookie destinations and consent requirement | No dead # links; RFQ upload privacy | Do not generate fake legal copy |
| Approved logo/fonts/colors/media/workshop proof | Brand/trust | Neutral development assets only |
| Approved product/category/brand data cleanup + datasheets | Specs/selector/claims | Sparse-state templates; no invented fields or taxonomy membership |
| Backup/deployment/rollback owner and procedure | Controlled rebuild safety | Do not launch until defined |

## Implementation-time native decisions

Resolve from actual deployment without raw brief:

- WP menu structure/IA storage after the deterministic fallback has served bootstrap;
- approved category/brand term membership and descriptions;
- SEO-provider breadcrumb integration;
- exact current 4 application routes;
- which specialist themes deserve additional pages after approval;
- whether service detail Pages are necessary;
- whether Legal needs a dedicated Page route or provider-managed surface;
- whether one small Graha settings screen is needed.

## Architecture-change triggers

Update canonical docs before implementing a custom table, virtual public route engine, new AJAX or REST mutation beyond the approved one-shot importer, custom cache, first-party redirect engine, first-party SEO graph, custom RFQ storage-mail backend, custom commerce logic, document subsystem, or more than eight bootable services.

Default answer is **not yet** until evidence proves the native/provider path cannot satisfy the canonical requirements.
