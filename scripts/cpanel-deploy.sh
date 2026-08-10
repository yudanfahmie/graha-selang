#!/bin/sh
# Graha Selang cPanel deployment task.
#
# Run by cPanel Git Version Control's "Deploy HEAD Commit" (see .cpanel.yml),
# with the current working directory already set to the repository checkout
# root. See docs/cpanel-deployment-integrity.md for the full contract this
# script implements and why the interrupt-safety timing below matters.
set -eu

PLUGINROOT=/home/markascl/graha-selang.markas.cloud/wp-content/plugins
SOURCE=plugin/graha-selang-site-core
DEPLOYPATH=$PLUGINROOT/graha-selang-site-core
STAGEPATH=$PLUGINROOT/.graha-selang-site-core.deploy
BACKUPPATH=$PLUGINROOT/.graha-selang-site-core.previous

rollback() {
	status=${1:-1}
	echo "graha-selang deploy ROLLBACK (status=$status), restoring previous plugin directory" >&2
	/bin/rm -rf "$DEPLOYPATH" "$STAGEPATH"
	if [ -d "$BACKUPPATH" ]; then /bin/mv "$BACKUPPATH" "$DEPLOYPATH"; fi
	exit "$status"
}

if [ ! -d "$SOURCE" ]; then
	echo "graha-selang deploy FAIL, source path $SOURCE not found" >&2
	exit 1
fi

echo "graha-selang deploy staging $SOURCE"
/bin/rm -rf "$STAGEPATH" "$BACKUPPATH"
/bin/mkdir -p "$STAGEPATH"
/bin/cp -R "$SOURCE"/. "$STAGEPATH"/

echo "graha-selang deploy verifying staged copy matches source"
/usr/bin/diff -qr "$SOURCE" "$STAGEPATH" >/dev/null

command -v php >/dev/null 2>&1
echo "graha-selang deploy linting staged PHP"
php -l "$STAGEPATH/src/ProductPresentation.php" >/dev/null

# The rollback trap is armed only from here on. Staging above (the tree copy,
# the diff, the lint) runs unguarded on purpose: nothing under $DEPLOYPATH has
# been touched yet, so an interrupt during staging can safely just abort --
# rollback() unconditionally rm -rf's $DEPLOYPATH before restoring
# $BACKUPPATH, and $BACKUPPATH does not exist until the first mv below, so
# arming this any earlier would delete a still-good live directory with
# nothing to restore it from.
echo "graha-selang deploy swapping live plugin directory"
trap 'rollback 1' HUP INT TERM
if [ -d "$DEPLOYPATH" ]; then /bin/mv "$DEPLOYPATH" "$BACKUPPATH"; fi
if ! /bin/mv "$STAGEPATH" "$DEPLOYPATH"; then rollback 1; fi

echo "graha-selang deploy verifying live deployment integrity"
if ! /usr/bin/diff -qr "$SOURCE" "$DEPLOYPATH" >/dev/null || ! php -l "$DEPLOYPATH/src/ProductPresentation.php" >/dev/null; then
	rollback 1
fi

/bin/rm -rf "$BACKUPPATH"
trap - HUP INT TERM
echo "graha-selang deploy complete"
