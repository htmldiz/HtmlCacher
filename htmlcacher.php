<?php
/*
    Plugin Name: HtmlCacher
    Plugin URI:
    description:
    Version: 1.0
    Author: {Marcus code}
    Author URI: https://github.com/htmldiz
    License: GPL3
*/
class MainHtmlCacher{
	private $templates = array('404','archive','attachment','author','category','date','embed','frontpage','home','index','page','paged','privacypolicy','search','single','singular','tag','taxonomy');
	public function __construct(){
		register_activation_hook(__FILE__,array($this,'activate'));
        add_action( 'wp_ajax_htmlcacher_clear', array($this,'htmlcacher_clear') );
        add_action('wp_enqueue_scripts', array($this,'admin_enqueue_scripts'),99999,1);
        add_action('admin_enqueue_scripts', array($this,'admin_enqueue_scripts'),99999,1);
        add_action('admin_bar_menu', array($this,'admin_bar_menu'),99999,1);
		if(!is_admin()){
			foreach ($this->templates as $type) {
				add_filter( "{$type}_template", array($this,'template_hierarchy'),10,1 );
			}
		}
	}
    function htmlcacher_clear() {
        $upload_dir   = wp_upload_dir();
        if(!is_dir($upload_dir['basedir'].'/htmlcache')){
            mkdir($upload_dir['basedir'].'/htmlcache');
        }
        $files = glob($upload_dir['basedir'].'/htmlcache/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        wp_send_json(array('message'=>'Done!'));
        exit();
    }
    function admin_enqueue_scripts() {
        wp_enqueue_script( 'htmlcacher', plugins_url( '/htmlcacher.js', __FILE__ ), array(  ), '1.0', true );
        wp_localize_script(
            'htmlcacher',
            'htmlcacher',
            array(
                'ajaxurl' => admin_url( 'admin-ajax.php' )
            )
        );
    }
    function admin_bar_menu($wp_admin_bar) {
        $wp_admin_bar->add_menu( array(
            'id'    => 'htmlcache-clear-total',
            'parent' => null,
            'group'  => null,
            'title' => 'Remove Cache',
            'href'  => admin_url('admin.php?page=htmlcache_clear_total'),
            'meta' => [
                'title' => __( 'Menu Title', 'textdomain' ), //This title will show on hover
            ]
        ) );
    }
	public function activate(){
		$upload_dir   = wp_upload_dir();
		if(!is_dir($upload_dir['basedir'].'/htmlcache')){
			mkdir($upload_dir['basedir'].'/htmlcache');
		}
	}
	public function template_hierarchy($template){
		if( !empty($template) ){
			$template_path = explode('/',$template);
			$template_name = $template_path[count($template_path)-1];
			$template_name = str_replace('.php','.html',$template_name);
			$upload_dir    = wp_upload_dir();
            global $wp;
            $url = $wp->request;
            $template_name = $url.'.html';
            $template_name = str_replace('/','-',$template_name);
			$file_cache_path = $upload_dir['basedir'].'/htmlcache/'.$template_name;
            if(!current_user_can( 'edit_posts' )){
                if( !file_exists($file_cache_path) ){
                    add_filter( 'show_admin_bar', '__return_false' );
                    $f = fopen($file_cache_path,'w+');
                    ob_start();
                    require_once $template;
                    $content = ob_get_contents();
                    ob_clean();
                    fwrite($f,$content);
                    fclose($f);
                }
			    $template = $file_cache_path;
            }
		}
		return $template;
	}
}
new MainHtmlCacher();
