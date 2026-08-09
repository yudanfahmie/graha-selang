<?php

define( 'ABSPATH', __DIR__ . '/' );
$GLOBALS['actions']=array(); $GLOBALS['filters']=array();
function add_action($hook,$callback,$priority=10,$accepted_args=1){$GLOBALS['actions'][]=$hook;}
function add_filter($hook,$callback,$priority=10,$accepted_args=1){$GLOBALS['filters'][]=$hook;}
function plugin_dir_url($file){return 'https://example.test/plugin/';}
function is_admin(){return false;}
require_once dirname(__DIR__).'/plugin/graha-selang-site-core/src/ProductContentService.php';
require_once dirname(__DIR__).'/plugin/graha-selang-site-core/src/AssetService.php';
require_once dirname(__DIR__).'/plugin/graha-selang-site-core/src/NavigationService.php';
require_once dirname(__DIR__).'/plugin/graha-selang-site-core/src/TemplateService.php';
require_once dirname(__DIR__).'/plugin/graha-selang-site-core/src/AdminService.php';
require_once dirname(__DIR__).'/plugin/graha-selang-site-core/src/SiteLifecycleService.php';
require_once dirname(__DIR__).'/plugin/graha-selang-site-core/src/Kernel.php';
$kernel=new \GrahaSelang\Kernel(dirname(__DIR__).'/plugin/graha-selang-site-core/graha-selang.php','0.6.0'); $kernel->boot();
sort($GLOBALS['actions']); sort($GLOBALS['filters']);
$expected=array('admin_enqueue_scripts','admin_init','admin_menu','after_setup_theme','graha_selang_prepare_page','graha_selang_render_breadcrumbs','graha_selang_render_front_page','graha_selang_render_page','graha_selang_render_static_page','init','wp_enqueue_scripts','wp_enqueue_scripts'); sort($expected);
if($expected!==$GLOBALS['actions']){fwrite(STDERR,'FAIL: unexpected action hooks: '.implode(', ',$GLOBALS['actions'])."\n");exit(1);}
$expected_filters=array('template_include','the_content');sort($expected_filters);
if($expected_filters!==$GLOBALS['filters']){fwrite(STDERR,'FAIL: unexpected filters: '.implode(', ',$GLOBALS['filters'])."\n");exit(1);}
if(class_exists('GrahaSelang\\ProductCatalogMigration',false)){fwrite(STDERR,"FAIL: frontend Kernel boot loaded migration coordinator\n");exit(1);}
echo "PASS: Kernel boots native product/presentation/admin/lifecycle owners without frontend migration runtime\n";
