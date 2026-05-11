<?PHP
##############################################
#	Intro Technologies LTD.
#	Code By Mohammed AbuAbed - Intro.ps 
#	Email: info@intro.ps 
#	Support: http://intro.ps/support.php
#   Date: 2015-01-24 Time: 17:00:49
#	AppName: style
##############################################

class Style_AppAdmin extends Intro_AppsAdmin{

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
					<i class=\"px-2 fa-solid fa-border-top-left \"></i> ".$intro->lang["style_appname"]." </a>
				  </li>
				  <li class=\"nav-item mx-2\">
					<a class=\"btn btn-"._css_active("Form")." px-3 p_add\" href=\"{$this->base}/Form?t=add\">
					<i class=\"px-2 fa-solid fa-plus\"></i> ".$intro->lang["style_add"]."</a>
				  </li>
			</ul>";
			
	}

	
	function index(){
		global $intro,$array;

		$order = trim( $intro->input->get_post("order") );

		$this->nav();

		if ($order=="") $order="id:desc";
		$order = str_replace(":", " " , $order);

		$result = $intro->db->query("SELECT * from ".PREFIX."_style order by $order  ;");
		$totalrows = $intro->db->returned_rows;
		while($myrow = $intro->db->fetch_assoc($result)){
			@extract($myrow);			
			$data.= "
			<tr >
				<td class=\"center\">$id</td>
				<td>$varname</td>
				<td>$title</td>
				<td>$last_edit</td>
				<td class=\"center\"> 
					<a class=\"btn btn-info p_edit btn-sm\" href=\"{$this->base}/Form?t=edit&amp;id=$id\" title=\"".$intro->lang["edit"]."\"><i class=\"fa-solid fa-pen-to-square\"></i></a>
					<a class=\"btn btn-danger p_del intro_ui_del btn-sm\" href=\"{$this->base}/Del?id=$id\" OnClick=\"return false;\" title=\"".$intro->lang["del"]."\"><i class=\"fa-solid fa-trash\"></i></a>
				</td>
			</tr>";
		}
		echo "
		<div class=\"card border-info\">
			  <div class=\"card-header text-dark bg-info\">
				<i class=\"px-2 fa-solid fa-border-top-left\"></i> ".$intro->lang["style_cur"]." ($totalrows)
			  </div>
			  <div class=\"card-body\">
			   <div class=\"table-responsive\">
				<table class=\"table table-striped table-sm table-hover\">
					  <thead  class=\"table-dark\">
						<tr>
							<th>ID "._sort_th("id","index")."</th>
							<th>".$intro->lang["style_varname"]." "._sort_th("varname","index")." </th>
							<th>".$intro->lang["style_title"]." "._sort_th("title","index")." </th>
							<th>".$intro->lang["style_last_edit"]." "._sort_th("last_edit","index")." </th>
							<th>".$intro->lang["options"]."</th>
						</tr>
					  </thead>
					  <tbody>
					  $data
					  </tbody>
					 </table></div>
			</div>
		</div>";

	}

	function Form($t=""){
		global $intro,$error,$sess_admin,$array;
		global $varname,$title,$html,$last_edit;
		
		if($error || $_POST != null) @extract($_POST);
		$IF = intval( $intro->input->get_post("IF") );		
		$id = intval( $intro->input->get_post("id") );		
		$t = $t==""?$intro->input->get_post("t"):$t;
		
		if($IF != 1)
		$this->nav();
		
		if($t == "edit")
		{			
			policy($sess_admin['adminid'],$this->appname.".php" , "edit");
			
			$sql = $intro->db->query("SELECT * FROM ".PREFIX."_style where id='$id'");
			$row = $intro->db->fetch_assoc($sql);
			@extract($row);

			$btn['legend_name'] = $intro->lang["style_edit"]." <b>$id</b>";
			$btn['legend_icon'] = "icon-edit";
			$btn['name'] = $intro->lang["save_changes"];
			$btn['img_icon'] = "icon-floppy";		   
			$btn['action'] = "doEdit";
			$html = stripslashes($html);		   
			$html = str_replace("textarea","textarea100",$html);
			?>
			<script>
			$(document).ready(function (){
				$("#form_add").submit( function save_data_style() {   
				$('#result').html('...');
					$.post('<?=$this->base . '/doEdit?NH=1'?>',$(this).serialize(),
						function(data){ $("#result").html(data) }
					);
					return false;   
				});   

			});
			$(window).keypress(function(event) {
				if (!(event.which == 115 && event.ctrlKey) && !(event.which == 19)) return true;
				$("#form_add").submit();
				event.preventDefault();
				return false;
			});
			shortcut.add("Ctrl+S",function() {
				$("#form_add").submit();
			});
			</script>
			<?php
		}
		elseif($t == "add")
		{
			policy($sess_admin['adminid'],$this->appname.".php" , "add");
			
			$btn['legend_name'] = $intro->lang["style_add"];
			$btn['legend_icon'] = "icon-plus-squared";
			$btn['name'] = $intro->lang["add_new"];
			$btn['img_icon'] = "icon-plus-squared";		   
			$btn['action'] = "doAdd";
			$html = str_replace("textarea","textarea100",$html);
		}		
			
	echo "
		<div class=\"forms\">	
			<fieldset>
				<legend>
					<i class=\"{$btn['legend_icon']}\"></i> {$btn['legend_name']} 
				</legend>

			<form method=\"POST\" id=\"form_add\" name=\"form_add\" action=\"{$this->base}/{$btn['action']}\" enctype=\"multipart/form-data\">
			<table cellspacing=\"2\" style=\"margin:auto;width:95%\">
			<tr>
				<td>".$intro->lang["style_varname"]." :  <span style='color:#ff0000'>*</span></td>
				<td><input  type=\"text\" name=\"varname\" value=\"$varname\" size=\"30\"> {$this->error('varname')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["style_title"]." :  <span style='color:#ff0000'>*</span></td>
				<td><input  type=\"text\" name=\"title\" value=\"$title\" size=\"30\"> {$this->error('title')}</td>
			</tr>
			<tr>
				<td>".$intro->lang["style_html"]." : </td>
				<td>{$this->error('html')}</td>
			</tr>
			<tr>
				 <td dir=ltr colspan=2><textarea dir=ltr 
				 name=\"html\" id=\"html_edit_code\"
				 style=\"font-size: 20px; width:100%; color: #FFFF00; background-color: #2C4D54\" 
				 rows=\"25\" wrap=\"wrap\">$html</textarea>$error[html]</td>
			</tr>
			<tr>
				<td class=\"center\" colspan=\"2\">
					<input type=\"hidden\" name=\"app_name\"  value=\"{$this->appname}\">
					<input type=\"hidden\" name=\"t\"  value=\"$t\">
					<input type=\"hidden\" name=\"id\"  value=\"$id\">
					<input type=\"hidden\" name=\"IF\"  value=\"$IF\">
					<button type=\"submit\" name=\"app_action\" value=\"{$btn['action']}\"><i class=\"{$btn['img_icon']}\"> {$btn['name']} </i></button>
					<span id=\"result\"></span>
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
		$varname = trim( $intro->input->post('varname') );
		$title = trim( $intro->input->post('title') );
		$html = trim( $intro->input->post('html') );
		
		if($varname == "" || $title == ""){

			if($varname == ""){ $error['varname'] = "<span class=error>".$intro->lang["required"]."</span>"; }
			if($title == ""){ $error['title'] = "<span class=error>".$intro->lang["required"]."</span>"; }

			$this->Form("add");
			die();
		}		
		
		$data["varname"] = $intro->input->post('varname');
		$data["title"] = $intro->input->post('title');
		$data["html"] = $intro->input->post('html');
				 
		$intro->db->insert(PREFIX."_style",$data);
		
		//if($intro->input->post('IF') == 1) die("<script>parent.location.reload(true);parent.$.fancybox.close();</script>");
		
		$intro->redirect($this->appname);
	}

	############################################################################

	function doEdit(){
		global $intro,$array;
			
		
		$html = addslashes($intro->input->post('html'));
		$html = str_replace("textarea100","textarea",$html);
			 
		$data["varname"] = $intro->input->post('varname');
		$data["title"] = $intro->input->post('title');		

		$id = intval( $intro->input->post('id') );

		$intro->db->update(PREFIX."_style",$data,"id=$id");
		
		//if($intro->input->post('IF') == 1) die("<script>parent.$.fancybox.close();</script>");
		
		$sql = $intro->db->query_fast("UPDATE ".PREFIX."_style "
		." SET html='$html',last_edit=NOW() WHERE id=$id; ");
		
		echo "تم حفظ التعديل : " . date("H:i:s");
		
		//$intro->redirect($this->appname);
	}
	
	function Del(){
		global $intro,$sess_admin,$array;
		
		$id = intval( $intro->input->get_post('id') );
		
		policy($sess_admin['adminid'],$this->appname.".php" , "del");

		//$sql = $intro->db->query("DELETE FROM ".PREFIX."_style WHERE id=$id ");

		$intro->redirect($this->appname);
	}	
	############################################################################


}//end class Style
?>