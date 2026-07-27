<?PHP
##############################################
#	Intro Technologies LTD.
#	Code By Mohammed AbuAbed - Intro.ps 
#	Email: info@intro.ps 
#	Support: http://intro.ps/support.php
#   Date: 2013-02-11 Time: 19:14:24
#	AppName: adv
##############################################


class Adv_AppAdmin extends Intro_AppsAdmin{

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
					<i class=\"px-2 fa-solid fa-rectangle-ad\"></i> ".$intro->lang["adv_appname"]." </a>
				  </li>
				  <li class=\"nav-item mx-2\">
					<a class=\"btn btn-"._css_active("Form")." px-3 p_add\" href=\"{$this->base}/Form?t=add\">
					<i class=\"px-2 fa-solid fa-plus\"></i> ".$intro->lang["adv_add"]."</a>
				  </li>
				  <li class=\"nav-item mx-2\">
					<a class=\"btn btn-"._css_active("Cat")." px-3 p_add\" href=\"{$this->base}/Cat\">
					<i class=\"px-2 fa-solid fa-rectangle-ad\"></i> ".$intro->lang["cats"]."</a>
				  </li>
				  <li class=\"nav-item mx-2\">
					<a class=\"btn btn-"._css_active("FormCat")." px-3 p_add\" href=\"{$this->base}/FormCat?t=add\">
					<i class=\"px-2 fa-solid fa-plus\"></i> ".$intro->lang["cats_add_new"]."</a>
				  </li>	  
			</ul>";
	}

	
	function index(){
		global $intro,$active,$page,$search_txt,$policy;

		
		$qry = "";
		$this->nav();
	
		if($search_txt !=""){
			$qry = " where title  LIKE '%$search_txt%' ";
		}

      
		$order = isset($_GET['order']);
		if (!isset($order) or $order=="") $order="advid:desc";
		$order = str_replace(":", " " , $order);

		$rows_per_page = "30";
		if (!isset($page) or $page=="") $page=1;
		$nexlimit = $page * $rows_per_page - $rows_per_page;

		$result = $intro->db->query("SELECT * from ".PREFIX."_adv ".$qry." order by $order  limit $nexlimit,$rows_per_page");
		$totrows = $intro->db->returned_rows;
		
		$sql_all_rows = $intro->db->query("SELECT advid from ".PREFIX."_adv ".$qry." ");	
		$totalrows = $intro->db->returned_rows;
		while($myrow = $intro->db->fetch_assoc($result)){
			@extract($myrow);
			$catname = GeTheCat("adv_cat","catname","catid",$catid);
			if($type == 1){
				$img = "<img src=\"{$intro->base_url}$advfile\"  width=200 height=\"70\" />";	
			}else{
			$img= $intro->ar->adv_type($type);
			}
			$data.= "
			<tr class=\""._odd_even($i)."\">
				<td class=\"center\">$advid</td>
				<td>".$intro->ar->adv_type($type)."</td>
				<td>$catname</td>
				<td>$title</td>
				<td class=center>$img</td>
				<td class=center>$shows</td>
				<td class=center>$width</td>
				<td class=center>$height</td>
				<td class=\"center\"> 
					<a class=\"btn btn-info p_edit btn-sm\" href=\"{$this->base}/Form?t=edit&advid=$advid\" title=\"".$intro->lang["edit"]."\"><i class=\"fa-solid fa-pen-to-square\"></i></a>
					<a class=\"btn btn-danger p_del intro_ui_del btn-sm\" href=\"{$this->base}/Del?advid=$advid\" OnClick=\"return false;\" title=\"".$intro->lang["del"]."\"><i class=\"fa-solid fa-trash\"></i></a>		
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
				<i class=\"px-2 fa-solid fa-rectangle-ad\"></i> ".$intro->lang["adv_cur"]." ($totalrows)
			  </div>
			  <div class=\"card-body\">
			   <div class=\"table-responsive\">
				<table class=\"table table-striped table-sm table-hover\">
					  <thead  class=\"table-dark\">
						<tr>
							<th >ID "._sort_th("advid","index")."</th>
							<th >".$intro->lang["adv_type"]." "._sort_th("type","index")." </th>
							<th >".$intro->lang["adv_catid"]." "._sort_th("catid","index")." </th>
							<th >".$intro->lang["adv_title"]." "._sort_th("title","index")." </th>
							<th >".$intro->lang["adv_advfile"]." "._sort_th("title","index")." </th>
							<th >".$intro->lang["adv_shows"]." "._sort_th("shows","index")." </th>
							<th >".$intro->lang["adv_width"]." "._sort_th("width","index")." </th>
							<th >".$intro->lang["adv_height"]." "._sort_th("height","index")." </th>
							<th>".$intro->lang["options"]."</th>
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

	
	function error($index=""){
		global $error;
		
		return isset($error[$index])?$error[$index]:"";
	}
	function Form($t="add"){
		global $intro,$t,$error,$policy;
		global $advid,$type,$catid,$title,$advfile,$html,$url,$status,$hits,$shows,$date_start,$date_end,$width,$height,$target,$after_expire;
		global $username, $advfile_prev,$contents;
		
		if($_GET) @extract($_GET);
		if($error || $_POST) @extract($_POST);
		
		$advid = intval($advid);
		$t = stripslashes($t);

		$this->nav();
		if($t == "edit"){
			$sql = $intro->db->query("SELECT * FROM ".PREFIX."_adv where advid='$advid'");
			$row = $intro->db->fetch_assoc($sql);
			@extract($row);
			$class="p_edit";
			$btn['legend_name'] = "تعديل الاعلان رقم: <b>$advid</b>";
			$btn['legend_img'] = "fa-solid fa-edit";
			$btn['name'] = $intro->lang["save_changes"];
			$btn['img'] = "fa-solid fa-edit";		   
			$btn['action'] = "doEdit";
		
			$advfile_prev = "<br/><img src=\"{$intro->base_url}$advfile\" height=\"80\" /><br/>";		
		}
		elseif($t == "add"){

			$btn['legend_name'] = $intro->lang["adv_add"];
			$btn['legend_img'] = "fa-solid fa-plus";
			$btn['name'] = $intro->lang["add_new"];
			$btn['img'] = "fa-solid fa-plus";		   
			$btn['action'] = "doAdd";
			$btn['copy'] = "";
			$class="p_add";
			$status = $status==""?1:$status;
			$target = $target==""?"_self":$target;
			$date_start = $date_start==""?date("Y-m-d"):$date_start;
			$date_end = $date_end==""?date("Y-12-31"):$date_end;
		}		
		
		$html = stripslashes($html);
			
	echo "
		<div class=\"card border-info\">
			  <div class=\"card-header text-dark bg-info\">
				<i class=\"{$btn['legend_img']}\"></i> {$btn['legend_name']}  
			  </div>
			  <div class=\"card-body\">
			  <form method=\"POST\" name=\"form_add\"  action=\"{$this->base}/{$btn['action']}\" enctype=\"multipart/form-data\">
			   <div class=\"table-responsive\">
			  <table class=\"table table-sm \" id=\"table1\">
					 <tbody>
			<tr>
				<td>".$intro->lang["adv_type"]." :  <span style='color:#ff0000'>*</span></td>
				<td>".form_select_array("type",$intro->ar->adv_type(),$type)." {$this->error('type')}</td>
			
				<td>".$intro->lang["adv_catid"]." :  <span style='color:#ff0000'>*</span></td>
				<td>".form_select("catid",$catid,"adv_cat")." {$this->error('catid')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["adv_title"]." :  <span style='color:#ff0000'>*</span></td>
				<td><input  type=\"text\" name=\"title\" value=\"$title\" class='form-control'>{$this->error('title')}</td>
				<td> </td>
				<td><!--<input dir=\"ltr\" type=\"text\" name=\"username\" value=\"$username\" class='form-control'> {$this->error('username')}
				<br/>like: http://site.com/adv/username--></td>
			</tr>
			<tr class=\"typeupload\">
				<td>".$intro->lang["adv_advfile"]." : </td>
				<td colspan=\"3\"><input type=\"text\" dir=ltr name=\"advfile\" value=\"$advfile\" class='form-control'> 
				   $advfile_prev
				   [ <a href=\"javascript:void();\" OnClick=\"javascript:popup('../../upload.php?f=form_add.advfile&p=adv');\">".$intro->lang["file_bring"]."</a> ] {$this->error('advfile')}</td>
			</tr>
			<tr  class=\"typehtml\">
				<td>".$intro->lang["adv_html"]." : </td>
				<td colspan=\"3\"><textarea name=\"html\" dir=ltr class='form-control'>".intro_html_decode($html)."</textarea>{$this->error('html')}</td>
			</tr>
			<tr  class=\"typeurl\">
				<td>".$intro->lang["adv_url"]." : </td>
				<td colspan=\"3\"><input dir=\"ltr\" type=\"text\" name=\"url\" value=\"$url\" class='form-control'> {$this->error('url')} </td>
			</tr>
			<tr>
				<td>".$intro->lang["adv_status"]." : </td>
				<td colspan=\"3\">".form_select_array("status",$intro->ar->adv_status(),$status)."</td>
			</tr>";
			if($t == "edit"){
			echo "
			<tr>
				<td>".$intro->lang["adv_hits"]." : </td>
				<td><input dir=\"ltr\" type=\"text\" name=\"hits\" value=\"$hits\" class='form-control'> {$this->error('hits')} </td>
		
				<td>".$intro->lang["adv_shows"]." : </td>
				<td><input dir=\"ltr\" type=\"text\" name=\"shows\" value=\"$shows\" class='form-control'> {$this->error('shows')} </td>
			</tr>";
			}
			echo "
			<tr>
				<td>".$intro->lang["adv_date_start"]." : </td>
				<td><input dir=\"ltr\" type=\"text\" class=\"datepicker\" name=\"date_start\" value=\"$date_start\" class='form-control'> {$this->error('date_start')}</td>
			
				<td>".$intro->lang["adv_date_end"]." : </td>
				<td><input dir=\"ltr\" type=\"text\" class=\"datepicker\" name=\"date_end\" value=\"$date_end\" class='form-control'> </td>
			</tr>
			<tr>
				<td>".$intro->lang["adv_width"]." : </td>
				<td><input dir=\"ltr\" type=\"text\" name=\"width\" value=\"$width\" class='form-control'></td>
			
				<td>".$intro->lang["adv_height"]." : </td>
				<td><input dir=\"ltr\" type=\"text\" name=\"height\" value=\"$height\" class='form-control'> </td>
			</tr>
			<tr>
				<td  class=\"pic swf\">".$intro->lang["adv_target"]." : </td>
				<td>".form_select_array("target",$intro->ar->adv_target(),$target)."</td>
		
				<td>".$intro->lang["adv_after_expire"]." : </td>
				<td>".form_select_array("after_expire",$intro->ar->adv_after_expire(),$after_expire)." </td>
			</tr>
			<tr>
				<td class=\"center\" colspan=\"4\">
					<input type=\"hidden\" name=\"t\"  value=\"$t\">
					<input type=\"hidden\" name=\"advid\"  value=\"$advid\">
					<button class=\"btn btn-primary\" type=\"submit\"  title=\"{$btn['name']}\">
					<i class='{$btn['img']}'> {$btn['name']} </i>
					</button>
					
				</td>
			</tr>
			</tbody>
			</table></div>
			</form>
			</div>
			<div/>";
			
			//$this->adv_show_by_type($type);
			echo "<script>
			$(document).ready(function () {
				
				$('.typeurl, .typehtml, .typeupload').hide();
				
				$('select[name=type]').change(function(){
					var advtype = $(this).val();
					show_hide_type(advtype);
					
				});	
				function show_hide_type(advtype)
				{
					if(advtype == 1 || advtype == 2){
						$('.typeupload, .typeurl').show();
						$('.typehtml').hide();
					}	
					else if(advtype == 3){
						$('.typehtml').show();
						$('.typeupload, .typeurl').hide();
					}
					else if(advtype == 4){
						$('.typeurl').show();
						$('.typehtml, .typeupload').hide();
					}
				}
				
				show_hide_type($type);
			
			});</script>";
	}
	function ViewCode(){
		global $intro,$t,$error;
		global $advid;
		
		

		$advid = intval($advid);
		$t = stripslashes($t);

		$this->nav();
		

			$sql = $intro->db->query("SELECT * FROM ".PREFIX."_adv where advid='$advid'");
			$row = $intro->db->fetch_assoc($sql);
			@extract($row);
		echo "
		<div class=\"card border-info\">
			  <div class=\"card-header text-dark bg-info\">
				Code
			  </div>
			  <div class=\"card-body\">
			  <form method=\"POST\" name=\"form_add\"  action=\"{$this->base}/{$btn['action']}\" enctype=\"multipart/form-data\">
			  <table class=\"table table-sm \">
					 <tbody>
			
			<tr>
				<td>Style Variable: </td>
				<td><textarea dir=ltr name=\"html\" cols=\"60\" rows=\"10\">".'{$intro->banner('.$catid.')}'."</textarea></td>
			</tr>
			<tr>
				<td>JavaScript Code : </td>
				<td><textarea dir=ltr name=\"html\" cols=\"60\" rows=\"10\"><script src=\"{$intro->option['site_url']}/ad.php?id=$advid\"></script></textarea></td>
			</tr>
			</tbody>
			</table>
			</div>
			</div>";
	
	}
	
	function doAdd(){
			global $intro,$t,$error,$sess_admin;
			global $type,$catid,$title,$advfile,$html,$url,$status,$hits,$shows,$date_start,$date_end,$width,$height,$target,$after_expire ;
			$adm=$sess_admin['adminid'];
			$app=$this->appname;
			policy($adm,$app.".php",'add');
			$type = intval($_POST['type']);
		$catid = intval($_POST['catid']);
		$title = trim($_POST['title']);
		$advfile = trim($_POST['advfile']);
		$html = trim($_POST['html']);
		$url = trim($_POST['url']);
		$status = intval($_POST['status']);
		$hits = intval(@$_POST['hits']);
		$shows = intval(@$_POST['shows']);
		$date_start = trim($_POST['date_start']);
		$date_end = trim($_POST['date_end']);
		$width = intval($_POST['width']);
		$height = intval($_POST['height']);
		$target = trim($_POST['target']);
		$after_expire = trim($_POST['after_expire']);
		$contents = addslashes(@$_POST['contents']);
		$username = trim(@$_POST['username']);
		
		
		
		
		
			if($title == "" ||  $type == 0 || $catid == 0){

				if($title == ""){ $error['title'] = "<span class=error>".$intro->lang["required"]."</span>"; }
				
				if($type == 0){ $error['type'] = "<span class=error>".$intro->lang["required"]."</span>"; }
				if($catid == 0){ $error['catid'] = "<span class=error>".$intro->lang["required"]."</span>"; }
				
				
				$this->Form("add");
				die();
			}
			 
			
			$data["type"] = $type;
			$data["catid"] = $catid;
			$data["title"] = $title;
			$data["advfile"] = $advfile;
			$data["html"] = intro_html_encode($html);
			$data["url"] = $url;
			$data["status"] = $status;
			$data["hits"] = $hits;
			$data["shows"] = $shows;
			$data["date_start"] = $date_start;
			$data["date_end"] = $date_end;
			$data["width"] = $width;
			$data["height"] = $height;
			$data["target"] = $target;
			$data["after_expire"] = $after_expire;
			$data["contents"] = $contents;
			$data["username"] = $username;
			 
			//$sql = $intro->db->query("INSERT INTO ".PREFIX."_adv (type,catid,title,advfile,html,url,status,hits,shows,date_start,date_end,width,height,target,after_expire) VALUES ('$type','$catid','$title','$advfile','$html','$url','$status','$hits','$shows','$date_start','$date_end','$width','$height','$target','$after_expire') ");
			$intro->db->insert(PREFIX."_adv",$data);
			
			revalidateNext('adv');
			$intro->redirect($this->appname);
	}

	############################################################################

	function doEdit(){
		global $intro,$advid,$sess_admin;
		global $type,$catid,$title,$advfile,$html,$url,$status,$hits,$shows,$date_start,$date_end,$width,$height,$target,$after_expire ;

		$adm=$sess_admin['adminid'];
			$app=$this->appname;
			policy($adm,$app.".php",'edit');
			$data["type"] = $type;
			$data["catid"] = $catid;
			$data["title"] = $title;
			$data["advfile"] = $advfile;
			$data["html"] = intro_html_encode($html);
			$data["url"] = $url;
			$data["status"] = $status;
			$data["hits"] = $hits;
			$data["shows"] = $shows;
			$data["date_start"] = $date_start;
			$data["date_end"] = $date_end;
			$data["width"] = $width;
			$data["height"] = $height;
			$data["target"] = $target;
			$data["after_expire"] = $after_expire;
			$data["username"] = @$_POST['username'];
			$data["contents"] = addslashes(@$_POST['contents']);
			$catid=$_POST['catid'];
			$data["catid"] = $catid;
		
		//$sql = $intro->db->query("UPDATE ".PREFIX."_adv SET type='$type',catid='$catid',title='$title',advfile='$advfile',html='$html',url='$url',status='$status',hits='$hits',shows='$shows',date_start='$date_start',date_end='$date_end',width='$width',height='$height',target='$target',after_expire='$after_expire' WHERE advid='$advid' ");
		$intro->db->update(PREFIX."_adv",$data,"advid='$advid'");

		revalidateNext('adv');
		$intro->redirect($this->appname);
	}
	
	function Del(){
		global $intro;
		global $advid,$sess_admin;
			$adm=$sess_admin['adminid'];
			$app=$this->appname;
			policy($adm,$app.".php",'del');
			
		$sql = $intro->db->query("SELECT * FROM ".PREFIX."_adv where advid='$advid'");
		$row = $intro->db->fetch_assoc($sql);
		@extract($row);
			
		$sql = $intro->db->query("DELETE FROM ".PREFIX."_adv WHERE advid='$advid' ");
		
		@unlink("../../".$advfile);
		
		revalidateNext('adv');
		$intro->redirect($this->appname);
	}
	
	############################################################################
	# CAT
	############################################################################

	function Cat(){
		global $intro;

		$this->nav();

		echo "
		<div class=\"card border-info\">
			  <div class=\"card-header text-dark bg-info\">
				<i class=\"{$btn['legend_icon']}\"></i> ".$intro->lang["cat_edit"]." 
			  </div>
			  <div class=\"card-body\">
			  <form method=\"POST\" enctype=\"multipart/form-data\" action=\"{$this->base}/Cat\">
			  <table class=\"table table-sm \">
					 <tbody>
		
		<tr>
			<th align=center>".form_select("catid",0,"adv_cat")."</th>
		</tr>
		<tr>
		   <td align=center>
				 <input type=\"hidden\" name=\"app_action\" value=\"FormCat\">
				 <input type=\"hidden\" name=\"app_name\" value=\"{$this->appname}\">
					   <input type=\"hidden\" name=\"t\" value=\"edit\">
					   <input type=\"submit\" value=\" ".$intro->lang["edit"]." \" class='btn btn-primary' name=\"B1\">
		   </td>
		</tr>
		</tbody>
		</table>
		</form></div>
		</div>";

		$result = $intro->db->query("SELECT * from ".PREFIX."_adv_cat where father='0' order by w asc");

		echo "
		<div class=\"card border-info\">
			  <div class=\"card-header text-dark bg-info\">
				<i class=\"{$btn['legend_icon']}\"></i> ".$intro->lang["cat_cur"]." 
			  </div>
			  <div class=\"card-body\">
			   <div class=\"table-responsive\">
			 <table class=\"table table-striped table-sm table-hover\">
					  <thead  class=\"table-dark\">
						<tr>
							<th><b>".$intro->lang["id"]."</b></th>
							<th><b>".$intro->lang["cat_name"]."</b></th>
							<th><b>".$intro->lang["cat_image"]."</b></th>
							<th><b>".$intro->lang["cat_view_type"]."</b></th>
							<th><b>".$intro->lang["cat_info"]."</b></th>
							<th><b>".$intro->lang["options"]."</b></th>
						</tr>
					  </thead>
					  <tbody>";

		$i=0;
		while($myrow = $intro->db->fetch_assoc($result))
		{
			@extract($myrow);
			$i++;
			echo "
			<tr class=\""._odd_even($i)."\">
				<td class=\"center\">$catid</td>
				<td class=\"center\">$catname</td>
				<td class=\"center\">$catimage</td>
				<td class=\"center\">$catimage</td>
		
				<td class=\"center\">$catinfo</td>
			
				<td class=\"center\"> 
					<a class=\"btn btn-info p_edit\" href=\"{$this->base}/FormCat?t=edit&amp;catid=$catid\" title=\"".$intro->lang["edit"]."\"><i class=\"fa-solid fa-pen-to-square\"></i></a>
					<a class=\"btn btn-danger p_del intro_ui_del\" href=\"{$this->base}/DelCat?catid=$catid\" OnClick=\"return false;\" title=\"".$intro->lang["del"]."\"><i class=\"fa-solid fa-trash\"></i></a>
				</td>
				</tr>";
		}
		echo "</tbody>			 
		</table></div>
		</div>
		</div>
		</form>";

	}
	function doArrange(){
		 global  $intro;

		 $all_id = $_POST['all_id'];
		 $all_w = $_POST['all_w'];
		 
		 for ($i=0; $i<count($all_id); $i++){
		 $sql =  $intro->db->query("UPDATE  ".PREFIX."_adv_cat SET  w='$all_w[$i]' where catid='$all_id[$i]'");
		 }
		 revalidateNext('adv');
		 $intro->redirect($this->appname,"Cat");
	}
	
	############################################################################

	function FormCat($catid=0,$t='add'){
			global $intro,$error;
			global $father,$catname,$catimage,$catinfo,$catorder,$cat_status,$adds_rand,$adds_num,$cat_width,$cat_height,$cols,$rows;
			
			$this->nav();
			
			if($_GET) @extract($_GET);
			if($error || $_POST) @extract($_POST);
			
			$catid = intval($catid);
			$t = stripslashes($t);

			if($t == "edit"){
			   $sql = $intro->db->query("SELECT * FROM ".PREFIX."_adv_cat where catid='$catid'");
			   $row = $intro->db->fetch_assoc($sql);
			   @extract($row);

			   $info_text = $intro->lang["cat_edit_cat_id"]." : $catid</b>";
			   $info_icon = "$this->img_path/icons/edit_24.png";
			   $action = "doEditCat";
			   $btn_submit = $intro->lang["save_changes"];
			}
			elseif($t == "add"){
				 $info_text = $intro->lang["cat_add_new"];
				 $info_icon = "$this->img_path/icons/add_24.png";
				 $action = "doAddCat";
				 $btn_submit = $intro->lang["add_new"];
			}

	echo "
	<div class=\"card border-info\">
			  <div class=\"card-header text-dark bg-info\">
				<i class=\"px-2 fa-solid fa-download\"></i>$info_text
			  </div>
			  <div class=\"card-body\">
		
		<form method=\"POST\" name=\"form_add\"  action=\"{$this->base}/$action\" enctype=\"multipart/form-data\">
	
		 <table class='table table-sm'  id=\"table1\">
			<tbody>
			<tr>
			   <th style='width:200px;'>".$intro->lang["cat_main"]."</th>
			   <td>".form_select("father",$father,"adv_cat")." ".@$error[catid]."</td>
		   </tr>
			<tr>
				 <td>".$intro->lang["cat_name"]." : </td>
				 <td><input type=\"text\" name=\"catname\" value=\"$catname\" size=\"70\"> ".@$error[catname]."</td>
			</tr>
			
			<tr>
				 <td>".$intro->lang["cat_cat_status"]." : </td>
				 <td>".form_option('cat_status',$cat_status)."".@$error[cat_status]."</td>
			</tr>
			<tr>
				 <td>".$intro->lang["adv_adds_rand"]." : </td>
				 <td>".form_option('adds_rand',$adds_rand)." ".@$error[adds_rand]."</td>
			</tr>
			<tr>
				 <td>".$intro->lang["adv_cat_adds_num"]." : </td>
				 <td><input type=\"text\" name=\"adds_num\" value=\"$adds_num\" size=\"10\"> ".@$error[adds_num]."</td>
			</tr>
			<tr>
				 <td>".$intro->lang["adv_cat_width"]." : </td>
				 <td><input type=\"text\" name=\"cat_width\" value=\"$cat_width\" size=\"10\"> ".@$error[cat_width]."</td>
			</tr>
			<tr>
				 <td>".$intro->lang["adv_cat_height"]." : </td>
				 <td><input type=\"text\" name=\"cat_height\" value=\"$cat_height\" size=\"10\"> ".@$error[cat_height]."</td>
			</tr>
			<tr>
				 <td>".$intro->lang["adv_cat_cols"]." : </td>
				 <td><input type=\"text\" name=\"cols\" value=\"$cols\" size=\"10\"> ".@$error[cols]."</td>
			</tr>
			<tr>
				 <td>".$intro->lang["adv_cat_rows"]." : </td>
				 <td><input type=\"text\" name=\"rows\" value=\"$rows\" size=\"10\"> ".@$error[rows]."</td>
			</tr>
			<tr>
				 <td>".$intro->lang["cat_info"]." : </td>
				 <td> <textarea name=\"catinfo\" cols=\"60\" rows=\"5\">$catinfo</textarea> ".@$intro->lang["optional"]."</td>
			</tr>

		<tr>
			<td class=\"center\" colspan=\"2\">

				<input type=\"hidden\" name=\"app_action\"  value=\"$action\">
				<input type=\"hidden\" name=\"app_name\"  value=\"$this->appname\">
				<input type=\"hidden\" name=\"catid\"  value=\"$catid\">
				<input type=\"submit\" class='btn btn-primary' value=\" $btn_submit \" name=\"B1\">

			</td>
		</tr>
		</tbody>
		</table>
		</form>
		</div>
		</div>
		<br/>";

	}

	function doAddCat(){
		global $intro,$t,$error;

		$data["catname"] = trim($_POST['catname']);

		$data["catinfo"] = $_POST['catinfo'];
		$data["father"] = $_POST['father'];		
		$data["adds_rand"] = $_POST['adds_rand'];
		$data["adds_num"] = $_POST['adds_num'];
		$data["cat_status"] = $_POST['cat_status'];
		$data["rows"] = $_POST['rows'];
		$data["cols"] = $_POST['cols'];
		$data["cat_height"] = $_POST['cat_height'];
		$data["cat_width"] = $_POST['cat_width'];
		
		if($data["catname"] == "")
		{
			$error['catname'] = "<span class='error'>".$intro->lang["required"]."</span>";
			$this->FormCat(0,"add");
			die();
		}

		$intro->db->insert(PREFIX."_adv_cat", $data);

		revalidateNext('adv');
		$intro->redirect($this->appname,"Cat");
	}
	
	function doEditCat(){
		global $intro,$t,$error;

		$father = intval($_POST['father']);
		$catid = intval($_POST['catid']);
		
		if($catid == $father)
		{
			$error['catid'] = "<span class=error>".$intro->lang["cat_error_duplicate"]."</span>";
			$this->FormCat($catid,"edit");
			die();
		}
		
		$data["catname"] = $_POST['catname'];
		//$data["catimage"] = $_POST['catimage'];
		$data["catinfo"] = $_POST['catinfo'];
		$data["father"] = $father;
		//$data["catorder"] = $_POST['catorder'];
		$data["adds_rand"] = $_POST['adds_rand'];
		$data["adds_num"] = $_POST['adds_num'];
		$data["rows"] = $_POST['rows'];
		$data["cols"] = $_POST['cols'];
		$data["cat_status"] = $_POST['cat_status'];
		$data["cat_height"] = $_POST['cat_height'];
		$data["cat_width"] = $_POST['cat_width'];

		$intro->db->update(PREFIX."_adv_cat", $data, "catid='$catid'");

		revalidateNext('adv');
		$intro->redirect($this->appname,"Cat");
	}


	function DelCat(){
		global $intro,$catid;

		$sql = $intro->db->query("DELETE FROM ".PREFIX."_adv_cat WHERE catid='$catid' ");

		revalidateNext('adv');
		$intro->redirect($this->appname,"Cat");

	}

}//end class Adv
?>