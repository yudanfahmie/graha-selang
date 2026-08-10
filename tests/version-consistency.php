<?php
/**
 * Guards the single-source-of-truth version pattern: Kernel::VERSION is
 * the one runtime constant every asset-cache-busting consumer reads, and
 * the mandatory WordPress plugin-header docblock literal must match it so
 * a real release can never silently drift the two apart.
 */

define( 'ABSPATH', __DIR__ . '/' );

$GLOBALS['registered'] = array();
$GLOBALS['hooked']     = array();

function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
	$GLOBALS['hooked'][ $hook ][] = $callback;
}
function add_filter() {}
function plugin_dir_url() { return 'https://example.test/plugin/'; }
function wp_register_style( $handle, $src, $deps, $ver ) { $GLOBALS['registered'][ $handle ] = $ver; }
function wp_register_script( $handle, $src, $deps, $ver, $in_footer = false ) { $GLOBALS['registered'][ $handle ] = $ver; }
function wp_enqueue_style() {}
function wp_enqueue_script() {}
function is_admin() { return false; }
function is_front_page() { return false; }
function is_singular( $types = null ) { return false; }

require_once dirname( __DIR__ ) . '/plugin/graha-selang-site-core/src/AssetService.php';
require_once dirname( __DIR__ ) . '/plugin/graha-selang-site-core/src/ProductContentService.php';
require_once dirname( __DIR__ ) . '/plugin/graha-selang-site-core/src/NavigationService.php';
require_once dirname( __DIR__ ) . '/plugin/graha-selang-site-core/src/TemplateService.php';
require_once dirname( __DIR__ ) . '/plugin/graha-selang-site-core/src/ProductPresentation.php';
require_once dirname( __DIR__ ) . '/plugin/graha-selang-site-core/src/AdminService.php';
require_once dirname( __DIR__ ) . '/plugin/graha-selang-site-core/src/SiteLifecycleService.php';
require_once dirname( __DIR__ ) . '/plugin/graha-selang-site-core/src/Kernel.php';

function ok( $condition, $message ) { if ( ! $condition ) { fwrite( STDERR, "FAIL: {$message}\n" ); exit( 1 ); } echo "PASS: {$message}\n"; }

$plugin_file = dirname( __DIR__ ) . '/plugin/graha-selang-site-core/graha-selang.php';
$entry = file_get_contents( $plugin_file );

ok( 1 === preg_match( '/^\s*\*\s*Version:\s*([0-9]+\.[0-9]+\.[0-9]+)\s*$/m', $entry, $m ), 'plugin header declares a semantic Version line' );
$header_version = $m[1];

ok( \GrahaSelang\Kernel::VERSION === $header_version, 'plugin header Version matches Kernel::VERSION (no drift between the two required literals)' );
ok( '0.6.1' !== \GrahaSelang\Kernel::VERSION, 'version was actually bumped past the pre-frontend-rewrite baseline' );
ok( 0 === preg_match( '/Kernel\(\s*__FILE__\s*,/', $entry ), 'Kernel is instantiated without a second hand-duplicated version literal' );

// Boot through the real production wiring (graha-selang.php's own construction path), then
// fire the actually-registered wp_enqueue_scripts callback -- proving the live AssetService
// instance the Kernel built really did receive Kernel::VERSION, not a copy/paste literal.
$kernel = new \GrahaSelang\Kernel( $plugin_file );
$kernel->boot();
ok( ! empty( $GLOBALS['hooked']['wp_enqueue_scripts'] ), 'booted Kernel registers the public asset enqueue hook' );
foreach ( $GLOBALS['hooked']['wp_enqueue_scripts'] as $callback ) {
	call_user_func( $callback );
}

$expected_version = \GrahaSelang\Kernel::VERSION;
foreach ( array( 'graha-selang-tokens', 'graha-selang-foundation', 'graha-selang-navigation', 'graha-selang-shell', 'graha-selang-home' ) as $handle ) {
	ok( isset( $GLOBALS['registered'][ $handle ] ) && $expected_version === $GLOBALS['registered'][ $handle ], "public asset '{$handle}' is registered with the live Kernel::VERSION ({$expected_version}), not a stale literal" );
}

echo "Version consistency checks passed.\n";
