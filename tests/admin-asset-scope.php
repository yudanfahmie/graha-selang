<?php

define( 'ABSPATH', __DIR__ . '/' );
$GLOBALS['registered_styles']=array(); $GLOBALS['enqueued_styles']=array(); $GLOBALS['menu_calls']=array(); $GLOBALS['submenu_calls']=array();
function plugin_dir_url( $file ) { return 'https://example.test/plugin/'; }
function add_action() {}
function add_menu_page( $page_title,$menu_title,$capability,$slug,$callback,$icon,$position ){ $GLOBALS['menu_calls'][]=func_get_args(); return 'toplevel_page_'.$slug; }
function add_submenu_page( $parent,$page_title,$menu_title,$capability,$slug,$callback ){ $GLOBALS['submenu_calls'][]=func_get_args(); return $parent.'_page_'.$slug; }
function current_user_can( $cap ) { return false; }
function wp_register_style( $handle ) { $GLOBALS['registered_styles'][]=$handle; }
function wp_enqueue_style( $handle ) { $GLOBALS['enqueued_styles'][]=$handle; }
function wp_register_script() {} function wp_enqueue_script() {}
require_once dirname(__DIR__).'/src/AssetService.php'; require_once dirname(__DIR__).'/src/AdminService.php';
function assert_true($c,$m){if(!$c){fwrite(STDERR,"FAIL: $m\n");exit(1);}echo "PASS: $m\n";}
$assets=new \GrahaSelang\AssetService(dirname(__DIR__).'/graha-selang.php','0.4.0');
$admin=new \GrahaSelang\AdminService($assets);
$admin->register_menu();
assert_true(1===count($GLOBALS['menu_calls']),'one root menu registered');
assert_true(1===count($GLOBALS['submenu_calls']),'Ringkasan is the only child for user without migration capability');
assert_true('graha-selang-content'===$GLOBALS['menu_calls'][0][3],'root slug is canonical');
assert_true(3===$GLOBALS['menu_calls'][0][6],'root default position is 3');
$admin->enqueue_assets('dashboard_page_other'); assert_true(0===count($GLOBALS['enqueued_styles']),'unrelated admin screen receives no Graha assets');
$admin->enqueue_assets('toplevel_page_graha-selang-content'); assert_true(array('graha-selang-admin-overview')===$GLOBALS['enqueued_styles'],'Ringkasan screen receives only its stylesheet');
