<?php
##########################################################################
#                                                                        #
#               Programmed by: Mohammed AbuAbed                          #
# Site: www.phptarget.net Email: maaking@gmail.com Mobile: +970598505800 #
#              MSN: m@maaking.com   Skype: maaking                       #
#        Copyright 2011 - Please don't copy or use my code.              #
#                                                                        #
##########################################################################
# Ver  1  Rev. 2
# Last update: 01/04/2011

if (!defined('CHECK_ME'))
{
        echo "Error calling script."; exit;
}

error_reporting(E_ALL);
error_reporting(1);


ini_set('display_errors', 'On');

$starttime = explode(' ', microtime());
$starttime = $starttime[1] + $starttime[0];


$ROOT_DIR = realpath(dirname(__FILE__));
$ROOT_DIR = str_replace('\\', '/', $ROOT_DIR);

$root_dir = dirname(__FILE__) . DIRECTORY_SEPARATOR;
	
define('PATH', $root_dir);
if($_GET) @extract($_GET);
if($_POST) @extract($_POST);

function exception_handler(Exception $e)
{
	switch ($e->getCode()) {
		case E_ERROR:
		$code_name = 'E_ERROR';
		break;
		case E_WARNING:
		$code_name = 'E_WARNING';
		break;
		case E_PARSE:
		$code_name = 'E_PARSE';
		break;
		case E_NOTICE:
		$code_name = 'E_NOTICE';
		break;
		case E_CORE_ERROR:
		$code_name = 'E_CORE_ERROR';
		break;
		case E_CORE_WARNING:
		$code_name = 'E_CORE_WARNING';
		break;
		case E_COMPILE_ERROR:
		$code_name = 'E_COMPILE_ERROR';
		break;
		case E_COMPILE_WARNING:
		$code_name = 'E_COMPILE_WARNING';
		break;
		case E_USER_ERROR:
		$code_name = 'E_USER_ERROR';
		break;
		case E_USER_WARNING:
		$code_name = 'E_USER_WARNING';
		break;
		case E_USER_NOTICE:
		$code_name = 'E_USER_NOTICE';
		break;
		case E_STRICT:
		$code_name = 'E_STRICT';
		break;
		case E_RECOVERABLE_ERROR:
		$code_name = 'E_RECOVERABLE_ERROR';
		break;
		default:
		$code_name = $e->getCode();
		break;
	}
	?>
	<div style="width: 700px; margin: auto; direction:ltr">
	<span style="text-align: left; background-color: #fcc; border: 1px solid #600; color: #600; display: block; margin: 1em 0; padding: .33em 6px">
	<b>Error:</b> <?=$code_name?><br />
	<b>Message:</b> <?=$e->getMessage()?><br />
	<b>File:</b> <?=$e->getFile()?><br />
	<b>Line:</b> <?=$e->getLine()?>
	<b>Trace:</b> 
	<div><table><?=$e->xdebug_message?></table></div>
	</span>
	</div>
	<?php
	//var_dump($e);
}

//set_exception_handler('exception_handler');

require($root_dir . 'intro/intro.php');
 
/* instantiate */
$intro = new intro();
$intro->main();
$intro->debug = true;

@date_default_timezone_set($intro->option['time_zone']);

require($root_dir . 'includes/functions_forms.php');
require($root_dir . 'includes/class_validation.php');
require($root_dir . 'includes/array.php');
require($root_dir . 'style/style.php');

$sql_cat = $intro->db->query("SELECT adminid,admin_name FROM ".PREFIX."_admin order by admin_name asc");
if( $intro->db->returned_rows>0){
	while($cat_row = $intro->db->fetch_assoc($sql_cat)){
		$aid = $cat_row['adminid'];
		$array['admin'][$aid] = $cat_row['admin_name'];	
	}	
}
/*********************/

$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
if (preg_match('/bot|crawl|spider|slurp/i', $userAgent)) {
    exit; // لا تسجّل البوتات
}

function getUserIP() {
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        return $_SERVER['HTTP_CF_CONNECTING_IP']; // مع Cloudflare
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ipList[0]);
    }
    return $_SERVER['REMOTE_ADDR'];
}

$ip = getUserIP();
$sql_visitor = $intro->db->query("
    SELECT ip 
    FROM ".PREFIX."_visitors 
    WHERE ip = '$ip' 
      AND date_visited >= CURDATE() 
      AND date_visited <  CURDATE() + INTERVAL 1 DAY
");
if( $intro->db->returned_rows == 0){
	  $intro->db->query("INSERT INTO maa_visitors (date_visited, ip) VALUES (NOW(), '$ip' )");
}



/***********************/
$lang = $intro->maa->lang;
if($lang == "ar")
	$sql_cat = $intro->db->query("SELECT id,name_ar FROM ".PREFIX."_countries order by name_ar asc" , '', true);
else
	$sql_cat = $intro->db->query("SELECT id,name_en FROM ".PREFIX."_countries order by name_en asc" , '', true);

while( $cat_row = $intro->db->fetch_assoc($sql_cat) )
{
	$aid = $cat_row['id'];
	
	if($lang == "ar")
		$array['country'][$aid] = $cat_row['name_ar'];	
	else
		$array['country'][$aid] = $cat_row['name_en'];
}	
$sql_cat = $cat_row = $aid = null;

//var_dump($array['admin']);

define("_ver","1.0");

function _sort_th($fild,$func,$other=''){
	global $intro;
	
	$page = $intro->input->get_post('page');
	
	if(intval($page) == 0 ) $page = 1;
	
	return "<a href=\"?page=$page&amp;order=$fild:asc&amp;$other\" title=\"".$intro->lang['asending']."\">"
		."<i class=\"icon-up-dir\"  title=\"".$intro->lang['asending']."\" /></i></a>"
		."<a href=\"?page=$page&amp;order=$fild:desc&amp;$other\" title=\"".$intro->lang['desending']."\">"
		."<i class=\"icon-down-dir\" title=\"".$intro->lang['desending']."\" /></i></a>";
}

function random_number($length){
	$random = substr(number_format(time() * rand(),0,'',''),0,$length);
	return $random;
}
function _odd_even($i){
	return ($i%2==0?'odd':'even');
}
function RandCode($type, $length = 6)
{
	if($type == 'num'){
		$letters = '01234567893';
	}else{
		$letters = '1234567893qwertyuiopasdfghjklzxcvbnm';
	}

	$s = '';
	$lettersLength = strlen($letters)-1;

	for($i = 0 ; $i < $length ; $i++)
	{
		$s .= $letters[rand(0,$lettersLength)];
	}

	return $s;
} 

 function rrmdir($dir) {
   if (@is_dir($dir)) {
     $objects = scandir($dir);
     foreach ($objects as $object) {
       if ($object != "." && $object != "..") {
         if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else @unlink($dir."/".$object);
       }
     }
     @reset($objects);
     @rmdir($dir);
   }
 } 
 
  

function _substr($text,$num_chars){
	
	$text=strip_tags($text);
	$total_chars = strlen($text);
    $after_substr = substr($text,0,$num_chars);
    $last_pos_after_substr = strrpos($after_substr," "); 
	if($num_chars > $total_chars){
		return $text;
	}elseif($num_chars == $total_chars){
		return $text;
	}elseif($num_chars < $total_chars){
		return substr($text,0,$last_pos_after_substr);
	}
} 

function error_msg($txt){

	echo"<br><br>

	<div class=err_header>Error!</div>
	<div class=err_body><b>$txt</b></div>
	<div class=err_footer><a href=\"javascript:history.go(-1)\">Back</a></div>";
        
}

function GeTheCat($table,$field,$field_id,$val) {
        global $intro;

        $result = $intro->db->query("SELECT ".$field." from ".PREFIX."_".$table." WHERE ".$field_id."='$val'");
	$row = $intro->db->fetch_assoc($result);

        return  $row[$field];
}

function GeTheAdmin($adminid) {
        global $intro;

        $result = $intro->db->query("SELECT admin_name from ".PREFIX."_admin WHERE adminid='$adminid'");
	$row = $intro->db->fetch_assoc($result);

        return  $row['admin_name'];
}

function GetPage($pid) {
  global $intro;

     $sql = $intro->db->query("SELECT the_text FROM ".PREFIX."_pages where id='$pid'");
     $row =  @mysql_fetch_array($sql);
	 
     return stripslashes($row['the_text']);
}

function ajax_change($id,$table,$field,$id_field,$value){
	
	if($value == 1){
		$n_value = 0;
		$img = "cor_16.png";
	}
	elseif($value == 0){
		$n_value = 1;
		$img = "close_16.png";
	}
	
	$html_id = $field."_".$id;
	
	return "<span id=\"$html_id\"><a data-id=\"$html_id\" class=\"global_ajax\" href=\"../../ajax.php?maa=GlobalChange&table=$table&field=$field&id_field=$id_field&id=$id&value=$n_value\" OnClick=\"return false;\"><img src=\"../../images/icons/$img\" /></a></span>";
}

	

function policy($adminid,$cur_file,$type=''){
	global $intro;

	$html_data = '';
	$adm = $intro->auth->admin_data();
	
	//max_codes
	//over_sell
	if($adm['adminid']==1) return;
	
	/*if($type == 'add')
	{
		error_msg("Sorry: you cannot add.");
		die();
	}*/


}


function intro_html_encode($string) {
	$string = str_replace("'", "&#39;", $string);
	$string = str_replace("\"", "&quot;", $string);
	$string = str_replace("&", "&amp;", $string);
	$string = str_replace("¢", "&cent;", $string);
	$string = str_replace("©", "&copy;", $string);
	$string = str_replace("÷", "&divide;", $string);
	$string = str_replace(">", "&gt;", $string);
	$string = str_replace("<", "&lt;", $string);
	$string = str_replace("µ", "&micro;", $string);
	$string = str_replace("·", "&middot;", $string);
	$string = str_replace("¶", "&para;", $string);
	$string = str_replace("±", "&plusmn;", $string);
	$string = str_replace("€", "&euro;", $string);
	$string = str_replace("£", "&pound;", $string);
	$string = str_replace("®", "&reg;", $string);
	$string = str_replace("§", "&sect;", $string);
	$string = str_replace("™", "&trade;", $string);
	$string = str_replace("¥", "&yen;", $string);
	$string = str_replace("–", "&ndash;", $string);
	$string = str_replace("—", "&mdash;", $string);
	$string = str_replace("?", "&iexcl;", $string);
	$string = str_replace("?", "&iquest;", $string);
	$string = str_replace("\"", "&quot;", $string);
	$string = str_replace("“", "&ldquo;", $string);
	$string = str_replace("”", "&rdquo;", $string);
	$string = str_replace("‘", "&lsquo;", $string);
	$string = str_replace("’", "&rsquo;", $string);
	$string = str_replace("«", "&laquo;", $string);
	$string = str_replace("»", "&raquo;", $string);
	return($string);
}

function intro_html_decode($string) {
	$string = str_replace("&#39;", "'", $string);
	$string = str_replace("&quot;", "\"", $string);
	$string = str_replace("&amp;", "&", $string);
	$string = str_replace("&cent;", "¢", $string);
	$string = str_replace("&copy;", "©", $string);
	$string = str_replace("&divide;", "÷", $string);
	$string = str_replace("&gt;", ">", $string);
	$string = str_replace("&lt;", "<", $string);
	$string = str_replace("&micro;", "µ", $string);
	$string = str_replace("&middot;", "·", $string);
	$string = str_replace("&para;", "¶", $string);
	$string = str_replace("&plusmn;", "±", $string);
	$string = str_replace("&euro;", "€", $string);
	$string = str_replace("&pound;", "£", $string);
	$string = str_replace("&reg;", "®", $string);
	$string = str_replace("&sect;", "§", $string);
	$string = str_replace("&trade;", "™", $string);
	$string = str_replace("&yen;", "¥", $string);
	$string = str_replace("&ndash;", "–", $string);
	$string = str_replace("&mdash;", "—", $string);
	$string = str_replace("&iexcl;", "?", $string);
	$string = str_replace("&iquest;", "?", $string);
	$string = str_replace("&quot;", "\"", $string);
	$string = str_replace("&ldquo;", "“", $string);
	$string = str_replace("&rdquo;", "”", $string);
	$string = str_replace("&lsquo;", "‘", $string);
	$string = str_replace("&rsquo;", "’", $string);
	$string = str_replace("&laquo;", "«", $string);
	$string = str_replace("&raquo;", "»", $string);
	return($string);
}


function get_youtube_img($url)
{
	return "http://i1.ytimg.com/vi/" . get_youtube_name($url) . "/default.jpg";
}
function get_youtube_name($url)
{

	//$regexp= "http:\/\/www\.youtube\.com\/watch\?feature=player_embedded&v=(.*)(.*)";
	//if(preg_match_all("/$regexp/siu", $url, $matches))
	//return $matches[1][0];

	parse_str( parse_url( $url, PHP_URL_QUERY ), $my_array_of_vars );
	return @$my_array_of_vars['v'];  

}

function GetExt($Filename) {
$RetVal = explode ( '.', $Filename);
return $RetVal[count($RetVal)-1];
}

function _clean($str){
	
	$str = trim($str);
	$str = strip_tags($str);
	
	return preg_replace('/[^أ-يA-Za-z0-9 ]/ui', '', $str);
}
function _css_active($action){
	global $intro;
	
	if(isset($intro->url_segments[2]) )
	{	
		return ($action==$intro->url_segments[2]?'primary':'secondary');
	}
	return 'default';
}
function watermark($TempFile,$ext,$path_with_name){
	global $intro;

	if(!file_exists($intro->option['logowater']))
	die("<h1>error: غير قادر على ايجاد الصورة المائية - تاد من وجودها في الخيارات العامة : ". $intro->option['logowater']);
	$overlay = $intro->option['logowater'];
	$opacity = $intro->option['logowater_opacity']==""?70:$intro->option['logowater_opacity'];
	$quality = $intro->option['logowater_quality']==""?90:$intro->option['logowater_quality'];
	$pos = $intro->option['logowater_pos'];
	
	define('WATERMARK_OVERLAY_IMAGE', $overlay);
	define('WATERMARK_OVERLAY_OPACITY', $opacity);
	define('WATERMARK_OUTPUT_QUALITY', $quality);
	
	$source_file_path = $TempFile;
	$output_file_path = $path_with_name;

	list($source_width, $source_height, $source_type) = @getimagesize($source_file_path);
	if ($source_type === NULL) {
		return false;
	}
	switch ($source_type) {
		case IMAGETYPE_GIF:
			$source_gd_image = @imagecreatefromgif($source_file_path);
		break;
		case IMAGETYPE_JPEG:
			$source_gd_image = @imagecreatefromjpeg($source_file_path);
		break;
		case IMAGETYPE_PNG:
			$source_gd_image = @imagecreatefrompng($source_file_path);
		break;
		default:
			return false;
	}
	$overlay_gd_image = @imagecreatefrompng(WATERMARK_OVERLAY_IMAGE);
	$overlay_width = @imagesx($overlay_gd_image);
	$overlay_height = @imagesy($overlay_gd_image);
	

	if($pos == "top_left"){
		$dst_x = 0;
		$dst_y = 0;
		$src_x = 0;
		$src_y = 0;
	}elseif($pos == "top_right"){
		$dst_x = $source_width - $overlay_width;
		$dst_y = 0;
		$src_x = 0;
		$src_y = 0;
	}elseif($pos == "bottom_left"){
		$dst_x = 0;
		$dst_y = $source_width - $overlay_width;
		$src_x = 0;
		$src_y = 0;
	}elseif($pos == "bottom_right"){
		$dst_x = $source_width - $overlay_width;
		$dst_y = $source_height - $overlay_height;
		$src_x = 0;
		$src_y = 0;
	}else{
		//center
		$dst_x = ($source_width - $overlay_width)/2;
		$dst_y = ($source_height - $overlay_height)/2;
		$src_x = 0;
		$src_y = 0;
	}
	
	@imagecopymerge($source_gd_image,$overlay_gd_image, 
	$dst_x, $dst_y, $src_x, $src_y, 
	$overlay_width, $overlay_height, WATERMARK_OVERLAY_OPACITY ); 

	@imagejpeg($source_gd_image, $output_file_path, WATERMARK_OUTPUT_QUALITY);
	@imagedestroy($source_gd_image);
	@imagedestroy($overlay_gd_image);
}

function file_icon($file){
	global $intro;
	
	$ext = substr($file, strrpos($file, ".")+1);
	$ext = strtolower($ext);

	$icon_file = "style/img/icons/".$ext.".png";
	if( file_exists( "../".$icon_file ) )
	{
		return "<img src=\"{$intro->base_url}$icon_file\">";
	}else{
		$icon_file = $intro->base_url."style/img/icons/_blank.png";
		return "<img src=\"$icon_file\">($ext)";
	}
}
function edit_url($file,$func,$args){
	global $intro;
	
	//to catch the admin flag we need to set it's value
	$intro->auth->flag = 'admin';
	
	if ($intro->auth->auth_admin() == true) {

		$url = $intro->config['base_url'].$intro->option['admin_folder']."/index.php/$file/$func".$args."&amp;ref=".urlencode($_SERVER['REQUEST_URI']);
		return "<a href=\"$url\" class=\"edit_url\">Edit</a>";
	}	
	return "";	
	
}

function getparentCAT( $id, $table, $link = "" , $separator=">" ) {
	global $intro;

	$chain = '';
	$id = intval($id);
	$result = $intro->db->query("SELECT catid, catname, father FROM ".PREFIX."_".$table." WHERE catid=$id");
	$parent = $intro->db->fetch_assoc($result);

	if ( $parent['father'] && ( $parent['father'] != $parent['catid'] ) ) 
	{
		$chain .= getparentCAT( $parent['father'], $table, $link ,$separator );
	}

	if ($link != ""){
		$chain .= "<a href=\"{$link}{$parent['catid']}\">{$parent['catname']}</a>".$separator;
	}else{
		$chain .= $parent['catname'].$separator;
	}	
	
	return $chain;
}

$sql_newscat = $intro->db->query("SELECT * FROM ".PREFIX."_products_cat order by catid desc");
if( $intro->db->returned_rows>0){
	while($cat_row = $intro->db->fetch_assoc($sql_newscat)){
		$catid = $cat_row['catid'];
		$array['newscat'][$catid] = $cat_row['catname_'.$intro->maa->lang];	
	}	
}
$array['admins'][0]='no admin';
$aql_admins = $intro->db->query("SELECT * FROM ".PREFIX."_admin order by adminid desc");
if( $intro->db->returned_rows>0){
	while($row = $intro->db->fetch_assoc($aql_admins)){
		$adminid = $row['adminid'];
		$array['admins'][$adminid] = $row['admin_name'];	
	}	
}

function get_photo($catid){
global $intro;
	$sqlalbume = $intro->db->query("SELECT * FROM ".PREFIX."_photos where catid=$catid  order by id desc  ");
	$cat_row =$intro->db->fetch_assoc($sqlalbume);
	$imgfile=$cat_row['imgfile'];
		
	return $imgfile;	
}	



$l = $intro->maa->lang;
	if($l == "ar"){
	
	$links_lang = "<div class=\"btn_changelang\">
						<a href=\"#\">AR</a><ul> <li><a href=\"#en\">EN</a></li> </ul> 
					</div>	";
	}else{
	$links_lang = "<div class=\"btn_changelang\">
						<a href=\"#\">EN</a><ul> <li><a href=\"#ar\">AR</a></li> </ul> 
					</div>";
					
	}
function minicart(){
	global $intro;
	$products_items=$data='';
	$session=session_id();
	$lang = $intro->maa->lang;
	$sql = $intro->db->query("SELECT prod.net_price,prod.name_ar,prod.name_en,prod.photo,prod.price,cart.prodid ,cart.id as cartid,cart.qty "
		." from ".PREFIX."_cart cart "
		." JOIN ".PREFIX."_products prod ON cart.prodid=prod.id "
		." where cart.sessid='$session' order by cart.id asc");
	$products_numbers=$intro->db->returned_rows;
		
		$total = 0;
		while($row = $intro->db->fetch_assoc($sql))
		{
			@extract($row);
			
			$prodName = $lang == "ar"?$name_ar:$name_en;
			
			
			$sub_total = $net_price*$qty;
			$total += $sub_total;
			
			
			$url=$intro->uri_links('products','View',$prodid,'');
			$products_items.= "<div class=\"animated_item\">
							<div class=\"clearfix sc_product\">
							<a href=\"$url\" class=\"product_thumb\"><img src=\"{$intro->base_url}uploads/news/$photo\" width=60 height=60 alt=\"\"></a>
							<a href=\"$url\" class=\"product_name\">$prodName ... </a>
							<p> ".$intro->maa->Currency_amount($sub_total)."$sub_total test</p>
							
						</div>
						</div>";	
		}
	$data="
	<span class=\"total_price py-1 header_totalprice\"  > ".$intro->maa->Currency_amount($total)." </span>
	";
	
	return $data;
}
	


$array['paymentmethods'][0]='no payment';
$aql_admins = $intro->db->query("SELECT * FROM ".PREFIX."_gateway order by gate_id asc");
if( $intro->db->returned_rows>0){
	while($row = $intro->db->fetch_assoc($aql_admins)){
		$gate_id = $row['gate_id'];
		$array['paymentmethods'][$gate_id] = $row['gate_name'];	
	}	
}
	
function get_msg($var=''){
	global $intro;
		
	$lang = $intro->maa->lang;

	$sql = $intro->db->query_fast("SELECT * from ".PREFIX."_msgs where varname='$var';");
	$row = $intro->db->fetch_assoc($sql);
	
	$msg_ar = str_replace("&#39;", "'", $row['msg_ar']);
	$msg_en = str_replace("&#39;", "'", $row['msg_en']);
	
	$msg_en = str_replace("{lang}", $lang, $msg_en);
	$msg_ar = str_replace("{lang}", $lang, $msg_ar);
	
	if($lang == 'ar')
		return stripslashes($msg_ar);
	else
		return stripslashes($msg_en);
	
}	



	
function dicount($price=0,$discount=0, $discount_percent=0){
 
 $price = $price - $discount;
 
 $newprice = $price * ((100-$discount_percent) / 100); 
 
 return $newprice;
}

function off($prodid){
		global $intro;
		
		$sql = $intro->db->query("SELECT net_price,price "
		." from ".PREFIX."_products  where id=$prodid");
		$row = $intro->db->fetch_assoc($sql);
		
		$net_price=$row['net_price'];
		$price=$row['price'];
		
		$off=( $net_price * 100 / $price )  ;
		$off=round($off);
		
		return floatval($off);
		
}
	
	

function panel_open($title = '' , $type='default'){
	
	return "
			<!--panel -->
			<div class=\"panel panel-$type\">
				".($title != "" ? "<div class=\"panel-heading\">$title</div>":'')."
				<div class=\"panel-body\">";
}
function panel_close($title = ''){
	
	return "
				</div>
				".($title != "" ? "<div class=\"panel-footer\">$title</div>":'')."
			</div>
			<!--/panel-->";
}


function msg_set($msg , $type='success')
{
	$_SESSION['msg'] = "
	<div class=\"alert bg-$type fade in\" role=\"alert\">
		<a href=\"#\" class=\"close\" data-dismiss=\"alert\">&times;</a>
		$msg
	</div>";
}
function msg_get()
{
	if(isset($_SESSION['msg']) && $_SESSION['msg'] != '')
	{
		$msg = $_SESSION['msg'];
		//فضي الرسالة بعد الطباعة
		unset($_SESSION['msg']);	
		return $msg;
	}
}

function boot_alert($msg , $type='success')
{
 return "
 <div class=\"alert bg-$type fade in\" role=\"alert\">
  <a href=\"#\" class=\"close\" data-dismiss=\"alert\">&times;</a>
  $msg
 </div>";
}
	
$sqldiscount = $intro->db->query("SELECT * from ".PREFIX."_discounts where active=1 order by id desc");
$counttdiscount = mysqli_num_rows($sqldiscount);


if($counttdiscount > 0 ){
	$rowdis = $intro->db->fetch_assoc($sqldiscount);	
		$discount_text=$rowdis['discount_text'];
		$discount_percent=$rowdis['discount_percent'];
		
		}
function discountall($discount_text=0,$discount_percent=0,$price){
	
	if($discount_text !=0){
		
		$price_view= $price - $discount_text;
		$prev_price=  $price  ;
		
	}
	
	if($discount_percent !=0){
		
		$precent= $price * ($discount_percent / 100 ) ;
		$price_view= $price - $precent;
		$prev_price=  $price  ;
		
	}
	
	$return_price=array();
	$return_price['price_view']=$price_view;
	$return_price['prev_price']=$prev_price;
	
	return $return_price;
	

}	










				
	
	
	
?>