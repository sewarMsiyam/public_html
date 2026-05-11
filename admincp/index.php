<?php
$home = 1;
define('CHECK_ME', true);

include('../abc.php');

if($intro->option['admin_folder'] == "") $intro->option['admin_folder'] = "admincp";
$intro->admin_folder = $intro->option['admin_folder']."/";
$intro->auth->flag = 'admin';

if ($intro->auth->auth_admin() == true) {
	
	
	define("admin_path", $intro->base_url.$intro->admin_folder);
	
	$sess_admin = $intro->auth->sess_admin();
 
	$app_name = !empty($intro->url_segments[1]) ? preg_replace('!\W!','',$intro->url_segments[1]) : $intro->config['default_app'];
	$app_file = "{$app_name}.php";
	//echo $app_name;
	if(isset($_POST['app_name']) != '') $app_name = $_POST['app_name'];
	
	/* if no app, use default */
	if(!file_exists('apps_admin/'.$app_file))
	{
		$app_name = 'home';
		$app_file = "{$app_name}.php";
	}
    
	if(!empty($app_action)) {  
      /* user override if set */
      $app_action = 'index';
    } else {
      /* get from url if present, else use default */
      $app_action = !empty($intro->url_segments[2]) ? $intro->url_segments[2] : 'index';
      /* cannot call method names starting with underscore */
      if(substr($app_action,0,1)=='_')
        throw new Exception("Action name not allowed '{$app_action}'");    
    }
	if(isset($_POST['app_action']) != '') $app_action = $_POST['app_action'];

	$fldr = explode('index.php' , dirname($_SERVER['REQUEST_URI']));
	define('_BASE',$fldr[0]);
	
	include('../intro/intro_apps_admin.php');
	include('apps_admin/'.$app_file);
	
	//echo $intro->input->ip_address();

	/* see if app class exists */
	$app_class = $app_name.'_appadmin';

	/* instantiate the app */
	
	$app_admin = new $app_class($app_name,$intro->_base($app_name),"../../images");

	#clear cashe
	/*if($_POST != null){
		$path = $_SERVER['SCRIPT_FILENAME'];
		$path = str_replace( array("img.php","index.php","admincp/","{$intro->admin_folder}"),"",$path);
		$folder = $path ."includes/cache/files/";
		$folder = $path ."includes/db/cache/";
		if ($handle = opendir($folder)){
			while (false !== ($file = readdir($handle))){
				 if($file != '.' && $file != '..' && $file != 'index.php'){
					@unlink($folder.$file);
				}		
			}
			closedir($handle);
		}
	}*/
	
	if($intro->input->get_post('noHeader') != 1 && $intro->input->get_post('NH') != 1)
	include('header.php');

	$app_admin->{$app_action}();
	
	if($intro->input->get_post('noHeader') != 1 && $intro->input->get_post('NH') != 1)
	include('footer.php');
	 
	//echo "<hr>$app_class<hr>";
	
	//var_dump($intro->action);
	
	
}else{

	$admin_folder = $intro->base_url."{$intro->option['admin_folder']}/login.php";
	$admin_folder = str_replace("//","/", $admin_folder );

	header("location: $admin_folder");
}
?>