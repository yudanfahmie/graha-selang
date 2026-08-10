# cPanel Deployment Integrity

## Purpose

This document records the production deployment integrity contract for the Graha Selang plugin. It is intentionally non-runtime documentation and exists so deployment behavior is independently visible in normal repository history, not only in `.cpanel.yml`.

## Canonical paths

- Repository source: `plugin/graha-selang-site-core/`
- Production destination: `/home/markascl/graha-selang.markas.cloud/wp-content/plugins/graha-selang-site-core/`
- Deployment task implementation: `scripts/cpanel-deploy.sh` (run by `.cpanel.yml` with cwd already set to the repository checkout root, per cPanel's documented deployment behavior)

No other plugin source or destination is authoritative for this deployment.

`.cpanel.yml` itself intentionally stays a one-line task (`/bin/sh scripts/cpanel-deploy.sh`) rather than an inline shell one-liner. cPanel's own `.cpanel.yml` YAML parsing is far stricter than the YAML spec generally allows (it has repeatedly failed to deploy an inline task containing routine POSIX single-quote-escaping and message text with a colon-space, both of which are valid YAML and valid shell on their own) -- keeping deployment logic in a real `.sh` file removes that entire class of failure and makes the logic readable/diffable on its own.

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

Repository CI success proves repository integrity only. It does not prove the production filesystem has been updated until the cPanel deployment state is verified. `.github/workflows/verify.yml` runs repository tests on every push to `main`; it never touches the cPanel host, so a green GitHub Actions run and a deployed production site are two independent facts.

## Interrupt-safety hardening

The one-shot `trap ... HUP INT TERM` rollback guard is only armed immediately before the live-directory swap (the two `mv` operations), not for the entire task. Staging (`cp -R` of the full plugin tree, the pre-swap `diff`, the pre-swap `php -l`) runs unguarded first: if the task is interrupted during that phase, nothing under `$DEPLOYPATH` has been touched yet, so the process simply exits and the next deployment attempt cleans up the leftover stage directory. Arming the trap earlier was tried and found unsafe: `rollback()` unconditionally does `rm -rf "$DEPLOYPATH"` before restoring `$BACKUPPATH`, and during staging `$BACKUPPATH` does not exist yet — an interrupt in that window would have deleted the still-good live plugin directory with nothing to restore it from. Each `echo` line in the task is a deployment-log checkpoint so a stalled or interrupted run is diagnosable from cPanel's task output alone.
