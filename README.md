# Graha Selang Site Core

Canonical developer handoff for the Graha Selang website redesign.

This repository is the developer-facing source of truth. The implementation target is a **WordPress plugin that behaves as the site presentation/page-builder layer** while preserving native WordPress and WooCommerce ownership of their data and business logic.

## Scope

This repository is **developer-only**. It covers the website/plugin architecture, information architecture, content/data contracts, presentation requirements, migration-safe routing, responsive/accessibility/performance requirements, and developer-side SEO/GEO-friendly structure.

It does **not** own recurring SEO operations, backlink work, campaign/content operations, search-console monitoring, analytics reporting, marketplace operations, hosting/DNS administration, or other marketing/operations work.

## Raw-source policy

`yudanfahmie/project-9901` is provenance only. Do not copy raw project files into this repository and do not make routine implementation depend on reopening them. Requirements that belong to implementation must be normalized into canonical docs here.

## Baseline

`yudanfahmie/gloskin-site-core` is the current engineering-quality baseline for repository discipline, modular WordPress-plugin architecture, native ownership boundaries, validation, accessibility, responsive behavior, and staging/production hardening. Graha Selang must reuse those **principles**, not Gloskin-specific product models, branding, routes, copy, or medical-domain decisions.

## Status

Repository preparation in progress. Read `CONTRIBUTING.md` and `docs/developer-source-of-truth.md` before implementation once those canonical documents are present.
