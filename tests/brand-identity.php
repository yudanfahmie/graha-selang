<?php
/** Canonical Graha wordmark and favicon ownership regression checks. */
define( 'ABSPATH', __DIR__ . '/' );
$GLOBALS['removed_actions'] = array();

function add_action() {}
function remove_action( $hook, $callback, $priority = 10 ) { $GLOBALS['removed_actions'][] = array( $hook, $callback, $priority ); }
function plugin_dir_url() { return 'https://example.test/plugin/'; }
function plugin_dir_path( $file ) { return dirname( $file ) . '/'; }
function esc_attr( $text ) { return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' ); }
function esc_url( $url ) { return htmlspecialchars( trim( (string) $url ), ENT_QUOTES, 'UTF-8' ); }

require_once dirname( __DIR__ ) . '/plugin/graha-selang-site-core/src/AssetService.php';

function ok( $condition, $message ) {
	if ( ! $condition ) { fwrite( STDERR, "FAIL: {$message}\n" ); exit( 1 ); }
	echo "PASS: {$message}\n";
}

$plugin_file = dirname( __DIR__ ) . '/plugin/graha-selang-site-core/graha-selang.php';
$assets = new \GrahaSelang\AssetService( $plugin_file, '0.7.2' );
$header = (string) file_get_contents( dirname( $plugin_file ) . '/templates/parts/header.php' );

ok( 'https://example.test/plugin/assets/images/graha-selang-logo-text.svg' === $assets->canonical_wordmark_url(), 'canonical header wordmark resolves to logo-text SVG' );
ok( $assets->canonical_wordmark_url() === $assets->bundled_logo_url(), 'legacy helper aliases the canonical wordmark without stale path' );
ok( 'https://example.test/plugin/assets/images/graha-selang-logo.svg' === $assets->canonical_mark_url(), 'canonical icon source resolves to logo mark SVG' );
ok( false === strpos( $header, 'get_custom_logo' ) && false === strpos( $header, 'custom-logo' ), 'WordPress Custom Logo cannot outrank the plugin-owned Graha wordmark' );
ok( false !== strpos( $header, 'graha-site-identity__logo' ), 'header renders the dedicated canonical wordmark image class' );
ok( false === strpos( $header, 'graha-selang-brand-logo.svg' ), 'obsolete brand-logo filename is absent from header presentation runtime' );

ob_start();
$assets->render_canonical_favicons();
$favicons = (string) ob_get_clean();
ok( in_array( array( 'wp_head', 'wp_site_icon', 99 ), $GLOBALS['removed_actions'], true ), 'canonical favicon owner removes competing WordPress Site Icon output' );
foreach ( array( 'graha-selang-logo.svg', 'favicon.ico', 'favicon-16x16.png', 'favicon-32x32.png', 'apple-touch-icon.png', 'icon-192.png', 'icon-512.png' ) as $asset ) {
	ok( false !== strpos( $favicons, $asset ), "favicon output references {$asset}" );
	ok( file_exists( dirname( $plugin_file ) . '/assets/images/' . $asset ), "canonical favicon asset exists: {$asset}" );
}

echo "Brand identity checks passed.\n";
