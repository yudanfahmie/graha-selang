<?php

define( 'ABSPATH', __DIR__ . '/' );
define( 'OBJECT', 'OBJECT' );

$GLOBALS['styles']  = array();
$GLOBALS['scripts'] = array();

function add_action() {}
function add_filter() {}
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
function esc_url( $url ) { $url = trim( (string) $url ); return 0 === stripos( $url, 'javascript:' ) ? '' : htmlspecialchars( $url, ENT_QUOTES, 'UTF-8' ); }
function sanitize_key( $key ) { return strtolower( preg_replace( '/[^a-z0-9_\-]/', '', (string) $key ) ); }
function wp_strip_all_tags( $text ) { return strip_tags( (string) $text ); }
function wp_kses_post( $html ) { return (string) $html; }
function get_bloginfo( $show ) { return 'name' === $show ? 'Situs Uji' : ''; }
function home_url( $path = '/' ) { return 'https://example.test' . $path; }
function get_custom_logo() { return ''; }
function get_page_by_path( $slug, $output = OBJECT, $type = 'page' ) { return null; }
function get_permalink( $post ) { return ''; }
function get_post_type_archive_link( $type ) { return 'graha_product' === $type ? 'https://example.test/products/' : ''; }

require_once dirname( __DIR__ ) . '/plugin/graha-selang-site-core/src/AssetService.php';
require_once dirname( __DIR__ ) . '/plugin/graha-selang-site-core/src/NavigationService.php';
require_once dirname( __DIR__ ) . '/plugin/graha-selang-site-core/src/TemplateService.php';

function assert_true( $condition, $message ) {
	if ( ! $condition ) { fwrite( STDERR, "FAIL: {$message}\n" ); exit( 1 ); }
	echo "PASS: {$message}\n";
}

$assets = new \GrahaSelang\AssetService( dirname( __DIR__ ) . '/plugin/graha-selang-site-core/graha-selang.php', '0.4.0' );
$navigation = new \GrahaSelang\NavigationService();
$templates = new \GrahaSelang\TemplateService( $assets, $navigation );

$inner = $templates->render_page( 'about', array( 'heading' => 'Tentang', 'content_html' => '<p>Konten asli dari pemilik native.</p>' ) );
assert_true( false !== strpos( $inner, '<header class="graha-site-header">' ), 'semantic shell includes header' );
assert_true( false !== strpos( $inner, '<main id="graha-main"' ), 'semantic shell includes one main landmark' );
assert_true( false !== strpos( $inner, '<footer class="graha-site-footer">' ), 'semantic shell includes footer' );
assert_true( 1 === substr_count( $inner, '<h1>' ), 'rendered family contains exactly one intended H1' );
assert_true( false !== strpos( $inner, 'Lewati ke konten utama' ), 'shell includes Indonesian skip link' );
assert_true( false !== strpos( $inner, '>Beranda</a>' ), 'non-home breadcrumb includes native Home anchor' );
assert_true( false !== strpos( $inner, 'aria-current="page">Tentang</span>' ), 'breadcrumb identifies current page' );
assert_true( false === stripos( $inner, 'application/ld+json' ), 'visible breadcrumbs do not emit schema' );

$families = array( 'product_archive','product_category','product_single','application','brand','about','service','technical_rfq','article','legal','search','not_found' );
foreach ( $families as $family ) {
	$prototype = $templates->render_page( $family, array( 'heading' => 'Judul Uji', 'content_html' => '<p>Konten uji eksplisit.</p>' ) );
	assert_true( '' !== $prototype, "family prototype renders explicitly: {$family}" );
	assert_true( 1 === substr_count( $prototype, '<h1>' ), "family prototype preserves one H1: {$family}" );
}

$valid_sections = array(
	array( 'kind' => 'hero', 'heading' => 'Solusi selang industri', 'content_html' => '<p>Temukan kebutuhan produk melalui katalog native yang tersedia untuk situs ini.</p>' ),
	array( 'kind' => 'products', 'heading' => 'Produk & solusi', 'content_html' => '<p>Kelompok produk ditampilkan dari data produk native, bukan dari fixture katalog kedua.</p>' ),
	array( 'kind' => 'proof', 'heading' => 'Layanan & cakupan', 'content_html' => '<p>Bagian ini hanya boleh berisi konten dan tujuan native yang benar-benar tersedia.</p>' ),
	array( 'kind' => 'consultation', 'heading' => 'Konsultasi teknis', 'content_html' => '<p>Arahkan kebutuhan teknis menuju kanal kontak atau RFQ yang sudah tersedia secara nyata.</p>' ),
);
$home = $templates->render_page( 'home', array( 'heading' => 'Beranda', 'sections' => $valid_sections ) );
assert_true( 4 === substr_count( $home, '<section class="graha-page-section' ), 'Home renderer accepts four required substantial section kinds' );
assert_true( false === strpos( $home, 'graha-breadcrumbs' ), 'Home does not render breadcrumbs' );

$incomplete = $valid_sections;
array_pop( $incomplete );
assert_true( '' === $templates->render_page( 'home', array( 'heading' => 'Beranda', 'sections' => $incomplete ) ), 'Home refuses incomplete required section input' );

$dummy = $valid_sections;
$dummy[2]['content_html'] = '<p>Dummy placeholder content for a production-looking section.</p>';
assert_true( '' === $templates->render_page( 'home', array( 'heading' => 'Beranda', 'sections' => $dummy ) ), 'Home refuses dummy/placeholder section input' );

$templates->prepare_page( 'about' );
assert_true( array( 'graha-selang-tokens','graha-selang-foundation','graha-selang-navigation','graha-selang-shell' ) === $GLOBALS['styles'], 'shell preparation enqueues only the centralized style chain' );
assert_true( array( 'graha-selang-navigation' ) === $GLOBALS['scripts'], 'shell preparation enqueues only navigation JavaScript' );
