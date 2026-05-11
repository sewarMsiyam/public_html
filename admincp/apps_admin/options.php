<?PHP
##############################################
#	Intro Technologies LTD.
#	Code By Mohammed AbuAbed - Intro.ps 
#	Email: info@intro.ps 
#	Support: http://intro.ps/support.php
#   Date: 2013-02-03 Time: 13:31:38
#	AppName: lang
##############################################

class Options_AppAdmin extends Intro_AppsAdmin{

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
			 global $intro;

			
			echo "<ul class=\"nav justify-content-center mb-2\">
				  <li class=\"nav-item mx-2\">
					<a class=\"btn btn-"._css_active("index")."  px-3\" href=\"{$this->base}/index\">
					<i class=\"px-2 fa-solid fa-gear\"></i> ".$intro->lang["options"]." </a>
				  </li>
				  <li class=\"nav-item mx-2\">
					<a class=\"btn btn-"._css_active("apps")." px-3 p_add\" href=\"{$this->base}/apps\">
					<i class=\"px-2 fa-solid fa-prescription-bottle\"></i> ".$intro->lang["apps"]."</a>
				  </li>
				  <li class=\"nav-item mx-2\">
					<a class=\"btn btn-"._css_active("backup")." px-3 p_add\" href=\"{$this->base}/backup\">
					<i class=\"px-2 fa-solid fa-boxes-stacked\"></i> ".$intro->lang["backup"]."</a>
				  </li>
			</ul>";	 
	}

	
	function index(){
		global $intro;


		$this->nav();
	
	$sql = $intro->db->query("SELECT * FROM ".PREFIX."_options");

		while($row = $intro->db->fetch_assoc($sql) ) 
		{
			$option[$row['option_name']] = stripslashes($row['option_value']);
		}
		echo "
			<div class=\"card border-info\">
		  <div class=\"card-header text-dark bg-info\">
			General site options
		  </div>
		  <div class=\"card-body\">
		  <form method=\"POST\" action=\"{$this->base}/SaveOptions\" enctype=\"multipart/form-data\">
		  <table class=\"table table-sm \">
				 <tbody>
		<tr><td colspan='2'><div class='alert alert-dark'> General site options </div> </td></tr>
		<tr>
			<td>Site name:</td>
			<td><input type=\"text\" name=\"site_name\" class='form-control' value=\"{$option['site_name']}\"></td>
		</tr>
		<tr>
			<td>Email:</td>
			<td><input type=\"text\" name=\"site_email\" value=\"{$option['site_email']}\" class='form-control'>
				<br/>For orders, contact us form.</td>
		</tr>
	<tr>
			<td>Site mobile:</td>
			<td><input type=\"text\" name=\"site_mobile\" value=\"{$option['site_mobile']}\" class='form-control'>
				</td>
		</tr>
		<tr>
			<td>Site mobile For WhatsApp:</td>
			<td><input type=\"text\" name=\"site_mobile2\" value=\"{$option['site_mobile2']}\" class='form-control'>
				</td>
		</tr>
		
		<tr>
			<td>Site URL:</td>
			<td><input type=\"text\" name=\"site_url\" value=\"{$option['site_url']}\" class='form-control'></td>
		</tr>

		<tr>
			<td>SEO Key Words:</td>
			<td><textarea name=\"site_keywords\"  class='form-control'>{$option['site_keywords']}</textarea></td>
		</tr>
		<tr>
			<td>SEO Desciption: <br/></td>
			<td><textarea name=\"site_desc\"  class='form-control'>{$option['site_desc']}</textarea></td>
		</tr>
		<tr>
		  <td>Admin Folder</td>
		  <td><input dir=ltr type=\"text\" name=\"admin_folder\" value=\"{$option['admin_folder']}\"  class='form-control'></td>
		</tr>
		<tr>
			<td>لون خلفية الشريط المتحرك :</td>
			<td><input type=\"text\" class='ColorPicker'  dir=ltr name=\"mqrquee_color\" value=\"{$option['mqrquee_color']}\"  class='form-control'></td>
		</tr>
			<tr>
			<td>Show Visa Image : </td>
			<td>".form_option('showvisa',$option['showvisa'])." </td>
		</tr>
		</tr>
			<tr>
			<td>Show Visa Button : </td>
			<td>".form_option('showvisabtn',$option['showvisabtn'])." </td>
		</tr>
		

		
		<tr><td colspan='2'><div class='alert alert-dark'> Captcha </div> </td></tr>
		<tr>
			<td>Use Captcha?:</td>
			<td>".form_option('captcha',$option['captcha'])." </td>
		</tr>
		<tr>
			<td>Seo link : ?</td>
			<td>".form_option('seo_uri',$option['seo_uri'])." </td>
		</tr>
		
		<tr><td colspan='2'><div class='alert alert-dark'> Product Auto file Titles for SEO. </div> </td></tr>
		<tr>
			<td>Seo titles :</td>
			<td>
				<table style='width:100%'>
					<tr>
						<th>Arabic عناوين عربية</th>
						<th>English Titles</th>
					</tr>
					<tr>
						<td><textarea name=\"prod_titles_ar\" dir='rtl' class='form-control' >{$option['prod_titles_ar']}</textarea></td>
						<td><textarea name=\"prod_titles_en\" dir='ltr' class='form-control'>{$option['prod_titles_en']}</textarea></td>
					</tr>
				</table>
			</td>
		</tr>
		
		<tr><td colspan='2'><div class='alert alert-dark'> Social Network Links </div> </td></tr>
		<tr>
			<td>Facebook:</td>
			<td>http://<input dir=ltr type=\"text\" name=\"facebook\" value=\"{$option['facebook']}\" class='form-control'></td>
		</tr>
		<tr>
			<td>Twitter:</td>
			<td>http://<input dir=ltr type=\"text\" name=\"twitter\" value=\"{$option['twitter']}\" class='form-control'></td>
		</tr>
		<tr>
			<td>Youtube:</td>
			<td>http://<input dir=ltr type=\"text\" name=\"youtube\" value=\"{$option['youtube']}\" class='form-control'></td>
		</tr>
		<tr>
			<td>Google+:</td>
			<td>http://<input dir=ltr type=\"text\" name=\"google\" value=\"{$option['google']}\" class='form-control'></td>
		</tr>
		
		<tr><td colspan='2'><div class='alert alert-dark'>Email settings</div> </td></tr>
		
		<tr>
			<td>Mail Host:</td>
			<td><input type=\"text\" dir=ltr name=\"mail_host\" value=\"{$option['mail_host']}\" class='form-control'></td>
		</tr>
		<tr>
			<td>Mail Port:</td>
			<td><input type=\"text\" dir=ltr name=\"mail_port\" value=\"{$option['mail_port']}\" class='form-control'></td>
		</tr>
		<tr>
			<td>Mail Username:</td>
			<td><input type=\"text\" dir=ltr name=\"mail_user\" value=\"{$option['mail_user']}\" class='form-control'></td>
		</tr>
		<tr>
			<td>Mail Password:</td>
			<td><input type=\"text\" dir=ltr name=\"mail_pass\" value=\"{$option['mail_pass']}\" class='form-control'></td>
		</tr>
		<tr>
			<td>Mail Sender Name:</td>
			<td><input type=\"text\" dir=ltr name=\"mail_sender_name\" value=\"{$option['mail_sender_name']}\" class='form-control'></td>
		</tr>
		<tr>
			<td>Mail Sender Email:</td>
			<td><input type=\"text\" dir=ltr name=\"mail_sender_email\" value=\"{$option['mail_sender_email']}\" class='form-control'></td>
		</tr>
		
		
		<tr><td colspan='2'><div class='alert alert-dark'>Close Site</div> </td></tr>
		<tr>
			<td>Close Site:</td>
			<td>
				".form_option_array('close_site', array(1=>$intro->lang['yes'],0=>$intro->lang['no']), $option['close_site'])."
				<textarea name=\"close_msg\" cols=\"60\" rows=\"5\">{$option['close_msg']}</textarea></td>
		</tr>
		
		<tr>
			<td>Time Zone:</td>
			<td><span dir=ltr>".form_select_array('time_zone', $intro->ar->time_zone(), $option['time_zone'])."<br/> Server Time: ".date('Y-m-d H:i:s')."</span></td>
		</tr>
		<tr>
			<td>Debug:</td>
			<td>".form_option_array('debug', array(1=>$intro->lang['yes'],0=>$intro->lang['no']), $option['debug'])."</td>
		</tr>
		<tr>
			<td colspan=\"2\" class=\"center\">
				<input type=\"hidden\" name=\"maa\"  value=\"save\">
				<input class=\"btn btn-primary p_edit\" type=\"submit\" value=\"  Save changes \" name=\"B1\">
			</td>
		</tr>
		</table>
		
		</form>
		</div>
		</div>";
		
	
	}


	function delbodybg(){
		global  $intro;
		
		$bodybg=$intro->input->get_post("bodybg");
		@$intro->db->query("update ".PREFIX."_options set option_value='' where option_name='bodybg'");
		@unlink("../$bodybg");
		
		$intro->redirect($this->appname, 'index'); 
		
	}	
	function SaveOptions(){
		global  $intro;
	
		$upload_dir = "../uploads/";
		$imgfilec= @$_FILES['bodybg']['tmp_name'];
		$new_file = @$_FILES['bodybg'];
		$file_name = $new_file['name'];
		$file_tmp = $new_file['tmp_name'];
		$file_size = $new_file['size'];
		if (@is_uploaded_file($imgfilec))
		{
			$file_name= Date("Y-m-d")."_".time().".".GetExt($file_name);
			@move_uploaded_file($file_tmp,$upload_dir.$file_name);
			
			$upload_dir=str_replace("../","",$upload_dir);
			$bodybg=$upload_dir.$file_name;
			//$this->OptionsCheckNew('bodybg');
			$intro->db->query("update ".PREFIX."_options set option_value='$bodybg' where option_name='bodybg'");

		}
		if (@is_uploaded_file($_FILES['logowater']['tmp_name']))
		{
			$file=$upload_dir.'logowater.png';
			@move_uploaded_file($_FILES['logowater']['tmp_name'],$file);		
			$this->OptionsCheckNew('logowater');
			@$intro->db->query("update ".PREFIX."_options set option_value='$file' where option_name='logowater'");
		}

		foreach($_POST as $key=>$val)
		{
		  if($key !='maa' && $key !='B1')
		  {			
			$val = $intro->input->post($key);
			$val = addslashes($val);
			
			$this->OptionsCheckNew($key);
			//$data = array();
			//
			@$intro->db->query_fast("update ".PREFIX."_options set option_value='$val' where option_name='$key'");
		  }	  
		}

		$intro->redirect($this->appname, 'index');  
	}
	function OptionsCheckNew($option_name){
		global  $intro;
		
		$sql = $intro->db->query("select * from ".PREFIX."_options where option_name='$option_name'");
		if(mysqli_num_rows($sql) == 0){
			$intro->db->insert(PREFIX."_options",array('option_name'=>$option_name));
		}
	}
	/*************************************************************
	*
	* Apps		
	*
	**************************************************************/
	function apps_check_new(){
		global $intro;
		
		$root = scandir("./apps_admin");
		foreach($root as $file)
		{ 
			if (preg_match("/^([_0-9a-zA-Z]+)([.]{1})([_0-9a-zA-Z]{3})$/",$file) && $file != 'home.php' && $file != 'index.php'&& $file != 'login.php') 
			{
				$files[] = trim($file);

				$sql = $intro->db->query("SELECT filename from ".PREFIX."_apps where filename='$file'");
				if(mysqli_num_rows($sql) == 0){
					$title = str_replace('.php','',$file);
					//echo "$file <br>";
					$sub_links = "$title|index";
					$sql =  $intro->db->query("INSERT INTO ".PREFIX."_apps "
					." (title,filename,fimage,actit,isadmin,menu,sub_links) VALUES "
					." ('$title','$file','doc-text',0,0,0,'$sub_links')");
				}
			}
		}
	}	
	function apps(){
		global $intro;
		?>
		<style>
		td.dragHandle {
			
		}
		td.showDragHandle {
			background-image: url(<?=$this->img_path?>/updown2.gif);
			background-repeat: no-repeat;
			background-position: center center;
			cursor: move;
		}
		</style>
		<script>
		$(document).ready(function() {
			$('#table-1').tableDnD({
				onDrop: function(table, row) {
					//alert("Result of $.tableDnD.serialise() is "+$.tableDnD.serialize());
					$('#AjaxResult').load("<?=$this->base?>/AppDoSort?NH=1&"+$.tableDnD.serialize());
				},dragHandle: "dragHandle"
			}); 
			
			$("#table-1 tr").hover(function() {
				  $(this.cells[0]).addClass('showDragHandle');
			}, function() {
				  $(this.cells[0]).removeClass('showDragHandle');
			});
		});	
		</script>
		<?   
		
		$this->apps_check_new();
		$this->nav();
		echo "
		<div class=\"card border-info\">
			  <div class=\"card-header text-dark bg-info\">
				<i class=\"px-2 fa-solid fa-prescription-bottle\"></i> Apps
			  </div>
			  <div class=\"card-body\">
			  <div id=\"AjaxResult\"></div>
			   <div class=\"table-responsive\">
				<table id=\"table-1\" class=\"DataTable table table-striped table-sm table-hover\">
					  <thead  class=\"table-dark\">
						<tr>
				<th> ID</th>
				
				<th> {$intro->lang['file_title']}</th>
				<th> {$intro->lang['file']}</th>
				<th> image</th>
				<th> {$intro->lang['status']}</th>
				<th> {$intro->lang['admin_privte']}</th>
				<th> {$intro->lang['options']}</th>
			</tr>
			</thead>
			<tbody>";
		$i=0;
		$result = $intro->db->query("SELECT * from ".PREFIX."_apps order by w");
		while($myrow = $intro->db->fetch_assoc($result)){
		   extract($myrow);
		   $i++;
			
			if($actit == 1){
			   $actit = "<span class=\"label label-success\">{$intro->lang['active']}</span>";
			}elseif($actit == 0){
			   $actit = "<span class=\"label label-inactive\">{$intro->lang['inactive']}</span>";
			}
			
			if($isadmin == 1){
			   $isadmin = "<span class=\"label label-success\">{$intro->lang['yes']}</span>";
			}elseif($isadmin == 0){
			   $isadmin = "<span class=\"label label-warning\">{$intro->lang['no']}</span>";
			}
			
			if($i %2 == 0) $BG = "odd"; else $BG = "even";
			
			echo "
			<tr id=\"$fid\" >
				<td class=\"dragHandle\"> $w</td>				
				<td > $title</td>
				<td align='center'> $filename</td>	
				<td><i class='$fimage'></i></td>		
				<td align='center'>$actit</td>
				<td align='center'>$isadmin</td>
				<td> <a class=\"btn btn-info icon-edit btn-sm \" href=$this->base/AppForm?t=edit&amp;fid=$fid>{$intro->lang['edit']}</a>
				<a class=\"btn btn-danger intro_ui_del icon-cancel btn-sm\" href=\"$this->base/AppDel?fid=$fid\" onclick=\"return false;\">{$intro->lang['del']}</a></td>
			</tr>";
		}

		echo "
		</tbody>
		</table></div>
		<center><a class='btn btn-info' href=$this->base/AppFix>{$intro->lang['rearrange_files']}</a>
		
		</center>
		</div></div>";
	}

	function AppDoSort(){
			global $intro;
			
		$menu = $_GET['table-1'];
		
		for ($i = 0; $i < count($menu); $i++) 
		{
			$sql = $intro->db->query("UPDATE ".PREFIX."_apps SET w='$i' WHERE fid='$menu[$i]' ");
			
		}
		echo " <div class='alert alert-success'> {$intro->lang['save_rearrange']} @ " . date("i:s") ."</div>";
		
		//var_dump($_GET['table-1']);
	}

	function AppOrder(){
		global  $intro,$fid,$w,$w_replace,$fid_replace,$fid_original;
		
		@extract($_GET);
		@extract($_POST);
		
		$fid_replace = intval($fid_replace);
		$fid_original = intval($fid_original);

		$result = $intro->db->query("update ".PREFIX."_apps set w='$w' where fid='$fid_replace'");
		$result2 = $intro->db->query("update ".PREFIX."_apps set w='$w_replace' where fid='$fid_original'");



		$intro->redirect($this->appname, 'apps');

	}

	function AppFix() {
		global $intro;

		$result = $intro->db->query("select fid from ".PREFIX."_apps order by w ASC");
		$w1 = 0;
		while ($row = $intro->db->fetch_assoc($result)) {
		$w1++;
		$intro->db->query("update ".PREFIX."_apps set w='$w1' where fid='$row[fid]'");
		}
		$intro->redirect($this->appname, 'apps');
	}
		
	function AppForm($t="add") {
		global $intro,$fid, $error;

		$this->nav();
		
		if($_GET) @extract($_GET);
		if($error || $_POST) @extract($_POST);
		
		if($t == "edit"){
			$result = $intro->db->query("SELECT * from ".PREFIX."_apps WHERE fid='$fid'");
			$myrow = $intro->db->fetch_assoc($result);

			$title = $myrow['title'];
			$filename = $myrow['filename'];
			$actit = $myrow['actit'];
			$fimage = $myrow['fimage'];
			$isadmin = $myrow['isadmin'];
			$menu = $myrow['menu'];
			$sub_links = stripslashes($myrow['sub_links']);

			$btn['legend_name'] = $intro->lang["app_edit"]."<b>$fid</b>";
			$btn['legend_img'] = "fa-solid fa-edit";
			$btn['name'] = $intro->lang["save_changes"];
			$btn['icon'] = "fa-solid fa-edit";		   
			$btn['action'] = "AppDoEdit";		
		}
		elseif($t == "add"){

			$btn['legend_name'] = $intro->lang["app_add"];
			$btn['legend_img'] = "fa-solid fa-plus";
			$btn['name'] = $intro->lang["add_new"];
			$btn['icon'] = "fa-solid fa-plus";		   
			$btn['action'] = "AppDoAdd";	
			$fimage="fa-solid fa-list";				
		}

		echo "
		<div class=\"card border-info\">
			  <div class=\"card-header text-dark bg-info\">
				<i class=\"{$btn['icon']}\"></i> {$btn['legend_name']}  
			  </div>
			  <div class=\"card-body\">
			  <form method=\"POST\" enctype=\"multipart/form-data\" action=\"{$this->base}/{$btn['action']}\">
			  <table class=\"table table-sm \">
			  <tbody>
		<tr>
		  <td>{$intro->lang['file_title']}</td>
		  <td><input type=\"text\" name=\"title\" value=\"$title\" class='form-control'></td>
		</tr>
		<tr>
		  <td>{$intro->lang['file']}</td>
		  <td>"; $this->select_file($filename,"edit"); echo"</td>
		</tr>
		<tr>
		  <td>{$intro->lang['photo']}</td>
		  <td><input type=\"text\" name=\"fimage\" value=\"$fimage\" class='form-control'> </td>
		</tr>
		<tr>
		  <td>{$intro->lang['admin_privte']}</td>
		  <td>"; $this->for_admin($isadmin); echo"</td>
		</tr>
		<tr>
		  <td>{$intro->lang['status']}</td>
		  <td>"; $this->actit($actit); echo"</td>
		</tr>
		<tr>
		  <td>{$intro->lang['sub_links']}</td>
		  <td><textarea dir=ltr name=\"sub_links\"  class='form-control' rows=\"10\">$sub_links</textarea></td>
		</tr>
		<tr>
		  <td> </td>
		  <td> 
					<input type=\"hidden\" name=\"fid\" value=\"$fid\">
					<input type=\"hidden\" name=\"maa\" value=\"do_edit_apps\">
					
					<button class=\"btn btn-primary\" type=\"submit\" name=\"app_action\" value=\"{$btn['action']}\" title=\"{$btn['name']}\">
					<i class='{$btn['icon']}'></i> {$btn['name']} 
					</button>	
		  </td>
		</tr>
		</table>
		</form>
		</div>
		</div>";
	}
	function AppDoAdd(){
		global $intro,$title,$filename,$fimage,$actit,$isadmin,$menu,$sub_links;
		
		@extract($_POST);
		
		if (!$title){
		echo "{$intro->lang['empty']}";
		exit();
		}

		$sql =  $intro->db->query("INSERT INTO ".PREFIX."_apps (title,filename,fimage,actit,isadmin,menu,sub_links) VALUES ('$title','$filename','$fimage','$actit','$isadmin','$menu','$sub_links')");

		$intro->redirect($this->appname, 'apps');
	}	
	function AppDoEdit(){
		global $intro,$title,$fid,$filename,$fimage,$actit,$isadmin,$menu,$sub_links;
		
		@extract($_POST);

		
		$data['sub_links']=$sub_links;
		$data['fimage']=$fimage;
		$data['title']=$title;
		$data['filename']=$filename;
		$data['actit']=$actit;
		$data['isadmin']=$isadmin;
		$data['menu']=$menu;
		
		$fid = intval($_POST['fid']);
		
		$intro->db->update(PREFIX."_apps",$data,"fid=$fid");
		
		$intro->redirect($this->appname, 'apps');
	}
	function AppDel(){
		global $intro,$fid;
		
		@extract($_POST);
		@extract($_GET);
		
		$sql =  $intro->db->query("delete from  ".PREFIX."_apps  where fid='$fid'");

		$intro->redirect($this->appname, 'apps');
	}
	
	function for_admin($isadmin) {
		global $intro;
		
		if (($isadmin == 0) OR ($isadmin == "")) {
		$sel1 = "";
		$sel2 = "checked";
		}
		if ($isadmin == 1){
		$sel1 = "checked";
		$sel2 = "";
		}
		echo "<input type=\"radio\" name=\"isadmin\" value=\"1\" $sel1>{$intro->lang['yes']}
		  <input type=\"radio\" name=\"isadmin\" value=\"0\" $sel2>{$intro->lang['no']}  ";

	}
	function inmenu($menu) {
		global $intro;
		
		if (($menu == 0) OR ($menu == "")) {
		$sel1 = "";
		$sel2 = "checked";
		}
		if ($menu == 1){
		$sel1 = "checked";
		$sel2 = "";
		}
		echo "<input type=\"radio\" name=\"menu\" value=\"1\" $sel1>{$intro->lang['yes']}
		  <input type=\"radio\" name=\"menu\" value=\"0\" $sel2>{$intro->lang['no']}  ";

	}
	function actit($actit) {
		global $intro;
		
		if (($actit == 0) OR ($actit == "")) {
		$sel1 = "";
		$sel2 = "checked";
		}
		if ($actit == 1){
		$sel1 = "checked";
		$sel2 = "";
		}
		echo "<input type=\"radio\" name=\"actit\" value=\"1\" $sel1> {$intro->lang['on']}
		  <input type=\"radio\" name=\"actit\" value=\"0\" $sel2> {$intro->lang['off']}  ";

	}
	function select_file($filename,$type){
		global $intro;
			 
		echo "<select dir=ltr name=\"filename\">\n";
		//$handle=opendir("./apps_admin");
		$root = scandir("./apps_admin");
		foreach($root as $file)
		{ 
			  if (preg_match("/^([_0-9a-zA-Z]+)([.]{1})([_0-9a-zA-Z]{3})$/",$file)) {
				 $files[] = $file;
			  }
		}
		sort($files);
		for ($i=0; $i < sizeof($files); $i++) 
		{
			if($files[$i]!="") 
			{
				if ($filename == $files[$i]) {
				$sel = "selected";
				} else {
				$sel = "";
				}
				if($type=="add")
				{
					$result2 = $intro->db->query("select * from ".PREFIX."_apps where filename='$files[$i]'");
					$numrows = $intro->db->sql_numrows($result2);
					if ($numrows == 0) {
					
								echo "<option value=\"".$files[$i]."\" $sel>".$files[$i]."</option>\n";
					}
				}else{
				 
				   echo "<option value=\"".$files[$i]."\" $sel >".$files[$i]."</option>\n";
				}
			}
		}
		echo "</select>";
	}

	function select_img($fimage)
	{
		global $db,$prefix;

		$data = array('icon-spin1 ','icon-youtube-play ','icon-spin3 ','icon-spin4 ','icon-spin5 ','icon-spin6 ','icon-glass ','icon-music ','icon-search ','icon-mail ','icon-mail-alt ','icon-heart ','icon-heart-empty ','icon-star ','icon-star-empty ','icon-star-half ','icon-star-half-alt ','icon-user ','icon-users ','icon-male ','icon-female ','icon-video ','icon-videocam ','icon-picture ','icon-camera ','icon-camera-alt ','icon-th-large ','icon-th ','icon-th-list ','icon-ok ','icon-ok-circled ','icon-ok-circled2 ','icon-ok-squared ','icon-cancel ','icon-cancel-circled ','icon-cancel-circled2 ','icon-plus ','icon-plus-circled ','icon-plus-squared ','icon-plus-squared-small ','icon-minus ','icon-minus-circled ','icon-minus-squared ','icon-minus-squared-alt ','icon-minus-squared-small ','icon-help ','icon-help-circled ','icon-info-circled ','icon-info ','icon-home ','icon-link ','icon-unlink ','icon-link-ext ','icon-link-ext-alt ','icon-attach ','icon-lock ','icon-lock-open ','icon-lock-open-alt ','icon-pin ','icon-eye ','icon-eye-off ','icon-tag ','icon-tags ','icon-bookmark ','icon-bookmark-empty ','icon-flag ','icon-flag-empty ','icon-flag-checkered ','icon-thumbs-up ','icon-thumbs-down ','icon-thumbs-up-alt ','icon-thumbs-down-alt ','icon-download ','icon-upload ','icon-download-cloud ','icon-upload-cloud ','icon-reply ','icon-reply-all ','icon-forward ','icon-quote-left ','icon-quote-right ','icon-code ','icon-export ','icon-export-alt ','icon-pencil ','icon-pencil-squared ','icon-edit ','icon-print ','icon-retweet ','icon-keyboard ','icon-gamepad ','icon-comment ','icon-chat ','icon-comment-empty ','icon-chat-empty ','icon-bell ','icon-bell-alt ','icon-attention-alt ','icon-attention ','icon-attention-circled ','icon-location ','icon-direction ','icon-compass ','icon-trash ','icon-doc ','icon-docs ','icon-doc-text ','icon-doc-inv ','icon-doc-text-inv ','icon-folder ','icon-folder-open ','icon-folder-empty ','icon-folder-open-empty ','icon-box ','icon-rss ','icon-rss-squared ','icon-phone ','icon-phone-squared ','icon-menu ','icon-cog ','icon-cog-alt ','icon-wrench ','icon-basket ','icon-calendar ','icon-calendar-empty ','icon-login ','icon-logout ','icon-mic ','icon-mute ','icon-volume-off ','icon-volume-down ','icon-volume-up ','icon-headphones ','icon-clock ','icon-lightbulb ','icon-block ','icon-resize-full ','icon-resize-full-alt ','icon-resize-small ','icon-resize-vertical ','icon-resize-horizontal ','icon-move ','icon-zoom-in ','icon-zoom-out ','icon-down-circled2 ','icon-up-circled2 ','icon-down-dir ','icon-up-dir ','icon-left-dir ','icon-right-dir ','icon-down-open ','icon-left-open ','icon-right-open ','icon-up-open ','icon-angle-left ','icon-angle-right ','icon-angle-up ','icon-angle-down ','icon-angle-circled-left ','icon-angle-circled-right ','icon-angle-circled-up ','icon-angle-circled-down ','icon-angle-double-left ','icon-angle-double-right ','icon-angle-double-up ','icon-angle-double-down ','icon-down ','icon-left ','icon-right ','icon-up ','icon-down-big ','icon-left-big ','icon-right-big ','icon-up-big ','icon-right-hand ','icon-left-hand ','icon-up-hand ','icon-down-hand ','icon-left-circled ','icon-right-circled ','icon-up-circled ','icon-down-circled ','icon-spin2 ','icon-ccw ','icon-arrows-cw ','icon-level-up ','icon-level-down ','icon-shuffle ','icon-exchange ','icon-collapse ','icon-collapse-top ','icon-expand ','icon-play ','icon-play-circled ','icon-play-circled2 ','icon-stop ','icon-pause ','icon-to-end ','icon-to-end-alt ','icon-to-start ','icon-to-start-alt ','icon-fast-fw ','icon-fast-bw ','icon-eject ','icon-target ','icon-signal ','icon-award ','icon-desktop ','icon-laptop ','icon-tablet ','icon-mobile ','icon-inbox ','icon-globe ','icon-sun ','icon-cloud ','icon-flash ','icon-moon ','icon-umbrella ','icon-flight ','icon-fighter-jet ','icon-leaf ','icon-font ','icon-bold ','icon-italic ','icon-text-height ','icon-text-width ','icon-align-left ','icon-align-center ','icon-align-right ','icon-align-justify ','icon-list ','icon-indent-left ','icon-indent-right ','icon-list-bullet ','icon-list-numbered ','icon-strike ','icon-underline ','icon-superscript ','icon-subscript ','icon-table ','icon-columns ','icon-crop ','icon-scissors ','icon-paste ','icon-briefcase ','icon-suitcase ','icon-ellipsis ','icon-ellipsis-vert ','icon-off ','icon-road ','icon-list-alt ','icon-qrcode ','icon-barcode ','icon-book ','icon-ajust ','icon-tint ','icon-check ','icon-check-empty ','icon-circle ','icon-circle-empty ','icon-asterisk ','icon-gift ','icon-fire ','icon-magnet ','icon-chart-bar ','icon-ticket ','icon-credit-card ','icon-floppy ','icon-megaphone ','icon-hdd ','icon-key ','icon-fork ','icon-rocket ','icon-bug ','icon-certificate ','icon-tasks ','icon-filter ','icon-beaker ','icon-magic ','icon-truck ','icon-money ','icon-euro ','icon-pound ','icon-dollar ','icon-rupee ','icon-yen ','icon-renminbi ','icon-won ','icon-bitcoin ','icon-sort ','icon-sort-down ','icon-sort-up ','icon-sort-alt-up ','icon-sort-alt-down ','icon-sort-name-up ','icon-sort-name-down ','icon-sort-number-up ','icon-sort-number-down ','icon-hammer ','icon-gauge ','icon-sitemap ','icon-spinner ','icon-coffee ','icon-food ','icon-beer ','icon-user-md ','icon-stethoscope ','icon-ambulance ','icon-medkit ','icon-h-sigh ','icon-hospital ','icon-building ','icon-smile ','icon-frown ','icon-meh ','icon-anchor ','icon-terminal ','icon-eraser ','icon-puzzle ','icon-shield ','icon-extinguisher ','icon-bullseye ','icon-adn ','icon-android ','icon-apple ','icon-bitbucket ','icon-bitbucket-squared ','icon-css3 ','icon-dribbble ','icon-dropbox ','icon-facebook ','icon-facebook-squared ','icon-flickr ','icon-foursquare ','icon-github ','icon-github-squared ','icon-github-circled ','icon-gittip ','icon-gplus-squared ','icon-gplus ','icon-html5 ','icon-instagramm ','icon-linkedin-squared ','icon-linux ','icon-linkedin ','icon-maxcdn ','icon-pinterest-circled ','icon-pinterest-squared ','icon-renren ','icon-skype ','icon-stackoverflow ','icon-trello ','icon-tumblr ','icon-tumblr-squared ','icon-twitter-squared ','icon-twitter ','icon-vkontakte ','icon-weibo ','icon-windows ','icon-xing ','icon-xing-squared ','icon-youtube ','icon-youtube-squared ','icon-cw',);
		asort($data);
		echo "<select dir=ltr name=\"fimage\" size=10>\n";

		foreach($data as $icon){
			if ($fimage == $icon) {
			$sel = "selected";
			} else {
			$sel = "";
			}
			echo "<option value=\"".$icon."\" class=\"$icon\" $sel style='font-size:25px;'> &nbsp;&nbsp;&nbsp; ".str_replace("icon-","",$icon)."</option>\n";
		
		}
		echo "</select>";
	}
	/*************************************************************
	*
	* BACKUP		
	*
	**************************************************************/
	function backup(){
		global $intro; 
		$this->nav();
		
		?><script>
		function submitform(f)
		{
		var url = "";     
		win3 = window.open(url, 'CSC', "width=700,height=400,status=no,resizable=yes,scrollbars=yes,menubar=no,toolbar=no,location=0");
		document.forms[f].submit();
		 
		}
		</script><?


		 echo "	
		 <div class=\"card border-info\">
			  <div class=\"card-header text-dark bg-info\">
				{$intro->lang['backups']}
			  </div>
			  <div class=\"card-body\">
			<table class=\"table table-striped table-bordered\">
			<thead>
			<tr>
				 <th>{$intro->lang['file']} </th>
				 <th><b>{$intro->lang['time']}</th>
				 <th>{$intro->lang['size']} </th>
				 <th>{$intro->lang['options']} </th>
			 </tr>
			 </thead>
			 <tbody>";
		$CONF['del_time'] = 360*360*360;
		$all_files=$this->get_backup_files($CONF['del_time']);
		if (is_array($all_files)) 
		{
			natsort($all_files);
			$i = $size_sum = 0;
			foreach($all_files as $filename) 
			{
				$i++;
				$file= $this->backup_folder(1).$filename;
			
				$time = "file time";
				$last_backup = "last_backup time";
				if($time>$last_backup) $last_backup=$time;
				$size_sum += $size = $this->file_info("size",$file);
				$size = $this->size_type($size);
				$date_time = @date ("Y-m-d H:i:s", @filemtime($file));
				echo "
				<tr class=\""._odd_even($i)."\">
					<td dir=ltr>\n".$filename."</td>
					<td dir=ltr><center> $date_time </td>
					<td>".$size['value']." ".$size['type']."</td>
					<td> 
						<a class=\"btn btn-success icon-down\" href=\"{$this->base}/Down?NH=1&amp;file=$filename\">Download</a>
						<a class=\"btn btn-danger icon-cancel intro_ui_del\" href=\"{$this->base}/delBackup?file=$filename\" onclick=\"return false;\">حذف</a>
					</td>
				</tr>";
			}
			$size_sum = $this->size_type($size_sum);
			echo "
			</tbody>
			<tfoot>
				<tr>
					  <td colspan=\"2\">   </td>
					  <td><div class=\"bold\">{$intro->lang['total_size']}: ".$size_sum['value']." ".$size_sum['type']." </div></td>
					  <td>  </td>
				  </tr>
			</tfoot>\n";
		} else {
			echo "<tfoot>
					<tr>
					  <td colspan=4><div class=\"bold\"> {$intro->lang['no_backups']} </div>\n</td>
				 </tr>
				 </tfoot>\n";
		}

		echo"</table>\n";

		echo "</div></div>
		<div class=\"card border-info mt-2\">
			  <div class=\"card-header text-dark bg-info\">
				{$intro->lang['create_new_backup']} 
			  </div>
			  <div class=\"card-body\">
			  
			<form method=\"GET\" name=\"frmBackup\" action=\"{$this->base}/doBackup?NH=1\" target='CSC' >
			<table class='table table-sm'>
				 <tbody>
				<tr>
					<td align=\"center\" bgcolor=\"#E4E4E4\">

					<br>
					<input type=\"checkbox\" CHECKED name=\"tables\"> <icon class=icon-table></icon> {$intro->lang['export_tables']} |
					<input type=\"checkbox\" CHECKED name=\"data\"> <icon class=icon-export></icon> {$intro->lang['export_data']} |
					<input type=\"checkbox\" CHECKED name=\"drop\"> <icon class=icon-cancel-circled2></icon> {$intro->lang['add_or_delete_table']} |
					<input type=\"checkbox\"  name=\"zip\"> <icon class=icon-box></icon>{$intro->lang['get_zip']}  <br>
					<br>
					<input type=\"hidden\" name=\"maa\" value=\"doBackup\">
					<input type=\"hidden\" name=\"NH\" value=\"1\">
					<input type=\"submit\" value=\"{$intro->lang['create_backup']}\" 
					onclick=\"submitform('frmBackup');\"> <p>

					</td>
				</tr>
			</table>
		</form></div>
		</div>";
		
		$string = "xxxxxxx('http://intro.ps/ssss.swf')";
		preg_match_all('!https?://[\S]+!', $string, $matches);
		$all_urls = $matches[0];
		
		var_dump($all_urls);

	}
	function backup_folder($path=0){
	
		if($path == 1){
			$cur_file = basename(__file__);
			$path = $_SERVER['SCRIPT_FILENAME'];
			$path = str_replace( array($cur_file,"index.php"),"",$path);
			return $path."sql/";
		}
		else
		return "../../sql/";
	}
	function flush_buffers($data=''){
		@ob_end_flush();
		@ob_flush();
		@flush();
		@ob_start();
		if($data != '') echo str_replace(PREFIX."_","",$data);
	} 
	function doBackup() {
		global $intro;
		
		
		header( 'Content-type: text/html; charset=utf-8' );
		
			

		
		$tables = $intro->input->get_post('tables');
		$data =$intro->input->get_post('data');
		$drop = $intro->input->get_post('drop');
		$zip = $intro->input->get_post('zip');
		
		$db = $intro->config['db']['database'];
		
		// set max string size before writing to file
		$max_size=1048576; // 1MB

		// set backupfile name
		if (function_exists('gzopen') && $zip) $backupfile=$db.".".date("Y-m-d_H-i-s").".sql.gz"; else $backupfile=$db.".".time().".sql";

		//create comment
		$out="# mySQL DB dump of database '".$db."'\n";
		$out.="# build by Intro.ps\n";
		$out.="# http://intro.ps\n\n\n";
	
		// get create table and insert data sql queries for each table
		$result = $intro->db->query_fast("SHOW TABLES FROM $db");
		$i=0;
		while ($row = $intro->db->fetch_assoc($result)) 
		{
			foreach($row AS $key=>$tablename)
			$tablename = $tablename;
			$i++;
			
			// export tables
			echo "$db - $tablename <br/>";
			if ( $tablename != "" ) {
				$res1=$intro->db->query('SHOW CREATE TABLE ' . $db. '.' . $tablename);
				$table_sql = $intro->db->fetch_assoc($res1);
				
				$out.="\n\n\n";				
				if ($drop) $out.="DROP TABLE IF EXISTS `".$tablename."`;\n";
				$out.= $table_sql['Create Table'];
				$out.=";\n\n";
			}
			//$this->flush_buffers("<li> - $i ok <li>");
			
			// export data
			if ($data && $tablename != "") {
				$res2 = $intro->db->query_fast("select * from `".$tablename."`");
				for ($j=0;$j<mysqli_num_rows($res2);$j++){
					$out .= "insert into `".$tablename."` values (";
					$row=mysqli_fetch_row($res2);
					for ($k=0;$k<$nf=mysqli_num_fields($res2);$k++) {
						$out .="'".$intro->db->escape($row[$k])."'";
						if ($k<($nf-1)) $out .=", ";						
					}
					$out .=");\n";
					if (strlen($out)>$max_size) $out=$this->save_to_file($backupfile,$zip,$out);			
				}
			}
			
			if (strlen($out)>$max_size) $out=$this->save_to_file($backupfile,$zip,$out);
		}
		

		$this->save_to_file($backupfile,$zip,$out);
		
		?>
		<script>alert('Backup Done!'); 
		window.opener.location.reload(false);
		window.close(); </script>	
		<?
		
		//$intro->redirect($this->appname, 'backup');
	}
	// saves the string in $fileData to the file $backupfile as gz file or not ($zip)
	function save_to_file($backupfile,$zip,$fileData) {
		if ($zip) {
			$zp=gzopen($this->backup_folder(1).$backupfile,"a9");
			gzwrite($zp,$fileData);
			gzclose($zp);
		} else {
			$fp=fopen($this->backup_folder(1).$backupfile,"a");
			fwrite($fp,$fileData);
			fclose($fp);
		}
		//update_options('backup_date',add_date(date("Y-m-d"), "day", 7, "Y-m-d"));
		return "";
	}
	
	function file_info($mode,$path) 
	{
		$filename=preg_replace("/.*/","",$path);
		$parts=explode(".",$filename);
		
		switch($mode) {
			case "db":
				return $parts[0];
			break;
			case "time":
				return $parts[1];
			break;
			case "gzip":
				return $parts[3];
			break;
			case "size":
				return filesize($path);
			break;
		
		}
	}
	function ungzip($mode,$path) 
	{
		$file_data=gzfile ($path);
		if ($mode!="lines") return implode("",$file_data); else return $file_data;
	}
	function size_type($size) 
	{
		$types=array("B","KB","MB","GB");
		for ($i=0; $size>1000; $i++,$size/=1024);
		$result['value']=round($size,2);
		$result['type']=$types[$i];
		return $result;
	}
	function get_backup_files($del_time) 
	{
		$all_files = array();
		$handle=opendir($this->backup_folder(1));
		$remove_time=time()-($del_time*86400);
		while ($file=readdir($handle)) {
			if ($file!="." && $file!=".." && preg_match("'\.sql$|\.sql.gz'",$file)) {
					$all_files[]=$file;
			}
		}
		return $all_files;
	}
	function delBackup()
	{		 
		$file = $_POST['file'] ==''?$_GET['file']:$_POST['file'] ;		
        @unlink($this->backup_folder(1).$file);
	}
	function Down()
	{		 
		global $intro;
		
		$file = $intro->input->get_post('file');	
        
		
		$gzfile = $this->backup_folder(1).$file.".gz";
		$fp = gzopen ($gzfile, 'w9');		
		gzwrite ($fp, file_get_contents($this->backup_folder(1).$file));
		gzclose($fp);		
		$dlfile = $this->backup_folder().$file.".gz";

		
		if (!headers_sent())
		{
			header('Location: '.$dlfile);
		}else {
			echo '<script type="text/javascript">window.location.href="'.$dlfile.'";</script>';
			echo '<noscript><meta http-equiv="refresh" content="0;url='.$dlfile.'" /></noscript>';
		}
		
		
	}
	
}
?>