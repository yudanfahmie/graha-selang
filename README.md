# Graha Selang Site Core

Canonical developer handoff for the Graha Selang controlled website rebuild.

This repository is the **developer-facing source of truth**. Normal implementation must not depend on reopening `yudanfahmie/project-9901`.

## Product definition

The target is a **WordPress plugin that behaves as the site presentation/page-builder layer**. It owns Graha Selang public presentation, reusable UI, route-aware templates, responsive/accessibility behavior, technical product discovery, RFQ presentation/integration, developer-side SEO/GEO-friendly structure, and safe integration with WordPress/WooCommerce.

It does not replace WordPress, WooCommerce, the configured SEO provider, the configured form provider, or their business data.

## Frozen operational baseline

The redesign brief defines a **96-URL legacy inventory**:

- 68 product / series URLs;
- 18 hub URLs;
- 4 application URLs;
- 5 merge + permanent redirect URLs;
- 1 retire URL.

That means **90 retained content-intent URLs + 6 legacy-action URLs** as the brief baseline. A fresh crawl is still required before production because the live site may have changed. Every delta from the 96-URL baseline must be reconciled; nothing may be silently dropped.

The brief also requires:

- six visible homepage product groups with two anchor categories;
- four discovery entry doors: product, application/industry, brand, specification need;
- technical spec presentation, crawl-safe filters/selector and compatible fittings/resources;
- application/specialist architecture;
- dynamic technical RFQ, secure upload support, source-page context, buyer/reseller routing, contextual WhatsApp and conversion events;
- controlled migration, crawl-diff, rollback readiness, accessibility and Core Web Vitals work.

Read `docs/operational-requirements.md` for the complete normalized public/runtime contract.

## Admin-side contract

All **Graha Selang plugin-owned admin pages** must live under exactly one top-level WordPress admin parent:

- label: **Graha Selang Content**;
- slug: `graha-selang-content`;
- target position: immediately after Dashboard (default implementation position `3`);
- owner: `AdminService`.

No Graha-specific settings/RFQ/content-helper page may appear as a separate root sidebar menu. Native WordPress, WooCommerce, SEO-provider and form-provider screens remain in their authoritative native locations rather than being cloned or proxied.

See `docs/admin-information-architecture.md`.

## Scope boundary

### In this repository

- WordPress plugin architecture/bootstrap;
- shell/header/navigation/footer;
- page-family/template presentation;
- WooCommerce presentation integration;
- product/category/brand/application discovery;
- technical product selector/filter presentation using authoritative data;
- Services, About, Contact/RFQ and article presentation;
- responsive behavior and accessibility;
- performance/Core Web Vitals engineering;
- stable IA and migration-safe routes;
- semantic HTML, crawlability, internal linking and provider-safe metadata/schema integration;
- developer-side analytics/event continuity hooks;
- one clean Graha-specific admin wrapper and screen-scoped admin UX;
- staging/launch/rollback verification contracts.

### Outside this repository

- recurring keyword/content operations;
- backlinks and rank-monitoring retainers;
- routine GSC/GA4/GTM/GBP operations;
- social/media/paid campaigns;
- marketplace operations;
- hosting procurement/DNS/domain administration;
- custom payment/order systems;
- business CRM/ERP replacement;
- speculative AI/GEO ranking tooling.

## Requirements authority and reading order

1. `CONTRIBUTING.md`
2. `docs/developer-source-of-truth.md`
3. `docs/operational-requirements.md`
4. `docs/admin-information-architecture.md`
5. `docs/scope-inventory.csv`
6. `docs/requirement-traceability.csv`
7. `docs/architecture-efficiency-audit.md`
8. `docs/runtime-service-map.csv`
9. `docs/content-data-contracts.md`
10. `docs/template-matrix.csv`
11. `docs/page-matrix.csv`
12. `docs/seo-geo-engineering-contract.md`
13. `docs/legacy-migration-contract.md`
14. `docs/implementation-plan.md`
15. `docs/verification-contract.md`
16. `docs/approved-next-bundle-contract.md`
17. `docs/prune-matrix.csv`
18. `docs/source-notes.md`
19. `docs/implementation-inputs.md`
20. `docs/developer-kickoff-prompt.md`

Owner instructions outrank repository docs. Material implementation discoveries must update the affected canonical docs in the same coherent change.

## Raw-source policy

Raw Graha source fingerprint:

- path: `graha-selang/Graha Selang Website Redesign & SEO Brief Jul 26.docx`
- blob SHA: `bc5e29fb65a9da45ac355d0b1ca189cafbefd340`
- size: `136040` bytes

Do not copy that binary into this repository. Requirements needed by developers have been normalized here.

## Engineering baseline

`yudanfahmie/gloskin-site-core` is the engineering-quality baseline for repository discipline, modular plugin architecture, native ownership, responsive/accessibility behavior and verification. Reuse principles, not Gloskin branding, medical models, routes, CSS/JS or content.

## Architecture decision

Use a small modular monolith with one composition root and one owner per concern. Prefer native WordPress/WooCommerce storage/routing. No custom database tables, generic migration framework, duplicate commerce stack, custom mail backend, global mega-class, or sprawling custom admin framework by default.

## Product catalog migration

A narrow one-shot product catalog migration now lives behind the existing `AdminService` boundary. It is **not** a bootable service and never loads in normal frontend Kernel composition.

- permanent source/audit copy: `migration-source/product-catalog-v1/`;
- disposable runtime copy: `migration-runtime/product-catalog-v1/`;
- temporary admin child: `Graha Selang Content -> Migrasi Produk` only while the runtime bundle is pending/retryable;
- execution: explicit authenticated `wp_ajax_*`, `manage_woocommerce`, nonce, atomic option lock;
- persistence: native WooCommerce products + native post meta provenance only;
- consumed state is stored before cleanup; cleanup only removes the fixed runtime bundle files/directory.

The committed v1 bundle contains **44 conservative current-public product identity/title records** observed on Graha Selang on 2026-08-09: 15 hydraulic-anchor, 11 industrial-anchor, 5 ducting-support, 2 PVC/suction-support, 10 fittings/accessories-support and 1 CNG specialist record. The bundle intentionally omits prices, stock, SKU, technical specifications, certifications, media, attributes and authoritative brand taxonomy. It does **not** replace the still-required 96-URL Wave 0 reconciliation.

## Development verification

Run the lightweight repository gate before each push:

```bash
./scripts/verify.sh
```

It performs PHP syntax checks, static architecture/security guards, navigation normalization/render checks, page/Home presentation tests, one-shot migration validation/idempotency/lock/cleanup tests, admin capability/nonce/submenu/asset checks, Kernel hook smoke checks, and JavaScript syntax validation when Node is available. These checks are repository-level verification; they do not replace activation and behavior testing on the target WordPress environment.

## Implementation status

**Wave 1 environment-independent foundation is substantially implemented and the narrow product-catalog migration path is prepared.** The plugin still has one `Kernel` with four active owners: `AdminService`, the canonical `AssetService`, native `NavigationService`, and `TemplateService`. The one-shot migration coordinator is a lazy admin helper rather than a fifth bootable owner.

Native Page/Post content receives the centralized Graha presentation primitives without route takeover. The production Homepage augmentation now reads only migrated/native Woo products carrying Graha migration provenance, preserves the required two-anchor/three-support/one-specialist hierarchy, and activates only when all six product groups plus real published Woo shop, `/layanan-kami/`, and `/contact-us/` destinations are available. Otherwise WordPress native Home content remains untouched.

Wave 1 is **not complete** until a real WordPress runtime verifies activation, actual admin placement/collision behavior, and representative Page/Post/Woo integration. The committed migration runtime bundle remains logically **pending until it is explicitly run on a real target WooCommerce environment**; repository simulations do not claim production import or cleanup. Wave 0 remains incomplete for the deployment inputs recorded in `docs/implementation-inputs.md`.
