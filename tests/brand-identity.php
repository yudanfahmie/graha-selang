<?php
/**
 * Covers the deterministic Graha brand-mark hierarchy: real WordPress
 * Custom Logo first, otherwise the bundled Graha Selang SVG mark -- never
 * the bare letter-mark as a normal production fallback -- with no nested
 * anchors and no duplicate accessible name.
 */

define( 'ABSPATH', __DIR__ . '/' );
define( 'OBJECT', 'OBJECT' );

$GLOBALS['custom_logo'] = '';
$GLOBALS['styles']      = array();
$GLOBALS['scripts']     = array();

function add_action() {}
function add_filter() {}
function plugin_dir_url() { return 'https://example.test/plugin/'; }
function wp_register_style() {}
function wp_register_script() {}
function wp_enqueue_style( $handle ) { $GLOBALS['styles'][] = $handle; }
function wp_enqueue_script( $handle ) { $GLOBALS['scripts'][] = $handle; }
function get_nav_menu_locations() { return array(); }
function absint( $value ) { return abs( (int) $value ); }
function wp_get_nav_menu_items() { return array(); }
function register_nav_menus() {}
function __( $text ) { return $text; }
function esc_attr( $text ) { return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' ); }
function esc_attr__( $text ) { return esc_attr( $text ); }
function esc_html( $text ) { return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' ); }
function esc_html__( $text ) { return esc_html( $text ); }
function esc_url( $url ) { return htmlspecialchars( trim( (string) $url ), ENT_QUOTES, 'UTF-8' ); }
function sanitize_key( $key ) { return strtolower( preg_replace( '/[^a-z0-9_\-]/', '', (string) $key ) ); }
function wp_strip_all_tags( $text ) { return strip_tags( (string) $text ); }
function wp_kses_post( $html ) { return (string) $html; }
function get_bloginfo( $show ) { return 'name' === $show ? 'Graha Selang' : ''; }
function home_url( $path = '/' ) { return 'https://example.test' . $path; }
function get_custom_logo() { return $GLOBALS['custom_logo']; }
function get_page_by_path( $slug, $output = OBJECT, $type = 'page' ) { return null; }
function get_permalink( $post ) { return ''; }
function get_post_type_archive_link( $type ) { return 'graha_product' === $type ? 'https://example.test/products/' : ''; }

require_once dirname( __DIR__ ) . '/plugin/graha-selang-site-core/src/AssetService.php';
require_once dirname( __DIR__ ) . '/plugin/graha-selang-site-core/src/NavigationService.php';
require_once dirname( __DIR__ ) . '/plugin/graha-selang-site-core/src/TemplateService.php';

function ok( $condition, $message ) { if ( ! $condition ) { fwrite( STDERR, "FAIL: {$message}\n" ); exit( 1 ); } echo "PASS: {$message}\n"; }

$assets = new \GrahaSelang\AssetService( dirname( __DIR__ ) . '/plugin/graha-selang-site-core/graha-selang.php', '0.7.0' );
$nav = new \GrahaSelang\NavigationService();
$templates = new \GrahaSelang\TemplateService( $assets, $nav );

ok( 'https://example.test/plugin/assets/images/graha-selang-brand-logo.svg' === $assets->bundled_logo_url(), 'bundled logo URL resolves beneath the plugin asset base URL' );

// 1) No Custom Logo configured: the bundled SVG mark renders, decorative, inside one link
// whose only accessible name is the visible site-name text -- not the bare letter square.
$GLOBALS['custom_logo'] = '';
$html = $templates->render_page( 'about', array( 'heading' => 'Tentang', 'content_html' => '<p>Konten.</p>' ) );
ok( false !== strpos( $html, 'graha-selang-brand-logo.svg' ), 'bundled Graha brand SVG renders when no Custom Logo is configured' );
ok( false !== strpos( $html, 'class="graha-site-identity__logo"' ), 'bundled brand mark uses its dedicated sizing class' );
ok( false === strpos( $html, 'class="graha-site-identity__mark"' ), 'the generic one-letter square is not used while the bundled SVG is available' );
ok( 1 === preg_match( '/<img[^>]*graha-site-identity__logo[^>]*alt=""/', $html ), 'bundled brand image is decorative (empty alt) so it carries no competing accessible name' );
ok( false !== strpos( $html, '<span class="graha-site-identity__name">Graha Selang</span>' ), 'the visible site-name text supplies the one accessible name for the fallback identity link' );

// 2) A real Custom Logo overrides the bundled fallback entirely, and is not re-wrapped in a
// second anchor (WordPress' own get_custom_logo() output already is a complete <a><img></a>).
$GLOBALS['custom_logo'] = '<a href="https://example.test/" class="custom-logo-link" rel="home"><img width="180" height="60" src="https://example.test/wp-content/uploads/logo.png" class="custom-logo" alt="Graha Selang"/></a>';
$html = $templates->render_page( 'about', array( 'heading' => 'Tentang', 'content_html' => '<p>Konten.</p>' ) );
ok( false === strpos( $html, 'graha-selang-brand-logo.svg' ), 'configured Custom Logo suppresses the bundled SVG fallback' );
ok( false !== strpos( $html, 'custom-logo-link' ), 'configured Custom Logo output is rendered' );
ok( 1 === preg_match( '/<div class="graha-site-identity">\s*<a[^>]*class="custom-logo-link"/', $html ), 'Custom Logo anchor is not re-wrapped in a second outer anchor (no nested links)' );
ok( false === strpos( $html, '<a href="https://example.test/"><a ' ), 'no literal nested-anchor sequence is produced' );

echo "Brand identity checks passed.\n";
