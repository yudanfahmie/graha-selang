<?php

define( 'ABSPATH', __DIR__ . '/' );
$GLOBALS['menu_calls']      = array();
$GLOBALS['submenu_calls']   = array();
$GLOBALS['actions']         = array();
$GLOBALS['caps']            = array( 'edit_pages' => true, 'manage_woocommerce' => true );
$GLOBALS['migration_state'] = array();
$GLOBALS['enqueued']        = array();

function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) { $GLOBALS['actions'][ $hook ] = $callback; }
function current_user_can( $cap ) { return ! empty( $GLOBALS['caps'][ $cap ] ); }
function is_admin() { return true; }
function plugin_dir_path( $file ) { return dirname( $file ) . '/'; }
function trailingslashit( $value ) { return rtrim( $value, '/\\' ) . '/'; }
function add_menu_page() { $GLOBALS['menu_calls'][] = func_get_args(); return 'toplevel_page_graha-selang-content'; }
function add_submenu_page() { $GLOBALS['submenu_calls'][] = func_get_args(); $args = func_get_args(); return $args[0] . '_page_' . $args[4]; }
function wp_create_nonce( $action ) { return 'nonce-' . $action; }
function check_ajax_referer( $action, $field, $die = true ) { return ! empty( $GLOBALS['valid_nonce'] ); }
function wp_send_json_error( $data, $status = 400 ) { throw new RuntimeException( 'json-error-' . $status . ':' . $data['message'] ); }
function wp_send_json_success( $data ) { $GLOBALS['ajax_success'] = $data; return null; }
function get_option( $key, $default = array() ) { return isset( $GLOBALS['migration_state'][ $key ] ) ? $GLOBALS['migration_state'][ $key ] : $default; }
function update_option( $key, $value, $autoload = false ) { $GLOBALS['migration_state'][ $key ] = $value; return true; }
function delete_option( $key ) { unset( $GLOBALS['migration_state'][ $key ] ); return true; }
function add_option( $key, $value ) { if ( isset( $GLOBALS['migration_state'][ $key ] ) ) return false; $GLOBALS['migration_state'][ $key ] = $value; return true; }
function is_wp_error( $value ) { return $value instanceof WP_Error; }
class WP_Error { private $message; public function __construct( $code, $message ) { $this->message = $message; } public function get_error_message() { return $this->message; } }
function post_type_exists( $type ) { return false; }
function admin_url( $path ) { return 'https://example.test/wp-admin/' . $path; }
function esc_url( $value ) { return $value; }
function esc_html__( $value ) { return $value; }
function esc_html( $value ) { return htmlspecialchars( $value, ENT_QUOTES ); }
function esc_attr__( $value ) { return $value; }
function esc_attr( $value ) { return htmlspecialchars( $value, ENT_QUOTES ); }
function __( $value ) { return $value; }
function wp_die( $value ) { throw new RuntimeException( $value ); }
function disabled( $condition ) { return $condition ? 'disabled' : ''; }
function wp_register_style() {}
function wp_register_script() {}
function wp_enqueue_style( $handle ) { $GLOBALS['enqueued'][] = $handle; }
function wp_enqueue_script( $handle ) { $GLOBALS['enqueued'][] = $handle; }
function wp_localize_script( $handle, $object, $data ) { $GLOBALS['localized'] = $data; }
function plugin_dir_url( $file ) { return 'https://example.test/plugin/'; }

require_once dirname( __DIR__ ) . '/plugin/graha-selang-site-core/src/AssetService.php';
require_once dirname( __DIR__ ) . '/plugin/graha-selang-site-core/src/AdminService.php';

function assert_true( $condition, $message ) {
	if ( ! $condition ) { fwrite( STDERR, "FAIL: {$message}\n" ); exit( 1 ); }
	echo "PASS: {$message}\n";
}

class FakeMigration {
	private $result;
	public function __construct( array $result ) { $this->result = $result; }
	public function execute() { return $this->result; }
}

$assets = new \GrahaSelang\AssetService( dirname( __DIR__ ) . '/plugin/graha-selang-site-core/graha-selang.php', '0.4.0' );
$admin  = new \GrahaSelang\AdminService( $assets );
$admin->register();
assert_true( isset( $GLOBALS['actions']['wp_ajax_graha_selang_run_product_catalog_migration'] ), 'authenticated migration AJAX hook registered' );
assert_true( ! isset( $GLOBALS['actions']['wp_ajax_nopriv_graha_selang_run_product_catalog_migration'] ), 'no nopriv migration AJAX hook' );

$admin->register_menu();
assert_true( 1 === count( $GLOBALS['menu_calls'] ), 'single canonical Graha root remains' );
assert_true( 2 === count( $GLOBALS['submenu_calls'] ), 'valid pending bundle creates one temporary migration child' );
$migration_hook = $GLOBALS['submenu_calls'][1][0] . '_page_' . $GLOBALS['submenu_calls'][1][4];
$admin->enqueue_assets( $migration_hook );
assert_true( in_array( 'graha-selang-admin-migration', $GLOBALS['enqueued'], true ), 'migration assets load on exact temporary screen' );

$GLOBALS['caps']['manage_woocommerce'] = false;
try { $admin->run_product_migration(); assert_true( false, 'capability should reject' ); }
catch ( RuntimeException $error ) { assert_true( false !== strpos( $error->getMessage(), '403' ), 'migration AJAX requires capability' ); }

$GLOBALS['caps']['manage_woocommerce'] = true;
$GLOBALS['valid_nonce'] = false;
try { $admin->run_product_migration(); assert_true( false, 'nonce should reject' ); }
catch ( RuntimeException $error ) { assert_true( false !== strpos( $error->getMessage(), '403' ), 'migration AJAX requires nonce' ); }

$property = new ReflectionProperty( $admin, 'migration' );
$property->setAccessible( true );
$GLOBALS['valid_nonce'] = true;
$property->setValue( $admin, new FakeMigration( array( 'status' => 'consumed', 'cleanup' => 'complete' ) ) );
$admin->run_product_migration();
assert_true( false !== strpos( $GLOBALS['ajax_success']['message'], 'telah dibersihkan' ), 'AJAX success message confirms runtime cleanup only when cleanup completes' );

$property->setValue( $admin, new FakeMigration( array( 'status' => 'consumed', 'cleanup' => 'failed', 'cleanup_message' => 'read-only' ) ) );
$admin->run_product_migration();
assert_true( false !== strpos( $GLOBALS['ajax_success']['message'], 'pembersihan file runtime gagal' ), 'AJAX success message clearly reports cleanup failure after successful import' );
assert_true( false === strpos( $GLOBALS['ajax_success']['message'], 'telah dibersihkan' ), 'cleanup-failed message never claims runtime files were cleaned' );
$property->setValue( $admin, null );

$GLOBALS['migration_state'][ \GrahaSelang\ProductCatalogMigration::STATE_OPTION ] = array( 'status' => 'consumed', 'cleanup' => 'failed', 'cleanup_message' => 'read-only' );
$GLOBALS['submenu_calls'] = array();
$admin->register_menu();
assert_true( 1 === count( $GLOBALS['submenu_calls'] ), 'consumed state hides temporary migration submenu even when cleanup failed' );
