<?php

define( 'ABSPATH', __DIR__ . '/' );
$GLOBALS['actions']    = array();
$GLOBALS['post_types'] = array();
$GLOBALS['taxonomies'] = array();

function add_action( $hook, $callback, $priority = 10 ) { $GLOBALS['actions'][ $hook ] = array( $callback, $priority ); }
function __( $value ) { return $value; }
function register_post_type( $type, $args ) { $GLOBALS['post_types'][ $type ] = $args; }
function register_taxonomy( $taxonomy, $objects, $args ) { $GLOBALS['taxonomies'][ $taxonomy ] = array( 'objects' => $objects, 'args' => $args ); }

require_once dirname( __DIR__ ) . '/plugin/graha-selang-site-core/src/ProductContentService.php';

function assert_true( $condition, $message ) {
	if ( ! $condition ) { fwrite( STDERR, "FAIL: {$message}\n" ); exit( 1 ); }
	echo "PASS: {$message}\n";
}

$service = new \GrahaSelang\ProductContentService();
$service->register();
assert_true( isset( $GLOBALS['actions']['init'] ) && 5 === $GLOBALS['actions']['init'][1], 'native product model registers on early init' );

$service->register_content_model();
$product = $GLOBALS['post_types']['graha_product'];
assert_true( 'products' === $product['has_archive'], 'graha_product archive is /products/' );
assert_true( 'product' === $product['rewrite']['slug'] && false === $product['rewrite']['with_front'], 'graha_product single route is /product/{slug}/' );
assert_true( true === $product['show_ui'] && false === $product['show_in_menu'], 'native product CRUD stays WordPress-owned without a second root menu' );
assert_true( 'post' === $product['capability_type'] && true === $product['map_meta_cap'], 'graha_product uses native WordPress post capabilities' );

$category = $GLOBALS['taxonomies']['graha_product_category'];
$brand    = $GLOBALS['taxonomies']['graha_product_brand'];
assert_true( array( 'graha_product' ) === $category['objects'] && 'product-category' === $category['args']['rewrite']['slug'] && true === $category['args']['hierarchical'], 'native product category preserves /product-category/{slug}/ hierarchy' );
assert_true( array( 'graha_product' ) === $brand['objects'] && 'brand' === $brand['args']['rewrite']['slug'], 'native brand preserves /brand/{slug}/' );
