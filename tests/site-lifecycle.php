<?php

define( 'ABSPATH', __DIR__ . '/' );
define( 'OBJECT', 'OBJECT' );

$GLOBALS['options'] = array( 'show_on_front' => 'posts', 'page_on_front' => 0, 'fresh_site' => 1 );
$GLOBALS['posts'] = array();
$GLOBALS['next_id'] = 1;
$GLOBALS['flush_count'] = 0;
$GLOBALS['caps'] = array( 'manage_options' => true );
$GLOBALS['actions'] = array();
$GLOBALS['fail_slug'] = '';

class WP_Post {
	public $ID; public $post_type='page'; public $post_status='publish'; public $post_title=''; public $post_name=''; public $post_content='';
	public function __construct($id,$slug,$title,$content='',$status='publish'){ $this->ID=$id;$this->post_name=$slug;$this->post_title=$title;$this->post_content=$content;$this->post_status=$status; }
}
class WP_Error { public function get_error_message(){return 'error';} }
function is_wp_error($v){return $v instanceof WP_Error;}
function absint($v){return abs((int)$v);}
function add_action($hook,$callback,$priority=10,$accepted_args=1){$GLOBALS['actions'][]=$hook;}
function current_user_can($cap){return !empty($GLOBALS['caps'][$cap]);}
function get_option($key,$default=false){return array_key_exists($key,$GLOBALS['options'])?$GLOBALS['options'][$key]:$default;}
function update_option($key,$value,$autoload=false){$GLOBALS['options'][$key]=$value;return true;}
function flush_rewrite_rules($hard=true){$GLOBALS['flush_count']++;}
function get_page_by_path($slug,$output=OBJECT,$type='page'){foreach($GLOBALS['posts'] as $post){if($post->post_type===$type&&$post->post_name===$slug)return $post;}return null;}
function wp_insert_post($data,$wp_error=false){if(!empty($GLOBALS['fail_slug'])&&$GLOBALS['fail_slug']===$data['post_name'])return new WP_Error();$id=$GLOBALS['next_id']++;$post=new WP_Post($id,$data['post_name'],$data['post_title'],$data['post_content']??'',$data['post_status']??'draft');$post->post_type=$data['post_type']??'post';$GLOBALS['posts'][$id]=$post;return $id;}
function get_post($id){return $GLOBALS['posts'][$id]??null;}
function get_post_status($id){$p=get_post($id);return $p?$p->post_status:false;}

require_once dirname(__DIR__).'/plugin/graha-selang-site-core/src/SiteLifecycleService.php';
function ok($c,$m){if(!$c){fwrite(STDERR,"FAIL: $m\n");exit(1);}echo "PASS: $m\n";}

$lifecycle=new \GrahaSelang\SiteLifecycleService();
$lifecycle->register();
ok(in_array('admin_init',$GLOBALS['actions'],true),'lifecycle registers admin_init upgrade hook');
$lifecycle->activate();
ok(5===count($GLOBALS['posts']),'fresh activation provisions five essential Pages exactly once');
$slugs=array_map(fn($p)=>$p->post_name,$GLOBALS['posts']);sort($slugs);
ok($slugs===array('about-us','contact-us','home','layanan-kami','request-quote'),'canonical Page slugs are provisioned');
ok('page'===$GLOBALS['options']['show_on_front'],'fresh site switches to static front page');
$front=get_post($GLOBALS['options']['page_on_front']);ok($front&&'home'===$front->post_name,'provisioned Home becomes front page');
ok(\GrahaSelang\SiteLifecycleService::SCHEMA_VERSION===$GLOBALS['options'][\GrahaSelang\SiteLifecycleService::VERSION_OPTION],'schema version persisted');
ok(1===$GLOBALS['flush_count'],'activation flushes rewrites once');

$lifecycle->activate();
ok(5===count($GLOBALS['posts']),'reactivation is idempotent and creates no duplicate Pages');
ok(2===$GLOBALS['flush_count'],'reactivation performs only activation rewrite flush');
$lifecycle->maybe_upgrade();
ok(2===$GLOBALS['flush_count'],'same schema admin_init performs no provisioning flush');
$lifecycle->deactivate();
ok(2===$GLOBALS['flush_count'],'deactivation does not flush rewrites');

// Existing meaningful editor content is reused untouched.
$about=get_page_by_path('about-us',OBJECT,'page');$about->post_title='Profil Perusahaan';$about->post_content='<p>Isi editor penting.</p>';
$GLOBALS['options'][\GrahaSelang\SiteLifecycleService::VERSION_OPTION]='0';
$lifecycle->maybe_upgrade();
ok('Profil Perusahaan'===$about->post_title&&'<p>Isi editor penting.</p>'===$about->post_content,'schema upgrade preserves existing meaningful Page title/content');
ok(5===count($GLOBALS['posts']),'schema upgrade reuses canonical Pages without duplicates');

// Intentional existing static front page is preserved.
$custom_id=wp_insert_post(array('post_type'=>'page','post_status'=>'publish','post_title'=>'Landing Existing','post_name'=>'landing-existing'),true);
$GLOBALS['options']['show_on_front']='page';$GLOBALS['options']['page_on_front']=$custom_id;$GLOBALS['options'][\GrahaSelang\SiteLifecycleService::VERSION_OPTION]='0';
$lifecycle->maybe_upgrade();
ok($custom_id===$GLOBALS['options']['page_on_front'],'intentional existing static front Page is preserved');

// Established posts-front configuration is not overwritten when site is not fresh.
$GLOBALS['options']['show_on_front']='posts';$GLOBALS['options']['page_on_front']=0;$GLOBALS['options']['fresh_site']=0;$GLOBALS['options'][\GrahaSelang\SiteLifecycleService::VERSION_OPTION]='0';
$lifecycle->maybe_upgrade();
ok('posts'===$GLOBALS['options']['show_on_front']&&0===$GLOBALS['options']['page_on_front'],'established posts-front configuration is preserved');




// Existing unpublished canonical Page is reused but does not satisfy public-ready schema.
$GLOBALS['posts']=array();$GLOBALS['next_id']=1;$GLOBALS['options']=array('show_on_front'=>'posts','page_on_front'=>0,'fresh_site'=>0,\GrahaSelang\SiteLifecycleService::VERSION_OPTION=>'1');$GLOBALS['fail_slug']='';
foreach(array('home','about-us','layanan-kami','contact-us','request-quote') as $slug){wp_insert_post(array('post_type'=>'page','post_status'=>'publish','post_title'=>$slug,'post_name'=>$slug),true);}
$contact=get_page_by_path('contact-us',OBJECT,'page');$contact->post_status='private';$contact->post_content='<p>Editor-owned contact content.</p>';$before_count=count($GLOBALS['posts']);$before_flush=$GLOBALS['flush_count'];
$lifecycle->maybe_upgrade();
ok($before_count===count($GLOBALS['posts']),'unpublished canonical Page is reused without duplicate insertion');
ok('private'===$contact->post_status&&'<p>Editor-owned contact content.</p>'===$contact->post_content,'unpublished editor-owned Page is not silently published or overwritten');
ok('1'===$GLOBALS['options'][\GrahaSelang\SiteLifecycleService::VERSION_OPTION],'unpublished required Page does not mark bootstrap schema complete');
ok($before_flush===$GLOBALS['flush_count'],'incomplete public bootstrap does not flush upgrade rewrites');
$contact->post_status='publish';$lifecycle->maybe_upgrade();
ok(\GrahaSelang\SiteLifecycleService::SCHEMA_VERSION===$GLOBALS['options'][\GrahaSelang\SiteLifecycleService::VERSION_OPTION],'bootstrap upgrade completes after editor publishes required Page');

// Incomplete schema provisioning remains retryable and does not claim completion.
$GLOBALS['posts']=array();$GLOBALS['next_id']=1;$GLOBALS['options']=array('show_on_front'=>'posts','page_on_front'=>0,'fresh_site'=>1,\GrahaSelang\SiteLifecycleService::VERSION_OPTION=>'0');$GLOBALS['fail_slug']='contact-us';$before_flush=$GLOBALS['flush_count'];
$lifecycle->maybe_upgrade();
ok('0'===$GLOBALS['options'][\GrahaSelang\SiteLifecycleService::VERSION_OPTION],'failed schema provisioning does not mark lifecycle complete');
ok($before_flush===$GLOBALS['flush_count'],'failed admin upgrade does not flush rewrites');
$GLOBALS['fail_slug']='';$lifecycle->maybe_upgrade();
ok(5===count($GLOBALS['posts'])&&\GrahaSelang\SiteLifecycleService::SCHEMA_VERSION===$GLOBALS['options'][\GrahaSelang\SiteLifecycleService::VERSION_OPTION],'next admin upgrade safely retries incomplete provisioning');

echo "Site lifecycle checks passed.\n";
