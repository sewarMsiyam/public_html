<?PHP
##############################################
#	Intro Technologies LTD.
#	Code By Mohammed AbuAbed - Intro.ps 
#	Email: info@intro.ps 
#	Support: http://intro.ps/support.php
#   Date: 2014-06-17 Time: 10:56:48
#	AppName: pages
##############################################

class Pages_AppAdmin extends Intro_AppsAdmin{

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
					<i class=\"px-2 fa-solid fa-file\"></i> ".$intro->lang["pages_appname"]." </a>
				  </li>
				  <li class=\"nav-item mx-2\">
					<a class=\"btn btn-"._css_active("Form")." px-3 p_add\" href=\"{$this->base}/Form?t=add\">
					<i class=\"px-2 fa-solid fa-plus\"></i> ".$intro->lang["pages_add"]."</a>
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
			$qry = " where title  LIKE '%$search_txt%' ";
		}

      
		
		if ($order=="") $order="id:desc";
		$order = str_replace(":", " " , $order);

		$rows_per_page = "30";
		if ($page==0) $page=1;
		$nexlimit = $page * $rows_per_page - $rows_per_page;

		$result = $intro->db->query("SELECT * from ".PREFIX."_pages p"
		." LEFT JOIN ".PREFIX."_pages_cat cat ON p.catid=cat.catid $qry order by $order  limit $nexlimit,$rows_per_page");
		$totrows = $intro->db->returned_rows;
		
		$sql_all_rows = $intro->db->query("SELECT id from ".PREFIX."_pages $qry ");	
		$totalrows = $intro->db->returned_rows;
		while($myrow = $intro->db->fetch_assoc($result)){
			@extract($myrow);
			$data.="
			<tr >
				<td class=\"center\">$id</td>
				<td>$title_ar</td>
				<td>$title_en</td>
				<td>$hits</td>
				<td class=\"center\"> 
					<a class=\"btn btn-info p_edit btn-sm\" href=\"{$this->base}/Form?t=edit&id=$id\" title=\"".$intro->lang["edit"]."\"><i class=\"fa-solid fa-pen-to-square\"></i></a>
					<a class=\"btn btn-danger p_del intro_ui_del btn-sm\" href=\"{$this->base}/Del?id=$id\" OnClick=\"return false;\" title=\"".$intro->lang["del"]."\"><i class=\"fa-solid fa-trash\"></i></a>
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
					  <input type=\"hidden\" name=\"maa\" value=\"Main\">
						<button type=\"submit\" class=\"btn btn-primary mb-3\"> <i class=\"fa-solid fa-search fa-sm\"></i> Search</button>
					  </div>
					</form>
				  </div>
				</div>";
	
         echo "
		<div class=\"card border-info\">
			  <div class=\"card-header text-dark bg-info\">
				<i class=\"px-2 fa-solid fa-file\"></i> ".$intro->lang["pages_cur"]." ($totalrows)
			  </div>
			  <div class=\"card-body\">
				 <div class=\"table-responsive\">
				<table class=\"table table-striped table-sm table-hover\">
					  <thead  class=\"table-dark\">
						<tr>
							<th  scope=\"col\">ID "._sort_th("id","index")."</th>
							<th  scope=\"col\" >".$intro->lang["pages_title_ar"]." "._sort_th("title","index")." </th>
							<th  scope=\"col\">".$intro->lang["pages_title_en"]." "._sort_th("title","index")." </th>
							<th  scope=\"col\">".$intro->lang["pages_hits"]." "._sort_th("hits","index")." </th>
							<th  scope=\"col\">".$intro->lang["options"]."</th>
						</tr>
					  </thead>
					  <tbody>
					  $data
					  </tbody>
					 </table></div>";  
	
		$order = str_replace(" ", ":" , $order);
		echo "<center class='pagination'>".pagination3("{$this->base}/index?search_txt=$search_txt&order=$order", $totalrows, $rows_per_page, $page)."</center>";
		echo"</div></div>";
	}


	function Form($t=""){
		global $intro,$error,$sess_admin,$array;
		global $catid,$title,$title_en,$hits,$title_ar,$bodytext_ar,$bodytext,$image,$bodytext_en;
		
		if($_GET != null) @extract($_GET);
		if($error || $_POST != null) @extract($_POST);
		
		
		$id = intval( $intro->input->get_post("id") );
		
		$t = $t==""?$intro->input->get_post("t"):$t;

		$this->nav();
		if($t == "edit"){
			
			policy($sess_admin['adminid'],$this->appname.".php" , "edit");
			
			$sql = $intro->db->query("SELECT * FROM ".PREFIX."_pages where id='$id'");
			$row = $intro->db->fetch_assoc($sql);
			@extract($row);

			$btn['legend_name'] = $intro->lang["pages_edit"]." <b>$id</b>";
			$btn['legend_icon'] = "fa-solid fa-pen-to-square";
			$btn['name'] = $intro->lang["save_changes"];
			$btn['img_icon'] = "fa-solid fa-plus";		   
			$btn['action'] = "doEdit";
		
		}
		elseif($t == "add"){

			policy($sess_admin['adminid'],$this->appname.".php" , "add");
			
			$btn['legend_name'] = $intro->lang["pages_add"];
			$btn['legend_icon'] = "fa-solid fa-plus";
			$btn['name'] = $intro->lang["add_new"];
			$btn['img_icon'] = "fa-solid fa-plus";		   
			$btn['action'] = "doAdd";
			
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
				<td>".$intro->lang["pages_title_ar"]." :  <span style='color:#ff0000'>*</span></td>
				<td><input class='ltr' type=\"text\" name=\"title_ar\" value=\"$title_ar\" class='form-control'> {$this->error('title_ar')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["pages_title_en"]." :  <span style='color:#ff0000'>*</span></td>
				<td><input  type=\"text\" name=\"title_en\" value=\"$title_en\" class='form-control'> {$this->error('title_en')}</td>
			</tr>
			<tr>
				<td> التفاصيل بالعربية  : </td>
				<td>"; fck_editor("bodytext_ar",stripslashes($bodytext_ar)/*,array('tools'=>'mini','dir'=>'rtl') */); echo " {$this->error('bodytext')}</td>
			</tr>
			<tr>
				<td>التفاصيل بالانجليزية: </td>
				<td>"; fck_editor("bodytext_en",stripslashes($bodytext_en)/*,array('tools'=>'mini','dir'=>'rtl') */); echo " {$this->error('bodytext')}</td>
			</tr>
			<tr>
				<td class=\"center\" ></td>
				<td class=\"center\" >
					<input type=\"hidden\" name=\"t\"  value=\"$t\">
					<input type=\"hidden\" name=\"id\"  value=\"$id\">
					<button type=\"submit\" class='btn btn-primary'  title=\"{$btn['name']}\">
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
		$catid = intval( $intro->input->post('catid') );
		$title_ar = trim( $intro->input->post('title_ar') );
		$title_en = trim( $intro->input->post('title_en') );
		$hits = intval( $intro->input->post('hits') );
		$bodytext = trim( $intro->input->post('bodytext') );
		$image = trim( $intro->input->post('image') );
		
		
		if($title_ar == "" || $title_en == "" ){

			if($title_ar == ""){ $error['title_ar'] = "<span class=error>".$intro->lang["required"]."</span>"; }
			if($title_en == ""){ $error['title_en'] = "<span class=error>".$intro->lang["required"]."</span>"; }
			$this->Form("add");
			die();
		}		
		
		$data["catid"] = $catid;
		$data["title_en"] = $intro->input->post('title_en');
		$data["title_ar"] = $intro->input->post('title_ar');
		$data["hits"] = $intro->input->post('hits');
		$data["image"] = $intro->input->post('image');
		$data["bodytext_ar"] = addslashes($_POST['bodytext_ar']);
		$data["bodytext_en"] = addslashes($_POST['bodytext_en']);		 
		$intro->db->insert(PREFIX."_pages",$data);
		revalidateNext('pages');
		$intro->redirect($this->appname);
	}

	############################################################################

	function doEdit(){
		global $intro,$array;
			
		
		$data["catid"] = intval($intro->input->post('catid'));
		$data["title_ar"] = $intro->input->post('title_ar');
		$data["title_en"] = $intro->input->post('title_en');
		$data["hits"] = $intro->input->post('hits');
		$data["image"] = $intro->input->post('image');
		$data["bodytext_ar"] = addslashes($_POST['bodytext_ar']);
		$data["bodytext_en"] = addslashes($_POST['bodytext_en']);
		
		
		$id = intval( $intro->input->post('id') );

		$intro->db->update(PREFIX."_pages",$data,"id=$id");

		revalidateNext('pages');
		$intro->redirect($this->appname);
	}
	
	function Del(){
		global $intro,$sess_admin,$array;
		
		$id = intval( $intro->input->get_post('id') );
		policy($sess_admin['adminid'],$this->appname.".php" , "del");
		$sql = $intro->db->query("DELETE FROM ".PREFIX."_pages WHERE id=$id ");

		revalidateNext('pages');
		$intro->redirect($this->appname);
	}
	


}//end class Pages
?>