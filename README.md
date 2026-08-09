# Graha Selang Site Core

Canonical developer handoff for the Graha Selang website redesign.

This repository is the **developer-facing source of truth**. Normal implementation must not depend on reopening the raw project repository.

## Product definition

The target is a **WordPress plugin that behaves as the site presentation/page-builder layer**. It owns the Graha Selang public shell, page-family presentation, reusable UI, responsive behavior, developer-side SEO/GEO-friendly structure, and safe integration with WordPress/WooCommerce.

It does not replace WordPress or WooCommerce.

## Scope boundary

### In this repository

- WordPress plugin architecture and bootstrap;
- global shell, header, navigation and footer;
- page-family templates and reusable components;
- WooCommerce presentation integration;
- product/category/brand discovery presentation using existing Woo-owned data;
- services, company, contact and article presentation using native WordPress data;
- responsive behavior and accessibility;
- performance/Core Web Vitals engineering;
- stable information architecture and migration-safe routes;
- semantic HTML, crawlability, internal-linking structure and metadata/schema integration readiness;
- developer-side SEO/GEO engineering contract;
- staging/launch verification rules.

### Outside this repository

- recurring keyword/content operations;
- backlink campaigns;
- SEO reporting/retainers;
- routine GSC/GA4/GTM/GBP operations;
- social/media campaigns;
- marketplace operations;
- paid ads;
- hosting procurement, DNS and domain administration;
- custom payment/order systems;
- business CRM/ERP replacement;
- speculative AI/GEO tooling with no stable web-platform contract.

## Requirements authority

Read in this order:

1. `CONTRIBUTING.md`
2. `docs/developer-source-of-truth.md`
3. `docs/architecture-efficiency-audit.md`
4. `docs/runtime-service-map.csv`
5. `docs/content-data-contracts.md`
6. `docs/seo-geo-engineering-contract.md`
7. `docs/legacy-migration-contract.md`
8. `docs/implementation-plan.md`
9. `docs/page-matrix.csv`
10. `docs/prune-matrix.csv`
11. `docs/verification-contract.md`
12. `docs/source-notes.md`
13. `docs/implementation-inputs.md`

The owner’s explicit instruction always outranks repository documentation. When requirements change, update the canonical docs in the same coherent change as implementation.

## Raw-source policy

`yudanfahmie/project-9901` is provenance only. Do not modify it, copy its raw files here, or make routine development dependent on re-reading it.

Raw Graha Selang source fingerprint pinned at preparation time:

- path: `graha-selang/Graha Selang Website Redesign & SEO Brief Jul 26.docx`
- blob SHA: `bc5e29fb65a9da45ac355d0b1ca189cafbefd340`
- size: `136040` bytes

If a future requirement exists only in raw material and has not been normalized here, treat it as new/pending input. Do not silently rediscover or invent it.

## Engineering baseline

`yudanfahmie/gloskin-site-core` at commit `e36039034533d3debb51ae6092e74a311c87d55a` is the current quality baseline for repository discipline, modular plugin architecture, ownership boundaries, validation, responsive behavior, accessibility and staging hardening.

Graha Selang reuses those **engineering principles**, not Gloskin-specific branding, medical content models, page taxonomy, routes or UI assets.

## Core architecture decision

Use a small modular monolith with one composition root and one owner per concern. Prefer native WordPress/WooCommerce storage and routing. No custom database tables, generic migration framework, duplicate commerce stack, custom mail backend, or global mega-class in v1.

The intended v1 owners are defined in `docs/runtime-service-map.csv`.

## SEO/GEO baseline

Developer-side SEO/GEO friendliness is **part of the product**, not an excluded marketing task. The plugin must produce a crawlable, semantic, fast, stable and internally connected website structure while yielding metadata/schema ownership to the configured SEO/WooCommerce providers when they already own it.

Operational SEO remains outside this repository. See `docs/seo-geo-engineering-contract.md`.

## Legacy-site rule

Redesign does not mean URL reset. Existing indexed routes must be inventoried and classified before launch. Preserve valuable URLs when possible; otherwise create an explicit one-hop redirect map. Never solve migration with guessed wildcard redirects.

See `docs/legacy-migration-contract.md`.

## Implementation status

**Repository preparation only.** No production plugin code is intentionally included yet. The next developer should start from `docs/implementation-plan.md`, not from the raw brief.