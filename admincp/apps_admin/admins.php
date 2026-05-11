<?PHP

#script
define("_ADMIN_NAME_TITLE", $intro->lang['admins_list']);
define("_ADMIN_ADD", $intro->lang['admin_add']);
define("_ADMIN_EDIT", $intro->lang['Admin_edit']);
define("_ADMIN_CURRENT", $intro->lang['admin_cur']);
define("_ADMIN_ALLACTIVE", "");
##########
define("_ADMIN_ADMIN_NAME", $intro->lang['admin_name']);
define("_ADMIN_ADM_USERNAME", $intro->lang['username']);
define("_ADMIN_ADM_PASSWORD", $intro->lang['users_password']);
define("_ADMIN_EMAIL",$intro->lang['email']);
define("_ADMIN_FULLNAME", $intro->lang['fullname_admin']);

define("_ADMIN_COUNTRY", $intro->lang['country']);
define("_ADMIN_CITY", $intro->lang['city']);
define("_ADMIN_TEL", $intro->lang['users_tel']);
define("_ADMIN_REGDATE", $intro->lang['users_reg_date']);
define("_ADMIN_IPADDRESS", $intro->lang['IP']);
define("_ADMIN_LASTLOGIN", $intro->lang['last_visit']);
define("_ADMIN_TYPE", $intro->lang['admin_type']);

class Admins_AppAdmin extends Intro_AppsAdmin{

	var $appname = null;
	var $base = null;
	var $img_path;
	
	function __construct($appname,$base,$img_path="")
	{
		$this->appname = $appname;
		$this->base = $base;
		$this->img_path = $img_path;
	}

	function nav(){
		global $intro,$sess_admin,$policy;
		
		$adminid=$sess_admin['adminid'];
		$base=$this->appname;
		$policy = policy($adminid,$base.".php");
		
		echo $policy="<style>$policy</style>";
		echo "<ul class=\"nav justify-content-center mb-2\">
				  <li class=\"nav-item mx-2\">
					<a class=\"btn btn-"._css_active("index")."  px-3\" href=\"{$this->base}/index\">
					<i class=\"px-2 fa-solid fa-users-gear\"></i> ".$intro->lang["admins_list"]." </a>
				  </li>
				  <li class=\"nav-item mx-2\">
					<a class=\"btn btn-"._css_active("Form")." px-3 p_add\" href=\"{$this->base}/Form?t=add\">
					<i class=\"px-2 fa-solid fa-plus\"></i> ".$intro->lang["admin_add"]."</a>
				  </li>
			</ul>";	
			
	}


	function index(){
		  global $intro,$active,$page,$policy,$CONF,$order,$search_txt,$admin_typ;

			$this->nav();
		$qry = "";
		  if($search_txt !=""){
			 $qry = " where title  LIKE '%$search_txt%' ";
		  }

		  $text = _ADMIN_CURRENT;

		  
		  if (!isset($order) or $order=="") $order="adminid_asc";
		  $order = str_replace("_desc", " desc" , $order);
		  $order = str_replace("_asc", " asc" , $order);

		  $rows_per_page = "30";
		  if (!isset($page) or $page=="") $page=1;
		  $nexlimit = $page * $rows_per_page - $rows_per_page;

		  $result = $intro->db->query("SELECT * from ".PREFIX."_admin $qry order by $order  limit $nexlimit,$rows_per_page") or die(mysql_error());
		  $resultnumm = $intro->db->query("SELECT adminid from ".PREFIX."_admin $qry");
		  $totrows = mysqli_num_rows($result);
		  $totalrows = mysqli_num_rows($resultnumm);
		  while($myrow = $intro->db->fetch_assoc($result)){
				extract($myrow);
				$data.="<tr >
						<td align=\"center\">$adminid</td>
						 <td>$admin_name </th>
						 <td>$adm_username</th>						
						 <td>$email</th>
						 <td>$ipaddress</th>
						<td>$admin_typ[$type]</th>
						<td class=\"center\"> 
						<a class=\"btn btn-info p_edit btn-sm\" href=\"$this->base/Form?t=edit&the_adminid=$adminid\" title=\"".$intro->lang["edit"]."\"><i class=\"fa-solid fa-pen-to-square\"></i></a>
						<a class=\"btn btn-danger p_del intro_ui_del btn-sm\" href=\"$this->base/Del?the_adminid=$adminid\" OnClick=\"return false;\" title=\"".$intro->lang["del"]."\"><i class=\"fa-solid fa-trash\"></i></a>
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
						<input type=\"text\" class=\"form-control\" name=\"search_txt\" value=\"$search_txt\" placeholder=\"Type Text\">
					  </div>
					  <div class=\"col-auto\">
					  <button type=\"submit\" class=\"btn btn-primary mb-3\"> <i class=\"fa-solid fa-search fa-sm\"></i> Search</button>
					  </div>
					</form>
				  </div>
				</div>";	   
			echo "
		<div class=\"card border-info\">
			  <div class=\"card-header text-dark bg-info\">
				<i class=\"px-2 fa-solid fa-users-gear\"></i> ".$intro->lang["admin_cur"]." ($totalrows)
			  </div>
			  <div class=\"card-body\">
			  <div class=\"table-responsive\">
				<table class=\"table table-striped table-sm table-hover\">
					  <thead  class=\"table-dark\">
						<tr>
							<th scope=\"col\"><b>ID</b> 
							<th scope=\"col\"><b>"._ADMIN_ADMIN_NAME."</b>  
							<th scope=\"col\"><b>"._ADMIN_ADM_USERNAME."</b>  
							<th scope=\"col\"><b>"._ADMIN_EMAIL."</b>  
							<th scope=\"col\"><b>ip</b> 
							<th scope=\"col\"><b>"._ADMIN_TYPE."</b>  
							<th scope=\"col\">".$intro->lang["options"]."</th>
						</tr>
					  </thead>
					  <tbody>
					  $data
					  </tbody>
					 </table></div>
			  ";   
		$order = str_replace(" desc", "_desc" , $order);
		  $order = str_replace(" asc", "_asc" , $order);
		  echo "<div class='pagination' >".pagination3("$this->base/index?search_txt=$search_txt&order=$order", $totalrows, $rows_per_page, $page)."</div>";
		  echo"</div></div>";

		  
	}


	function Form(){
			global $intro,$t,$error,$admin_typ,$policy,$adminid;
			global $the_adminid,$admin_name,$adm_username,$adm_password,$email,
			$fullname,$site,$country,$city,$tel,$regdate,$ipaddress,$lastlogin,$type;

			$adminid = intval($adminid);
			$t = stripslashes($t);

			$this->nav();
			if($t == "edit"){
			   $sql = $intro->db->query("SELECT * FROM ".PREFIX."_admin where adminid='$the_adminid'") or die(mysql_error());
			   $row = $intro->db->fetch_assoc($sql);
			   @extract($row);
			   
			   $info_text = "تعديل <b>$adminid</b>";
			   $info_icon = "fa-solid fa-edit";
			   $action = "doEdit";
			   $btn_submit = "Save ";
			   $class="p_edit";
			}
			elseif($t == "add"){
				 $info_text = "Add";
				 $info_icon = "fa-solid fa-plus";
				 $action = "doAdd";
				 $btn_submit = "Add New";
				  $class="p_add";
			}
			

	echo "
	<div class=\"card border-info\">
			  <div class=\"card-header text-dark bg-info\">
				<i class=\"{$info_icon}\"></i> {$info_text}  
			  </div>
			  <div class=\"card-body\">
			  <form method=\"POST\" name=\"form_add\"  action=\"{$this->base}/$action\" enctype=\"multipart/form-data\">
			   <div class=\"table-responsive\">
			  <table class=\"table table-sm \">
					 <tbody>
				<tr>
				<td>"._ADMIN_ADMIN_NAME." : <font color=red>*</font></td>
				<td><input type=\"text\" name=\"admin_name\" value=\"$admin_name\" class='form-control'> ".@$error['admin_name']."</td>
			</tr>

			<tr>
				<td>"._ADMIN_ADM_USERNAME." : <font color=red>*</font></td>
				<td><input type=\"text\" name=\"adm_username\" value=\"$adm_username\" class='form-control'>".@$error['adm_username']."</td>
			</tr>

			<tr>
				<td>"._ADMIN_ADM_PASSWORD." : <font color=red>*</font></td>
				<td><input type=\"password\" name=\"adm_password\" value=\"\" class='form-control' > {$intro->lang['users_type_pass_to_changeit']} ".@$error[adm_password]."</td>
			</tr>

			<tr>
				<td>"._ADMIN_EMAIL." : </td>
				<td><input type=\"text\" name=\"email\" value=\"$email\" class='form-control'> ".@$error[email]."</td>
			</tr>
			<tr>
				<td>"._ADMIN_TYPE." : </td>
				<td>".sel_array("type",$admin_typ,$type,$OnCHange='')." $error[type]</td>
			</tr>";

			$sql3 = $intro->db->query("SELECT * FROM ".PREFIX."_admin where adminid='$the_adminid'") or die(mysql_error());
			$row3 = $intro->db->fetch_assoc($sql3);
			if( $row3['type']==3){

				echo"<script>
				$(document).ready(function () {
				$(\"#policies\").show();
				});	
				</script>";

			}else{
				echo"<script>
				$(document).ready(function () {
				$(\"#policies\").hide();
				});	
				</script>";
			}

			echo "
			<tr id='policies'>
				<td>{$intro->lang['control']} : </td>
				<td>
					<table>
						<tr>
							<th>الملف</th>	
							<th>اضافة</th>	
							<th>{$intro->lang['edit']}</th>	
							<th>{$intro->lang['del']}</th>		
							<th>قراءة</th>
						</tr>";

						$sqlfiles = $intro->db->query("select * from ".PREFIX."_apps  order by filename ASC ");
						$i=0;
						while($row = $intro->db->fetch_assoc($sqlfiles))
						{
							$i++;
							if($i %2 == 0) $BG = "odd"; else $BG = "even";

							$file=$row['filename'];
							$title=$row['title'];
							$title = empty($intro->lang['app_'.$title]) ? (!empty($intro->lang[$title.'_appname'])?$intro->lang[$title.'_appname']:$title) : $intro->lang['app_'.$title];
							$the_adminid=intval($the_adminid);
							$fid=intval($row['fid']);

							$sqlp = $intro->db->query("select * from ".PREFIX."_apps_policy where fid='$fid' and adminid='$the_adminid' ");
							$rowp = @$intro->db->fetch_assoc($sqlp);

							echo "
							<tr class=\"$BG\">
								<td class=center>$title  <input type=hidden name='fid[]' value='$fid'/></td>
								<td class=center><input type='checkbox' name='p_add[$fid]' value='1' ".(($rowp['p_add'] == 1) ? "checked=checked" : "")."></td>
								<td class=center><input type='checkbox' name='p_edit[$fid]' value='1' ".(($rowp['p_edit'] == 1) ? "checked=checked" : "")."></td>
								<td class=center><input type='checkbox' name='p_del[$fid]' value='1' ".(($rowp['p_del'] == 1) ? "checked=checked" : "")."></td>
								<td class=center><input type='checkbox' name='p_view[$fid]' value='1' ".(($rowp['p_view'] == 1) ? "checked=checked" : "")."></td>
							</tr>";
						}
						echo "
					</table>
				</td>
			</tr> 
			
			<tr>
			<td class=\"center\" ></td>
			<td class=\"center\" >
				<input type=\"hidden\" name=\"t\"  value=\"$t\">
				<input type=\"hidden\" name=\"the_adminid\"  value=\"$the_adminid\">
				<input class='btn btn-primary' type=\"submit\" value=\" $btn_submit \" >				
			</td>
		</tr>
		</table>
		</div>
		</form>
		</div>
		</div>
		<br/><br/><br/>";

		 

	}

	############################################################################

	function doAdd(){
		global $intro,$t,$error,$a_files,$fid,$p_add,$date_now,$sess_admin;
		global $admin_name,$adm_username,$adm_password,$email,$fullname,$site,$country,$city,$tel,$type ;

		$adm=$sess_admin['adminid'];
		$app=$this->appname;
		policy($adm,$app.".php",'add');

		$admin_name = trim($intro->input->post('admin_name'));
		$adm_username = trim($intro->input->post('adm_username'));
		$adm_password = trim($intro->input->post('adm_password'));
		$email = trim($intro->input->post('email'));

		$regdate =date("Y-m-d H:i:s");

		$type = intval($intro->input->post('type'));




		if($admin_name == "" || $adm_username == "" || $adm_password == "" || $type==0 )
		{
			if($admin_name == ""){ $error['admin_name'] = "<font class=error>".$intro->lang['required']."</font>"; }
			if($adm_username == ""){ $error['adm_username'] = "<font class=error>".$intro->lang['required']."</font>"; }
			if($adm_password == ""){ $error['adm_password'] = "<font class=error>".$intro->lang['required']."</font>"; }
			if($type == 0){ $error['type'] = "<font class=error>".$intro->lang['required']."</font>"; }
			$t = "add";
			$this->Form();
			die();
		}


		$data["admin_name"] = $admin_name;
		$data["adm_username"] = $adm_username;
		$data["adm_password"] = $intro->pwd($adm_password);
		$data["email"] = $email;
		$data["regdate"] = $regdate;
		$data["type"] = $type;

		$intro->db->insert(PREFIX."_admin",$data , true);

		$admin_id = $intro->db->insert_id();

		if(isset($_POST['fid']))
		{
			$fid = $_POST['fid'];
			for($i=0;$i<=count($fid);$i++){
				$the_fid = @$fid[$i];
				if(isset($_POST['p_add'][$the_fid]) == 1){$p_add=1;}else{$p_add=0;}
				if(isset($_POST['p_edit'][$the_fid]) == 1){$p_edit=1;}else{$p_edit=0;}
				if(isset($_POST['p_del'][$the_fid]) == 1){$p_del=1;}else{$p_del=0;}
				if(isset($_POST['p_view'][$the_fid]) == 1){$p_view=1;}else{$p_view=0;}

				if($p_add ==1 || $p_edit == 1 || $p_view == 1 || $p_del == 1){
					$data2["p_add"] = intval($p_add);
					$data2["p_edit"] = intval($p_edit);
					$data2["p_view"] = intval($p_view);
					$data2["p_del"] = intval($p_del);
					$data2["fid"] = intval($the_fid);
					$data2["adminid"] =intval($admin_id);
					$intro->db->insert(PREFIX."_apps_policy",$data2, true);
				}
			}
		}
		//echo "<h1>DONE";
		$intro->redirect($this->appname);
	}

	############################################################################

	function doEdit(){
			global $intro,$the_adminid,$fid,$sess_admin;
			 
			$admin_name = trim($intro->input->post('admin_name'));
			$adm_username = trim($intro->input->post('adm_username'));
			$adm_password = trim($intro->input->post('adm_password'));
			$email = trim($intro->input->post('email'));
			$type = trim($intro->input->post('type'));
			
			$adm=$sess_admin['adminid'];
			$app=$this->appname;
			policy($adm,$app.".php",'edit');
			$adminid=$the_adminid;
			$data["admin_name"] = $admin_name;
			$data["adm_username"] = $adm_username;
			
			$data["email"] = $email;
			$data["fullname"] = $admin_name;
			$data["site"] = "";
			$data["country"] = "";
			$data["city"] = "";
			$data["tel"] = "";
			$data["type"] = $type;
			
			if($adm_password !=""){
			$data["adm_password"] = $intro->pwd($adm_password);
			}
			
			$intro->db->update(PREFIX."_admin",$data,"adminid='$adminid'");
			
			
			
			$intro->db->query("DELETE FROM ".PREFIX."_apps_policy WHERE adminid='$adminid' ");
			
			for($i=0;$i<=count($fid);$i++){
				$the_fid = @$fid[$i];
				//echo "$the_fid | ";
				if(isset($_POST['p_add'][$the_fid]) == 1){$p_add=1;}else{$p_add=0;}
				if(isset($_POST['p_edit'][$the_fid]) == 1){$p_edit=1;}else{$p_edit=0;}
				if(isset($_POST['p_del'][$the_fid]) == 1){$p_del=1;}else{$p_del=0;}
				if(isset($_POST['p_view'][$the_fid]) == 1){$p_view=1;}else{$p_view=0;}

				if($p_add ==1 || $p_edit == 1 || $p_view == 1 || $p_del == 1){
					$data2["p_add"] = intval($p_add);
					$data2["p_edit"] = intval($p_edit);
					$data2["p_view"] = intval($p_view);
					$data2["p_del"] = intval($p_del);
					$data2["fid"] = intval($the_fid);
					$data2["adminid"] =intval( $adminid);

					$intro->db->insert(PREFIX."_apps_policy",$data2);

				}

			}

			$intro->redirect($this->appname);
	}

	############################################################################

	function Del(){
			 global $intro,$the_adminid,$sess_admin;
			$adm=$sess_admin['adminid'];
			$app=$this->appname;
			if($adm ==1){
			 $sql = $intro->db->query("DELETE FROM ".PREFIX."_admin WHERE adminid='$the_adminid' and adminid !=1 ");
					$intro->db->query("DELETE FROM ".PREFIX."_apps_policy WHERE adminid='$the_adminid' ");
			}
		//	 $intro->redirect($this->appname);

	}

	############################################################################

	function Active(){
		global $intro,$adminid;

		$sql = $intro->db->query("UPDATE ".PREFIX."_admin SET active='1' WHERE adminid='$adminid' ");

		header("LOCATION: $this->base?maa=Main&active=0");

	}


}


?>