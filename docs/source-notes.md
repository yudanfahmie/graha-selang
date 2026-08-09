# Source and Provenance Notes

## 1. Raw Graha source

Private provenance repository: `yudanfahmie/project-9901`.

Pinned raw source:

- `graha-selang/Graha Selang Website Redesign & SEO Brief Jul 26.docx`
- blob SHA `bc5e29fb65a9da45ac355d0b1ca189cafbefd340`
- size `136040` bytes.

Do not copy the binary into this repository.

## 2. Owner-confirmed normalization

- developer-only scope;
- pure WordPress plugin as website-builder/presentation layer;
- developer-side SEO/GEO-friendly structure included;
- operational SEO/marketing excluded;
- Gloskin is engineering-quality baseline, not a Graha product template;
- Graha repo must be sufficient for future developers without routine raw-source reading.

## 3. Deep-audit normalization decisions

The raw brief and project audit establish the following canonical operational baseline:

- controlled rebuild, not a blank-site reset;
- 96 legacy URLs classified as 68 product/series, 18 hubs, 4 applications, 5 merge/301, 1 retire;
- six homepage product groups with two anchors, three supporting groups and CNG specialist;
- four catalog entry doors: product, application/industry, brand, specification need;
- reusable presentation coverage for Home/archive/category/product/application/brand/About/service/RFQ/guide/legal/search/404;
- technical specs, crawl-safe selector/filter/decision tree, compatible fittings and approved resources;
- application/specialist themes such as mining, cement/bulk, marine, dredging/slurry, drilling, oil/gas, MRO and CNG without automatic doorway-page generation;
- technical RFQ with dynamic fields, upload capability, source context, buyer/reseller routing, contextual WhatsApp and conversion-event hooks;
- field-performance/CWV, practical WCAG AA, crawl-diff, rollback and launch verification;
- retained `/layanan-kami/` with competing `/services/` consolidation requirement;
- Contact conversion priority toward buyer technical RFQ;
- real legal destinations rather than `#` placeholders;
- Indonesian public UI/locale and removal of English/template/editorial leaks.

These requirements now live in canonical docs/matrices; they are not left as chat knowledge.

## 4. Why the 96-URL list is a baseline rather than copied raw rows

The live site can change after a brief is written. The safest developer contract is therefore:

1. preserve the brief's exact classification/count invariant;
2. run a fresh Wave-0 crawl;
3. reconcile every live row against the baseline;
4. document every delta;
5. reach zero REVIEW rows before launch.

This avoids making a stale appendix silently override current public reality while still preventing the brief's 96-URL scope from being forgotten.

## 5. Public-site migration evidence observed during preparation/audit

Current public evidence has shown, among other things:

- `/products/` and `/products-2/` both discoverable;
- public product/category/archive routes under Woo;
- `/articles/` hub and `/blog/{slug}/` article detail family;
- meaningful brand/category surfaces;
- `Uncategorized`/thin product-content cases;
- duplicate heading/template-string issues on some surfaces;
- inconsistent contact wording across pages;
- external technical-resource links on some products.

These are migration evidence, not automatically approved content truth.

## 6. Engineering baseline

Use `yudanfahmie/gloskin-site-core` for proven engineering principles: canonical docs, one Kernel, single concern ownership, native platform owners, asset discipline, accessibility/responsive quality, staging verification and raw-source independence.

Do not copy Gloskin medical domains, page inventory, styling or content.

## 7. No invented content

Do not infer company legal facts, addresses/hours/phones, certifications, pressure ratings, standards, media compatibility, brand authorization, pricing/stock, marketplace state or SEO keyword targets.

Templates provide structure and graceful sparse states; approved data remains authoritative.