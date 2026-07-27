<?PHP
##############################################
#	Intro Technologies LTD.
#	Code By Mohammed AbuAbed - Intro.ps 
#	Email: info@intro.ps 
#	Support: http://intro.ps/support.php
#   Date: 2016-03-03 Time: 13:08:29
#	AppName: marquee
##############################################

class Marquee_AppAdmin extends Intro_AppsAdmin{

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
					<i class=\"px-2 fa-solid fa-list\"></i> ".$intro->lang["marquee_appname"]." </a>
				  </li>
				  <li class=\"nav-item mx-2\">
					<a class=\"btn btn-"._css_active("Form")." px-3 p_add\" href=\"{$this->base}/Form?t=add\">
					<i class=\"px-2 fa-solid fa-plus\"></i> ".$intro->lang["marquee_add"]."</a>
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
			$qry = " where mtitle  LIKE '%$search_txt%' ";
		}

      
		
		if ($order=="") $order="mid:desc";
		$order = str_replace(":", " " , $order);

		$rows_per_page = "30";
		if ($page==0) $page=1;
		$nexlimit = $page * $rows_per_page - $rows_per_page;

		$result = $intro->db->query("SELECT * from ".PREFIX."_marquee $qry order by $order  limit $nexlimit,$rows_per_page");
		$totrows = $intro->db->returned_rows;
		
		$sql_all_rows = $intro->db->query("SELECT mid from ".PREFIX."_marquee $qry ");	
		$totalrows = $intro->db->returned_rows;
		while($myrow = $intro->db->fetch_assoc($result)){
			@extract($myrow);
			$data.="
				<tr >
					
					<td class=\"center\">$mid</td>
					<td style='font-size:0.8rem'>$mtitle_ar</td>
					<td>$mtitle_en</td>
					<td>$w</td>
					<td style='background-color:#$color'>$color</td>
					<td class=\"center\" style='width:100px;'> 
						<a class=\"btn btn-info p_edit btn-sm\" href=\"{$this->base}/Form?t=edit&mid=$mid\" title=\"".$intro->lang["edit"]."\"><i class=\" fa-solid fa-pen-to-square\"></i></a>
						<a class=\"btn btn-danger p_del intro_ui_del btn-sm\" href=\"{$this->base}/Del?mid=$mid\" OnClick=\"return false;\" title=\"".$intro->lang["del"]."\"><i class=\" fa-solid fa-trash\"></i></a>
					</td>
				</tr>";
		}
		

           
		echo "<div class=\"card my-3 \">
				  <div class=\"card-header  mb-3\">
					<i class=\"px-2 fa-solid fa-search\"></i> Search Form
				  </div>
				  <div class=\"card-body nopadding pl-2\">
					<form class=\"row \"  action=\"\" method=\"post\">
					 
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
				<i class=\"px-2 fa-solid fa-list\"></i> ".$intro->lang["marquee_cur"]." ($totalrows)
			  </div>
			  <div class=\"card-body\">
			  <div class=\"table-responsive\">
				 <div class=\"table-responsive\">
				<table class=\"table table-striped table-sm table-hover\">
					  <thead  class=\"table-dark\">
						<tr>
							<th >ID </th>
							<th>".$intro->lang["marquee_mtitle_ar"]."  </th>
							<th>".$intro->lang["marquee_mtitle_en"]."  </th>
							<th>Arrange  </th>
							<th>".$intro->lang["marquee_color"]." </th>
							<th>".$intro->lang["options"]."</th>
						</tr>
					  </thead>
					  <tbody>
					  $data
					  </tbody>
					 </table></div>

			  </div>
			  </div>
		</div>";
	$order = str_replace(" ", ":" , $order);
		echo "<center>".pagination3("{$this->base}/index?search_txt=$search_txt&order=$order", $totalrows, $rows_per_page, $page)."</center>";
		
	}

	

	function Form($t=""){
		global $intro,$error,$sess_admin,$array;
		global $mtitle_ar,$mtitle_en,$url_ar,$color,$url_en,$w;
		
		if($_GET != null) @extract($_GET);
		if($error || $_POST != null) @extract($_POST);
		
		
		$mid = intval( $intro->input->get_post("mid") );
		
		$t = $t==""?$intro->input->get_post("t"):$t;

		$this->nav();
		
		if($t == "edit"){
			
			policy($sess_admin['adminid'],$this->appname.".php" , "edit");
			
			$sql = $intro->db->query("SELECT * FROM ".PREFIX."_marquee where mid='$mid'");
			$row = $intro->db->fetch_assoc($sql);
			@extract($row);

			$btn['legend_name'] = $intro->lang["marquee_edit"]." <b>$mid</b>";
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
			
			$btn['legend_name'] = $intro->lang["marquee_add"];
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
						<td style='width:200px;'>".$intro->lang["marquee_mtitle_ar"]." :  <span style='color:#ff0000'>*</span></td>
						<td><input  type=\"text\" name=\"mtitle_ar\" value=\"$mtitle_ar\" class='form-control' > {$this->error('mtitle_ar')}</td>
					</tr>
					 <tr>
						<td>".$intro->lang["marquee_mtitle_en"]." : </td>
						<td><input  type=\"text\" name=\"mtitle_en\" value=\"$mtitle_en\" class='form-control' > {$this->error('mtitle_en')}</td>
					</tr>
					<tr>
						<td>Arrange : </td>
						<td><input  type=\"number\" name=\"w\" value=\"$w\" class='form-control' > {$this->error('w')}</td>
					</tr>
					<tr>
						<td>".$intro->lang["marquee_url"]." : </td>
						<td><input  type=\"text\" name=\"url_ar\" value=\"$url_ar\" class='form-control'  > {$this->error('url')}</td>
					</tr>
					 <tr>
						<td>English Url : </td>
						<td><input  type=\"text\" name=\"url_en\" value=\"$url_en\" class='form-control'  > {$this->error('url')}</td>
					</tr>
					<tr>
						<td>".$intro->lang["marquee_color"]." : </td>
						<td><input  type=\"color\" name=\"color\" value=\"#$color\" >#$color {$this->error('color')}</td>
					</tr>		 
		
			<tr>
				<td class=\"center\" ></div>
				<td class=\"center\" >
					<input type=\"hidden\" name=\"app_name\"  value=\"{$this->appname}\">
					<input type=\"hidden\" name=\"t\"  value=\"$t\">
					<input type=\"hidden\" name=\"mid\"  value=\"$mid\">
					<button class=\"btn btn-primary\" type=\"submit\" name=\"app_action\" value=\"{$btn['action']}\" title=\"{$btn['name']}\">
					<i class=\"{$btn['legend_icon']}\"></i>  {$btn['name']}
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
		$mtitle_ar = trim( $intro->input->post('mtitle_ar') );
		$mtitle_en = trim( $intro->input->post('mtitle_en') );
		$url = trim( $intro->input->post('url') );
		$color = trim( $intro->input->post('color') );
		$colors=explode('#',$color);
		
		if($mtitle_ar == ""){

			if($mtitle_ar == ""){ $error['mtitle_ar'] = "<span class=error>".$intro->lang["required"]."</span>"; }
			$this->Form("add");
			die();
		}		
		
		$data["mtitle_ar"] = $intro->input->post('mtitle_ar');
		$data["mtitle_en"] = $intro->input->post('mtitle_en');
		$data["url_ar"] = $intro->input->post('url_ar');
		$data["color"] = $colors[1];
		$data["url_en"] = $intro->input->post('url_en');
		$data["w"] = $intro->input->post('w');
				 
		$intro->db->insert(PREFIX."_marquee",$data);
		
		revalidateNext('marquee');
		$intro->redirect($this->appname);
	}

	############################################################################

	function doEdit(){
		global $intro,$array;
			
		$color = trim( $intro->input->post('color') );
		$colors=explode('#',$color);
		
		$data["mtitle_ar"] = $intro->input->post('mtitle_ar');
		$data["mtitle_en"] = $intro->input->post('mtitle_en');
		$data["url_ar"] = $intro->input->post('url_ar');
		$data["color"] = $colors[1];
		$data["url_en"] = $intro->input->post('url_en');
		$data["w"] = $intro->input->post('w');
		
		
		$mid = intval( $intro->input->post('mid') );

		$intro->db->update(PREFIX."_marquee",$data,"mid=$mid");

		revalidateNext('marquee');
		$intro->redirect($this->appname);
	}
	
	function Del(){
		global $intro,$sess_admin,$array;
		
		$mid = intval( $intro->input->get_post('mid') );
		
		policy($sess_admin['adminid'],$this->appname.".php" , "del");

		$sql = $intro->db->query("DELETE FROM ".PREFIX."_marquee WHERE mid=$mid ");

		revalidateNext('marquee');
		$intro->redirect($this->appname);
	}
	
	############################################################################

	function Active(){
		global $intro,$mid;

		$sql = $intro->db->query("UPDATE ".PREFIX."_marquee SET status='1' WHERE mid='$mid' ");

		revalidateNext('marquee');
		$intro->redirect($this->appname);

	}

}//end class Marquee
?>