# cPanel Deployment Integrity

## Purpose

This document records the production deployment integrity contract for the Graha Selang plugin. It is intentionally non-runtime documentation and exists so deployment behavior is independently visible in normal repository history, not only in `.cpanel.yml`.

## Canonical paths

- Repository source: `plugin/graha-selang-site-core/`
- Production destination: `/home/markascl/graha-selang.markas.cloud/wp-content/plugins/graha-selang-site-core/`

No other plugin source or destination is authoritative for this deployment.

## Deployment contract

A production deployment must:

1. start from the currently checked-out repository HEAD;
2. copy only the canonical plugin source into a clean staging directory;
3. verify the staged tree matches the repository source;
4. lint `src/ProductPresentation.php` before activation of the staged tree;
5. replace the destination as one coherent directory tree rather than overlaying files into an unknown existing tree;
6. verify the deployed tree still matches the repository source after replacement;
7. lint the deployed `src/ProductPresentation.php` again;
8. rollback to the previous plugin directory if post-swap verification fails;
9. remove temporary staging/backup directories after a successful deployment.

## cPanel operator verification

For pull-based cPanel Git deployment, a GitHub push alone is not production verification. Before considering a release deployed, confirm in cPanel Git Version Control that:

- `Currently Checked-Out Branch` is `main`;
- `HEAD Commit` matches the intended GitHub `main` SHA after **Update from Remote**;
- **Deploy HEAD Commit** completes successfully;
- `Last Deployed SHA` matches that same intended SHA.

Repository CI success proves repository integrity only. It does not prove the production filesystem has been updated until the cPanel deployment state is verified.
