<?PHP

define('CHECK_ME', true);

include ("../abc.php");

$intro->admin_folder = $intro->option['admin_folder']."/";
$intro->auth->flag = 'admin';

class login_error{
	
	var $error = null;
	
	function __construct(){
				
	}
	function Login(){
		global $intro,$maaking_ver;

		$site_name = "";
		$pagetitle = "";		
		
		
		$admin_name = $intro->input->post('admin_name');
		$password = $intro->input->post('password');
		
		include("style/login.html");

	}

	function do_login(){
		global $intro,$admin_name,$password, $remember,$error,$error_msg;

		$ip = $intro->input->ip_address();

		$admin_name = $intro->input->post('admin_name');
		$password = $intro->input->post('password');

		if((!$admin_name) || (!$password)){
			$reqmsg= "(<span class=error>Required!</span>)";

			if(empty($admin_name)){
			   $this->error['user'] = $reqmsg;
			}
			if(empty($password)){
			   $this->error['pass'] = $reqmsg;
			}           
			$this->Login();
			exit();
		}
		$intro->db->_optimize(' ',' ',' ');
		if($intro->auth->check_admin_and_login($admin_name,$intro->pwd($password)) == true)
		{
			$intro->redirect("home", "index");
		}
		else{
			
			$this->error['msg'] = "<div class=\"error_msg\">Login error. Please check admin name/password.</div>";
			$this->error['user'] = $this->error['pass'] = '';
			unset($password);
			$this->Login();
			//echo $this->error['msg'];
			exit();
		}
	}


	################################################################################
	#------------------------------------------------------------------------------#
	#  logout
	#------------------------------------------------------------------------------#
	################################################################################
	function Logout() {
		global $intro;

	//	$intro->session->stop();
		$_SESSION['admin']=null;
    	$this->Login();

	}
	function Forgot(){
		global $intro;

		$username = trim($intro->input->post('username'));
		$email = trim($intro->input->post('email'));
		
		include("style/forgot.html");

	}
	function do_Forgot(){
		global $intro;


		$username = trim($intro->input->post('username'));
		$email = trim($intro->input->post('email'));

		$result = $intro->db->query("SELECT * FROM ".PREFIX."_admin WHERE adm_username='$username' AND email='$email';");
		
		if($intro->db->returned_rows == 1){


			$new_pwd = $this->new_pwd();
			$md5_password = $intro->pwd($new_pwd);
			$sql = $intro->db->query("UPDATE ".PREFIX."_admin SET adm_password='$md5_password' WHERE email='$email' and adm_username='$username';");

			$subject = "New password";
			$message = "
			Hello $username,

			You are receiving this email because you have (or someone pretending to be you has)
			requested a new password be sent for your account on {$intro->option['site_name']}.

			Here it is below.
			--------------------------
			admin name: $username
			Password: $new_pwd
			--------------------------
			You may login below:
			{$intro->url}

			You can of course change this password yourself via the profile page. 
			If you have any difficulties please contact the webmaster.
			
			--
			-Thanks
			{$intro->option['site_name']}

			This email was automatically generated.
			Please do not respond to this email or it will ignored.";

			$message = nl2br($message);

			$intro->send_email($email, $username,"New admin password!",$message);
			
			msg_redirect("تم توليد كلمة مرور جديدة وارسالها الى بريدك الالكتروني .... الرجاء قم بفحص بريدك الالكتروني ","login.php","5");

		}else{
			$this->error = "<center><font class=\"error\">خطأ: تأكد من اسم المشرف أو البريد الالكتروني</font></center><br>";

			$this->Forgot();
			die();
		}
	}
	function new_pwd(){
		$chars = "abchefghjkmnpqrstuvwxyz0123456789";
		srand((double)microtime()*1000000);
		$i = 0;
		$pwd = '';
		while ($i <= 7) {
			$num = rand() % 33;
			$tmp = substr($chars, $num, 1);
			$pwd = $pwd . $tmp;
			$i++;
		}
		return $pwd;
	}
}

$login = new login_error();
$maa = trim($intro->input->get_post('maa'));

if( $maa != '' && method_exists($login,$_REQUEST['maa']) )
{
	try {
		$login->$maa();
	} catch (Exception $e) {
		echo 'Caught exception: ',  $e->getMessage(), "\n";
	}
	
}else{
	$login->Login();
}
?>