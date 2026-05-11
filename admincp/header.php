<?php

global $intro,$maaking_ver, $sess_admin;

function fck_editor($textarea_name,$textarea_value,$rows=8,$cols=60,$style=''){
	global $intro;

	?>
	<textarea name="<?=$textarea_name?>" rows="<?=$rows?>" cols="<?=$cols?>" style="<?=$style?>"><?=stripslashes($textarea_value)?></textarea>
	<script type="text/javascript" src="<?=$intro->base_url?>ckeditor/ckeditor.js?t=B49E5BQ"></script>
	<script type="text/javascript">	
	CKEDITOR.replace('<?=$textarea_name?>', 
		{			
			"filebrowserBrowseUrl":"<?=admin_path?>images.php",
			"filebrowserImageBrowseUrl":"<?=admin_path?>images.php?type=images",
			"filebrowserFlashBrowseUrl":"<?=admin_path?>images.php?type=Flash",
			"filebrowserUploadUrl":"<?=admin_path?>images.php?command=QuickUpload&type=Files",
			"filebrowserImageUploadUrl":"<?=admin_path?>images.php?command=QuickUpload&type=Images",
			"filebrowserFlashUploadUrl":"<?=admin_path?>images.php?command=QuickUpload&type=Flash"			
		}
	);
	</script>
	<?	
}

//GetAdminData($adminid);
function AdminMenu($adminid,$type){
	global $intro;

	$adminid = intval($adminid);
	
	$admin_menu = "";
	
	if($type !=1){
		$result = $intro->db->query("SELECT * FROM ".PREFIX."_apps ap "
		." left join  ".PREFIX."_apps_policy  apc on ap.fid=apc.fid where adminid=$adminid order by w");	
	}else{
	$result = $intro->db->query("SELECT * FROM ".PREFIX."_apps where actit=1 order by w");
	}
	$x=0;
	while ($row = $intro->db->fetch_assoc($result))
	{
		$x++;
		$fid = $row['fid'];
		$title = $row['title'];
		$filename = $row['filename'];
		$fimage = $row['fimage'];
		$sub_links = explode("\n", $row['sub_links']);

		//
		$filename = str_replace(".php",'',$filename);


		$full_links = '';
		$siz= sizeof($sub_links);
		
		for ($i=0; $i < sizeof($sub_links); $i++)
		{
			list($link_title,$the_link)=explode("|",$sub_links[$i]);
			$link_title = trim($link_title);
			$the_link = str_replace(" ","",trim($the_link));
			$the_link = preg_replace('/\s+/', '', $the_link);
			if( preg_match("^/^",$the_link) )
			{
				$lllink = "$the_link";
			}else{
				$lllink = "$filename/$the_link";
			}
			$full_links .= "<li><a class=\"dropdown-item\" href=\"".admin_path."index.php/$lllink\">".(empty($intro->lang[$link_title]) ? $link_title : $intro->lang[$link_title])."</a></li>";
		}
	
			
		if(sizeof($sub_links) > 1 && $full_links != ""){
			$has_sub = "
			<ul class=\"dropdown-menu\" aria-labelledby=\"dropdown0$x\">$full_links</ul>
			";
		}else{
			$has_sub = "";
		}
		
		$title = (empty($intro->lang['app_'.$title]) ? (!empty($intro->lang[$title.'_appname'])?$intro->lang[$title.'_appname']:$title) : $intro->lang['app_'.$title]);
		
			/*	$admin_menu .= "
			<li class=\"".($has_sub!=''?'dropdown':'')." ".($intro->url_segments[1]==$filename?'active':'')."\">
				<a href=\"".admin_path."index.php/$filename/index\" class=\"dropdown-toggle\" data-toggle=\"dropdown\"> <icon class=\"$fimage mic\"></icon> ".$title." ".($has_sub!=''?'<b class="caret"></b>':'')."</a> 
				$has_sub
			</li>";*/
    	$admin_menu .= "<li class=\"nav-item ".($has_sub!=''?'dropdown':'')."\">
						<a class=\"nav-link ".($has_sub!=''?'dropdown-toggle':'')."\" 
						href=\"".($has_sub!=''?'#':"".admin_path."index.php/$filename/index")."\"
						id=\"".($has_sub!=''?"dropdown0$x":'')."\"
						".($has_sub!=''?"data-bs-toggle=\"dropdown\" aria-expanded=\"false\"":'')."
						
						>
						<i class=\"px-2 $fimage\"></i>
						".$title." 
						</a>
						$has_sub
					</li>";



	}//end while

	$menu = "
		<li class=\"nav-item \">
			<a class=\"nav-link ".($intro->url_segments[1]=='home'?'active':'')."\" href=\"".admin_path."index.php/home/index\"><i class='px-2 fa-solid fa-home'></i> {$intro->lang['home']}</a>
		</li>\n
		$admin_menu
		<li class=\"nav-item\">
			<a class=\"nav-link\" href=\"".admin_path."login.php?maa=Logout\"><i class='px-2 fa-solid fa-right-from-bracket'></i> {$intro->lang['logout']}</a>
		</li>";
	 
	return $menu;
}
 
$admin_welcome = "{$intro->lang['welcome']}  <span class='fw-bold text-danger  '>  {$sess_admin['admin_name']} </span>  
, {$intro->lang['last_login']} {$intro->lang['from']}: <span class='fw-bold text-success  '>  [{$sess_admin['ip']}] </span> 
{$intro->lang['on']}  <span class='fw-bold text-danger  '>   [{$sess_admin['lastlogin']}] </span>  ";

$add_news = $intro->admin_url('news','Form');

if($intro->lang['dir'] == 'rtl'){
	$print_lang = "<a href=\"#en\">English</a>";		
}else{
	$print_lang = "<a href=\"#ar\">عربي</a>";
}


		$res_today = $intro->db->query("
				SELECT COUNT(DISTINCT ip) AS today_visitors
				FROM ".PREFIX."_visitors
				WHERE DATE(date_visited) = CURDATE()
			");
			$row_today = $res_today->fetch_assoc();
			$today_visitors = $row_today['today_visitors'];
			
			
			
include('style/header.html');

?>