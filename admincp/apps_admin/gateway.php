<?PHP
##############################################
#	Intro Technologies LTD.
#	Code By Mohammed AbuAbed - Intro.ps 
#	Email: info@intro.ps 
#	Support: http://intro.ps/support.php
#   Date: 2015-07-11 Time: 15:15:18
#	AppName: gateway
##############################################

class Gateway_AppAdmin extends Intro_AppsAdmin{

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
					<i class=\"px-2 fa-solid fa-regular\"></i> ".$intro->lang["gateway_appname"]." </a>
				  </li>
				  <li class=\"nav-item mx-2\">
					<a class=\"btn btn-"._css_active("Form")." px-3 p_add\" href=\"{$this->base}/Form?t=add\">
					<i class=\"px-2 fa-solid fa-plus\"></i> ".$intro->lang["gateway_add"]."</a>
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
			$qry = " where gate_name  LIKE '%$search_txt%' ";
		}
		
		if ($order=="") $order="gate_id:desc";
		$order = str_replace(":", " " , $order);

		$rows_per_page = 30;
		if ($page==0) $page=1;
		$nexlimit = ($page*$rows_per_page)-$rows_per_page;

		$result = $intro->db->query("SELECT * from ".PREFIX."_gateway $qry order by $order  limit $nexlimit,$rows_per_page");
		$totrows = $intro->db->returned_rows;
		
		$sql_all_rows = $intro->db->query("SELECT gate_id from ".PREFIX."_gateway $qry ");	
		$totalrows = $intro->db->returned_rows;
		while($myrow = $intro->db->fetch_assoc($result)){
			@extract($myrow);			
			$data.= "
			<tr >
				<td class=\"center\">$gate_id</td>
				<td>$gate_name</td>
				<td>$gate_path</td>
				<td>$gate_discount</td>
				<td class=\"center\"> 
					<a class=\"btn btn-info p_edit btn-sm\" href=\"{$this->base}/Form?t=edit&amp;gate_id=$gate_id\" title=\"".$intro->lang["edit"]."\"><i class=\"fa-solid fa-pen-to-square\"></i></a>
					<a class=\"btn btn-danger p_del intro_ui_del  btn-sm\" href=\"{$this->base}/Del?gate_id=$gate_id\" OnClick=\"return false;\" title=\"".$intro->lang["del"]."\"><i class=\"fa-solid fa-trash\"></i></a>
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
				<i class=\"px-2 fa-solid fa-regular\"></i> ".$intro->lang["gateway_cur"]." ($totalrows)
			  </div>
			  <div class=\"card-body\">
			   <div class=\"table-responsive\">
				<table class=\"table table-striped table-sm table-hover\">
					  <thead  class=\"table-dark\">
						<tr>
							<th>ID "._sort_th("gate_id","index")."</th>
							<th>".$intro->lang["gateway_gate_name"]." "._sort_th("gate_name","index")." </th>
							<th>".$intro->lang["gateway_gate_path"]." "._sort_th("gate_path","index")." </th>
							<th>Discount</th>
							<th>".$intro->lang["options"]."</th>
						</tr>
					  </thead>
					  <tbody>
					  $data
					  </tbody>
					 </table></div>
			 ";
	
		$order = str_replace(" ", ":" , $order);
		echo "<center class='pagination'>".pagination3("{$this->base}/index?search_txt=$search_txt&amp;order=$order", $totalrows, $rows_per_page, $page)."</center>";
		echo" </div>
		</div>";
	}
	
	function Form($t=""){
		global $intro,$error,$sess_admin,$array;
		global $gate_name,$gate_status,$gate_path,$gate_discount,$gate_privatekey,$gate_sellerId,$gate_user,$gate_pass,$gate_verifyssl,$gate_sandbox,$gate_format;
		
		if($error || $_POST != null) @extract($_POST);
		$IF = intval( $intro->input->get_post("IF") );		
		$gate_id = intval( $intro->input->get_post("gate_id") );		
		$t = $t==""?$intro->input->get_post("t"):$t;
		
		if($IF != 1)
		$this->nav();
		
		if($t == "edit")
		{			
			policy($sess_admin['adminid'],$this->appname.".php" , "edit");
			
			$sql = $intro->db->query("SELECT * FROM ".PREFIX."_gateway where gate_id='$gate_id'");
			$row = $intro->db->fetch_assoc($sql);
			@extract($row);

			$btn['legend_name'] = $intro->lang["gateway_edit"]." <b>$gate_id</b>";
			$btn['legend_icon'] = "fa-solid fa-edit";
			$btn['name'] = $intro->lang["save_changes"];
			$btn['img_icon'] = "fa-solid fa-edit";		   
			$btn['action'] = "doEdit";
			
		}
		elseif($t == "add")
		{
			policy($sess_admin['adminid'],$this->appname.".php" , "add");
			
			$btn['legend_name'] = $intro->lang["gateway_add"];
			$btn['legend_icon'] = "fa-solid fa-plus";
			$btn['name'] = $intro->lang["save"];
			$btn['img_icon'] = "fa-solid fa-plus";		   
			$btn['action'] = "doAdd";
			$btn['copy'] = "";
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
				<td>".$intro->lang["gateway_gate_name"]." :  <span style='color:#ff0000'>*</span></td>
				<td><input  type=\"text\" name=\"gate_name\" value=\"$gate_name\" class='form-control'> {$this->error('gate_name')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["gateway_gate_path"]." :  <span style='color:#ff0000'>*</span></td>
				<td><input  type=\"text\" name=\"gate_path\" value=\"$gate_path\" class='form-control'> {$this->error('gate_path')}</td>
			</tr>
				<tr>
				<td>Discount : </td>
				<td><input  type=\"text\" name=\"gate_discount\" value=\"$gate_discount\" class='form-control'> {$this->error('gate_discount')}</td>
			</tr>
			
			<tr>
				<td>Status :  <span style='color:#ff0000'>*</span></td>
				<td>".form_option('gate_status',$gate_status)."{$this->error('gate_status')}</td>
			</tr>
			
			<tr>
				<td>".$intro->lang["gateway_gate_privatekey"]." : </td>
				<td><input  type=\"text\" name=\"gate_privatekey\" value=\"$gate_privatekey\" class='form-control'> {$this->error('gate_privatekey')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["gateway_gate_sellerId"]." : </td>
				<td><input  type=\"text\" name=\"gate_sellerId\" value=\"$gate_sellerId\" class='form-control'> {$this->error('gate_sellerId')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["gateway_gate_user"]." : </td>
				<td><input  type=\"text\" name=\"gate_user\" value=\"$gate_user\" class='form-control'> {$this->error('gate_user')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["gateway_gate_pass"]." : </td>
				<td><input  type=\"text\" name=\"gate_pass\" value=\"$gate_pass\" class='form-control'> {$this->error('gate_pass')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["gateway_gate_verifyssl"]." : </td>
				<td><input  type=\"text\" name=\"gate_verifyssl\" value=\"$gate_verifyssl\" class='form-control'> {$this->error('gate_verifyssl')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["gateway_gate_sandbox"]." : </td>
				<td><input  type=\"text\" name=\"gate_sandbox\" value=\"$gate_sandbox\" class='form-control'> {$this->error('gate_sandbox')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["gateway_gate_format"]." : </td>
				<td><input  type=\"text\" name=\"gate_format\" value=\"$gate_format\" class='form-control'> {$this->error('gate_format')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["details"]." : </td>
				<td>
				"; fck_editor("gate_bodytext",intro_html_decode($gate_bodytext) ); echo "
				
				 {$this->error('gate_bodytext')}</td>
			</tr>
			
			<tr>
				<td class=\"center\" ></td>
				<td class=\"center\" >
					<input type=\"hidden\" name=\"t\"  value=\"$t\">
					<input type=\"hidden\" name=\"gate_id\"  value=\"$gate_id\">
					<input type=\"hidden\" name=\"IF\"  value=\"$IF\">
					<button type=\"submit\" class='btn btn-primary' ><i class=\"{$btn['img_icon']}\"></i> {$btn['name']} </button>
					
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
		$gate_name = trim( $intro->input->post('gate_name') );
		$gate_path = trim( $intro->input->post('gate_path') );
		$gate_privatekey = trim( $intro->input->post('gate_privatekey') );
		$gate_sellerId = trim( $intro->input->post('gate_sellerId') );
		$gate_user = trim( $intro->input->post('gate_user') );
		$gate_pass = trim( $intro->input->post('gate_pass') );
		$gate_verifyssl = trim( $intro->input->post('gate_verifyssl') );
		$gate_sandbox = trim( $intro->input->post('gate_sandbox') );
		$gate_format = trim( $intro->input->post('gate_format') );
		$gate_status = trim( $intro->input->post('gate_status') );
		$gate_discount = trim( $intro->input->post('gate_discount') );
		
		if($gate_name == "" || $gate_path == ""){

			if($gate_name == ""){ $error['gate_name'] = "<span class=error>".$intro->lang["required"]."</span>"; }
				if($gate_path == ""){ $error['gate_path'] = "<span class=error>".$intro->lang["required"]."</span>"; }
				
			
			
			$this->Form("add");
			die();
		}		
		
		$data["gate_name"] = $intro->input->post('gate_name');
		$data["gate_path"] = $intro->input->post('gate_path');
		$data["gate_privatekey"] = $intro->input->post('gate_privatekey');
		$data["gate_sellerId"] = $intro->input->post('gate_sellerId');
		$data["gate_user"] = $intro->input->post('gate_user');
		$data["gate_pass"] = $intro->input->post('gate_pass');
		$data["gate_verifyssl"] = $intro->input->post('gate_verifyssl');
		$data["gate_sandbox"] = $intro->input->post('gate_sandbox');
		$data["gate_format"] = $intro->input->post('gate_format');
		$data["gate_status"] = $intro->input->post('gate_status');
		$data["gate_discount"] = trim($intro->input->post('gate_discount'));
		$data["gate_bodytext"] = trim($intro->input->post('gate_bodytext'));

				 
		$intro->db->insert(PREFIX."_gateway",$data);
		
		//if($intro->input->post('IF') == 1) die("<script>parent.location.reload(true);parent.$.fancybox.close();</script>");
		
		$intro->redirect($this->appname);
	}

	############################################################################

	function doEdit(){
		global $intro,$array;
			
		
		$data["gate_name"] = $intro->input->post('gate_name');
		$data["gate_path"] = $intro->input->post('gate_path');
		$data["gate_privatekey"] = $intro->input->post('gate_privatekey');
		$data["gate_sellerId"] = $intro->input->post('gate_sellerId');
		$data["gate_user"] = $intro->input->post('gate_user');
		$data["gate_pass"] = $intro->input->post('gate_pass');
		$data["gate_verifyssl"] = $intro->input->post('gate_verifyssl');
		$data["gate_sandbox"] = $intro->input->post('gate_sandbox');
		$data["gate_format"] = $intro->input->post('gate_format');
		$data["gate_status"] = $intro->input->post('gate_status');
		$data["gate_discount"] = trim($intro->input->post('gate_discount'));
		$data["gate_bodytext"] = trim($intro->input->post('gate_bodytext'));
		
		
		$gate_id = intval( $intro->input->post('gate_id') );

		$intro->db->update(PREFIX."_gateway",$data,"gate_id=$gate_id");
				
		$intro->redirect($this->appname);
	}
	
	function Del(){
		global $intro,$sess_admin,$array;
		
		$gate_id = intval( $intro->input->get_post('gate_id') );
		
		policy($sess_admin['adminid'],$this->appname.".php" , "del");

		$sql = $intro->db->query("DELETE FROM ".PREFIX."_gateway WHERE gate_id=$gate_id ");

		$intro->redirect($this->appname);
	}
	
	############################################################################

	function Active(){
		global $intro,$gate_id;

		$sql = $intro->db->query("UPDATE ".PREFIX."_gateway SET status='1' WHERE gate_id='$gate_id' ");

		$intro->redirect($this->appname);

	}

}//end class Gateway
?>