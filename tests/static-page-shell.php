<?php
/**
 * Covers the branded document-shell routing for native WordPress Pages
 * (About/Services/Contact/RFQ and any other Page), added so the Graha
 * plugin's own header/nav/footer presentation applies sitewide rather
 * than only on the front page.
 */

define( 'ABSPATH', __DIR__ . '/' );
define( 'OBJECT', 'OBJECT' );

$GLOBALS['is_admin']      = false;
$GLOBALS['front_page']    = false;
$GLOBALS['singular_type'] = 'page';
$GLOBALS['queried_id']    = 301;
$GLOBALS['options']       = array( 'show_on_front' => 'page', 'page_on_front' => 50 );
$GLOBALS['styles']        = array();
$GLOBALS['scripts']       = array();

$GLOBALS['posts'] = array(
	50  => array( 'slug' => 'home', 'title' => 'Home', 'status' => 'publish', 'content' => '', 'ancestors' => array() ),
	301 => array( 'slug' => 'about-us', 'title' => 'Tentang Kami Graha', 'status' => 'publish', 'content' => '', 'ancestors' => array() ),
	302 => array( 'slug' => 'kebijakan-privasi', 'title' => 'Kebijakan Privasi', 'status' => 'publish', 'content' => '<p>Isi kebijakan yang dipublikasikan.</p>', 'ancestors' => array( 300 ) ),
	300 => array( 'slug' => 'legal', 'title' => 'Legal', 'status' => 'publish', 'content' => '', 'ancestors' => array() ),
	303 => array( 'slug' => 'draft-page', 'title' => 'Draft', 'status' => 'draft', 'content' => '', 'ancestors' => array() ),
	304 => array( 'slug' => 'layanan-kami', 'title' => 'Layanan Kami', 'status' => 'publish', 'content' => '', 'ancestors' => array() ),
	305 => array( 'slug' => 'contact-us', 'title' => 'Contact Us', 'status' => 'publish', 'content' => '', 'ancestors' => array() ),
	306 => array( 'slug' => 'request-quote', 'title' => 'Request Quote', 'status' => 'publish', 'content' => '', 'ancestors' => array() ),
);

function add_action() {}
function add_filter() {}
function remove_filter() {}
function plugin_dir_url() { return 'https://example.test/plugin/'; }
function wp_register_style() {}
function wp_register_script() {}
function wp_enqueue_style( $handle ) { $GLOBALS['styles'][] = $handle; }
function wp_enqueue_script( $handle ) { $GLOBALS['scripts'][] = $handle; }
function is_admin() { return $GLOBALS['is_admin']; }
function is_front_page() { return $GLOBALS['front_page']; }
function is_singular( $types = null ) {
	$type = $GLOBALS['singular_type'];
	if ( ! $type ) return false;
	if ( null === $types ) return true;
	return in_array( $type, (array) $types, true );
}
function in_the_loop() { return true; }
function is_main_query() { return true; }
function get_option( $key, $default = false ) { return array_key_exists( $key, $GLOBALS['options'] ) ? $GLOBALS['options'][ $key ] : $default; }
function absint( $value ) { return abs( (int) $value ); }
function get_queried_object_id() { return $GLOBALS['queried_id']; }
function get_the_ID() { return $GLOBALS['queried_id']; }
function get_post_status( $id ) { return isset( $GLOBALS['posts'][ $id ] ) ? $GLOBALS['posts'][ $id ]['status'] : false; }
function get_the_title( $id = 0 ) { $id = $id ? (int) $id : $GLOBALS['queried_id']; return isset( $GLOBALS['posts'][ $id ] ) ? $GLOBALS['posts'][ $id ]['title'] : ''; }
function get_post_field( $field, $id ) {
	if ( ! isset( $GLOBALS['posts'][ $id ] ) ) return '';
	if ( 'post_name' === $field ) return $GLOBALS['posts'][ $id ]['slug'];
	if ( 'post_content' === $field ) return $GLOBALS['posts'][ $id ]['content'];
	return '';
}
function get_post_ancestors( $id ) { return isset( $GLOBALS['posts'][ $id ] ) ? $GLOBALS['posts'][ $id ]['ancestors'] : array(); }
function get_permalink( $target ) {
	$id = is_object( $target ) ? (int) $target->ID : (int) $target;
	return isset( $GLOBALS['posts'][ $id ] ) ? 'https://example.test/' . $GLOBALS['posts'][ $id ]['slug'] . '/' : '';
}
function get_page_by_path( $slug, $output = OBJECT, $type = 'page' ) {
	foreach ( $GLOBALS['posts'] as $id => $post ) {
		if ( $post['slug'] === $slug ) return (object) array( 'ID' => $id, 'post_name' => $slug, 'post_status' => $post['status'] );
	}
	return null;
}
function get_post_type_archive_link( $type ) { return 'graha_product' === $type ? 'https://example.test/products/' : ''; }
function post_type_exists() { return false; }
function get_posts() { return array(); }
function get_post_meta() { return ''; }
function apply_filters( $hook, $value ) { return $value; }
function wp_strip_all_tags( $value ) { return strip_tags( (string) $value ); }
function wp_kses_post( $value ) { return (string) $value; }
function sanitize_key( $value ) { return preg_replace( '/[^a-z0-9_-]/', '', strtolower( (string) $value ) ); }
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

require_once dirname( __DIR__ ) . '/plugin/graha-selang-site-core/src/AssetService.php';
require_once dirname( __DIR__ ) . '/plugin/graha-selang-site-core/src/NavigationService.php';
require_once dirname( __DIR__ ) . '/plugin/graha-selang-site-core/src/TemplateService.php';

function ok( $condition, $message ) { if ( ! $condition ) { fwrite( STDERR, "FAIL: {$message}\n" ); exit( 1 ); } echo "PASS: {$message}\n"; }

$assets = new \GrahaSelang\AssetService( dirname( __DIR__ ) . '/plugin/graha-selang-site-core/graha-selang.php', '0.7.0' );
$nav = new \GrahaSelang\NavigationService();
$templates = new \GrahaSelang\TemplateService( $assets, $nav );

// resolve_native_template(): a singular Page (not the front page) resolves to the plugin-owned page.php shell.
$GLOBALS['front_page'] = false; $GLOBALS['singular_type'] = 'page';
$resolved = $templates->resolve_native_template( '/theme/page.php' );
ok( false !== strpos( $resolved, 'templates/page.php' ), 'singular Page resolves to plugin-owned document shell' );

// resolve_native_template(): a genuinely custom (non-Graha) static front Page is left untouched.
$GLOBALS['front_page'] = true; $GLOBALS['options']['page_on_front'] = 999; $GLOBALS['posts'][999] = array( 'slug' => 'custom-front', 'title' => 'Custom', 'status' => 'publish', 'content' => '', 'ancestors' => array() );
ok( '/theme/page.php' === $templates->resolve_native_template( '/theme/page.php' ), 'established custom front Page is not hijacked' );
$GLOBALS['front_page'] = false; $GLOBALS['options']['page_on_front'] = 50;

// resolve_native_template(): posts/products keep the active theme template.
$GLOBALS['singular_type'] = 'post';
ok( '/theme/single.php' === $templates->resolve_native_template( '/theme/single.php' ), 'singular post keeps the active theme template' );
$GLOBALS['singular_type'] = 'page';

// prepare_native_presentation(): Pages get the full branded shell chain; posts/products stay on foundation only.
$GLOBALS['styles'] = array(); $GLOBALS['scripts'] = array();
$templates->prepare_native_presentation();
ok( array( 'graha-selang-tokens','graha-selang-foundation','graha-selang-navigation','graha-selang-shell' ) === $GLOBALS['styles'], 'singular Page enqueues the full branded shell chain' );

$GLOBALS['styles'] = array(); $GLOBALS['scripts'] = array(); $GLOBALS['singular_type'] = 'post';
$templates->prepare_native_presentation();
ok( array( 'graha-selang-tokens','graha-selang-foundation' ) === $GLOBALS['styles'], 'singular post keeps the narrower foundation-only chain' );
$GLOBALS['singular_type'] = 'page';

// render_static_page() via output_static_page(): About maps to the 'about' family with hero/eyebrow/breadcrumbs and full shell chrome.
$GLOBALS['queried_id'] = 301;
ob_start(); $templates->output_static_page(); $about = (string) ob_get_clean();
ok( '' !== $about, 'About Page renders a non-empty branded shell' );
ok( false !== strpos( $about, '<header class="graha-site-header">' ), 'static Page shell includes the branded header' );
ok( false !== strpos( $about, '<footer class="graha-site-footer">' ), 'static Page shell includes the branded footer' );
ok( 1 === substr_count( $about, '<h1>' ), 'static Page shell preserves exactly one H1' );
ok( false !== strpos( $about, 'Tentang Kami Graha' ), 'static Page shell uses the real native Page title, not an invented one' );
ok( false !== strpos( $about, 'graha-page--about' ), 'About Page resolves to the about family' );
ok( false !== strpos( $about, 'Tentang Graha Selang' ), 'empty About Page content still renders the safe bootstrap fallback' );

// An unmapped native Page slug still renders, defaulting to the generic 'legal' family, with real breadcrumb ancestry.
$GLOBALS['queried_id'] = 302;
ob_start(); $templates->output_static_page(); $legal = (string) ob_get_clean();
ok( false !== strpos( $legal, 'graha-page--legal' ), 'unmapped Page slug defaults to the generic legal family' );
ok( false !== strpos( $legal, 'Isi kebijakan yang dipublikasikan.' ), 'unmapped Page keeps its real editor content' );
ok( false !== strpos( $legal, '>Legal</a>' ), 'unmapped Page breadcrumb includes its real native ancestor' );

// A draft/unpublished Page never renders as if it were live.
$GLOBALS['queried_id'] = 303;
ob_start(); $templates->output_static_page(); $draft = (string) ob_get_clean();
ok( '' === $draft, 'unpublished Page renders nothing rather than a soft/fake page' );
$GLOBALS['queried_id'] = 301;

// Services/Contact/RFQ each resolve to their own distinct family (not silently sharing About's).
$GLOBALS['queried_id'] = 304; ob_start(); $templates->output_static_page(); $service = (string) ob_get_clean();
ok( false !== strpos( $service, 'graha-page--service' ), 'Services Page resolves to the service family' );
$GLOBALS['queried_id'] = 305; ob_start(); $templates->output_static_page(); $contact = (string) ob_get_clean();
ok( false !== strpos( $contact, 'graha-page--contact' ), 'Contact Page resolves to its own contact family' );
$GLOBALS['queried_id'] = 306; ob_start(); $templates->output_static_page(); $rfq = (string) ob_get_clean();
ok( false !== strpos( $rfq, 'graha-page--technical_rfq' ), 'Request Quote Page resolves to the technical_rfq family' );
ok( false !== strpos( $rfq, 'https://example.test/request-quote/' ), 'static Page header exposes a working Request Quote CTA' );

// Each family carries its own distinct composition marker, not merely a different graha-page--{family} CSS class.
ok( false !== strpos( $about, 'class="graha-about-editorial' ), 'About wraps its editorial body in a distinct about-specific marker' );
ok( false !== strpos( $about, 'class="graha-about-nextsteps"' ), 'About renders its own honest next-steps area (Layanan + Request Quote both exist)' );
ok( false !== strpos( $service, 'class="graha-service-capability' ), 'Services wraps its body in a distinct service-specific marker' );
ok( false !== strpos( $service, 'class="graha-service-nextsteps"' ), 'Services renders its own honest next-steps area (Produk + Request Quote both exist)' );
ok( false !== strpos( $contact, 'class="graha-contact-card' ), 'Contact presents its content in a distinct contact-card marker' );
ok( false !== strpos( $contact, 'class="graha-contact-alternative"' ), 'Contact offers a distinct RFQ-alternative marker, not a next-steps grid' );
ok( false !== strpos( $rfq, 'class="graha-rfq-form-region' ), 'Request Quote wraps its body in a distinct form-region marker' );
foreach ( array( 'graha-about-editorial', 'graha-service-capability', 'graha-contact-card', 'graha-rfq-form-region' ) as $marker ) {
	$total = substr_count( $about, $marker ) + substr_count( $service, $marker ) + substr_count( $contact, $marker ) + substr_count( $rfq, $marker );
	ok( 1 === $total, "composition marker '{$marker}' appears in exactly one family, not shared generically" );
}

// Request Quote: provider/editor content remains authoritative and untouched; only its container is styled.
// No fabricated recipient/upload/CRM/provider behavior is ever introduced around it.
$GLOBALS['queried_id'] = 306;
$GLOBALS['posts'][306]['content'] = '<div class="wpcf7"><form action="/quote" method="post"><input type="text" name="your-name"><button type="submit">Kirim</button></form></div>';
ob_start(); $templates->output_static_page(); $rfq_live = (string) ob_get_clean();
$GLOBALS['posts'][306]['content'] = '';
ok( false !== strpos( $rfq_live, 'class="wpcf7"' ) && false !== strpos( $rfq_live, '<form action="/quote"' ), 'Request Quote provider/editor markup is preserved verbatim, not replaced' );
$wrapper_pos = strpos( $rfq_live, 'graha-rfq-form-region' );
$form_pos = strpos( $rfq_live, 'class="wpcf7"' );
ok( false !== $wrapper_pos && false !== $form_pos && $wrapper_pos < $form_pos, 'the form-region wrapper contains, rather than replaces, the real provider markup' );
ok( false === strpos( $rfq_live, 'Konsultasi kebutuhan teknis' ), 'real Request Quote content suppresses the empty-state bootstrap fallback copy' );
foreach ( array( 'upload', 'whatsapp', 'recipient', 'cc:', 'bcc:' ) as $invented ) {
	ok( false === stripos( $rfq_live, $invented ), "Request Quote composition invents no '{$invented}' provider behavior" );
}

// Contact: real editor/contact content stays authoritative; the RFQ-alternative is additive, not a replacement.
$GLOBALS['queried_id'] = 305;
$GLOBALS['posts'][305]['content'] = '<p>Kantor Graha Selang buka Senin sampai Jumat.</p>';
ob_start(); $templates->output_static_page(); $contact_live = (string) ob_get_clean();
$GLOBALS['posts'][305]['content'] = '';
ok( false !== strpos( $contact_live, 'Kantor Graha Selang buka Senin sampai Jumat.' ), 'real Contact editor content remains authoritative' );
ok( false === strpos( $contact_live, 'Hubungi Graha Selang</h2>' ), 'real Contact content suppresses the empty-state bootstrap fallback heading' );
ok( false === stripos( $contact_live, 'mailto:' ) && false === stripos( $contact_live, 'tel:' ), 'Contact composition invents no phone/email contact fact' );

echo "Static Page shell routing checks passed.\n";
