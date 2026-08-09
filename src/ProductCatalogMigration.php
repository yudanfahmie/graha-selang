<?php

namespace GrahaSelang;

defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/ProductCatalogBundle.php';

/** Narrow, non-bootable one-shot Woo product migration coordinator. */
final class ProductCatalogMigration {
	const RUNTIME_RELATIVE_PATH = 'migration-runtime/product-catalog-v1';
	const STATE_OPTION = 'graha_selang_product_catalog_v1_state';
	const LOCK_OPTION = 'graha_selang_product_catalog_v1_lock';
	const SOURCE_META = '_graha_source_identity';
	const BUNDLE_META = '_graha_source_bundle';
	const SOURCE_URL_META = '_graha_source_url';
	const HOME_GROUP_META = '_graha_home_group';
	const LOCK_TTL = 900;

	/** @var ProductCatalogBundle */
	private $bundle;

	public function __construct( $plugin_file ) { $this->bundle = new ProductCatalogBundle( $plugin_file ); }
	public function runtime_dir() { return $this->bundle->runtime_dir(); }
	public function validate_bundle() { return $this->bundle->validate(); }

	/** Cheap state/header detection; no checksum/import work. */
	public function get_summary() {
		$state = $this->get_state();
		if ( 'consumed' === $state['status'] ) return array_merge( array( 'detection'=>'consumed', 'bundle_id'=>$state['bundle_id'] ?? '', 'source_version'=>$state['source_version'] ?? '', 'expected_records'=>(int) ( $state['expected_records'] ?? 0 ) ), $state );
		$manifest = $this->bundle->read_header();
		if ( is_wp_error( $manifest ) ) return array_merge( $state, array( 'detection'=>'invalid', 'message'=>$manifest->get_error_message() ) );
		if ( empty( $manifest ) ) return array_merge( $state, array( 'detection'=>'none' ) );
		$status = $state['status'] ?? 'pending';
		if ( 'running' === $status && ! $this->lock_is_active() ) $status = 'failed';
		return array_merge( $state, array( 'detection'=>$status, 'bundle_id'=>(string) $manifest['bundle_id'], 'source_version'=>(string) $manifest['source_version'], 'expected_records'=>(int) $manifest['expected_records'] ) );
	}

	public function should_show_menu() {
		$summary = $this->get_summary();
		return in_array( $summary['detection'], array( 'pending', 'failed', 'running' ), true );
	}

	/** Execute after AdminService capability+nonce checks. */
	public function execute() {
		$state = $this->get_state();
		if ( 'consumed' === $state['status'] ) throw new \RuntimeException( 'Bundle ini sudah dikonsumsi dan tidak dapat dijalankan kembali.' );
		if ( ! class_exists( 'WC_Product_Simple' ) || ! function_exists( 'wc_get_product' ) ) {
			$state['status']='failed'; $state['message']='WooCommerce tidak tersedia; migrasi produk tidak dijalankan.'; $this->save_state( $state );
			throw new \RuntimeException( $state['message'] );
		}
		$token = $this->acquire_lock();
		if ( '' === $token ) throw new \RuntimeException( 'Migrasi sedang berjalan pada request lain.' );
		try {
			$bundle=$this->bundle->validate(); $manifest=$bundle['manifest']; $products=$bundle['products'];
			$this->save_state( array( 'status'=>'running', 'bundle_id'=>(string) $manifest['bundle_id'], 'source_version'=>(string) $manifest['source_version'], 'expected_records'=>count($products), 'message'=>'' ) );
			$created=0; $updated=0;
			foreach ( $products as $record ) { 'created' === $this->import_product( $record, (string) $manifest['bundle_id'] ) ? $created++ : $updated++; }
			$this->verify_products( $products );
			// Logical consumption is authoritative and persisted before cleanup.
			$this->save_state( array( 'status'=>'consumed', 'bundle_id'=>(string) $manifest['bundle_id'], 'source_version'=>(string) $manifest['source_version'], 'expected_records'=>count($products), 'created'=>$created, 'updated'=>$updated, 'cleanup'=>'pending', 'message'=>'' ) );
			$this->release_lock( $token );
			$cleanup=$this->bundle->cleanup( $manifest ); $state=$this->get_state();
			$state['cleanup']=$cleanup['ok']?'complete':'failed'; $state['cleanup_message']=$cleanup['message']; $this->save_state( $state );
			return $this->get_state();
		} catch ( \Throwable $error ) {
			$this->release_lock( $token ); $failed=$this->get_state();
			if ( 'consumed' !== $failed['status'] ) { $failed['status']='failed'; $failed['message']=$error->getMessage(); $this->save_state( $failed ); }
			throw new \RuntimeException( $error->getMessage(), 0, $error );
		}
	}

	public function get_state() {
		$state=get_option( self::STATE_OPTION, array() ); if ( ! is_array($state) ) $state=array();
		if ( empty($state['status']) ) $state['status']='pending'; return $state;
	}

	private function import_product( array $record, $bundle_id ) {
		$ids=$this->find_by_source_identity($record['source_id']);
		if ( count($ids)>1 ) throw new \RuntimeException( 'Source identity collision di target: '.$record['source_id'] );
		$is_new=false; $id=empty($ids)?0:(int)$ids[0];
		if ( ! $id ) {
			$title_ids=get_posts(array('post_type'=>'product','post_status'=>'any','title'=>$record['name'],'fields'=>'ids','numberposts'=>2,'suppress_filters'=>true));
			if ( count($title_ids)>1 ) throw new \RuntimeException( 'Lebih dari satu product target memiliki judul yang sama: '.$record['name'] );
			if ( ! empty($title_ids) ) { $id=(int)$title_ids[0]; $existing=(string)get_post_meta($id,self::SOURCE_META,true); if ( ''!==$existing && $record['source_id']!==$existing ) throw new \RuntimeException('Judul target sudah dimiliki source identity lain: '.$record['name']); }
		}
		if ( ! $id ) {
			$post=get_page_by_path($record['slug'],OBJECT,'product');
			if ( $post ) { $existing=(string)get_post_meta($post->ID,self::SOURCE_META,true); if ( ''!==$existing && $record['source_id']!==$existing ) throw new \RuntimeException('Slug target sudah dimiliki source identity lain: '.$record['slug']); if ( sanitize_title($post->post_title)!==sanitize_title($record['name']) ) throw new \RuntimeException('Slug target bertabrakan dengan produk berjudul berbeda: '.$record['slug']); $id=(int)$post->ID; }
		}
		$product=$id?wc_get_product($id):new \WC_Product_Simple(); if(!$product) throw new \RuntimeException('WooCommerce gagal membuka product target untuk '.$record['source_id']);
		if(!$id){$is_new=true;$product->set_name($record['name']);$product->set_slug($record['slug']);$product->set_status('publish');} elseif($product->get_name()!==$record['name']){$product->set_name($record['name']);}
		$id=(int)$product->save(); if(!$id) throw new \RuntimeException('WooCommerce gagal menyimpan '.$record['source_id']);
		update_post_meta($id,self::SOURCE_META,$record['source_id']); update_post_meta($id,self::BUNDLE_META,$bundle_id); update_post_meta($id,self::HOME_GROUP_META,$record['home_group']);
		if(''!==$record['source_url']) update_post_meta($id,self::SOURCE_URL_META,$record['source_url']);
		return $is_new?'created':'updated';
	}

	private function verify_products( array $products ) {
		foreach($products as $record){$ids=$this->find_by_source_identity($record['source_id']); if(1!==count($ids)) throw new \RuntimeException('Verifikasi source identity gagal: '.$record['source_id']); $id=(int)$ids[0];$product=wc_get_product($id);if(!$product||$product->get_name()!==$record['name']) throw new \RuntimeException('Verifikasi product gagal: '.$record['source_id']);if((string)get_post_meta($id,self::HOME_GROUP_META,true)!==$record['home_group']) throw new \RuntimeException('Verifikasi home_group gagal: '.$record['source_id']);}
	}

	private function find_by_source_identity( $source_id ) {
		$ids=get_posts(array('post_type'=>'product','post_status'=>'any','meta_key'=>self::SOURCE_META,'meta_value'=>(string)$source_id,'fields'=>'ids','numberposts'=>2,'suppress_filters'=>true));
		return is_array($ids)?array_map('intval',$ids):array();
	}

	private function acquire_lock() {
		$existing=get_option(self::LOCK_OPTION,array()); if(is_array($existing)&&!empty($existing['created_at'])&&(time()-(int)$existing['created_at'])>self::LOCK_TTL) delete_option(self::LOCK_OPTION);
		$token=function_exists('wp_generate_uuid4')?wp_generate_uuid4():uniqid('graha-',true); return add_option(self::LOCK_OPTION,array('token'=>$token,'created_at'=>time()),'',false)?$token:'';
	}
	private function lock_is_active() {$lock=get_option(self::LOCK_OPTION,array());return is_array($lock)&&!empty($lock['created_at'])&&(time()-(int)$lock['created_at'])<=self::LOCK_TTL;}
	private function release_lock($token){$lock=get_option(self::LOCK_OPTION,array());if(is_array($lock)&&isset($lock['token'])&&hash_equals((string)$lock['token'],(string)$token))delete_option(self::LOCK_OPTION);}
	private function save_state(array $state){$state['updated_at']=time();update_option(self::STATE_OPTION,$state,false);}
}
