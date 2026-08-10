# cPanel Deployment

## Canonical paths

- Repository source: `plugin/graha-selang-site-core/`
- Production destination: `/home/markascl/graha-selang.markas.cloud/wp-content/plugins/graha-selang-site-core/`

## Deployment rule

Keep `.cpanel.yml` intentionally simple and compatible with cPanel's documented task format. Deployment removes the existing Graha plugin directory, recreates it, then copies only `plugin/graha-selang-site-core/.` into that destination.

Do not add deployment logic to the plugin runtime and do not deploy any other repository directory into WordPress.

## Pull versus automatic deployment

The cPanel repository at `/home/markascl/repositories/graha-selang` is a pull-style clone of GitHub. In this mode, cPanel does not automatically pull and deploy merely because GitHub `main` receives a push. Manual operation is **Update from Remote** followed by **Deploy HEAD Commit** unless a separate automation invokes the cPanel update/deployment API or the repository is converted to cPanel push deployment.

Repository CI proves repository integrity only; production is verified only when cPanel has updated and deployed the intended SHA.
