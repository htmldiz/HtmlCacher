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
	private $templates = array(
		'404',
		'archive',
		'attachment',
		'author',
		'category',
		'date',
		'embed',
		'frontpage',
		'home',
		'index',
		'page',
		'paged',
		'privacypolicy',
		'search',
		'single',
		'singular',
		'tag',
		'taxonomy',
	);
	public function __construct(){
		if(!is_admin()){
			foreach ($this->templates as $type) {
				add_filter( "{$type}_template", array($this,'template_hierarchy'),10,3 );
			}
		}
	}
	public function template_hierarchy($template, $type, $templates){
		ob_start();
		if(!empty($template)){
			$template_path = explode('/',$template);
			$template_name = $template_path[count($template_path)-1];
			$template_name = str_replace('.php','.html',$template_name);
			$file_cache_path = __DIR__.'/cache/'.$template_name;
			if(!file_exists($file_cache_path)){
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
		return $template;
	}
}
new MainHtmlCacher();
