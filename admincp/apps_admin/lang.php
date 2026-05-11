<?PHP
##############################################
#	Intro Technologies LTD.
#	Code By Mohammed AbuAbed - Intro.ps 
#	Email: info@intro.ps 
#	Support: http://intro.ps/support.php
#   Date: 2013-02-03 Time: 13:31:38
#	AppName: lang
##############################################


class Lang_AppAdmin extends Intro_AppsAdmin{

	var $appname = null;
	var $base = null;
	var $img_path;
	var $filter_form;
	var $filter_qry;
	var $filter_pages;
	
	function __construct($appname,$base,$img_path="")
	{
		$this->appname = $appname;
		$this->base = $base;
		$this->img_path = $img_path;
	}

	function nav(){
			 global $intro;

		
			echo "<ul class=\"nav justify-content-center mb-2\">
				  <li class=\"nav-item mx-1\">
					<a class=\"btn btn-"._css_active("index")." px-1\" href=\"{$this->base}/index\">
					<i class=\"px-1 fa-solid fa-language\"></i> ".$intro->lang["lang_appname"]." </a>
				  </li>
				  <li class=\"nav-item mx-1\">
					<a class=\"btn btn-"._css_active("Form")." px-1 p_add\" href=\"{$this->base}/Form?t=add\">
					<i class=\"px-1 fa-solid fa-plus\"></i> ".$intro->lang["lang_add"]."</a>
				  </li>
				  <li class=\"nav-item mx-1\">
					<a class=\"btn btn-"._css_active("Cat")." px-1 p_add\" href=\"{$this->base}/Cat\">
					<i class=\"px-1 fa-solid fa-language\"></i> ".$intro->lang["cats"]."</a>
				  </li>
				  <li class=\"nav-item mx-1\">
					<a class=\"btn btn-"._css_active("FormCat")." px-1 p_add\" href=\"{$this->base}/FormCat?t=add\">
					<i class=\"px-1 fa-solid fa-plus\"></i> ".$intro->lang["cats_add_new"]."</a>
				  </li>
				   <li class=\"nav-item mx-1\">
					<a class=\"btn btn-"._css_active("Export")." px-1 p_add\" href=\"{$this->base}/Export?NH=1\">
					<i class=\"px-1 fa-solid fa-language\"></i> ".$intro->lang["export"]."</a>
				  </li>
				   <li class=\"nav-item mx-1\">
					<a class=\"btn btn-"._css_active("Import")." px-1 p_add\" href=\"{$this->base}/Import?\">
					<i class=\"px-1 fa-solid fa-upload\"></i> ".$intro->lang["import"]."</a>
				  </li>
				  
				   <li class=\"nav-item mx-1\">
					<a class=\"btn btn-"._css_active("Compare")." px-1 p_add\" href=\"{$this->base}/Compare?\">
					<i class=\"px-1 fa-solid fa-upload\"></i> Compare</a>
				  </li>
			</ul>";
	}

	function _filter($cols){
		global $intro;
		
		$result = $intro->maa->filter($this->appname, $cols);
		
		$this->filter_qry = $result['where'];
		$this->filter_form = $result['form'];
		$this->filter_pages = $result['filter_pages'];
	}
	function testt(){
		global $intro;
		
		$data = array();
		
		$val = $_POST['update_value'];
		$id = $_POST['element_id'];
		$name = $_POST['element_name'];
		$original_value = $_POST['original_value'];
		$original_html = $_POST['original_html'];
	
		$data["text"] = $val;
		$intro->db->update(PREFIX."_lang",$data,"lid='$id'");
		echo $data["text"];
	
	}
	function Compare(){
		global $intro,$active,$page,$search_txt;

		

		$this->nav();
		
		$catid = intval($intro->input->get_post('catid'));
		$catid2 = intval($intro->input->get_post('catid2'));


		$result = $intro->db->query("SELECT * from ".PREFIX."_lang where catid=$catid order by type asc ");
		$totrows = $intro->db->returned_rows;		
           
		echo "
		<div class=\"card border-info\">
			  <div class=\"card-header text-dark bg-info\">
				<i class=\"px-2 fa-solid fa-download\"></i> ".$intro->lang["lang_cur"]." ($totrows)
			  </div>
			  <div class=\"card-body\">
			  <ul>
				<li><a href=\"$this->base/Compare?catid=1&amp;catid2=2\">Arabic to English</a> </li>
				<li><a href=\"$this->base/Compare?catid=2&amp;catid2=1\">English to Arabic</a> </li>
			</ul>
			<form action=\"$this->base/index\" method=\"post\" name=\"fieldsForm\"  id=\"fieldsForm\">
			 <div class=\"table-responsive\">
			<table class=\"table table-striped  table-sm  table-hover data ajax\" id=\"table_results\">
             <thead  class=\"table-dark\">
					  
			<tr>
				<th> </th>
				<th> ID </th>
				<th> Lang </th>
				<th> Type </th>
				<th> Var </th>
				<th> Text </th>
				
				<th>  </th>
				<th> ID </th>
				<th> Lang </th>
				<th> Type </th>
				<th> Var </th>
				<th> Text </th>
				<th>".$intro->lang["options"]."</th>
			</tr>
			</thead>
			<tbody>";
		$i=0;
		while($myrow = $intro->db->fetch_assoc($result)){
			@extract($myrow);
			$i++;
			$sql_en = $intro->db->query("SELECT type,lid,text,varname from ".PREFIX."_lang where type='$type' and varname='$varname' and catid=$catid2;");
			$row_en = $intro->db->fetch_assoc($sql_en);
			
			echo "
			<tr >
				<td class=\"data inline_edit  $BG center\"><input type=\"checkbox\" id=\"checkbox_row_".$lid."\" value=\"$lid\" name=\"selected_fld[]\"></td>
				<td class=\"center\">$lid</td>
				<td>".(($catid == 1) ? 'ar' : 'en')."</td>
				<td>$type</td>
				<td>$varname</td>				
				<td style='border-left: 2px solid #000;'>$text</td>
				
				<td class=\"data inline_edit  $BG center\"><input type=\"checkbox\" id=\"checkbox_row_{$row_en['lid']}\" value=\"{$row_en['lid']}\" name=\"selected_fld[]\"></td>
				<td>{$row_en['lid']}</td>
				<td>".(($catid2 == 1) ? 'ar' : 'en')."</td>
				<td>{$row_en['type']}</td>
				<td>{$row_en['varname']}</td>
				<td>{$row_en['text']}</td>
				<td class=\"center\"> </td>
			</tr>";
		}
		echo "</tbody>
			</table></div>";

		echo"</div></div>";
	}
	
	function index(){
		global $intro,$active,$page,$search_txt;

		

		$this->nav();
		
		var_dump($_POST);
		$order = $intro->input->get_post('order');
		$page = $intro->input->get_post('page');
		$stext = trim($intro->input->get_post('stext'));
		$svarname = trim($intro->input->get_post('svarname'));
		$stype = trim($intro->input->get_post('stype'));
		$scatid = intval($intro->input->get_post('scatid'));
		if($stext !=''){
			$qry.="and text like '%$stext%' ";
		}
		if($svarname !=''){
			$qry.="and varname like '%$svarname%'  ";
		}
		if($stype !=''){
			$qry.="and type like '%$stype%'  ";
		}
		
		if($scatid !=0){
			$qry.="and catid ='$scatid'  ";
		}
		
		
		if ($order=="") $order="lid:desc";
		$order = str_replace(":", " " , $order);

		$rows_per_page = "50";
		if ($page=="") $page=1;
		$nexlimit = $page * $rows_per_page - $rows_per_page;

		$result = $intro->db->query("SELECT * from ".PREFIX."_lang where true $qry order by $order  limit $nexlimit,$rows_per_page");
		$totrows = $intro->db->returned_rows;
		
		$sql_all_rows = $intro->db->query("SELECT lid from ".PREFIX."_lang where true $qry ");	
		$totalrows = $intro->db->returned_rows;

		?>
		<script>
		$(document).ready(function(){
			$('.intro_slide_down').click(function(event)
			{
				var link = $(this).attr('href');
				var container = $('#intro_result');
				$('#loading').show();
				event.preventDefault();
				container.load(link +'&NH=1&NN=1', function(data) {
				$('#loading').hide();	
				}).slideToggle();
				//container.slideToggle();		
				
				return false;
			});
			
			$(".editable2").editInPlace({
				/*callback: function(unused, enteredText) { return enteredText; },*/
				url: "<?=$this->base?>/testt?NH=1",
				bg_over: "#cff",
				field_type: "textarea",
				textarea_rows: "2",
				textarea_cols: "35",
				saving_image: "<?=$this->img_path?>/ajax-loader.gif"
			}); 
		});
		</script>
		<?php
		echo "<div id=\"intro_result\" ></div>";
		$order = str_replace(" ", ":" , $order);
		
		echo "<div class=\"card my-3 \">
				  <div class=\"card-header  mb-3\">
					<i class=\"px-2 fa-solid fa-search\"></i> Search Form
				  </div>
				  <div class=\"card-body nopadding\">
					<form class=\"row nopadding\" action=\"{$this->base}/index\" method=\"post\">
					<div class=\"col-auto\">
					<input placeholder=\"Text\" type=\"text\" name=\"stext\" value=\"$stext\" class='form-control mb-1' > 
					</div>
					<div class=\"col-auto\">
					<input placeholder=\"Varname\" type=\"text\" name=\"svarname\" value=\"$svarname\" class='form-control mb-1'> 
					</div>
					<div class=\"col-auto\">
					<input placeholder=\"Type\" type=\"text\" name=\"stype\" value=\"$stype\" class='form-control mb-1'>
					</div>
					<div class=\"col-auto\">
					".form_select("scatid",$scatid,"lang_cat")."
					
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
				<i class=\"px-2 fa-solid fa-download\"></i> ".$intro->lang["lang_cur"]." ($totalrows)
			  </div>
			  <div class=\"card-body\">
				<table class=\"table table-striped table-sm table-hover \" id=\"table_results\" >
					  <thead  class=\"table-dark\">
						<tr>
							<th><input type=\"checkbox\" id=\"parent\" /> </th>
							<th >ID "._sort_th("lid","index")."</th>
							<th >".$intro->lang["lang_catid"]." "._sort_th("catid","index", $this->filter_pages)." </th>
							<th >".$intro->lang["lang_varname"]." "._sort_th("varname","index", $this->filter_pages)." </th>
							<th >".$intro->lang["lang_type"]." "._sort_th("type","index", $this->filter_pages)." </th>
							<th >".$intro->lang["lang_text"]." "._sort_th("text","index", $this->filter_pages)." </th>
							
							<th> AR </th>
							<th> Code </th>
							<th>".$intro->lang["options"]."</th>
						</tr>
					  </thead>
					  <tbody>";
					  $i=0;
		while($myrow = $intro->db->fetch_assoc($result)){
			@extract($myrow);
			
			$sql_ar = $intro->db->query("SELECT lid,text from ".PREFIX."_lang where type='$type' and varname='$varname' and catid=1 ");
			$row_ar = $intro->db->fetch_assoc($sql_ar);
			
			echo "
			<tr >
				<td class=\"data  center child\"><input type=\"checkbox\" id=\"checkbox_row_".$lid."\" value=\"$lid\" name=\"selected_fld[]\"></td>
				<td class=\"center\">$lid</td>
				<td>".(($catid == 1) ? 'ar' : 'en')."</td>
				<td>$varname</td>
				<td>$type</td>
				<td id='$lid' name='text' >$text</td>
				<td  name='text'>{$row_ar['text']}</td>
				<td dir=ltr>";echo '{$intro->lang[\''.$varname.'\']}'; echo"</td>
				
				<td class=\"center\">  ".isset($active_link)."
					<a href=\"{$this->base}/Form?t=edit&lid=$lid\" class='btn btn-info p_edit btn-sm' title=\"".$intro->lang["edit"]."\"> <i class=\"fa-solid fa-pen-to-square\"></i></a>
					<a class=\"btn btn-danger p_edit btn-sm intro_ui_del\" href=\"{$this->base}/Del?lid=$lid\" OnClick=\"return false;\" title=\"".$intro->lang["del"]."\"><i class=\"fa-solid fa-trash\"></i></a>
				</td>
			</tr>";
		}
		echo "</tbody>
			</table>";
		$order = str_replace(" ", ":" , $order);
		
		
		echo "<center class='pagination'>".pagination3("{$this->base}/index?order=$order".$this->filter_pages, $totalrows, $rows_per_page, $page)."</center>";
		
		echo"</div></div>";
		
	}
	function Export(){
		global $intro;

		$sql_all_rows = $intro->db->query("SELECT * from ".PREFIX."_lang ");	
		$all_rows = array();
		while($myrow = $intro->db->fetch_assoc($sql_all_rows))
		{
			$all_rows[] = $myrow;
		}
		
		header('Content-Type: application/json; charset=utf-8');
		header('Content-Disposition: attachment; filename="'.$this->appname.'_'.date('Y-m-d_H:i:s').'.json"');
		echo json_encode($all_rows);
	}	
	function multiDel(){
		global $intro,$t,$error;

		$selected_fld = $_POST['selected_fld'];
		$count = count($selected_fld);
		
		for ($i=0; $i<$count; $i++) {

			$sql = $intro->db->query("DELETE FROM ".PREFIX."_lang WHERE lid='{$selected_fld[$i]}' ");
		}

		$intro->redirect($this->appname);
	}

	function Form($t="add"){
		global $intro,$t,$error;
		global $lid,$catid,$varname,$type,$text,$text_en;
		$btn=array();
		
		if($_GET) @extract($_GET);
		if($error || $_POST) @extract($_POST);
		
		$lid = intval($lid);
		$t = intro_html_decode($t);

		if(isset($_REQUEST['NN']) !=1)
		$this->nav();
		
		if($t == "edit"){
			$sql = $intro->db->query("SELECT * FROM ".PREFIX."_lang where lid='$lid'");
			$row = $intro->db->fetch_assoc($sql);
			@extract($row);
			
			
			$btn['legend_name'] = $intro->lang["lang_edit"]."<b>$lid</b>";
			$btn['legend_img'] = "fa-solid fa-edit";
			$btn['name'] = $intro->lang["save_changes"];
			$btn['img'] = "fa-solid fa-edit";		   
			$btn['action'] = "doEdit";
			$btn['copy'] = "<button class=\"mult_submit\" type=\"submit\" name=\"app_action\" value=\"doAdd\" title=\"add new\">
						<span><img src=\"$this->img_path/icons/add_16.png\" title=\"add new\" alt=\"add new\" /> حفظ كسجل جديد</span>
					</button>";
			$textarea="<tr>
				<td>".$intro->lang["lang_text"]." :  <span style='color:#ff0000'>*</span></td>
				<td><textarea placeholder=\"{$intro->lang['ar']}\" name=\"text\" cols=\"50\" rows=\"3\">$text</textarea>
				$error[text]</td>
			</tr>";
			$cats="<tr>
				<td>".$intro->lang["lang_catid"]." :  <span style='color:#ff0000'>*</span></td>
				<td>".form_select("catid",$catid,"lang_cat")." $error[catid]</td>
			</tr>";
		}
		elseif($t == "add"){
		$cats="";

			$btn['legend_name'] = $intro->lang["lang_add"];
			$btn['legend_img'] = "fa-solid fa-plus";
			$btn['name'] = $intro->lang["add_new"];
			$btn['img'] = "fa-solid fa-plus";		   
			$btn['action'] = "doAdd";
			$btn['copy'] = "";
			$textarea="<tr>
				<td>".$intro->lang["lang_text"]." :  <span style='color:#ff0000'>*</span></td>
				<td><textarea placeholder=\"{$intro->lang['ar']}\" name=\"text\" cols=\"50\" rows=\"3\">$text</textarea>
				<textarea dir=\"ltr\" placeholder=\"English\" name=\"text_en\" cols=\"50\" rows=\"3\">$text_en</textarea>$error[text]</td>
			</tr>";
		}else{
			die("Error: what to do? add or edit?");	
		}		
		
		
		echo "
		<div class=\"card border-info\">
			  <div class=\"card-header text-dark bg-info\">
				<i class=\"{$btn['legend_img']}\"></i> {$btn['legend_name']}  
			  </div>
			  <div class=\"card-body\">
			 <form method=\"POST\" name=\"form_add\" id=\"form_add\"  action=\"{$this->base}/{$btn['action']}\" enctype=\"multipart/form-data\">
			  <table class=\"table table-sm \"  id=\"table1\">
					 <tbody>
					 
			$cats
			<tr>
				<td>".$intro->lang["lang_varname"]." :  <span style='color:#ff0000'>*</span></td>
				<td><input dir=\"ltr\" type=\"text\" name=\"varname\" value=\"$varname\" class='form-control'> $error[varname]</td>
			</tr>
			<tr>
				<td>".$intro->lang["lang_type"]." : </td>
				<td><input dir=\"ltr\" type=\"text\" name=\"type\" value=\"$type\" class='form-control'> $error[type]</td>
			</tr>
			$textarea
			<tr>
				<td class=\"center\" colspan=\"2\">
					<input type=\"hidden\" name=\"app_name\"  value=\"{$this->appname}\">
					<input type=\"hidden\" name=\"t\"  value=\"$t\">
					<input type=\"hidden\" name=\"lid\"  value=\"$lid\">
					<button class=\"btn btn-primary\" type=\"submit\" name=\"app_action\" value=\"{$btn['action']}\" title=\"{$btn['name']}\">
					<i class='{$btn['legend_img']}'></i> {$btn['name']} 
					</button>
					
				</td>
			</tr>
			</tbody>
			</table>
			</form>
			</div>
			</div>
			<br/>";
	}

	############################################################################

	function doAdd(){
			global $intro,$t,$error;
			global $catid,$varname,$type,$text ;

			
			
		//	$catid = intval($_POST['catid']);
			$varname = trim($_POST['varname']);
			$type = trim($_POST['type']);
			$text = trim($_POST['text']);
			$text_en = trim($_POST['text_en']);
			

			if($varname == ""){ $error['varname'] = "<span class=error>".$intro->lang["required"]."</span>"; }
			if($text == ""){ $error['text'] = "<span class=error>".$intro->lang["required"]."</span>"; }
			if($text_en == ""){ $error['text_en'] = "<span class=error>".$intro->lang["required"]."</span>"; }
			
			//if($catid == 0){ $error['catid'] = "<span class=error>".$intro->lang["required"]."</span>"; }
				
	 
			 
			if($error){
			$this->Form("add");
				die();
			}
			
			$data["catid"] = 1;
			$data["varname"] = $varname;
			$data["type"] = $type;
			$data["text"] = intro_html_encode($text);
			 
			$intro->db->insert(PREFIX."_lang",$data);
			
			if($text_en != ''){
			
				$data2["catid"] = 2;
				$data2["varname"] = $varname;
				$data2["type"] = $type;
				$data2["text"] = intro_html_encode($text_en);
			 
				$intro->db->insert(PREFIX."_lang",$data2);
			}
			
			$data["success"] = 1;
			$data["lid"] = $intro->db->insert_id();
			
			$data["code"] = '{$intro->lang[\''.$varname.'\']}';
	
			$intro->redirect($this->appname);
	}

	############################################################################

	function doEdit(){
		global $intro,$lid;
		global $catid,$varname,$type,$text ;

			extract($_POST);
			
			$data["catid"] = $catid;
			$data["varname"] = $varname;
			$data["type"] = $type;
			$data["text"] = intro_html_encode($text);

		//$sql = $intro->db->query("UPDATE ".PREFIX."_lang SET catid='$catid',varname='$varname',type='$type',text='$text' WHERE lid='$lid' ");
		$intro->db->update(PREFIX."_lang",$data,"lid='$lid'");

		$intro->redirect($this->appname);
	}
	
	function Del(){
		global $intro;
		global $lid;

		$sql = $intro->db->query("DELETE FROM ".PREFIX."_lang WHERE lid='$lid' ");

		$intro->redirect($this->appname);
	}
	
	############################################################################
	# CAT
	############################################################################

	function Cat(){
		global $intro;

		$this->nav();

		echo "
		<fieldset>
			<legend>
				<img src=\"$this->img_path/icons/edit_24.png\" align=\"absmiddle\">".$intro->lang["cat_edit"]."
			</legend>
		
		<form method=\"POST\" enctype=\"multipart/form-data\" action=\"{$this->base}/Cat\">
		<table align=center width=500  border=1>
		<tr>
			<th align=center>".form_select("catid",0,"lang_cat")."</th>
		</tr>
		<tr>
		   <td align=center>
				<input type=\"hidden\" name=\"app_action\" value=\"FormCat\">
				<input type=\"hidden\" name=\"app_name\" value=\"{$this->appname}\">
				<input type=\"hidden\" name=\"t\" value=\"edit\">
				<input type=\"submit\" value=\" ".$intro->lang["edit"]." \" name=\"B1\">
		   </td>
		</tr>
		</table>
		</form>
		
		</fieldset>";

		$result = $intro->db->query("SELECT * from ".PREFIX."_lang_cat where father='0' order by w asc");
		echo "
		<div class=\"card border-info\">
			  <div class=\"card-header text-dark bg-info\">
				<i class=\"px-2 fa-solid fa-language\"></i> ".$intro->lang["cat_cur"]." ($totalrows)
			  </div>
			  <div class=\"card-body\">
				<table class=\"table table-striped table-sm table-hover\">
					  <thead  class=\"table-dark\">
						<tr>
							<th><b>".$intro->lang["id"]."</b></th>
							<th><b>".$intro->lang["cat_name"]."</b></th>
							<th><b>".$intro->lang["cat_image"]."</b></th>
							<th><b>".$intro->lang["cat_order"]."</b></th>
							<th><b>".$intro->lang["cat_view_type"]."</b></th>
							<th><b>".$intro->lang["cat_info"]."</b></th>
							<th><b>".$intro->lang["options"]."</b></th>
						</tr>
					  </thead>
					  <tbody>";
		while($myrow = $intro->db->fetch_assoc($result))
		{
			@extract($myrow);
			
			if($catimage != ""){
				$catimage = "<img src=\"../$catimage\" width=\"70\" height=\"70\" alt=\"\" />";
			}
			echo "
			<tr>
				<td class=\"center\">$catid</td>
				<td class=\"center\">$catname</td>
				<td class=\"center\">$catimage</td>
				<td class=\"center\"><input type=\"hidden\" name=\"all_id[]\" value=\"$catid\" size=\"3\"><input type=\"text\" name=\"all_w[]\" value=\"$w\" size=\"3\"></td>
				<td class=\"center\">{$intro->ar->catorder($catorder)}</td>
				<td class=\"center\">$catinfo</td>
				<td class=\"center\">
					<a class=\"btn btn-info btn-sm \" href=\"{$this->base}/FormCat?t=edit&amp;catid=$catid\"><i class=\" fa-solid fa-pen-to-square\"></i></a>
					<a class=\"btn btn-danger btn-sm intro_ui_del\" href=\"{$this->base}/DelCat?catid=$catid\" OnClick=\"return false;\"><i class=\" fa-solid fa-trash\"></i></a>
				</td>
			</tr>";
		}
		echo "
		<tr>
			<td></td>
			<td><input type=\"hidden\" name=\"app_name\" value=\"{$this->appname}\"></td>
			<td class=\"center\"><input type=\"hidden\" name=\"app_action\" value=\"doArrange\"></td>
			<td class=\"center\"><input type=\"submit\" class='btn btn-primary' value=\"".$intro->lang["btn_arrange"]."\" name=\"save\"></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
				 
		  </tbody>
		</table>
		</div>
		</div>
		</form>";

	}
	function doArrange(){
		 global  $intro;

		 $all_id = $_POST['all_id'];
		 $all_w = $_POST['all_w'];
		 
		 for ($i=0; $i<count($all_id); $i++){
		 $sql =  $intro->db->query("UPDATE  ".PREFIX."_lang_cat SET  w='$all_w[$i]' where catid='$all_id[$i]'");
		 }
		 $intro->redirect($this->appname,"Cat");
	}
	
	############################################################################

	function FormCat($catid=0,$t='add'){
			global $intro,$error;
			
			$this->nav();
			
			if($_GET) @extract($_GET);
			if($error || $_POST) @extract($_POST);
			
			$catid = intval($catid);
			$t = intro_html_decode($t);

			if($t == "edit"){
			   $sql = $intro->db->query("SELECT * FROM ".PREFIX."_lang_cat where catid='$catid'");
			   $row = @$intro->db->fetch_assoc($sql);
			   @extract($row);

			   $info_text = $intro->lang["cat_edit_cat_id"]." : $catid</b>";
			   $info_icon = "$this->img_path/icons/edit_24.png";
			   $action = "doEditCat";
			   $btn_submit = $intro->lang["save_changes"];
			}
			elseif($t == "add"){
				 $info_text = $intro->lang["cat_add_new"];
				 $info_icon = "fa-solid fa-plus";
				 $action = "doAddCat";
				 $btn_submit = $intro->lang["add_new"];
			}

	echo "
	<div class=\"card my-3 \">
				  <div class=\"card-header  mb-3\">
					<i class=\"$info_icon\"></i> $info_text
				  </div>
				  <div class=\"card-body nopadding\">
				<form method=\"POST\" name=\"form_add\"  action=\"{$this->base}/$action\" enctype=\"multipart/form-data\">
				<table  id=\"table1\" class=\"table table-sm \">
					 <tbody>

			<tr>
			   <th>".$intro->lang["cat_main"]."</th>
			   <td>".form_select("father",$father,"lang_cat")." $error[catid]</td>
		   </tr>
			<tr>
				 <td>".$intro->lang["cat_name"]." : </td>
				 <td><input type=\"text\" name=\"catname\" value=\"$catname\" class='form-control'> $error[catname]</td>
			</tr>
			<tr>
				 <td>".$intro->lang["cat_image"]." : </td>
				 <td><input dir=ltr type=\"text\" name=\"catimage\" value=\"$catimage\" class='form-control' > ".$intro->lang["optional"]." [<a href=\"javascript:popimg('upload.php?f=form_add.catimage&p=lang');\">".$intro->lang["file_bring"]."</a> ]</td>
			</tr>
			<tr>
			   <th>".$intro->lang["cat_view_type"]." :</th>
			   <td>".form_select_array("catorder",$intro->ar->catorder(),$catorder)." $error[catorder]</td>
		   </tr>
			<tr>
				 <td>".$intro->lang["cat_info"]." : </td>
				 <td> <textarea name=\"catinfo\" class='form-control' >$catinfo</textarea> ".$intro->lang["optional"]."</td>
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
		$data["catimage"] = trim($_POST['catimage']);
		$data["catinfo"] = $_POST['catinfo'];
		$data["father"] = $_POST['father'];
		$data["catorder"] = $_POST['catorder'];
		
		if($data["catname"] == "")
		{
			$error['catname'] = "<span class='error'>".$intro->lang["required"]."</span>";
			FormCat(0,"add");
			die();
		}

		$intro->db->insert(PREFIX."_lang_cat", $data);

		$intro->redirect($this->appname,"Cat");
	}
	
	function doEditCat(){
		global $intro,$t,$error;

		$father = intval($_POST['father']);
		$catid = intval($_POST['catid']);
		
		if($catid == $father)
		{
			$error['catid'] = "<span class=error>".$intro->lang["cat_error_duplicate"]."</span>";
			FormCat($catid,"edit");
			die();
		}
		
		$data["catname"] = $_POST['catname'];
		$data["catimage"] = $_POST['catimage'];
		$data["catinfo"] = $_POST['catinfo'];
		$data["father"] = $father;
		$data["catorder"] = $_POST['catorder'];

		$intro->db->update(PREFIX."_lang_cat", $data, "catid='$catid'");

		$intro->redirect($this->appname,"Cat");
	}


	function DelCat(){
		global $intro,$catid;

		$sql = $intro->db->query("DELETE FROM ".PREFIX."_lang_cat WHERE catid='$catid' ");

		$intro->redirect($this->appname,"Cat");

	}
	
	############################################################################

	function Active(){
		global $intro,$lid;

		$sql = $intro->db->query("UPDATE ".PREFIX."_lang SET status='1' WHERE lid='$lid' ");

		$intro->redirect($this->appname);

	}
		############################################################################

	function Import(){
			global $intro,$error;
			
			if($_REQUEST['NN'] !=1)
			$this->nav();
			
		echo "
		<div class=\"card border-info\">
			  <div class=\"card-header text-dark bg-info\">
				{$intro->lang['import']}
			  </div>
			  <div class=\"card-body\">
			  <form method=\"POST\" id=\"form_add\" name=\"form_add\"  action=\"{$this->base}/doImport\" enctype=\"multipart/form-data\">
		 <table class='table table-sm' >
			<tbody>
			<tr>
			   <td>".$intro->lang["upload"]."</td>
			   <td><input type=\"file\" name=\"file\" id=\"file\" /></td>
		   </tr>
		   
		<tr>
			<td class=\"center\" colspan=\"2\">
				<input type=\"submit\" value=\" {$intro->lang['import']} \" name=\"B1\">

			</td>
		</tr>
		</tbody>
		</table>
		</form>
		</div>
		</div>";

	}

	function doImport(){
		global $intro,$t,$error;

		if ($_FILES['file']['error'] == UPLOAD_ERR_OK && is_uploaded_file($_FILES['file']['tmp_name'])) 
		{ 
		  $data = file_get_contents($_FILES['file']['tmp_name']); 
		}else{
			die("Faild Uploading");
		}
		
		$data = json_decode($data, true);
		foreach($data as $db_data){
		
			$intro->db->insert(PREFIX."_lang",$db_data, true);//true = ignore duplicates
		}
		

		$this->nav();
		
		echo "Import Done";
		
	}
}//end class Lang
?>