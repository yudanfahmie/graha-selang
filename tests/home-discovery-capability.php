<?php
/** Phase D2 Home discovery/capability regression contract. */
$root   = dirname( __DIR__ );
$plugin = $root . '/plugin/graha-selang-site-core';

function ok( $condition, $message ) {
	if ( ! $condition ) { fwrite( STDERR, "FAIL: {$message}\n" ); exit( 1 ); }
	echo "PASS: {$message}\n";
}
function text( $path ) { return (string) file_get_contents( $path ); }

$content   = text( $plugin . '/templates/home/content.php' );
$bridge    = text( $plugin . '/templates/native-home.php' );
$hero      = text( $plugin . '/templates/parts/home-hero.php' );
$home_css  = text( $plugin . '/assets/css/home.css' );
$entry     = text( $plugin . '/graha-selang.php' );
$kernel    = text( $plugin . '/src/Kernel.php' );

ok( false !== strpos( $content, 'graha-home-capability' ) && false !== strpos( $content, "if ( '' !== \$services_url )" ), 'technical capability renders only with a real Services destination' );
ok( false !== strpos( $bridge, "illustration_url( 'technical-services.svg' )" ), 'technical-services.svg is resolved through the existing illustration owner' );
ok( false !== strpos( $content, 'graha-home-capability__visual' ) && false !== strpos( $content, 'width="640" height="480" loading="lazy" decoding="async"' ), 'technical-services illustration is below-fold, dimensioned and lazy' );
foreach ( array( 'Crimping & assembly', 'Custom fitting, coupling & flange', 'Konsultasi pemilihan produk' ) as $capability ) {
	ok( false !== strpos( $content, $capability ), "approved capability wording remains present: {$capability}" );
}

ok( false !== strpos( $content, 'graha-home-discovery' ) && false !== strpos( $content, 'graha-home-pathways' ), 'D2 discovery pathway section exists' );
ok( false !== strpos( $content, '$products_url' ) && false !== strpos( $content, '$rfq_url' ), 'discovery uses the real Product and RFQ destinations already prepared for Home' );
foreach ( array( '/applications/', '/industry/', '/brands/' ) as $invented_route ) {
	ok( false === strpos( $content . $bridge, $invented_route ), "no synthetic discovery route is introduced: {$invented_route}" );
}
ok( false === stripos( $content, 'coming soon' ) && false === stripos( $content, 'segera hadir' ), 'D2 renders no coming-soon discovery filler' );

foreach ( array( 'produk native', 'production surface', 'Home group', 'current repository', 'route availability', 'saat ini dipublikasikan Graha Selang' ) as $internal_copy ) {
	ok( false === stripos( $content, $internal_copy ), "public Home copy hides implementation vocabulary: {$internal_copy}" );
}
foreach ( array( 'terbaik', 'nomor satu', 'terlengkap', 'premium quality', 'world class', 'certified' ) as $unsupported_superlative ) {
	ok( false === stripos( $content, $unsupported_superlative ), "public Home copy avoids unsupported superlative: {$unsupported_superlative}" );
}

ok( false !== strpos( $content, 'graha-home-trust' ) && false !== strpos( $content, '$about_url' ) && false !== strpos( $content, '$services_url' ), 'trust layer is qualitative and driven by existing public destinations' );
ok( false !== strpos( $content, 'graha-home-closing' ) && false !== strpos( $content, '$closing_primary_url' ), 'closing journey is destination-gated' );
ok( false !== strpos( $content, "if ( '' !== \$rfq_url )" ) && false !== strpos( $content, "elseif ( '' !== \$contact_url )" ) && false !== strpos( $content, "elseif ( '' !== \$products_url )" ), 'closing prioritizes RFQ then real Contact/Product fallbacks' );
foreach ( array( 'tel:', 'mailto:', 'whatsapp', 'wa.me', '@graha', 'Jl.' ) as $contact_fact ) {
	ok( false === stripos( $content, $contact_fact ), "closing invents no direct contact fact: {$contact_fact}" );
}

foreach ( array( 'template_redirect', 'add_rewrite_rule', 'wp_redirect', 'wp_safe_redirect' ) as $route_primitive ) {
	ok( false === strpos( $content . $bridge, $route_primitive ), "D2 creates no route/redirect owner: {$route_primitive}" );
}
foreach ( array( 'wp_insert_post', 'wp_update_post', 'wp_set_object_terms', 'update_post_meta', 'add_post_meta', 'delete_post_meta' ) as $mutation ) {
	ok( false === strpos( $content, $mutation ), "D2 performs no product/taxonomy mutation: {$mutation}" );
}

foreach ( array( 'graha-home-orientation', 'graha-home-anchors', 'graha-home-support', 'graha-home-specialist' ) as $d1_marker ) {
	ok( false !== strpos( $content, $d1_marker ), "D1 hierarchy remains intact: {$d1_marker}" );
}
ok( 1 === substr_count( $hero, '<h1' ) && 0 === substr_count( $content, '<h1' ), 'Hero remains the sole intended H1 owner' );
ok( false !== strpos( $entry, 'Version: 0.7.8' ) && false !== strpos( $kernel, "const VERSION = '0.7.8'" ), 'D2 release version is synchronized at 0.7.8' );
ok( 0 === preg_match( '/https?:\/\//', $home_css ) && false === strpos( $home_css, '@import' ), 'D2 Home CSS has no external frontend dependency' );
ok( false === stripos( $home_css, 'animation' ) && false === stripos( $home_css, 'parallax' ), 'D2 adds no animation framework or parallax behavior' );

echo "Home discovery and capability checks passed.\n";
