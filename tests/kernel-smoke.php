<?php

define( 'ABSPATH', __DIR__ . '/' );
$GLOBALS['hooks'] = array();

function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) {
	$GLOBALS['hooks'][] = $hook;
}
function plugin_dir_url( $file ) { return 'https://example.test/plugin/'; }

require_once dirname( __DIR__ ) . '/src/AssetService.php';
require_once dirname( __DIR__ ) . '/src/NavigationService.php';
require_once dirname( __DIR__ ) . '/src/TemplateService.php';
require_once dirname( __DIR__ ) . '/src/AdminService.php';
require_once dirname( __DIR__ ) . '/src/Kernel.php';

$kernel = new \GrahaSelang\Kernel( dirname( __DIR__ ) . '/graha-selang.php', '0.3.0' );
$kernel->boot();

sort( $GLOBALS['hooks'] );
$expected = array(
	'admin_enqueue_scripts',
	'admin_menu',
	'after_setup_theme',
	'graha_selang_prepare_page',
	'graha_selang_render_breadcrumbs',
	'graha_selang_render_page',
	'wp_enqueue_scripts',
);
sort( $expected );

if ( $expected !== $GLOBALS['hooks'] ) {
	fwrite( STDERR, 'FAIL: unexpected registered hooks: ' . implode( ', ', $GLOBALS['hooks'] ) . "\n" );
	exit( 1 );
}

echo "PASS: Kernel boots exactly the expected safe Wave 1 hooks\n";
