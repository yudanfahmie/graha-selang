# Product Catalog v1 source archive

Permanent developer/audit copy for the one-shot Graha Selang product migration.

## Provenance

The records are a conservative snapshot of **44 product identities/titles visibly present in the current public Graha Selang WooCommerce catalog/navigation on 2026-08-09**. This is known current product evidence, not a replacement for the still-required Wave 0 full crawl/reconciliation.

The archive deliberately stores only:

- deterministic `source_id`;
- observed product title/name;
- deterministic target slug;
- source URL only where the exact public product URL was directly verified;
- one of the six already-approved Homepage presentation groups.

It does **not** import or infer price, stock, SKU, technical specification, certification, media, attributes, authoritative brand taxonomy, or commerce settings.

Group coverage in this bundle:

- 15 Hydraulic Hose anchor records;
- 11 Industrial Hose & Assembly anchor records;
- 5 Ducting support records;
- 2 PVC Spiral/Spring/Suction support records;
- 10 Fittings/Couplings/Accessories support records;
- 1 CNG/high-pressure specialist record.

## Dual-copy rule

This directory is the permanent repository source/archive and is never read or auto-deleted by production runtime.

The deployable disposable copy is `migration-runtime/product-catalog-v1/`. Its manifest and payload are intentionally identical at commit time. After verified target import, only that runtime copy is eligible for cleanup.

This bundle is **not** the frozen 96-URL migration matrix and does not change the 68 product/series + 18 hub + 4 application + 5 redirect + 1 retire baseline.
