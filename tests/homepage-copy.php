<?php
/**
 * Guards the Homepage's public-facing copy: no staging/internal wording,
 * and no invented "coming soon" discovery doors for destinations that do
 * not exist yet (Application/Brand hubs are not built).
 */

define( 'ABSPATH', __DIR__ . '/' );
define( 'OBJECT', 'OBJECT' );

$GLOBALS['front_page']    = true;
$GLOBALS['singular_type'] = null;
$GLOBALS['options']       = array( 'show_on_front' => 'page', 'page_on_front' => 50 );
$GLOBALS['products']      = array();
$GLOBALS['pages']         = array(
	'home'          => null,
	'contact-us'    => (object) array( 'ID' => 201, 'post_name' => 'contact-us', 'post_status' => 'publish' ),
	'layanan-kami'  => (object) array( 'ID' => 202, 'post_name' => 'layanan-kami', 'post_status' => 'publish' ),
	'about-us'      => (object) array( 'ID' => 203, 'post_name' => 'about-us', 'post_status' => 'publish' ),
	'request-quote' => (object) array( 'ID' => 204, 'post_name' => 'request-quote', 'post_status' => 'publish' ),
);
class WP_Post { public $ID; public $post_name; public $post_status='publish'; public function __construct($id,$slug){$this->ID=$id;$this->post_name=$slug;} }
$GLOBALS['pages']['home'] = new WP_Post( 50, 'home' );

function add_action() {}
function add_filter() {}
function remove_filter() {}
function plugin_dir_url() { return 'https://example.test/plugin/'; }
function wp_register_style() {}
function wp_register_script() {}
function wp_enqueue_style() {}
function wp_enqueue_script() {}
function is_admin() { return false; }
function get_option( $key, $default = false ) { return array_key_exists( $key, $GLOBALS['options'] ) ? $GLOBALS['options'][ $key ] : $default; }
function is_front_page() { return $GLOBALS['front_page']; }
function is_singular( $types = null ) { return null !== $GLOBALS['singular_type']; }
function in_the_loop() { return true; }
function is_main_query() { return true; }
function post_type_exists( $type ) { return 'graha_product' === $type; }
function get_posts( $args ) { return array_keys( $GLOBALS['products'] ); }
function get_post_meta( $id, $key, $single = true ) { return isset( $GLOBALS['products'][ $id ][ $key ] ) ? $GLOBALS['products'][ $id ][ $key ] : ''; }
function get_the_ID() { return 0; }
function get_the_title( $id = 0 ) { return isset( $GLOBALS['products'][ $id ] ) ? $GLOBALS['products'][ $id ]['name'] : ''; }
function get_permalink( $target ) { return is_object( $target ) ? 'https://example.test/' . $target->post_name . '/' : 'https://example.test/product/' . (int) $target . '/'; }
function get_page_by_path( $slug, $output = OBJECT, $type = 'page' ) { return isset( $GLOBALS['pages'][ $slug ] ) ? $GLOBALS['pages'][ $slug ] : null; }
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
function get_bloginfo() { return 'Graha Selang'; }
function get_custom_logo() { return ''; }
function get_queried_object_id() { return 0; }
function get_post_field() { return ''; }
function apply_filters( $hook, $value ) { return $value; }

require_once dirname( __DIR__ ) . '/plugin/graha-selang-site-core/src/AssetService.php';
require_once dirname( __DIR__ ) . '/plugin/graha-selang-site-core/src/NavigationService.php';
require_once dirname( __DIR__ ) . '/plugin/graha-selang-site-core/src/TemplateService.php';

function ok( $condition, $message ) { if ( ! $condition ) { fwrite( STDERR, "FAIL: {$message}\n" ); exit( 1 ); } echo "PASS: {$message}\n"; }

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
	$GLOBALS['products'][ $id ] = array( 'name' => $name, '_graha_home_group' => $group );
	$id++;
}

$assets = new \GrahaSelang\AssetService( dirname( __DIR__ ) . '/plugin/graha-selang-site-core/graha-selang.php', '0.7.0' );
$nav = new \GrahaSelang\NavigationService();
$templates = new \GrahaSelang\TemplateService( $assets, $nav );

$html = $templates->enhance_native_content( '<p>Konten beranda asli.</p>' );

foreach ( array( 'Segera hadir', 'sedang disiapkan', 'prioritas tampilan yang tidak setara' ) as $banned ) {
	ok( false === stripos( $html, $banned ), "Homepage contains no staging/internal copy: \"{$banned}\"" );
}
ok( false === strpos( $html, 'graha-discovery-card--sparse' ), 'Homepage renders no inert "coming soon" discovery card' );
ok( 2 === substr_count( $html, 'class="graha-discovery-card"' ), 'Homepage discovery grid shows exactly the doors with real destinations (Produk, Konsultasi)' );
ok( false !== strpos( $html, 'https://example.test/products/' ), 'Produk discovery door links to the real product archive' );
ok( false !== strpos( $html, 'https://example.test/request-quote/' ), 'Konsultasi discovery door links to the real Request Quote page' );
ok( false === stripos( $html, 'Aplikasi & Kebutuhan Industri' ) && false === stripos( $html, '>Brand<' ), 'Homepage does not present Aplikasi/Brand as if they were live discovery doors' );

echo "Homepage copy checks passed.\n";
