<?php

define( 'ABSPATH', __DIR__ . '/' );
require_once dirname( __DIR__ ) . '/plugin/graha-selang-site-core/src/NavigationService.php';

function assert_true( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, "FAIL: {$message}\n" );
		exit( 1 );
	}
	echo "PASS: {$message}\n";
}

$root_a                   = new stdClass();
$root_a->ID               = 10;
$root_a->menu_item_parent = 0;
$root_a->title            = 'Produk';
$root_a->url              = '/produk/';
$root_a->target           = '';
$root_a->xfn              = '';
$root_a->current          = false;
$root_a->current_item_ancestor = true;

$child                   = new stdClass();
$child->ID               = 11;
$child->menu_item_parent = 10;
$child->title            = 'Kategori';
$child->url              = '/kategori/';
$child->target           = '_blank';
$child->xfn              = 'nofollow';
$child->current          = true;
$child->current_item_ancestor = false;

$root_b                   = new stdClass();
$root_b->ID               = 12;
$root_b->menu_item_parent = 0;
$root_b->title            = 'Tentang';
$root_b->url              = '/tentang/';
$root_b->target           = '';
$root_b->xfn              = '';
$root_b->current          = false;
$root_b->current_item_ancestor = false;

$service = new \GrahaSelang\NavigationService();
$tree    = $service->normalize_items( array( $root_a, $child, $root_b ) );

assert_true( 2 === count( $tree ), 'two root menu items retained' );
assert_true( 10 === $tree[0]['id'] && 12 === $tree[1]['id'], 'native menu order retained' );
assert_true( 1 === count( $tree[0]['children'] ), 'child item nested under native parent' );
assert_true( 11 === $tree[0]['children'][0]['id'], 'child ID retained' );
assert_true( '_blank' === $tree[0]['children'][0]['target'], 'safe target retained' );
assert_true( true === $tree[0]['children'][0]['current'], 'current state retained' );
assert_true( true === $tree[0]['ancestor'], 'ancestor state retained' );
