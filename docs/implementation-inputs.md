# Implementation Inputs

These are deployment/client inputs, not unresolved architecture questions. Do not guess them.

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