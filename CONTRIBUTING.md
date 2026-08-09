# Contribution Rules

These rules are mandatory for AI agents and human developers working in this repository.

## Branch policy

- Work directly on `main`.
- Do not create feature/work/temp branches or pull requests unless the repository owner explicitly changes this policy.
- Never use a branch as a scratch area.

## Repository and source authority

For normal Graha Selang development, this repository is authoritative. Read relevant material in this order:

1. `docs/developer-source-of-truth.md`
2. `docs/architecture-efficiency-audit.md`
3. `docs/runtime-service-map.csv`
4. `docs/content-data-contracts.md`
5. `docs/seo-geo-engineering-contract.md`
6. `docs/legacy-migration-contract.md`
7. `docs/implementation-plan.md`
8. `docs/page-matrix.csv`
9. `docs/prune-matrix.csv`
10. `docs/verification-contract.md`
11. `docs/source-notes.md`
12. `docs/implementation-inputs.md`

`yudanfahmie/project-9901` is provenance/raw reference only. Do not modify it, copy raw project files here, or make routine implementation depend on re-reading it.

`yudanfahmie/gloskin-site-core` is an engineering baseline and provenance source only. Do not copy Gloskin branding, medical content models, page contracts or implementation history wholesale.

## Before editing

1. Confirm repository `yudanfahmie/graha-selang`.
2. Checkout `main`.
3. Pull latest `origin/main`.
4. Record/report current HEAD.
5. Read the canonical docs relevant to the task.
6. Inspect current implementation before assuming a missing feature.
7. Define one coherent outcome for the change.
8. Identify the canonical owner for every runtime concern being changed.
9. If routes/content ownership change, review migration and SEO/GEO implications before coding.

## Architecture efficiency contract

The target is a modular WordPress plugin with one micro-kernel and small internal owners. Do not introduce distributed-service infrastructure, a generic dependency-injection framework, or framework complexity merely to imitate microservices.

Mandatory rules:

- exactly one composition root (`Kernel`);
- at most eight first-party bootable services in v1 unless the owner approves an architecture change;
- one canonical owner per concern;
- one first-party asset registry/owner;
- native WordPress routing/storage before custom infrastructure;
- WooCommerce remains the sole commerce authority;
- WordPress remains the sole Posts/Pages/Media authority;
- optional integrations are adapters and dependency availability is resolved inside the adapter;
- no Graha Selang `System` mega-class;
- no second bootstrap/workflow composition layer;
- no class whose primary purpose is to repair/protect/restore another first-party class instead of fixing the canonical owner;
- no generic migration console, diagnostics bundle, telemetry subsystem or cache framework in v1;
- no custom database tables without a demonstrated requirement and explicit architecture update;
- no duplicate product/category/brand storage merely for presentation convenience;
- do not dual-write relationships unless a measured need justifies denormalization.

See `docs/architecture-efficiency-audit.md` and `docs/runtime-service-map.csv`.

## WordPress/WooCommerce ownership

### Graha Selang Site Core may own

- site shell and navigation presentation;
- page-family template selection and presentation contexts;
- reusable site-specific components;
- frontend asset loading;
- presentation-safe WooCommerce integration;
- minimal global presentation settings;
- developer-side SEO/GEO structure and provider integration boundaries.

### It must not own

- parallel product CRUD/storage;
- parallel cart/checkout/order/payment logic;
- custom customer/account database;
- parallel article database;
- custom media library;
- custom form mail transport where a form provider owns submissions;
- generic SEO campaign/content management.

## SEO/GEO engineering contract

Technical SEO/GEO is not optional polish. Any route/template/component change must preserve:

- server-rendered crawlable primary content;
- semantic landmarks and valid heading hierarchy;
- one clear page topic/H1;
- stable canonical route behavior;
- indexable hub/detail relationships;
- real anchor links for important internal navigation;
- accessible breadcrumb capability on deep content;
- metadata/schema provider compatibility without duplicate output;
- meaningful media alt-data support from WordPress Media Library;
- useful product specifications in semantic HTML;
- Core Web Vitals-minded asset/media behavior;
- no hidden keyword/GEO blocks, cloaking or duplicated SEO copy.

Do not interpret “SEO operations excluded” as permission to ship poor site structure. Read `docs/seo-geo-engineering-contract.md`.

## Legacy migration discipline

Before changing an existing public route:

- identify its current legacy URL;
- decide KEEP, REDIRECT or RETIRE;
- preserve the old URL where practical;
- if redirecting, map it to the closest equivalent destination;
- avoid redirect chains and wildcard guesses;
- confirm canonical/internal links point directly to the final destination.

Do not remove `/products-2/` or other legacy surfaces merely because they look redundant; retirement requires a redirect decision documented in the migration inventory.

## Validation and persistence discipline

Simplification must not weaken security.

For custom state-changing admin paths use capability checks, nonces, field-appropriate validation/sanitization, then one native WordPress persistence path. Escape again for final output context.

Prefer native Pages, Posts, Media, registered post meta, the Settings API and WooCommerce APIs. Avoid direct `$wpdb` writes.

No public `wp_ajax_nopriv_*` endpoint belongs in v1 unless an explicit feature requires it and its threat model is documented.

Do not add routine custom locks, revision choreography, read-after-write verification, rollback wrappers or manual option-cache surgery around ordinary WordPress writes.

## Commit policy

- Group files implementing one coherent outcome into one commit.
- Do not create one commit per file.
- Do not create probe/checkpoint/temporary commits.
- Keep messages short, lowercase and action-oriented.
- If a task contains independent production outcomes, use the smallest reasonable number of coherent commits.

## Change discipline

- Make only changes required by the current task and canonical architecture.
- Preserve working Graha Selang behavior unless requirements explicitly change it.
- Do not add dependencies/frameworks without demonstrated need.
- Do not wholesale-copy Gloskin or Morgen.
- Do not introduce Gloskin treatment/clinic/doctor models.
- Do not introduce Morgen historical migrations, recovery state, compatibility aliases, virtual routing, diagnosis bundles, telemetry, custom mail or product systems.
- Do not duplicate WooCommerce product/cart/checkout/order/payment ownership.
- Do not build operational SEO, backlink, reporting, analytics-management or marketing-campaign tooling into this plugin.
- Do not copy raw client DOCX/XLSX/PDF files into this repository.

## Content discipline

- Never invent product specifications, certifications, pressure ratings, standards, brand claims, addresses, phone numbers, prices or company facts.
- Treat existing public-site content as migration evidence, not automatically as approved truth when sources conflict.
- Keep missing factual data editable and render graceful empty states.
- Product technical specifications belong to WooCommerce-managed product attributes/meta where possible.
- Repeated marketing/SEO prose must not be hard-coded into templates.

## Documentation discipline

When implementation changes architecture ownership, service boundaries, storage, content fields, routes, legacy redirects, SEO/GEO responsibilities or retained/pruned baseline behavior, update the matching canonical documentation in the **same coherent commit**.

Do not let implementation knowledge live only in chat, commit messages or developer memory.

## Verification before push

1. Review the complete diff.
2. Confirm production files changed when the task is an implementation task.
3. Run available checks.
4. Check for secrets, raw client files, generated archives and debug artifacts.
5. Run architecture/exclusion checks when relevant.
6. Verify affected canonical and legacy routes.
7. Verify no duplicate concern owner was introduced.
8. Verify important internal links are crawlable anchors.
9. Verify no duplicate canonical/meta/schema owner was introduced.
10. Verify WooCommerce ownership remains intact.
11. Commit the coherent change set.
12. Push directly to `origin/main`.
13. Verify remote `main` points to the pushed commit.
14. Inspect final commit stats/diff.

Do not claim completion when changes exist only locally or push verification fails.