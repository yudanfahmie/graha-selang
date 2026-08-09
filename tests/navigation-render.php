<?php

define( 'ABSPATH', __DIR__ . '/' );

$GLOBALS['nav_items'] = array();

function get_nav_menu_locations() {
	return array( 'graha-primary' => 99 );
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

require_once dirname( __DIR__ ) . '/src/NavigationService.php';

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
