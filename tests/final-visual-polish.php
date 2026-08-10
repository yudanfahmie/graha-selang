<?php
/** Final visual-polish regression contract. */

define( 'ABSPATH', __DIR__ . '/' );

function __( $value ) { return $value; }
function esc_html__( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_html( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_url( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_attr( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }

function ok( $condition, $message ) {
	if ( ! $condition ) { fwrite( STDERR, "FAIL: {$message}\n" ); exit( 1 ); }
	echo "PASS: {$message}\n";
}

function render_footer_for_family( $family ) {
	$site_name = 'Graha Selang';
	$footer_links = array(
		'products_url' => 'https://example.test/products/',
		'services_url' => 'https://example.test/layanan-kami/',
		'about_url' => 'https://example.test/about-us/',
		'rfq_url' => 'https://example.test/request-quote/',
		'contact_url' => 'https://example.test/contact-us/',
	);
	ob_start();
	include dirname( __DIR__ ) . '/plugin/graha-selang-site-core/templates/parts/footer.php';
	return (string) ob_get_clean();
}

$root = dirname( __DIR__ );
$plugin = $root . '/plugin/graha-selang-site-core';
$home_css = (string) file_get_contents( $plugin . '/assets/css/home.css' );
$shell_css = (string) file_get_contents( $plugin . '/assets/css/shell.css' );
$header = (string) file_get_contents( $plugin . '/templates/parts/header.php' );
$entry = (string) file_get_contents( $plugin . '/graha-selang.php' );
$kernel = (string) file_get_contents( $plugin . '/src/Kernel.php' );

ok( false !== strpos( $home_css, 'padding: var(--graha-space-8) 0;' ) && false === strpos( $home_css, 'padding: var(--graha-space-9) 0;' ), 'wide Hero no longer expands to major-section vertical padding' );
ok( false !== strpos( $home_css, 'inline-size: min(100%, 38rem);' ) && false !== strpos( $home_css, 'justify-self: end;' ), 'wide Hero illustration is capped and aligned instead of scaling with the full 90rem grid' );
ok( false !== strpos( $home_css, 'justify-self: center;' ) && false !== strpos( $home_css, 'margin-inline: auto;' ), 'stacked Home illustrations are centered consistently' );
ok( false !== strpos( $home_css, '.graha-home-pathway:focus-visible' ) && false !== strpos( $home_css, '@media (hover: hover)' ), 'Home pathway focus and hover polish remains input-aware' );

$home_footer = render_footer_for_family( 'home' );
$page_footer = render_footer_for_family( 'about' );
ok( false === strpos( $home_footer, '<div class="graha-footer__cta">' ), 'Home does not repeat its closing CTA immediately in the Footer' );
ok( false !== strpos( $page_footer, '<div class="graha-footer__cta">' ), 'non-Home pages retain the global Footer CTA' );
ok( false !== strpos( $home_footer, 'graha-footer__grid--3' ), 'Footer declares the three real rendered columns instead of reserving an empty fourth track' );
ok( false !== strpos( $shell_css, '.graha-footer__grid--1' ) && false !== strpos( $shell_css, '.graha-footer__grid--2' ) && false !== strpos( $shell_css, '.graha-footer__grid--3' ), 'Footer grid adapts to optional real destination columns' );
ok( false === strpos( $shell_css, '1.2fr repeat(3, minmax(0, 1fr))' ), 'obsolete four-track Footer layout is removed' );
ok( false !== strpos( $shell_css, '.graha-site-footer__bottom p' ) && false !== strpos( $shell_css, 'overflow-wrap: anywhere;' ), 'Footer rhythm and long technical labels are overflow-safe' );

ok( false !== strpos( $header, 'width="738"' ) && false !== strpos( $header, 'height="174"' ), 'canonical header wordmark reserves its intrinsic ratio to reduce CLS' );
ok( false !== strpos( $entry, 'Version: 0.7.8' ) && false !== strpos( $kernel, "const VERSION = '0.7.8'" ), 'final visual-polish release version is synchronized at 0.7.8' );

foreach ( array( 'template_redirect', 'add_rewrite_rule', 'wp_redirect', 'wp_safe_redirect', 'wp_insert_post', 'wp_update_post', 'wp_set_object_terms' ) as $forbidden ) {
	ok( false === strpos( $home_css . $shell_css . $header, $forbidden ), "visual polish introduces no route/data primitive: {$forbidden}" );
}

echo "Final visual-polish checks passed.\n";
