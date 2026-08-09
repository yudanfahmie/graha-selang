<?php

define( 'ABSPATH', __DIR__ . '/' );
define( 'OBJECT', 'OBJECT' );

$GLOBALS['options']    = array();
$GLOBALS['posts']      = array();
$GLOBALS['meta']       = array();
$GLOBALS['next_id']    = 1;
$GLOBALS['save_count'] = 0;
$GLOBALS['fail_at']    = 0;

class WP_Error {
	private $message;
	public function __construct( $code, $message ) { $this->message = $message; }
	public function get_error_message() { return $this->message; }
}
function is_wp_error( $value ) { return $value instanceof WP_Error; }
function plugin_dir_path( $file ) { return dirname( $file ) . '/'; }
function trailingslashit( $value ) { return rtrim( $value, '/\\' ) . '/'; }
function wp_strip_all_tags( $value ) { return strip_tags( $value ); }
function sanitize_title( $value ) { $value = strtolower( trim( $value ) ); $value = preg_replace( '/[^a-z0-9]+/', '-', $value ); return trim( $value, '-' ); }
function sanitize_key( $value ) { return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( $value ) ); }
function esc_url_raw( $value ) { return filter_var( $value, FILTER_SANITIZE_URL ); }
function get_option( $key, $default = array() ) { return isset( $GLOBALS['options'][ $key ] ) ? $GLOBALS['options'][ $key ] : $default; }
function update_option( $key, $value, $autoload = false ) { $GLOBALS['options'][ $key ] = $value; return true; }
function delete_option( $key ) { unset( $GLOBALS['options'][ $key ] ); return true; }
function add_option( $key, $value ) { if ( isset( $GLOBALS['options'][ $key ] ) ) return false; $GLOBALS['options'][ $key ] = $value; return true; }
function wp_generate_uuid4() { return uniqid( 'uuid-', true ); }
function get_post_meta( $id, $key, $single = true ) { return isset( $GLOBALS['meta'][ $id ][ $key ] ) ? $GLOBALS['meta'][ $id ][ $key ] : ''; }
function update_post_meta( $id, $key, $value ) { $GLOBALS['meta'][ $id ][ $key ] = $value; return true; }
function get_posts( $args ) {
	$out = array();
	foreach ( $GLOBALS['posts'] as $id => $post ) {
		if ( 'product' !== ( isset( $args['post_type'] ) ? $args['post_type'] : '' ) ) continue;
		if ( isset( $args['meta_key'] ) && ( isset( $GLOBALS['meta'][ $id ][ $args['meta_key'] ] ) ? $GLOBALS['meta'][ $id ][ $args['meta_key'] ] : null ) !== ( isset( $args['meta_value'] ) ? $args['meta_value'] : null ) ) continue;
		if ( isset( $args['title'] ) && $post['name'] !== $args['title'] ) continue;
		$out[] = $id;
		if ( count( $out ) >= ( isset( $args['numberposts'] ) ? $args['numberposts'] : 99 ) ) break;
	}
	return $out;
}
function get_page_by_path( $slug, $output, $type ) {
	foreach ( $GLOBALS['posts'] as $id => $post ) {
		if ( $post['slug'] === $slug ) return (object) array( 'ID' => $id, 'post_title' => $post['name'] );
	}
	return null;
}
function wc_get_product( $id ) { return isset( $GLOBALS['posts'][ $id ] ) ? new WC_Product_Simple( $id ) : false; }
class WC_Product_Simple {
	private $id = 0;
	private $name = '';
	private $slug = '';
	private $status = 'publish';
	public function __construct( $id = 0 ) { $this->id = (int) $id; if ( $id && isset( $GLOBALS['posts'][ $id ] ) ) { $post = $GLOBALS['posts'][ $id ]; $this->name = $post['name']; $this->slug = $post['slug']; $this->status = $post['status']; } }
	public function set_name( $value ) { $this->name = $value; }
	public function set_slug( $value ) { $this->slug = $value; }
	public function set_status( $value ) { $this->status = $value; }
	public function get_name() { return $this->name; }
	public function save() {
		$GLOBALS['save_count']++;
		if ( $GLOBALS['fail_at'] && $GLOBALS['save_count'] === $GLOBALS['fail_at'] ) throw new RuntimeException( 'simulated partial failure' );
		if ( ! $this->id ) $this->id = $GLOBALS['next_id']++;
		$GLOBALS['posts'][ $this->id ] = array( 'name' => $this->name, 'slug' => $this->slug, 'status' => $this->status );
		return $this->id;
	}
}

require_once dirname( __DIR__ ) . '/src/ProductCatalogMigration.php';

function assert_true( $condition, $message ) {
	if ( ! $condition ) { fwrite( STDERR, "FAIL: {$message}\n" ); exit( 1 ); }
	echo "PASS: {$message}\n";
}
function temp_root() {
	$root = sys_get_temp_dir() . '/graha-product-migration-' . uniqid();
	mkdir( $root, 0777, true );
	return $root;
}
function runtime_path( $root ) { return $root . '/migration-runtime/product-catalog-v1'; }
function restore_runtime( $root ) {
	$source = dirname( __DIR__ ) . '/migration-source/product-catalog-v1';
	$runtime = runtime_path( $root );
	if ( ! is_dir( $runtime ) ) mkdir( $runtime, 0777, true );
	copy( $source . '/manifest.json', $runtime . '/manifest.json' );
	copy( $source . '/products.json', $runtime . '/products.json' );
	return $runtime;
}
function remove_tree( $path ) {
	if ( ! is_dir( $path ) ) return;
	$items = array_diff( scandir( $path ), array( '.', '..' ) );
	foreach ( $items as $item ) {
		$child = $path . '/' . $item;
		is_dir( $child ) ? remove_tree( $child ) : @unlink( $child );
	}
	@rmdir( $path );
}
function reset_store( $root, $with_bundle = true ) {
	$GLOBALS['options'] = array();
	$GLOBALS['posts'] = array();
	$GLOBALS['meta'] = array();
	$GLOBALS['next_id'] = 1;
	$GLOBALS['save_count'] = 0;
	$GLOBALS['fail_at'] = 0;
	remove_tree( $root . '/migration-runtime' );
	if ( $with_bundle ) restore_runtime( $root );
}

$root   = temp_root();
$plugin = $root . '/graha-selang.php';

reset_store( $root );
$migration = new \GrahaSelang\ProductCatalogMigration( $plugin );
$summary = $migration->get_summary();
assert_true( 'pending' === $summary['detection'] && 44 === $summary['expected_records'], 'valid bundle detected as pending with 44 records' );
$validated = $migration->validate_bundle();
assert_true( 44 === count( $validated['products'] ), 'manifest checksum and product count validate' );

$manifest_path = runtime_path( $root ) . '/manifest.json';
$manifest = json_decode( file_get_contents( $manifest_path ), true );
unset( $manifest['checksums'] );
file_put_contents( $manifest_path, json_encode( $manifest ) );
$migration = new \GrahaSelang\ProductCatalogMigration( $plugin );
$summary = $migration->get_summary();
assert_true( 'invalid' === $summary['detection'] && ! $migration->should_show_menu(), 'structurally corrupt manifest does not expose temporary submenu' );

reset_store( $root );
file_put_contents( runtime_path( $root ) . '/products.json', "corrupt\n", FILE_APPEND );
$migration = new \GrahaSelang\ProductCatalogMigration( $plugin );
try { $migration->validate_bundle(); assert_true( false, 'checksum mismatch should reject' ); }
catch ( RuntimeException $error ) { assert_true( false !== strpos( $error->getMessage(), 'Checksum' ), 'payload checksum mismatch rejected before writes' ); }
assert_true( 0 === count( $GLOBALS['posts'] ), 'corrupt bundle performs no product write' );

reset_store( $root );
$migration = new \GrahaSelang\ProductCatalogMigration( $plugin );
$GLOBALS['fail_at'] = 10;
try { $migration->execute(); assert_true( false, 'partial failure should throw' ); }
catch ( RuntimeException $error ) { assert_true( false !== strpos( $error->getMessage(), 'simulated' ), 'partial failure surfaced' ); }
assert_true( 9 === count( $GLOBALS['posts'] ), 'partial failure writes only completed products' );
assert_true( 'failed' === $migration->get_state()['status'], 'partial failure state is retryable failed' );

$GLOBALS['fail_at'] = 0;
$GLOBALS['save_count'] = 0;
$result = $migration->execute();
assert_true( 'consumed' === $result['status'], 'retry reaches consumed' );
assert_true( 44 === count( $GLOBALS['posts'] ), 'retry reconciles to exactly 44 products without duplicate' );
assert_true( ! is_dir( $migration->runtime_dir() ), 'verified success cleans disposable runtime bundle' );
assert_true( is_dir( dirname( __DIR__ ) . '/migration-source/product-catalog-v1' ), 'permanent repository archive remains after cleanup' );
try { $migration->execute(); assert_true( false, 'consumed bundle rerun should fail' ); }
catch ( RuntimeException $error ) { assert_true( false !== strpos( $error->getMessage(), 'sudah dikonsumsi' ), 'consumed state blocks rerun' ); }

reset_store( $root );
$GLOBALS['options'][ \GrahaSelang\ProductCatalogMigration::LOCK_OPTION ] = array( 'token' => 'active', 'created_at' => time() );
$migration = new \GrahaSelang\ProductCatalogMigration( $plugin );
try { $migration->execute(); assert_true( false, 'active lock should reject concurrent run' ); }
catch ( RuntimeException $error ) { assert_true( false !== strpos( $error->getMessage(), 'request lain' ), 'active lock blocks concurrent execution' ); }

reset_store( $root );
$migration = new \GrahaSelang\ProductCatalogMigration( $plugin );
$property = new ReflectionProperty( $migration, 'bundle' );
$property->setAccessible( true );
$bundle = $property->getValue( $migration );
$method = new ReflectionMethod( $bundle, 'confined_file' );
$method->setAccessible( true );
try { $method->invoke( $bundle, '../graha-selang.php' ); assert_true( false, 'path traversal should reject' ); }
catch ( Throwable $error ) { assert_true( false !== strpos( $error->getMessage(), 'tidak aman' ), 'path traversal rejected' ); }

reset_store( $root );
$runtime = runtime_path( $root );
file_put_contents( $runtime . '/unexpected.txt', 'must remain' );
$migration = new \GrahaSelang\ProductCatalogMigration( $plugin );
$result = $migration->execute();
assert_true( 'consumed' === $result['status'] && 'failed' === $result['cleanup'], 'cleanup failure preserves logical consumed state' );
assert_true( is_file( $runtime . '/unexpected.txt' ), 'cleanup does not recursively delete undeclared file' );
assert_true( ! $migration->should_show_menu(), 'consumed state hides migration menu despite cleanup failure' );
try { $migration->execute(); assert_true( false, 'cleanup-failed consumed state should not rerun' ); }
catch ( RuntimeException $error ) { assert_true( true, 'cleanup-failed consumed state remains non-rerunnable' ); }

reset_store( $root );
$GLOBALS['posts'][1] = array( 'name' => 'Sunflex AH600 Air Hose 600 PSI', 'slug' => 'existing-different', 'status' => 'publish' );
$GLOBALS['meta'][1][ \GrahaSelang\ProductCatalogMigration::SOURCE_META ] = 'graha-public-product:another-source';
$GLOBALS['next_id'] = 2;
$migration = new \GrahaSelang\ProductCatalogMigration( $plugin );
try { $migration->execute(); assert_true( false, 'existing same title with another source identity should collide' ); }
catch ( RuntimeException $error ) { assert_true( false !== strpos( $error->getMessage(), 'source identity lain' ), 'target identity collision rejected' ); }

reset_store( $root, false );
$migration = new \GrahaSelang\ProductCatalogMigration( $plugin );
$summary = $migration->get_summary();
assert_true( 'none' === $summary['detection'] && ! $migration->should_show_menu(), 'no bundle means no migration submenu' );

remove_tree( $root );
