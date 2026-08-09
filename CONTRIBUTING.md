# Contribution Rules

These rules are mandatory for AI agents and human developers working in this repository.

## Branch policy

- Work directly on `main`.
- Do not create feature/work/temp branches or pull requests unless the repository owner explicitly changes this policy.
- Never use a branch as a scratch area.

## Repository and source authority

For normal Graha Selang development, this repository is authoritative. Read relevant material in this order:

1. `docs/developer-source-of-truth.md`
2. `docs/operational-requirements.md`
3. `docs/admin-information-architecture.md`
4. `docs/scope-inventory.csv`
5. `docs/requirement-traceability.csv`
6. `docs/architecture-efficiency-audit.md`
7. `docs/runtime-service-map.csv`
8. `docs/content-data-contracts.md`
9. `docs/template-matrix.csv`
10. `docs/page-matrix.csv`
11. `docs/seo-geo-engineering-contract.md`
12. `docs/legacy-migration-contract.md`
13. `docs/implementation-plan.md`
14. `docs/verification-contract.md`
15. `docs/prune-matrix.csv`
16. `docs/source-notes.md`
17. `docs/implementation-inputs.md`

`yudanfahmie/project-9901` is provenance/raw reference only. Do not modify it, copy raw project files here, or make routine implementation depend on re-reading it.

`yudanfahmie/gloskin-site-core` is an engineering baseline only. Do not wholesale-copy Gloskin branding, domain models, routes, CSS, JS or historical repairs.

## Frozen scope baseline

The brief baseline is **96 legacy URLs**: 68 product/series + 18 hub + 4 application + 5 merge/301 + 1 retire.

Rules:

- treat the 96 count as a migration reconciliation baseline;
- run a fresh current-site crawl before production route work;
- explain every delta between current crawl and baseline;
- never silently reduce the inventory;
- every current/baseline URL must end with a KEEP, REDIRECT or RETIRE decision before launch;
- do not confuse URL count with template count;
- do not create pages only to hit a numeric count.

## Before editing

1. Confirm repository `yudanfahmie/graha-selang`.
2. Checkout `main`.
3. Pull latest `origin/main`.
4. Record/report current HEAD.
5. Read `CONTRIBUTING.md`.
6. Read canonical docs relevant to the task.
7. Inspect current implementation before assuming a feature is missing.
8. Define one coherent outcome.
9. Identify canonical owner(s) for changed concerns.
10. For route/content changes, check scope inventory, migration and SEO/GEO consequences.
11. For RFQ/form changes, check provider capability, file-security/privacy and routing ownership.
12. For any custom admin page/menu change, check `docs/admin-information-architecture.md` first.

## Architecture efficiency contract

The target is a modular WordPress plugin with one micro-kernel and small internal owners.

Mandatory rules:

- exactly one composition root (`Kernel`);
- at most eight first-party bootable services in v1 unless an architecture change is explicitly approved;
- one canonical owner per concern;
- one first-party asset registry/owner;
- native WordPress routing/storage before custom infrastructure;
- `ProductContentService` owns registration of native Graha product entities;
- WordPress posts, terms and meta are authoritative Graha product storage;
- `TemplateService` owns presentation only and does not become a data owner;
- WordPress remains Pages/Posts/Media authority;
- no WooCommerce dependency unless a future approved architecture change explicitly introduces it;
- do not introduce commerce/cart/order behavior accidentally;
- SEO provider owns metadata/schema/sitemap when configured for them;
- form provider owns submission transport/spam/mail/file retention when it can satisfy the RFQ contract;
- optional integrations are adapters;
- no `System` mega-class or second composition layer;
- no custom database tables by default;
- no generic migration/recovery/telemetry/cache framework;
- no duplicate product/category/brand persistence.

## Admin-side information architecture

All **Graha plugin-owned admin menu pages** must be wrapped under exactly one top-level menu:

- visible label: **Graha Selang Content**;
- slug: `graha-selang-content`;
- intended position: immediately after Dashboard; use WordPress menu position `3` as the default implementation;
- canonical owner: `AdminService`.

Rules:

- all Graha custom admin pages use this parent, normally through `add_submenu_page()`;
- no separate root Graha settings/RFQ/content/helper menus;
- keep native WordPress/SEO-provider/form-provider screens in their own native menus;
- do not clone native CRUD merely to place it under the wrapper;
- capability checks remain per-screen and are not replaced by menu visibility;
- state-changing admin actions require capability + nonce + validation/sanitization + native persistence;
- load Graha admin assets only on Graha-owned screens;
- do not globally restyle or reorder unrelated WordPress admin menus;
- if a target-site plugin collision prevents the parent from being immediately after Dashboard, use only a narrow menu-order adjustment for this parent while preserving unrelated menu order.

See `docs/admin-information-architecture.md`.

## Product and discovery contract

Do not flatten the homepage into equal cards. Preserve the normalized hierarchy:

- anchor: Hydraulic Hose / MORGEN;
- anchor: Industrial Hose & Assembly / HAMMER value + SUNFLEX premium;
- support: Ducting Hose;
- support: PVC Spiral/Spring/Suction Hose;
- support: Fittings/Couplings/Accessories;
- specialist: CNG/high-pressure gas.

Support four discovery doors: product, application/industry, brand, specification need.

Filters/selectors must use authoritative structured data, be crawl-safe, and leave a server-rendered catalog path usable without JavaScript.

## Technical RFQ contract

RFQ is not a generic contact form. Preserve:

- page/entity context;
- dynamic technical fields;
- secure upload when approved provider supports it;
- buyer/end-user vs reseller/cooperation routing where configured;
- contextual WhatsApp;
- accessible validation/state;
- approved conversion-event hooks.

Do not silently add custom mail, submission storage or upload infrastructure. If the provider cannot meet the mandatory RFQ behavior, document an architecture decision first.

## SEO/GEO engineering contract

Technical SEO/GEO is required. Any public route/template/component change must preserve:

- server-rendered crawlable primary content;
- semantic landmarks and one intended H1;
- stable canonical routes;
- crawlable internal links and breadcrumbs;
- provider-safe canonical/meta/schema ownership;
- semantic technical product data;
- Indonesian public UI without template-string leaks;
- Core Web Vitals-minded asset/media behavior;
- no hidden keyword/GEO blocks, duplicate SEO copy or crawler hacks.

Operational SEO remains outside plugin code.

## Migration discipline

Before changing a public route:

- classify KEEP, REDIRECT or RETIRE;
- use the closest equivalent destination for redirects;
- one hop where practical;
- never blanket-redirect removed content to Home;
- internal links must point directly to final URLs;
- verify sitemap/canonical after migration.

Confirmed architectural concern: `/services/` must not remain a competing service hub against retained `/layanan-kami/`; exact redirect ownership is deployment-dependent. `/products-2/` likewise cannot remain a competing product hub and must be reconciled in the final URL matrix.

## Validation and persistence

For first-party state changes use capability checks, nonce, field-appropriate validation/sanitization, native persistence and final-context escaping.

Prefer Pages, Posts, Media, registered meta and Settings API. Native Graha products use the registered `graha_product` post type plus its registered taxonomies/meta. Avoid direct `$wpdb` writes.

No public unauthenticated AJAX/REST mutation without an explicit requirement and documented threat model.

## Content discipline

Never invent product specs, standards, certifications, brand authorization, addresses, phone numbers, prices or technical claims. Missing data stays editable/omitted.

Do not expose editorial strings such as `Keyword utama`, `Meta Title`, `Meta Description` or internal prompts in public templates.

## Commit policy

- Group one coherent outcome into one commit.
- Do not create one commit per file.
- No probe/checkpoint/temp commits.
- Never use generic messages such as `update`, `fix`, or `changes`.
- Messages are short, lowercase and action-oriented, for example `stabilize branded shell and catalog surfaces`.
- Docs/tests affected by an architectural decision ship in the same coherent commit.
- Public CSS/JS/runtime changes require version consideration.
- Run verification before commit.
- Push according to this owner policy and verify remote `main` after push.

## Documentation discipline

Changes to ownership, services, data fields, routes, inventory counts/classification, redirects, RFQ behavior, admin menu architecture, SEO/GEO responsibilities or retained/pruned baseline behavior must update canonical docs in the same coherent change.

## Verification before push

1. Review complete diff.
2. Run available checks.
3. Check secrets/raw files/generated archives/debug artifacts.
4. Verify architecture owner budget.
5. Verify affected scope/route rows and migration decisions.
6. Verify crawlable internal links and one H1.
7. Verify no duplicate canonical/meta/schema owner.
8. Verify native Graha product ownership remains with `ProductContentService` + WordPress posts/terms/meta.
9. Verify RFQ security/provider boundaries when affected.
10. Verify admin parent/submenu/capability/asset rules when affected.
11. Verify accessibility/performance regressions on affected families.
12. Commit coherent change set.
13. Push directly to `origin/main`.
14. Verify remote `main` points to pushed commit.
15. Inspect final commit stats/diff.

Do not claim completion when remote verification fails.
