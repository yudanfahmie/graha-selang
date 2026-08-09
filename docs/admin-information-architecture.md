# Admin Information Architecture Contract

## Purpose

All **Graha Selang plugin-owned admin menu pages** must be grouped under one clean WordPress admin parent. The plugin must not scatter custom Graha pages across the root admin sidebar.

## Canonical parent menu

- Visible label: **Graha Selang Content**
- Canonical menu slug: `graha-selang-content`
- Intended WordPress menu position: `3`
- Visual requirement: the parent must render as the **second visible admin sidebar item, immediately after Dashboard**.
- Canonical owner: `AdminService`.

Use the normal WordPress admin-menu API. Position `3` is the default implementation choice because Dashboard occupies the first core position. Verify the actual target environment. If another plugin causes a collision and Graha Selang Content is no longer immediately after Dashboard, use only a narrowly-scoped menu-order adjustment that moves this single parent directly after `index.php` while preserving the relative order of unrelated WordPress/plugin menus.

## Submenu-only rule

Every custom Graha Selang admin screen must be registered beneath `graha-selang-content`, normally with `add_submenu_page()`.

Examples of plugin-owned screens that belong under the wrapper **when they genuinely exist**:

- Overview / content handoff entry point;
- minimal site presentation settings;
- Graha-specific integration settings;
- RFQ presentation/routing configuration owned by the plugin;
- other future Graha-specific admin screens explicitly approved by the repository contracts.

Do **not** register separate top-level Graha menus such as `Graha Settings`, `Graha RFQ`, `Graha SEO`, `Graha Products`, or similar root siblings.

The parent page may use the same slug as the first Overview submenu so WordPress does not expose an awkward duplicate landing destination.

## Native-owner exception

This rule applies to **plugin-owned custom admin pages**. It does not move or clone native/provider administration.

Keep these in their authoritative native locations:

- WordPress Pages, Posts and Media;
- WooCommerce Products, categories, attributes, orders, customers and commerce settings;
- SEO-provider screens;
- form-provider submission/configuration screens;
- WordPress Users/Settings/Tools.

Graha Selang Content may link to those native screens when useful, but it must not create parallel CRUD or proxy administration merely to make everything appear under one menu.

## Capability and security rules

Menu placement is not authorization.

- Each child screen must declare and verify an appropriate least-privilege capability.
- State-changing actions require capability checks, nonce verification, validation/sanitization and native persistence.
- Do not grant broad `manage_options` access to every child merely for convenience.
- Do not expose sensitive provider configuration to users who only need editorial access.

The parent must remain useful for the intended admin/editor audience without weakening child-page authorization.

## Admin UX rules

- Keep labels concise and Indonesian-facing unless a provider screen is inherently external.
- Do not create empty submenu placeholders for future ideas.
- Keep navigation depth to one submenu level under Graha Selang Content unless an approved requirement proves deeper hierarchy is necessary.
- Use WordPress admin conventions; do not build a SPA/admin framework for simple settings.
- Provide clear page titles, descriptions, save feedback and error states.
- Load Graha admin CSS/JS only on Graha-owned admin screens.
- Do not globally restyle the WordPress admin.
- Do not hide or reorder unrelated WordPress/plugin menus except the minimal parent placement rule above.

## Ownership expectations

`AdminService` owns:

- registration of `Graha Selang Content`;
- submenu registration for plugin-owned screens;
- the minimal placement/order behavior needed to keep the wrapper immediately after Dashboard;
- routing/dispatch for Graha-owned admin pages;
- screen-scoped admin assets/enhancements;
- minimal presentation/integration settings allowed by the data contracts.

`AdminService` must not own:

- a duplicate product/content CRUD system;
- WooCommerce administration;
- SEO campaign/rank administration;
- form submission storage/mail transport;
- generic migration/diagnostics tooling;
- global WordPress admin menu management.

## Verification contract

At minimum verify on the target WordPress admin:

1. `Graha Selang Content` exists exactly once as a top-level plugin-owned menu.
2. It is immediately after Dashboard in the visible sidebar.
3. Its canonical slug is `graha-selang-content`.
4. Every Graha plugin-owned menu page is a child of that parent.
5. No other Graha plugin-owned top-level menu exists.
6. Native WordPress/Woo/SEO/form-provider screens remain in their native owners.
7. Direct URL access to each child enforces its own capability.
8. State-changing actions enforce nonce + capability + validation.
9. Graha admin assets do not load globally on unrelated admin screens.
10. No child screen introduces duplicate storage/CRUD merely for admin convenience.

Any future feature requiring a new Graha admin page must update this contract and `requirement-traceability.csv` in the same coherent change.