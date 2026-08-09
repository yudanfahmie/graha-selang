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
- one asset owner;
- one navigation data tree;
- optional providers resolved centrally;
- no duplicate product/content persistence;
- no custom DB by default;
- no generic repair/migration/telemetry framework;
- no unthreat-modeled public mutation endpoint.

## 3. Scope-count invariants

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

## 4. Template-family coverage

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

## 5. Required route coverage

At minimum test `/`, `/about-us/`, `/products/`, parent+child product category where present, brand route, rich+sparse product, all four retained application URLs, `/layanan-kami/`, `/articles/`, representative `/blog/{slug}/`, `/contact-us/`, RFQ route/state, approved evergreen, search, 404 and all redirect/retire rows.

## 6. Migration assertions

- every REDIRECT reaches closest intended final URL in one hop where practical;
- no loops/chains;
- `/services/` no longer competes with `/layanan-kami/` after final mapping;
- `/products-2/` duplication resolved;
- internal links do not point at redirects;
- sitemap lists canonical URLs only;
- retired URL is not soft-redirected to Home;
- all continuing product/category/brand/article/application slugs resolve as expected;
- crawl-diff against Wave 0 has no unintended disappearances.

## 7. Homepage/product hierarchy assertions

Home must visibly and semantically preserve:

- anchor Hydraulic Hose/MORGEN;
- anchor Industrial Hose & Assembly/HAMMER+SUNFLEX;
- supporting Ducting;
- supporting PVC Spiral/Spring/Suction;
- supporting Fittings/Couplings/Accessories;
- specialist CNG/high-pressure gas;
- four discovery doors: product/application/brand/specification.

Do not accept six equal-priority cards if that loses the required hierarchy.

## 8. Product technical assertions

Test product/category/application states for:

- semantic populated specs only;
- no invented pressure/standard/certification/compatibility;
- resource links valid when present;
- compatible fitting relationships explicit;
- related product paths real anchors;
- rich and sparse records both deliberate.

## 9. Selector/filter assertions

- catalog remains usable without JS;
- keyboard-operable filters/decision tree;
- query behavior uses authoritative data;
- no uncontrolled indexable filter combinations;
- reset/empty/no-result states accessible;
- no duplicate product registry/query ownership.

## 10. RFQ assertions

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

## 11. SEO/GEO structural assertions

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

## 12. Homepage/LCP/performance assertions

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

## 13. Accessibility assertions

Keyboard test nav, drawer, selector/filter, product gallery if interactive, RFQ/form, pagination and any carousel. Verify visible focus, names/labels, contrast, reduced motion, no hover-only critical action, responsive technical tables, useful ~44px primary mobile targets and sticky CTA not covering content.

## 14. Legal/privacy assertions

- no production footer legal link is `href="#"`;
- legal destinations are approved/real or intentionally absent;
- privacy policy reflects enabled RFQ/form upload behavior;
- consent behavior matches actual tracking stack/legal requirement.

## 15. Analytics continuity assertions

When tracking exists, verify approved tags/events survive rebuild without creating plugin-owned reporting. RFQ, WhatsApp, contact/resource/selector events fire once with no duplicate listeners.

## 16. Content/fallback assertions

Missing specs/logo/contact/form data yields deliberate omission/fallback, not invented facts. No staging image impersonates a factual product/brand/workshop/project.

## 17. Security assertions

First-party mutations: capability, nonce, validation/sanitization, native persistence and contextual escaping. RFQ/file upload threat model/provider controls must be verified before production.

## 18. Regression exclusions

Guard against Gloskin/Morgen runtime identifiers, custom product manager/database, Technical Library/PDF machinery, generic migration framework, telemetry, custom mail transport, duplicate SEO admin/proxy, hidden SEO content generator.

## 19. Launch assertions

- staging approved;
- backup/rollback ready;
- production noindex/robots safe;
- 96-baseline/current inventory reconciliation complete;
- redirect/canonical/schema/sitemap verified;
- analytics/event continuity verified;
- post-launch crawl completed;
- 30-day defect-warranty scope and 30/60/90 operations handoff documented.

Production implementation is complete only when these contracts pass on the target environment.