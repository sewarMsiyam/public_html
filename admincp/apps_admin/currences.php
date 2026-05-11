<?PHP
##############################################
#	Intro Technologies LTD.
#	Code By Mohammed AbuAbed - Intro.ps 
#	Email: info@intro.ps 
#	Support: http://intro.ps/support.php
#   Date: 2015-11-09 Time: 11:37:21
#	AppName: downloads
##############################################

class Currences_AppAdmin extends Intro_AppsAdmin{

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
					<i class=\"px-2 fa-solid fa-download\"></i> ".$intro->lang["currences_appname"]."   </a>
				  </li>
				  <li class=\"nav-item mx-2\">
					<a class=\"btn btn-"._css_active("Form")." px-3 p_add\" href=\"{$this->base}/Form?t=add\">
					<i class=\"px-2 fa-solid fa-plus\"></i> ".$intro->lang["currences_add"]."  </a>
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

		$result = $intro->db->query("SELECT * from ".PREFIX."_currences $qry order by $order  limit $nexlimit,$rows_per_page");
		$totrows = $intro->db->returned_rows;
		
		$sql_all_rows = $intro->db->query("SELECT id from ".PREFIX."_currences $qry ");	
		$totalrows = $intro->db->returned_rows;
		$i=0;
		while($myrow = $intro->db->fetch_assoc($result)){
			@extract($myrow);
		$status=$status ==1?"Active":"Not Active";
			$data.= "
						<tr>
						  <th scope=\"row\">$id</th>
						  <td>$title_ar</td>
						  <td>$title_en</td>
						  <td>$sygnal</td>
						  <td>$status</td>
							<td>$cur_per_dollar</td>
							<td class=\"center\"> 
								<a class=\"btn btn-info p_edit btn-sm\" href=\"{$this->base}/Form?t=edit&id=$id\" title=\"".$intro->lang["edit"]."\"><i class=\" fa-solid fa-pen-to-square\"></i></a>
								<a class=\"btn btn-danger p_del intro_ui_del btn-sm \" href=\"{$this->base}/Del?id=$id\" OnClick=\"return false;\" title=\"".$intro->lang["del"]."\"><i class=\" fa-solid fa-trash\"></i></a>
							</td>
						</tr>";
		}
		

	
           
		echo "
		<div class=\"card border-info\">
			  <div class=\"card-header text-dark bg-info\">
				<i class=\"px-2 fa-solid fa-download\"></i> ".$intro->lang["currences_cur"]." ($totalrows)
			  </div>
			  <div class=\"card-body\">
			   <div class=\"table-responsive\">
				<table class=\"table table-striped table-sm table-hover\">
					  <thead  class=\"table-dark\">
						<tr>
							<th scope=\"col\">ID "._sort_th("id","index")."</th>
							<th scope=\"col\" >".$intro->lang["currences_title_ar"]." "._sort_th("title_ar","index")."  </th>
							<th scope=\"col\">".$intro->lang["currences_title_en"]." "._sort_th("title_en","index")." </th>
							<th scope=\"col\">".$intro->lang["currences_sygnal"]." "._sort_th("sygnal","index")." </th>
							<th scope=\"col\">".$intro->lang["currences_status"]." "._sort_th("status","index")." </th>
							<th scope=\"col\">".$intro->lang["currences_cur_per_dollar"]." "._sort_th("cur_per_dollar","index")." </th>

							<th scope=\"col\">".$intro->lang["options"]."</th>
						</tr>
					  </thead>
					  <tbody>
					  $data
					  </tbody>
					 </table></div>
			  </div>
		</div>";
		
		$order = str_replace(" ", ":" , $order);
		
		
		echo "<center>".pagination3("{$this->base}/index?search_txt=$search_txt&order=$order", $totalrows, $rows_per_page, $page)."</center>";
		echo"</fieldset>";
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
			
			$sql = $intro->db->query("SELECT * FROM ".PREFIX."_currences where id='$id'");
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
			  
				<table class=\"table table-sm \">
					 <tbody>
					  
				<tr>
					<td>".$intro->lang["currences_title_ar"]." :  <span style='color:#ff0000'>*</span></td>
					<td><input  type=\"text\" name=\"title_ar\" value=\"$title_ar\" class='form-control'> {$this->error('title_ar')}</td>
				</tr>
				<tr>
					<td>".$intro->lang["currences_title_en"]." :  <span style='color:#ff0000'>*</span></td>
					<td><input  type=\"text\" name=\"title_en\" value=\"$title_en\" class='form-control'> {$this->error('title_en')}</td>
				</tr>
				<tr>
					<td>".$intro->lang["currences_sygnal"]." :  <span style='color:#ff0000'>*</span></td>
					<td><input  type=\"text\" name=\"sygnal\" value=\"$sygnal\" class='form-control'> {$this->error('sygnal')}</td>
				</tr>
				<tr>
					<td>".$intro->lang["currences_cur_per_dollar"]." :  <span style='color:#ff0000'>*</span></td>
					<td><input  type=\"text\" name=\"cur_per_dollar\" value=\"$cur_per_dollar\" class='form-control'> {$this->error('cur_per_dollar')}</td>
				</tr>		
			<tr>
				<td>".$intro->lang["status"]." : </td>
				<td>".form_option("status",$status)." {$this->error('status')}</td>
			</tr>
			<tr>
				<td ></td>
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
		$title_ar = trim( $intro->input->post('title_ar') );
		$title_en = trim( $intro->input->post('title_en') );
		$cur_per_dollar = trim( $intro->input->post('cur_per_dollar') );
		$sygnal = trim( $intro->input->post('sygnal') );
		$status = intval( $intro->input->post('status') );
	
	
		if($title_ar == "" ){

			if($title_ar == ""){ $error['title_ar'] = "<span class=error>".$intro->lang["required"]."</span>"; }
			$this->Form("add");
			die();
		}		
		
		$data["title_ar"] = $intro->input->post('title_ar');
		$data["title_en"] = $intro->input->post('title_en');
		$data["cur_per_dollar"] = $intro->input->post('cur_per_dollar');
		$data["sygnal"] = $intro->input->post('sygnal');
		$data["status"] = $intro->input->post('status');
		
		$intro->db->insert(PREFIX."_currences",$data);
		
		$intro->redirect($this->appname);
	}

	############################################################################

	function doEdit(){
		global $intro,$array;
			
		$data["title_ar"] = $intro->input->post('title_ar');
		$data["title_en"] = $intro->input->post('title_en');
		$data["cur_per_dollar"] = $intro->input->post('cur_per_dollar');
		$data["sygnal"] = $intro->input->post('sygnal');
		$data["status"] = $intro->input->post('status');
		
		$id = intval( $intro->input->post('id') );

		$intro->db->update(PREFIX."_currences",$data,"id=$id");

		$intro->redirect($this->appname);
	}
	
	function Del(){
		global $intro,$sess_admin,$array;
		
		$id = intval( $intro->input->get_post('id') );
		
		policy($sess_admin['adminid'],$this->appname.".php" , "del");

		$sql = $intro->db->query("DELETE FROM ".PREFIX."_currences WHERE id=$id ");

		$intro->redirect($this->appname);
	}
	
	############################################################################

	function Active(){
		global $intro,$id;

		$sql = $intro->db->query("UPDATE ".PREFIX."_currences SET status='1' WHERE id='$id' ");

		$intro->redirect($this->appname);

	}

}//end class Downloads
?>