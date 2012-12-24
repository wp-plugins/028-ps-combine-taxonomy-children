<?php
/*
Plugin Name: 028 ps combine taxonomy children
Plugin URI: http://wordpress.org/extend/plugins/028-ps-combine-taxonomy-children/
Description: Combine Taxonomy Children
Author: Wang Bin (oh@prime-strategy.co.jp)
Version: 1.1
Author URI: http://www.prime-strategy.co.jp/about/staff/oh/
*/

/**
 * ps_combine_taxonomy_children
 *
 * Main combine taxonomy children Class
 *
 * @package ps_combine_taxonomy_children
 */

class ps_combine_taxonomy_children{

	/*
	*設定ファイルの読み込みフラグ
	*/
	var $include_items_flag = false;

	/*
	*Start transfer by ftp  on plugins loaded
	*/
	function ps_combine_taxonomy_children( ){
		$this->__construct( );
	}

	/*
	 * コンストラクタ.
	 */
	function __construct( ) {
		$this->init( );
			
	}

	/**
	* ファンクション名：set_items
	* 機能概要：設定変数を設定
	*/
	function set_items( $_items ){
		$this->_items = $_items;
	}
	
	/**
	* ファンクション名：get_items
	* 機能概要：設定変数を取得
	*/
	function get_items( ){
		return $this->_items;
	}
	
	/**
	* ファンクション名：_set
	* 機能概要：Class内部変数を取得
	*/
	function _set( $key , $value ){
		$this->$key = $value;
	}

	/**
	* ファンクション名：_get
	* 機能概要：Class内部変数を取得
	*/	
	function _get( $key ){
		return $this->$key;
	}

	/*
	 * initializing
	 */
	function init( ){
		//echo number_format(memory_get_usage());
		/*
		 * 実行する.DOCUMENTROOT
		 */
		if( ! defined('DOCUMENTROOT') ):
			define( 'DOCUMENTROOT' , $_SERVER['DOCUMENT_ROOT'] );
		endif;

		/*
		 * 実行する.DOCUMENTROOTの↑一階層
		 */
		if( ! defined('HOMEDIR') ):
			define( 'HOMEDIR' , dirname($_SERVER['DOCUMENT_ROOT']) );
		endif;

		/*
		 * ディレクトリの区切り文字
		 */		
		if( ! defined('DS') ):
			define( 'DS', DIRECTORY_SEPARATOR );
		endif;

		/*
		 * 設定ファイルディレクトリ名
		 */		
		if( ! defined('CONFIG_DIR') ):
			define( 'CONFIG_DIR' , '/config' );
		endif;

		/*
		 * インクルードディレクトリパスを設定
		 */	
		if( ! defined('INCLUDES_DIR') ):
			define( 'INCLUDES_DIR' , '/includes' );
		endif;

		/*
		 * キャッションディレクトリパスを設定
		 */	
		if( ! defined('PLUGIN_028_CACHE_DIR') ):
			define( 'PLUGIN_028_CACHE_DIR' , '/plugin-028-cache' );
		endif;
		
		/******/
		define( 'THE_PLUGIN_028_DIR' , dirname(__FILE__) );
		define( 'WPCONTENT_028_CONFIG_DIR' , WP_CONTENT_DIR . '/028-config' );
		/******/

		/*
		*　メッセージを読み込み
		*/
		if ( file_exists( THE_PLUGIN_028_DIR . INCLUDES_DIR . DS . 'ps-plugin-message.php'  )):
			include_once ( THE_PLUGIN_028_DIR . INCLUDES_DIR . DS . 'ps-plugin-message.php'  );
			$this->_set( 'MESSAGES' , $MESSAGES );
		endif;

		/*
		*インクルードファイルを確認
		*/
		if ( ! $this->ps_include_once( ) && $this->include_items_flag === true ):
			return;
		endif;    

		/*
		*設定項目を確認
		*/
		if ( ! $this->get_items( ) ):
			if ( $this->include_items_flag === true ){
				add_action('admin_notices'								, array(&$this,'admin_notices_custom'));
				return;		
			}
		endif;
						
		$this->_init( );
	}	

	/**
	* ファンクション名：_init
	* 機能概要：プラグインの機能実行をスタート 
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 作成：
	* 変更：
	* @param なし
	* @return なし
	*/
	function _init( ){
		if ( is_admin( ) ):
			//管理
			add_action( 'admin_init'										, array( &$this, 'admin_init'));
			add_action( 'admin_menu'										, array( &$this, 'admin_menu'));
			//add_action( 'admin_print_footer_scripts'						, array( &$this, 'add_admin_print_styles' ) );
			//add_action( 'add_meta_boxes'			                    	, array( &$this, 'ps_excludes_category' ) , 10  , 2);
			add_filter( 'wp_terms_checklist_args'							, array( &$this, 'ps_wp_terms_checklist_args' ) ,10,2);
		else:
		//フォロートン
		endif;
		//全部
	}

	/**
	* ファンクション名：ps_wp_terms_checklist_args
	* 機能概要： カテゴリーTOPチェックを外す
	* 作成：プライム・ストラテジー株式会社 王 濱 2012/10/22
	* 変更：
	* @param Array $args
	* @return なし
	*/
	function ps_wp_terms_checklist_args( $args ) { 
		$args['checked_ontop'] = false;
		return $args;
	}   

	/**
	* ファンクション名： admin_init
	* 機能概要： 管理のinit
	* 作成：プライム・ストラテジー株式会社 王 濱 2012/10/22
	* 変更：
	* @param なし
	* @return なし
	*/
	function admin_init( ){
		add_action( 'admin_print_styles-post.php'       , array( &$this, 'add_admin_print_styles' ) );
	    add_action( 'admin_print_styles-post-new.php'   , array( &$this, 'add_admin_print_styles' ) );
	    
		add_action( 'admin_footer-post-new.php'			, array( &$this, 'add_admin_excludes_taxonomies' ) );
		add_action( 'admin_footer-post.php'				, array( &$this, 'add_admin_excludes_taxonomies' ) );
	}

	function add_admin_excludes_taxonomies(  ){
		$excludes = $this->get_items( );
		if ( ! $this->chk_array_empty( $excludes['excludes'] ) ){
			$_excludes = join( ',' ,  $excludes['excludes'] );
			echo '<input type="hidden" id="_excludes_taxonomies" name="_excludes_taxonomies" value="'.$_excludes.'" />';
		}

	}

	/**
	* ファンクション名： admin_menu
	* 機能概要： admin メニューがのフック
	* 作成：プライム・ストラテジー株式会社 王 濱 2012/10/22
	* 変更：
	* @param なし
	* @return なし
	*/	
	function admin_menu( ){

	}
	/**
	* ファンクション名： ps_include_once
	* 機能概要： check confing file and include
	* 作成：プライム・ストラテジー株式会社 王 濱 2012/10/22
	* 変更：
	* @param なし
	* @return なし
	*/
	function  ps_include_once( ){
		if ( is_multisite( ) ):
			global $blog_id;
			$include_path[] = WPCONTENT_028_CONFIG_DIR . CONFIG_DIR . DS .'config-'.$blog_id.'.php';
			$include_path[] = WPCONTENT_028_CONFIG_DIR . CONFIG_DIR . DS .'config.php';
			$include_path[] = THE_PLUGIN_028_DIR . CONFIG_DIR . DS .'config-'.$blog_id.'.php';
			$include_path[] = THE_PLUGIN_028_DIR . CONFIG_DIR . DS .'config.php';

			foreach ( $include_path as $path ):
				if (file_exists( $path ) ):
					include_once ( $path );
					$this->set_items( $items );
					return true;
				endif;
			endforeach;
			
		else:
		
			$include_path[] = WPCONTENT_028_CONFIG_DIR . CONFIG_DIR . DS .'config.php';
			$include_path[] = THE_PLUGIN_028_DIR . CONFIG_DIR . DS .'config.php';

			foreach ( $include_path as $path ):
				if (file_exists( $path ) ):
					include_once ( $path );
					$this->set_items( $items );
					return true;
				endif;
			endforeach;
			
		endif;

		if ( $this->include_items_flag === true ):
			add_action('admin_notices', array( &$this, 'admin_notices_include_once'));
		endif;
		
		return false;
	}

	/**
	* ファンクション名： add_admin_print_includes_styles
	* 機能概要： プラグインのincludesのcssとjavascriptを読み込み
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 作成：
	* 変更：
	* @param resource
	* @param int
	* @param string
	* @return
	*/
	function add_admin_print_includes_styles( ){

		wp_enqueue_script( 'includes-prefix-js-' . strtolower(__CLASS__) , plugins_url('includes/js/prefix-js.js', __FILE__) );

		wp_register_style( 'includes-prefix-style-'. strtolower(__CLASS__) , plugins_url('includes/css/prefix-style.css', __FILE__) );
		
		wp_enqueue_style( 'includes-prefix-style-' . strtolower(__CLASS__) );
	}


	/**
	* ファンクション名： add_admin_print_styles
	* 機能概要： プラグインのcssと＿javascriptを読み込み
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 作成：
	* 変更：
	* @param resource
	* @param int
	* @param string
	* @return
	*/
	function add_admin_print_styles( ){

		wp_enqueue_script( 'prefix-js-' . strtolower(__CLASS__) , plugins_url('js/prefix-js.js', __FILE__) );

		wp_register_style( 'prefix-style-'. strtolower(__CLASS__) , plugins_url('css/prefix-style.css', __FILE__) );
		
		wp_enqueue_style( 'prefix-style-' . strtolower(__CLASS__) );
	}

	/**
	* ファンクション名： admin_notices_auto_message
	* 機能概要： 警告メッセージ
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	*/	
	function admin_notices_auto_message(){
		$auto_message = $this->_get( 'notices_auto_message' );
		$this->_set( 'notices_auto_message' , null );

		if ( ! $this->chk_string_empty( $auto_message ) ):
			return;
		endif;

		echo '<div class="error" style="text-align: center;">
			<p style="color: red; font-size: 14px; font-weight: bold;">
			' . $auto_message . '
			</p>
		</div>';
	}

	/**
	* ファンクション名： admin_notices_include_once
	* 機能概要：設定ファイルなし、警告メッセージ
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	*/	
	function admin_notices_include_once(){
		echo '<div class="error" style="text-align: center;">
				<p style="color: red; font-size: 14px; font-weight: bold;">
					プラグイン028 PS Combine Taxonomy Children :設定ファイル<strong>_config.php</strong>の名前を<strong>config.php OR config-{$blog_id}.php</strong>に変更し、<strong>configファイル</strong>の設定を行ってください。
				</p>
			 </div>';
	}
	
	/**
	* ファンクション名：admin_notices_custom
	* 機能概要：設定ファイルあり、$ultilingualがない場合警告メッセージ
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	*/	
	function admin_notices_custom(){
		echo '<div class="error" style="text-align: center;">
				<p style="color: red; font-size: 14px; font-weight: bold;">
					プラグイン028 PS Combine Taxonomy Children:設定ファイル<strong>config.php OR config-{$blog_id}.php</strong>の($custom)の設定を行ってください。
				</p>
			</div>';
	}

	/**
	* ファンクション名： check_cache_dir
	* 機能概要：　キャーションディレクトリを確認する
	* 作成：プライム・ストラテジー株式会社 2012.12.04
	* 変更：
	* @param String $cache_dir
	* @return boolean true/false
	*/	
	function check_cache_dir( $cache_dir ) {
		$uploads = wp_upload_dir();
		if ( $uploads['error'] !== false ) { return false; }
		$cache_dir = $uploads['basedir'] . $cache_dir;
		if ( is_writable( $uploads['basedir'] ) ) {
			if ( file_exists( $cache_dir ) ) {
				if ( is_writable( $cache_dir ) ) {
					return $cache_dir;
				}
			} else {
				if( mkdir( $cache_dir ) ) {
					return $cache_dir;
				}
			}
		}
	}
	/**
	* ファンクション名： create_cache
	* 機能概要：　キャーションファイルを作成する
	* 作成：プライム・ストラテジー株式会社 2012.12.04
	* 変更：
	* @param String $cache_dir
	* @param String $cache_name
	* @param String $content
	* @return boolean true/false
	*/	
	function create_cache( $cache_dir , $cache_name  , $content ) {
		if ( is_dir( $cache_dir ) ){
			try {
				$handle = @fopen( $cache_dir . DS . $cache_name , 'w' );
				if ( $handle ) {
					fwrite( $handle, $content );
					fclose( $handle );
				}			
			} catch (Exception $e) {
				return $e;	
			}

		}
		return true;
	}

	/**
	* ファンクション名： delete_cache
	* 機能概要：　キャーションファイルを削除する
	* 作成：プライム・ストラテジー株式会社 2012.12.04
	* 変更：
	* @param String $cache_file
	* @return boolean true/false
	*/	
	function delete_cache( $cache_file ) {
		if ( file_exists( $cache_file ) ) {
			unlink( $cache_file );
		}
	}

	/**
	* ファンクション名： check_cache
	* 機能概要：　キャーションファイルを確認する
	* 作成：プライム・ストラテジー株式会社 2012.12.04
	* 変更：
	* @param String $cache_file
	* @return boolean true/false
	*/	
	function check_cache( $cache_file ) {
		if ( file_exists( $cache_file ) ) {
			return true;
		}
		return false;
	}

	/**
	* ファンクション名： check_htaccess
	* 機能概要：　.htaccessファイルを作成する
	* 作成：プライム・ストラテジー株式会社 2012.12.04
	* 変更：
	* @param String $cache_dir
	* @return boolean true/false
	*/	
	function check_htaccess( $cache_dir ) {
		if ( file_exists( $cache_dir . DS . '.htaccess' ) ) { 
			return; 
		}
		$handle = @fopen( $cache_dir . DS . '.htaccess', 'w' );
		if ( $handle ) {
			fwrite( $handle, "order deny,allow\ndeny from all" );
			fclose( $handle );
		}
	}
	/**  
	 * ファンクション名：ps_get_post_metas
	 * 機能概要：全カスタムフィールドを取得
	 * 作成：プライム・ストラテジー株式会社 王 濱
	 * 変更：
	 * @param   String post_id
	  * @return  Array 全カスタムフィールド
	 */
	function ps_get_post_metas( $post_id  ){   
		$custom_fields = get_post_custom($post_id);
		$return = array();  
		foreach( $custom_fields as $key => $field ):
			if ( is_array( $field ) && count( $field ) > 1 ):
				foreach ( $field as $key2 => $val  ):
					$return[$key][$key2] = $val;
				endforeach;
			else:
				$return[$key] = maybe_unserialize($field[0]) ;
			endif;
		endforeach;
		return $return;
	}

	/**
	* ファンクション名：get_category_ancestors
	* 機能概要：親子関係のカテゴリー全部取得する
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	*/	
	function get_category_ancestors( $cat_id, $ancestors = array() ) {
		$cat = get_category( $cat_id );
		$ancestors[] = $cat;
		if ( $cat->parent != 0 ) {
			$ancestors = $this->get_category_ancestors( $cat->parent, $ancestors );
		}
		return $ancestors;
	}

	/**
	* ファンクション名： chk_string_empty
	* 機能概要：文字列のチェック
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	*/	
	function chk_string_empty( $string ){
		if ( ! isset( $string  )){
			return true;
		}
		if ( empty( $string )){
			return true;
		}

		if ( $string == '' ){
			return true;
		}

		if ( ! $string ){
			return true;
		}

		return false;
	}

	/**
	* ファンクション名： chk_array_empty
	* 機能概要：配列・オブジェクトの空チェック
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	*/	
	function chk_array_empty( $array ){

		if ( ! isset( $array )){
			return true;
		}

		if ( ! is_array( $array ) && ! is_object( $array )){
			return true;
		}

		if ( ! $array ){
			return true;			
		}
		
		foreach( $array as $val ){
			return false;
		}

		return true;
	}


	/**
	* ファンクション名： check_date_time
	* 機能概要： 日時のフォーマット[YYYY/MM/DD hh:ii]
	* 作成：プライム・ストラテジー株式会社 王 濱
	* 変更：
	*/	
	function check_date_time( $date ){
		if ( empty( $date ) && ! isset( $date  ) ){
			return false;	
		}
		 
		if ( ! preg_match( '/([0-9]{4})[-|\/]([0-9]{1,2})[-|\/]([0-9]{1,2}).?([0-9]{1,2}):([0-9]{1,2})/' , $date , $m ) ){
			return false;
		}
		if ( ! checkdate( $m[2], $m[3], $m[1] ) ){
			return false;
		}
		if ( $m[4]>24 || $m[4]<0 ){
			return false;
		}
		if ( $m[5]>59 || $m[5]<0 ){
			return false;
		}
		return true;
	}

    /** 
     * destruct
     *
     * @author プライム・ストラテジー株式会社 王 濱
     * @date 2012.11.27
     *
     * @param void
     * @return null
     */
    function __destruct() {

    	//number_format(memory_get_usage());

	}   
	
}//class end

include_once ( dirname(__FILE__) . '/config/functions.php' );

$ps_combine_taxonomy_children = new ps_combine_taxonomy_children( );

?>
