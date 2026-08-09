<?php

$path = dirname( __DIR__ ) . '/plugin/graha-selang-site-core/src/ProductCatalogMigration.php';
if ( ! is_file( $path ) ) {
	fwrite( STDERR, "FAIL: ProductCatalogMigration.php is unavailable\n" );
	exit( 1 );
}
$content = file_get_contents( $path );
$blob_sha = sha1( 'blob ' . strlen( $content ) . "\0" . $content );
if ( '8227bd880a9b0681475932d81e2067ad0903985a' !== $blob_sha ) {
	fwrite( STDERR, "FAIL: ProductCatalogMigration blob changed from 61eea576\n" );
	exit( 1 );
}
echo "PASS: ProductCatalogMigration blob remains unchanged from 61eea576\n";
