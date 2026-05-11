<?php

function form_sel_multi($sel_name,$array,$comp,$txt='Choose'){
	global $intro,$array;
	
	$html  = "<select dir=ltr name=\"$sel_name\" multiple size=10>\n";
	$html .= "<option $def_sel value=\"all\">".$txt."</option>\n";
	$result = $intro->db->query("SELECT dvb_id,dvb_name,dvb_adminid from ".PREFIX."_dvb order by dvb_adminid,dvb_name",'',true);
	while($row = $intro->db->fetch_assoc($result))
	{		
		if(is_array($comp) && in_array($row[dvb_id], $comp) ) $sel = "selected";
		
		$admin = $array['admins'][$row[dvb_adminid]];
		$html .= "<option $sel value=\"$row[dvb_id]\">$admin -> $row[dvb_name]</option>\n";
		$sel = "";
	}
	$html .= "</select>\n\n";
	return $html;
}

function form_select_global($html_name,$html_title,$table,$compair,$the_id,$the_name,$where='',$order='') {
   global $db, $prefix,$intro;

$sel="";
   $html = "\n\n<select name=\"$html_name\" id=\"$html_name\" class=\"form-control form-control-sm\">\n\n";
   $sql =$intro->db->query("select $the_id,$the_name from ".PREFIX."_".$table." $where $order");
   $html .= "<option value=\"0\"> $html_title </option>\n";
   while($row = $intro->db->fetch_assoc($sql)) {

   if ($row[$the_id]==$compair) { $sel = "selected "; }
  	$html .= "<option $sel value=\"$row[$the_id]\"> $row[$the_name]</option>\n";
        $sel = "";
   }
   $html .= "</select>\n\n";
 

   return $html;

}

function sel_array($sel_name,$array,$comp,$txt='',$first_value=0){
	global $intro;

	$sel = "";
	
   $html  = "\n<select name=\"$sel_name\" id=\"$sel_name\">\n";
   $html .= "\t<option value=\"$first_value\" selected>".($txt==''?$intro->lang['choose']:$txt)."</option>\n";
   while(list($key, $val) = @each($array)){
        if ($comp == $key){ $sel = "selected"; }
        $html .= "\t<option $sel value=\"$key\">$val</option>\n";
        $sel = "";
   }
   $html .= "</select>\n\n";

   return $html;
}


function form_select_array($sel_name,$array,$comp,$txt=''){
	global $intro;

   $html  = "<select name=\"$sel_name\" id=\"$sel_name\">\n";
   $html .= "<option selected value=\"0\">".($txt==""?$intro->lang['choose']:$txt)."</option>\n";
   foreach($array as $key=>$val){
        
        $html .= "<option ".(($comp==$key) ? "selected " : '' )." value=\"$key\">$val</option>\n";
        $sel = "";
   }
   $html .= "</select>\n\n";
   return $html;
}

function form_select_array_adv_type($sel_name,$array,$comp){
	global $intro;

   $html  = "<select name=\"$sel_name\" style=\"width: 250\" OnChange=\"javascript:hide_adds(this.value);\">\n";
   $html .= "<option selected value=\"0\">".$intro->lang['choose']." ".$intro->lang['adds_type']."</option>\n";
   while(list($key, $val) = each($array)){
        if ($comp == $key){ $sel = "selected"; }
        $html .= "<option $sel value=\"$key\">$val</option>\n";
        $sel = "";
   }
   $html .= "</select>\n\n";

   return $html;


}

function form_option_array($fld_name,$array,$comp){

	$html = $sel = '';
   while(list($key, $val) = each($array)){
        if ($comp == $key){ $sel = "checked"; }
        $html .= "<input type=\"radio\" name=\"$fld_name\" value=\"$key\" $sel> $val \n";
        $sel = "";
   }

   return $html;
}

function form_option($name,$value){
	global $intro;
    if (($value == 0) OR ($value == "")) {
	$sel1 = "";
	$sel2 = "checked";
    }
    if ($value == 1){
	$sel1 = "checked";
	$sel2 = "";
    }
    return "<input type=\"radio\" name=\"$name\" value=\"1\" $sel1 class='mx-1' > {$intro->lang["yes"]}
           <input type=\"radio\" name=\"$name\" value=\"0\" $sel2  class='mx-1'> {$intro->lang["no"]}  ";

}
function form_option_status($name,$value){
	global $intro;
    if (($value == 0) OR ($value == "")) {
	$sel1 = "";
	$sel2 = "checked";
    }
    if ($value == 1){
	$sel1 = "checked";
	$sel2 = "";
    }
    return "<input type=\"radio\" name=\"$name\" value=\"1\" $sel1> Enabled
           <input type=\"radio\" name=\"$name\" value=\"0\" $sel2> Disabled ";

}

function form_select($html_name,$cat_value,$sql_table, $where='',$sub='') {
	global $intro;
	
	$lang = $intro->maa->lang;
	
	$dyn_menu ="
	<select name=\"$html_name\" class=\"form-select\" >\n";
	$dyn_menu .="
		<option value=\"0\" selected> ".$intro->lang['choose']." </option>\n";
	$result = $intro->db->query("SELECT * from ".PREFIX."_".$sql_table." where father=0 $where order by catid ASC");
	while ($row = $intro->db->fetch_assoc($result)){
		//var_dump($row);
		$catname = $sql_table=="products_cat"?$row['catname_'.$lang]:$row['catname'];
	   $dyn_menu .="
		<option ".(($cat_value==$row['catid']) ? "selected " : '' )." value=\"{$row['catid']}\" style='background-color: #CEDEFB'>".$catname."</option>";
		 if($sub=="show_sub"){
			$dyn_menu .= form_select_sub($row['catid'],$cat_value,$sql_table);
		 }
	}
	$dyn_menu .="
	</select>\n\n ";
	return "$dyn_menu";
}
function form_select_sub($father,$cat_value,$sql_table,$bar="") {
	global $intro;
	
	$father = intval($father);
	
	$dyn_menu ="";
	$result = $intro->db->query("SELECT * from ".PREFIX."_".$sql_table." where father=$father order by catid ASC");
	while ($row = $intro->db->fetch_assoc($result)){
		//var_dump($row);
	   $dyn_menu .="
		<option ".(($cat_value==$row['catid']) ? "selected " : '' )." value=\"{$row['catid']}\" style='background-color: #CEDEFB'>$bar--------|{$row['catname']}</option>";
		$dyn_menu .= form_select_sub($row['catid'],$cat_value,$sql_table,"--------");
	}
	return " $dyn_menu";
}



function form_select2($html_name,$cat_value,$sql_table, $where='',$sub='') {
	global $intro;
	
	$dyn_menu ="
	<select name=\"$html_name\">\n";
	$dyn_menu .="
		<option value=\"0\" selected> ".$intro->lang['choose']." </option>\n";
	$result = $intro->db->query("SELECT * from ".PREFIX."_".$sql_table." where father=0 $where order by catid ASC");
	while ($row = $intro->db->fetch_assoc($result)){
		//var_dump($row);
	   $dyn_menu .="
		<option ".(($cat_value==$row['catid']) ? "selected " : '' )." value=\"{$row['catid']}\" style='background-color: #CEDEFB'>".$row['catname_'.$intro->maa->lang]."</option>";
		 if($sub=="show_sub"){
			$dyn_menu .= form_select_sub2($row['catid'],$cat_value,$sql_table);
		 }
	}
	$dyn_menu .="
	</select>\n\n ";
	return "$dyn_menu";
}
function form_select_sub2($father,$cat_value,$sql_table,$bar="") {
	global $intro;
	
	$father = intval($father);
	
	$dyn_menu ="";
	$result = $intro->db->query("SELECT * from ".PREFIX."_".$sql_table." where father=$father order by catid ASC");
	while ($row = $intro->db->fetch_assoc($result)){
		//var_dump($row);
	   $dyn_menu .="
		<option ".(($cat_value==$row['catid']) ? "selected " : '' )." value=\"{$row['catid']}\" style='background-color: #CEDEFB'>$bar--------|".$row['catname_'.$intro->maa->lang]."</option>";
		$dyn_menu .= form_select_sub($row['catid'],$cat_value,$sql_table,"--------");
	}
	return " $dyn_menu";
}



function brand_form_select($html_name,$cat_value,$sql_table, $where='',$sub='') {
	global $intro;
	
	$dyn_menu ="
	<select name=\"$html_name\" id=\"$html_name\">\n";
	$dyn_menu .="
		<option value=\"0\" selected> ".$intro->lang['choose']." </option>\n";
	$result = $intro->db->query("SELECT * from ".PREFIX."_".$sql_table." where father=0 $where order by brand_id,prod_cat_type,catname ASC");
	while ($row = $intro->db->fetch_assoc($result))
	{
	   $dyn_menu .="
		<option class=\"{$row['brand_id']} {$row['prod_cat_type']}\" ".(($cat_value==$row['catid']) ? "selected " : '' )." value=\"{$row['catid']}\" style='background-color: #CEDEFB'>{$row['catname']}</option>";
		 if($sub=="show_sub"){
			$dyn_menu .= brand_form_select_sub($row['catid'],$cat_value,$sql_table);
		 }
	}
	$dyn_menu .="
	</select>\n\n ";
	return "$dyn_menu";
}
function brand_form_select_sub($father,$cat_value,$sql_table,$bar="") {
	global $intro;
	
	$father = intval($father);
	
	$dyn_menu ="";
	$result = $intro->db->query("SELECT * from ".PREFIX."_".$sql_table." where father=$father order by catname ASC");
	while ($row = $intro->db->fetch_assoc($result))
	{
	   $dyn_menu .="
		<option class=\"{$row['brand_id']} {$row['prod_cat_type']}\" ".(($cat_value==$row['catid']) ? "selected " : '' )." value=\"{$row['catid']}\" style='background-color: #CEDEFB'>$bar--------|{$row['catname']}</option>";
		$dyn_menu .= brand_form_select_sub($row['catid'],$cat_value,$sql_table,"--------");
	}
	return "$dyn_menu";
}
?>