<?PHP
##############################################
#	Intro Technologies LTD.
#	Code By Mohammed AbuAbed - Intro.ps 
#	Email: info@intro.ps 
#	Support: http://intro.ps/support.php
#   Date: 2015-07-21 Time: 12:46:05
#	AppName: msgs
##############################################

class Msgs_AppAdmin extends Intro_AppsAdmin{

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
					<i class=\"px-2 fa-solid fa-message\"></i> ".$intro->lang["msgs_appname"]." </a>
				  </li>
				  <li class=\"nav-item mx-2\">
					<a class=\"btn btn-"._css_active("Form")." px-3 p_add\" href=\"{$this->base}/Form?t=add\">
					<i class=\"px-2 fa-solid fa-plus\"></i> ".$intro->lang["msgs_add"]."</a>
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
			$qry = " where title  LIKE '%$search_txt%' ";
		}
		
		if ($order=="") $order="id:desc";
		$order = str_replace(":", " " , $order);

		$rows_per_page = 30;
		if ($page==0) $page=1;
		$nexlimit = ($page*$rows_per_page)-$rows_per_page;

		$result = $intro->db->query("SELECT * from ".PREFIX."_msgs $qry order by $order  limit $nexlimit,$rows_per_page");
		$totrows = $intro->db->returned_rows;
		
		$sql_all_rows = $intro->db->query("SELECT id from ".PREFIX."_msgs $qry ");	
		$totalrows = $intro->db->returned_rows;
		while($myrow = $intro->db->fetch_assoc($result)){
			@extract($myrow);			
			$data.= "
			<tr >
				<td class=\"center\">$id</td>
				<td>$title</td>
				<td>$varname</td>
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
				<i class=\"px-2 fa-solid fa-message\"></i> ".$intro->lang["msgs_cur"]." ($totalrows)
			  </div>
			  <div class=\"card-body\">
			   <div class=\"table-responsive\">
				<table class=\"table table-striped table-sm table-hover\">
					  <thead  class=\"table-dark\">
						<tr>
							<th>ID "._sort_th("id","index")."</th>
							<th>".$intro->lang["msgs_title"]." "._sort_th("title","index")." </th>
							<th>".$intro->lang["msgs_varname"]." "._sort_th("varname","index")." </th>
							<th>".$intro->lang["options"]."</th>
						</tr>
					  </thead>
					  <tbody>
					  $data
					  </tbody>
					 </table> </div>";
		$order = str_replace(" ", ":" , $order);
		
		echo "<center class='pagination'>".pagination3("{$this->base}/index?search_txt=$search_txt&amp;order=$order", $totalrows, $rows_per_page, $page)."</center>";
		echo" </div>
		</div>";
	}
	
	function Form($t=""){
		global $intro,$error,$sess_admin,$array;
		global $title,$varname,$msg_ar,$msg_en;
		
		if($error || $_POST != null) @extract($_POST);
		$IF = intval( $intro->input->get_post("IF") );		
		$id = intval( $intro->input->get_post("id") );		
		$t = $t==""?$intro->input->get_post("t"):$t;
		
		if($IF != 1)
		$this->nav();
		
		if($t == "edit")
		{			
			policy($sess_admin['adminid'],$this->appname.".php" , "edit");
			
			$sql = $intro->db->query("SELECT * FROM ".PREFIX."_msgs where id='$id'");
			$row = $intro->db->fetch_assoc($sql);
			@extract($row);

			$btn['legend_name'] = $intro->lang["msgs_edit"]." <b>$id</b>";
			$btn['legend_icon'] = "fa-solid fa-pen-to-square";
			$btn['name'] = $intro->lang["save_changes"];
			$btn['img_icon'] = "fa-solid fa-edit";		   
			$btn['action'] = "doEdit";
			
		}
		elseif($t == "add")
		{
			policy($sess_admin['adminid'],$this->appname.".php" , "add");
			
			$btn['legend_name'] = $intro->lang["msgs_add"];
			$btn['legend_icon'] = "fa-solid fa-plus";
			$btn['name'] = $intro->lang["save"];
			$btn['img_icon'] = "fa-solid fa-plus";		   
			$btn['action'] = "doAdd";
			
		}		
		
		$msg_ar = stripslashes($msg_ar);
		$msg_en = stripslashes($msg_en);
			
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
				<td>".$intro->lang["msgs_title"]." :  <span style='color:#ff0000'>*</span></td>
				<td><input  type=\"text\" name=\"title\" value=\"$title\" class='form-control'> {$this->error('title')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["msgs_varname"]." :  <span style='color:#ff0000'>*</span></td>
				<td><input  type=\"text\" name=\"varname\" value=\"$varname\" class='form-control'> {$this->error('varname')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["msgs_msg_ar"]." : </td>
				<td><textarea name=\"msg_ar\" class='form-control' >$msg_ar</textarea>{$this->error('msg_ar')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["msgs_msg_en"]." : </td>
				<td><textarea dir=ltr name=\"msg_en\" class='form-control'>$msg_en</textarea>{$this->error('msg_en')}</td>
			</tr>
			<tr>
				<td class=\"center\" ></td>
				<td class=\"center\" >
					<input type=\"hidden\" name=\"app_name\"  value=\"{$this->appname}\">
					<input type=\"hidden\" name=\"t\"  value=\"$t\">
					<input type=\"hidden\" name=\"id\"  value=\"$id\">
					<input type=\"hidden\" name=\"IF\"  value=\"$IF\">
					<button type=\"submit\" class='btn btn-primary' ><i class=\"{$btn['img_icon']}\"> </i>{$btn['name']} </button>
					
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
		$title = trim( $intro->input->post('title') );
		$varname = trim( $intro->input->post('varname') );
		$msg_ar = trim( $intro->input->post('msg_ar') );
		$msg_en = trim( $intro->input->post('msg_en') );
		
		if($title == "" || $varname == ""){

			if($title == ""){ $error['title'] = "<span class=error>".$intro->lang["required"]."</span>"; }
				if($varname == ""){ $error['varname'] = "<span class=error>".$intro->lang["required"]."</span>"; }
				
			
			
			$this->Form("add");
			die();
		}		
		
		$msg_ar = str_replace("'","&#39;" , $msg_ar);
		$msg_en = str_replace("'","&#39;" , $msg_en);
		
		$data["title"] = $intro->input->post('title');
		$data["varname"] = $intro->input->post('varname');
		$data["msg_ar"] = addslashes($msg_ar);
		$data["msg_en"] = addslashes($msg_en);
				 
		$intro->db->insert(PREFIX."_msgs",$data);
		
		//if($intro->input->post('IF') == 1) die("<script>parent.location.reload(true);parent.$.fancybox.close();</script>");
		
		$intro->redirect($this->appname);
	}

	############################################################################

	function doEdit(){
		global $intro,$array;
			
		$msg_ar = trim( $intro->input->post('msg_ar') );
		$msg_en = trim( $intro->input->post('msg_en') );
		
		$msg_ar = str_replace("'","&#39;" , $msg_ar);
		$msg_en = str_replace("'","&#39;" , $msg_en);
		
		$data["title"] = $intro->input->post('title');
		$data["varname"] = $intro->input->post('varname');
		
		$data["msg_ar"] = addslashes($msg_ar);
		$data["msg_en"] = addslashes($msg_en);
		
		
		$id = intval( $intro->input->post('id') );

		$intro->db->update(PREFIX."_msgs",$data,"id=$id");
		
		//if($intro->input->post('IF') == 1) die("<script>parent.$.fancybox.close();</script>");
		
		$intro->redirect($this->appname);
	}
	
	function Del(){
		global $intro,$sess_admin,$array;
		
		$id = intval( $intro->input->get_post('id') );
		
		policy($sess_admin['adminid'],$this->appname.".php" , "del");

		$sql = $intro->db->query("DELETE FROM ".PREFIX."_msgs WHERE id=$id ");

		$intro->redirect($this->appname);
	}
	
	############################################################################

	function Active(){
		global $intro,$id;

		$sql = $intro->db->query("UPDATE ".PREFIX."_msgs SET status='1' WHERE id='$id' ");

		$intro->redirect($this->appname);

	}

}//end class Msgs
?>