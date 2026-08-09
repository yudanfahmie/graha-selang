# Source and Provenance Notes

## 1. Raw Graha Selang source

Private provenance repository: `yudanfahmie/project-9901`.

Pinned source at repository-preparation time:

- path: `graha-selang/Graha Selang Website Redesign & SEO Brief Jul 26.docx`
- Git blob SHA: `bc5e29fb65a9da45ac355d0b1ca189cafbefd340`
- size: `136040` bytes

Do not copy this binary file into `yudanfahmie/graha-selang`.

The filename/date is provenance, not itself a guarantee that every current public-site value is approved/final.

## 2. Owner clarifications normalized into this repo

Owner-confirmed project rules:

- scope is developer-only for both Gloskin and Graha Selang;
- implementation is a pure WordPress plugin acting as website builder/presentation layer;
- developer-side SEO/GEO-friendly web structure is in scope;
- SEO/GEO operations/marketing are not plugin responsibilities;
- Graha Selang repo preparation should inherit the strongest engineering lessons/state from Gloskin;
- current task is repository preparation/rules/plans/contracts so the implementation team does not need to read raw material routinely.

These rules are reflected in `developer-source-of-truth.md` and `seo-geo-engineering-contract.md`.

## 3. Engineering baseline

Quality baseline used during preparation:

- repository: `yudanfahmie/gloskin-site-core`
- commit: `e36039034533d3debb51ae6092e74a311c87d55a`

Retained principles:

- direct-main contribution discipline;
- canonical repo documentation;
- modular monolith/micro-kernel;
- native platform ownership;
- single concern owners;
- conditional assets;
- no speculative migration/recovery infrastructure;
- strong accessibility/responsive verification;
- raw-source independence.

Not retained:

- Gloskin medical domain models;
- Gloskin routes/page inventory;
- Gloskin branding/assets/copy;
- product decisions specific to skincare;
- historical Morgen UI/runtime identifiers.

## 4. Current public-site evidence snapshot

Observed on 2026-08-09 for migration/context only:

- `https://www.grahaselang.com/`
- `https://www.grahaselang.com/about-us/`
- `https://www.grahaselang.com/products/`
- `https://www.grahaselang.com/products-2/`
- `https://www.grahaselang.com/layanan-kami/`
- `https://www.grahaselang.com/articles/`
- `https://www.grahaselang.com/contact-us/`
- Woo product/category routes under `/product/` and `/product-category/`
- observed brand archive under `/brand/hammer/`
- observed article detail routes under `/blog/.../`
- observed evergreen/topic landing page such as `/industrial-hose-indonesia-supplier-selang-industri-lengkap/`.

Public search evidence showed `/products/` exposing 96 product results at the time of observation. Treat counts as dynamic, not requirements.

## 5. Important migration observations

### Duplicate product discovery surfaces

Both `/products/` and `/products-2/` are publicly discoverable. The new build must not preserve two competing indexable product hubs.

### Product taxonomy hygiene

Some public product pages were observed with `Category: Uncategorized`, while others use meaningful product categories. Taxonomy cleanup is a Woo content/migration concern, not a template workaround.

### Brand taxonomy

A public `/brand/{slug}/` route exists. The implementation must identify the actual installed authoritative brand taxonomy/provider before registering anything.

### Article permalink shape

The public article hub is `/articles/`, while individual articles are observed under `/blog/{slug}/`. Do not normalize this cosmetically without a redirect inventory.

### Visible SEO control notes

At least one observed product page rendered editorial strings resembling `Keyword utama`, `Meta Title` and `Meta Description` in visible body content. New templates must not leak SEO-control notes into user-facing output.

### Contact/NAP inconsistency

Public pages expose inconsistent Surabaya/location address wording. Therefore repository docs intentionally do not hard-code a canonical NAP. Approved contact data is a launch input.

### External technical resources

At least one public product page links to an external Google Drive resource. This is evidence that product resources may need link presentation; it is **not** evidence for building a custom document/PDF subsystem.

## 6. Source confidence rule

Canonical architecture/ownership/scope decisions in this repository are firm because they come from owner instruction and baseline engineering decisions.

Business values that are only observed from legacy/public content remain migration evidence until approved. Future owner/client data overrides conflicting legacy values.

If a raw-only requirement is later surfaced that materially changes implementation, normalize it into this repository and update affected contracts in the same coherent change. Do not send ordinary developers back to raw files as a permanent workflow.

## 7. No invented content

Do not infer or fabricate:

- company legal facts;
- addresses/hours/phones;
- certifications;
- product standards;
- working/burst pressure;
- media compatibility;
- brand authorization claims;
- pricing/stock;
- marketplace status;
- SEO keyword targets.

Templates should provide fields/layouts/fallbacks, not invented client truth.