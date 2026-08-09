<?php

define( 'ABSPATH', __DIR__ . '/' );

$GLOBALS['styles']  = array();
$GLOBALS['scripts'] = array();

function add_action() {}
function plugin_dir_url() { return 'https://example.test/plugin/'; }
function wp_register_style() {}
function wp_register_script() {}
function wp_enqueue_style( $handle ) { $GLOBALS['styles'][] = $handle; }
function wp_enqueue_script( $handle ) { $GLOBALS['scripts'][] = $handle; }
function get_nav_menu_locations() { return array(); }
function absint( $value ) { return abs( (int) $value ); }
function wp_get_nav_menu_items() { return array(); }
function register_nav_menus() {}
function __( $text ) { return $text; }
function esc_attr( $text ) { return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' ); }
function esc_attr__( $text ) { return esc_attr( $text ); }
function esc_html( $text ) { return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' ); }
function esc_html__( $text ) { return esc_html( $text ); }
function esc_url( $url ) {
	$url = trim( (string) $url );
	return 0 === stripos( $url, 'javascript:' ) ? '' : htmlspecialchars( $url, ENT_QUOTES, 'UTF-8' );
}
function sanitize_key( $key ) { return strtolower( preg_replace( '/[^a-z0-9_\-]/', '', (string) $key ) ); }
function wp_strip_all_tags( $text ) { return strip_tags( (string) $text ); }
function wp_kses_post( $html ) { return (string) $html; }
function get_bloginfo( $show ) { return 'name' === $show ? 'Situs Uji' : ''; }
function home_url( $path = '/' ) { return 'https://example.test' . $path; }
function get_custom_logo() { return ''; }

require_once dirname( __DIR__ ) . '/src/AssetService.php';
require_once dirname( __DIR__ ) . '/src/NavigationService.php';
require_once dirname( __DIR__ ) . '/src/TemplateService.php';

function assert_true( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, "FAIL: {$message}\n" );
		exit( 1 );
	}
	echo "PASS: {$message}\n";
}

$assets     = new \GrahaSelang\AssetService( dirname( __DIR__ ) . '/graha-selang.php', '0.3.0' );
$navigation = new \GrahaSelang\NavigationService();
$templates  = new \GrahaSelang\TemplateService( $assets, $navigation );

$inner = $templates->render_page(
	'about',
	array(
		'heading'      => 'Tentang',
		'content_html' => '<p>Konten asli dari pemilik native.</p>',
	)
);

assert_true( false !== strpos( $inner, '<header class="graha-site-header">' ), 'semantic shell includes header' );
assert_true( false !== strpos( $inner, '<main id="graha-main"' ), 'semantic shell includes one main landmark' );
assert_true( false !== strpos( $inner, '<footer class="graha-site-footer">' ), 'semantic shell includes footer' );
assert_true( 1 === substr_count( $inner, '<h1>' ), 'rendered family contains exactly one intended H1' );
assert_true( false !== strpos( $inner, 'Lewati ke konten utama' ), 'shell includes Indonesian skip link' );
assert_true( false !== strpos( $inner, '>Beranda</a>' ), 'non-home breadcrumb includes native Home anchor' );
assert_true( false !== strpos( $inner, 'aria-current="page">Tentang</span>' ), 'breadcrumb identifies current page' );
assert_true( false === stripos( $inner, 'application/ld+json' ), 'visible breadcrumbs do not emit schema' );
assert_true( false !== strpos( $inner, 'aria-label="Jejak navigasi"' ), 'breadcrumb accessibility label is Indonesian' );

$families = array(
	'product_archive',
	'product_category',
	'product_single',
	'application',
	'brand',
	'about',
	'service',
	'technical_rfq',
	'article',
	'legal',
	'search',
	'not_found',
);
foreach ( $families as $family ) {
	$prototype = $templates->render_page(
		$family,
		array(
			'heading'      => 'Judul Uji',
			'content_html' => '<p>Konten uji eksplisit.</p>',
		)
	);
	assert_true( '' !== $prototype, "family prototype renders explicitly: {$family}" );
	assert_true( 1 === substr_count( $prototype, '<h1>' ), "family prototype preserves one H1: {$family}" );
}

$three_sections = array(
	array( 'heading' => 'Satu', 'content_html' => '<p>A</p>' ),
	array( 'heading' => 'Dua', 'content_html' => '<p>B</p>' ),
	array( 'heading' => 'Tiga', 'content_html' => '<p>C</p>' ),
);
assert_true( '' === $templates->render_page( 'home', array( 'heading' => 'Beranda', 'sections' => $three_sections ) ), 'Home refuses fewer than four substantial sections' );

$four_sections   = $three_sections;
$four_sections[] = array( 'heading' => 'Empat', 'content_html' => '<p>D</p>' );
$home            = $templates->render_page( 'home', array( 'heading' => 'Beranda', 'sections' => $four_sections ) );
assert_true( 4 === substr_count( $home, '<section class="graha-page-section">' ), 'Home prototype renders four explicit substantial sections' );
assert_true( false === strpos( $home, 'graha-breadcrumbs' ), 'Home does not render breadcrumbs' );

$templates->prepare_page( 'about' );
assert_true(
	array(
		'graha-selang-tokens',
		'graha-selang-foundation',
		'graha-selang-navigation',
		'graha-selang-shell',
	) === $GLOBALS['styles'],
	'shell preparation enqueues only the centralized style chain'
);
assert_true( array( 'graha-selang-navigation' ) === $GLOBALS['scripts'], 'shell preparation enqueues only navigation JavaScript' );
