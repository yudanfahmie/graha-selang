<?php

define( 'ABSPATH', __DIR__ . '/' );
define( 'OBJECT', 'OBJECT' );

$GLOBALS['current_id'] = 101;
$GLOBALS['slugs'] = array( 101=>'about-us', 102=>'layanan-kami', 103=>'contact-us', 104=>'request-quote' );
$GLOBALS['titles'] = array( 101=>'About Us', 102=>'Layanan Kami', 103=>'Contact Us', 104=>'Request Quote' );
$GLOBALS['pages'] = array();

class WP_Post {
	public $ID; public $post_name; public $post_status='publish';
	public function __construct( $id, $slug, $status='publish' ) { $this->ID=$id; $this->post_name=$slug; $this->post_status=$status; }
}
foreach ( $GLOBALS['slugs'] as $id => $slug ) { $GLOBALS['pages'][ $slug ] = new WP_Post( $id, $slug ); }

function add_action() {}
function add_filter() {}
function remove_filter() {}
function is_admin() { return false; }
function is_front_page() { return false; }
function is_singular( $types = null ) { return null === $types ? true : in_array( 'page', (array) $types, true ); }
function in_the_loop() { return true; }
function is_main_query() { return true; }
function get_the_ID() { return $GLOBALS['current_id']; }
function get_post_field( $field, $id ) { return 'post_name' === $field ? ( $GLOBALS['slugs'][ $id ] ?? '' ) : ''; }
function get_the_title( $id = 0 ) { $id = $id ?: $GLOBALS['current_id']; return $GLOBALS['titles'][ $id ] ?? ''; }
function get_post_ancestors() { return array(); }
function get_page_by_path( $slug, $output = OBJECT, $type = 'page' ) { return $GLOBALS['pages'][ $slug ] ?? null; }
function get_permalink( $page ) { return is_object( $page ) ? 'https://example.test/' . $page->post_name . '/' : 'https://example.test/?p=' . (int) $page; }
function get_post_type_archive_link( $type ) { return 'graha_product' === $type ? 'https://example.test/products/' : ''; }
function post_type_exists() { return true; }
function get_posts() { return array(); }
function get_post_meta() { return ''; }
function get_post_status() { return 'publish'; }
function get_option( $key, $default = false ) { return $default; }
function absint( $value ) { return abs( (int) $value ); }
function wp_strip_all_tags( $value ) { return strip_tags( (string) $value ); }
function wp_kses_post( $value ) { return (string) $value; }
function sanitize_key( $value ) { return preg_replace( '/[^a-z0-9_-]/', '', strtolower( $value ) ); }
function __( $value ) { return $value; }
function esc_html__( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_html( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_attr__( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_attr( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_url( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function home_url( $path = '/' ) { return 'https://example.test' . $path; }
function get_bloginfo() { return 'Graha Selang'; }
function get_custom_logo() { return ''; }
function get_nav_menu_locations() { return array(); }
function wp_get_nav_menu_items() { return array(); }
function register_nav_menus() {}
function apply_filters( $hook, $value ) { return $value; }

require_once dirname( __DIR__ ) . '/plugin/graha-selang-site-core/src/AssetService.php';
require_once dirname( __DIR__ ) . '/plugin/graha-selang-site-core/src/NavigationService.php';
require_once dirname( __DIR__ ) . '/plugin/graha-selang-site-core/src/TemplateService.php';

function ok( $condition, $message ) { if ( ! $condition ) { fwrite( STDERR, "FAIL: {$message}\n" ); exit( 1 ); } echo "PASS: {$message}\n"; }
$assets = new \GrahaSelang\AssetService( dirname( __DIR__ ) . '/plugin/graha-selang-site-core/graha-selang.php', '0.6.1' );
$nav = new \GrahaSelang\NavigationService();
$templates = new \GrahaSelang\TemplateService( $assets, $nav );

$GLOBALS['current_id'] = 101;
$about = $templates->enhance_native_content( '' );
ok( false !== strpos( $about, 'Tentang Graha Selang' ), 'empty bootstrap About renders useful company/product orientation' );
ok( false !== strpos( $about, '/products/' ) && false !== strpos( $about, '/layanan-kami/' ) && false !== strpos( $about, '/contact-us/' ), 'About fallback links only to canonical public discovery paths' );

$GLOBALS['current_id'] = 102;
$services = $templates->enhance_native_content( '   ' );
ok( false !== strpos( $services, 'Layanan dan kapabilitas' ) && false !== strpos( $services, 'crimping dan assembly' ), 'empty Services renders canonical capability orientation' );
ok( false !== strpos( $services, '/request-quote/' ), 'Services fallback exposes technical consultation path' );

$GLOBALS['current_id'] = 103;
$contact = $templates->enhance_native_content( '' );
ok( false !== strpos( $contact, 'Hubungi Graha Selang' ) && false !== strpos( $contact, '/request-quote/' ), 'empty Contact renders safe RFQ orientation' );
ok( false === strpos( $contact, 'mailto:' ) && false === strpos( $contact, 'tel:' ) && false === strpos( $contact, '@' ), 'Contact fallback invents no phone, email, or address-like contact fact' );

$GLOBALS['current_id'] = 104;
$rfq = $templates->enhance_native_content( '' );
ok( false !== strpos( $rfq, 'Konsultasi kebutuhan teknis' ) && false !== strpos( $rfq, '/contact-us/' ), 'empty Request Quote renders technical inquiry orientation and Contact fallback' );
ok( false === stripos( $rfq, 'upload' ) && false === stripos( $rfq, 'whatsapp' ) && false === stripos( $rfq, 'recipient' ), 'Request Quote fallback invents no provider behavior' );

$GLOBALS['current_id'] = 101;
$editor = '<p>Profil perusahaan dari editor dengan informasi yang disetujui.</p>';
$about_editor = $templates->enhance_native_content( $editor );
ok( false !== strpos( $about_editor, 'Profil perusahaan dari editor' ), 'meaningful editor content remains authoritative' );
ok( false === strpos( $about_editor, 'Tentang Graha Selang' ), 'meaningful editor content suppresses bootstrap fallback copy' );

$image_editor = '<figure><img src="/approved.jpg" alt=""></figure>';
$about_image = $templates->enhance_native_content( $image_editor );
ok( false === strpos( $about_image, 'Tentang Graha Selang' ), 'meaningful media-only editor content also suppresses fallback' );

echo "Bootstrap Page fallback checks passed.\n";
