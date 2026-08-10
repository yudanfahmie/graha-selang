<?php
/** Static regression guard for the 0.7.2 frontend stabilization contracts. */
$root = dirname( __DIR__ );
$plugin = $root . '/plugin/graha-selang-site-core';

function ok( $condition, $message ) {
	if ( ! $condition ) { fwrite( STDERR, "FAIL: {$message}\n" ); exit( 1 ); }
	echo "PASS: {$message}\n";
}
function content( $path ) { return (string) file_get_contents( $path ); }

$tokens = content( $plugin . '/assets/css/tokens.css' );
$foundation = content( $plugin . '/assets/css/foundation.css' );
$shell = content( $plugin . '/assets/css/shell.css' );
$navigation = content( $plugin . '/assets/css/navigation.css' );
$assets = content( $plugin . '/src/AssetService.php' );
$product = content( $plugin . '/src/ProductPresentation.php' );
$header = content( $plugin . '/templates/parts/header.php' );
$entry = content( $plugin . '/graha-selang.php' );
$kernel = content( $plugin . '/src/Kernel.php' );
$contributing = content( $root . '/CONTRIBUTING.md' );
$admin = content( $plugin . '/src/AdminService.php' );

ok( false !== strpos( $tokens, '--graha-content-narrow: 50rem' ), 'narrow readable content is 50rem' );
ok( false !== strpos( $tokens, '--graha-content-max: 80rem' ), 'default content limit is 80rem' );
ok( false !== strpos( $tokens, '--graha-content-wide: 90rem' ), 'wide content limit is 90rem' );
ok( false !== strpos( str_replace( ' ', '', $foundation ), '--graha-container-limit:var(--graha-content-wide)' ), 'wide container overrides the shared container limit' );
ok( false !== strpos( $foundation, 'var(--graha-container-limit,var(--graha-content-max))' ) || false !== strpos( $foundation, 'var(--graha-container-limit, var(--graha-content-max))' ), 'container primitive consumes a modifier-controlled limit' );
ok( false === strpos( $foundation, '.graha-container--wide { max-inline-size:' ), 'wide modifier is no longer trapped below the default inline-size cap' );

ok( false !== strpos( $foundation, '.graha-ui :where(a)' ), 'generic anchor default is low specificity' );
ok( false !== strpos( $foundation, '.graha-button--primary:visited' ), 'primary button owns visited text state' );
ok( false !== strpos( $foundation, '.graha-button--primary:focus-visible' ), 'primary button owns focus-visible text state' );
ok( false !== strpos( $foundation, 'color:var(--graha-color-on-primary)' ), 'primary button retains high-contrast on-primary text' );

ok( false !== strpos( $assets, 'graha-selang-logo-text.svg' ), 'AssetService owns canonical wordmark asset' );
ok( false === strpos( $assets . $header, 'graha-selang-brand-logo.svg' ), 'obsolete brand-logo path is removed from presentation runtime' );
ok( false !== strpos( $header, 'graha-site-identity__logo' ) && false === strpos( $header, 'custom-logo' ), 'header renders deterministic plugin-owned wordmark rather than Custom Logo' );
ok( false !== strpos( $assets, "remove_action( 'wp_head', 'wp_site_icon', 99 )" ), 'Graha favicon removes competing WordPress Site Icon output' );
foreach ( array( 'graha-selang-logo-text.svg', 'graha-selang-logo.svg', 'favicon.ico', 'favicon-16x16.png', 'favicon-32x32.png', 'apple-touch-icon.png', 'icon-192.png', 'icon-512.png' ) as $name ) {
	ok( is_file( $plugin . '/assets/images/' . $name ), "canonical image exists: {$name}" );
}

ok( false !== strpos( $product, "return 'product_archive'" ), 'product presentation identifies product archive view' );
ok( false !== strpos( $product, "return 'product_single'" ), 'product presentation identifies product single view' );
ok( false !== strpos( $product, "return 'product_category'" ), 'product presentation identifies category taxonomy view' );
ok( false !== strpos( $product, "return 'brand'" ), 'product presentation identifies brand taxonomy view' );
ok( false !== strpos( $product, "archive_template" ) && false !== strpos( $product, "single_template" ) && false !== strpos( $product, "taxonomy_template" ), 'native template-loader filters own the four product route families without custom routing' );
ok( is_file( $plugin . '/templates/product.php' ), 'Graha-owned product document template exists' );
ok( false !== strpos( $product, 'graha-product-grid' ), 'server-rendered product archive grid is present' );
ok( false !== strpos( $product, 'graha-catalog-empty' ), 'branded product archive empty state is present' );
ok( false !== strpos( $product, 'get_the_excerpt' ) && false !== strpos( $product, 'get_the_post_thumbnail' ), 'product cards consume only existing native content when available' );
ok( false === strpos( $product, 'wp_insert_post' ) && false === strpos( $product, 'wp_update_post' ) && false === strpos( $product, 'wp_set_object_terms' ), 'product presentation performs no product or taxonomy mutation' );

ok( false !== strpos( $navigation, '@media (max-width: 68rem)' ), 'navigation collapses before desktop composition collision' );
ok( false !== strpos( $shell, 'body.admin-bar .graha-site-header' ), 'sticky header accounts for logged-in WordPress admin bar' );
ok( false !== strpos( $shell, '.graha-product-grid' ), 'product collection has branded shell styling' );

ok( false !== strpos( $entry, 'Version: 0.7.9' ), 'plugin header version is 0.7.9' );
ok( false !== strpos( $kernel, "const VERSION = '0.7.9'" ), 'Kernel version is 0.7.9' );
ok( false !== strpos( $entry, "ProductPresentation.php" ) && false !== strpos( $kernel, 'new ProductPresentation' ), 'product presentation collaborator is wired by the composition root' );
ok( false !== strpos( $contributing, 'owns registration of native Graha product entities' ), 'CONTRIBUTING documents native Graha product ownership' );
ok( false === strpos( $contributing, 'WooCommerce remains commerce/product authority' ), 'CONTRIBUTING no longer claims WooCommerce product authority' );

ok( false !== strpos( $admin, 'Logo header kanonis' ) && false !== strpos( $admin, 'Favicon kanonis' ), 'Ringkasan exposes canonical brand asset readiness' );
ok( false !== strpos( $admin, 'Presentasi katalog produk' ) && false !== strpos( $admin, 'product_presentation_active' ), 'Ringkasan exposes product presentation ownership readiness' );
ok( false !== strpos( $assets, 'canonical_asset_status' ), 'AssetService provides narrow canonical asset readiness status' );

echo "Frontend stabilization checks passed.\n";
