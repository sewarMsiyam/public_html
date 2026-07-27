<?PHP


class Discount_AppAdmin extends Intro_AppsAdmin{

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
					<i class=\"px-2 fa-solid fa-box-open\"></i> Discount List  </a>
				  </li>
				  <li class=\"nav-item mx-2\">
					<a class=\"btn btn-"._css_active("Form")." px-3 p_add\" href=\"{$this->base}/Form?t=add\">
					<i class=\"px-2 fa-solid fa-plus\"></i> Add New Discount</a>
				  </li>
				
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

		if ($order=="") $order="id:desc";
		$order = str_replace(":", " " , $order);

		$rows_per_page = 30;
		if ($page==0) $page=1;
		$nexlimit = ($page*$rows_per_page)-$rows_per_page;

		$result = $intro->db->query("SELECT * from ".PREFIX."_discounts order by $order  limit $nexlimit,$rows_per_page");
		$totrows = $intro->db->returned_rows;
		
		$sql_all_rows = $intro->db->query("SELECT id from ".PREFIX."_discounts  ");	
		$totalrows = $intro->db->returned_rows;
		while($myrow = $intro->db->fetch_assoc($result)){
			@extract($myrow);			
			$i++;
			$active= ($active == 1) ? "Yes":"No";
			$discount_percent = ($discount_percent !='') ? " $discount_percent % " : "";
			$data.= "
				<tr >
				<td >$id</td>
				<td >$active</td>
				<td >$discount_text</td>
				<td >$discount_percent</td>
				<td>$dateadded</td>
				<td class=\"center\"> 
					<a class=\"btn btn-info p_edit btn-sm\" href=\"{$this->base}/Form?t=edit&id=$id\" title=\"".$intro->lang["edit"]."\"><i class=\"fa-solid fa-pen-to-square\"></i></a>
					<a class=\"btn btn-danger p_del intro_ui_del btn-sm\" href=\"{$this->base}/Del?id=$id\" OnClick=\"return false;\" title=\"".$intro->lang["del"]."\"><i class=\"fa-solid fa-trash\"></i></a>
				</td>
				
			
			</tr>";
		}
		
		echo "
		<div class=\"card border-info\">
			  <div class=\"card-header text-dark bg-info\">
				<i class=\"px-2 fa-solid fa-box-open\"></i>  All Discount ($totalrows)
			  </div>
			  <div class=\"card-body\">
		
				<div class='clear'></div><br>
			 <div class=\"table-responsive\">
				<table class=\"table table-striped table-sm table-hover\">
					  <thead  class=\"table-dark\">
						<tr>
							<th scope=\"col\">ID </th>
							<th scope=\"col\">Active  </th>
							<th scope=\"col\"> Text </th>
							<th scope=\"col\"> Percentage </th>
							<th scope=\"col\">Date  </th>
							<th scope=\"col\">Options  </th>
							
					
						</tr>
					  </thead>
					  <tbody>
					  $data
					  </tbody>
					 </table></div>";
		$order = str_replace(" ", ":" , $order);
		echo "<center class='pagination'>".pagination3("{$this->base}/index?search_txt=$search_txt&amp;order=$order", $totalrows, $rows_per_page, $page)."</center>";
		echo"  </div></div>";
	}
	
  
	function Form($t=""){
		global $intro,$error,$sess_admin,$array;
	
		if($error || $_POST != null) @extract($_POST);
		$IF = intval( $intro->input->get_post("IF") );		
		$id = intval( $intro->input->get_post("id") );		
		$t = $t==""?$intro->input->get_post("t"):$t;
		
		if($IF != 1)
		$this->nav();
		
		if($t == "edit")
		{			
			policy($sess_admin['adminid'],$this->appname.".php" , "edit");	
			$sql = $intro->db->query("SELECT * FROM ".PREFIX."_discounts where id='$id'");
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
			$dateadded=date('Y-m-d H:i:s');
			
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
				<td>Active  :  <span style='color:#ff0000'>*</span></td>
				<td>".form_option('active',$active)."</td>
			</tr>
			<tr>
				<td>Discount  :  <span style='color:#ff0000'>*</span></td>
				<td><input  type=\"text\" name=\"discount_text\" value=\"$discount_text\" class='form-control'> {$this->error('discount_text')}</td>
			</tr>
			
				<tr>
				<td>Discount % :	  <span style='color:#ff0000'>*</span></td>
				<td><input  type=\"text\" name=\"discount_percent\" value=\"$discount_percent\" class='form-control' {$this->error('discount_percent')}</td>
			</tr>
			<tr>
				<td>datea dded  :  <span style='color:#ff0000'>*</span></td>
				<td><input dir=ltr  type=\"text\" name=\"dateadded\" value=\"$dateadded\" class='form-control'> {$this->error('dateadded')}</td>
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

	function doAdd(){
		global $intro,$error;
	
		
		$data["active"] = $intro->input->post('active');
		$data["discount_text"] = $intro->input->post('discount_text');
		$data["discount_percent"] = $intro->input->post('discount_percent');
		$data["dateadded"] = $intro->input->post('dateadded') ;
		 
		$intro->db->insert(PREFIX."_discounts",$data);
		revalidateNext('products');
		$intro->redirect($this->appname);
	}

	############################################################################

	
	function do_Edit(){
		global $intro,$error,$array;
		
	
		$data["active"] = $intro->input->post('active');
		$data["discount_text"] = $intro->input->post('discount_text');
		$data["discount_percent"] = $intro->input->post('discount_percent');
		$data["dateadded"] = $intro->input->post('dateadded') ;
		
		$id = intval( $intro->input->post('id') );
		
		$intro->db->update(PREFIX."_discounts",$data,"id=$id");
		
		revalidateNext('products');
		$intro->redirect($this->appname);
	}
	
	function Del(){
		global $intro,$sess_admin,$array;
		
		$id = intval( $intro->input->get_post('id') );
		
		policy($sess_admin['adminid'],$this->appname.".php" , "del");

		$sql = $intro->db->query("DELETE FROM ".PREFIX."_discounts WHERE id=$id ");

		revalidateNext('products');
		$intro->redirect($this->appname);
	}
	
	
	
}//end class Products
?>