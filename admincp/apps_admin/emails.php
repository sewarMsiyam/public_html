<?php

class Emails_AppAdmin extends Intro_AppsAdmin{

	var $appname = null;
	var $base = null;
	var $img_path;
	
	function __construct($appname,$base,$img_path="")
	{
		$this->appname = $appname;
		$this->base = $base;
		$this->img_path = $img_path;
	}

	function index(){
		global $admin, $intro, $acct_type,$status_val,$array,$sess_admin,$option;
		
	
	/***************************/

	$contact_emil=$users_emil='';
	$sql2 = $intro->db->query("SELECT * FROM ".PREFIX."_contactus  order by cid desc ");
		while($row =  $intro->db->fetch_assoc($sql2)){
			@extract($row);
		
			$contact_emil.="$email, ";
			
			
		}	
		$sql3 = $intro->db->query("SELECT * FROM ".PREFIX."_users  order by userid desc");
		while($row =  $intro->db->fetch_assoc($sql3)){
			@extract($row);
		
			$users_emil.="$email, ";
		}
		
		echo "<div class=\"card border-info\">
			  <div class=\"card-header text-dark bg-info\">
				Contact Us emails
			  </div>
			  <div class=\"card-body\">
			  <textarea dir=ltr   class='form-control' >$contact_emil</textarea>
			  </div>
			  </div>
			  
			  
			  <div class=\"card border-info\">
			  <div class=\"card-header text-dark bg-info\">
				Users emails
			  </div>
			  <div class=\"card-body\">
			  <textarea dir=ltr  class='form-control' >$users_emil</textarea>
			  </div>
			  </div>
			  ";

	}
	
	
	

}
?>