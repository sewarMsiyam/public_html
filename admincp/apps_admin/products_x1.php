<?PHP
##############################################
#	Intro Technologies LTD.
#	Code By Mohammed AbuAbed - Intro.ps 
#	Email: info@intro.ps 
#	Support: http://intro.ps/support.php
#   Date: 2015-06-24 Time: 10:44:58
#	AppName: products
##############################################

class Products_AppAdmin extends Intro_AppsAdmin{

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
		
		echo "<div class=\"app_nav\">
		<a class=\"btn btn-"._css_active("index")."\" href=\"{$this->base}/index\"><icon class=\"icon-list\"> ".$intro->lang["products_appname"]."</icon></a> 
		<a class=\"btn btn-"._css_active("Form")." p_add\" href=\"{$this->base}/Form?t=add\"><icon class=\"icon-plus-squared\"> ".$intro->lang["products_add"]."</icon></a>           
		<a class=\"btn btn-"._css_active("Cat")."\" href=\"{$this->base}/Cat\"><icon class=\"icon-th\"> {$intro->lang["cats"]}</icon></a> 
		<a class=\"btn btn-"._css_active("FormCat")." p_add\" href=\"{$this->base}/FormCat?t=add\"><icon class=\"icon-plus-circled\"> {$intro->lang["cats_add_new"]}</icon></a>		 		 
		<a class=\"btn btn-"._css_active("FormCat")." p_add\" href=\"{$this->base}/discountall\"><icon class=\"icon-plus-circled\"> {$intro->lang["products_discountall"]}</icon></a>		 		 
		</div>";
	}

	
	function index(){
		global $intro,$array;

		$qry = "";
		$page = intval( $intro->input->get_post("page") );
		$order = trim( $intro->input->get_post("order") );
		$search_txt = trim( $intro->input->get_post("search_txt") );

		$this->nav();

		if($search_txt !=""){
			$qry = " where name_ar  LIKE '%$search_txt%' OR name_en  LIKE '%$search_txt%'";
		}
		
		if ($order=="") $order="id:desc";
		$order = str_replace(":", " " , $order);

		$rows_per_page = 30;
		if ($page==0) $page=1;
		$nexlimit = ($page*$rows_per_page)-$rows_per_page;

		$result = $intro->db->query("SELECT *,(select catname_en from ".PREFIX."_products_cat where catid=prod.catid) as catname from ".PREFIX."_products prod $qry order by $order  limit $nexlimit,$rows_per_page");
		$totrows = $intro->db->returned_rows;
		
		$sql_all_rows = $intro->db->query("SELECT id from ".PREFIX."_products $qry ");	
		$totalrows = $intro->db->returned_rows;

		echo "
		<fieldset><legend><i class=\"icon-search\"></i> ".$intro->lang["search"]."</legend>
		<form action=\"\" method=\"post\">
		".$intro->lang["search_form"].": <input type=\"text\" name=\"search_txt\" value=\"$search_txt\" size=\"10\">
		<input type=\"hidden\" name=\"maa\" value=\"Main\">
		<input name=\"name\" value=\"".$intro->lang["search"]."\" type=\"submit\">
		</form></fieldset>";
		?>
         <script>
$(document).ready(function()
{
$('.discountadd').click(function() {

		$.ajax({
			type: "POST",url: "<?=$intro->base_url?>admincp/index.php/products/active_discount_all?NH=1",data: "d",
			success: function(result){
				$("#discount_result").html(result);
			}
		});

	});
	
	
	$('.undiscountall').click(function() {

		$.ajax({
			type: "POST",url: "<?=$intro->base_url?>admincp/index.php/products/unactive_discount_all?NH=1",data: "d",
			success: function(result){
				$("#discount_result").html(result);
			}
		});

	});
	
});
</script>	
<?	 
		echo "
		<fieldset><legend><i class=\"icon-list\"></i> ".$intro->lang["products_cur"]." ($totalrows)</legend>
		<div class='discountadd' >{$intro->lang["active_discount_all"]}</div>
		<div class='undiscountall'>{$intro->lang["unactive_discout_all"]}</div>
		<div class='clear'></div>
		<div id='discount_result'></div>
		
		<table class=\"DataTable table-striped table-bordered\" id=\"table_products\">
        <thead>
	    <tr>
			
			<th>ID "._sort_th("id","index")."</th>
			<th>".$intro->lang["products_catid"]." "._sort_th("catid","index")." </th>
			<th>".$intro->lang["products_photo"]." "._sort_th("photo","index")." </th>
			<th>Status "._sort_th("status","index")." </th>
			<th>".$intro->lang["products_name_ar"]." "._sort_th("name_ar","index")." </th>
			<th>Code "._sort_th("code","index")." </th>
			
			<th>".$intro->lang["products_price"]." "._sort_th("price","index")." </th>
			<th>DIS </th>
			<th>DIS% </th>
			<th>After Dis. "._sort_th("net_price","index")." </th>
			<th>".$intro->lang["options"]."</th>
	    </tr>
		</thead>
		
		<tbody>";
		$i=0;
		while($myrow = $intro->db->fetch_assoc($result)){
			@extract($myrow);			
			$i++;
			
			$data = array();
			$data["net_price"] = dicount($price,$discount, $discount_percent);
			$intro->db->update(PREFIX."_products",$data,"id=$id");
			
			echo "
			<tr class=\""._odd_even($i)."\">
				
				<td class=\"center\">$id</td>
				<td>$catname</td>
				<td><img src=\"{$intro->base_url}uploads/news/{$photo}\" style=\"max-height:50px;\" alt=\"\" /></td>
				<td>$status</td>
				<td>$name_ar<br/>$name_en</td>
				<td>$code</td>
				<td class='c'>$price</td>
				<td class='c'>$discount</td>
				<td class='c'>$discount_percent%</td>
				<td class='c'>$net_price</td>
				<td class=\"center\"> 
					<a class=\"btn btn-info btn-xs p_edit\" href=\"{$this->base}/Form?t=edit&amp;id=$id\" title=\"".$intro->lang["edit"]."\"><i class=\"icon-edit\"></i></a>
					<a class=\"btn btn-danger btn-xs p_del intro_ui_del\" href=\"{$this->base}/Del?id=$id\" OnClick=\"return false;\" title=\"".$intro->lang["del"]."\"><i class=\"icon-cancel-circled2\"></i></a>
				</td>
			</tr>";
		}
		echo "</tbody>
			</table>";
		$order = str_replace(" ", ":" , $order);
		
		echo "<center>".pagination3("{$this->base}/index?search_txt=$search_txt&amp;order=$order", $totalrows, $rows_per_page, $page)."</center>";
		echo"</fieldset>";
	}
	function multiDel(){
		global $intro,$error,$sess_admin;

		$selected_fld = $_POST['selected_fld'];
		$count = count($selected_fld);
		
		policy($sess_admin['adminid'],$this->appname.".php" , "del");
		
		for ($i=0; $i<$count; $i++) {

			$sql = $intro->db->query("DELETE FROM ".PREFIX."_products WHERE id='{$selected_fld[$i]}' ");
		}

		$intro->redirect($this->appname);
	}
	function discountall(){
		global $intro,$error,$sess_admin,$array;
		global $catid,$name_ar,$name_en,$place,$date_add,$photo,$price,$discount,$discount_percent,$can_discount,$details_ar,$details_en;
		
		if($error || $_POST != null) @extract($_POST);
		$IF = intval( $intro->input->get_post("IF") );		

		
		if($IF != 1)
		$this->nav();
		
			
			$btn['legend_name'] = $intro->lang["products_discountall"];
			$btn['legend_icon'] = "icon-plus-squared";
			$btn['name'] = $intro->lang["discount"];
			$btn['img_icon'] = "icon-plus-squared";		   
			$btn['action'] = "dodiscount";
	echo "
		<div class=\"forms\">	
			<fieldset>
				<legend>
					<i class=\"{$btn['legend_icon']}\"></i> {$btn['legend_name']} 
				</legend>
				<h3>تنويه : هذه الشاشة سوف تقوم بتغيير قيم الخصم ونسبة الخصم لجميع المنتجات المسموح عمل لها خصم جماعي</h3>
			<form method=\"POST\" name=\"form_add\"  action=\"{$this->base}/{$btn['action']}\" enctype=\"multipart/form-data\">
			<table cellspacing=\"2\" style=\"margin:auto;width:95%\">
			
			<tr>
				<td>".$intro->lang["products_discount"]." : </td>
				<td><input  type=\"text\" name=\"discount\" value=\"0\" size=\"10\"> 
				(خصم مبلغ صافي) {$this->error('discount')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["products_discount_percent"]." : </td>
				<td><input  type=\"text\" name=\"discount_percent\" value=\"0\" size=\"10\"> 
				(خصم نسبة مئوية من المبلغ) {$this->error('discount_percent')}</td>
			</tr>
			
			<tr>
				<td class=\"center\" colspan=\"2\">
					<input type=\"hidden\" name=\"app_name\"  value=\"{$this->appname}\">
					<input type=\"hidden\" name=\"IF\"  value=\"$IF\">
					<button type=\"submit\" name=\"app_action\" value=\"{$btn['action']}\"><i class=\"{$btn['img_icon']}\"> {$btn['name']} </i></button>
				
				</td>
			</tr>
			</table>
			</form>
			</fieldset>
		</div>";
	}
	function dodiscount(){
		global $intro,$array;
		
		$sql = $intro->db->query("SELECT * FROM ".PREFIX."_products where can_discount=1 order by id desc");
		while($row = $intro->db->fetch_assoc($sql)){
			$price = $row['price'];
			$id = $row['id'];
			
			$data["discount"] = $intro->input->post('discount');
			$data["discount_percent"] = $intro->input->post('discount_percent');
			
			$data["net_price"] = dicount($price,$data["discount"], $data["discount_percent"]); 
		
			$intro->db->update(PREFIX."_products",$data,"id=$id");
			
		
		}
		
		$intro->redirect($this->appname);
	}
/**************/
	function active_discount_all(){
		global $intro,$error,$sess_admin,$array;
		
		$sql = $intro->db->query("SELECT * FROM ".PREFIX."_products order by id desc");
		while($row = $intro->db->fetch_assoc($sql)){
			$price = $row['price'];
			$id = $row['id'];
			
			$data["can_discount"] =1;
		
			$intro->db->update(PREFIX."_products",$data,"id=$id");
		
		}
		echo "تم تفعيل الخصم الجماعي ";
	
	}
	
	function unactive_discount_all(){
		global $intro,$error,$sess_admin,$array;
		
		$sql = $intro->db->query("SELECT * FROM ".PREFIX."_products order by id desc");
		while($row = $intro->db->fetch_assoc($sql)){
			$price = $row['price'];
			$id = $row['id'];
			
			$data["can_discount"] =0;
		
			$intro->db->update(PREFIX."_products",$data,"id=$id");
		
		}
		echo "تم تعطيل الخصم الجماعي ";
	
	}
	function Form($t=""){
		global $intro,$error,$sess_admin,$array;
		global $catid,$name_ar,$title2_en,$status,$code_ar,$img_ar,$title2_ar,$name_en,$place,$date_add,$photo,$price,$discount,$discount_percent,$can_discount,$details_ar,$details_en;
		global $code;
		
		if($error || $_POST != null) @extract($_POST);
		$IF = intval( $intro->input->get_post("IF") );		
		$id = intval( $intro->input->get_post("id") );		
		$t = $t==""?$intro->input->get_post("t"):$t;
		
		if($IF != 1)
		$this->nav();
		
		if($t == "edit")
		{			
			policy($sess_admin['adminid'],$this->appname.".php" , "edit");
			
			$sql = $intro->db->query("SELECT * FROM ".PREFIX."_products where id='$id'");
			$row = $intro->db->fetch_assoc($sql);
			@extract($row);

			$btn['legend_name'] = $intro->lang["products_edit"]." <b>$id</b>";
			$btn['legend_icon'] = "icon-edit";
			$btn['name'] = $intro->lang["save_changes"];
			$btn['img_icon'] = "icon-floppy";		   
			$btn['action'] = "doEdit";
		}
		elseif($t == "add")
		{
			policy($sess_admin['adminid'],$this->appname.".php" , "add");
			
			$btn['legend_name'] = $intro->lang["products_add"];
			$btn['legend_icon'] = "icon-plus-squared";
			$btn['name'] = $intro->lang["save"];
			$btn['img_icon'] = "icon-plus-squared";		   
			$btn['action'] = "doAdd";
			
		}		
			
	echo "
		<div class=\"forms\">	
			<fieldset>
				<legend>
					<i class=\"{$btn['legend_icon']}\"></i> {$btn['legend_name']} 
				</legend>

			<form method=\"POST\" name=\"form_add\"  action=\"{$this->base}/{$btn['action']}\" enctype=\"multipart/form-data\">
			<table cellspacing=\"2\" style=\"margin:auto;width:95%\">
			<tr>
				<td>".$intro->lang["products_catid"]." :  <span style='color:#ff0000'>*</span></td>
				<td>".form_select("catid",$catid,"products_cat")." {$this->error('catid')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["products_name_ar"]." :  <span style='color:#ff0000'>*</span></td>
				<td><input  type=\"text\" name=\"name_ar\" value=\"$name_ar\" size=\"50\"> {$this->error('name_ar')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["products_name_en"]." : </td>
				<td><input class='ltr' type=\"text\" name=\"name_en\" value=\"$name_en\" size=\"50\"> {$this->error('name_en')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["products_photo"]." :  <span style='color:#ff0000'>*</span></td>
				<td><input type=\"text\" dir=ltr name=\"photo\" value=\"$photo\" id=\"$photo\" size=\"30\"> 
				   <span id=\"preview_photo\"></span><img src=\"{$intro->base_url}uploads/news/$photo\" width=\"100\" height=\"100\" style=\"float:left;\" alt=\"\" />
				   <a class=\"btn btn-info icon-upload\" OnClick=\"javascript:popup('".admin_path."images.php?for_id=photo');\">".$intro->lang["file_bring"]."</a> 
				   {$this->error('photo')}
				</td>
			</tr>	
			<tr>
				<td>".$intro->lang["products_photo_ar"]." :  <span style='color:#ff0000'>*</span></td>
				<td><input type=\"text\" dir=ltr name=\"img_ar\" value=\"$img_ar\" id=\"$img_ar\" size=\"30\"> 
				   <span id=\"preview_img_ar\"></span><img src=\"{$intro->base_url}uploads/news/$img_ar\" width=\"100\" height=\"100\" style=\"float:left;\" alt=\"\" />
				   <a class=\"btn btn-info icon-upload\" OnClick=\"javascript:popup('".admin_path."images.php?for_id=img_ar');\">".$intro->lang["file_bring"]."</a> 
				   {$this->error('img_ar')}
				</td>
			</tr>
			<tr>
				<td>Code :  <span style='color:#ff0000'>*</span></td>
				<td><input  type=\"text\" name=\"code\" value=\"$code\" size=\"10\"> {$this->error('code')}</td>
			</tr>
			
				<tr>
				<td>Code Arabic:  <span style='color:#ff0000'>*</span></td>
				<td><input  type=\"text\" name=\"code_ar\" value=\"$code_ar\" size=\"10\"> {$this->error('code_ar')}</td>
			</tr>
			
			
			<tr>
				<td>Place :</td>
				<td>".form_select_array("place",$array['place'],$place , "None")."</td>
			</tr>
			<tr>
				<td>".$intro->lang["products_price"]." :  <span style='color:#ff0000'>*</span></td>
				<td><input  type=\"text\" name=\"price\" value=\"$price\" size=\"10\"> {$this->error('price')}</td>
			</tr>
			
			<tr>
				<td>".$intro->lang["products_discount"]." : </td>
				<td><input  type=\"text\" name=\"discount\" value=\"$discount\" size=\"10\"> 
				(خصم مبلغ صافي) {$this->error('discount')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["products_discount_percent"]." : </td>
				<td><input  type=\"text\" name=\"discount_percent\" value=\"$discount_percent\" size=\"10\"> 
				(خصم نسبة مئوية من المبلغ) {$this->error('discount_percent')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["products_can_discount"]." : </td>
				<td><input type=\"checkbox\" name=\"can_discount\" value=\"1\" ".($can_discount==1?"checked=\"checked\"":"")." /> 
				(اذا تم وضع اشارة صح سيتم تطبيق الخصم على هذا المنتج في حالة عمل خصم جماعي.) {$this->error('can_discount')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["products_title2_ar"]." :  <span style='color:#ff0000'>*</span></td>
				<td><textarea name=\"title2_ar\" class='rtl' style=\"width:80%;height:50px;\">$title2_ar</textarea></td>
			</tr>
			<tr>
				<td>".$intro->lang["products_title2_en"]." :  <span style='color:#ff0000'>*</span></td>
				<td><textarea name=\"title2_en\" class='ltr' style=\"width:80%;height:50px;\">$title2_en</textarea></td>
			</tr>
			<tr>
				<td>".$intro->lang["products_status"]." :  <span style='color:#ff0000'>*</span></td>
				<td>".form_option('status',$status)."</td>
			</tr>
			
			
			<tr>
				<td>".$intro->lang["products_details_ar"]." : </td>
				<td>
				"; fck_editor("details_ar",intro_html_decode($details_ar) ); echo "
				
				 {$this->error('details_ar')}</td>
			</tr>
			
			<tr>
				<td>".$intro->lang["products_details_en"]." : </td>
				<td>
				"; fck_editor("details_en",intro_html_decode($details_en)); echo " 
				
				{$this->error('details_en')}</td>
			</tr>
			<tr>
				<td class=\"center\" colspan=\"2\">
					<input type=\"hidden\" name=\"app_name\"  value=\"{$this->appname}\">
					<input type=\"hidden\" name=\"t\"  value=\"$t\">
					<input type=\"hidden\" name=\"id\"  value=\"$id\">
					<input type=\"hidden\" name=\"IF\"  value=\"$IF\">
					<button type=\"submit\" name=\"app_action\" value=\"{$btn['action']}\"><i class=\"{$btn['img_icon']}\"> {$btn['name']} </i></button>
				
				</td>
			</tr>
			</table>
			</form>
			</fieldset>
		</div>";
	}

	############################################################################

	function doAdd(){
		global $intro,$error;
		$catid = intval( $intro->input->post('catid') );
		$name_ar = trim( $intro->input->post('name_ar') );
		$name_en = trim( $intro->input->post('name_en') );
		$date_add = trim( $intro->input->post('date_add') );
		$photo = trim( $intro->input->post('photo') );
		$price = floatval( $intro->input->post('price') );
		$discount = floatval( $intro->input->post('discount') );
		$discount_percent = floatval( $intro->input->post('discount_percent') );
		$can_discount = intval( $intro->input->post('can_discount') );
		$details_ar = trim( $intro->input->post('details_ar') );
		$title2_en = trim( $intro->input->post('title2_en') );
		$title2_ar = trim( $intro->input->post('title2_ar') );
		$code = trim( $intro->input->post('code') );
		$status = trim( $intro->input->post('status') );
		
		if($name_ar == "" || $photo == "" ||  $catid == 0){

			if($name_ar == ""){ $error['name_ar'] = "<span class=error>".$intro->lang["required"]."</span>"; }
				if($photo == ""){ $error['photo'] = "<span class=error>".$intro->lang["required"]."</span>"; }
				
			if($catid == 0){ $error['catid'] = "<span class=error>".$intro->lang["required"]."</span>"; }
				
			
			$this->Form("add");
			die();
		}		
		
		$data["catid"] = $intro->input->post('catid');
		$data["name_ar"] = $intro->input->post('name_ar');
		$data["name_en"] = $intro->input->post('name_en');
		$data["date_add"] = date("Y-m-d H:i:s");
		$data["photo"] = $intro->input->post('photo');
		$data["price"] = $intro->input->post('price');
		$data["discount"] = $intro->input->post('discount');
		$data["discount_percent"] = $intro->input->post('discount_percent');
		$data["net_price"] = dicount($data["price"],$data["discount"], $data["discount_percent"]);
		$data["can_discount"] = $intro->input->post('can_discount');
		$data["details_ar"] = $intro->input->post('details_ar');
		$data["details_en"] = $intro->input->post('details_en');
		$data["place"] = intval($intro->input->post('place'));
		$data["title2_en"] = $intro->input->post('title2_en');
		$data["title2_ar"] = $intro->input->post('title2_ar');
		
		$data["code"] = $code;
		$data["code_ar"] = $intro->input->post('code_ar');
		$data["img_ar"] = $intro->input->post('img_ar');
		$data["status"] = $intro->input->post('status');
				 
		$intro->db->insert(PREFIX."_products",$data);
		
		//if($intro->input->post('IF') == 1) die("<script>parent.location.reload(true);parent.$.fancybox.close();</script>");
		
		$intro->redirect($this->appname);
	}

	############################################################################

	
	function doEdit(){
		global $intro,$array;
			
		
		$data["catid"] = $intro->input->post('catid');
		$data["name_ar"] = $intro->input->post('name_ar');
		$data["name_en"] = $intro->input->post('name_en');
		$data["date_add"] = $intro->input->post('date_add');
		$data["photo"] = $intro->input->post('photo');
		$data["price"] = $intro->input->post('price');
		$data["discount"] = $intro->input->post('discount');
		$data["discount_percent"] = $intro->input->post('discount_percent');
		$data["net_price"] = dicount($data["price"],$data["discount"], $data["discount_percent"]);
		$data["can_discount"] = $intro->input->post('can_discount');
		$data["details_ar"] = $intro->input->post('details_ar');
		$data["details_en"] = $intro->input->post('details_en');
		$data["place"] = intval($intro->input->post('place'));
		$data["title2_en"] = $intro->input->post('title2_en');
		$data["title2_ar"] = $intro->input->post('title2_ar');
		$data["code"] = trim($intro->input->post('code'));
		$data["code_ar"] = $intro->input->post('code_ar');
		$data["img_ar"] = $intro->input->post('img_ar');
		$data["status"] = $intro->input->post('status');
		$id = intval( $intro->input->post('id') );

		$intro->db->update(PREFIX."_products",$data,"id=$id");
		
		//if($intro->input->post('IF') == 1) die("<script>parent.$.fancybox.close();</script>");
		
		$intro->redirect($this->appname);
	}
	
	function Del(){
		global $intro,$sess_admin,$array;
		
		$id = intval( $intro->input->get_post('id') );
		
		policy($sess_admin['adminid'],$this->appname.".php" , "del");

		$sql = $intro->db->query("DELETE FROM ".PREFIX."_products WHERE id=$id ");

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

		if($search_txt !=""){
			$qry = " where catname_ar  LIKE '%$search_txt%' ";
		}
		
		if ($order=="") $order="catid:desc";
		$order = str_replace(":", " " , $order);

		$rows_per_page = 30;
		if ($page==0) $page=1;
		$nexlimit = ($page*$rows_per_page)-$rows_per_page;

		$result = $intro->db->query("SELECT * from ".PREFIX."_products_cat $qry order by $order  limit $nexlimit,$rows_per_page");
		$totrows = $intro->db->returned_rows;
		
		$sql_all_rows = $intro->db->query("SELECT catid from ".PREFIX."_products_cat $qry ");	
		$totalrows = $intro->db->returned_rows;

		echo "
		<fieldset><legend><i class=\"icon-search\"></i> ".$intro->lang["search"]."</legend>
		<form action=\"$this->base/Cat\" method=\"post\">
		".$intro->lang["search_form"].": <input type=\"text\" name=\"search_txt\" value=\"$search_txt\" size=\"10\">
		<input name=\"name\" value=\"".$intro->lang["search"]."\" type=\"submit\">
		</form></fieldset>";
           
		echo "
		<fieldset><legend><i class=\"icon-list\"></i> ".$intro->lang["products_cat_cur"]." ($totalrows)</legend>
		
		<table class=\"DataTable table-striped table-bordered\" id=\"table_products_cat\">
        <thead>
	    <tr>
			<th> </th>
			<th>ID "._sort_th("catid","index")."</th>
			<th>".$intro->lang["products_cat_catname_ar"]." "._sort_th("catname_ar","Cat")." </th>
			<th>".$intro->lang["products_cat_catname_en"]." "._sort_th("catname_en","Cat")." </th>
			<th>".$intro->lang["products_cat_catimage"]." "._sort_th("catimage","Cat")." </th>
			<th>".$intro->lang["options"]."</th>
	    </tr>
		</thead>
		
		<tbody>";
		$i=0;
		while($myrow = $intro->db->fetch_assoc($result)){
			@extract($myrow);			
			$i++;
			
			echo "
			<tr class=\""._odd_even($i)."\">
				<td class=\"center\"><input type=\"checkbox\" value=\"$catid\" name=\"selected_fld[]\"></td>
				<td class=\"center\">$catid</td>
				<td>$catname_ar</td>
				<td>$catname_en</td>
				<td><img src=\"{$intro->base_url}$catimage\" style=\"max-height:50px;\" alt=\"\" /></td>
				<td class=\"center\"> 
					<a class=\"btn btn-info p_edit\" href=\"{$this->base}/FormCat?t=edit&amp;catid=$catid\" title=\"".$intro->lang["edit"]."\"><i class=\"icon-edit\"></i></a>
					<a class=\"btn btn-danger p_del intro_ui_del\" href=\"{$this->base}/DelCat?catid=$catid\" OnClick=\"return false;\" title=\"".$intro->lang["del"]."\"><i class=\"icon-cancel-circled2\"></i></a>
				</td>
			</tr>";
		}
		echo "</tbody>
			</table>";
		$order = str_replace(" ", ":" , $order);
		
		echo "<center>".pagination3("{$this->base}/Cat?search_txt=$search_txt&amp;order=$order", $totalrows, $rows_per_page, $page)."</center>";
		echo"</fieldset>";
	}	
	
	function FormCat($t=""){
		global $intro,$error,$sess_admin,$array;
		global $catname_ar,$catname_en,$catimage;
		
		if($error || $_POST != null) @extract($_POST);
		$catid = intval( $intro->input->get_post("catid") );		
		$t = $t==""?$intro->input->get_post("t"):$t;
		
		$this->nav();
		
		if($t == "edit")
		{			
			policy($sess_admin['adminid'],$this->appname.".php" , "edit");
			
			$sql = $intro->db->query("SELECT * FROM ".PREFIX."_products_cat where catid='$catid'");
			$row = $intro->db->fetch_assoc($sql);
			@extract($row);

			$btn['legend_name'] = $intro->lang["products_cat_edit"]." <b>$catid</b>";
			$btn['legend_icon'] = "icon-edit";
			$btn['name'] = $intro->lang["save_changes"];
			$btn['img_icon'] = "icon-floppy";		   
			$btn['action'] = "doEditCat";
		}
		elseif($t == "add")
		{
			policy($sess_admin['adminid'],$this->appname.".php" , "add");
			
			$btn['legend_name'] = $intro->lang["products_cat_add"];
			$btn['legend_icon'] = "icon-plus-squared";
			$btn['name'] = $intro->lang["save"];
			$btn['img_icon'] = "icon-plus-squared";		   
			$btn['action'] = "doAddCat";
		}		
			
	echo "
		<div class=\"forms\">	
			<fieldset>
				<legend>
					<i class=\"{$btn['legend_icon']}\"></i> {$btn['legend_name']} 
				</legend>

			<form method=\"POST\" name=\"form_add\"  action=\"{$this->base}/{$btn['action']}\" enctype=\"multipart/form-data\">
			<table cellspacing=\"2\" style=\"margin:auto;width:95%\">
			<tr>
				<td>".$intro->lang["products_cat_catname_ar"]." :  <span style='color:#ff0000'>*</span></td>
				<td><input  type=\"text\" name=\"catname_ar\" value=\"$catname_ar\" size=\"40\"> {$this->error('catname_ar')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["products_cat_catname_en"]." :  <span style='color:#ff0000'>*</span></td>
				<td><input  type=\"text\" name=\"catname_en\" value=\"$catname_en\" size=\"40\"> {$this->error('catname_en')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["products_cat_catimage"]." : </td>
				<td><input type=\"text\" dir=ltr name=\"catimage\" value=\"$catimage\" id=\"$catimage\" size=\"30\"> 
				   <span id=\"preview_catimage\"></span><img src=\"{$intro->base_url}$catimage\" width=\"100\" height=\"100\" style=\"float:left;\" alt=\"\" />
				   <a class=\"btn btn-info icon-upload\" OnClick=\"javascript:popup('".admin_path."images.php?for_id=catimage');\">".$intro->lang["file_bring"]."</a> 
				   {$this->error('catimage')}
				</td>
			</tr>
			<tr>
				<td class=\"center\" colspan=\"2\">
					<input type=\"hidden\" name=\"app_name\"  value=\"{$this->appname}\">
					<input type=\"hidden\" name=\"t\"  value=\"$t\">
					<input type=\"hidden\" name=\"catid\"  value=\"$catid\">
					<button type=\"submit\" name=\"app_action\" value=\"{$btn['action']}\"><i class=\"{$btn['img_icon']}\"> {$btn['name']} </i></button>
					
				</td>
			</tr>
			</table>
			</form>
			</fieldset>
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
				 
		$intro->db->insert(PREFIX."_products_cat",$data);
		
		//if($intro->input->post('IF') == 1) die("<script>parent.location.reload(true);parent.$.fancybox.close();</script>");
		
		$intro->redirect($this->appname , "Cat");
	}

	############################################################################

	function doEditCat(){
		global $intro,$array;
			
		
		$data["catname_ar"] = $intro->input->post('catname_ar');
		$data["catname_en"] = $intro->input->post('catname_en');
		$data["catimage"] = $intro->input->post('catimage');
		
		
		$catid = intval( $intro->input->post('catid') );

		$intro->db->update(PREFIX."_products_cat",$data,"catid=$catid");
		
		//if($intro->input->post('IF') == 1) die("<script>parent.$.fancybox.close();</script>");
		
		$intro->redirect($this->appname , "Cat");
	}
	
	function DelCat(){
		global $intro,$sess_admin,$array;
		
		$catid = intval( $intro->input->get_post('catid') );
		
		policy($sess_admin['adminid'],$this->appname.".php" , "del");

		$sql = $intro->db->query("DELETE FROM ".PREFIX."_products_cat WHERE catid=$catid ");

		$intro->redirect($this->appname , "Cat");
	}
		
}//end class Products
?>