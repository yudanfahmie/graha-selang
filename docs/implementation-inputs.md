# Implementation Inputs

These are **deployment/client inputs**, not architecture questions. They do not block repository preparation and must not be guessed by developers.

## Required before production launch

| Input | Why it matters | Safe implementation behavior until supplied |
|---|---|---|
| Approved canonical company/contact/NAP data | Public legacy pages conflict on address wording | Keep one editable owner; do not hard-code conflicting legacy values |
| Target WordPress/PHP/Woo versions | Compatibility/testing | Build to supported APIs; confirm before package/release |
| Actual Woo brand taxonomy/provider + rewrite slug | Prevent duplicate brand model/routes | Adapter detects/reuses approved provider; do not register second taxonomy |
| Active SEO provider and ownership settings | Prevent duplicate canonical/meta/schema/breadcrumbs | Provider-safe guards; WordPress defaults if no provider |
| Active form provider/form ID | Contact behavior | Render contact fallback until configured |
| Full current URL crawl + redirects | SEO-safe redesign migration | Do not fabricate redirect matrix; preserve known routes by default |
| Approved brand assets/logo/fonts/colors | Production identity | Use neutral development tokens/placeholders; no Gloskin/Morgen assets |
| Approved product/category/brand content cleanup | Taxonomy and thin/duplicate content | Woo data remains authoritative; templates tolerate sparse data |
| Commerce mode: inquiry/catalog vs transactional Woo | Header/product CTA/cart surfaces | Keep architecture Woo-compatible; do not disable or invent commerce mode |
| Approved global consultation/WhatsApp targets | CTA correctness | Omit/use editable fallback rather than guessing |

## Decisions that can remain implementation-time

These should be resolved from the actual environment without reopening raw project files:

- whether a native WordPress menu already represents the intended IA;
- whether services need child Pages or only sections on `/layanan-kami/`;
- which Woo-supported archive/filter hooks best fit the installed version;
- whether provider breadcrumbs should be rendered directly or consumed as data;
- whether any existing evergreen Page needs a custom template family;
- whether a small Graha settings page is needed after native owners are inspected.

## Architecture-change triggers

Stop and update canonical docs before implementing if discovery shows a genuine need for:

- a custom CPT;
- a custom database table;
- more than eight bootable owners;
- a custom public route/rewrite engine;
- custom AJAX/REST mutation;
- a custom cache;
- a first-party redirect engine;
- a first-party metadata/schema output layer despite an existing provider;
- custom commerce/order/payment logic;
- document/PDF management beyond simple approved links.

The default answer to these is **not yet** until evidence demonstrates the need.