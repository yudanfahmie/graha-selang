<?php
/**
 * Covers the native WordPress search-results and 404 route presentation:
 * both consume the same branded document shell as any other Page family,
 * search preserves the real WordPress query (no second query/index), 404
 * preserves the real HTTP 404 status with no redirect, and neither invents
 * business facts or product/taxonomy state.
 */

define( 'ABSPATH', __DIR__ . '/' );
define( 'OBJECT', 'OBJECT' );

$GLOBALS['is_admin']        = false;
$GLOBALS['front_page']      = false;
$GLOBALS['search']          = false;
$GLOBALS['not_found']       = false;
$GLOBALS['singular_type']   = null;
$GLOBALS['search_query']    = '';
$GLOBALS['styles']          = array();
$GLOBALS['scripts']         = array();
$GLOBALS['status_header']   = null;
$GLOBALS['pages'] = array(
	'contact-us'    => (object) array( 'ID' => 305, 'post_name' => 'contact-us', 'post_status' => 'publish' ),
	'request-quote' => (object) array( 'ID' => 306, 'post_name' => 'request-quote', 'post_status' => 'publish' ),
);
$GLOBALS['wp_query'] = (object) array( 'posts' => array() );
$GLOBALS['result_posts'] = array(
	701 => array( 'title' => 'Selang Hidrolik MORGEN R1', 'excerpt' => 'Selang hidrolik untuk kebutuhan tekanan tinggi.' ),
	702 => array( 'title' => 'Fitting Kamlok Aluminium', 'excerpt' => 'Fitting cepat sambung untuk aplikasi industri.' ),
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
function is_search() { return $GLOBALS['search']; }
function is_404() { return $GLOBALS['not_found']; }
function is_singular( $types = null ) {
	$type = $GLOBALS['singular_type'];
	if ( ! $type ) return false;
	if ( null === $types ) return true;
	return in_array( $type, (array) $types, true );
}
function get_search_query() { return $GLOBALS['search_query']; }
function get_the_excerpt( $id ) { return isset( $GLOBALS['result_posts'][ $id ] ) ? $GLOBALS['result_posts'][ $id ]['excerpt'] : ''; }
function get_the_title( $id = 0 ) { return isset( $GLOBALS['result_posts'][ $id ] ) ? $GLOBALS['result_posts'][ $id ]['title'] : ''; }
function get_permalink( $target ) {
	$id = is_object( $target ) ? (int) $target->ID : (int) $target;
	if ( isset( $GLOBALS['result_posts'][ $id ] ) ) return 'https://example.test/?p=' . $id;
	if ( isset( $GLOBALS['pages'][ 'contact-us' ] ) && $GLOBALS['pages']['contact-us']->ID === $id ) return 'https://example.test/contact-us/';
	if ( isset( $GLOBALS['pages'][ 'request-quote' ] ) && $GLOBALS['pages']['request-quote']->ID === $id ) return 'https://example.test/request-quote/';
	return '';
}
function get_the_posts_pagination( $args = array() ) { return '<nav class="graha-pagination-stub">paginated</nav>'; }
function get_page_by_path( $slug, $output = OBJECT, $type = 'page' ) { return isset( $GLOBALS['pages'][ $slug ] ) ? $GLOBALS['pages'][ $slug ] : null; }
function get_post_type_archive_link( $type ) { return 'graha_product' === $type ? 'https://example.test/products/' : ''; }
function status_header( $code ) { $GLOBALS['status_header'] = $code; }
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
function absint( $value ) { return abs( (int) $value ); }
function wp_get_nav_menu_items() { return array(); }
function register_nav_menus() {}

require_once dirname( __DIR__ ) . '/plugin/graha-selang-site-core/src/AssetService.php';
require_once dirname( __DIR__ ) . '/plugin/graha-selang-site-core/src/NavigationService.php';
require_once dirname( __DIR__ ) . '/plugin/graha-selang-site-core/src/TemplateService.php';

function ok( $condition, $message ) { if ( ! $condition ) { fwrite( STDERR, "FAIL: {$message}\n" ); exit( 1 ); } echo "PASS: {$message}\n"; }

$assets = new \GrahaSelang\AssetService( dirname( __DIR__ ) . '/plugin/graha-selang-site-core/graha-selang.php', '0.8.0' );
$nav = new \GrahaSelang\NavigationService();
$templates = new \GrahaSelang\TemplateService( $assets, $nav );

// resolve_native_template()/prepare_native_presentation(): native search resolves to the plugin-owned search shell.
$GLOBALS['search'] = true;
$resolved = $templates->resolve_native_template( '/theme/search.php' );
ok( false !== strpos( $resolved, 'templates/search.php' ), 'native WordPress search resolves to the plugin-owned search document shell' );
ok( '/theme/search.php' !== $resolved, 'native search is never left on the active theme fallback template' );
$GLOBALS['styles'] = array();
$templates->prepare_native_presentation();
ok( in_array( 'graha-selang-shell', $GLOBALS['styles'], true ), 'native search prepares the full branded shell asset chain' );

// --- 1 & 2: Search uses branded presentation and preserves the real WordPress query (reads $wp_query, no second query). ---
$GLOBALS['search_query']    = 'selang hidrolik';
$GLOBALS['wp_query']->posts = array( 701, 702 );
ob_start(); $templates->output_search_page(); $search_html = (string) ob_get_clean();
ok( false !== strpos( $search_html, '<header class="graha-site-header">' ), 'search results use the branded header' );
ok( false !== strpos( $search_html, '<footer class="graha-site-footer">' ), 'search results use the branded footer' );
ok( 1 === substr_count( $search_html, '<h1>' ), 'search results page preserves exactly one H1' );
ok( false !== strpos( $search_html, 'graha-page--search' ), 'search results resolve to the search family' );
ok( false !== strpos( $search_html, 'Hasil pencarian:' ) && false !== strpos( $search_html, 'selang hidrolik' ), 'search H1 reflects the real search term' );
ok( false !== strpos( $search_html, 'Selang Hidrolik MORGEN R1' ) && false !== strpos( $search_html, 'Fitting Kamlok Aluminium' ), 'search results render the real matched titles from the current native query, not a second query' );
ok( false !== strpos( $search_html, 'Selang hidrolik untuk kebutuhan tekanan tinggi.' ), 'search results render the real matched excerpt' );
ok( false !== strpos( $search_html, 'https://example.test/?p=701' ), 'search result links to the real permalink' );
ok( false !== strpos( $search_html, 'graha-pagination-stub' ), 'search results preserve native WordPress pagination when WordPress provides it' );
ok( 0 === preg_match( '/\d+\s*hasil/i', $search_html ), 'search results never print a fabricated result count' );

// --- 3: Search empty state is deliberate. ---
$GLOBALS['wp_query']->posts = array();
ob_start(); $templates->output_search_page(); $empty_search = (string) ob_get_clean();
ok( false !== strpos( $empty_search, 'Tidak ada hasil ditemukan' ), 'empty search query renders a deliberate, honest empty-result message' );
ok( false === strpos( $empty_search, 'graha-search-results' ), 'empty search state does not render an empty results list container' );
ok( false !== strpos( $empty_search, 'https://example.test/products/' ), 'empty search state still offers a real path back to Products' );
$GLOBALS['search'] = false;

// resolve_native_template()/prepare_native_presentation(): native 404 resolves to the plugin-owned 404 shell.
$GLOBALS['not_found'] = true;
$resolved = $templates->resolve_native_template( '/theme/404.php' );
ok( false !== strpos( $resolved, 'templates/404.php' ), 'native WordPress 404 resolves to the plugin-owned 404 document shell' );
ok( '/theme/404.php' !== $resolved, 'native 404 is never left on the active theme fallback template' );
$GLOBALS['styles'] = array();
$templates->prepare_native_presentation();
ok( in_array( 'graha-selang-shell', $GLOBALS['styles'], true ), 'native 404 prepares the full branded shell asset chain' );

// --- 4 & 5 & 6: 404 uses branded presentation, preserves the real 404 HTTP status, and never redirects. ---
$GLOBALS['status_header'] = null;
ob_start(); $templates->output_not_found_page(); $not_found_html = (string) ob_get_clean();
ok( 404 === $GLOBALS['status_header'], '404 page sends the real HTTP 404 status' );
ok( false !== strpos( $not_found_html, '<header class="graha-site-header">' ), '404 page uses the branded header' );
ok( false !== strpos( $not_found_html, '<footer class="graha-site-footer">' ), '404 page uses the branded footer' );
ok( 1 === substr_count( $not_found_html, '<h1>' ), '404 page preserves exactly one H1' );
ok( false !== strpos( $not_found_html, 'graha-page--not_found' ), '404 page resolves to the not_found family' );
ok( false !== strpos( $not_found_html, 'Halaman Tidak Ditemukan' ), '404 page shows concise Indonesian copy' );
ok( false !== strpos( $not_found_html, 'https://example.test/' ) && false !== strpos( $not_found_html, 'Ke beranda' ), '404 page links to the real Home destination' );
ok( false !== strpos( $not_found_html, 'https://example.test/products/' ), '404 page links to the real Products destination' );
ok( false !== strpos( $not_found_html, 'https://example.test/contact-us/' ), '404 page links to the real Contact destination' );
ok( false !== strpos( $not_found_html, 'https://example.test/request-quote/' ), '404 page links to the real Request Quote destination' );
ok( false === strpos( $not_found_html, 'href="#"' ), '404 page contains no dead "#" link' );

$search_source = file_get_contents( dirname( __DIR__ ) . '/plugin/graha-selang-site-core/src/TemplateService.php' );
foreach ( array( 'wp_redirect', 'wp_safe_redirect', 'template_redirect', 'add_rewrite_rule' ) as $forbidden ) {
	ok( false === strpos( $search_source, $forbidden ), "search/404 presentation introduces no route/redirect primitive: {$forbidden}" );
}
foreach ( array( 'wp_insert_post', 'wp_update_post', 'wp_set_object_terms', 'update_post_meta', 'add_post_meta', 'delete_post_meta' ) as $forbidden ) {
	ok( false === strpos( $search_source, $forbidden ), "search/404 presentation performs no product/taxonomy mutation: {$forbidden}" );
}
foreach ( array( 'tel:', 'mailto:', 'whatsapp', 'wa.me' ) as $invented ) {
	ok( false === stripos( $search_html . $not_found_html, $invented ), "search/404 presentation invents no direct contact fact: {$invented}" );
}
ok( ! isset( \GrahaSelang\TemplateService::STATIC_PAGE_FAMILIES['application'] ), 'no speculative Application route/slug mapping is introduced' );

echo "Search and 404 route presentation checks passed.\n";
