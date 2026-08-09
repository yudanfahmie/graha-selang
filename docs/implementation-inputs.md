# Implementation Inputs

These are deployment/client inputs, not unresolved architecture questions. Do not guess them.

## Current Wave 0 discovery status

Repository-only execution on **2026-08-09** did not provide target WordPress admin/server access, a fresh current-site crawl/export, or verified deployment provider configuration. Wave 0 production discovery therefore remains incomplete for route, redirect, provider, taxonomy, analytics, RFQ transport/upload, contact/legal, commerce-mode, and admin-collision decisions.

Safe implementation boundary for this state:

- do not create/finalize `docs/redirect-matrix.csv` without real crawl evidence;
- do not freeze unknown public routes, provider ownership, brand taxonomy, contact data, event IDs, RFQ recipients, or upload policy;
- continue only environment-independent Wave 1 foundation work that preserves native WordPress/WooCommerce ownership and provider-safe boundaries;
- verify the admin parent placement/collision behavior on the actual target WordPress admin before calling the admin-shell requirement complete.

## Product migration input packaged 2026-08-09

A one-shot **44-record current-public product identity bundle** is now packaged in `migration-source/product-catalog-v1/` with an identical disposable copy in `migration-runtime/product-catalog-v1/`.

This input is deliberately narrower than the frozen migration baseline and does not close Wave 0:

- it contains only product identity/title, deterministic target slug, directly verified source URL where available, and one approved Home presentation group;
- it does not invent or import price, stock, SKU, technical specifications, certification, media, product attributes, or authoritative brand taxonomy;
- it is not the fresh full-site crawl and does not reconcile the 68 product/series baseline or the remaining 96-URL scope;
- target execution/verification/cleanup remains pending until a real WooCommerce runtime is available.

The source/archive copy is permanent repository evidence. Only the fixed plugin-local runtime copy may be consumed and cleaned after verified import.

## Required during Wave 0 / before production

| Input | Why it matters | Safe behavior until supplied |
|---|---|---|
| Fresh full current-site crawl/export | Reconcile live state against 96-URL baseline | Do not finalize redirect matrix without it |
| Exact row mapping for current 68/18/4-equivalent content and 5 redirect/1 retire baseline | Prevent silent route loss | Use `scope-inventory.csv` count contract and mark reconciliation incomplete |
| Approved canonical NAP/company data | Legacy contact wording conflicts | One editable owner; no hard-coded guess |
| Target WordPress/PHP/Woo versions | Compatibility | Use supported APIs and verify release target |
| Actual Woo brand taxonomy/provider | Prevent duplicate brand routes | Detect/reuse provider; no second taxonomy |
| SEO provider + ownership settings | Prevent duplicate canonical/meta/schema/sitemap/breadcrumb | Provider-safe integration |
| Form provider + RFQ conditional/upload/routing capability | Mandatory technical RFQ | Render safe fallback/prototype; architecture decision if provider insufficient |
| RFQ upload allowed file types/size/count + retention/privacy policy | Security/legal | Upload disabled until approved |
| Buyer vs reseller/cooperation routing rules | Correct lead handling | Present intent safely but do not invent recipient/routing |
| Approved WhatsApp/contact targets | Contextual CTA | Omit/configurable fallback rather than guess |
| Analytics/tag owner, current tag method, approved conversion-event names | Measurement continuity | Stable integration hooks; no reporting product |
| Approved legal/privacy/terms/cookie destinations and consent requirement | No dead # links; RFQ upload privacy | Do not generate fake legal copy |
| Approved logo/fonts/colors/media/workshop proof | Brand/trust | Neutral development assets only |
| Approved product/category/brand data cleanup + datasheets | Specs/selector/claims | Sparse-state templates; no invented fields |
| Commerce mode: inquiry/catalog vs transactional Woo | CTA/cart/account presentation | Remain Woo-compatible; do not force mode |
| Backup/deployment/rollback owner and procedure | Controlled rebuild safety | Do not launch until defined |

## Implementation-time native decisions

Resolve from actual deployment without raw brief:

- WP menu structure/IA storage;
- exact authoritative brand taxonomy slug;
- SEO-provider breadcrumb integration;
- specific Woo hooks for archive/filter presentation;
- exact current 4 application routes;
- which specialist themes deserve additional pages after approval;
- whether service detail Pages are necessary;
- whether Legal/RFQ needs a dedicated Page route or provider-managed surface;
- whether one small Graha settings screen is needed.

## Architecture-change triggers

Update canonical docs before implementing a custom CPT/table/public route engine/AJAX or REST mutation/custom cache/first-party redirect engine/first-party SEO graph/custom RFQ storage-mail backend/custom commerce logic/document subsystem/more than eight services.

Default answer is **not yet** until evidence proves the native/provider path cannot satisfy the canonical requirements.
