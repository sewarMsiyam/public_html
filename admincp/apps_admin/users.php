<?PHP
##############################################
#	Intro Technologies LTD.
#	Code By Mohammed AbuAbed - Intro.ps 
#	Email: info@intro.ps 
#	Support: http://intro.ps/support.php
#   Date: 2015-06-24 Time: 12:45:58
#	AppName: users
##############################################

class Users_AppAdmin extends Intro_AppsAdmin{

	var $appname = null;
	var $base = null;
	var $img_path;
	
	function __construct($appname,$base,$img_path="")
	{
		$this->appname = $appname;
		$this->base = $base;
		$this->img_path = $img_path;
	}
	function error($index=""){
		global $error;
		
		return isset($error[$index])?$error[$index]:"";
	}
	
	function nav(){
		global $intro,$sess_admin;
		
		echo policy($sess_admin['adminid'],$this->appname.".php");
		
		echo "<ul class=\"nav justify-content-center mb-2\">
				  <li class=\"nav-item mx-2\">
					<a class=\"btn btn-"._css_active("index")."  px-3\" href=\"{$this->base}/index\">
					<i class=\"px-2 fa-solid fa-users\"></i> ".$intro->lang["users_appname"]." </a>
				  </li>
				  <li class=\"nav-item mx-2\">
					<a class=\"btn btn-"._css_active("Form")." px-3 p_add\" href=\"{$this->base}/Form?t=add\">
					<i class=\"px-2 fa-solid fa-plus\"></i> ".$intro->lang["users_add"]."</a>
				  </li>
				</ul>";
				
	}

	
	function index(){
		global $intro,$array;

		$qry = "";
		$page = intval( $intro->input->get_post("page") );
		$order = trim( $intro->input->get_post("order") );
		$search_txt = trim( $intro->input->get_post("search_txt") );

		$this->nav();

		if($search_txt !=""){
			$qry = " where fullname  LIKE '%$search_txt%' or email  LIKE '%$search_txt%' or city  LIKE '%$search_txt%' ";
		}
		
		if ($order=="") $order="userid:desc";
		$order = str_replace(":", " " , $order);

		$rows_per_page = 30;
		if ($page==0) $page=1;
		$nexlimit = ($page*$rows_per_page)-$rows_per_page;

		$result = $intro->db->query("SELECT * from ".PREFIX."_users $qry order by $order  limit $nexlimit,$rows_per_page");
		$totrows = $intro->db->returned_rows;
		
		$sql_all_rows = $intro->db->query("SELECT userid from ".PREFIX."_users $qry ");	
		$totalrows = $intro->db->returned_rows;
		while($myrow = $intro->db->fetch_assoc($result)){
			@extract($myrow);			
			$i++;
			
			$country = isset($array['country'][$country])?$array['country'][$country]:'-';
			
			if($status == 0) $lab = "danger";
			if($status == 1) $lab = "success";
			if($status == 2) $lab = "default";
			if($status == 3) $lab = "danger";
			
			$cc = "";//file_get_contents("http://ipinfo.io/{$ip}/country");
			$data.= "
			<tr >
				<td class=\"center\"><input type=\"checkbox\" class='child' value=\"$userid\" name=\"selected_fld[]\"></td>
				<td class=\"center\">$userid</td>
				<td>$fullname</td>
				<td>$email</td>
				<td>$country</td>
				<td><a href='https://www.geolocation.com/?ip=$ip#ipresult' target=_blank>$ip</a></td>
				<td>$date_login</td>
				<td><span class=' bg-$lab'>".@$array['user_status'][$status]."  </span></td>
				<td class=\"center\"> 
					<a class=\"btn btn-info p_edit btn-sm\" href=\"{$this->base}/Form?t=edit&amp;userid=$userid\" title=\"".$intro->lang["edit"]."\"><i class=\" fa-solid fa-pen-to-square\"></i></a>
					<a class=\"btn btn-danger btn-sm p_del intro_ui_del btn-sm\" href=\"{$this->base}/Del?userid=$userid\" OnClick=\"return false;\" title=\"".$intro->lang["del"]."\"><i class=\" fa-solid fa-trash\"></i></a>
				</td>
			</tr>";
		}
		

		echo "<div class=\"card my-3 \">
				  <div class=\"card-header  mb-3\">
					<i class=\"px-2 fa-solid fa-search\"></i> Search Form
				  </div>
				  <div class=\"card-body nopadding\">
				  
					<form class=\"row nopadding\"  action=\"\" method=\"post\">
					 
					  <div class=\"col-auto\">
						<input type=\"text\" class=\"form-control\" name=\"search_txt\" value=\"$search_txt\" placeholder=\"type a name or Email\">
					  </div>
					  <div class=\"col-auto\">
					  <input type=\"hidden\" name=\"maa\" value=\"Main\">
						<button type=\"submit\" class=\"btn btn-primary mb-3\"> <i class=\"fa-solid fa-search fa-sm\"></i> Search</button>
					  </div>
					</form>
				  </div>
				</div>";
				
		echo "
		<div class=\"card border-info\">
			  <div class=\"card-header text-dark bg-info\">
				<i class=\"px-2 fa-solid fa-users\"></i> ".$intro->lang["users_cur"]." ($totalrows)
			  </div>
			  <div class=\"card-body\">
			  <form action=\"{$this->base}/multiDel\" method=\"post\">
			   <div class=\"table-responsive\">
				<table class=\"table table-striped table-sm table-hover\">
					  <thead  class=\"table-dark\">
						<tr>
							<th><input type=\"checkbox\" id=\"parent\" /></th>
							<th scope=\"col\">ID "._sort_th("userid","index")."</th>
							<th scope=\"col\" style='width:100px;'>".$intro->lang["users_fullname"]." "._sort_th("fullname","index")." </th>
							<th scope=\"col\">".$intro->lang["users_email"]." "._sort_th("email","index")." </th>
							<th scope=\"col\">".$intro->lang["users_country"]." "._sort_th("country","index")." </th>
							<th scope=\"col\">IP "._sort_th("country","index")." </th>
							<th scope=\"col\">".$intro->lang["users_date_login"]." "._sort_th("date_login","index")." </th>
							<th scope=\"col\">".$intro->lang["users_status"]." "._sort_th("status","index")." </th>
							<th scope=\"col\">".$intro->lang["options"]."</th>
						</tr>
					  </thead>
					  <tbody>
					  $data
					
				</tbody>
			</table></div>
			<button  name=\"name\" type=\"submit\"  OnClick=\"return confirm('Are you sure? please backup data first.');\" class='btn btn-danger btn-sm'> Delete Selected </button>
			</form>";
		$order = str_replace(" ", ":" , $order);
		
		echo "<center class='pagination' >".pagination3("{$this->base}/index?search_txt=$search_txt&amp;order=$order", $totalrows, $rows_per_page, $page)."</center>";
		echo"</div></div>";
		
		
		echo " <script>
		$(document).ready(function() {
		  $(\"#parent\").click(function() {
			$(\".child\").prop(\"checked\", this.checked);
		  });

		  $('.child').click(function() {
			if ($('.child:checked').length == $('.child').length) {
			  $('#parent').prop('checked', true);
			} else {
			  $('#parent').prop('checked', false);
			}
		  });
		});
		</script>";
		
	}
	function multiDel(){
		global $intro,$error,$sess_admin;

		$selected_fld = $_POST['selected_fld'];
		$count = count($selected_fld);
		
		policy($sess_admin['adminid'],$this->appname.".php" , "del");
		
		for ($i=0; $i<$count; $i++) {

			$sql = $intro->db->query("DELETE FROM ".PREFIX."_users WHERE userid='{$selected_fld[$i]}' ");
		}

		$intro->redirect($this->appname);
	}
	function Form($t=""){
		global $intro,$error,$sess_admin,$array;
		global $fullname,$email,$password,$address,$city,$country,$tel,$status,$reg_date,$date_login,$ip;
		
		if($error || $_POST != null) @extract($_POST);
		$IF = intval( $intro->input->get_post("IF") );		
		$userid = intval( $intro->input->get_post("userid") );		
		$t = $t==""?$intro->input->get_post("t"):$t;
		
		if($IF != 1)
		$this->nav();
		
		if($t == "edit")
		{			
			policy($sess_admin['adminid'],$this->appname.".php" , "edit");
			
			$sql = $intro->db->query("SELECT * FROM ".PREFIX."_users where userid='$userid'");
			$row = $intro->db->fetch_assoc($sql);
			@extract($row);

			$btn['legend_name'] = $intro->lang["users_edit"]." <b>$userid</b>";
			$btn['legend_icon'] = "fa-solid fa-edit";
			$btn['name'] = $intro->lang["save_changes"];
			$btn['img_icon'] = "fa-solid fa-edit";		   
			$btn['action'] = "doEdit";
			$passEdit = "(اكتب كلمة سر جديدة في حالة التغيير)";
		}
		elseif($t == "add")
		{
			policy($sess_admin['adminid'],$this->appname.".php" , "add");
			
			$btn['legend_name'] = $intro->lang["users_add"];
			$btn['legend_icon'] = "fa-solid fa-plus";
			$btn['name'] = $intro->lang["save"];
			$btn['img_icon'] = "fa-solid fa-plus";		   
			$btn['action'] = "doAdd";
			$passEdit = "";
			$status = $status==""?1:$status;
			
		}		
		
	echo "
	<div class=\"card border-info\">
			  <div class=\"card-header text-dark bg-info\">
				<i class=\"{$btn['legend_icon']}\"></i> {$btn['legend_name']}  
			  </div>
			  <div class=\"card-body\">
			  <form method=\"POST\" name=\"form_add\"  action=\"{$this->base}/{$btn['action']}\" enctype=\"multipart/form-data\">
			  <table class=\"table table-sm \">
					 <tbody>
			<tr>
				<td>".$intro->lang["users_fullname"]." :  <span style='color:#ff0000'>*</span></td>
				<td><input  type=\"text\" name=\"fullname\" value=\"$fullname\" class='form-control'> {$this->error('fullname')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["users_email"]." :  <span style='color:#ff0000'>*</span></td>
				<td><input dir=\"ltr\" type=\"text\" name=\"email\" value=\"$email\" class='form-control'> {$this->error('email')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["users_password"]." :  <span style='color:#ff0000'>*</span></td>
				<td><input dir=\"ltr\" type=\"text\" name=\"password\" value=\"\" class='form-control'> $passEdit {$this->error('password')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["users_address"]." : </td>
				<td><input dir=\"ltr\" type=\"text\" name=\"address\" value=\"$address\" class='form-control'> {$this->error('address')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["users_city"]." : </td>
				<td><input dir=\"ltr\" type=\"text\" name=\"city\" value=\"$city\" class='form-control'> {$this->error('city')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["users_country"]." : </td>
				<td>".form_select_array("country",$array['country'],$country)." {$this->error('country')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["users_tel"]." : </td>
				<td><input dir=\"ltr\" type=\"text\" name=\"tel\" value=\"$tel\" > {$this->error('tel')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["users_status"]." : </td>
				<td>".form_select_array("status",$array['user_status'],$status)." {$this->error('status')}</td>
			</tr>";
			if($t=="edit"){
			echo "
			<tr>
				<td>".$intro->lang["users_reg_date"]." : </td>
				<td>$reg_date</td>
			</tr>
			<tr>
				<td>".$intro->lang["users_date_login"]." : </td>
				<td>$date_login</td>
			</tr>
			<tr>
				<td>".$intro->lang["users_ip"]." : </td>
				<td>$ip</td>
			</tr>";
			}
			echo "
			<tr>
				<td class=\"center\" ></td>
				<td class=\"center\" >
					<input type=\"hidden\" name=\"app_name\"  value=\"{$this->appname}\">
					<input type=\"hidden\" name=\"t\"  value=\"$t\">
					<input type=\"hidden\" name=\"userid\"  value=\"$userid\">
					<input type=\"hidden\" name=\"IF\"  value=\"$IF\">
					<button type=\"submit\" name=\"app_action\" class='btn btn-primary' value=\"{$btn['action']}\">
					<i class=\"{$btn['img_icon']}\"></i> {$btn['name']} </button>
					
				</td>
			</tr>
			</table>
			</form>
			</div>
		</div>";
	}

	############################################################################

	function doAdd(){
		global $intro,$error;
		$fullname = trim( $intro->input->post('fullname') );
		$email = trim( $intro->input->post('email') );
		$password = trim( $intro->input->post('password') );
		$address = trim( $intro->input->post('address') );
		$city = trim( $intro->input->post('city') );
		$country = intval( $intro->input->post('country') );
		$tel = trim( $intro->input->post('tel') );
		$status = intval( $intro->input->post('status') );
		$reg_date = trim( $intro->input->post('reg_date') );
		$date_login = trim( $intro->input->post('date_login') );
		$ip = trim( $intro->input->post('ip') );
		
		if($fullname == "" || $email == "" || $password == ""){

			if($fullname == ""){ $error['fullname'] = "<span class=error>".$intro->lang["required"]."</span>"; }
				if($email == ""){ $error['email'] = "<span class=error>".$intro->lang["required"]."</span>"; }
				if($password == ""){ $error['password'] = "<span class=error>".$intro->lang["required"]."</span>"; }
				
			
			
			$this->Form("add");
			die();
		}		
		
		$data["fullname"] = $intro->input->post('fullname');
		$data["email"] = $intro->input->post('email');
		$data["password"] = $intro->pwd($password);
		$data["address"] = $intro->input->post('address');
		$data["city"] = $intro->input->post('city');
		$data["country"] = $intro->input->post('country');
		$data["tel"] = $intro->input->post('tel');
		$data["status"] = $intro->input->post('status');
		$data["reg_date"] = date("Y-m-d H:i:s");
				 
		$intro->db->insert(PREFIX."_users",$data);
		
		//if($intro->input->post('IF') == 1) die("<script>parent.location.reload(true);parent.$.fancybox.close();</script>");
		
		$intro->redirect($this->appname);
	}

	############################################################################

	function doEdit(){
		global $intro,$array;
			
		
		$password = trim($intro->input->post('password'));
		
		$data["fullname"] = $intro->input->post('fullname');
		$data["email"] = trim($intro->input->post('email'));
		
		if(strlen($password) > 2)
		$data["password"] = $intro->pwd($password);
	
		$data["address"] = $intro->input->post('address');
		$data["city"] = $intro->input->post('city');
		$data["country"] = $intro->input->post('country');
		$data["tel"] = $intro->input->post('tel');
		$data["status"] = $intro->input->post('status');
		
		
		$userid = intval( $intro->input->post('userid') );

		$intro->db->update(PREFIX."_users",$data,"userid=$userid");
		
		//if($intro->input->post('IF') == 1) die("<script>parent.$.fancybox.close();</script>");
		
		$intro->redirect($this->appname);
	}
	
	function Del(){
		global $intro,$sess_admin,$array;
		
		$userid = intval( $intro->input->get_post('userid') );
		
		policy($sess_admin['adminid'],$this->appname.".php" , "del");

		$sql = $intro->db->query("DELETE FROM ".PREFIX."_users WHERE userid=$userid ");

		$intro->redirect($this->appname);
	}
	
	############################################################################

	function Active(){
		global $intro,$userid;

		$sql = $intro->db->query("UPDATE ".PREFIX."_users SET status='1' WHERE userid='$userid' ");

		$intro->redirect($this->appname);

	}

}//end class Users
?>