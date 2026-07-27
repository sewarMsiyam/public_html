<?PHP
##############################################
#	Intro Technologies LTD.
#	Code By Mohammed AbuAbed - Intro.ps 
#	Email: info@intro.ps 
#	Support: http://intro.ps/support.php
#   Date: 2015-06-24 Time: 10:44:58
#	AppName: products
##############################################

class Videos_AppAdmin extends Intro_AppsAdmin{

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
					<i class=\"px-2 fas fa-video\"></i> ".$intro->lang["videos_appname"]." </a>
				  </li>
				  <li class=\"nav-item mx-2\">
					<a class=\"btn btn-"._css_active("Form")." px-3 p_add\" href=\"{$this->base}/Form?t=add\">
					<i class=\"px-2 fa-solid fa-plus\"></i> ".$intro->lang["videos_add"]."</a>
				  </li>
				   <li class=\"nav-item mx-2\">
					<a class=\"btn btn-"._css_active("Cat")." px-3 p_add\" href=\"{$this->base}/Cat\">
					<i class=\"px-2 fa-solid fa-folder\"></i> ".$intro->lang["cats"]."</a>
				  </li>
				   <li class=\"nav-item mx-2\">
					<a class=\"btn btn-"._css_active("FormCat")." px-3 p_add\" href=\"{$this->base}/FormCat?t=add\">
					<i class=\"px-2 fa-solid fa-folder\"></i> ".$intro->lang["cats_add_new"]."</a>
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
		$qry.=" and  vtitle_ar  LIKE '%$search_txt%' OR vtitle_en  LIKE '%$search_txt%' "
				." or body_ar  LIKE '%$search_txt%' or body_en  LIKE '%$search_txt%'   ";	
		}
		if ($order=="") $order="vid:desc";
		$order = str_replace(":", " " , $order);

		$rows_per_page = 30;
		if ($page==0) $page=1;
		$nexlimit = ($page*$rows_per_page)-$rows_per_page;

		$result = $intro->db->query("SELECT *,(select catname_en from maa_videos_cat where catid=v.catid) as catname from ".PREFIX."_videos v WHERE true $qry order by $order  limit $nexlimit,$rows_per_page");
		$totrows = $intro->db->returned_rows;
		
		$sql_all_rows = $intro->db->query("SELECT vid from ".PREFIX."_videos WHERE true $qry ");	
		$totalrows = $intro->db->returned_rows;
		while($myrow = $intro->db->fetch_assoc($result)){
			@extract($myrow);			
			$i++;
			
			$data2.= "
				<tr >
				<td >$vid</td>
				<td>$catname</td>
				<td><img src=\"{$intro->base_url}uploads/news/{$photo_en}\" style=\"max-height:50px;\" alt=\"\" /></td>
				<td>$status</td>
				<td>$vtitle_ar</td>
				<td>$vtitle_en</td>
			
				<td > 
					<a class=\"btn btn-info btn-xs p_edit\" href=\"{$this->base}/Form?t=edit&amp;id=$vid\" title=\"".$intro->lang["edit"]."\"><i class=\"fa-solid fa-pen-to-square\"></i></a>
					<a class=\"btn btn-danger btn-xs p_del intro_ui_del\" href=\"{$this->base}/Del?id=$vid\" OnClick=\"return false;\" title=\"".$intro->lang["del"]."\"><i class=\"fa-solid fa-trash\"></i></a>
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
				<i class=\"px-2 fas fa-video\"></i> Videos list  ($totalrows)
			  </div>
			  <div class=\"card-body\">
			 
			 <div class=\"table-responsive\">
				<table class=\"table table-striped table-sm table-hover\">
					  <thead  class=\"table-dark\">
						<tr>
							<th scope=\"col\">ID </th>
							<th scope=\"col\">Categories</th>
							<th scope=\"col\">photo </th>
							<th scope=\"col\">Status </th>
							<th scope=\"col\">Title Arabic </th>
							<th scope=\"col\">Title English </th>
							<th scope=\"col\">".$intro->lang["options"]."</th>
						</tr>
					  </thead>
					  <tbody>
					  $data2
					  </tbody>
					 </table></div>";
		$order = str_replace(" ", ":" , $order);
		echo "<center class='pagination'>".pagination3("{$this->base}/index?search_txt=$search_txt&amp;order=$order", $totalrows, $rows_per_page, $page)."</center>";
		echo"  </div></div>";
	}
	
	function Form($t=""){
		global $intro,$error,$sess_admin,$array;
		global $catid,$vtitle_ar,$title2_en,$status,$code_ar,$img_ar,$title2_ar,$vtitle_en,$place,$date_add,$photo,$price,$discount,$discount_percent,$can_discount,$body_ar,$body_en;
		global $code,$extra_titles_en,$extra_titles_ar;
		
		if($error || $_POST != null) @extract($_POST);
		$IF = intval( $intro->input->get_post("IF") );		
		$id = intval( $intro->input->get_post("id") );		
		$t = $t==""?$intro->input->get_post("t"):$t;
		
		if($IF != 1)
		$this->nav();
		
		if($t == "edit")
		{			
			policy($sess_admin['adminid'],$this->appname.".php" , "edit");	
			$sql = $intro->db->query("SELECT * FROM ".PREFIX."_videos where vid='$id'");
			$row = $intro->db->fetch_assoc($sql);
			@extract($row);

			$btn['legend_name'] = $intro->lang["videos_edit"]." <b>$id</b>";
			$btn['legend_icon'] = "fa-solid fa-edit";
			$btn['name'] = $intro->lang["save_changes"];
			$btn['img_icon'] = "fa-solid fa-edit";		   
			$btn['action'] = "do_Edit";
			$filear=$vfile_ar!=''?"<a href='{$intro->base_url}$vfile_ar'> Download </a> ":"";
			$fileen=$vfile_en !=''?"<a href='{$intro->base_url}$vfile_en'> Download </a> ":"";
		}
		elseif($t == "add")
		{
			policy($sess_admin['adminid'],$this->appname.".php" , "add");
			
			$btn['legend_name'] = $intro->lang["videos_add"];
			$btn['legend_icon'] = "fa-solid fa-plus";
			$btn['name'] = $intro->lang["save"];
			$btn['img_icon'] = "fa-solid fa-plus";		   
			$btn['action'] = "doAdd";
			$vdateadded=date('Y-m-d H:i:s');
			
		}		
			
	echo "
			<div class=\"card border-info\">
			  <div class=\"card-header text-dark bg-info\">
				<i class=\"{$btn['legend_icon']}\"></i> {$btn['legend_name']}  
			  </div>
			  <div class=\"card-body\">
			  <form method=\"POST\" name=\"form_add\" action=\"{$this->base}/{$btn['action']}\" enctype=\"multipart/form-data\">
			  <table class=\"table table-sm \">
			<tbody>
			<tr>
				<td>".$intro->lang["videos_catid"]." :  <span style='color:#ff0000'>*</span></td>
				<td>".form_select_global('catid','Choose','videos_cat',$catid,'catid','catname_en')."
				{$this->error('catid')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["videos_vtitle_ar"]." :  <span style='color:#ff0000'>*</span></td>
				<td><input  type=\"text\" name=\"vtitle_ar\" value=\"$vtitle_ar\" class='form-control'> {$this->error('vtitle_ar')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["videos_vtitle_en"]." : </td>
				<td><input dir='ltr' type=\"text\" name=\"vtitle_en\" value=\"$vtitle_en\" class='form-control'> {$this->error('vtitle_en')}</td>
			</tr> 
			<tr>
				<td>".$intro->lang["products_status"]." :  <span style='color:#ff0000'>*</span></td>
				<td>".form_option('status',$status)."</td>
			</tr>
			<tr>
				<td>Date Added :  <span style='color:#ff0000'>*</span></td>
				<td><input dir='ltr' type=\"text\" name=\"vdateadded\" value=\"$vdateadded\" class='form-control'></td>
			</tr>
			
			<tr>
				<td colspan=2><div class='alert alert-success'> If it is  in youtube link 
					<p>It will be the priority </p>
				</div></td>
			</tr>
			<tr>
				<td>Youtube Arabic :  </td>
				<td><input type=text dir=ltr name=\"url_ar\"  value='$url_ar' class='form-control' ></td>
			</tr>
			<tr>
				<td>Youtube English : </td>
				<td><input type=text  dir=ltr  name=\"url_en\"  class='form-control' value='$url_en'></td>
			</tr>
			<tr>
				<td colspan=2><div class='alert alert-success'> If it is not in youtube link 
					<p>Please : attach Files and there images</p>
				</div></td>
			</tr>
			
			<tr>
				<td>".$intro->lang["videos_photo_ar"]." Arabic Photo :  <span style='color:#ff0000'>*</span></td>
				<td><input type=\"text\" dir=ltr name=\"photo_ar\" value=\"$photo_ar\" id=\"$photo_ar\" > 
				   <span id=\"preview_photo_ar\"></span><img src=\"{$intro->base_url}uploads/news/$photo_ar\" width=\"100\" height=\"100\" style=\"float:left;\" alt=\"\" />
				   <a class=\"btn btn-info icon-upload\" OnClick=\"javascript:popup('".admin_path."images.php?for_id=photo_ar');\">".$intro->lang["file_bring"]."</a> 
				   {$this->error('photo_ar')}
				</td>
			</tr>	
			<tr>
				<td>".$intro->lang["videos_photo_en"]." :  <span style='color:#ff0000'>*</span></td>
				<td><input type=\"text\" dir=ltr name=\"photo_en\" value=\"$photo_en\" id=\"$photo_en\" > 
				   <span id=\"preview_photo_en\"></span><img src=\"{$intro->base_url}uploads/news/$photo_en\" width=\"100\" height=\"100\" style=\"float:left;\" alt=\"\" />
				   <a class=\"btn btn-info icon-upload\" OnClick=\"javascript:popup('".admin_path."images.php?for_id=photo_en');\">".$intro->lang["file_bring"]."</a> 
				   {$this->error('photo_en')}
				</td>
			</tr>
			<tr>
				<td>File Arabic  : </td>
				<td><input type=file  dir=ltr  name=\"vfile_ar\" class='ltr'  >$filear</td>
			</tr>
			<tr>
				<td>File  English : </td>
				<td><input type=file  dir=ltr  name=\"vfile_en\" class='ltr'  >$fileen</td>
			</tr>
			
			<tr>
				<td>Details Arabic : </td>
				<td>"; fck_editor("body_ar",intro_html_decode($body_ar)); echo "  </td>
			</tr>
			<tr>
				<td>Details English : </td>
				<td>"; fck_editor("body_en",intro_html_decode($body_en)); echo "</td>
			</tr>
			
			
			
			<tr>
				<td  ></td>
				<td  >
					<input type=\"hidden\" name=\"t\"  value=\"$t\">
					<input type=\"hidden\" name=\"id\"  value=\"$vid\">
					<input type=\"hidden\" name=\"IF\"  value=\"$IF\">
					<button type=\"submit\" class='btn btn-primary'>
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
	
		$status = intval( $intro->input->post('status') );
		$catid = intval( $intro->input->post('catid') );
		$vtitle_ar = trim( $intro->input->post('vtitle_ar') );
		$vtitle_en = trim( $intro->input->post('vtitle_en') );
		$vdateadded = trim( $intro->input->post('vdateadded') );
		$url_ar = trim( $intro->input->post('url_ar') );
		$url_en = trim( $intro->input->post('url_en') );
		$photo_ar = trim( $intro->input->post('photo_ar') );
		$photo_en = trim( $intro->input->post('photo_en') );
		$body_ar = trim( $intro->input->post('body_ar') );
		$body_en = trim( $intro->input->post('body_en') );
		
		
		if($catid == 0){
			if($catid == 0){ $error['catid'] = "<span class=error>".$intro->lang["required"]."</span>"; }
			$this->Form("add");
			die();
		}		

		$TempFile = $_FILES["vfile_ar"]["tmp_name"];
		$check = @getimagesize($_FILES["vfile_ar"]["tmp_name"]);
		$path = "../uploads/videos/";
			if(!is_dir($path)){
				@mkdir($path);
				@file_put_contents($path."index.html", "404 not found.");
			}
		if( @is_uploaded_file($TempFile) )
		{		
			$FileName = $_FILES["vfile_ar"]["name"];
			$newfilename=explode('.',$FileName);
			$ext = pathinfo($FileName, PATHINFO_EXTENSION);
			$FileNamenew = $newfilename[0].".".$ext;
			@move_uploaded_file ($TempFile, $path.$FileName);
			$data["vfile_ar"] = "uploads/videos/".$FileName;
		}
		
		$TempFile = $_FILES["vfile_en"]["tmp_name"];
		$check = @getimagesize($_FILES["vfile_en"]["tmp_name"]);
		$path = "../uploads/videos/";
			if(!is_dir($path)){
				@mkdir($path);
				@file_put_contents($path."index.html", "404 not found.");
			}
		if( @is_uploaded_file($TempFile) )
		{		
			$FileName = $_FILES["vfile_en"]["name"];
			$newfilename=explode('.',$FileName);
			$ext = pathinfo($FileName, PATHINFO_EXTENSION);
			$FileNamenew = $newfilename[0].".".$ext;
			@move_uploaded_file ($TempFile, $path.$FileName);
			$data["vfile_en"] = "uploads/videos/".$FileName;
		}
		
		$data["catid"] = $catid;
		$data["vtitle_ar"] = $vtitle_ar;
		$data["vtitle_en"] = $vtitle_en;
		$data["vdateadded"] = $vdateadded;
		$data["url_ar"] = $url_ar;
		$data["url_en"] = $url_en;
		$data["photo_ar"] = $photo_ar;
		$data["photo_en"] = $photo_en;
		$data["body_ar"] = intro_html_encode($body_ar);
		$data["body_en"] = intro_html_encode($body_en);
		$data["status"] = $status;
		
		$intro->db->insert(PREFIX."_videos",$data);
		revalidateNext('videos');
		$intro->redirect($this->appname);
	}


	function do_Edit(){
		global $intro,$error,$array;
		
		$status = intval( $intro->input->post('status') );
		$catid = intval( $intro->input->post('catid') );
		$vtitle_ar = trim( $intro->input->post('vtitle_ar') );
		$vtitle_en = trim( $intro->input->post('vtitle_en') );
		$vdateadded = trim( $intro->input->post('vdateadded') );
		$url_ar = trim( $intro->input->post('url_ar') );
		$url_en = trim( $intro->input->post('url_en') );
		$photo_ar = trim( $intro->input->post('photo_ar') );
		$photo_en = trim( $intro->input->post('photo_en') );
		$body_ar = trim( $intro->input->post('body_ar') );
		$body_en = trim( $intro->input->post('body_en') );
		
		$data["catid"] = $catid;
		$data["vtitle_ar"] = $vtitle_ar;
		$data["vtitle_en"] = $vtitle_en;
		$data["vdateadded"] = $vdateadded;
		$data["url_ar"] = $url_ar;
		$data["url_en"] = $url_en;
		$data["photo_ar"] = $photo_ar;
		$data["photo_en"] = $photo_en;
		$data["body_ar"] = intro_html_encode($body_ar);
		$data["body_en"] = intro_html_encode($body_en);
		$data["status"] = $status;
		
		$TempFile = $_FILES["vfile_ar"]["tmp_name"];
		$check = @getimagesize($_FILES["vfile_ar"]["tmp_name"]);
		$path = "../uploads/videos/";
			if(!is_dir($path)){
				@mkdir($path);
				@file_put_contents($path."index.html", "404 not found.");
			}
		if( @is_uploaded_file($TempFile) )
		{		
			$FileName = $_FILES["vfile_ar"]["name"];
			$newfilename=explode('.',$FileName);
			$ext = pathinfo($FileName, PATHINFO_EXTENSION);
			$FileNamenew = $newfilename[0].".".$ext;
			@move_uploaded_file ($TempFile, $path.$FileName);
			$data["vfile_ar"] = "uploads/videos/".$FileName;
		}
	
		$id = intval( $intro->input->post('id') );

		$intro->db->update(PREFIX."_videos",$data,"vid=$id");
		revalidateNext('videos');
		$intro->redirect($this->appname);
	}

	function Del(){
		global $intro,$sess_admin,$array;
		
		$id = intval( $intro->input->get_post('id') );
		
		policy($sess_admin['adminid'],$this->appname.".php" , "del");

		$sql = $intro->db->query("DELETE FROM ".PREFIX."_videos WHERE vid=$id ");

		revalidateNext('videos');
		$intro->redirect($this->appname);
	}



	############################################################################
	# CAT
	############################################################################

	function Cat(){
		global $intro,$array;

		$qry = "";
		$page = intval( $intro->input->get_post("page") );
		$order = trim( $intro->input->get_post("order") );
		$search_txt = trim( $intro->input->get_post("search_txt") );

		$this->nav();

		
		if ($order=="") $order="catid:desc";
		$order = str_replace(":", " " , $order);

		$rows_per_page = 30;
		if ($page==0) $page=1;
		$nexlimit = ($page*$rows_per_page)-$rows_per_page;

		$result = $intro->db->query("SELECT * from ".PREFIX."_videos_cat $qry order by $order  limit $nexlimit,$rows_per_page");
		$totrows = $intro->db->returned_rows;
		
		$sql_all_rows = $intro->db->query("SELECT catid from ".PREFIX."_videos_cat $qry ");	
		$totalrows = $intro->db->returned_rows;
			while($myrow = $intro->db->fetch_assoc($result)){
			@extract($myrow);	
			$data.= "
			<tr >
				<td >$catid</td>
				<td>$catname_ar</td>
				<td>$catname_en</td>
				<td><img src=\"{$intro->base_url}uploads/news/$catimage\" style=\"max-height:50px;\" alt=\"\" /></td>
				<td > 
					<a class=\"btn btn-info p_edit btn-sm\" href=\"{$this->base}/FormCat?t=edit&amp;catid=$catid\" title=\"".$intro->lang["edit"]."\"><i class=\"fa-solid fa-pen-to-square\"></i></a>
					<a class=\"btn btn-danger p_del intro_ui_del btn-sm\" href=\"{$this->base}/DelCat?catid=$catid\" OnClick=\"return false;\" title=\"".$intro->lang["del"]."\"><i class=\"fa-solid fa-trash\"></i></a>
				</td>
			</tr>";
		}

		echo "
		<div class=\"card border-info\">
			  <div class=\"card-header text-dark bg-info\">
				<i class=\"px-2 fa-solid fa-folder\"></i> ".$intro->lang["products_cat_cur"]." ($totalrows)
			  </div>
			  <div class=\"card-body\">
			 <div class=\"table-responsive\">
				<table class=\"table table-striped table-sm table-hover\">
					  <thead  class=\"table-dark\">
						<tr>
							<th scope=\"col\" >ID "._sort_th("catid","index")."</th>
							<th scope=\"col\">Arabic title </th>
							<th scope=\"col\">English title </th>
							<th scope=\"col\">Image </th>
							<th scope=\"col\">".$intro->lang["options"]."</th>
						</tr>
					  </thead>
					  <tbody>
					  $data
					  </tbody>
					 </table></div> "; 
		$order = str_replace(" ", ":" , $order);
		echo "<center class='pagination'>".pagination3("{$this->base}/Cat?search_txt=$search_txt&amp;order=$order", $totalrows, $rows_per_page, $page)."</center>";
		echo" </div>
		</div>";
	}	
	
	function FormCat($t=""){
		global $intro,$error,$sess_admin,$array;
		global $catvtitle_ar,$catvtitle_en,$catimage;
		
		if($error || $_POST != null) @extract($_POST);
		$catid = intval( $intro->input->get_post("catid") );		
		$t = $t==""?$intro->input->get_post("t"):$t;
		
		$this->nav();
		
		if($t == "edit")
		{			
			policy($sess_admin['adminid'],$this->appname.".php" , "edit");
			
			$sql = $intro->db->query("SELECT * FROM ".PREFIX."_videos_cat where catid='$catid'");
			$row = $intro->db->fetch_assoc($sql);
			@extract($row);

			$btn['legend_name'] = $intro->lang["products_cat_edit"]." <b>$catid</b>";
			$btn['legend_icon'] = "fa-solid fa-edit";
			$btn['name'] = $intro->lang["save_changes"];
			$btn['img_icon'] = "fa-solid fa-edit";		   
			$btn['action'] = "doEditCat";
		}
		elseif($t == "add")
		{
			policy($sess_admin['adminid'],$this->appname.".php" , "add");
			
			$btn['legend_name'] = $intro->lang["products_cat_add"];
			$btn['legend_icon'] = "fa-solid fa-plus";
			$btn['name'] = $intro->lang["save"];
			$btn['img_icon'] = "fa-solid fa-plus";		   
			$btn['action'] = "doAddCat";
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
				<td>Arabic title :  <span style='color:#ff0000'>*</span></td>
				<td><input  type=\"text\" name=\"catname_ar\" value=\"$catname_ar\" class='form-control'> {$this->error('catname_ar')}</td>
			</tr>
			<tr>
				<td>English title :  <span style='color:#ff0000'>*</span></td>
				<td><input  type=\"text\" name=\"catname_en\" value=\"$catname_en\" class='form-control'> {$this->error('catname_en')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["products_cat_catimage"]." : </td>
				<td><input type=\"text\" dir=ltr name=\"catimage\" value=\"$catimage\" id=\"$catimage\" > 
				   <span id=\"preview_catimage\"></span><img src=\"{$intro->base_url}uploads/news/$catimage\" width=\"50\" height=\"50\" style=\"float:left;\" alt=\"\" />
				   <a class=\"btn btn-info icon-upload\" OnClick=\"javascript:popup('".admin_path."images.php?for_id=catimage');\">".$intro->lang["file_bring"]."</a> 
				   {$this->error('catimage')}
				</td>
			</tr>
			<tr>
				<td ></td>
				<td  >
					<input type=\"hidden\" name=\"app_name\"  value=\"{$this->appname}\">
					<input type=\"hidden\" name=\"t\"  value=\"$t\">
					<input type=\"hidden\" name=\"catid\"  value=\"$catid\">
					<button type=\"submit\" class='btn btn-primary' >
					<i class=\"{$btn['img_icon']}\"></i>{$btn['name']} </button>
					
				</td>
			</tr>
			</table>
			</form>
			</div>
		</div>";
	}

	############################################################################

	function doAddCat(){
		global $intro,$error;
		$catname_ar = trim( $intro->input->post('catname_ar') );
		$catname_en = trim( $intro->input->post('catname_en') );
		$catimage = trim( $intro->input->post('catimage') );
		
		if($catname_ar == "" || $catname_en == ""){

			if($catname_ar == ""){ $error['catname_ar'] = "<span class=error>".$intro->lang["required"]."</span>"; }
				if($catname_en == ""){ $error['catname_en'] = "<span class=error>".$intro->lang["required"]."</span>"; }
			$this->FormCat("add");
			die();
		}		
		
		$data["catname_ar"] = $intro->input->post('catname_ar');
		$data["catname_en"] = $intro->input->post('catname_en');
		$data["catimage"] = $intro->input->post('catimage');
		$intro->db->insert(PREFIX."_videos_cat",$data);
		revalidateNext('videos');
		$intro->redirect($this->appname , "Cat");
	}

	############################################################################

	function doEditCat(){
		global $intro,$array;
			
		
		$data["catname_ar"] = $intro->input->post('catname_ar');
		$data["catname_en"] = $intro->input->post('catname_en');
		$data["catimage"] = $intro->input->post('catimage');
		$catid = intval( $intro->input->post('catid') );
		$intro->db->update(PREFIX."_videos_cat",$data,"catid=$catid");
		revalidateNext('videos');
		$intro->redirect($this->appname , "Cat");
	}
	
	function DelCat(){
		global $intro,$sess_admin,$array;
		
		$catid = intval( $intro->input->get_post('catid') );
		
		policy($sess_admin['adminid'],$this->appname.".php" , "del");

		$sql = $intro->db->query("DELETE FROM ".PREFIX."_videos_cat WHERE catid=$catid ");

		revalidateNext('videos');
		$intro->redirect($this->appname , "Cat");
	}
		
}//end class Products
?>