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
require_once dirname(__DIR__).'/plugin/graha-selang-site-core/src/ProductPresentation.php';
require_once dirname(__DIR__).'/plugin/graha-selang-site-core/src/AdminService.php';
require_once dirname(__DIR__).'/plugin/graha-selang-site-core/src/SiteLifecycleService.php';
require_once dirname(__DIR__).'/plugin/graha-selang-site-core/src/Kernel.php';
$kernel=new \GrahaSelang\Kernel(dirname(__DIR__).'/plugin/graha-selang-site-core/graha-selang.php'); $kernel->boot();
if('' === \GrahaSelang\Kernel::VERSION){fwrite(STDERR,"FAIL: Kernel::VERSION must not be empty\n");exit(1);}
echo 'PASS: Kernel exposes a single authoritative VERSION constant ('.\GrahaSelang\Kernel::VERSION.")\n";
sort($GLOBALS['actions']); sort($GLOBALS['filters']);
$expected=array('admin_enqueue_scripts','admin_init','admin_menu','after_setup_theme','graha_selang_prepare_page','graha_selang_render_breadcrumbs','graha_selang_render_front_page','graha_selang_render_page','graha_selang_render_static_page','graha_selang_render_search_page','graha_selang_render_not_found_page','graha_selang_render_product_view','init','wp_enqueue_scripts','wp_enqueue_scripts','wp_enqueue_scripts','wp_head'); sort($expected);
if($expected!==$GLOBALS['actions']){fwrite(STDERR,'FAIL: unexpected action hooks: '.implode(', ',$GLOBALS['actions'])."\n");exit(1);}
$expected_filters=array('archive_template','single_template','taxonomy_template','template_include','the_content');sort($expected_filters);
if($expected_filters!==$GLOBALS['filters']){fwrite(STDERR,'FAIL: unexpected filters: '.implode(', ',$GLOBALS['filters'])."\n");exit(1);}
if(class_exists('GrahaSelang\\ProductCatalogMigration',false)){fwrite(STDERR,"FAIL: frontend Kernel boot loaded migration coordinator\n");exit(1);}
echo "PASS: Kernel boots native product/presentation/admin/lifecycle owners without frontend migration runtime\n";
