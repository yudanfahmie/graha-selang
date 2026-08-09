<?php
/** Covers the Ringkasan presentation readiness signal. */
define( 'ABSPATH', __DIR__ . '/' );
define( 'OBJECT', 'OBJECT' );
class WP_Post { public $ID; public $post_name; public $post_status = 'publish'; public function __construct( $id, $slug, $status = 'publish' ) { $this->ID = $id; $this->post_name = $slug; $this->post_status = $status; } }
$GLOBALS['options'] = array( 'show_on_front' => 'page' );
$GLOBALS['pages'] = array(
	'home' => new WP_Post( 50, 'home' ),
	'about-us' => new WP_Post( 51, 'about-us' ),
	'layanan-kami' => new WP_Post( 52, 'layanan-kami' ),
	'contact-us' => new WP_Post( 53, 'contact-us', 'draft' ),
);
function plugin_dir_url( $file ) { return 'https://example.test/plugin/'; }
function add_action() {}
function add_menu_page() { return 'toplevel_page_graha-selang-content'; }
function add_submenu_page() { return 'graha-selang-content_page_x'; }
function current_user_can( $cap ) { return 'edit_pages' === $cap; }
function wp_register_style() {}
function wp_enqueue_style() {}
function wp_register_script() {}
function wp_enqueue_script() {}
function get_option( $key, $default = false ) { return array_key_exists( $key, $GLOBALS['options'] ) ? $GLOBALS['options'][ $key ] : $default; }
function get_page_by_path( $slug, $output = OBJECT, $type = 'page' ) { return isset( $GLOBALS['pages'][ $slug ] ) ? $GLOBALS['pages'][ $slug ] : null; }
function esc_html( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_html__( $value ) { return esc_html( $value ); }
function esc_attr( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function esc_attr__( $value ) { return esc_attr( $value ); }
function esc_url( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES, 'UTF-8' ); }
function admin_url( $path = '' ) { return 'https://example.test/wp-admin/' . $path; }
function __( $value ) { return $value; }
function wp_die( $message ) { throw new \RuntimeException( (string) $message ); }
require_once dirname( __DIR__ ) . '/plugin/graha-selang-site-core/src/AssetService.php';
require_once dirname( __DIR__ ) . '/plugin/graha-selang-site-core/src/Kernel.php';
require_once dirname( __DIR__ ) . '/plugin/graha-selang-site-core/src/AdminService.php';
function ok( $condition, $message ) { if ( ! $condition ) { fwrite( STDERR, "FAIL: {$message}\n" ); exit( 1 ); } echo "PASS: {$message}\n"; }
$assets = new \GrahaSelang\AssetService( dirname( __DIR__ ) . '/plugin/graha-selang-site-core/graha-selang.php', \GrahaSelang\Kernel::VERSION );
$admin = new \GrahaSelang\AdminService( $assets );
ob_start(); $admin->render_overview(); $html = (string) ob_get_clean();
ok( false !== strpos( $html, \GrahaSelang\Kernel::VERSION ), 'Ringkasan shows live Kernel version' );
ok( false !== strpos( $html, 'Kepemilikan Homepage' ) && false !== strpos( $html, 'Aktif' ), 'Homepage ownership is active' );
ok( false !== strpos( $html, 'Logo header kanonis' ) && false !== strpos( $html, 'Favicon kanonis' ), 'canonical brand asset readiness is visible' );
ok( false !== strpos( $html, 'Presentasi katalog produk' ), 'product presentation ownership is visible' );
ok( substr_count( $html, '>OK</span>' ) >= 2, 'canonical logo and favicon assets report OK' );
ok( false !== strpos( $html, 'Halaman statis' ), 'homepage mode is reported' );
ok( false !== strpos( $html, '3/5' ), 'structural Page availability count reflects publish status' );
echo "Admin presentation status checks passed.\n";
