<?PHP
define('CHECK_ME', true);
define('ADMIN_FILE', true);
include('../abc.php');
include ("../includes/class_upload.php");
$intro->admin_folder = $intro->option['admin_folder']."/";
$intro->auth->flag = 'admin';

if ( $intro->auth->auth_admin() ) {

$maa=$intro->input->get_post("maa");


if(isset($_GET['CKEditorFuncNum']) && $_GET['CKEditorFuncNum'] != "")
{
	$_SESSION['CKEditorFuncNum'] = $_GET['CKEditorFuncNum'];
	unset($_SESSION['for_id']);
}
if(isset($_GET['for_id']) && $_GET['for_id'] != "")
{
	$_SESSION['for_id'] = $_GET['for_id'];
	unset($_SESSION['CKEditorFuncNum']);
}
$for_id = isset($_SESSION['for_id'])&&$_SESSION['for_id']!=""?$_SESSION['for_id']:"";
$for_ck = isset($_SESSION['CKEditorFuncNum'])&&$_SESSION['CKEditorFuncNum']!=""?$_SESSION['CKEditorFuncNum']:"";

$img_path = "../uploads/news/";


?>
<!DOCTYPE html>
<html dir="rtl">
<head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Gallery Library</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width">

      
         <link rel="stylesheet" href="<?=$intro->base_url?>style/css/style.css"  >
         <link rel="stylesheet" href="<?=$intro->base_url?>style/css/style_ar.css"  >
	
		
		<script src="<?=$intro->base_url?>style/js/jquery-3.4.1.min.js"></script>
		<script src="<?=$intro->base_url?>style/js/source/jquery.fancybox.pack.js"></script>
		<link  href="<?=$intro->base_url?>style/js/source/jquery.fancybox.css" rel="stylesheet" media="screen" />
        <style>
            body {
                padding-top: 0px;
                padding-bottom: 0px;
				background: #fff;
            }
        </style>

<script>

function jsdel(url) {
    var answer = confirm("Are you sure you want to delete?")
     if (answer){
         window.location = ""+ url +"";
     }
}

function insertBox(file,imgID)
{
	<? if($for_id != ""){ ?>

	$( "input[name='<?=$for_id?>']", window.opener.document ).val( ""+file );
	$( "#preview_<?=$for_id?>", window.opener.document ).html(''
	+ '<span><a class="fancy" href="<?=$intro->base_url?>'+file+'">'
	+ '<img src="<?=$intro->base_url?>uploads/news/'+file+'" style="max-height:100px;"></a></span>');
	
	<? } else { ?>
	var full_file = "<?=$intro->base_url?>uploads/news/"+file;
	window.opener.CKEDITOR.tools.callFunction(<?=$for_ck?>,full_file);
	<? } ?>
	window.close();
}


</script>

<script>
$(document).ready(function() {
	
	$(".fancy").fancybox({
		'overlayShow'	: false,
		/*'transitionIn'	: 'none',
		'transitionOut'	: 'none',*/
		'type'			: 'iframe'/*ajax*/,
		'width'			: 750,
		'height'		: 600,
		'scrolling'   	: 'no',
		'showNavArrows'   : false,  
		helpers : false,
		cache	: false,
	});
		
});
</script>
</head>
<body>

<?php

function navnav(){
         global $db, $prefix,$id,$ismain,$intro;
		  
echo "
<div class=\"container-fluid mt-3\">
		<div class=\"row \">
			<div class='col-6 p-0 text-center'><h5 class=\" position-relative text-uppercase mb-3\"><span class=\"px-5\">
				<a href=images.php class='font-weight-bold'>  البداية </a>  || 
				<a href=images.php?maa=view_cat&imgcatid= class='font-weight-bold'> صور بلا تصنيف</a>
			
			</span></h5></div>
			<div class='col-md-6 text-center'><h5 class=\" position-relative text-uppercase mb-3\">
			<span class=\" px-5\"><a href=images.php?maa=load_cat class='font-weight-bold'> عرض التصنيف</a></span></h5></div>
			
			<div class='col-md-7 p-0 m-0'>
			<div class=\"card\">
			  <div class=\"card-header bg-success text-white \">
				إضافة صورة جديدة
			  </div>
			  <div class=\"card-body\">
				
				<form method=\"POST\" dir=rtl enctype=\"multipart/form-data\" action=\"images.php\">
					  <table border=0 class='table'>
					  <tbody>
					  <tr>
						  <td style='width:200px;'>ملف الصورة</td>
						  <td>&nbsp;<input type=\"file\" name=\"imgfile\" ></td>
					  </tr>
					  <tr>
						  <td>&nbsp;اسم الصورة</td>
						  <td>&nbsp;<input type=\"text\" name=\"imgname\" ></td>
					  </tr>

						<tr>
						  <td>&nbsp;</td>
						  <td>&nbsp;<input type=\"checkbox\" name=\"islogo\" value=\"1\" >
							  اضافة شعار الموقع على الصورة</td>
					  </tr>
					  <tr>
						  <td>&nbsp;التصنيف</td>
						  <td> <select name='imgcat'>
							   <option value='0' selected>اختر التصنيف</option>\n";
							   $result = $intro->db->query("SELECT * from ".PREFIX."_images_cat");
							  
							   while($myrow = $intro->db->fetch_assoc($result)){
							   echo "<option  value=$myrow[imgcatid]>$myrow[imgcatname]</option>\n";
							   } echo "</select>
						  </td>
					   </tr>
					   <tr>
						   <td>&nbsp;</td>
							<td>
								 <input type=\"hidden\" name=\"maa\" value=\"do_imgAdd\">
								 <input type=\"hidden\" name=\"id\" value=\"$id\">
								 <input type=\"submit\" class='btn btn-primary' value=\"أضف صورة\" name=\"B1\"></p>
							</td>
					   </tr>
					   </tbody>
					   </table>
			</form>
			  </div>
			</div>
			
			</div>
			<div class='col-md-5 p-0 m-0'>
			<div class=\"card\">
				  <div class=\"card-header bg-success text-white\">
					إضافة تصنيف جديد
				  </div>
				  <div class=\"card-body\">
					<form method=\"POST\" action=\"images.php\">
					<table border=0 class='table'>
					  <tbody>
					 <tr>
						<td style='width:150px;' > إسم التصنيف :</td>
						<td><input type=\"text\" name=\"imgcatname\" class='form-control'></td>
					</tr>
					 <tr>
						<td> <input type=\"hidden\" name=\"maa\" value=\"do_imgAdd_Cat\"> </td>
						<td><input type=\"submit\" value=\"أضف تصنيف\" class='btn btn-primary mt-1' name=\"B1\"></td>
					</tr>
					
					</tbody> 
					 </table> 
						
					</form>
					<hr>
					<h5>البحث عن الصور</h5>
					
					<form action=\"\" method=\"POST\">"
					  ."<input  type=\"text\" name=\"query\" value=\"\">"
					  ."<br><select name='imgcat'>
							   <option value='0' selected>البحث في جميع التصنيفات</option>";
							   $result = $intro->db->query("SELECT * from ".PREFIX."_images_cat");
							   while($myrow = $intro->db->fetch_assoc($result)){
							   echo "<option  value=$myrow[imgcatid]>$myrow[imgcatname]</option>";
							   } echo "</select><br>";
					  echo "<input type=\"hidden\" name=\"maa\" value=\"do_search\">"
					  ."<input type=\"submit\" class='btn btn-primary mt-1' value=\" --  بحث  -- \"></form>
		
				  </div>
				</div>
			</div>	
		</div>
</div>";

}

function index(){
     global  $imgfile,$imgname,$imgcat,$db,$prefix,$img_path,$lastid,$id,$ismain,$intro;

     navnav();

}

function do_imgAdd(){
     global  $imgfile,$imgname,$imgcat,$db,$prefix,$img_path,$islogo,$lastid,$id,$ismain,$intro;

     navnav();
	
	$imgname=$intro->input->get_post("imgname");
	$id=$intro->input->get_post("id");
	$lastid=$intro->input->get_post("lastid");
	$ismain=$intro->input->get_post("ismain");
	$imgcat=$intro->input->get_post("imgcat");
	$islogo=$intro->input->get_post("islogo");
	
	
    /* if (!$imgfile){
     echo "<h3><font color=red>خطأ: <br>";
     echo "الحقل فارغ";
     exit();
     }*/
     if ($imgcat==0){
     echo "<h3><font color=red>خطأ: <br>";
     echo " لا يمكن اضافة هذه الصورة، يجب أن تختر تصنيف لها.";
     exit();
     }
	 
	 
	$upload_dir = $img_path.$imgcat."/";

	if(!is_dir($upload_dir)){
	    @mkdir($upload_dir);
	}
	
	$file=$_FILES['imgfile'];
	

	$thumbsize = 1500;

	$date_now = date("d-m-Y");

	$TempFile = $_FILES ['imgfile']['tmp_name'];
	$FileName2 = $_FILES ['imgfile']['name'];
	$file_size = $_FILES["imgfile"]["size"];
    $file_type = $_FILES["imgfile"]["type"];
	$TheExt = GetExt($FileName2);
	$TheExt = strtolower($TheExt);

	$FILENAME2= rand(0,999999999);
	$ff = date("Y-m-d")."_".$FILENAME2.".".$TheExt;
	
	if($islogo == 1){
	  watermark($TempFile,$TheExt,$upload_dir.$ff);
	}else{

		$imgsize = getimagesize ($TempFile);
		if ($imgsize[0] > $thumbsize){
	
			if($file_size){

				if($file_type == "image/pjpeg" || $file_type == "image/jpeg"){
				$im = imagecreatefromjpeg($TempFile);
				}elseif($file_type == "image/x-png" || $file_type == "image/png"){
				$im = imagecreatefrompng($TempFile);
				}elseif($file_type == "image/gif"){
				$im = imagecreatefromgif($TempFile);
				}
				list($width, $height) = getimagesize($TempFile);
				$imgratio=$width/$height;
				if ($imgratio>1){
				$newwidth = $thumbsize;
				$newheight = $thumbsize/$imgratio;
				}else{
				$newheight = $thumbsize;
				$newwidth = $thumbsize*$imgratio;
				}
				$newim = ImageCreateTrueColor($newwidth, $newheight);
				imagecopyresized($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
				ImageJpeg($newim,$upload_dir.$ff,95);
				ImageDestroy($newim);
				ImageDestroy($im);
			}//if file_size
		}else{
			 move_uploaded_file ($TempFile, $upload_dir.$ff);
		}
		  
	}//end else logo
	
	if($TheExt == 'swf'){
	 move_uploaded_file ($TempFile, $upload_dir.$ff);
	}


	$sql =  $intro->db->query("INSERT INTO ".PREFIX."_images "
	." (imgname,imgfile,imgcat) VALUES "
	." ('$imgname','$imgcat/$ff','$imgcat')") or die ("Error Adding Image: ". mysql_error());

	echo "<br>File <a href={$upload_dir}$ff>$ff</a> uploaded!<br>";
	
	echo "<br> تم رفع الصورة - الرجاء الانتظار ... ";
	echo "<meta http-equiv=Refresh content=0;url=?maa=view_cat&imgcatid=$imgcat>";
}



function file_newname($path, $filename){

    if ($pos = strrpos($filename, '.')) {
           $name = substr($filename, 0, $pos);
           $ext = substr($filename, $pos);
    } else {
           $name = $filename;
    }

    $newpath = $path.'/'.$filename;
    $newname = $filename;
    $counter = 0;
    while (file_exists($newpath)) {
           $newname = $name .'_'. $counter . $ext;
           $newpath = $path.'/'.$newname;
           $counter++;
     }

    return $newname;
}
////////////////////////////////////////////////////////////////////////////////
/*----------------------------------------------------------------------------*/
// Edit image
/*----------------------------------------------------------------------------*/
////////////////////////////////////////////////////////////////////////////////
function edit_img() {
	global $imgid,$img_path,$db,$intro;
        $imgid = intval($imgid);

        navnav();

        $result2 = $intro->db->query("SELECT * from ".PREFIX."_images where imgid='$imgid'") or die (mysql_error());
        $imgrow = $intro->db->fetch_assoc($result2);
		@extract($imgrow);
	
       
echo "  
<div class=\"container-fluid\">
		<h5>تعديل صورة</h5>
		<img src=\"$img_path$imgfile\" style='max-width:150'><br>
		<span dir=ltr>$img_path + $imgfile</span>
</div>

 <form method=\"POST\" enctype=\"multipart/form-data\" action=\"images.php\">
<table class='table'>
<tbody>
<tr>
    <td>&nbsp;ملف الصورة</td>
    <td>&nbsp;<input type=\"file\" name=\"imgfile\" size=\"20\"></td>
</tr>
<tr>
    <td>&nbsp;اسم الصورة</td>
    <td>&nbsp;<input type=\"text\" name=\"imgname\" value=\"$imgrow[imgname]\" size=\"50\"></td>
</tr>
<tr>
   <td>&nbsp;التصنيف</td>
   <td> <select name=\"imgcat\">";
	$w_sql = $intro->db->query("SELECT * from ".PREFIX."_images_cat");
	$sel = "";
	echo "<option value=\"0\">--التصنيف--</option>\n";
        while($myrow = $intro->db->fetch_assoc($w_sql)){
    	    if ($myrow['imgcatid']==$imgrow['imgcat']) { $sel = "selected "; }
        	echo "<option $sel value=\"{$myrow['imgcatid']}\">{$myrow['imgcatname']}</option>\n";
		$sel = "";
	}

        echo "</select>
   </td>
</tr>
<tr>
     <td>&nbsp;</td>
     <td>
         <input type=\"hidden\" name=\"maa\" value=\"do_edit_img\">
         <input type=\"hidden\" name=\"imgid\" value=\"$imgid\">
         <input type=\"hidden\" name=\"imgfiled\" value=\"$imgfile\">
         <input type=\"submit\" value=\"تعديل صورة\" class='btn btn-primary' name=\"B1\"></p></form>
     </td>
</tr></tbody>
</table>";
}

function do_edit_img(){
	global $imgid,$imgcat,$img_path,$db,$imgname,$imgfiled,$intro;



	$upload_dir = $img_path;

	$imgfilec=$_FILES['imgfile']['tmp_name'];
	$new_file = $_FILES['imgfile'];
	$file_name = $new_file['name'];
	$file_tmp = $new_file['tmp_name'];
	$file_size = $new_file['size'];

	if (@is_uploaded_file($imgfilec))
	{
	
		@unlink($upload_dir."$imgfiled");
		$TheExt = GetExt($file_name);
		$FILENAME2 = Date("Y-m-d")."_".time();
		$file_name= $FILENAME2.".".$TheExt;
		$upload_dir2 = "$img_path$imgcat/";
		
		if(!is_dir($upload_dir2)){
			@mkdir($upload_dir2);
		}			
		@move_uploaded_file($file_tmp,$upload_dir.$imgcat."/$file_name");
		$file_name = $file_name;			
		$result = $intro->db->query("UPDATE ".PREFIX."_images SET imgfile='$imgcat/$file_name' WHERE imgid='$imgid'") or die ("Error Adding Cat: ". mysql_error());

	}
	$result = $intro->db->query("UPDATE ".PREFIX."_images SET imgname='$imgname',imgcat='$imgcat' WHERE imgid='$imgid'") or die ("Error Adding Cat: ". mysql_error());

		echo "<font size=3> <center>تم التعديل</center></font>";
	
	 echo "<meta http-equiv=Refresh content=1;url=images.php>";
}


////////////////////////////////////////////////////////////////////////////////
/*----------------------------------------------------------------------------*/
// add categories
/*----------------------------------------------------------------------------*/
////////////////////////////////////////////////////////////////////////////////

function do_imgAdd_Cat(){

   global  $imgcatname,$intro,$intro,$intro;

     navnav();
	 $imgcatname=$intro->input->get_post("imgcatname");
	
     if (!$imgcatname){
     echo "الحقل فارغ";
     exit();
     }
   $result = $intro->db->query("INSERT INTO ".PREFIX."_images_cat (imgcatname) VALUES ('$imgcatname')") or die ("Error Adding Cat: ". mysql_error());
   echo "تم اضافة تصنيف بنجاح";
   
   echo "<meta http-equiv=Refresh content=1;url=images.php?maa=load_cat>";

}
////////////////////////////////////////////////////////////////////////////////
/*----------------------------------------------------------------------------*/
// edit Categories
/*----------------------------------------------------------------------------*/
////////////////////////////////////////////////////////////////////////////////
function edit_cat(){
global $admin,$img_path,$db,$imgcatid,$intro;

$result = $intro->db->query("SELECT * from ".PREFIX."_images_cat WHERE imgcatid='$imgcatid'");
        $myrow = $intro->db->fetch_assoc($result);
         navnav();
         
         
   echo "<form method=\"POST\" action=\"images.php\">

	<input type=\"text\" name=\"imgcatname\" value=\"$myrow[imgcatname]\"size=\"20\"><br>
        <input type=\"hidden\" name=\"imgcatid\" value=\"$imgcatid\">
        <input type=\"hidden\" name=\"maa\" value=\"do_edit_cat\">
        <input type=\"submit\" value=\"تعديل\" name=\"B1\"></form>";

}


function do_edit_cat(){
	global $admin,$img_path,$db,$imgcatid,$imgcatname,$intro;

	$result = $intro->db->query("UPDATE ".PREFIX."_images_cat SET imgcatname='$imgcatname' WHERE imgcatid='$imgcatid'") or die ("Error Adding Cat: ". mysql_error());

   echo "<meta http-equiv=Refresh content=0;url=images.php?maa=load_cat>";

}

////////////////////////////////////////////////////////////////////////////////
/*----------------------------------------------------------------------------*/
// View Categories
/*----------------------------------------------------------------------------*/
////////////////////////////////////////////////////////////////////////////////
function load_cat(){
        global $intro;


        navnav();
        
      
		echo "<br>
		<center><font size=2>كل التصنيفات </font></center>
		<table class=\"table\">
		  <thead class='bg-dark text-white'>
			<tr>
			  <th scope=\"col\">التصنيف</th>
			  <th scope=\"col\">عدد الصور</th>
			  <th scope=\"col\">خيارات</th>
			</tr>
		  </thead>
		  <tbody>";
		
		$result = $intro->db->query("SELECT * from ".PREFIX."_images_cat order by imgcatname asc");
        while($myrow = $intro->db->fetch_assoc($result)){

           $result2 = $intro->db->query("SELECT imgcat from ".PREFIX."_images where imgcat='$myrow[imgcatid]'");
           $num = mysqli_num_rows($result2);
            echo "<tr> 
					<td><a href=?maa=view_cat&imgcatid=$myrow[imgcatid]>$myrow[imgcatname] </a></td>
					<td>$num</td>
					<td><a href=?maa=edit_cat&imgcatid=$myrow[imgcatid]>تعديل </a></td>
				</tr>";

        }
		echo "</tbody></table>";

}

////////////////////////////////////////////////////////////////////////////////
/*----------------------------------------------------------------------------*/
// view a single cat.
/*----------------------------------------------------------------------------*/
////////////////////////////////////////////////////////////////////////////////
function view_cat(){
        global $page, $prefix, $db,  $img_path, $imgcatid,$intro;

        navnav();
		$page=intval($intro->input->get('page'));
		if($page==0) $page=intval($intro->url_segments[1]);
        $artperpage = "30";

        if (!isset($page) or $page=="") $page=1;

        $nexlimit = $page * $artperpage - $artperpage;
        $result = $intro->db->query("SELECT * from ".PREFIX."_images where imgcat='$imgcatid' ORDER BY imgid DESC limit $nexlimit,$artperpage");
        $totrows = $intro->db->returned_rows;
	   $resultnumm = $intro->db->query("SELECT * from ".PREFIX."_images where imgcat='$imgcatid'");
       
        $totalrows = $intro->db->returned_rows;

        //Custom Table Stsrt//
        $cols = 3;
        $i =1;
        echo "<div style=\"overflow:auto;direction:rtl;height:300px;\"><table border=\"1\" class=\"gallery\" cellpadding=\"4\" cellspacing=\"4\" width=\"100%\" id=\"table1\" bordercolor=\"#04599F\" bgcolor=\"#F8F8F8\">"
             ."<tr>";

        while($myrow = $intro->db->fetch_assoc($result)){
            $imgid = $myrow['imgid'];
            $imgfile = $myrow['imgfile'];
            $imgname = $myrow['imgname'];
            $sid = $myrow['sid'];
            $mainimg  = $myrow['mainimg'];
            $imghome = $myrow['imghome'];

				
		  $mybox = "<div><a class=\"fancy\" href=\"$img_path/$myrow[imgfile]\">
				<img src=\"$img_path/$myrow[imgfile]\" width=\"150\" height=\"100\" alt=\"$imgname\" /></a></div>
				
				<div class=\"image_name\">$imgname</div>
						<div class=\"links\">											
						[<a href=\"javascript:insertBox('$imgfile','$imgid');\">جلب</a> - 
						[<a href=images.php?maa=edit_img&imgid=$imgid>تعديل</a>] 
						- [<a href=\"javascript:;\" onClick=\"cf=confirm('هل أنت متأكد من عملية الحذف؟');if (cf)window.location='images.php?maa=del_img&imgid=$imgid&imgfile=$imgfile&catid=$imgcatid'; return false;\">حذف</a>]
						</div>";

                if (is_int($i / $cols)){
                    echo "<td width='200' align='center'>$mybox</td></tr><tr>";
                }else{
                    echo "<td width='200' align='center'>$mybox</td>";
                }
             $i++;
          //end if
       }//end while
       echo "</tr></table></div>";
       //Custom Table End//

       $pages  = "";
       $totpages = ceil($totalrows/$artperpage);
       for ($i=1;$i<=$totpages;$i++) {
	    if ($i==$page) {
		$pages .= " $i ";
	    } else {
		$pages .= "[ <a href=\"images.php?maa=view_cat&imgcatid=$imgcatid&page=$i\">$i</a> ]";
	    }
       }
       echo "<br><center>الصفحات: $pages <br>";
}


////////////////////////////////////////////////////////////////////////////////
/*----------------------------------------------------------------------------*/
// Delete image
/*----------------------------------------------------------------------------*/
////////////////////////////////////////////////////////////////////////////////
function del_img(){
	global $imgfile, $imgid, $intro, $catid, $img_path;
	
	

	 unlink($img_path."$imgfile");

	$result = $intro->db->query("DELETE from ".PREFIX."_images where imgid='$imgid'");
	echo"<br><h2><font color=red>The File ($img_path$imgfile) was deleted!</font><br>
	<br>تم الحذف، انتظر</h2>";
	echo "<meta http-equiv=Refresh content=1;url=images.php>";
}

////////////////////////////////////////////////////////////////////////////////
/*----------------------------------------------------------------------------*/
// Search
/*----------------------------------------------------------------------------*/
////////////////////////////////////////////////////////////////////////////////

function do_search(){
       global $query, $imgcat, $page, $prefix, $intro,  $img_path;

        navnav();

        $rows_per_page = "30";

        if (!isset($page) or $page=="") $page=1;

        $nexlimit = $page * $rows_per_page - $rows_per_page;
        if ($imgcat==0){
           $result = $intro->db->query("SELECT * from ".PREFIX."_images where imgname LIKE '%$query%' ORDER BY imgid DESC limit $nexlimit,$rows_per_page");
           $resultnumm = $intro->db->query("SELECT * from ".PREFIX."_images where imgname LIKE '%$query%'");
        }else{
           $result = $intro->db->query("SELECT * from ".PREFIX."_images where imgname LIKE '%$query%' AND imgcat='$imgcat' ORDER BY imgid DESC limit $nexlimit,$rows_per_page");
           $resultnumm = $intro->db->query("SELECT * from ".PREFIX."_images where imgname LIKE '%$query%' AND imgcat='$imgcat'");
        }
        $totrows = mysqli_num_rows($result);
        $totalrows = mysqli_num_rows($resultnumm);
           echo "<div style=\"overflow:auto;direction:rtl;height:300px;\"><hr><center><font color=blue><b>نتائج البحث ($totalrows) نتيجة</b></font></center>";
           if("$totrows" == 0){
              echo "<center><font color=red>لايوجد نتائج</font><br><br>";
              echo "<br><a href=javascript:history.go(-1)>عودة </a>";
              exit();
           }
        //Custom Table Stsrt//
        $cols = 3;
        $i =1;
        echo "<table border=\"1\"   class=\"gallery\"   cellpadding=\"4\" cellspacing=\"4\" width=\"100%\" id=\"table1\" bordercolor=\"#04599F\" bgcolor=\"#F8F8F8\">"
             ."<tr>";

        while($myrow = $intro->db->fetch_assoc($result)){
            $imgid = $myrow['imgid'];
            $imgfile = $img = $myrow['imgfile'];
            $imgname = $myrow['imgname'];
            $imgcatid = $myrow['imgcat'];
				
				
             $mybox = "<div><a class=\"fancy\" href=\"$img_path/$myrow[imgfile]\">
				<img src=\"$img_path$imgfile\" width=\"150\" height=\"100\" alt=\"$imgname\" /></a></div>
				
				<div class=\"image_name\">$imgname </div>
						<div class=\"links\">						
						[<a href=\"javascript:insertBox('$imgfile','$imgid');\">جلب</a> - 
						[<a href=images.php?maa=edit_img&imgid=$imgid>تعديل</a>] 
						- [<a href=\"javascript:;\" onClick=\"cf=confirm('هل أنت متأكد من عملية الحذف؟');if (cf)window.location='images.php?maa=del_img&imgid=$imgid&imgfile=$imgfile&catid=$imgcatid'; return false;\">حذف</a>]
						</div>";

                if (is_int($i / $cols)){
                    echo "<td width='200' align='center'>$mybox</td></tr><tr>";
                }else{
                    echo "<td width='200' align='center'>$mybox</td>";
                }
             $i++;
       
       }//end while
       echo "</tr></table></div>";

       echo "<center>".pagination3("images.php?maa=do_search&query=$query&imgcat=$imgcat", $totalrows, $rows_per_page, $page)."</center>";
	   
	   

}

////////////////////////////////////////////////////////////////////////////////
/*----------------------------------------------------------------------------*/
// Switch
/*----------------------------------------------------------------------------*/
////////////////////////////////////////////////////////////////////////////////



switch($maa) {


  case "do_search":
             do_search();
             break;
             
  case "nooon":
             nooon();
             break;
             

       case "big_mama":
             big_mama();
             break;
             
             
             
        case "edit_img":
             edit_img();
             break;

       case "do_edit_img":
             do_edit_img();
             break;

       	case "edit_cat":
             edit_cat();
             break;
       
      	case "do_edit_cat":
             do_edit_cat();
             break;


        case "view_cat":
             view_cat();
             break;


        case "load_cat":
             load_cat();
             break;
             
             
       	case "load_img":
             load_img();
             break;
        
        case "imgAdd":
             imgAdd();
             break;
       	case "do_imgAdd":
             do_imgAdd();
             break;
       	case "imgAdd_Cat":
             imgAdd_Cat();
             break;
     	case "do_imgAdd_Cat":
             do_imgAdd_Cat();
             break;

        case "del_img":
             del_img();
             break;

       default:
             index();
             break;
}

}else{
      echo " <meta charset=\"utf-8\"> لست مخول  بالتواجد هنا  ";
      exit();
}

?>