<?PHP
##############################################
#	Intro Technologies LTD.
#	Code By Mohammed AbuAbed - Intro.ps 
#	Email: info@intro.ps 
#	Support: http://intro.ps/support.php
#   Date: 2015-07-07 Time: 11:59:03
#	AppName: slides
##############################################

class Slides_AppAdmin extends Intro_AppsAdmin{

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
					<i class=\"px-2 fa-solid fa-image\"></i> ".$intro->lang["slides_appname"]." </a>
				  </li>
				  <li class=\"nav-item mx-2\">
					<a class=\"btn btn-"._css_active("Form")." px-3 p_add\" href=\"{$this->base}/Form?t=add\">
					<i class=\"px-2 fa-solid fa-plus\"></i> ".$intro->lang["slides_add"]."</a>
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
			$qry = " where stitle  LIKE '%$search_txt%' ";
		}

      
		
		if ($order=="") $order="sid:desc";
		$order = str_replace(":", " " , $order);

		$rows_per_page = "30";
		if ($page==0) $page=1;
		$nexlimit = $page * $rows_per_page - $rows_per_page;

		$result = $intro->db->query("SELECT * from ".PREFIX."_slides $qry order by $order  limit $nexlimit,$rows_per_page");
		$totrows = $intro->db->returned_rows;
		
		$sql_all_rows = $intro->db->query("SELECT sid from ".PREFIX."_slides $qry ");	
		$totalrows = $intro->db->returned_rows;
		while($myrow = $intro->db->fetch_assoc($result)){
			@extract($myrow);
			$data.= "
			<tr >
				<td class=\"center\">$sid</td>
				<td>$stitle_ar</td>
				<td>$stitle_en</td>
				<td><img src=\"{$intro->base_url}uploads/news/$image\" width=\"50\" height=\"50\" alt=\"\" /></td>
				<td>$url_ar<br/>$url_en</td>
				<td class=\"center\"> 
					<a class=\"btn btn-info p_edit\" href=\"{$this->base}/Form?t=edit&sid=$sid\" title=\"".$intro->lang["edit"]."\"><i class=\"icon-edit\"></i></a>
					<a class=\"btn btn-danger p_del intro_ui_del\" href=\"{$this->base}/Del?sid=$sid\" OnClick=\"return false;\" title=\"".$intro->lang["del"]."\"><i class=\"icon-cancel-circled2\"></i></a>
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
				<i class=\"px-2 fa-solid fa-image\"></i> ".$intro->lang["slides_cur"]." ($totalrows)
			  </div>
			  <div class=\"card-body\">
			   <div class=\"table-responsive\">
				<table class=\"table table-striped table-sm table-hover\">
					  <thead  class=\"table-dark\">
						<tr>
							<th>ID "._sort_th("sid","index")."</th>
							<th>".$intro->lang["slides_stitle_ar"]." "._sort_th("stitle_ar","index")." </th>
							<th>".$intro->lang["slides_stitle_en"]." "._sort_th("stitle_en","index")." </th>
							<th>".$intro->lang["slides_image"]." "._sort_th("image","index")." </th>
							<th>".$intro->lang["slides_url"]." "._sort_th("url","index")." </th>
							<th>".$intro->lang["options"]."</th>
						</tr>
					  </thead>
					  <tbody>
					  $data
					  </tbody>
					 </table></div> ";    
		$order = str_replace(" ", ":" , $order);
		echo "<center class='pagination' >".pagination3("{$this->base}/index?search_txt=$search_txt&order=$order", $totalrows, $rows_per_page, $page)."</center>";
		echo"</div></div>";
	}
	
	function Form($t=""){
		global $intro,$error,$sess_admin,$array;
		global $stitle_ar,$stitle_en,$image,$url_en,$url_ar,$image_en;
		
		if($_GET != null) @extract($_GET);
		if($error || $_POST != null) @extract($_POST);
		
		
		$sid = intval( $intro->input->get_post("sid") );
		
		$t = $t==""?$intro->input->get_post("t"):$t;

		$this->nav();
		if($t == "edit"){
			
			policy($sess_admin['adminid'],$this->appname.".php" , "edit");
			
			$sql = $intro->db->query("SELECT * FROM ".PREFIX."_slides where sid='$sid'");
			$row = $intro->db->fetch_assoc($sql);
			@extract($row);

			$btn['legend_name'] = $intro->lang["slides_edit"]." <b>$sid</b>";
			$btn['legend_icon'] = "fa-solid fa-edit";
			$btn['name'] = $intro->lang["save_changes"];
			$btn['img_icon'] = "fa-solid fa-edit";		   
			$btn['action'] = "doEdit";
		
		}
		elseif($t == "add"){

			policy($sess_admin['adminid'],$this->appname.".php" , "add");
			
			$btn['legend_name'] = $intro->lang["slides_add"];
			$btn['legend_icon'] = "fa-solid fa-plus";
			$btn['name'] = $intro->lang["add_new"];
			$btn['img_icon'] = "fa-solid fa-plus";		   
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
			  <table class=\"table table-sm \">
					 <tbody>
				<tr>
				<td>".$intro->lang["slides_stitle_ar"]." :  </td>
				<td><input dir=\"rtl\" type=\"text\" name=\"stitle_ar\" value=\"$stitle_ar\" class='form-control'> {$this->error('stitle_ar')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["slides_stitle_en"]." :  </td>
				<td><input dir=\"rtl\" type=\"text\" name=\"stitle_en\" value=\"$stitle_en\" class='form-control'> {$this->error('stitle_en')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["slides_image"]." :<span style='color:#ff0000'>*</span> </td>
				<td><input type=\"text\" dir=ltr name=\"image\" value=\"$image\" id=\"$image\" class='form-control'> 
				   <span id=\"preview_image\"></span><img src=\"{$intro->base_url}uploads/news/$image\" width=\"70\" height=\"70\" style=\"float:left;\" alt=\"\" />
				   <a class=\"btn btn-info icon-upload\" OnClick=\"javascript:popup('".admin_path."images.php?for_id=image');\">".$intro->lang["file_bring"]."</a> 
				   {$this->error('image')}
				</td>
			</tr>
			<tr>
				<td>".$intro->lang["slides_image_en"]." :<span style='color:#ff0000'>*</span> </td>
				<td><input type=\"text\" dir=ltr name=\"image_en\" value=\"$image_en\" id=\"$image_en\" class='form-control'> 
				   <span id=\"preview_image_en\"></span><img src=\"{$intro->base_url}uploads/news/$image_en\" width=\"70\" height=\"70\" style=\"float:left;\" alt=\"\" />
				   <a class=\"btn btn-info icon-upload\" OnClick=\"javascript:popup('".admin_path."images.php?for_id=image_en');\">".$intro->lang["file_bring"]."</a> 
				   {$this->error('image_en')}
				</td>
			</tr>
			<tr>
				<td>Arabic URL : </td>
				<td><input dir=\"ltr\" type=\"text\" name=\"url_ar\" value=\"$url_ar\" class='form-control'> {$this->error('url_ar')}</td>
			</tr>
			<tr>
				<td>English URL: </td>
				<td><input dir=\"ltr\" type=\"text\" name=\"url_en\" value=\"$url_en\" class='form-control'> {$this->error('url_en')}</td>
			</tr>
			<tr>
				<td class=\"center\" ></td>
				<td class=\"center\" >
					<input type=\"hidden\" name=\"app_name\"  value=\"{$this->appname}\">
					<input type=\"hidden\" name=\"t\"  value=\"$t\">
					<input type=\"hidden\" name=\"sid\"  value=\"$sid\">
					<button class=\"btn btn-primary\" type=\"submit\" title=\"{$btn['name']}\">
					<i class=\"{$btn['img_icon']}\"></i> {$btn['name']} 
					</button>
					
				</td>
			</tr>
			</tbody>
			</table>
			</form>
			</div>
		</div>";
	}

	############################################################################

	function doAdd(){
		global $intro,$error;
		$stitle_ar = trim( $intro->input->post('stitle_ar') );
		$stitle_en = trim( $intro->input->post('stitle_en') );
		$image = trim( $intro->input->post('image') );
		$url = trim( $intro->input->post('url') );
		
		if($image == ""){

			if($image == ""){ $error['image'] = "<span class=error>".$intro->lang["required"]."</span>"; }
		
			$this->Form("add");
			die();
		}		
		
		$data["stitle_ar"] = $intro->input->post('stitle_ar');
		$data["stitle_en"] = $intro->input->post('stitle_en');
		$data["image"] = $intro->input->post('image');
		$data["image_en"] = $intro->input->post('image_en');
		$data["url_ar"] = $intro->input->post('url_ar');
		$data["url_en"] = $intro->input->post('url_en');
				 
		$intro->db->insert(PREFIX."_slides",$data);
		
		$intro->redirect($this->appname);
	}

	############################################################################

	function doEdit(){
		global $intro,$array;
			
		
		$data["stitle_ar"] = $intro->input->post('stitle_ar');
		$data["stitle_en"] = $intro->input->post('stitle_en');
		$data["image"] = $intro->input->post('image');
		$data["image_en"] = $intro->input->post('image_en');
		$data["url_ar"] = $intro->input->post('url_ar');
		$data["url_en"] = $intro->input->post('url_en');
		
		
		$sid = intval( $intro->input->post('sid') );

		$intro->db->update(PREFIX."_slides",$data,"sid=$sid");

		$intro->redirect($this->appname);
	}
	
	function Del(){
		global $intro,$sess_admin,$array;
		
		$sid = intval( $intro->input->get_post('sid') );
		
		policy($sess_admin['adminid'],$this->appname.".php" , "del");

		$sql = $intro->db->query("DELETE FROM ".PREFIX."_slides WHERE sid=$sid ");

		$intro->redirect($this->appname);
	}
	
	############################################################################

	function Active(){
		global $intro,$sid;

		$sql = $intro->db->query("UPDATE ".PREFIX."_slides SET status='1' WHERE sid='$sid' ");

		$intro->redirect($this->appname);

	}

}//end class Slides
?>