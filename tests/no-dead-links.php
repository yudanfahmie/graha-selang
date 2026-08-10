<?php
/**
 * Static presentation-dead-end guard: every plugin-owned public template
 * must never emit a placeholder "#" href or a javascript: pseudo-link.
 * Real-destination omission is handled entirely at the PHP composition
 * layer (graha_render_discovery_grid()/graha_render_button() drop any door
 * without a live URL); this test only proves no template hand-authors a
 * dead anchor as a substitute for that omission.
 */

$root = dirname( __DIR__ ) . '/plugin/graha-selang-site-core/templates';

function ok( $condition, $message ) { if ( ! $condition ) { fwrite( STDERR, "FAIL: {$message}\n" ); exit( 1 ); } echo "PASS: {$message}\n"; }

$iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $root, FilesystemIterator::SKIP_DOTS ) );
$checked = 0;
foreach ( $iterator as $file ) {
	if ( 'php' !== strtolower( $file->getExtension() ) ) continue;
	$path = (string) $file->getPathname();
	$text = (string) file_get_contents( $path );
	$relative = substr( $path, strlen( dirname( __DIR__ ) ) + 1 );
	foreach ( array( 'href="#"', "href='#'", 'href="javascript:', "href='javascript:" ) as $dead ) {
		ok( false === stripos( $text, $dead ), "no dead/placeholder link in {$relative}: {$dead}" );
	}
	$checked++;
}
ok( $checked >= 8, 'the dead-link scan actually covered the plugin template tree (' . $checked . ' files)' );

echo "No dead-link presentation checks passed.\n";
