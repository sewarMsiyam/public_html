<?PHP
##############################################
#	Intro Technologies LTD.
#	Code By Mohammed AbuAbed - Intro.ps 
#	Email: info@intro.ps 
#	Support: http://intro.ps/support.php
#   Date: 2015-11-09 Time: 11:37:21
#	AppName: downloads
##############################################

class Downloads_AppAdmin extends Intro_AppsAdmin{

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
					<i class=\"px-2 fa-solid fa-download\"></i> ".$intro->lang["downloads_appname"]." </a>
				  </li>
				  <li class=\"nav-item mx-2\">
					<a class=\"btn btn-"._css_active("Form")." px-3 p_add\" href=\"{$this->base}/Form?t=add\">
					<i class=\"px-2 fa-solid fa-plus\"></i> ".$intro->lang["downloads_add"]."</a>
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
			$qry = " where title_ar  LIKE '%$search_txt%' ";
		}

      
		
		if ($order=="") $order="id:desc";
		$order = str_replace(":", " " , $order);

		$rows_per_page = "30";
		if ($page==0) $page=1;
		$nexlimit = $page * $rows_per_page - $rows_per_page;

		$result = $intro->db->query("SELECT * from ".PREFIX."_downloads $qry order by $order  limit $nexlimit,$rows_per_page");
		$totrows = $intro->db->returned_rows;
		
		$sql_all_rows = $intro->db->query("SELECT id from ".PREFIX."_downloads $qry ");	
		$totalrows = $intro->db->returned_rows;
		$i=0;
		while($myrow = $intro->db->fetch_assoc($result)){
			@extract($myrow);
		$the_file=$the_file !=''?"<a href='{$intro->base_url}$the_file'>The File</a>":"No files";
		$status=$status ==1?"Active":"Not Active";
			$data.= "
						<tr>
						  <th scope=\"row\">$id</th>
						  <td>$title</td>
						  <td>$lang</td>
						  <td>$the_file</td>
							<td>$status</td>
							<td class=\"center\"> 
								<a class=\"btn btn-info p_edit btn-sm\" href=\"{$this->base}/Form?t=edit&id=$id\" title=\"".$intro->lang["edit"]."\"><i class=\" fa-solid fa-pen-to-square\"></i></a>
								<a class=\"btn btn-danger p_del intro_ui_del btn-sm \" href=\"{$this->base}/Del?id=$id\" OnClick=\"return false;\" title=\"".$intro->lang["del"]."\"><i class=\" fa-solid fa-trash\"></i></a>
							</td>
						</tr>";
		}
		

		echo "<div class=\"card my-3 \">
				  <div class=\"card-header  mb-3\">
					<i class=\"px-2 fa-solid fa-search\"></i> Search Form
				  </div>
				  <div class=\"card-body\">
					<form class=\"row g-3\"  action=\"\" method=\"post\">
					 
					  <div class=\"col-auto\">
						<input type=\"text\" class=\"form-control\" name=\"search_txt\" value=\"$search_txt\" placeholder=\"Type Text\">
					  </div>
					  <div class=\"col-auto\">
					  <input type=\"hidden\" name=\"maa\" value=\"Main\">
						<button type=\"submit\" class=\"btn btn-primary mb-3\">Search</button>
					  </div>
					</form>
				  </div>
				</div>";
           
		echo "
		<div class=\"card border-info\">
			  <div class=\"card-header text-dark bg-info\">
				<i class=\"px-2 fa-solid fa-download\"></i> ".$intro->lang["downloads_cur"]." ($totalrows)
			  </div>
			  <div class=\"card-body\">
				<table class=\"table table-striped table-sm table-hover\">
					  <thead  class=\"table-dark\">
						<tr>
							<th scope=\"col\">ID "._sort_th("id","index")."</th>
							<th scope=\"col\" >".$intro->lang["downloads_title"]." "._sort_th("title_ar","index")." </th>
							<th scope=\"col\">".$intro->lang["downloads_lang"]." "._sort_th("status","index")." </th>
							<th scope=\"col\">".$intro->lang["downloads_the_file"]." "._sort_th("status","index")." </th>
							<th scope=\"col\">".$intro->lang["downloads_status"]." "._sort_th("status","index")." </th>
							<th scope=\"col\">".$intro->lang["options"]."</th>
						</tr>
					  </thead>
					  <tbody>
					  $data
					  </tbody>
					 </table>
			  </div>
		</div>";
		
		$order = str_replace(" ", ":" , $order);
		
		
		echo "<center>".pagination3("{$this->base}/index?search_txt=$search_txt&order=$order", $totalrows, $rows_per_page, $page)."</center>";
		echo"</fieldset>";
	}
	function doInline(){
		global $intro;

		$data = array();
		
		$val = $_POST['update_value'];
		$id = $_POST['element_id'];
		$name = $_POST['element_name'];
		$original_value = $_POST['original_value'];
		$original_html = $_POST['original_html'];
	
		$data[$name] = $val;

		$intro->db->update(PREFIX."_downloads",$data,"id='$id'");
		echo $val;
	
	}
	function multiDel(){
		global $intro,$error,$sess_admin;

		$selected_fld = $_POST['selected_fld'];
		$count = count($selected_fld);
		
		policy($sess_admin['adminid'],$this->appname.".php" , "del");
		
		for ($i=0; $i<$count; $i++) {

			$sql = $intro->db->query("DELETE FROM ".PREFIX."_downloads WHERE id='{$selected_fld[$i]}' ");
		}

		$intro->redirect($this->appname);
	}

	function Form($t=""){
		global $intro,$error,$sess_admin,$array;
		global $title_ar,$lang,$notes,$title,$the_file,$file_ar,$file_en,$date_time,$status;
		
		if($_GET != null) @extract($_GET);
		if($error || $_POST != null) @extract($_POST);
		
		
		$id = intval( $intro->input->get_post("id") );
		
		$t = $t==""?$intro->input->get_post("t"):$t;

		$this->nav();
		if($t == "edit"){
			
			policy($sess_admin['adminid'],$this->appname.".php" , "edit");
			
			$sql = $intro->db->query("SELECT * FROM ".PREFIX."_downloads where id='$id'");
			$row = $intro->db->fetch_assoc($sql);
			@extract($row);

			$btn['legend_name'] = $intro->lang["downloads_edit"]." <b>$id</b>";
			$btn['legend_icon'] = "fa-solid fa-edit";
			$btn['name'] = $intro->lang["save_changes"];
			$btn['img_icon'] = "fa-solid fa-edit";		   
			$btn['action'] = "doEdit";
			$btn['copy'] = "<button class=\"mult_submit\" type=\"submit\" name=\"app_action\" value=\"doAdd\" title=\"add new\">
						<span class=\"icon-floppy\"> حفظ كسجل جديد</span>
					</button>";
		}
		elseif($t == "add"){

			policy($sess_admin['adminid'],$this->appname.".php" , "add");
			
			$btn['legend_name'] = $intro->lang["downloads_add"];
			$btn['legend_icon'] = "fa-solid fa-plus";
			$btn['name'] = $intro->lang["add_new"];
			$btn['img_icon'] = "fa-solid fa-plus";		   
			$btn['action'] = "doAdd";
			$btn['copy'] = "";
			$date_time=date("Y-m-d H:i:s");
		}		
			
	echo "<div class=\"card border-info\">
			  <div class=\"card-header text-dark bg-info\">
				<i class=\"{$btn['legend_icon']}\"></i> {$btn['legend_name']}  
			  </div>
			  <div class=\"card-body\">
			  <form method=\"POST\" name=\"form_add\"  action=\"{$this->base}/{$btn['action']}\" enctype=\"multipart/form-data\">
			  
				<table class=\"table  table-sm \">
					 <tbody>
					  
				<tr>
					<td>".$intro->lang["downloads_title"]." :  <span style='color:#ff0000'>*</span></td>
					<td><input  type=\"text\" name=\"title\" value=\"$title\" class='form-control'> {$this->error('title')}</td>
				</tr>
		
			<tr>
				<td>".$intro->lang["downloads_the_file"]." : </td>
				<td><input type=\"file\" name=\"the_file\" size=\"30\"> 
				</td>
			</tr>
			<tr>
				<td>".$intro->lang["downloads_notes"]." : </td>
				<td><textarea name='notes' cols=30 rows=5>$notes</textarea> 
				</td>
			</tr>
			<tr>
				<td>".$intro->lang["downloads_lang"]." : </td>
				<td>".sel_array('lang',$array['langs'],$lang,'Choose Language')." 
				</td>
			</tr>
			<tr>
				<td>".$intro->lang["downloads_date_time"]." : </td>
				<td><input dir=\"ltr\" type=\"text\" name=\"date_time\" value=\"$date_time\" size=\"30\"> {$this->error('date_time')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["downloads_status"]." : </td>
				<td>".form_option("status",$status)." {$this->error('status')}</td>
			</tr>
			<tr>
				<td class=\"center\" ></td>
				<td class=\"center\" >
					<input type=\"hidden\" name=\"app_name\"  value=\"{$this->appname}\">
					<input type=\"hidden\" name=\"t\"  value=\"$t\">
					<input type=\"hidden\" name=\"id\"  value=\"$id\">
					<button class=\"btn btn-primary\" type=\"submit\" name=\"app_action\" value=\"{$btn['action']}\" title=\"{$btn['name']}\">
					<i class=\"{$btn['legend_icon']}\"></i>  {$btn['name']}
					</button>
					{$btn['copy']}
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
		$the_file = trim( $intro->input->post('the_file') );
		$title = trim( $intro->input->post('title') );
		$notes = trim( $intro->input->post('notes') );
		$lang = trim( $intro->input->post('lang') );
		$date_time = trim( $intro->input->post('date_time') );
		$status = intval( $intro->input->post('status') );
	
		if($title == "" ){

			if($title == ""){ $error['title'] = "<span class=error>".$intro->lang["required"]."</span>"; }
			$this->Form("add");
			die();
		}		
		
		$data["title"] = $intro->input->post('title');
		$data["notes"] = $intro->input->post('notes');
		$data["lang"] = $intro->input->post('lang');
		$data["date_time"] = $intro->input->post('date_time');
		$data["status"] = $intro->input->post('status');
		
		$TempFile = $_FILES ["the_file"]["tmp_name"];
		$FileName = $_FILES ["the_file"]["name"];
		$path = "../uploads/".$this->appname."/";
		if(!is_dir($path)){
			@mkdir($path);
			@file_put_contents($path."index.html", "404 not found.");
			@chmod($filename, 0664);
		}
		if( @is_uploaded_file($TempFile) )
		{		
			@move_uploaded_file ($TempFile, $path.$FileName);
			$data["the_file"] = "uploads/".$this->appname."/".$FileName;
		}
	
				 
		$intro->db->insert(PREFIX."_downloads",$data);
		
		$intro->redirect($this->appname);
	}

	############################################################################

	function doEdit(){
		global $intro,$array;
			
		
		$data["title"] = $intro->input->post('title');
		$data["notes"] = $intro->input->post('notes');
		$data["lang"] = $intro->input->post('lang');
		$data["date_time"] = $intro->input->post('date_time');
		$data["status"] = $intro->input->post('status');
		
		$TempFile = $_FILES ["the_file"]["tmp_name"];
		$FileName = $_FILES ["the_file"]["name"];
		$path = "../uploads/".$this->appname."/";
		if(!is_dir($path)){
			@mkdir($path);
			@file_put_contents($path."index.html", "404 not found.");
			@chmod($filename, 0664);
		}
		if( @is_uploaded_file($TempFile) )
		{		
			@move_uploaded_file ($TempFile, $path.$FileName);
			$data["the_file"] = "uploads/".$this->appname."/".$FileName;
		}
	
		
		$id = intval( $intro->input->post('id') );

		$intro->db->update(PREFIX."_downloads",$data,"id=$id");

		$intro->redirect($this->appname);
	}
	
	function Del(){
		global $intro,$sess_admin,$array;
		
		$id = intval( $intro->input->get_post('id') );
		
		policy($sess_admin['adminid'],$this->appname.".php" , "del");

		$sql = $intro->db->query("DELETE FROM ".PREFIX."_downloads WHERE id=$id ");

		$intro->redirect($this->appname);
	}
	
	############################################################################

	function Active(){
		global $intro,$id;

		$sql = $intro->db->query("UPDATE ".PREFIX."_downloads SET status='1' WHERE id='$id' ");

		$intro->redirect($this->appname);

	}

}//end class Downloads
?>