<?php

if (!defined('CHECK_ME')) { exit; }

class auth
{
	public $db;
	var $flag;
	
	public function __construct($db)
    {
        $this->db = $db;
		
		//if($this->flag == '') $this->flag = 'admin';
        
    }

	function check_admin_and_login($user_name,$password)
	{
		global $intro;
		
		$result = $this->db->query("SELECT * FROM ".PREFIX."_admin WHERE adm_username='$user_name' AND adm_password='$password'");
		
		if($this->db->returned_rows ==1)
		{
			$row = $this->get_Admin($user_name,$password);

			$_SESSION[$this->flag] = array(
				'adminid'		=> $this->_xor($row['adminid']),
				'admin_name'	=> $this->_xor($row['adm_username']),
				'password'		=> $this->_xor($row['adm_password']),
				'ipaddress'		=> $this->_xor($row['ipaddress']),
				'lastlogin'		=> $row['lastlogin'],
				'type'			=> $row['type'],
			);
			
			//print_r($_SESSION[$this->flag]);die();

			//setcookie("admin","$info",0);
			$user_agent = $this->user_agent();
			$this->db->query("UPDATE ".PREFIX."_admin SET ipaddress='{$intro->ip()}',/*user_agent='$user_agent',*/ lastlogin=NOW() WHERE adminid='{$row['adminid']}'");
			
			return true;
		}
		return false;
	}
	function auth_admin()
	{
		if( isset($_SESSION[$this->flag]) && is_array($_SESSION[$this->flag]))
		{			
			if( isset($_SESSION[$this->flag]['adminid']) )
			{	
				$adminid = $this->_xor($_SESSION[$this->flag]['adminid']);
				$passwd = $this->_xor($_SESSION[$this->flag]['password']);
				$adminid = intval(addslashes($adminid));
				if ($adminid != 0 AND $passwd != "")
				{
					$result = $this->db->query("SELECT adm_password FROM ".PREFIX."_admin WHERE adminid=$adminid; ");//and user_agent='".$this->user_agent()."'
					$row = $this->db->fetch_assoc($result);
					
					if($row['adm_password'] == $passwd && $row['adm_password'] != "") {
						return 1;
					}
				}
			}
		}
		return 0;
	}
	function sess_admin(){
		 
		 return array(
			'adminid'		=> $this->_xor($_SESSION[$this->flag]['adminid']),
			'admin_name'	=> $this->_xor($_SESSION[$this->flag]['admin_name']),
			'ip'		=> $this->_xor($_SESSION[$this->flag]['ipaddress']),
			'lastlogin'		=> $_SESSION[$this->flag]['lastlogin'],
			'type'			=> $_SESSION[$this->flag]['type'],
		 );
	
	}

	function _xor($InputString, $KeyPhrase=169){

		$retString='';
		for ($j = 0; $j < strlen($InputString); $j++)
			  $retString .= chr(ord(substr($InputString, $j, 1)) ^ $KeyPhrase);
		
		return $retString;
	}
	function user_agent()
	{
		return (md5((isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '') . 'zZ9xX5Vv_SoSo'));
	}
	function auth_module($module_name)
	{
		return false;
	}
	
	function get_Admin($user_name,$password)
	{
		
		$result = $this->db->query("SELECT * FROM ".PREFIX."_admin WHERE adm_username='$user_name' AND adm_password='$password'");
		$row = $this->db->fetch_assoc($result);
		
		return $row;	
	}
	function admin_data($adminid=0)
	{
		
		if($adminid == 0) $adminid = $this->_xor($_SESSION[$this->flag]['adminid']);
		if($adminid == 0) die("Erro: unable to get adminid from sssion");
		
		$result = $this->db->query("SELECT * FROM ".PREFIX."_admin WHERE adminid='$adminid'");
		$row = $this->db->fetch_assoc($result);
		
		return $row;	
	}
	function adminid()
	{		
		return $this->_xor($_SESSION[$this->flag]['adminid']);
	}

	function check_user_and_login($email,$password)
	{
		global $intro;
		
		$ip = $intro->input->server('REMOTE_ADDR');
		$result = $intro->db->query("SELECT * FROM ".PREFIX."_users WHERE email='$email' AND password='$password' ");
		
		if($this->db->returned_rows >0)
		{
			$row = $this->get_user($email,$password);
			$_SESSION['user'] = array(
				'userid'	 => $this->_xor($row['userid']),
				'email'	 => $this->_xor($row['email']),
				'fullname'	 => $this->_xor($row['fullname']),
				'password'	 => $this->_xor($row['password']),
				'ipaddress'	 => $this->_xor($row['ip']),
				'last_login' => $row['date_login'],
				
			);
			if($row['status'] != 1){
			  
				
				//	$intro->app_redirect("login",'Login' ,"?msg= الحساب معطل  ");
					
				pHeader();
				$url=$intro->uri_links("login","Login",0,'');
				$intro->SuccessMsg("{$intro->lang['suspend_title']}", get_msg('suspended_msg') ,$url, 15000);
				pFooter();
				die();
			}
			
			$this->update_cart_to_userid($row['userid']);
		
			$user_agent = $this->user_agent();
			$this->db->query("UPDATE ".PREFIX."_users SET ip='$ip', date_login=NOW() ,user_agent='$user_agent' WHERE userid='{$row['userid']}'");
			$this->login_attempts_clear($email);
			
			return true;
		}
		return false;
	}
	function update_cart_to_userid($userid=0){
		global $intro;
		$sessid = session_id();
		$userid=intval($userid);
		$intro->db->query("UPDATE ".PREFIX."_cart set userid=$userid where sessid='$sessid';");
	}
	function update_sess_fullname($fullname)
	{
		$_SESSION['user']['fullname'] =  $this->_xor($fullname);
	}
	function update_sess_pass($pass)
	{
		$_SESSION['user']['password'] =  $this->_xor($pass);
	}
	function get_user($email,$password)
	{

		$result = $this->db->query("SELECT * FROM ".PREFIX."_users WHERE email='$email' AND password='$password'");
		$row = $this->db->fetch_assoc($result);
		
		return $row;	
	}
	
	function sess_user($field=1){
		 
		 $user_array = array(
			'userid'		=> $this->_xor($_SESSION['user']['userid']),
			'email'		=> $this->_xor($_SESSION['user']['email']),
			'fullname'		=> $this->_xor($_SESSION['user']['fullname']),
			'password'		=> $this->_xor($_SESSION['user']['password']),
			'ipaddress'		=>$this->_xor( $_SESSION['user']['ipaddress']),
			'last_login'	=> $_SESSION['user']['last_login'],
			
		 );
		 
		return (($field == 1) ? $user_array : $user_array[$field]);
		//return $user_array;
	}
	
	function check_user($email,$password)
	{
		$result = $this->db->query("SELECT * FROM ".PREFIX."_users WHERE email='$email' AND password='$password'");
		
		return ( ($this->db->returned_rows ==1) ? true : false );
	}

	function check_email($email)
	{		
		$result = $this->db->query("SELECT email FROM ".PREFIX."_users WHERE email='$email' ");
		
		return ( ($this->db->returned_rows ==1) ? true : false );
	}
	
	function check_active_code($email,$reg_code)
	{
		$result = $this->db->query("SELECT email,reg_code FROM ".PREFIX."_users WHERE email='$email' AND reg_code='$reg_code' AND reg_code!='' AND status='0'");
		
		return ( ($this->db->returned_rows ==1) ? true : false );
	}
	
	#user

	
	function user_id()
	{		
		return $this->_xor($_SESSION['user']['userid']);
	}
	
//
	function auth_user()
	{
		if(isset($_SESSION['user'])){
		
			$userid = $this->_xor($_SESSION['user']['userid']);
			$passwd = $this->_xor($_SESSION['user']['password']);

			$userid = addslashes($userid);
			$userid = intval($userid);
			if ($userid != "" AND $passwd != "") {
				$result = $this->db->query("SELECT password FROM ".PREFIX."_users WHERE userid='$userid' and user_agent='".$this->user_agent()."'");
				$row = $this->db->fetch_assoc($result);
				$pass = $row['password'];
				if($pass == $passwd && $pass != "") {
					   return 1;
				}
			}
		}
		return 0;
	}

	


	function get_user_id($userid)
	{
		$result = $this->db->query("SELECT * FROM ".PREFIX."_users WHERE userid='$userid'");
		$row = $this->db->fetch_assoc($result);
		
		return $row;	
	}
	function login_attempts_save($email)
	{
		global $intro;
		
		$result = $this->db->query("SELECT * FROM ".PREFIX."_users WHERE email='$email'");
		
		if( $this->db->returned_rows == 1)		
		$this->db->query("INSERT INTO ".PREFIX."_users_fail SET email = '$email', ip_address = INET_ATON('{$intro->ip()}'), time = CURRENT_TIMESTAMP;");
		
	}
	function login_attempts_clear($email)
	{
		global $intro;
		
		$this->db->query("DELETE FROM ".PREFIX."_users_fail where email = '$email' ;");
	}
	function login_attempts_check()
	{
		
		$throttle = array(5 => 1, 10 => 2, 15 => 5, 20 => 10);

		$result = $this->db->query("SELECT MAX(time) AS attempted FROM ".PREFIX."_users_fail");
		$row = $this->db->fetch_assoc($result);
		//$this->db->free_result($result);
		
		$latest_attempt = (int) date('U', strtotime($row['attempted'])); 
		
		$result = $this->db->query("SELECT COUNT(1) AS failed FROM ".PREFIX."_users_fail WHERE time > DATE_SUB(NOW(), INTERVAL 15 minute)");
		$row = $this->db->fetch_assoc($result);
		//$this->db->free_result($result);
		
		$failed_attempts = (int) $row['failed'];

		//echo "<dir dir=ltr>failed_attempts = $failed_attempts | latest_attempt = $latest_attempt<div>";	
		
		krsort($throttle);
		foreach ($throttle as $attempts => $delay) 
		{
			if ($failed_attempts > $attempts) {
				
				$remaining_delay = (time() - $latest_attempt) - $delay;

				if ($remaining_delay < 0) {
					return 'You must wait <b>' . abs(round($remaining_delay/60)) . '</b> Minutes before next login.';
				}
				break;
			}
		}
	}			 

}

?>