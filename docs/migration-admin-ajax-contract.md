# Migration Admin AJAX Contract

## 1. Purpose

The one-shot migration UI must remain operationally light. Opening the Graha admin screen must not trigger bulk imports, recursive scans, large checksum passes, media downloads, product queries, or other migration work.

This contract refines REQ-037–REQ-039 without changing the requirement that migration stays admin-only, native-owner, idempotent, one-shot, and disposable-bundle based.

## 2. Page-load budget

Normal admin page rendering may perform only cheap bounded work needed to decide whether the temporary migration entry can exist, such as checking a known plugin-local manifest path and small native state.

Do not perform on page render:

- imports or writes;
- recursive directory scans;
- checksum passes over bundle payloads;
- media sideload/download work;
- large product/page/term queries;
- retry loops;
- synchronous verification of the complete migration payload.

The migration page itself is a lightweight status/action shell.

## 3. AJAX transport

Operational migration work from the UI uses authenticated WordPress admin AJAX.

Rules:

- use `wp_ajax_*`, never `wp_ajax_nopriv_*`;
- every state-changing request requires an explicit least-privilege capability and nonce;
- validate the expected bundle identity/state again inside the AJAX handler;
- return structured success/error state suitable for accessible UI feedback;
- do not poll aggressively or run background loops merely because the screen is open;
- heavy work starts only from an explicit authorized user action;
- repeated clicks/refreshes must be protected by migration state/lock semantics;
- frontend requests never load or execute migration work.

## 4. Loading strategy

Migration-specific JavaScript/CSS loads only on the temporary Graha migration child screen. It must not be registered as a global WordPress-admin behavior.

The screen should render useful static state first, then progressively enhance actions through AJAX. JavaScript failure must not cause an import to execute accidentally.

## 5. Batching boundary

Do not introduce a generic job queue or migration framework solely to make AJAX appear scalable.

If the real approved bundle proves too large for one bounded AJAX request, add only the smallest deterministic batch/checkpoint behavior required by that bundle and document the concrete need before implementation.

Do not invent batch sizes, payload schemas, or importer behavior before the real bundle exists.

## 6. Failure behavior

AJAX errors must leave the logical migration state inspectable and retry-safe. A timeout/network error must not be treated as proof that the import failed or succeeded; the next request must reconcile against persisted native state/source identities before continuing.

Cleanup is attempted only after verified logical consumption. Cleanup failure must not cause another import attempt.

## 7. Verification

When migration runtime exists, repository/runtime checks must verify:

- no expensive migration operation is called from admin render/menu hooks;
- no `wp_ajax_nopriv_*` migration endpoint exists;
- migration AJAX handlers enforce capability + nonce;
- migration assets are screen-scoped;
- no automatic polling/import starts on page load;
- explicit user action is required for heavy validation/import;
- concurrent/repeated AJAX execution cannot duplicate successful native writes;
- frontend requests remain free of migration runtime work.
