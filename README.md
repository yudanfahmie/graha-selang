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

Read `docs/operational-requirements.md` for the complete normalized contract.

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

Use a small modular monolith with one composition root and one owner per concern. Prefer native WordPress/WooCommerce storage/routing. No custom database tables, generic migration framework, duplicate commerce stack, custom mail backend or global mega-class by default.

## Implementation status

**Repository preparation / canonical planning only.** Production plugin code is intentionally not started in this phase. The next developer begins from `docs/implementation-plan.md` and the traceability/inventory contracts above, not from the raw brief.