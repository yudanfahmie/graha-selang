<?php

define( 'ABSPATH', __DIR__ . '/' );
define( 'OBJECT', 'OBJECT' );
class WP_Post { public $ID; public $post_name; public $post_status='publish'; public function __construct($id,$slug,$status='publish'){$this->ID=$id;$this->post_name=$slug;$this->post_status=$status;} }

$GLOBALS['front_page']    = true;
$GLOBALS['singular_type'] = null;
$GLOBALS['current_id']    = 0;
$GLOBALS['styles']        = array();
$GLOBALS['options']       = array( 'show_on_front' => 'page', 'page_on_front' => 50 );
$GLOBALS['products']      = array();
$GLOBALS['pages']         = array(
	'home'          => null,
	'contact-us'    => (object) array( 'ID' => 201, 'post_name' => 'contact-us', 'post_status' => 'publish' ),
	'layanan-kami'  => (object) array( 'ID' => 202, 'post_name' => 'layanan-kami', 'post_status' => 'publish' ),
	'about-us'      => (object) array( 'ID' => 203, 'post_name' => 'about-us', 'post_status' => 'publish' ),
	'request-quote' => (object) array( 'ID' => 204, 'post_name' => 'request-quote', 'post_status' => 'publish' ),
);
$GLOBALS['pages']['home'] = new WP_Post( 50, 'home' );
$GLOBALS['statuses'] = array( 400 => 'publish', 401 => 'publish', 501 => 'publish', 601 => 'publish' );
$GLOBALS['titles'] = array( 400 => 'Induk Halaman', 401 => 'Anak Halaman', 501 => 'Panduan Selang', 601 => 'Produk Native' );
$GLOBALS['ancestors'] = array( 401 => array( 400 ) );

function add_action() {}
function add_filter() {}
function remove_filter() {}
function plugin_dir_url() { return 'https://example.test/plugin/'; }
function wp_register_style() {}
function wp_register_script() {}
function wp_enqueue_style( $handle ) { $GLOBALS['styles'][] = $handle; }
function wp_enqueue_script() {}
function is_admin() { return false; }
function get_option( $key, $default = false ) { return array_key_exists( $key, $GLOBALS['options'] ) ? $GLOBALS['options'][ $key ] : $default; }
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
function get_permalink( $target ) {
	if ( is_object( $target ) ) return 'https://example.test/' . $target->post_name . '/';
	return 'https://example.test/?p=' . (int) $target;
}
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
function get_queried_object_id() { return 0; }
function get_post_field() { return ''; }
function apply_filters( $hook, $value ) { return $value; }

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
		'name'              => $name,
		'_graha_home_group' => $group,
	);
	$id++;
}

$assets = new \GrahaSelang\AssetService( dirname( __DIR__ ) . '/plugin/graha-selang-site-core/graha-selang.php', '0.6.0' );
$nav = new \GrahaSelang\NavigationService();
$templates = new \GrahaSelang\TemplateService( $assets, $nav );

$html = $templates->enhance_native_content( '<p>Konten beranda asli.</p>' );
assert_true( 4 === substr_count( $html, '<section class="graha-page-section' ), 'Home renders four substantial sections' );
assert_true( false !== strpos( $html, 'Konten beranda asli.' ), 'Home preserves meaningful editor content' );
assert_true( false !== strpos( $html, 'Hydraulic Hose / MORGEN' ), 'Home renders hydraulic anchor hierarchy' );
assert_true( false !== strpos( $html, 'CNG / High-pressure Gas Hose' ), 'Home renders specialist group' );
assert_true( false !== strpos( $html, 'https://example.test/products/' ), 'Home uses native product archive' );
assert_true( false !== strpos( $html, 'https://example.test/request-quote/' ), 'Home exposes Request Quote' );
assert_true( false === stripos( $html, 'woocommerce' ), 'Home contains no Woo dependency' );

$templates->prepare_native_presentation();
assert_true( array( 'graha-selang-tokens', 'graha-selang-foundation', 'graha-selang-navigation', 'graha-selang-shell', 'graha-selang-home' ) === $GLOBALS['styles'], 'front page loads complete Graha shell style chain regardless of product readiness' );
$resolved = $templates->resolve_native_template( '/theme/index.php' );
assert_true( false !== strpos( $resolved, 'templates/front-page.php' ), 'front page resolves to plugin-owned document shell' );
assert_true( '/theme/index.php' !== $resolved, 'the active theme fallback template is not selected for the front page' );
ob_start(); $templates->output_front_page(); $front_shell = (string) ob_get_clean();
assert_true( false !== strpos( $front_shell, 'graha-site-shell' ) && false !== strpos( $front_shell, 'graha-hero' ), 'canonical static Home front page renders the full Graha shell and hero' );

// Route-driven ownership: Graha owns the public front page purely because it IS is_front_page(),
// never because Reading Settings happen to point at the canonical static Home Page. An established
// latest-posts front (show_on_front=posts, fresh_site=0 -- SiteLifecycleService deliberately leaves
// this untouched) must still resolve to the Graha shell/Homepage instead of falling back to
// whatever the active theme renders ("No content found" / "Powered by WordPress").
$GLOBALS['options']['show_on_front'] = 'posts';
$GLOBALS['options']['page_on_front'] = 0;
$templates = new \GrahaSelang\TemplateService( $assets, $nav );
$resolved = $templates->resolve_native_template( '/theme/index.php' );
assert_true( false !== strpos( $resolved, 'templates/front-page.php' ), 'established posts-front STILL resolves to the Graha front-page shell (route-driven ownership)' );
assert_true( '/theme/index.php' !== $resolved, 'established posts-front does not fall back to the active theme template' );
$before_styles = count( $GLOBALS['styles'] );
$templates->prepare_native_presentation();
assert_true( count( $GLOBALS['styles'] ) > $before_styles && in_array( 'graha-selang-shell', $GLOBALS['styles'], true ) && in_array( 'graha-selang-home', $GLOBALS['styles'], true ), 'established posts-front STILL enqueues the full Graha Home asset chain' );
$posts_front_content = '<article>Native posts index item.</article>';
$posts_front_home = $templates->enhance_native_content( $posts_front_content );
assert_true( $posts_front_content !== $posts_front_home, 'established posts-front content is composed into the Graha Homepage rather than passed through untouched' );
assert_true( 4 === substr_count( $posts_front_home, '<section class="graha-page-section' ), 'posts-front Homepage still renders all four required sections without any static Page existing' );
ob_start(); $templates->output_front_page(); $posts_front_shell = (string) ob_get_clean();
assert_true( false !== strpos( $posts_front_shell, 'graha-site-shell' ) && false !== strpos( $posts_front_shell, 'graha-hero' ), 'established posts-front renders the full Graha shell and hero, not the theme "No content found" fallback' );
$GLOBALS['options']['show_on_front'] = 'page';
$GLOBALS['options']['page_on_front'] = 50;

unset( $GLOBALS['products'][6] );
$templates = new \GrahaSelang\TemplateService( $assets, $nav );
$html = $templates->enhance_native_content( '<p>Native fallback.</p>' );
assert_true( 4 === substr_count( $html, '<section class="graha-page-section' ), 'Home remains useful when a product group is empty' );
assert_true( false !== strpos( $html, 'Gunakan katalog atau konsultasi teknis' ), 'missing product group renders graceful empty state' );

$GLOBALS['products'][6] = array( 'name' => $groups['cng_specialist'], '_graha_home_group' => 'cng_specialist' );
unset( $GLOBALS['pages']['contact-us'] );
$templates = new \GrahaSelang\TemplateService( $assets, $nav );
$html = $templates->enhance_native_content( '<p>Native fallback.</p>' );
assert_true( 4 === substr_count( $html, '<section class="graha-page-section' ), 'Home does not disappear when Contact destination is unavailable' );
$GLOBALS['pages']['contact-us'] = (object) array( 'ID' => 201, 'post_name' => 'contact-us', 'post_status' => 'publish' );

$GLOBALS['front_page'] = false;
$GLOBALS['singular_type'] = 'page';
$GLOBALS['current_id'] = 401;
$templates = new \GrahaSelang\TemplateService( $assets, $nav );
$page = $templates->enhance_native_content( '<p>Isi halaman asli.</p>' );
assert_true( false !== strpos( $page, 'graha-native-content' ) && false !== strpos( $page, 'Isi halaman asli.' ), 'native Page content keeps shared presentation' );
assert_true( false !== strpos( $page, 'Induk Halaman</a>' ), 'Page breadcrumb uses native parent' );
assert_true( false !== strpos( $page, 'aria-current="page">Anak Halaman</span>' ), 'Page breadcrumb current' );

$GLOBALS['singular_type'] = 'post';
$GLOBALS['current_id'] = 501;
$templates = new \GrahaSelang\TemplateService( $assets, $nav );
$post = $templates->enhance_native_content( '<p>Isi artikel asli.</p>' );
assert_true( false !== strpos( $post, '>Beranda</a>' ), 'Post breadcrumb starts Home' );

$GLOBALS['singular_type'] = 'graha_product';
$GLOBALS['current_id'] = 601;
$templates = new \GrahaSelang\TemplateService( $assets, $nav );
$product = $templates->enhance_native_content( '<p>Isi produk asli.</p>' );
assert_true( false !== strpos( $product, 'href="https://example.test/products/"' ), 'product breadcrumb links archive' );
assert_true( false !== strpos( $product, 'aria-current="page">Produk Native</span>' ), 'product breadcrumb marks current' );
