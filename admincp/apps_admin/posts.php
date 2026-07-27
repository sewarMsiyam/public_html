<?PHP
##############################################
#	Intro Technologies LTD.
#	Code By Mohammed AbuAbed - Intro.ps 
#	Email: info@intro.ps 
#	Support: http://intro.ps/
#   Date: 2018-06-03 Time: 22:10:43
#	AppName: posts
##############################################

class Posts_AppAdmin extends Intro_AppsAdmin{

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
					<i class=\"px-2 fa-solid fa-newspaper\"></i> ".$intro->lang["posts_appname"]." </a>
				  </li>
				  <li class=\"nav-item mx-2\">
					<a class=\"btn btn-"._css_active("Form")." px-3 p_add\" href=\"{$this->base}/Form?t=add\">
					<i class=\"px-2 fa-solid fa-plus\"></i> ".$intro->lang["posts_add"]."</a>
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
			$qry = " where title_ar  LIKE '%$search_txt%' ";
		}
		
		if ($order=="") $order="id:desc";
		$order = str_replace(":", " " , $order);

		$rows_per_page = 30;
		if ($page==0) $page=1;
		$nexlimit = ($page*$rows_per_page)-$rows_per_page;

		$result = $intro->db->query("SELECT * from ".PREFIX."_posts $qry order by $order  limit $nexlimit,$rows_per_page");
		$totrows = $intro->db->returned_rows;
		
		$sql_all_rows = $intro->db->query("SELECT id from ".PREFIX."_posts $qry ");	
		$totalrows = $intro->db->returned_rows;
		while($myrow = $intro->db->fetch_assoc($result)){
			@extract($myrow);			
			$data.= "
			<tr >
				<td class=\"center\">$id</td>
				<td>$title_ar</td>
				<td>$title_en</td>
				<td>$dtime</td>
				<td><img src=\"{$intro->base_url}$image\" style=\"max-height:50px;\" alt=\"\" /></td>
				<td>$hits</td>
				<td class=\"center\"> 
					<a class=\"btn btn-info p_edit btn-sm\" href=\"{$this->base}/Form?t=edit&amp;id=$id\" title=\"".$intro->lang["edit"]."\"><i class=\"fa-solid fa-pen-to-square\"></i></a>
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
					  <button type=\"submit\" class=\"btn btn-primary mb-3\"> <i class=\"fa-solid fa-search fa-sm\"></i> Search</button>
					  </div>
					</form>
				  </div>
				</div>";
	echo "
		<div class=\"card border-info\">
			  <div class=\"card-header text-dark bg-info\">
				<i class=\"px-2 fa-solid fa-newspaper\"></i> ".$intro->lang["posts_cur"]." ($totalrows)
			  </div>
			  <div class=\"card-body\">
			   <div class=\"table-responsive\">
				<table class=\"table table-striped table-sm table-hover\">
					  <thead  class=\"table-dark\">
						<tr>
							<th scope=\"col\">ID "._sort_th("id","index")."</th>
							<th scope=\"col\" >".$intro->lang["posts_title_ar"]." "._sort_th("title_ar","index")." </th>
							<th scope=\"col\">".$intro->lang["posts_title_en"]." "._sort_th("title_en","index")." </th>
							<th scope=\"col\">".$intro->lang["posts_dtime"]." "._sort_th("dtime","index")." </th>
							<th scope=\"col\">".$intro->lang["posts_image"]." "._sort_th("image","index")." </th>
							<th scope=\"col\">".$intro->lang["posts_hits"]." "._sort_th("hits","index")." </th>
							<th scope=\"col\">".$intro->lang["options"]."</th>
						</tr>
					  </thead>
					  <tbody>
					  $data
					  </tbody>
					 </table></div> ";		
		$order = str_replace(" ", ":" , $order);
		echo "<div class='pagination'>".pagination3("{$this->base}/index?search_txt=$search_txt&amp;order=$order", $totalrows, $rows_per_page, $page)."</div></div>
		</div>";
	}
	
	function Form($t=""){
		global $intro,$error,$sess_admin,$array;
		global $title_ar,$titles_ar,$title_en,$titles_en,$dtime,$image,$hits,$details_ar,$details_en;
		
		if($error || $_POST != null) @extract($_POST);	
		$id = intval( $intro->input->get_post("id") );		
		$t = $t==""?$intro->input->get_post("t"):$t;
		
		$this->nav();
		
		if($t == "edit")
		{			
			policy($sess_admin['adminid'],$this->appname.".php" , "edit");
			
			$sql = $intro->db->query("SELECT * FROM ".PREFIX."_posts WHERE id=$id;");
			$row = $intro->db->fetch_assoc($sql);
			@extract($row);

			$btn['legend_name'] = $intro->lang["posts_edit"]." <b>$id</b>";
			$btn['legend_icon'] = "fa-solid fa-pen-to-square";
			$btn['name'] = $intro->lang["save_changes"];
			$btn['img_icon'] = "fa-solid fa-pen-to-square";		   
			$btn['action'] = "doEdit";
		}
		elseif($t == "add")
		{
			policy($sess_admin['adminid'],$this->appname.".php" , "add");
			
			$btn['legend_name'] = $intro->lang["posts_add"];
			$btn['legend_icon'] = "fa-solid fa-plus";
			$btn['name'] = $intro->lang["save"];
			$btn['img_icon'] = "fa-solid fa-plus";		   
			$btn['action'] = "doAdd";
			$dtime = $dtime==""?date("Y-m-d H:i:s"):$dtime;
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
						<td>".$intro->lang["posts_title_ar"]." :  <span style='color:#ff0000'>*</span></td>
						<td><input  type=\"text\" name=\"title_ar\" value=\"$title_ar\" class='form-control' > {$this->error('title_ar')}</td>
					</tr>
					<tr>
						<td>".$intro->lang["posts_titles_ar"]." : </td>
						<td><textarea name=\"titles_ar\" class='form-control'>$titles_ar</textarea>{$this->error('titles_ar')}</td>
					</tr>
					<tr>
						<td>".$intro->lang["posts_title_en"]." :  <span style='color:#ff0000'>*</span></td>
						<td><input dir=\"ltr\" type=\"text\" name=\"title_en\" value=\"$title_en\" class='form-control'> {$this->error('title_en')}</td>
					</tr>
					<tr>
						<td>".$intro->lang["posts_titles_en"]." : </td>
						<td><textarea name=\"titles_en\" class='form-control'>$titles_en</textarea>{$this->error('titles_en')}</td>
					</tr>
					<tr>
						<td>".$intro->lang["posts_dtime"]." : </td>
						<td><input dir=\"ltr\" type=\"text\" name=\"dtime\" value=\"$dtime\" class='form-control' > {$this->error('dtime')}</td>
					</tr>
					<tr>
						<td>".$intro->lang["posts_image"]." : </td>
						<td>
							<input type=\"text\" dir=ltr name=\"image\" value=\"$image\" id=\"$image\" > 
							<span id=\"preview_image\"></span><img src=\"{$intro->base_url}uploads/news/$image\" width=\"50\" height=\"50\" style=\"float:left;\" alt=\"\" />
							<a class=\"btn btn-info icon-upload\" OnClick=\"javascript:popup('".admin_path."images.php?for_id=image');\">".$intro->lang["file_bring"]."</a> 
							{$this->error('image')}
						</td>
					</tr>
					<tr>
						<td>".$intro->lang["posts_hits"]." : </td>
						<td>$hits</td>
					</tr>
					<tr>
						<td>".$intro->lang["posts_details_ar"]." : </td>
						<td>"; fck_editor("details_ar",stripslashes($details_ar)/*,array('tools'=>'mini','dir'=>'rtl') */); echo " {$this->error('details_ar')}</td>
					</tr>
					<tr>
						<td>".$intro->lang["posts_details_en"]." : </td>
						<td>"; fck_editor("details_en",stripslashes($details_en)/*,array('tools'=>'mini','dir'=>'rtl') */); echo " {$this->error('details_en')}</td>
					</tr>
					<tr>
						<td></td>
						<td>
							<input type=\"hidden\" name=\"app_name\"  value=\"{$this->appname}\">
							<input type=\"hidden\" name=\"t\"  value=\"$t\">
							<input type=\"hidden\" name=\"id\"  value=\"$id\">
							<button type=\"submit\" name=\"app_action\" class='btn btn-primary' value=\"{$btn['action']}\">
								<i class=\"{$btn['img_icon']}\"></i> {$btn['name']} 
							</button>
						</td>
					</tr>
					</table>
					</div>
					</div>";
	
	}

	############################################################################

	function doAdd(){
		global $intro,$error;
				
		$title_ar = trim( $intro->input->post('title_ar') );
		$titles_ar = trim( $intro->input->post('titles_ar') );
		$title_en = trim( $intro->input->post('title_en') );
		$titles_en = trim( $intro->input->post('titles_en') );
		$dtime = trim( $intro->input->post('dtime') );
		$image = trim( $intro->input->post('image') );
		$hits = intval( $intro->input->post('hits') );
		$details_ar = trim( $intro->input->post('details_ar') );
		$details_en = trim( $intro->input->post('details_en') );
				
		if($title_ar == "" || $title_en == ""){

			if($title_ar == ""){ $error['title_ar'] = "<span class=error>".$intro->lang["required"]."</span>"; }
				if($title_en == ""){ $error['title_en'] = "<span class=error>".$intro->lang["required"]."</span>"; }
				
			
			
			$this->Form("add");
			die();
		}		
		
		$data["title_ar"] = $title_ar;
		$data["titles_ar"] = $titles_ar;
		$data["title_en"] = $title_en;
		$data["titles_en"] = $titles_en;
		$data["dtime"] = $dtime;
		$data["image"] = $image;
		//$data["hits"] = $hits;
		$data["details_ar"] = addslashes($details_ar);
		$data["details_en"] = addslashes($details_en);
				 
		$intro->db->insert(PREFIX."_posts",$data);
		$id = $intro->db->insert_id();
		revalidateNext('blog');
		$intro->redirect($this->appname);
	}

	############################################################################

	function doEdit(){
		global $intro,$array;
			
		$title_ar = trim( $intro->input->post('title_ar') );
		$titles_ar = trim( $intro->input->post('titles_ar') );
		$title_en = trim( $intro->input->post('title_en') );
		$titles_en = trim( $intro->input->post('titles_en') );
		$dtime = trim( $intro->input->post('dtime') );
		$image = trim( $intro->input->post('image') );
		$hits = intval( $intro->input->post('hits') );
		$details_ar = trim( $intro->input->post('details_ar') );
		$details_en = trim( $intro->input->post('details_en') );
		
		
		$data["title_ar"] = $title_ar;
		$data["titles_ar"] = $titles_ar;
		$data["title_en"] = $title_en;
		$data["titles_en"] = $titles_en;
		$data["dtime"] = $dtime;
		$data["image"] = $image;
		$data["hits"] = $hits;
		$data["details_ar"] = addslashes($details_ar);
		$data["details_en"] = addslashes($details_en);
		
		
		$id = intval( $intro->input->post('id') );

		$intro->db->update(PREFIX."_posts",$data,"id=$id");
		
		//$intro->logs($this->appname,"{$intro->lang["posts_edit"]} : $id",$_POST);
		
		revalidateNext('blog');
		$intro->redirect($this->appname);
	}
	
	function Del(){
		global $intro,$sess_admin,$array;
		
		$id = intval( $intro->input->get_post('id') );
		
		policy($sess_admin['adminid'],$this->appname.".php" , "del");
		
		$sql = $intro->db->query("SELECT * FROM ".PREFIX."_posts WHERE id=$id;");
		$row = $intro->db->fetch_assoc($sql);
		
		$sql = $intro->db->query("DELETE FROM ".PREFIX."_posts WHERE id=$id; ");
		
		//$intro->logs($this->appname,"delete : $id",$row);
		
		revalidateNext('blog');
		$intro->redirect($this->appname);
	}
	
	############################################################################

	function Active(){
		global $intro,$id;

		$sql = $intro->db->query("UPDATE ".PREFIX."_posts SET status='1' WHERE id='$id' ");

		revalidateNext('blog');
		$intro->redirect($this->appname);

	}

}//end class Posts
?>