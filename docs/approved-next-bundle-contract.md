# Approved Next-Bundle Contract

## 1. Authority and purpose

This document canonicalizes the owner-approved next-bundle requirements for Graha Selang. It is read together with `developer-source-of-truth.md`, `operational-requirements.md`, `implementation-plan.md`, `verification-contract.md`, and the existing ownership/data/migration contracts.

It does not reopen the raw project brief and does not change the frozen 96-URL reconciliation baseline.

The implementation order is:

1. canonicalize these requirements;
2. finish the environment-independent Wave 1 page/design foundation;
3. build the production Homepage only when approved real content/native destinations are available;
4. build the narrow one-shot migration mechanism only when an approved migration bundle and required runtime prerequisites exist;
5. complete production inner-page families with real data/provider information.

Wave 0 remains incomplete until the target environment, fresh crawl, providers, and other deployment inputs in `implementation-inputs.md` are known.

## 2. Production public-page quality

A shipped public page is complete only when it is production-oriented rather than a rendering demo.

Every shipped public page must have:

- real approved/native content;
- deliberate responsive composition;
- production-quality typography driven by centralized tokens;
- centralized spacing/design language;
- semantic landmarks;
- exactly one intended H1;
- useful internal navigation;
- an appropriate CTA/conversion path where required;
- deliberate sparse/empty-data behavior;
- practical WCAG AA behavior;
- provider-safe SEO/GEO structure;
- no dummy or fabricated business/product facts.

Do not ship lorem ipsum, fake product cards/specifications/certifications, generic demo copy, or visual placeholders that impersonate factual business content.

## 3. Homepage quality gate

The production Homepage must contain at least four substantial sections backed by real approved/native content:

1. Hero/value proposition with a meaningful primary action.
2. Product/solution discovery with real product families and crawlable destinations.
3. Capabilities/applications/trust using verified proof/content.
4. Technical consultation/RFQ conversion with a meaningful buyer-oriented path.

Additional sections are allowed only when supported by real content.

The existing hierarchy remains mandatory:

- anchor: Hydraulic Hose / MORGEN;
- anchor: Industrial Hose & Assembly / HAMMER + SUNFLEX;
- support: Ducting Hose;
- support: PVC Spiral / Spring / Suction Hose;
- support: Fittings / Couplings / Accessories;
- specialist: CNG / high-pressure gas.

The minimum-four-section requirement must not be satisfied by four equal generic cards.

## 4. Centralized typography and design tokens

`AssetService` remains the asset owner. Do not create a separate design-system service merely for tokens.

Before page implementation expands, centralize at minimum:

- font stacks;
- type scale;
- font weights;
- line heights;
- spacing scale;
- content/container widths;
- approved breakpoint set;
- semantic colors;
- borders;
- radii;
- shadows when used;
- minimum control/touch size;
- focus treatment;
- motion and reduced-motion behavior.

Components/templates consume shared tokens rather than introducing repeated one-off values. Until approved brand font/color inputs arrive, use neutral/system-safe fallback tokens and do not present them as final brand facts.

## 5. Breadcrumb contract

All non-home public page families provide a useful visible breadcrumb when structurally appropriate.

Requirements:

- one reusable visible breadcrumb renderer;
- semantic `<nav>`/list markup;
- real crawlable anchors for ancestors;
- correct hierarchy supplied by the active presentation/native/provider context;
- responsive and accessible presentation;
- no breadcrumb JSON-LD or duplicate metadata/schema output from Graha while the authoritative SEO provider is unknown;
- no second competing breadcrumb system.

The renderer may safely provide the native Home ancestor, but must not invent intermediate product/category/brand/application routes.

## 6. Public language and copy

Primary public UI/copy is Indonesian. Technical English may remain where natural.

Public copy must be professional, concise, fact-based, and free from keyword-stuffed or AI-style filler. Do not expose internal SEO/editorial controls or accidental English template labels.

## 7. One-shot migration scope

The approved migration facility is a small one-shot import mechanism, not a permanent generic migration framework.

It must stay admin-only and off public frontend requests.

Lifecycle:

`repository source/archive bundle -> disposable plugin-local runtime bundle -> detect -> validate -> temporary admin submenu -> native import -> verify -> mark consumed -> attempt disposable-bundle cleanup -> hide migration UI`

The root-repository source/archive copy is retained for audit/reproducibility and is never a production runtime dependency or auto-deletion target.

The disposable runtime copy is the only eligible migration input/cleanup target and must live at one explicitly approved path inside the deployed plugin tree.

## 8. Migration manifest and detection

Each runtime bundle requires an explicit compact manifest containing at least:

- bundle ID;
- schema/version;
- migration type;
- source identity/version;
- explicit file list;
- checksums;
- optional minimum runtime/plugin dependency;
- expected import/verification counts when reliable.

Never infer behavior from arbitrary directory names and never recursively scan arbitrary paths.

Supported detection states:

- no bundle: no migration submenu;
- valid pending bundle: temporary submenu under `Graha Selang Content`;
- invalid/corrupt bundle: no destructive write; authorized admin receives an actionable error;
- consumed bundle: never executes again and submenu stays hidden from normal use;
- filesystem error: no fatal public failure; report the exact operational problem.

## 9. Migration admin/security contract

The temporary migration screen:

- is a child of `graha-selang-content` only;
- appears only for a valid pending bundle;
- uses the native `edit_posts` capability for the current `graha_product` capability model;
- requires capability and nonce for execution;
- validates all manifest/file inputs before writes;
- provides clear pending/running/failed/consumed feedback;
- disappears after logical consumption;
- is not a generic diagnostics/admin SPA.

Protect against double-click execution, concurrent requests, refresh replays, partial failure, corrupt files, missing dependencies, target collisions, and unsafe arbitrary paths.

## 10. Native import ownership and idempotency

Migration writes use authoritative WordPress APIs only.

WordPress owns Pages, Posts, Media, the native `graha_product` records, `graha_product_category` terms, `graha_product_brand` terms, and their normal metadata/media relationships. `ProductContentService` owns registration of that product content model; it does not create a parallel database or custom CRUD layer.

The one-shot product importer writes `graha_product` using `wp_insert_post`, `wp_update_post`, and post meta. New identity-only products are created as drafts, existing product publication status is preserved, and the current 44-record bundle does not infer category or brand membership because those fields are not present in verified bundle data.

Use stable migration/source identities so retries do not duplicate successfully imported entities or relationships. Partial failure must remain detectable and retryable.

Prefer small native option state when sufficient. State must distinguish at least pending, running/locked when needed, failed/retryable, and consumed.

No custom migration database table by default.

## 11. Consumption and cleanup semantics

After successful import and verification:

1. persist logical `consumed` state first;
2. attempt cleanup of the disposable runtime bundle;
3. hide migration UI from normal admin use.

Logical consumption is authoritative. If physical cleanup fails because the filesystem is read-only/restricted, the bundle still must not rerun accidentally and the cleanup failure must be surfaced to authorized administrators/developers.

The plugin must never delete or modify its own core runtime source as cleanup. Only the explicitly approved disposable migration bundle is eligible for deletion. Repository archive/source files remain untouched.

## 12. Architecture boundary

Do not add a permanently bootable `MigrationService` by default.

Preferred first implementation:

- a small detector/coordinator loaded only in relevant admin requests;
- `AdminService` owns conditional submenu registration;
- parsing/validation is separated from write execution without becoming a generic framework;
- native WordPress APIs own writes;
- no frontend migration runtime.

`ProductContentService` is the narrow bootable owner for native product content-model registration only. If another dedicated bootable owner becomes necessary, document why native/current owners cannot satisfy the requirement, update `runtime-service-map.csv` and traceability, and remain within the `<= 8` owner budget.

## 13. Current readiness gates

### Safe now

- centralized tokens/typography;
- reusable responsive/accessibility primitives;
- `TemplateService` presentation composition without public route takeover;
- native `graha_product`/product-category/brand content-model registration;
- semantic shell/header/navigation/main/footer;
- visible breadcrumb renderer/integration boundary;
- representative presentation-family prototypes;
- conditional asset loading;
- repository-level verification for these contracts.

### Not ready without real inputs

Production Homepage activation/content composition still depends on real approved content and crawlable native destinations.

The committed one-shot product migration mechanism still requires real WordPress runtime execution/verification. Repository simulations do not prove target import, rewrite behavior, or filesystem cleanup.