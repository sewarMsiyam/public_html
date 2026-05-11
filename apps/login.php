<?PHP
class Login_App extends Intro_Apps
{
	public $appname = null;
	public $base    = null;

	public function __construct($appname, $base)
	{
		$this->appname = $appname;
		$this->base    = $base;
	}
	public function error($index = "")
	{
		global $error;

		return isset($error[$index]) ? $error[$index] : "";
	}
	public function index()
	{
		global $intro;

		if ($intro->auth->auth_user()) {
			$intro->app_redirect("account", "index");
		} else {
			$this->Login();
			//var_dump($_SESSION);
		}
	}

	public function Login()
	{
		global $error, $intro, $order;

		pHeader();
		TableOpen();

		$order = $intro->input->get('order');
		

		$create_account =
			"<br><br>
		<center><h2>{$intro->lang['askaccount']}</h2></center>
		<br>
		<center>
		<a href='{$intro->uri_links('register', 'index', 0, '')}' class='btn btn-success'>
		{$intro->lang['createaccount']}</a>
		</center>";

		if($intro->maa->lang=='en'){$position="right";}else{$position="left";}
		$login_form = "
    <form method=\"POST\" action=\"$this->base/doLogin\" >
        <div class=\"mb-3 row\">
            <label class=\"col-sm-3 col-form-label\">{$intro->lang['email']}</label>
            <div class=\"col-sm-8\">
                <input class=\"form-control\" type=\"email\" name=\"email\" value=\"{$intro->input->post('email')}\" placeholder='example@example.com' required >
                {$this->error('user')}
            </div>
        </div>

        <div class=\"mb-3 row\">
            <label class=\"col-sm-3 col-form-label\">{$intro->lang['passowrd']}</label>
            <div class=\"col-sm-8 position-relative\">
                <input class=\"form-control\" type=\"password\" name=\"password\" id=\"password\" required >
                <i class=\"fa fa-eye toggle-password\" id=\"togglePassword\" style=\"position: absolute; top: 50%; $position: 25px; transform: translateY(-50%); cursor: pointer;\"></i>
                {$this->error('pass')}
            </div>
        </div>

        <div class=\"mb-3 row\">
            <label class=\"col-sm-3 col-form-label\"></label>
            <div class=\"col-sm-8\">
                <button class=\"btn btn-primary\">{$intro->lang['login']}</button>
                <a href=\"{$intro->uri_links($this->appname, 'ResetPass')}\" class=\"small_link\">
                    {$intro->lang['forgotpass']}
                </a>
            </div>
        </div>

        <div class='error_log'>{$this->error('msg')}</div>
    </form>
";

?>

		<div class="container-fluid">
			<h4 class="section-title position-relative text-uppercase mx-xl-5 mb-4"><span class="bg-secondary px-4"><?php echo $intro->lang['login'] ?></span></h4>
			<div class="row px-xl-5">
				<div class="col-lg-7 mb-5">
					<div class='bg-light  p-5'>
						<?php echo $login_form ?>
					</div>
				</div>
				<div class="col-lg-5 mb-5">
					<div class='bg-light  p-5'>
						<?php echo $create_account ?>
					</div>
				</div>

			</div>
		</div>
		<!-- Breadcrumb End -->

	<?php

		pFooter();
	}
	public function doLogin()
	{
		global $intro, $username, $password, $remember, $error;

		$ip = $intro->input->server('REMOTE_ADDR');

		$cap      = trim(strip_tags($intro->input->post('cap')));
		$email    = trim(strip_tags($intro->input->post('email')));
		$password = strip_tags($intro->input->post('password'));
		$order    = $intro->input->post('order');

		if ((! $email) || (! $password)) {
			$reqmsg = "(<span class=error>Required!</span>)";

			if (empty($email)) {
				$error['email'] = $reqmsg;
			}
			if (empty($password)) {
				$error['pass'] = $reqmsg;
			}
			$this->login();
			exit();
		}
		$attempts = $intro->auth->login_attempts_check();

		if ($attempts != '') {

			$error['msg'] = "<sapn class=error>$attempts</span>";
			$this->login();
			exit();
		}
		$password = $intro->pwd($password);

		//echo $password;

		if ($intro->auth->check_user_and_login($email, $password)) {

			$intro->auth->login_attempts_clear($email);
			if ($order == 1) {
				$intro->app_redirect("order", "paymentmethod");
			} else {
				$intro->app_redirect("account", "index");
			}
		} else {
			$intro->auth->login_attempts_save($email);
			unset($password);
			$error['msg'] = "<br/><font color=red><center>" . get_msg('login_error') . "</cener></font>";
			$this->Login();
			die();
		}
	}
	public function Logout()
	{
		global $intro;

		//$intro->session->stop();

		$_SESSION['user'] = '';
		unset($_SESSION['user']);
		$intro->app_redirect($this->appname, 'Login');
	}

	################################################################################
	#------------------------------------------------------------------------------#
	#  Forgot Password
	#------------------------------------------------------------------------------#
	################################################################################
	public function ResetPass()
	{
		global $error, $intro;

		pHeader();
		TableOpen();

		$create_account =
			"<br><br>
		<center><h2>{$intro->lang['askaccount']}</h2></center>
		<br>
		<center>
		<div> <a href='{$intro->uri_links('register', 'index', 0, '')}' class='btn btn-success w-full'>
		{$intro->lang['createaccount']}</a>
		</div>
		<div class=\"mt-2\" > <a href='{$intro->uri_links('login', 'index', 0, '')}' class='btn btn-primary w-full'>
		{$intro->lang['login']}</a>
		</div>
		</center>";
		$reset_form = "
			<div class='forgotpass'>
				<br>
			<center> {$intro->lang['resetpass']} </center>
			<br>
			</div>
	   <form method='POST' action='$this->base/doResetPass'>
		{$error['msg']}
		<div class=\"mb-3 row\">
            <label class=\"col-sm-2 col-form-label\">{$intro->lang['email']}</label>
            <div class=\"col-sm-7\">
              <input type='email' name='email'  class=\"form-control\" required value='{$intro->input->post('email')}' placeholder='example@example.com' > 
				{$this->error('user')}
            </div>
        </div>
		 <div class=\"mb-3 row\">
            <label class=\"col-sm-2 col-form-label\"></label>
            <div class=\"col-sm-7\">
                
                <input type='submit' class=\"btn btn-primary\"  value=' {$intro->lang['restpas']} '>
            </div>
        </div>

		</form>";

	?>

		<div class="container-fluid">
			<h4 class="section-title position-relative text-uppercase mx-xl-5 mb-4"><span class="bg-secondary px-4"><?php echo $intro->lang['returninfo'] ?></span></h4>
			<div class="row px-xl-5">
				<div class="col-lg-7 mb-5">
					<div class='bg-light  p-5'>
						<?php echo $reset_form ?>
					</div>
				</div>
				<div class="col-lg-5 mb-5">
					<div class='bg-light  p-5'>
						<?php echo $create_account ?>
					</div>
				</div>

			</div>
		</div>
		<!-- Breadcrumb End -->

<?php


		pFooter();
	}

	public function doResetPass()
	{
		global $admin, $intro, $prefix, $db, $email, $error;

		$email = strip_tags($intro->input->post('email'));

		$result = $intro->db->query("SELECT * FROM " . PREFIX . "_users WHERE email='$email' and status=1  ");
		$check  = $intro->db->returned_rows;

		if ($check == 1) {
			$row = $intro->db->fetch_assoc($result);

			$this->SendResetPassword($row["email"], $row["fullname"], $row["userid"]);

			//$intro->app_redirect($this->appname, 'ResetSuccess');

			$url = $intro->app_url('login', 'index');

			pHeader();
			$intro->SuccessMsg(get_msg('doreset_password'), get_msg('login_reset_msg'), $url, 9000);
			pFooter();
		} else {

			//$this->ResetError();
			$error['msg'] = "<span class='error'><center>" . get_msg('reset_error') . "</center></span>";

			$this->ResetPass();
			die();
		}
	}
	public function SendResetPassword($email, $name, $userid)
	{
		global $intro;

		$code = uniqid();

		$link = "{$intro->url}{$intro->uri->lang}/login/newpasword?code=$code&amp;id=$userid&amp;reset_code=" . uniqid() . "&amp;time=" . uniqid() . "&amp;run=" . uniqid() . "";

		$today = date("F j, Y, g:i a");
		$ip    = $_SERVER['REMOTE_ADDR'];

		$emailBody = get_msg('email_reset_pass_link');
		$password  = '';
		$emailBody = str_replace(
			["{link}", "{ip}", "{today}", "{name}", "{email}", "{site_name}", "{site_url}", "{logo}"],
			[$link, $ip, $today, $name, $email, $intro->site_name, $intro->site_url, $intro->logo],
			$emailBody
		);

		$next2hours = date("Y-m-d H:i:s", strtotime("+2 hours"));

		$result = $intro->db->query("UPDATE " . PREFIX . "_users "
			. " set reset_pwd_expire='$next2hours',reg_code='$code' "
			. " WHERE userid='$userid' and status=1  ");

		$intro->send_email($email, $name, $intro->lang['email_subject_reset'], $emailBody);
	}
	public function newpasword()
	{
		global $intro, $error;

		pHeader();

		TableOpen();
		$id   = intval($intro->input->get_post('id'));
		$code = trim($intro->input->get_post('code'));

		$datenow = date("Y-m-d H:i:s");

		$result = $intro->db->query("SELECT fullname,reset_pwd_expire,reg_code,userid FROM " . PREFIX . "_users WHERE reg_code='$code' and userid='$id' and status=1  ");
		$row    = $intro->db->fetch_assoc($result);

		if ($intro->db->returned_rows == 1 && $datenow <= $row['reset_pwd_expire']) {
			$fullname = $row['fullname'];
			
			$create_account =
				"<br><br>
				<center><h2>{$intro->lang['askaccount']}</h2></center>
				<br>
				<center>
				<div> <a href='{$intro->uri_links('register', 'index', 0, '')}' class='btn btn-success w-full'>
				{$intro->lang['createaccount']}</a>
				</div>
				<div class=\"mt-2\" > <a href='{$intro->uri_links('login', 'index', 0, '')}' class='btn btn-primary w-full'>
				{$intro->lang['login']}</a>
				</div>
				</center>";
				
				$form="	<form action=\"$this->base/donewpass\" class=\"type_2\" method=\"POST\" id=\"Register\">
						<div class=\"mb-3 row\">
							<label class=\"col-sm-4 col-form-label\">{$intro->lang['password']}</label>
							<div class=\"col-sm-7\">
							  <input type=\"password\" name=\"password\" id=\"password\"  class=\"form-control\" required value=''  > 
								{$this->error('user')}
							</div>
						</div>
						<div class=\"mb-3 row\">
							<label class=\"col-sm-4 col-form-label\" for=\"password2\">{$intro->lang['returnpass']}</label>
							<div class=\"col-sm-7\">
							  <input type=\"password\" name=\"password2\" id=\"password2\" value=\"\"  class=\"form-control\" required  > 
								{$this->error('user')}
							</div>
						</div>
						<div class=\"mb-3 row\">
							<label class=\"col-sm-4 col-form-label\"></label>
							<div class=\"col-sm-7\">
							<input type='hidden' name='code' value='$row[reg_code]'>
							<input type='hidden' name='id' value='$row[userid]'>
							<input type='hidden' name='reset' value='" . uniqid() . "'>
							<input type='hidden' name='resetcode' value='" . uniqid() . "'>
							<input type='hidden' name='resetsesstion' value='" . uniqid() . "'>
							<input type='submit' class=\"btn btn-primary\"  value=' {$intro->lang['changepass']} '>
							</div>
						</div>
					</form>";
		
			?>
			
			<div class="container-fluid">
			<h4 class="section-title position-relative text-uppercase mx-xl-5 mb-4"><span class="bg-secondary px-4"><?php echo $intro->lang['returninfo'] ?></span></h4>
			<div class="row px-xl-5">
				<div class="col-lg-7 mb-5">
					<div class='bg-light  p-5'>
						<div class='align_right' style='color:blue'>
						<?=$intro->lang['welcome']?> : <span ><?=$fullname?></span>
						</div>
						<br>
						<?php echo $form ?>
					</div>
				</div>
				<div class="col-lg-5 mb-5">
					<div class='bg-light  p-5'>
						<?php echo $create_account ?>
					</div>
				</div>

			</div>
		</div>
		<!-- Breadcrumb End -->
		<?php
			echo "
			<div class='log_left'>

			<!--<div class='lognow'><span>{$intro->lang['reset_pass']}</span></div>-->
			<center><h1>{$intro->lang['reset_pass']}</h1></center>
			<!-- alignright = float right -->
			<div class='align_right' style='color:blue'>
			{$intro->lang['welcome']} : <span >$fullname</span>
			</div>

			<div class=\"row\">
        <div class=\"col-md-4\"> </div>
        <div class=\"col-md-4\">

		

			</div>
        <div class=\"col-md-4\"> </div>
      </div>

			</div>";
		} else {
			$url   = $intro->uri_links('login', 'ResetPass');
			$bodys = get_msg('login_error_reset');
			$intro->SuccessMsg($intro->lang['reset_pass_title'], $bodys, $url, 20000);
		}
		pFooter();
	}
	public function donewpass()
	{
		global $intro, $error, $email;

		$star = "<span style=\"color:red\">*</span>";
		$err  = "<span class=error>{$intro->lang['required']}</span>";

		$v = new validation();

		if ($v->required($intro->input->post('password')) == false) {
			$error['password'] = $err;
		}

		if ($v->min_length($intro->input->post('password'), 6) == false) {
			$error['password'] = "<span class=error>{$intro->lang['pass_min6']}</span>";
		}

		if ($v->required_with($intro->input->post('password'), $intro->input->post('password2')) == false) {
			$error['password'] = "<span class=error>input_password</span>";
		}

		if ($v->match_field($intro->input->post('password'), $intro->input->post('password2')) == false) {
			$error['password'] = "<span class=error>password not match</span>";
		}

		if ($error) {
			$this->newpasword();
			die();
		}

		$password                 = $intro->input->post('password');
		$reg_code                 = $intro->input->post('code');
		$id                       = $intro->input->post('id');
		$data["password"]         = $intro->pwd($password);
		$data["reg_code"]         = "";
		$data["reset_pwd_expire"] = "";

		$intro->db->update(PREFIX . "_users", $data, "reg_code='$reg_code' and userid='$id'");

		$url   = $intro->uri_links('login', 'index');
		$bodys = "{$intro->lang['pass_has_changes']}<br>";

		pHeader();

		$intro->SuccessMsg($intro->lang['changepass'], $bodys, $url, 5000);

		pFooter();
	}
}
?>