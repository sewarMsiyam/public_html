<?PHP
##############################################
#	Intro Technologies LTD.
#	Code By Mohammed AbuAbed - Intro.ps 
#	Email: info@intro.ps 
#	Support: http://intro.ps/support.php
#   Date: 2015-07-15 Time: 12:02:05
#	AppName: contactus
##############################################

class Contactus_AppAdmin extends Intro_AppsAdmin{

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
			
			policy($sess_admin['adminid'],$this->appname.".php");
			
			echo "<ul class=\"nav justify-content-center mb-2\">
				  <li class=\"nav-item mx-2\">
					<a class=\"btn btn-"._css_active("index")."  px-3\" href=\"{$this->base}/index\">
					<i class=\"px-2 fa-solid fa-envelope\"></i> ".$intro->lang["contactus_appname"]." </a>
				  </li>
				  <li class=\"nav-item mx-2\">
					<a class=\"btn btn-"._css_active("Form")." px-3 p_add\" href=\"{$this->base}/Form?t=add\">
					<i class=\"px-2 fa-solid fa-plus\"></i> ".$intro->lang["contactus_add"]."</a>
				  </li>
			
				</ul>";
				
				
	}

	
	function index(){
		global $intro,$array;

		$qry = "";
		$get_active = $intro->input->get_post("active");
		$page = intval( $intro->input->get_post("page") );
		$order = trim( $intro->input->get_post("order") );
		$search_txt = trim( $intro->input->get_post("search_txt") );

		$this->nav();
	
		if($search_txt !=""){
			$qry = " where cname  LIKE '%$search_txt%' ";
		}

      
		
		if ($order=="") $order="cid:desc";
		$order = str_replace(":", " " , $order);

		$rows_per_page = "30";
		if ($page==0) $page=1;
		$nexlimit = $page * $rows_per_page - $rows_per_page;

		$result = $intro->db->query("SELECT * from ".PREFIX."_contactus $qry order by $order  limit $nexlimit,$rows_per_page");
		$totrows = $intro->db->returned_rows;
		
		$sql_all_rows = $intro->db->query("SELECT cid from ".PREFIX."_contactus $qry ");	
		$totalrows = $intro->db->returned_rows;
			while($myrow = $intro->db->fetch_assoc($result)){
			@extract($myrow);
			$data.= "
			<tr >
				<td class=\"center\"><input type=\"checkbox\" class='child' value=\"$cid\" 
				name=\"selected_fld[]\"></td>
				<td class=\"center\">$cid</td>
				<td>$cname</td>
				<td>$email</td>
				<td>$subject</td>
				<td>$datesend</td>
				<td class=\"center\"> 
					<a class=\"btn btn-info p_edit btn-sm\" href=\"{$this->base}/Form?t=edit&cid=$cid\" title=\"".$intro->lang["edit"]."\"><i class=\" fa-solid fa-search\"></i></a>
					<a class=\"btn btn-danger p_del intro_ui_del btn-sm\" href=\"{$this->base}/Del?cid=$cid\" OnClick=\"return false;\" title=\"".$intro->lang["del"]."\"><i class=\" fa-solid fa-trash\"></i></a>
				</td>
			</tr>";
		}
		
	
		echo "<div class=\"card my-3 \">
				  <div class=\"card-header  mb-3\">
					<i class=\"px-2 fa-solid fa-search\"></i> Search Form
				  </div>
				  <div class=\"card-body nopadding pl-2\">
					<form class=\"row ml-3 \"  action=\"\" method=\"post\">
					 
					  <div class=\"col-auto\">
						<input type=\"text\" class=\"form-control\" name=\"search_txt\" value=\"$search_txt\" placeholder=\"Type Text\">
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
				<i class=\"px-2 fa-solid fa-envelope\"></i> ".$intro->lang["contactus_cur"]." ($totalrows)
			  </div>
			  <div class=\"card-body\">
			  <div class=\"table-responsive\">
			   <form action=\"{$this->base}/multiDel\" method=\"post\">
			    <div class=\"table-responsive\">
				<table class=\"table table-striped table-sm table-hover\">
					  <thead  class=\"table-dark\">
						<tr>
							<th><input type=\"checkbox\" id=\"parent\" /></th>
							<th>ID "._sort_th("cid","index")."</th>
							<th>".$intro->lang["contactus_cname"]." "._sort_th("cname","index")." </th>
							<th>".$intro->lang["contactus_email"]." "._sort_th("email","index")." </th>
							<th>".$intro->lang["contactus_subject"]." "._sort_th("subject","index")." </th>
							<th>".$intro->lang["contactus_datesend"]." "._sort_th("datesend","index")." </th>
							<th>".$intro->lang["options"]."</th>
						</tr>
					  </thead>
					  <tbody>
					  $data
					  </tbody>
					 </table></div>
				<button  name=\"name\" type=\"submit\"  OnClick=\"return confirm('Are you sure? please backup data first.');\" class='btn btn-danger btn-sm'> Delete Selected </button>
				</form>
			  </div>
			  ";		
   
		$order = str_replace(" ", ":" , $order);
		echo "<center class='pagination' >".pagination3("{$this->base}/index?search_txt=$search_txt&order=$order", $totalrows, $rows_per_page, $page)."</center>";
		echo "</div>
		</div>";
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

			$sql = $intro->db->query("DELETE FROM ".PREFIX."_contactus WHERE cid='{$selected_fld[$i]}' ");
		}

		revalidateNext('contactus');
		$intro->redirect($this->appname);
	}

	function Form($t=""){
		global $intro,$error,$sess_admin,$array;
		global $cname,$email,$subject,$datesend,$msg,$ip;
		
		if($_GET != null) @extract($_GET);
		if($error || $_POST != null) @extract($_POST);
		
		
		$cid = intval( $intro->input->get_post("cid") );
		
		$t = $t==""?$intro->input->get_post("t"):$t;

		$this->nav();
		if($t == "edit"){
			
			policy($sess_admin['adminid'],$this->appname.".php" , "edit");
			
			$sql = $intro->db->query("SELECT * FROM ".PREFIX."_contactus where cid='$cid'");
			$row = $intro->db->fetch_assoc($sql);
			@extract($row);

			$btn['legend_name'] = $intro->lang["contactus_edit"]." <b>$cid</b>";
			$btn['legend_icon'] = "icon-edit";
			$btn['name'] = $intro->lang["save_changes"];
			$btn['img_icon'] = "icon-floppy";		   
			$btn['action'] = "doEdit";
			$btn['copy'] = "<button class=\"mult_submit\" type=\"submit\" name=\"app_action\" value=\"doAdd\" title=\"add new\">
						<span class=\"icon-floppy\"> حفظ كسجل جديد</span>
					</button>";
		}
		elseif($t == "add"){

			policy($sess_admin['adminid'],$this->appname.".php" , "add");
			
			$btn['legend_name'] = $intro->lang["contactus_add"];
			$btn['legend_icon'] = "icon-plus-squared";
			$btn['name'] = $intro->lang["add_new"];
			$btn['img_icon'] = "icon-plus-squared";		   
			$btn['action'] = "doAdd";
			$btn['copy'] = "";
		}		
			
	echo "
	
		<div class=\"card border-info\">
			  <div class=\"card-header text-dark bg-info\">
				<i class=\"{$btn['legend_icon']}\"></i> {$btn['legend_name']} 
			  </div>
			  <div class=\"card-body\">
			 <form method=\"POST\" name=\"form_add\"  action=\"{$this->base}/{$btn['action']}\" enctype=\"multipart/form-data\">
			  <div class=\"table-responsive\">
			 <table class=\"table table-sm \">
			<tbody>
			<tr>
				<td>".$intro->lang["contactus_cname"]." :  <span style='color:#ff0000'>*</span></td>
				<td><input  type=\"text\" name=\"cname\" value=\"$cname\" class='form-control'> {$this->error('cname')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["contactus_email"]." :  <span style='color:#ff0000'>*</span></td>
				<td><input dir=\"ltr\" type=\"text\" name=\"email\" value=\"$email\"  class='form-control'> {$this->error('email')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["contactus_subject"]." :  <span style='color:#ff0000'>*</span></td>
				<td><input dir=\"ltr\" type=\"text\" name=\"subject\" value=\"$subject\"  class='form-control'> {$this->error('subject')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["contactus_datesend"]." : </td>
				<td><input dir=\"ltr\" type=\"text\" name=\"datesend\" value=\"$datesend\"  class='form-control'> {$this->error('datesend')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["contactus_ip"]." : </td>
				<td><input dir=\"ltr\" type=\"text\" name=\"ip\" value=\"$ip\"  class='form-control' > {$this->error('ip')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["contactus_msg"]." :  <span style='color:#ff0000'>*</span></td>
				<td><textarea name=\"msg\" class='form-control'>$msg</textarea>{$this->error('msg')}</td>
			</tr>
			</tbody>
			</table>		
			</form></div>
			</div>
		</div>";
	}

	############################################################################

	function doAdd(){
		global $intro,$error;
		$cname = trim( $intro->input->post('cname') );
		$email = trim( $intro->input->post('email') );
		$subject = trim( $intro->input->post('subject') );
		$datesend = trim( $intro->input->post('datesend') );
		$msg = trim( $intro->input->post('msg') );
		$ip = trim( $intro->input->post('ip') );
		
		if($cname == "" || $email == "" || $subject == "" || $msg == ""){

			if($cname == ""){ $error['cname'] = "<span class=error>".$intro->lang["required"]."</span>"; }
				if($email == ""){ $error['email'] = "<span class=error>".$intro->lang["required"]."</span>"; }
				if($subject == ""){ $error['subject'] = "<span class=error>".$intro->lang["required"]."</span>"; }
				if($msg == ""){ $error['msg'] = "<span class=error>".$intro->lang["required"]."</span>"; }
				
			
			
			$this->Form("add");
			die();
		}		
		
		$data["cname"] = $intro->input->post('cname');
		$data["email"] = $intro->input->post('email');
		$data["subject"] = $intro->input->post('subject');
		$data["datesend"] = $intro->input->post('datesend');
		$data["msg"] = addslashes($_POST['msg']);
		$data["ip"] = $intro->input->post('ip');
				 
		$intro->db->insert(PREFIX."_contactus",$data);
		
		revalidateNext('contactus');
		$intro->redirect($this->appname);
	}

	############################################################################

	function doEdit(){
		global $intro,$array;
			
		
		$data["cname"] = $intro->input->post('cname');
		$data["email"] = $intro->input->post('email');
		$data["subject"] = $intro->input->post('subject');
		$data["datesend"] = $intro->input->post('datesend');
		$data["msg"] = addslashes($_POST['msg']);
		$data["ip"] = $intro->input->post('ip');
		
		
		$cid = intval( $intro->input->post('cid') );

		$intro->db->update(PREFIX."_contactus",$data,"cid=$cid");

		revalidateNext('contactus');
		$intro->redirect($this->appname);
	}
	
	function Del(){
		global $intro,$sess_admin,$array;
		
		$cid = intval( $intro->input->get_post('cid') );
		
		policy($sess_admin['adminid'],$this->appname.".php" , "del");

		$sql = $intro->db->query("DELETE FROM ".PREFIX."_contactus WHERE cid=$cid ");

		revalidateNext('contactus');
		$intro->redirect($this->appname);
	}
	
	############################################################################

	function Active(){
		global $intro,$cid;

		$sql = $intro->db->query("UPDATE ".PREFIX."_contactus SET status='1' WHERE cid='$cid' ");

		revalidateNext('contactus');
		$intro->redirect($this->appname);

	}

}//end class Contactus
?>