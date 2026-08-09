<?php

define( 'ABSPATH', __DIR__ . '/' );
define( 'OBJECT', 'OBJECT' );

class WP_Post { public $ID; public $post_name; public $post_status='publish'; public function __construct($id,$slug){$this->ID=$id;$this->post_name=$slug;} }
$GLOBALS['fallback_pages']=array();

$GLOBALS['nav_items']    = array();
$GLOBALS['nav_location'] = 99;

function get_nav_menu_locations() {
	return $GLOBALS['nav_location'] ? array( 'graha-primary' => $GLOBALS['nav_location'] ) : array();
}
function absint( $value ) {
	return abs( (int) $value );
}
function wp_get_nav_menu_items( $menu_id ) {
	return 99 === $menu_id ? $GLOBALS['nav_items'] : array();
}
function __( $text ) { return $text; }
function esc_attr( $text ) { return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' ); }
function esc_html( $text ) { return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' ); }
function esc_html__( $text ) { return esc_html( $text ); }
function esc_url( $url ) {
	$url = trim( (string) $url );
	if ( 0 === stripos( $url, 'javascript:' ) ) {
		return '';
	}
	return htmlspecialchars( $url, ENT_QUOTES, 'UTF-8' );
}
function home_url( $path = '/' ) { return 'https://example.test' . $path; }
function get_post_type_archive_link( $type ) { return 'graha_product' === $type ? 'https://example.test/products/' : ''; }
function get_page_by_path( $slug, $output = OBJECT, $type = 'page' ) { return $GLOBALS['fallback_pages'][ $slug ] ?? null; }
function get_permalink( $page ) { return 'https://example.test/' . $page->post_name . '/'; }

require_once dirname( __DIR__ ) . '/plugin/graha-selang-site-core/src/NavigationService.php';

function item( $id, $parent, $title, $url, $target = '' ) {
	$item                        = new stdClass();
	$item->ID                    = $id;
	$item->menu_item_parent      = $parent;
	$item->title                 = $title;
	$item->url                   = $url;
	$item->target                = $target;
	$item->xfn                   = '';
	$item->current               = false;
	$item->current_item_ancestor = false;
	return $item;
}

function assert_true( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, "FAIL: {$message}\n" );
		exit( 1 );
	}
	echo "PASS: {$message}\n";
}

$GLOBALS['nav_items'] = array(
	item( 1, 0, 'Produk', '/produk/' ),
	item( 2, 1, 'Subproduk', '/produk/sub/', '_blank' ),
	item( 3, 0, 'Invalid', 'javascript:alert(1)' ),
);

$service = new \GrahaSelang\NavigationService();
$html    = $service->render_primary();

assert_true( false !== strpos( $html, 'href="/produk/"' ), 'native menu URL renders as a real anchor' );
assert_true( false !== strpos( $html, 'href="/produk/sub/"' ), 'nested native menu URL remains crawlable' );
assert_true( false !== strpos( $html, 'rel="noopener"' ), '_blank link receives noopener' );
assert_true( false === strpos( $html, 'javascript:' ), 'unsafe URL is not rendered as an anchor target' );
assert_true( false !== strpos( $html, 'data-graha-nav-toggle hidden' ), 'root disclosure control is inert without JavaScript' );
assert_true( false !== strpos( $html, 'data-graha-disclosure-toggle hidden' ), 'submenu disclosure control is inert without JavaScript' );

foreach ( array( 'layanan-kami','about-us','request-quote','contact-us' ) as $i => $slug ) { $GLOBALS['fallback_pages'][ $slug ] = new WP_Post( 20 + $i, $slug ); }
$GLOBALS['nav_location'] = 0;
$_SERVER['REQUEST_URI']  = '/products/';
$tree = $service->get_primary_tree();
assert_true( 6 === count( $tree ), 'missing native menu uses canonical Graha fallback tree' );
assert_true( array( 'Beranda','Produk','Layanan','Tentang Kami','Request Quote','Hubungi Kami' ) === array_column( $tree, 'title' ), 'fallback navigation is deterministic and canonical' );
assert_true( true === $tree[1]['current'], 'fallback navigation marks current route' );
$html = $service->render_primary();
assert_true( false !== strpos( $html, 'https://example.test/request-quote/' ), 'fallback navigation exposes Request Quote' );
$GLOBALS['fallback_pages']['contact-us']->post_status = 'trash';
$tree = $service->get_primary_tree();
assert_true( ! in_array( 'Hubungi Kami', array_column( $tree, 'title' ), true ), 'trashed bootstrap destination is omitted from fallback navigation' );
assert_true( false === strpos( $service->render_primary(), '/contact-us/' ), 'trashed bootstrap destination never renders a broken fallback link' );
