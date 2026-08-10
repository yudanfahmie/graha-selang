# Owner Decision Matrix

This document lists only the items that genuinely require an owner or
content decision before Graha Selang can move past this release-candidate
state. Everything here is a business/content input, not an engineering
question -- the site is technically ready to receive each answer.

## A. Application pages

- Exact retained URLs/slugs for the 4 baseline application intents (the
  original brief lists mining, cement/bulk material, marine, dredging/
  slurry, drilling, oil & gas, MRO/plant maintenance, and CNG/high-pressure
  gas as candidate themes -- only 4 are retained, and which 4 depends on a
  fresh crawl of the live site).
- Final page titles for each retained application.
- Approved copy and media for each (proof, relevant products, specification
  notes).

## B. Contact

- Official company address.
- Phone number(s).
- Email address(es).
- WhatsApp number, if a WhatsApp contact channel is wanted.
- Map/location embed or link.

## C. Request Quote (RFQ)

- Approved form provider (the Page is ready to host whichever provider is
  chosen; no provider is currently wired).
- Recipient/routing rules -- who receives a submission, and how buyer vs.
  reseller/cooperation inquiries should be distinguished.
- Attachment/upload policy: allowed file types, size, count, and retention.
- Privacy/consent wording required for the form and any file upload.
- Whether a WhatsApp fallback should sit alongside the form.

## D. Brand

- Which brands Graha Selang is authorized to present (MORGEN, HAMMER,
  SUNFLEX, and any others), and the evidence backing that authorization.
- Which product records belong to which brand (native taxonomy membership).
- Whether brand discovery should be a prominent Home/navigation entry or a
  quieter catalog-only presence.

## E. Articles

- Whether an Articles Hub is required at all for launch.
- If required, the canonical permalink strategy (`/articles/` vs. an
  existing structure) and whether any existing `/blog/{slug}/` content must
  be preserved as-is.
- Who owns the first batch of editorial content.

## F. Legal

- Whether a Privacy Policy is required, and its approved content.
- Whether Terms of Service/Use are required, and their approved content.
- Whether cookie/consent UI is legally required for the current tracking
  stack. (The technical Search and 404 utility routes on this release do
  not set cookies themselves.)

## G. Legacy URL reconciliation

- A fresh crawl of the live site is required before finalizing:
  - the 5 merge/301 redirect decisions (closest retained destination for
    each);
  - the 1 retire decision (return an intentional 404/410, never a blind
    redirect to Home).
- Until that crawl exists, no redirect matrix can be finalized without
  guessing, and guessing risks silently dropping indexed content.

---

Everything not listed above (Home, Products, Product Single/Category,
Brand routes, About, Services, Search, 404, generic Page presentation) is
implemented and does not require further owner input to remain in its
current, honest state.
