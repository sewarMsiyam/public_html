<?php

if(!defined('DS'))
  define('DS',DIRECTORY_SEPARATOR);  

class intro
{
  var $config = null;
  var $option = null;
  var $lang = null;
  var $style = null;
  var $app = null;
  var $action = null;
  var $path_info = null;
  var $url_segments = null;
  var $home = null;
  var $brand = null;
  
  var $db = null;
  var $sec = null;
  var $auth = null;
  var $input = null;
  var $session = null;
  var $sms = null;
  var $forms = null;
  var $maa = null;
  var $ar = null;
  var $admin_folder = null;
  var $base_url = null;
  var $site_url = null;
  var $url = null;
  var $logo = null;
  var $site_name = null;
  var $href = null;
 
  public function __construct($id='default')
  {
    self::instance($this,$id);
  }
    
  public function main()
  {

    self::timer('intro_app_start');
    
    $this->path_info = !empty($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] :
	    (!empty($_SERVER['ORIG_PATH_INFO']) ? $_SERVER['ORIG_PATH_INFO'] : '');
    
   // $this->setupErrorHandling();
    global $config;
    include(PATH . 'includes/class_io.php');
    include(PATH . 'includes/db/mysql.php');
    include(PATH . 'includes/class_maa.php');
    include(PATH . 'includes/class_Security.php');
    include(PATH . 'includes/class_Security_Input.php');
    include(PATH . 'includes/class_auth.php');
    include(PATH . 'includes/class_session.php');
    include(PATH . 'includes/class_sms.php');
    include(PATH . 'includes/class_intro_forms.php');
    include(PATH . 'includes/class_array.php');
	include(PATH . 'includes/array.php');

	
    

    $this->config = $config;
	
	$this->db           = new Intro_Database();
	#$this->db->memcache_host = 'localhost';
	#$this->db->memcache_port = 11211;
	//$this->db->memcache_compressed = true;
	#$this->db->caching_method = 'memcache';
	$this->db->connect($config['db']['hostname'],$config['db']['username'],$config['db']['password'],$config['db']['database']);
	$this->db->set_charset($config['db']['charset'],$config['db']['collation']);
	$this->db->query("set sql_mode = '';");
	unset($config['db']['password']);
	$this->sec 			= new Security();
	
	$this->input 		= new Input();
	$this->auth 		= new auth($this->db);
	$this->session	 	= new Intro_Session($this->db,21600);
	$this->sms 			= new Sms_Api();
	$this->forms 		= new Intro_Form();
	$this->maa 			= new maa($this->db);
	$this->ar 			= new ar($this->db);
	$this->option 		= $this->maa->obtain_config();
	
	$this->style 		= $this->maa->obtain_style();
	$this->base_url 	= $this->config['base_url'];
	$this->site_url 	= $this->option['site_url'];
	$this->site_name 	= $this->option['site_name'];
	$this->url 	= $this->site_url . $this->base_url;
	$this->logo 	= $this->url ."style/images/logo.png";
	
	
	require(PATH . 'includes/functions.php');
	
	require(PATH . 'includes/functions_filter_text.php');
	include(PATH . 'intro/uri.php');
	$this->uri 			= new Uri();
	//$this->uri->lang	=  $this->maa->lang;
	if($this->uri->lang != ""){
		$this->href = $this->base_url."{$this->uri->lang}/";
	}
	
	$this->lang 		= $this->maa->obtain_lang($this->uri->lang);
	
    $this->setupRouting();
    $this->setupSegments();
    //$this->setupApps();
    $this->setupAction();
	
	//echo "<hr>{$this->action}<hr>";
	

  }
 
  public function setupRouting()
  {
    if(!empty($this->config['routing']['search'])&&!empty($this->config['routing']['replace']))
      $this->path_info = preg_replace(
          $this->config['routing']['search'],
          $this->config['routing']['replace'],
          $this->path_info);
  }
   
  public function setupSegments()
  {
    $this->url_segments = !empty($this->path_info) ? array_filter(explode('/',$this->path_info)) : null;
  }
  
  public function setupApps()
  {
   /* 

	$app_name = !empty($this->url_segments[1]) ? preg_replace('!\W!','',$this->url_segments[1]) : $this->config['default_app'];
	$app_file = "{$app_name}.php";
	
	if(!file_exists('apps/'.$app_file))
	{
		$app_name = $this->config['default_app'];
		$app_file = "{$app_name}.php";
	}
    
    
   include('apps/'.$app_file);
    

    $app_class = $app_name.'_app';
      
    $this->app = new $app_class(true);
	echo "<hr>$app_class<hr>";
	
	var_dump($this->app);
    */
  }  
  public function setupAction()
  {
    if(!empty($this->config['root_action'])) {  
      /* user override if set */
      $this->action = $this->config['root_action'];
    } else {
      /* get from url if present, else use default */
      $this->action = !empty($this->url_segments[2]) ? $this->url_segments[2] :
      (!empty($this->config['default_action']) ? $this->config['default_action'] : 'index');
      /* cannot call method names starting with underscore */
      if(substr($this->action,0,1)=='_')
        throw new Exception("Action name not allowed '{$this->action}'");    
    }
  }  
   
  public static function &instance($new_instance=null,$id='default')
  {
    static $instance = array();
    if(isset($new_instance) && is_object($new_instance))
      $instance[$id] = $new_instance;
    return $instance[$id];
  }
  
  public static function timer($id=null,$id2=null)
  {
    static $times = array();
    if($id !== null && $id2 !== null)
      return (isset($times[$id]) && isset($times[$id2])) ? ($times[$id2] - $times[$id]) : false;
    elseif($id !== null)
      return $times[$id] = microtime(true);
    return false;
  }
  
	public function redirect($app,$method = "index",$args = '')
	{
	$location = $this->config['base_url'] . $this->admin_folder ."index.php/{$app}/{$method}/{$args}";

		//if headers not sent
		if (!headers_sent())
		{
			header('Location: '.$location);
		}else {
			echo '<script type="text/javascript">window.location.href="'.$location.'";</script>';
			echo '<noscript><meta http-equiv="refresh" content="0;url='.$location.'" /></noscript>';
		}
		exit;
	}
	public function redirect2($app,$method = "index",$args = array())
	{
		$location = $this->config['base_url'] . "index.php/". $app . "/" . $method;// . "/" . implode("/",$args);

		//if headers not sent
		if (!headers_sent())
		{
			header('Location: '.$location);
		}else {
			echo '<script type="text/javascript">window.location.href="'.$location.'";</script>';
			echo '<noscript><meta http-equiv="refresh" content="0;url='.$location.'" /></noscript>';
		}
		exit;
	}
	
	
	public function _base($app)
	{
		return $this->config['base_url'] . $this->admin_folder ."index.php/". $app ;
	}
	
	public function _base2($app)
	{
		return $this->href. $app ;
	}
	
	public function app_redirect($app,$method = "index",$args = '')
	{
		//$location = $this->config['base_url'] ."index.php/". $app . "/" . $method . $args;// . "/" . implode("/",$args);
		$location = $this->uri_links($app, $method , $args);
		//if headers not sent
		if (!headers_sent())
		{
			header('Location: '.$location);
		}else {
			echo '<script type="text/javascript">window.location.href="'.$location.'";</script>';
			echo '<noscript><meta http-equiv="refresh" content="0;url='.$location.'" /></noscript>';
		}
		exit;
	}
	
	public function app_url($app,$action = "index", $args='')
	{
		return $this->base_url . $this->admin_folder ."index.php/". $app . '/'.$action . $args;
	}
	public function adv_url($link,$args='')
	{
		return $this->config['base_url'] ."adv/user/". $link ."?NH=1";
	}
	public function admin_url($app,$action = "index")
	{
		return $this->config['base_url'] . $this->admin_folder ."index.php/". $app . '/'.$action;
	}
	
	public function ip()
	{
		return $_SERVER['REMOTE_ADDR'];
	}
	
	public function send_email_old($to, $name, $subject, $body, $type='')
	{	
	      require PATH . 'includes/mail/PHPMailerAutoload.php';
	//Create a new PHPMailer instance
		$mail = new PHPMailer;
		
		$mail->CharSet = 'UTF-8';
		$mail->Encoding = 'base64';
		//Tell PHPMailer to use SMTP
	//	$mail->isSMTP();
	
		$mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
		//Enable SMTP debugging
		// 0 = off (for production use)
		// 1 = client messages
		// 2 = client and server messages
		$mail->SMTPDebug = 2;
		//Ask for HTML-friendly debug output
		$mail->Debugoutput = 'html';
		//Set the hostname of the mail server
		$mail->Host = trim($this->option['mail_host']);
		//Set the SMTP port number - likely to be 25, 465 or 587
		$mail->Port = trim($this->option['mail_port']);
		//Whether to use SMTP authentication
		$mail->SMTPAuth = true;
		//Username to use for SMTP authentication
		$mail->Username = trim($this->option['mail_user']);
		//Password to use for SMTP authentication
		$mail->Password = trim($this->option['mail_pass']);
		//Set who the message is to be sent from
		//$mail->setFrom($this->option['mail_sender_email'], $this->option['mail_sender_name']);
		$mail->From = $this->option['mail_sender_email'];
		$mail->FromName = $this->option['mail_sender_name'];
		//Set an alternative reply-to address
		$mail->addReplyTo($this->option['mail_sender_email'], $this->option['mail_sender_name']);
		//Set who the message is to be sent to
		$mail->addAddress($to, $name);
		//Set the subject line
		$mail->Subject = $subject;
		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body
		$mail->msgHTML($body);
		//Replace the plain bodytext body with one created manually
		//$mail->AltBody = $body;
		//Attach an image file
		//$mail->addAttachment('images/phpmailer_mini.png');
		
		/*if (isset($_FILES['mailfile']) &&
			$_FILES['mailfile']['error'] == UPLOAD_ERR_OK) {
			$mail->AddAttachment($_FILES['mailfile']['tmp_name'],
			$_FILES['mailfile']['name']);
		}*/

		//send the message, check for errors
		if (!$mail->send()) {
			//var_dump($this->option); 
			die( "Mailer Error: " . $mail->ErrorInfo );
		} else {
		//	echo "Message sent!";
		}
		
		
	
	}
	
	

	public function send_email($to, $name, $subject, $body, $type='')
	{	
		require PATH . 'includes/mail/PHPMailerAutoload.php';
		//Create a new PHPMailer instance
		$this->option['mail_user']="info2@buyformula.net";
		$this->option['mail_pass']="pal@1948";
		$mail = new PHPMailer;
		$mail->CharSet = 'UTF-8';
		$mail->Encoding = 'base64';
		//Tell PHPMailer to use SMTP
		//$mail->isSMTP();
	
		$mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
		//Enable SMTP debugging
		// 0 = off (for production use)
		// 1 = client messages
		// 2 = client and server messages
		$mail->SMTPDebug = 2;
		//Ask for HTML-friendly debug output
		$mail->Debugoutput = 'html';
		//Set the hostname of the mail server
		$mail->Host = trim($this->option['mail_host']);
		//Set the SMTP port number - likely to be 25, 465 or 587
		$mail->Port = trim($this->option['mail_port']);
		//Whether to use SMTP authentication
		$mail->SMTPAuth = true;
		//Username to use for SMTP authentication
		$mail->Username = trim($this->option['mail_user']);
		//Password to use for SMTP authentication
		$mail->Password = trim($this->option['mail_pass']);
		//Set who the message is to be sent from
		//$mail->setFrom($this->option['mail_sender_email'], $this->option['mail_sender_name']);
		$mail->From = $this->option['mail_sender_email'];
		$mail->FromName = $this->option['mail_sender_name'];
		//Set an alternative reply-to address
		$mail->addReplyTo($this->option['mail_sender_email'], $this->option['mail_sender_name']);
		//Set who the message is to be sent to
		$mail->addAddress($to, $name);
		//Set the subject line
		$mail->Subject = $subject;
		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body
		$mail->msgHTML($body);
		

		//send the message, check for errors
		if (!$mail->send()) {
			//var_dump($this->option); 
			die( "Mailer Error: " . $mail->ErrorInfo );
		} else {
			//echo "Message sent!";
		}


	}
	
	function captcha2(){
	
		include(PATH . 'includes/captcha/captcha.php');
		
		$captcha = new SimpleCaptcha();
		//$captcha->session_var = 'secretword';
	//	$captcha->imageFormat = 'png';
		//$captcha->lineWidth = 3;
		//$captcha->scale = 3; $captcha->blur = true;
		$captcha->fontsPath = PATH . "includes/captcha/fonts";
		$captcha->CreateImage();
	}
	
	function captcha(){
	
		include(PATH . 'includes/captcha/captcha.php');
		
		$captcha = new SimpleCaptcha();
		//$captcha->session_var = 'secretword';
		//$captcha->imageFormat = 'png';
		//$captcha->lineWidth = 3;
		//$captcha->scale = 3; $captcha->blur = true;
		$captcha->fontsPath = PATH . "includes/captcha/fonts";
		$captcha->CreateImage();
	}
	
	
	
	
	function uri_links($file,$function,$id=0,$title='',$page=0)
	{
		$lang = $this->maa->lang;
		
		$args = '';
		
		//$title = preg_replace('/[^A-Za-z0-9-_ ]/', '', $title);
		//$title= mb_ereg_replace('[^Ã-íA-Za-z]', ' ', $title);
		$title = str_replace(' ',"+",$title);
		$title = str_replace('"',"",$title);
		$title = str_replace("'","",$title);
		if($this->option['seo_uri'] == 1)
		{
			if($id != 0) $args .= "/$id";
			if($title != '') $args .= "/$title";
			if($page != 0) $args .= "/$page";
			//.$lang."/"
			return "{$this->href}".$file."/{$function}{$args}";
		}
		else{
			if($id != 0) $args .= "?id=".$id;
            return $this->href."index.php/$file/$function{$args}";			
		}
		
	}
	
	
	function banner($advcat){
		
		$advcat = intval($advcat);
		$final_banner="";
		$sql = $this->db->query("SELECT * from ".PREFIX."_adv_cat where cat_status=1 AND catid=$advcat");
		if(mysqli_num_rows($sql) != 0)
		{
		$crow = $this->db->fetch_assoc($sql);
	
		if($crow['cols'] < 1) $crow['cols'] =1;
		if($crow['adds_num'] < 1) $crow['adds_num'] =1;

		$result = $this->db->query_fast("SELECT * from ".PREFIX."_adv where status=1 AND catid=$advcat order by ".(($crow['adds_rand'] == 1) ? "RAND()" : "advid desc")." limit 0,{$crow['adds_num']}");
		if(mysqli_num_rows($result) != 0)
		{
			$final_banner = "<table style=\"width:100%;border-spacing:0;border-collapse: collapse;\">"
			."<tbody><tr>";
			
			$i = 1;
			while ($myrow = $this->db->fetch_assoc($result))
			{
				$advid=$myrow['advid'];
				$title=$myrow['title'];
				$status=$myrow['status'];
				$advfile=$myrow['advfile'];
				$url=$myrow['url'];
				$html=$myrow['html'];
				$type=$myrow['type'];
				$width=$myrow['width'];
				$height=$myrow['height'];
				$target=$myrow['target'];
				$after_expire=$myrow['after_expire'];
				$date_start=$myrow['date_start'];
				$date_end=$myrow['date_end'];
				$shows=$myrow['shows'];
				$username=@$myrow['username'];
				
				$sql = $this->db->query("UPDATE ".PREFIX."_adv SET shows=shows+1 where advid='$advid'");
				
				
				if($username != ''){
					$link = $this->adv_url($username);
				}else{
					$link = $this->base_url."index.php/home/ClickAdd/$advid/?NH=1";
				}
				if($advid == 1){
					//var_dump($myrow );
				}
				if($type == 1){//image
				
					$banner_box = "<a title='$url' href='".$link."' target='$target'><img width='$width' height='$height' src='{$this->base_url}$advfile' alt='$title' /></a>";
				}
				if($type == 2){//swf
					$banner_box  = "<a title='$url' href='".$link."' target='$target'><embed width='$width' height='$height' src='{$this->base_url}$advfile' alt='$title' border='0'></embed></a>";
				}				
				if($type == 3){//html
					//$banner_box = stripslashes($html);
					$banner_box = intro_html_decode($html);
				}
				if($type == 4){//iframe
					$banner_box = " <iframe src=\"$url\" width=\"$width\" height=\"$height\" frameborder=\"0\"></iframe>";
				}				
				$res = (100/$crow['cols']);
				if (is_int($i / $crow['cols'])){
					$final_banner .= "<td style=\"width:$res%;padding: 0px;text-align:center;\" >$banner_box</td></tr><tr>";
				}else{
					$final_banner .= "<td style=\"width:$res%;padding: 0px;text-align:center;\" >$banner_box</td>";
				}
				$i++;
			}#while				
			$final_banner .= "</tr></tbody></table>";
		}#num
		else{ 
			$final_banner =""; 
		}
		
	}	
	   return $final_banner;
	}
	
	function pwd($str){
		
		$x1 = sha1($str ."AniMoh");
		$pass = md5($x1 ."AniSo");
		
		return $pass;
	}
	public function SuccessMsg($title, $body,$url="", $time='1500' , $btn='')
	{
	
	
		$seconds = @round($time/1000, 0);
		//$seconds = round($seconds/60, 0);
		if($url !=""){$urlred=" window.location = '$url';";}
		echo  " 
		<script>
		$(document).ready(function(){
			$(\".sucess_reg\").delay($time).fadeOut(5000, function() {
				$urlred
			});
		});
		$(function(){
			var count = $seconds;
			countdown = setInterval(function(){
				$(\"#cdown\").html(count);
				if (count == 0) {
					//alert('yooo');
				}
				count--;
				}, 1000);
		});
		</script>
			<div class=\"container p-5 \">
			
			<div class=\"card  sucess_reg mb-3\" style=\"max-width: 50rem;\">
			  <div class=\"card-header text-white bg-success\">
				$title
			  </div>
			  <div class=\"card-body\">
				<p class=\"card-text\">
				$body
				</p>
				<a href=\"{$this->uri_links('login','index',0,'')}\" class=\"btn btn-primary px-5\">Login</a>
			  </div>
			  <div class=\"card-footer bg-secondary  text-muted text-white\">
				<div id='cdown' style='font-size:30px;color: red;'>
				$seconds
				</div>
			  </div>
			  </div>
			  
			</div>";	

	}
	
}
 
?>