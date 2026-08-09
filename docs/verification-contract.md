# Verification Contract

## Purpose

Implementation must prove requirement coverage, not merely render pages.

## 1. Repository invariants

- canonical docs internally consistent;
- no raw client files/secrets/generated packages/debug dumps;
- implementation contract changes update docs in same commit;
- traceability rows remain mapped to implementation and verification.

## 2. Architecture invariants

- exactly one Kernel;
- <=8 first-party bootable owners unless approved change;
- one native Graha product content-model owner;
- one asset owner;
- one navigation data tree;
- optional providers resolved centrally;
- no duplicate product/content persistence;
- no custom DB by default;
- no generic repair/migration/telemetry framework;
- no unthreat-modeled public mutation endpoint;
- no WooCommerce prerequisite for Graha product activation, CRUD, migration or rendering.

## 3. Admin information-architecture assertions

Verify on the actual WordPress admin:

- exactly one plugin-owned top-level Graha menu exists;
- its visible label is **Graha Selang Content**;
- its canonical slug is `graha-selang-content`;
- it is the second visible sidebar item, immediately after Dashboard;
- default implementation uses menu position `3`;
- if a plugin collision requires ordering correction, only the Graha parent is repositioned and unrelated menu order is preserved;
- every plugin-owned Graha custom menu page is a child of `graha-selang-content`;
- no sibling root Graha settings/RFQ/content/helper/product menu exists;
- standard WordPress product/category/brand screens linked by Graha remain native CRUD screens;
- direct access to each Graha child page enforces the correct capability;
- state-changing admin actions require capability + nonce + validation/sanitization + native persistence;
- Graha admin CSS/JS loads only on Graha-owned screens;
- the admin wrapper does not introduce duplicate product/content/provider CRUD.

## 4. Scope-count invariants

Before launch verify the final URL inventory explicitly reconciles the brief baseline:

- total baseline = 96;
- product/series baseline = 68;
- hub baseline = 18;
- application baseline = 4;
- merge/redirect baseline = 5;
- retire baseline = 1;
- retained content intents baseline = 90;
- zero unexplained baseline/current crawl rows;
- zero `REVIEW` decisions at launch.

A changed live-site count is allowed only with documented reconciliation/owner-approved classification; never silently alter baseline numbers to make tests pass.

## 5. Template-family coverage

Representative tests must cover every row in `template-matrix.csv`:

- Home;
- archive/product hub;
- category;
- product rich + sparse;
- application;
- brand;
- About;
- Service;
- Technical RFQ;
- Guide/Article;
- Legal/Trust when required;
- Search;
- 404.

A template family may share code with another family; behavior still needs coverage.

## 6. Required route coverage

At minimum test `/`, `/about-us/`, `/products/`, representative `/product/{slug}/`, parent+child `/product-category/{slug}/` where present, representative `/brand/{slug}/`, all four retained application URLs, `/layanan-kami/`, `/articles/`, representative `/blog/{slug}/`, `/contact-us/`, RFQ route/state, approved evergreen, search, 404 and all redirect/retire rows.

The native product model itself must verify `graha_product`, `graha_product_category`, and `graha_product_brand` register the intended route bases through normal WordPress rewrite arguments.

## 7. Migration assertions

- every REDIRECT reaches closest intended final URL in one hop where practical;
- no loops/chains;
- `/services/` no longer competes with `/layanan-kami/` after final mapping;
- `/products-2/` duplication resolved;
- internal links do not point at redirects;
- sitemap lists canonical URLs only;
- retired URL is not soft-redirected to Home;
- all continuing product/category/brand/article/application slugs resolve as expected;
- crawl-diff against Wave 0 has no unintended disappearances.

## 8. Homepage/product hierarchy assertions

Home must visibly and semantically preserve:

- anchor Hydraulic Hose/MORGEN;
- anchor Industrial Hose & Assembly/HAMMER+SUNFLEX;
- supporting Ducting;
- supporting PVC Spiral/Spring/Suction;
- supporting Fittings/Couplings/Accessories;
- specialist CNG/high-pressure gas;
- four discovery doors: product/application/brand/specification.

Published product links must come from native `graha_product` records. Do not accept six equal-priority cards or a second PHP fixture catalog.

## 9. Product technical assertions

Test product/category/application states for:

- semantic populated specs only;
- no invented pressure/standard/certification/compatibility;
- resource links valid when present;
- compatible fitting relationships explicit;
- related product paths real anchors;
- rich and sparse records both deliberate.

## 10. Selector/filter assertions

- catalog remains usable without JS;
- keyboard-operable filters/decision tree;
- query behavior uses authoritative native taxonomy/meta;
- no uncontrolled indexable filter combinations;
- reset/empty/no-result states accessible;
- no duplicate product registry/query ownership.

## 11. RFQ assertions

Test representative product/application/contact RFQ flows:

- source URL/entity context captured correctly;
- dynamic field variation works as configured;
- buyer vs reseller/cooperation routing works where enabled;
- upload accepts only provider-approved type/size/count and follows privacy/retention policy;
- spam/security provider remains active;
- no arbitrary path/file access;
- accessible labels/errors/success state;
- contextual WhatsApp destination/context correct;
- conversion events fire once at approved points;
- plugin does not persist plaintext submission/upload data outside the chosen owner.

## 12. SEO/GEO structural assertions

Representative pages:

- server-rendered primary content;
- exactly one intended H1;
- logical headings/landmarks;
- important links are real anchors;
- one breadcrumb system;
- no duplicate canonical/meta/schema output;
- canonical/internal links align with current route;
- crawlable pagination;
- no visible `Keyword utama`, `Meta Title`, `Meta Description` or prompt/editor notes;
- no hidden keyword/GEO blocks;
- Indonesian UI/locale without accidental English archive labels;
- one approved organization/contact dataset.

## 13. Homepage/LCP/performance assertions

Inspect:

- first viewport contains useful HTML value proposition/CTA;
- actual LCP hero image is not lazy-loaded and uses appropriate priority;
- responsive image dimensions/srcset/sizes;
- below-fold media lazy-loads appropriately;
- only required CSS/JS loads per family;
- no duplicate libraries/fonts;
- layout shift from header/fonts/images controlled;
- archive/product query behavior acceptable;
- server/TTFB bottleneck reported separately from frontend regressions.

Perfect Lighthouse 100 is not the criterion; material field/CWV quality is.

## 14. Accessibility assertions

Keyboard test nav, drawer, selector/filter, product gallery if interactive, RFQ/form, pagination and any carousel. Verify visible focus, names/labels, contrast, reduced motion, no hover-only critical action, responsive technical tables, useful ~44px primary mobile targets and sticky CTA not covering content.

## 15. Legal/privacy assertions

- no production footer legal link is `href="#"`;
- legal destinations are approved/real or intentionally absent;
- privacy policy reflects enabled RFQ/form upload behavior;
- consent behavior matches actual tracking stack/legal requirement.

## 16. Analytics continuity assertions

When tracking exists, verify approved tags/events survive rebuild without creating plugin-owned reporting. RFQ, WhatsApp, contact/resource/selector events fire once with no duplicate listeners.

## 17. Content/fallback assertions

Missing specs/logo/contact/form data yields deliberate omission/fallback, not invented facts. No staging image impersonates a factual product/brand/workshop/project.

## 18. Security assertions

First-party mutations: capability, nonce, validation/sanitization, native persistence and contextual escaping. RFQ/file upload threat model/provider controls must be verified before production.

## 19. Regression exclusions

Guard against Gloskin/Morgen runtime identifiers outside approved Home grouping, custom product manager/database, a second product registry, Woo-only product primitives, Technical Library/PDF machinery, generic migration framework, telemetry, custom mail transport, duplicate SEO admin/proxy, hidden SEO content generator, multiple Graha root admin menus, or a global custom admin framework.

## 20. Launch assertions

- staging approved;
- backup/rollback ready;
- production noindex/robots safe;
- 96-baseline/current inventory reconciliation complete;
- redirect/canonical/schema/sitemap verified;
- analytics/event continuity verified;
- Graha Selang Content hierarchy/placement/capabilities verified on production admin;
- native `/products/`, `/product/{slug}/`, `/product-category/{slug}/`, and `/brand/{slug}/` routes verified on production;
- post-launch crawl completed;
- 30-day defect-warranty scope and 30/60/90 operations handoff documented.

Production implementation is complete only when these contracts pass on the target environment.

## 21. Production page-quality assertions

Apply `docs/approved-next-bundle-contract.md` to every shipped public page:

- no lorem ipsum, dummy/fabricated products/specs/certifications/NAP/legal facts or factual-looking visual placeholders;
- centralized typography/design tokens are used rather than repeated one-off component values;
- exactly one intended H1 and semantic shell/landmarks;
- deliberate responsive composition and sparse-state behavior;
- primary public template copy is Indonesian and fact-based;
- non-home families expose one useful visible breadcrumb when structurally appropriate;
- breadcrumb links are real anchors and Graha does not emit duplicate breadcrumb schema/provider metadata.

## 22. Homepage production assertions

A production Homepage may not be accepted until:

- at least four substantial real sections exist: hero/value, product discovery, capability/application/trust, and technical consultation/RFQ;
- all sections use real approved/native content and crawlable destinations;
- required anchor/support/specialist product hierarchy remains unequal and visible;
- the primary action is meaningful and not a placeholder;
- first-viewport/LCP behavior follows the existing performance contract.

A renderer/prototype with fixture data is not evidence that the production Homepage is complete.

## 23. One-shot migration assertions

When the one-shot migration mechanism is implemented, verify:

- no recursive/arbitrary-directory scanning;
- runtime reads only the explicitly approved disposable plugin-local bundle path;
- manifest identity/schema/file list/checksums are validated before writes;
- no bundle means no migration submenu;
- only valid pending state creates a temporary child submenu under `Graha Selang Content`;
- invalid/corrupt/filesystem states perform no destructive write and remain non-fatal to the public site;
- execution requires explicit native `edit_posts` capability + nonce;
- native `wp_insert_post` / `wp_update_post` plus post meta own imported `graha_product` records;
- new identity-only products are drafts;
- existing published/draft/private records are never promoted by migration;
- stable source identities make retry idempotent and prevent duplicate successful imports;
- partial failure is visible/retryable;
- consumed state prevents rerun even when physical cleanup fails;
- logical consumed state is persisted before cleanup is attempted;
- cleanup targets only the disposable runtime bundle, never plugin core or repository archive/source files;
- migration runtime stays off public frontend requests;
- no permanent root or generic migration admin framework is introduced.

## 24. Readiness assertions

Wave 0 remains incomplete while the deployment inputs in `implementation-inputs.md` are missing.

Wave 1 remains incomplete until a real WordPress runtime proves activation, actual admin placement/collision behavior, native product/taxonomy screen behavior, rewrite resolution, and representative Page/Post/product presentation. Repository-level PHP/stub/static checks must be reported as such and must not be presented as target-runtime verification.

## 25. Migration admin AJAX assertions

When the one-shot migration UI exists, additionally verify:

- admin page/menu render performs no bulk import, recursive scan, full checksum pass, media sideload, or large migration query;
- heavy validation/import is triggered only by explicit authorized action through authenticated `wp_ajax_*`;
- no `wp_ajax_nopriv_*` migration action exists;
- state-changing AJAX handlers enforce least-privilege capability + nonce and revalidate bundle identity/state;
- migration CSS/JS is scoped to the temporary Graha migration child screen;
- opening the screen does not start aggressive polling or an automatic import;
- timeout/network retry reconciles persisted state/source identities before continuing;
- no generic queue/framework or invented batch protocol is introduced without evidence from the real bundle.