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
		
	
		echo "<ul class=\"nav justify-content-center mb-2\">
				  <li class=\"nav-item mx-2\">
					<a class=\"btn btn-"._css_active("index")."  px-3\" href=\"{$this->base}/index\">
					<i class=\"px-2 fa-solid fa-box-open\"></i> ".$intro->lang["products_appname"]." </a>
				  </li>
				  <li class=\"nav-item mx-2\">
					<a class=\"btn btn-"._css_active("Form")." px-3 p_add\" href=\"{$this->base}/Form?t=add\">
					<i class=\"px-2 fa-solid fa-plus\"></i> ".$intro->lang["products_add"]."</a>
				  </li>
				   <li class=\"nav-item mx-2\">
					<a class=\"btn btn-"._css_active("Cat")." px-3 p_add\" href=\"{$this->base}/Cat\">
					<i class=\"px-2 fa-solid fa-folder\"></i> ".$intro->lang["cats"]."</a>
				  </li>
				   <li class=\"nav-item mx-2\">
					<a class=\"btn btn-"._css_active("FormCat")." px-3 p_add\" href=\"{$this->base}/FormCat?t=add\">
					<i class=\"px-2 fa-solid fa-folder\"></i> ".$intro->lang["cats_add_new"]."</a>
				  </li>
				   <li class=\"nav-item mx-2\">
					<a class=\"btn btn-"._css_active("discountall")." px-3 p_add\" href=\"{$intro->base_url}admincp/index.php/discount/index\">
					<i class=\"px-2 fa-solid fa-square-minus\"></i> ".$intro->lang["products_discountall"]."</a>
				  </li>
				   <li class=\"nav-item mx-2\">
					<a class=\"btn btn-"._css_active("Generate")." px-3 p_add\" href=\"{$this->base}/Generate\">
					<i class=\"px-2 fa-solid fa-recycle\"></i>".$intro->lang["products_gen_titles"]."</a>
				  </li>
				  
				   <li class=\"nav-item mx-2\">
					<a class=\"btn btn-"._css_active("Generate")." px-3 p_add\" href=\"{$this->base}/ExportToExcel?NH=1\">
					<i class=\"px-2 fa-solid fa-recycle\"></i>".$intro->lang["exporttoexcel"]."</a>
				  </li>
				  
				  
				</ul>";
	}

	function Generate(){
		global $intro,$error,$sess_admin,$array;
		
		
		$this->nav();
		echo "
			<div class=\"card border-info\">
			  <div class=\"card-header text-dark bg-info\">
				<i class=\"fa-solid fa-plus\"></i> Generate Titles 
			  </div>
			  <div class=\"card-body\">
			 <table class=\"table table-sm \">
			<tbody>
			<tr>
					<th>Arabic عناوين عربية</th>
					<th>English Titles</th>
				</tr>
				<tr>
					<td>
						<a class=\"btn btn-primary extraTitles\" data-for='ar' href=\"javascript:void(0);\" \">توليد عناوين عربية بشكل تلقائي</a>
						<textarea name=\"extra_titles_ar\" class='rtl' style=\"width:50%;height:400px;font-size:14px;\"></textarea>
					</td>
					<td>
						<a class=\"btn btn-info extraTitles\" data-for='en' href=\"javascript:void(0);\" \">Generate Auto Titles</a>
						<textarea name=\"extra_titles_en\" class='ltr' style=\"width:50%;height:400px;font-size:14px;\"></textarea>
					</td>
				</tr>
				</tbody>
			</table></div></div>";
			
	
	}
	
	function auto()
	{	
		global $intro,$page;
		
		$lang=$intro->maa->lang;	
		
		$query = trim($intro->input->get_post('query'));
		$term = trim($intro->input->get_post('term'));

		$name = 'name_'.$lang;
		$x=$intro->uri->segments[3];
			
			$sql2 = $intro->db->query("SELECT * FROM ".PREFIX."_products where status=1 and $name LIKE '%$term%' 
			or details_ar  LIKE '%$term%' or details_en  LIKE '%$term%'
			or code  LIKE '%$term%' or code_ar  LIKE '%$term%' GROUP BY $name
			order by $name asc;");
	
				
				
			$totrows = $intro->db->returned_rows;

			while($row2 =  $intro->db->fetch_assoc($sql2))
			{
				@extract($row2);
				$name = $row2['name_'.$lang];
				$link = $intro->uri_links("products","View",$row2['id'],'');
				
				$json[] = array("value" => $name , "label" => $name);
			}
		//	$data = array("suggestions" => $json);
		echo json_encode($json);
	
	
	}
	
	
	function ExportToExcel(){
    global $intro;

    $filename = "products_" . date('Y-m-d') . ".xls";

    header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Pragma: no-cache");
    header("Expires: 0");

    echo '<meta charset="UTF-8">';
    echo '<table border="1" style="border-collapse:collapse;">';
    echo '<tr>
            <th style="width:80px;">ID</th>
            <th style="width:500px;">Name (Arabic)</th>
            <th style="width:500px;">Name (English)</th>
          </tr>';

    $sql = $intro->db->query("SELECT id, name_ar, name_en FROM ".PREFIX."_products ORDER BY id DESC");
    while($row = $intro->db->fetch_assoc($sql)){
        echo '<tr>';
        echo '<td>'.$row['id'].'</td>';
        echo '<td>'.$row['name_ar'].'</td>';
        echo '<td>'.$row['name_en'].'</td>';
        echo '</tr>';
    }

    echo '</table>';
    exit;
}



	function index(){
		global $intro,$array;

		$qry = "";
		$page = intval( $intro->input->get_post("page") );
		$order = trim( $intro->input->get_post("order") );
		$search_txt = trim( $intro->input->get_post("search_txt") );
		$scatid = intval( $intro->input->get_post("scatid") );
		
	

		$this->nav();

		if($search_txt !=""){
		$qry.=" and  name_ar  LIKE '%$search_txt%' OR name_en  LIKE '%$search_txt%' "
				." or details_ar  LIKE '%$search_txt%' or details_en  LIKE '%$search_txt%'  "
				." or code  LIKE '%$search_txt%' or code_ar  LIKE '%$search_txt%'"
				." ";	
		}
		if($scatid !=0){$qry.="and catid='$scatid' ";	}
		if ($order=="") $order="id:desc";
		$order = str_replace(":", " " , $order);

		$rows_per_page = 30;
		if ($page==0) $page=1;
		$nexlimit = ($page*$rows_per_page)-$rows_per_page;

		$result = $intro->db->query("SELECT *,
		(select catname_en from ".PREFIX."_products_cat where catid=prod.catid) as catname 
		from ".PREFIX."_products prod 
		WHERE true $qry order by views desc , id desc  limit $nexlimit,$rows_per_page");
		$totrows = $intro->db->returned_rows;
		
		$sql_all_rows = $intro->db->query("SELECT id from ".PREFIX."_products WHERE true $qry ");	
		$totalrows = $intro->db->returned_rows;
		while($myrow = $intro->db->fetch_assoc($result)){
			@extract($myrow);			
			$i++;
			
			$data = array();
			$data["net_price"] = dicount($price,$discount, $discount_percent);
			$intro->db->update(PREFIX."_products",$data,"id=$id");
			
			$data2.= "
				<tr style=\"font-size:12px;\" >
				<td >$id</td>
				<td>$catname</td>
				<td><img src=\"{$intro->base_url}uploads/news/{$photo}\" style=\"max-height:30px;\" alt=\"\" /></td>
				<td>$status</td>
				<td>$name_ar<br/>$name_en</td>
				<td>$code</td>
				<td >$price</td>
				<td >$discount</td>
				<td >$discount_percent%</td>
				<td >$net_price</td>
				<td>$views</td>
				<td > 
					<a class=\"btn btn-info btn-sm  p_edit\" href=\"{$this->base}/Form?t=edit&amp;id=$id\" title=\"".$intro->lang["edit"]."\"><i class=\"fa-solid fa-pen-to-square\" style=\"font-size:10px;\"></i></a>
					<a class=\"btn btn-danger btn-sm p_del intro_ui_del\" href=\"{$this->base}/Del?id=$id\" OnClick=\"return false;\" title=\"".$intro->lang["del"]."\"><i class=\"fa-solid fa-trash\" style=\"font-size:10px;\"></i></a>
				</td>
			</tr>";
		}
		
	echo "<div class=\"card my-3 \">
			  <div class=\"card-header  mb-3\">
				<i class=\"px-2 fa-solid fa-search\"></i> Search Form
			  </div>
			  <div class=\"card-body nopadding\">
				<form class=\"row nopadding\"  action=\"\" method=\"post\">
				  <div class=\"col-md-7\">
					<input type=\"text\" class=\"form-control  \" name=\"search_txt\" id='autocomplete' value=\"$search_txt\" placeholder=\"Type Text\">
				  </div>
				  <div class=\"col-md-3\">
					".form_select("scatid",$scatid,"products_cat")."
				 </div>
				  <div class=\"col-md-2\">
				  <input type=\"hidden\" name=\"maa\" value=\"Main\">
					<button type=\"submit\" class=\"btn btn-primary mb-3\"> <i class=\"fa-solid fa-search fa-sm\"></i> Search</button>
				  </div>
				</form>
			  </div>
			</div>";
		?>

           <script>
			  $( function() {
				  $( "#autocomplete" ).autocomplete({
				
				   source: function( request, response ) {
					$.ajax( {
					  url: "https://buyformula.net/admincp/index.php/products/auto?NH=1",
					  dataType: "json",
					  data: {
						term: request.term
					  },
					  success: function( data ) {
						response( data );
						
					  }
					} ).fail(function() {
							alert(data );
						  });
				  },
				  minLength: 1,
				  select: function( event, ui ) {
				  }
				} );

			  } );
			  
			</script>
		
		<?	 
		echo "
		<div class=\"card border-info\">
			  <div class=\"card-header text-dark bg-info\">
				<i class=\"px-2 fa-solid fa-box-open\"></i> ".$intro->lang["products_cur"]." ($totalrows)
			  </div>
			  <div class=\"card-body\">
		
			 <div class=\"table-responsive\">
				<table class=\"table table-striped table-sm table-hover\" style=\"font-size:12px;\">
				<tr>
				    <td colspan=10></td>
				     <td><a  class=\"btn-warning \" href=\"{$this->base}/DelViews\" style=\"padding:2px;font-size:10px;\">Clear Views</a></td>
				      <td></td>
				</tr>
					  <thead  class=\"table-dark\">
						<tr>
							<th scope=\"col\">ID "._sort_th("id","index")."</th>
							<th scope=\"col\">".$intro->lang["products_catid"]." "._sort_th("catid","index")." </th>
							<th scope=\"col\">".$intro->lang["products_photo"]." "._sort_th("photo","index")." </th>
							<th scope=\"col\">Status "._sort_th("status","index")." </th>
							<th scope=\"col\">".$intro->lang["products_name_ar"]." "._sort_th("name_ar","index")." </th>
							<th scope=\"col\">Code "._sort_th("code","index")." </th>
							<th scope=\"col\">".$intro->lang["products_price"]." "._sort_th("price","index")." </th>
							<th scope=\"col\">DIS </th>
							<th scope=\"col\">DIS% </th>
							<th scope=\"col\" >After Dis. "._sort_th("net_price","index")." </th>
							<th scope=\"col\" >Views </th>
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
	

	


    
  
	function Form2(){
		global $intro,$error,$sess_admin,$array;
		
	
		$this->nav();
	echo "<form method=\"POST\"   action=\"www.buyformula.net/admincp/index.php/products/doaction\" >
			<table cellspacing=\"2\" style=\"margin:auto;width:95%\">
			<tr>
				<td>".$intro->lang["products_catid"]." :  <span style='color:#ff0000'>*</span></td>
				<td>".form_select("catid",5,"products_cat")." </td>
			</tr>
			<tr>
				<td>".$intro->lang["products_name_ar"]." :  <span style='color:#ff0000'>*</span></td>
				<td><input  type=\"text\" name=\"name_ar\" value=\"\" size=\"50\"> </td>
			</tr>
			<tr>
				<td  colspan=\"2\">
					<button type=\"submit\" name=\"app_action\" value=\"\"><i class=\"{$btn['img_icon']}\"> send </i></button>
				
				</td>
			</tr>
			</table>
		</div>
		</form>
		
			";
	
	}	
	function Form($t=""){
		global $intro,$error,$sess_admin,$array;
		global $catid,$name_ar,$title2_en,$status,$code_ar,$img_ar,$title2_ar,$name_en,$place,$date_add,$photo,$price,$discount,$discount_percent,$can_discount,$details_ar,$details_en;
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
			$sql = $intro->db->query("SELECT * FROM ".PREFIX."_products where id='$id'");
			$row = $intro->db->fetch_assoc($sql);
			@extract($row);

			$btn['legend_name'] = $intro->lang["products_edit"]." <b>$id</b>";
			$btn['legend_icon'] = "fa-solid fa-edit";
			$btn['name'] = $intro->lang["save_changes"];
			$btn['img_icon'] = "fa-solid fa-edit";		   
			$btn['action'] = "do_Edit";
		}
		elseif($t == "add")
		{
			policy($sess_admin['adminid'],$this->appname.".php" , "add");
			
			$btn['legend_name'] = $intro->lang["products_add"];
			$btn['legend_icon'] = "fa-solid fa-plus";
			$btn['name'] = $intro->lang["save"];
			$btn['img_icon'] = "fa-solid fa-plus";		   
			$btn['action'] = "doAdd";
			
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
				<td>عدد المشاهدات </td>
				<td>$views</td>
			</tr>
			<tr>
				<td>".$intro->lang["products_catid"]." :  <span style='color:#ff0000'>*</span></td>
				<td>".form_select("catid",$catid,"products_cat")." {$this->error('catid')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["products_name_ar"]." :  <span style='color:#ff0000'>*</span></td>
				<td><input  type=\"text\" name=\"name_ar\" value=\"$name_ar\" class='form-control'> {$this->error('name_ar')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["products_name_en"]." : </td>
				<td><input dir='ltr' type=\"text\" name=\"name_en\" value=\"$name_en\" class='form-control'> {$this->error('name_en')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["products_photo"]." :  <span style='color:#ff0000'>*</span></td>
				<td><input type=\"text\" dir=ltr name=\"photo\" value=\"$photo\" id=\"$photo\" > 
				   <span id=\"preview_photo\"></span><img src=\"{$intro->base_url}uploads/news/$photo\" width=\"50\" height=\"50\" style=\"float:left;\" alt=\"\" />
				   <a class=\"btn btn-info icon-upload\" OnClick=\"javascript:popup('".admin_path."images.php?for_id=photo');\">".$intro->lang["file_bring"]."</a> 
				   {$this->error('photo')}
				</td>
			</tr>	
			<tr>
				<td>".$intro->lang["products_photo_ar"]." :  <span style='color:#ff0000'>*</span></td>
				<td><input type=\"text\" dir=ltr name=\"img_ar\" value=\"$img_ar\" id=\"$img_ar\" > 
				   <span id=\"preview_img_ar\"></span><img src=\"{$intro->base_url}uploads/news/$img_ar\" width=\"50\" height=\"50\" style=\"float:left;\" alt=\"\" />
				   <a class=\"btn btn-info icon-upload\" OnClick=\"javascript:popup('".admin_path."images.php?for_id=img_ar');\">".$intro->lang["file_bring"]."</a> 
				   {$this->error('img_ar')}
				</td>
			</tr>
			<tr>
				<td>Code :  <span style='color:#ff0000'>*</span></td>
				<td><input  type=\"text\" name=\"code\" value=\"$code\" class='form-control'> {$this->error('code')}</td>
			</tr>
			
				<tr>
				<td>Code Arabic:  <span style='color:#ff0000'>*</span></td>
				<td><input  type=\"text\" name=\"code_ar\" value=\"$code_ar\" class='form-control' {$this->error('code_ar')}</td>
			</tr>
			
			
			<tr>
				<td>Place :</td>
				<td>".form_select_array("place",$array['place'],$place , "None")."</td>
			</tr>
			<tr>
				<td>".$intro->lang["products_price"]." :  <span style='color:#ff0000'>*</span></td>
				<td><input  type=\"text\" name=\"price\" value=\"$price\" class='form-control'> {$this->error('price')}</td>
			</tr>
			
			<tr>
				<td>".$intro->lang["products_discount"]." : </td>
				<td><input  type=\"text\" name=\"discount\" value=\"$discount\" class='form-control'> 
				(خصم مبلغ صافي) {$this->error('discount')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["products_discount_percent"]." : </td>
				<td><input  type=\"text\" name=\"discount_percent\" value=\"$discount_percent\" class='form-control'> 
				(خصم نسبة مئوية من المبلغ) {$this->error('discount_percent')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["products_can_discount"]." : </td>
				<td><input type=\"checkbox\" name=\"can_discount\" value=\"1\" ".($can_discount==1?"checked=\"checked\"":"")." /> 
				(اذا تم وضع اشارة صح سيتم تطبيق الخصم على هذا المنتج في حالة عمل خصم جماعي.) {$this->error('can_discount')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["products_title2_ar"]." :  <span style='color:#ff0000'>*</span></td>
				<td><textarea name=\"title2_ar\" class='rtl form-control'  >$title2_ar</textarea></td>
			</tr>
			<tr>
				<td>".$intro->lang["products_title2_en"]." :  <span style='color:#ff0000'>*</span></td>
				<td><textarea name=\"title2_en\" class='ltr form-control'  >$title2_en</textarea></td>
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
				<td  ></td>
				<td  >
					<input type=\"hidden\" name=\"app_name\"  value=\"{$this->appname}\">
					<input type=\"hidden\" name=\"t\"  value=\"$t\">
					<input type=\"hidden\" name=\"id\"  value=\"$id\">
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
	
		$catid = intval( $intro->input->post('catid') );
		$name_ar = trim( $intro->input->post('name_ar') );
		$name_en = trim( $intro->input->post('name_en') );
		$date_add = trim( $intro->input->post('date_add') );
		$photo = trim( $intro->input->post('photo') );
		$img_ar = trim( $intro->input->post('img_ar') );
		$price = floatval( $intro->input->post('price') );
		$discount = floatval( $intro->input->post('discount') );
		$discount_percent = floatval( $intro->input->post('discount_percent') );
		$can_discount = intval( $intro->input->post('can_discount') );
		$details_ar = trim( $intro->input->post('details_ar') );
		$title2_en = trim( $intro->input->post('title2_en') );
		$title2_ar = trim( $intro->input->post('title2_ar') );
		$code = trim( $intro->input->post('code') );
		$status = trim( $intro->input->post('status') );
		$extra_titles_en = trim( $intro->input->post('extra_titles_en') );
		$extra_titles_ar = trim( $intro->input->post('extra_titles_ar') );
		
		if(  $catid == 0){
			$error['catid'] = "<span class=error>".$intro->lang["required"]."</span>"; 
			$this->Form("add");
			die();
		}	

			if($name_ar=='' && $name_en==""){
				$error['name_ar'] = "<span class=error>يجب ملء احد العناوين العربية او الانجليزية </span>"; 
				$error['name_en'] = "<span class=error>يجب ملء احد العناوين العربية او الانجليزية </span>"; 
				$this->Form("add");
			die();
			}	
		
		
			if($img_ar=='' && $photo==""){
				$error['photo'] = "<span class=error> يجب اضافة احد الصور العربية او الانجليزية  </span>"; 
				$error['img_ar'] = "<span class=error> يجب اضافة احد الصور العربية او الانجليزية  </span>"; 
				$this->Form("add");
			die();
			}
			
			
		$data["catid"] = $intro->input->post('catid');
		$data["name_ar"] = $intro->input->post('name_ar');
		if($data["name_ar"]  != '' ){
			$data["ar"] = 1;
		}
		$data["name_en"] = $intro->input->post('name_en');
		if($data["name_en"]  != '' ){
			$data["en"] = 1;
		}
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
		$data["extra_titles_en"] = $extra_titles_en;
		$data["extra_titles_ar"] = $extra_titles_ar;		 
		$intro->db->insert(PREFIX."_products",$data);
		revalidateNext('products');
		$intro->redirect($this->appname);
	}

	############################################################################

	
	function do_Edit(){
		global $intro,$error,$array;
		
	
		$data["catid"] = $intro->input->post('catid');
		$data["name_ar"] = $intro->input->post('name_ar');
		if($data["name_ar"]  != '' ){
			$data["ar"] = 1;
		}
		
		$data["name_en"] = $intro->input->post('name_en');
		if($data["name_en"]  != '' ){
			$data["en"] = 1;
		}
		
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
		
		$data["extra_titles_en"] = trim($intro->input->post('extra_titles_en'));
		$data["extra_titles_ar"] = trim($intro->input->post('extra_titles_ar'));
		
		$id = intval( $intro->input->post('id') );
		
		

		$intro->db->update(PREFIX."_products",$data,"id=$id");
		
		//if($intro->input->post('IF') == 1) die("<script>parent.$.fancybox.close();</script>");
		
		revalidateNext('products');
		$intro->redirect($this->appname);
	}
	
	function Del(){
		global $intro,$sess_admin,$array;
		
		$id = intval( $intro->input->get_post('id') );
		
		policy($sess_admin['adminid'],$this->appname.".php" , "del");

		$sql = $intro->db->query("DELETE FROM ".PREFIX."_products WHERE id=$id ");

		revalidateNext('products');
		$intro->redirect($this->appname);
	}
	
	function DelViews(){
	    
		global $intro,$sess_admin,$array;
		
	


		$sql = $intro->db->query("update ".PREFIX."_products set views='0' ");

		revalidateNext('products');
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
			$qry = " where catname_ar  LIKE '%$search_txt%' or  catname_en  LIKE '%$search_txt%'";
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

		
		echo "<div class=\"card my-3 \">
				  <div class=\"card-header  mb-3\">
					<i class=\"px-2 fa-solid fa-search\"></i> Search Form
				  </div>
				  <div class=\"card-body nopadding\">
					<form class=\"row nopadding\"  action=\"$this->base/Cat\" method=\"post\">
					 
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
				<i class=\"px-2 fa-solid fa-folder\"></i> ".$intro->lang["products_cat_cur"]." ($totalrows)
			  </div>
			  <div class=\"card-body\">
			 <div class=\"table-responsive\">
				<table class=\"table table-striped table-sm table-hover\">
					  <thead  class=\"table-dark\">
						<tr>
							<th scope=\"col\" >ID "._sort_th("catid","index")."</th>
							<th scope=\"col\">".$intro->lang["products_cat_catname_ar"]." "._sort_th("catname_ar","Cat")." </th>
							<th scope=\"col\">".$intro->lang["products_cat_catname_en"]." "._sort_th("catname_en","Cat")." </th>
							<th scope=\"col\">".$intro->lang["products_cat_catimage"]." "._sort_th("catimage","Cat")." </th>
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
				<td>".$intro->lang["products_cat_catname_ar"]." :  <span style='color:#ff0000'>*</span></td>
				<td><input  type=\"text\" name=\"catname_ar\" value=\"$catname_ar\" class='form-control'> {$this->error('catname_ar')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["products_cat_catname_en"]." :  <span style='color:#ff0000'>*</span></td>
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
		var_dump($_POST);
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
		revalidateNext('products');
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
		revalidateNext('products');
		$intro->redirect($this->appname , "Cat");
	}
	
	function DelCat(){
		global $intro,$sess_admin,$array;
		
		$catid = intval( $intro->input->get_post('catid') );
		
		policy($sess_admin['adminid'],$this->appname.".php" , "del");

		$sql = $intro->db->query("DELETE FROM ".PREFIX."_products_cat WHERE catid=$catid ");

		revalidateNext('products');
		$intro->redirect($this->appname , "Cat");
	}
		
}//end class Products
?>