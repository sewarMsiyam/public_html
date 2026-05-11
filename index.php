<?php
//die("Comming Soon...");
//include("includes/cache/cache.php");
//http://www.catswhocode.com/blog/how-to-create-a-simple-and-efficient-php-cache

//include("https.php");
//use_ssl(TRUE);

session_start(); 

$bm_time = microtime(true);
define('CHECK_ME', true);
$root_dir = (defined('ROOT_DIR')) ? ROOT_DIR : './';
include($root_dir . 'abc.php');

if(!isset($intro->uri->segments[0]))
{
	//header("location: {$intro->base_url}" . $intro->uri->lang ."/");
}

$intro->admin_folder = "";//$intro->option['admin_folder']."/";
$intro->auth->flag = 'user';


	#close site
	if($intro->option['close_site'] == 1){

		//custom_header("Site Closed");
		pHeader();

		echo "<div style='margin: 10px; padding: 20px;text-align:center;font-size:30px; color: white;background: #c0c0c0; border-radius: 20px;'>
		<div><img src='{$intro->base_url}style/img/logo.png' /></div>
		{$intro->option['close_msg']}</div>";

		pFooter();
		die();
	}
	#end close 
	//var_dump($intro->uri);
	$app_name = $intro->uri->app;
	$app_file = "{$app_name}.php";
	/* if no app, use default */
	if(!file_exists('apps/'.$app_file))
	{
		$app_name = $intro->config['default_app'];
		$app_file = "{$app_name}.php";
	}
    
   
   include('intro/intro_apps.php');
   
   include('apps/'.$app_file);
    
    /* see if app class exists */
    $app_class = $app_name.'_app';
      
    /* instantiate the app */
	//echo "<hr>$app_class($app_name)</hr>";
    $app = new $app_class($app_name,$intro->_base2($app_name),"../../");
	
	if(strtolower($app_name) == 'home' && strtolower($intro->uri->action) == 'index')
	$home =1;
	
	//$listActions = array("logout","utozwed","doeditpassword");
	
	
	
	//@ob_start();
	$app->{$intro->uri->action}();
	//$ob_contents = @ob_get_contents();
	//@ob_end_clean();
	
	//if($NH != 1 && !in_array(strtolower($intro->action), $listActions))
	//pHeader($app->header_data,$app->img_path,$app->base);
		
	//echo $ob_contents;

	//if($NH != 1 && !in_array(strtolower($intro->action), $listActions))
	//pFooter();
	
	//if(function_exists('cache_page')) cache_page(ob_get_contents());
//	$intro->db->close();

//var_dump($intro->uri);
	
?>