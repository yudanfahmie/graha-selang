<?php

define( 'ABSPATH', __DIR__ . '/' );
define( 'OBJECT', 'OBJECT' );

$GLOBALS['front_page']    = true;
$GLOBALS['singular_type'] = null;
$GLOBALS['current_id']    = 0;
$GLOBALS['styles']        = array();
$GLOBALS['products']      = array();
$GLOBALS['pages']         = array(
	'contact-us'   => (object) array( 'ID' => 201, 'post_status' => 'publish' ),
	'layanan-kami' => (object) array( 'ID' => 202, 'post_status' => 'publish' ),
);
$GLOBALS['statuses'] = array( 400 => 'publish', 401 => 'publish', 501 => 'publish', 601 => 'publish' );
$GLOBALS['titles'] = array( 400 => 'Induk Halaman', 401 => 'Anak Halaman', 501 => 'Panduan Selang', 601 => 'Produk Native' );
$GLOBALS['ancestors'] = array( 401 => array( 400 ) );

function add_action() {}
function add_filter() {}
function plugin_dir_url() { return 'https://example.test/plugin/'; }
function wp_register_style() {}
function wp_register_script() {}
function wp_enqueue_style( $handle ) { $GLOBALS['styles'][] = $handle; }
function wp_enqueue_script() {}
function is_admin() { return false; }
function is_front_page() { return $GLOBALS['front_page']; }
function is_singular( $types = null ) {
	$type = $GLOBALS['singular_type'];
	if ( ! $type ) return false;
	if ( null === $types ) return true;
	return in_array( $type, (array) $types, true );
}
function in_the_loop() { return true; }
function is_main_query() { return true; }
function post_type_exists( $type ) { return 'graha_product' === $type; }
function get_posts( $args ) { return array_keys( $GLOBALS['products'] ); }
function get_post_meta( $id, $key, $single = true ) { return isset( $GLOBALS['products'][ $id ][ $key ] ) ? $GLOBALS['products'][ $id ][ $key ] : ''; }
function get_the_ID() { return $GLOBALS['current_id']; }
function get_the_title( $id = 0 ) {
	$id = $id ? (int) $id : $GLOBALS['current_id'];
	if ( isset( $GLOBALS['products'][ $id ] ) ) return $GLOBALS['products'][ $id ]['name'];
	return isset( $GLOBALS['titles'][ $id ] ) ? $GLOBALS['titles'][ $id ] : '';
}
function get_post_ancestors( $id ) { return isset( $GLOBALS['ancestors'][ $id ] ) ? $GLOBALS['ancestors'][ $id ] : array(); }
function get_permalink( $target ) { $id = is_object( $target ) ? $target->ID : (int) $target; return 'https://example.test/?p=' . $id; }
function get_page_by_path( $slug, $output, $type ) { return isset( $GLOBALS['pages'][ $slug ] ) ? $GLOBALS['pages'][ $slug ] : null; }
function get_post_status( $id ) { return isset( $GLOBALS['statuses'][ $id ] ) ? $GLOBALS['statuses'][ $id ] : false; }
function get_post_type_archive_link( $type ) { return 'graha_product' === $type ? 'https://example.test/products/' : ''; }
function wp_strip_all_tags( $value ) { return strip_tags( (string) $value ); }
function wp_kses_post( $value ) { return (string) $value; }
function sanitize_key( $value ) { return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( $value ) ); }
function __( $value ) { return $value; }
function esc_html__( $value ) { return htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' ); }
function esc_html( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_attr__( $value ) { return htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' ); }
function esc_attr( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_url( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function get_nav_menu_locations() { return array(); }
function absint( $value ) { return abs( (int) $value ); }
function wp_get_nav_menu_items() { return array(); }
function register_nav_menus() {}
function home_url( $path = '/' ) { return 'https://example.test' . $path; }
function get_bloginfo() { return 'Graha Test'; }
function get_custom_logo() { return ''; }

require_once dirname( __DIR__ ) . '/plugin/graha-selang-site-core/src/AssetService.php';
require_once dirname( __DIR__ ) . '/plugin/graha-selang-site-core/src/NavigationService.php';
require_once dirname( __DIR__ ) . '/plugin/graha-selang-site-core/src/TemplateService.php';

function assert_true( $condition, $message ) {
	if ( ! $condition ) { fwrite( STDERR, "FAIL: {$message}\n" ); exit( 1 ); }
	echo "PASS: {$message}\n";
}

$groups = array(
	'hydraulic_anchor'  => 'Morgen R1 Hydraulic Hose',
	'industrial_anchor' => 'Sunflex AH600 Air Hose 600 PSI',
	'ducting_support'   => 'Aluminium Ducting Hose',
	'pvc_support'       => 'Selang Kawat Spiral Transparan / PVC Spring Hose',
	'fittings_support'  => 'Camlock Aluminium',
	'cng_specialist'    => 'Morgen CNG Hose Assembly 5000 PSI High Pressure',
);
$id = 1;
foreach ( $groups as $group => $name ) {
	$GLOBALS['products'][ $id ] = array(
		'name'                   => $name,
		'_graha_source_identity' => 'graha-public-product:test-' . $id,
		'_graha_home_group'      => $group,
	);
	$id++;
}

$assets = new \GrahaSelang\AssetService( dirname( __DIR__ ) . '/plugin/graha-selang-site-core/graha-selang.php', '0.5.0' );
$nav = new \GrahaSelang\NavigationService();
$templates = new \GrahaSelang\TemplateService( $assets, $nav );

$html = $templates->enhance_native_content( '<p>Konten beranda asli.</p>' );
assert_true( 4 === substr_count( $html, '<section class="graha-page-section' ), 'data-ready native Home renders four substantial sections' );
assert_true( false !== strpos( $html, 'Hydraulic Hose / MORGEN' ), 'Home renders hydraulic anchor hierarchy' );
assert_true( false !== strpos( $html, 'Industrial Hose &amp; Assembly / HAMMER + SUNFLEX' ), 'Home renders industrial anchor hierarchy' );
assert_true( false !== strpos( $html, 'CNG / High-pressure Gas Hose' ), 'Home renders specialist group' );
assert_true( false !== strpos( $html, 'https://example.test/products/' ), 'Home uses native graha_product archive link' );
assert_true( false !== strpos( $html, 'https://example.test/?p=202' ) && false !== strpos( $html, 'https://example.test/?p=201' ), 'Home uses native Services and Contact destinations' );
assert_true( false === strpos( $html, 'graha-breadcrumbs' ), 'native Home renders no breadcrumb' );
assert_true( false === stripos( $html, 'woocommerce' ), 'Home contains no WooCommerce dependency/copy' );
assert_true( false === strpos( $html, 'migration-source/' ), 'Home never reads or exposes repository migration archive' );

$templates->prepare_native_presentation();
assert_true( array( 'graha-selang-tokens', 'graha-selang-foundation' ) === $GLOBALS['styles'], 'native Home loads only shared token/foundation styles when ready' );

unset( $GLOBALS['products'][6] );
$templates = new \GrahaSelang\TemplateService( $assets, $nav );
$original = '<p>Native fallback.</p>';
assert_true( $original === $templates->enhance_native_content( $original ), 'Home remains native when one required product group is missing' );

$GLOBALS['products'][6] = array( 'name' => $groups['cng_specialist'], '_graha_source_identity' => 'graha-public-product:test-6', '_graha_home_group' => 'cng_specialist' );
unset( $GLOBALS['pages']['contact-us'] );
$templates = new \GrahaSelang\TemplateService( $assets, $nav );
assert_true( $original === $templates->enhance_native_content( $original ), 'Home remains native when real Contact destination is unavailable' );
$GLOBALS['pages']['contact-us'] = (object) array( 'ID' => 201, 'post_status' => 'publish' );

$GLOBALS['front_page'] = false;
$GLOBALS['singular_type'] = 'page';
$GLOBALS['current_id'] = 401;
$templates = new \GrahaSelang\TemplateService( $assets, $nav );
$page = $templates->enhance_native_content( '<p>Isi halaman asli.</p>' );
assert_true( false !== strpos( $page, 'graha-native-content' ) && false !== strpos( $page, 'Isi halaman asli.' ), 'native Page content receives shared production presentation without route takeover' );
assert_true( false !== strpos( $page, 'href="https://example.test/?p=400"' ) && false !== strpos( $page, '>Induk Halaman</a>' ), 'native Page breadcrumb uses published WordPress parent hierarchy' );
assert_true( false !== strpos( $page, 'aria-current="page">Anak Halaman</span>' ), 'native Page breadcrumb marks current page' );
assert_true( false === stripos( $page, 'application/ld+json' ), 'native Page breadcrumb emits no schema' );

$GLOBALS['singular_type'] = 'post';
$GLOBALS['current_id'] = 501;
$templates = new \GrahaSelang\TemplateService( $assets, $nav );
$post = $templates->enhance_native_content( '<p>Isi artikel asli.</p>' );
assert_true( false !== strpos( $post, '>Beranda</a>' ), 'native Post breadcrumb starts with Home' );
assert_true( false !== strpos( $post, 'aria-current="page">Panduan Selang</span>' ), 'native Post breadcrumb marks current article' );
assert_true( false === strpos( $post, 'Induk Halaman' ), 'native Post breadcrumb stays simple without Page hierarchy' );

$GLOBALS['singular_type'] = 'graha_product';
$GLOBALS['current_id'] = 601;
$templates = new \GrahaSelang\TemplateService( $assets, $nav );
$product = $templates->enhance_native_content( '<p>Isi produk asli.</p>' );
assert_true( false !== strpos( $product, 'href="https://example.test/products/"' ) && false !== strpos( $product, '>Produk</a>' ), 'native product breadcrumb links to graha_product archive' );
assert_true( false !== strpos( $product, 'aria-current="page">Produk Native</span>' ), 'native product breadcrumb marks current product' );
assert_true( false === stripos( $product, 'application/ld+json' ), 'native product breadcrumb emits no schema' );
