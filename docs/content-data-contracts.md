# Content and Data Contracts

## 1. General rules

- Native WordPress/WooCommerce storage first.
- Zero custom database tables in v1.
- Stable WordPress/Woo IDs, not copied shadow records.
- Never invent factual business/product data.
- Optional fields must fail gracefully.
- All first-party admin writes require capability/nonce/validation/sanitization.
- Escape for the final output context.
- Public-site legacy data is migration evidence, not automatically approved truth when values conflict.

## 2. Site-wide settings

Prefer native WordPress settings/menu/page content before a custom option.

Only if needed, one small Graha settings record may contain presentation/integration values such as:

- primary consultation URL/WhatsApp target;
- secondary contact target;
- external form shortcode/block identifier;
- optional global social/marketplace URLs;
- optional logo/media attachment references when not better handled by WP custom logo/site identity;
- approved location selector/default.

Do not store product data, article data, brand definitions or navigation trees in this option.

## 3. Company/About data

Owner: native WordPress Page.

Support as content, blocks or registered page meta only when structured editing is truly required:

- official company name/display name;
- approved overview/history;
- value proposition;
- vision/mission;
- industries/applications served;
- capabilities/services summary;
- brand/supply context;
- trust/certification statements only when approved;
- location/contact CTA;
- approved media.

Do not hard-code “since 2016” or company claims in templates.

## 4. Locations/contact data

Public legacy evidence currently contains inconsistent address wording across pages. Therefore contact/location data requires one approved canonical owner before production launch.

Recommended model for a small number of locations:

- native Contact/About Page blocks or registered page meta;
- optionally one small structured setting if reused globally.

Fields when supplied:

- label/location name;
- address lines;
- city/postcode;
- phone;
- WhatsApp;
- email;
- map URL/embed data;
- opening hours if approved;
- purpose/audience label such as retail/grosir where applicable.

Missing values must be omitted or shown as a deliberate neutral fallback. Do not infer coordinates or normalize conflicting addresses automatically.

## 5. Products

Owner: WooCommerce Product.

The plugin reads/presents only.

Supported standard data may include:

- product name;
- slug/permalink;
- SKU;
- short/long description;
- featured/gallery images;
- price/stock if commerce uses them;
- product categories;
- brand taxonomy term when available;
- tags when editorially useful;
- Woo attributes;
- approved custom product meta registered by the authoritative commerce/content owner.

### Technical specification model

Prefer Woo attributes or clearly registered product meta for structured technical data such as:

- hose type/series;
- material/tube/cover;
- reinforcement;
- size/diameter range;
- working pressure;
- burst pressure;
- temperature range;
- applicable standard;
- bend radius;
- media compatibility;
- application/industry;
- connection/fitting compatibility.

These are **field capabilities**, not permission to fill missing values. Only render fields actually supplied for the product.

A semantic `<table>` or `<dl>` may present populated specs. Avoid turning specification labels into repeated keyword stuffing.

## 6. Product categories

Owner: WooCommerce product category terms.

Fields/data when supported by Woo/registered term meta:

- name;
- slug;
- description/introduction;
- image;
- parent/child hierarchy;
- optional approved supporting content;
- optional explicit related brand/article/service IDs.

Do not create a parallel “SEO category” database.

Category pages should gracefully handle empty terms and pagination.

## 7. Brands

Owner: the installed/approved Woo product-brand taxonomy.

Before implementation, identify the actual taxonomy slug/provider in the deployment. Reuse it.

Possible fields:

- brand name;
- slug;
- description;
- logo/media;
- website/reference URL when approved;
- related products from taxonomy membership;
- optional approved editorial content.

Do not register a second `brand` taxonomy if one already exists.

## 8. Services

Default owner: native WordPress Page content.

Current public evidence contains service themes for:

- crimping/assembly;
- custom fitting/coupling/flange;
- consultation/product selection;
- hose repair/replacement assessment.

Treat labels/copy as editable content.

If service detail URLs are required, prefer child/native Pages first. A CPT is justified only if there is a demonstrated need for repeated structured service records and relationships.

Service content may support:

- title;
- summary;
- detailed description;
- process/capability information;
- applications;
- media;
- related product/category IDs;
- consultation CTA.

## 9. Articles

Owner: native WordPress Posts.

Use standard:

- title/slug;
- excerpt;
- content;
- featured image;
- author/date if intentionally displayed;
- categories/tags;
- normal post status;
- optional explicit related entity IDs via registered post meta if required.

Do not create a parallel article repository.

### Related content

Prefer explicit/editorial relationships over runtime keyword matching. For example, an article may store selected Woo product/category/brand IDs. If that feature is not required, omit it rather than building an inference engine.

## 10. Evergreen/topic landing pages

Owner: native WordPress Pages.

Existing legacy landing pages may represent valuable search/user intent independent of Woo product records. Preserve them when migration review classifies them KEEP.

Their content is ordinary approved page content. The plugin supplies presentation patterns only.

Do not create hundreds of programmatic keyword pages from templates.

## 11. Navigation

Owner: native WordPress menu where available.

`NavigationService` normalizes one menu tree for all viewport presentations.

Fallback code navigation may exist for a safe initial state, but it must not become a second editable source once a WP menu is configured.

## 12. Media

Owner: WordPress Media Library; Woo owns product image associations.

Rules:

- use attachment IDs and responsive WordPress image functions;
- honor editor-supplied alt text;
- do not synthesize product-specific alt claims;
- preserve image dimensions/aspect ratio;
- generic staging fallback imagery must never impersonate a specific product/brand/location;
- production factual media overrides fallback media.

## 13. Documents/download links

Current public products may link to external technical resources. This does not justify a custom document/PDF subsystem.

Model approved resources as ordinary product/page links or registered metadata when needed:

- label;
- URL/attachment ID;
- resource type;
- optional file size/version if supplied.

Do not build signed downloads, PDF poster generation, document indexing or a technical-library database without a separate requirement.

## 14. Forms

Owner: external form provider.

Graha plugin stores at most the configured form identifier and renders provider output within the site design. If the provider is unavailable, show an intentional contact fallback.

No custom mail delivery, CAPTCHA, autoresponse or submission database.

## 15. SEO metadata/schema

Metadata/schema storage belongs to WordPress/SEO/Woo providers. Graha templates must expose clean semantic source content and hooks/integration data.

Never store a second copy of SEO title/description/schema fields merely to render the frontend.

## 16. Data conflict rule

When two legacy/public values conflict:

- do not choose silently;
- mark production value pending/needs approval;
- keep templates/data model capable of rendering the approved value later;
- record the input in `docs/implementation-inputs.md` if it blocks release.