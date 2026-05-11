<?php
define('CHECK_ME', true);

include('../abc.php');

function GlobalChange(){
	global $request,$db;
	
	//$id,$table,$field,$value
	$table = $_GET['table'];
	$field = $_GET['field'];
	$id_field = $_GET['id_field'];
	$value = $_GET['value'];
	$id = $_GET['id'];
	
	mysql_query("update $table set $field='{$value}' where {$id_field}=$id");
	
	
	if($value == 1){
		$n_value = 0;
		$img = "cor_16.png";
	}
	elseif($value == 0){
		$n_value = 1;
		$img = "close_16.png";
	}
	
	$html_id = $field."_".$id;
	
	echo "<a data-id=\"$html_id\" class=\"global_ajax\" href=\"ajax.php?maa=GlobalChange&table=$table&field=$field&id_field=$id_field&id=$id&value=$n_value\" OnClick=\"return false;\"><img src=\"../../images/icons/$img\" /></a>";
	
}

switch($_REQUEST['maa']){

	 default:
	 //Main();
	 break;

	 case "GlobalChange": GlobalChange(); break;

}	 
?>