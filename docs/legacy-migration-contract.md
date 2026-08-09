# Legacy Migration Contract

## Purpose

Graha Selang is a controlled rebuild of an indexed site. URL/content migration is developer correctness, not post-launch cleanup.

## 1. Frozen baseline

The brief classifies **96 legacy URLs**:

- 68 product/series;
- 18 hubs;
- 4 applications;
- 5 merge + permanent redirect;
- 1 retire.

This yields 90 retained content intents plus 6 legacy-action URLs.

A fresh current-site crawl is mandatory because the site can change after the brief. The final machine-readable inventory must reconcile current crawl rows against this baseline and explain all additions/removals/classification differences.

## 2. Decisions

Every baseline/current public URL receives exactly one final migration decision:

- `KEEP` — same canonical public route remains;
- `REDIRECT` — permanent migration to closest equivalent;
- `RETIRE` — no meaningful equivalent; correct 404/410 behavior according to deployment decision.

Temporary `REVIEW` may exist during Wave 0 but **zero REVIEW rows are allowed at launch**.

## 3. Required final artifact

Wave 0 creates `docs/redirect-matrix.csv` or an equivalent machine-readable URL inventory with at least:

- legacy_url;
- current_http_status;
- current_final_url;
- brief_classification if matched;
- current_content_owner/type;
- final_decision;
- final_url/status;
- redirect_owner;
- canonical_expected;
- sitemap_expected;
- notes/evidence.

Do not make ordinary developers reopen the raw brief to fill this file. Compare the live crawl to `scope-inventory.csv` and canonical contracts.

## 4. Known route intentions

Preserve by default unless current inventory/owner instruction proves otherwise:

- `/`;
- `/about-us/`;
- `/products/` as the canonical product hub;
- continuing `/product/{slug}/`;
- continuing `/product-category/{slug}/`;
- continuing authoritative `/brand/{slug}/`;
- 4 retained application URLs after reconciliation;
- `/layanan-kami/`;
- `/articles/`;
- existing `/blog/{post-slug}/` article family;
- `/contact-us/`;
- approved distinct evergreen/topic URLs.

## 5. Known consolidation concerns

### `/services/` → retained service intent

The brief explicitly retains `/layanan-kami/` and requires the competing `/services/` surface to be merged/redirected after useful-content reconciliation. Internal links must point directly to the retained final service URL.

### `/products-2/` vs `/products/`

Current public evidence exposes both. They may not remain two competing canonical product hubs. Wave 0 determines whether `/products-2/` is one of the five brief redirect rows and migrates any unique useful content before redirect/retirement.

Do not guess the remaining redirect/retire row identities from URL names. Resolve them from current crawl and brief-baseline reconciliation, then record them in the final matrix. The count invariant remains 5 redirect + 1 retire for the brief baseline unless owner-approved new evidence changes the contract.

## 6. Redirect owner

Exactly one operational redirect owner in production: server/hosting, approved SEO/redirect provider, or a narrowly scoped first-party layer only if neither platform option is available.

Do not scatter redirects across server rules, multiple plugins, PHP hooks, JS and meta refresh.

## 7. Quality rules

- one hop where practical;
- no loops/chains;
- closest semantic destination;
- no blanket removed-page → Home redirects;
- no wildcard rule without proof against the inventory;
- internal links point directly to final routes;
- preserved useful query parameters where needed;
- sitemap lists canonical retained URLs only;
- canonicals match final routes.

## 8. Native-owner migration

Products remain native `graha_product` posts; categories remain `graha_product_category` terms; brands remain `graha_product_brand` terms; articles remain Posts; application/fixed/evergreen surfaces remain Pages where appropriate; media remains Media Library.

The one-shot product importer uses stable source identity and native WordPress APIs only. No plugin shadow records or custom product database are introduced for migration convenience.

## 9. Product/category safeguards

Preserve continuing slugs where practical, preserve meaningful category hierarchy, assign category/brand terms only from approved evidence, eliminate accidental generic category ownership through content cleanup rather than template hacks, and verify images/spec meta after migration.

The current 44-record identity-only bundle does not infer category or brand membership.

## 10. Article safeguards

Do not cosmetically change the `/blog/{slug}/` detail family without a complete article redirect map. `/articles/` hub and article-detail permalink may legitimately differ.

## 11. Contact/entity safeguards

Legacy contact data currently conflicts across surfaces. Select one approved canonical dataset before launch and render reused contact facts from that owner.

## 12. Crawl-diff

Wave 0 captures baseline crawl. Wave 5 compares staging/final route map. Wave 6 verifies production.

Acceptance:

- all 96 brief-baseline rows accounted for;
- current-site additions accounted for;
- 68/18/4 retained-intent buckets reconciled;
- 5 brief redirect decisions resolved;
- 1 brief retire decision resolved;
- zero REVIEW at launch;
- zero unintended soft 404, loop or chain;
- no important internal link targets a redirect;
- sitemap/canonical output aligns with final inventory.