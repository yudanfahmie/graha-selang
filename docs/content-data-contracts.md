# Content and Data Contracts

## 1. General rules

- Native WordPress storage first.
- Zero custom database tables in v1 by default.
- Stable WordPress IDs; no shadow records.
- Never invent business/product/technical data.
- Optional fields fail gracefully.
- Public legacy data is migration evidence when conflicting.
- First-party admin writes require capability, nonce, validation/sanitization and output escaping.

## 2. Global/site settings

Prefer native Site Identity, menus, Pages and provider settings. At most one small Graha presentation/integration option if genuinely needed, containing values such as approved global WhatsApp/contact target, form identifier, logo/media reference or integration toggles.

Do not store product catalog, brand definitions, navigation trees, article data or RFQ submissions in that option.

## 3. Company/About

Owner: native WordPress Page.

May contain approved overview/history, vision/mission, capability/service summary, industries/applications, brand/supply context, trust evidence, locations/contact CTA and media.

Do not hard-code legal/company/importer/distributor/certification claims in templates.

## 4. Contact/location

Use one approved canonical dataset before launch. Fields may include label, address, city/postcode, phone, WhatsApp, email, map data, opening hours and audience/channel label.

Do not normalize conflicting legacy values automatically.

## 5. Products

Owner: native WordPress CPT `graha_product` registered by `ProductContentService`.

Canonical public routes are `/products/` for the archive and `/product/{slug}/` for individual products. Standard WordPress CRUD/admin remains authoritative. Supported data includes title, slug, editor content, excerpt, featured image/Media Library relationships, native category/brand terms, and approved registered product meta.

### One-shot migration provenance

The product-catalog v1 importer may write only these Graha-specific provenance/presentation meta fields in addition to normal native product fields:

- `_graha_source_identity`: deterministic stable source identity used to reconcile retries and prevent duplicate successful imports;
- `_graha_source_bundle`: bundle ID that last reconciled the product;
- `_graha_source_url`: observed public source URL only when that exact URL was verified;
- `_graha_home_group`: one of the six approved Homepage presentation groups.

These fields do not form a shadow catalog and do not imply price, stock, technical specification, certification, media compatibility, category membership, or brand authorization. After a successful import, `graha_product` posts remain the authoritative runtime product records; the repository archive remains audit evidence only.

### Technical fields

Prefer clearly registered product meta and native taxonomy terms for populated values such as:

- hose type/series;
- material/tube/cover;
- reinforcement;
- size/diameter range;
- working pressure;
- burst pressure;
- temperature range;
- standard;
- bend radius;
- media compatibility;
- application/industry;
- connection/fitting compatibility.

Render semantic table or definition-list markup. Missing values are omitted. Do not fill specs from product-name guesses.

### Resources and fittings

Approved product resources are links/attachment IDs with label/type/version if supplied. Do not build a parallel technical-library/PDF system.

Compatible fitting/coupling relationships should use explicit native product/term IDs or approved structured data, not runtime keyword inference.

## 6. Product categories

Owner: native taxonomy `graha_product_category` registered for `graha_product` by `ProductContentService`. Canonical public route: `/product-category/{slug}/`.

Support name/slug/description/hierarchy and approved supporting content/related entity IDs. No parallel SEO-category database.

## 7. Brands

Owner: native taxonomy `graha_product_brand` registered for `graha_product` by `ProductContentService`. Canonical public route: `/brand/{slug}/`.

Brand pages may use approved name/description/logo/reference links and product membership. The 44-record identity-only bundle does not infer or assign brand membership.

Brief positioning such as HAMMER value, SUNFLEX premium and MORGEN hydraulic prominence is content/presentation direction only. Technical superiority, authorization or certification claims require evidence.

## 8. Application pages

Default owner: native WordPress Pages.

Baseline: **4 retained application-intent URLs** after migration reconciliation.

Application content may include approved industry/use case, operating conditions, relevant product/category/brand IDs, technical selection factors, proof/media and RFQ CTA.

Specialist themes do not automatically become Pages. Create a new indexable route only for distinct approved intent/content.

## 9. Services

Default owner: native Page `/layanan-kami/` plus child/detail Pages only when distinct content justifies them.

Support approved crimping/assembly, custom fitting/coupling/flange, selection consultation, repair/replacement and workshop proof.

Do not preserve `/services/` as a second canonical service hub.

## 10. Articles/guides

Owner: native Posts. Use title/slug/excerpt/content/featured media/author/date/categories/tags and optional explicit related product/category/brand/application IDs.

No keyword inference engine or AI content generator.

## 11. Evergreen/topic hubs

Owner: native Pages when migration classifies the URL KEEP and it has distinct useful intent. Avoid mass programmatic keyword pages.

## 12. Navigation

Owner: native WordPress menu where available. NavigationService normalizes one tree for desktop/mobile. Four discovery doors—product, application, brand, specification—must remain reachable with real anchors.

## 13. Media

Owner: WordPress Media Library. Product image associations use normal WordPress featured-image/attachment relationships.

Use attachment IDs and responsive WP image APIs, honor editor alt data, preserve dimensions/aspect, and never use generic stock as a factual product/brand/workshop proof image.

## 14. Technical RFQ data contract

### Experience fields

The exact form schema is provider/configuration owned, but it must be capable of collecting application-specific technical information rather than only a generic message. Typical field groups may include only those approved for the selected inquiry path:

- contact/company identity;
- buyer/end-user vs reseller/cooperation intent;
- product/category/application context;
- technical requirement fields sourced from the selected path;
- quantity/project notes where configured;
- attachment/upload where enabled;
- source URL/entity ID captured by the integration;
- consent/privacy acknowledgement where required.

Do not fabricate mandatory technical fields that business owners have not approved; the UI must be data/config-driven enough to vary by inquiry context.

### Upload ownership/security

Prefer form-provider upload handling. Before enabling file upload confirm:

- allowed MIME/extensions;
- size/count limits;
- authenticated/public upload threat model as applicable;
- malware/provider security behavior;
- access visibility;
- retention/deletion policy;
- privacy wording.

The Graha plugin does not accept arbitrary filesystem paths or build a generic upload library.

### Routing

Provider/FormAdapter configuration may route/label buyer vs reseller/cooperation leads and preserve source context. Custom email transport/submission tables are excluded unless a documented architecture change proves provider limitations.

### Analytics events

Event hooks may expose approved RFQ/WhatsApp/resource/selector interactions. IDs/names belong to deployment analytics configuration. Do not store an analytics reporting database.

## 15. SEO metadata/schema

Metadata/schema storage/output belongs to WordPress/the configured SEO provider. Graha supplies semantic source content, route context and integration hooks without a duplicate title/canonical/schema store.

## 16. Legal/privacy

Legal content is approved WordPress/provider content. No footer legal link may point to `#` in production. Privacy must reflect actual RFQ/form/upload behavior.

## 17. Data-conflict rule

When legacy/public values conflict: do not choose silently; mark production value pending; keep native/provider editing possible; record a blocking input in `implementation-inputs.md`; update canonical docs after approval.