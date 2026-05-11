<?PHP

#base file code
define("ADMIN_FILE", true);
define('CHECK_ME', true);
include ("../abc.php");

$intro->auth->flag = 'admin';

$cur_file = basename(__file__);
if ($intro->auth->auth_admin()) {
global $intro;
 $maa=$intro->input->get_post("maa");
//if($intro->auth->auth_module($cur_file) == true){
//$adminid = GetAdminID();
#end base file code

echo "<html dir=rtl>\n"
     ."<head>\n"
     ."<meta http-equiv=\"content-language\" content=\"en-us\">\n"
     ."<meta name=\"generator\" content=\"microsoft frontpage 6.0\">\n"
     ."<meta name=\"progid\" content=\"frontpage.editor.document\">\n"
     ."<meta name=\"author\" content=\"Mohammed Ahmed\">\n\n"
     ."<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\">\n\n"
     ."<title>تحميل</title>";
?>
<script type="text/javascript">
function jsdel(url) {
    var answer = confirm("Are you sure you want to delete?")
     if (answer){
         window.location = ""+ url +"";
     }
}


       function insertFile(file) {
             opener.document.newsf.image1.value = (file);
             window.close();
       }

	function insertBox(frm_name,frm_field,file) {

        var ToAdd = "\n\n[img]"+file+"[/img]\n\n";

        opener.document.frm_name.frm_field.value += ToAdd;

        var addImg = confirm("هل تريد جلب صورة أخرى؟");
        if (addImg == false) {
                 window.close();
                 opener.document.frm_name.frm_field.focus();
        }
    }

</script>

<?php
echo "<link rel=\"stylesheet\" href=\"images/style.css\" type=\"text/css\">\n"
     ."</head>\n\n\n\n\n"
     ."<body topmargin=\"10\" leftmargin=\"10\" rightmargin=\"10\" bgcolor=\"#E9F1FE\">\n\n\n";

function navnav(){
         global $db, $prefix, $f, $p, $type, $prev, $folder,$intro;
         
$type=$intro->input->get_post('type');
	$p=$intro->input->get_post("p");
	$f=$intro->input->get_post("f");
	$path=$intro->input->get_post("path");
	$imgcat=$intro->input->get_post("imgcat");
	$imgname=$intro->input->get_post("imgname");

echo "<!-- image add -->
<fieldset style='border:1px solid #800000; padding:2px; '>
<legend><b>1 - رفع ملف جديد</legend>
<form method=\"POST\" enctype=\"multipart/form-data\" action=\"\">
      <table border=0>
      <tr>
          <td>&nbsp;الملف : </td>
          <td>&nbsp;<input type=\"file\" name=\"file\" size=\"20\"> </td>
      </tr>
       <tr>
           <td>&nbsp;</td>
            <td>
                 <input type=\"hidden\" name=\"maa\" value=\"do_imgAdd\">
                 <input type=\"hidden\" name=\"f\" value=\"$f\">
                 <input type=\"hidden\" name=\"p\" value=\"$p\">
                 <input type=\"hidden\" name=\"folder\" value=\"$folder\">
				 <input type=\"hidden\" name=\"type\" value=\"$type\">
                 <input type=\"submit\" value=\" تحميل  \" name=\"B1\"></p></form>
            </td>
       </tr>
       </table>
       </fieldset>";
       
       
echo "<!-- image add -->
<fieldset style='border:1px solid #800000; padding:2px; '>
<legend><b> 2 - جلب الملف من رابط</legend>
<form method=\"POST\" enctype=\"multipart/form-data\" action=\"\">
      <table border=0>
      <tr>
          <td>&nbsp;ألصق الرابط هنا : </td>
          <td>&nbsp;<input type=\"text\" name=\"link_url\" size=\"60\"> </td>
      </tr>
       <tr>
           <td colspan=2 align=center>
                 <input type=\"hidden\" name=\"maa\" value=\"from_url\">
                 <input type=\"hidden\" name=\"f\" value=\"$f\">
                 <input type=\"hidden\" name=\"p\" value=\"$p\">
				 <input type=\"hidden\" name=\"folder\" value=\"$folder\">
				 <input type=\"hidden\" name=\"type\" value=\"$type\">
                 <input type=\"submit\" value=\" جلب  \" name=\"B1\"></p></form>
            </td>
       </tr>
       </table>
       </fieldset>";
       
echo "<!-- image add -->
<fieldset style='border:1px solid #800000; padding:2px; '>
<legend><b> 3 - تحمل من رابط</legend>
<form method=\"POST\" enctype=\"multipart/form-data\" action=\"\">
      <table border=0>
      <tr>
          <td>&nbsp;ألصق الرابط هنا : </td>
          <td>&nbsp;<input type=\"text\" name=\"remote_dl\" size=\"60\"> </td>
      </tr>
       <tr>
           <td colspan=2 align=center>
                 <input type=\"hidden\" name=\"maa\" value=\"from_remote\">
                 <input type=\"hidden\" name=\"f\" value=\"$f\">
                 <input type=\"hidden\" name=\"p\" value=\"$p\">
				 <input type=\"hidden\" name=\"type\" value=\"$type\">
				 <input type=\"hidden\" name=\"prev\" value=\"$prev\">
				 <input type=\"hidden\" name=\"folder\" value=\"$folder\">
                 <input type=\"submit\" value=\" تحميل  \" name=\"B1\"></p></form>   ملاحظة: يجب التأكد من أن الدالة fopen غير محظورة.
            </td>
       </tr>
       </table>
       </fieldset>";

}


function index(){
     global  $imgfile,$imgname,$imgcat,$db,$prefix,$path,$f,$p, $type,$intro;
	$type=$intro->input->get_post('type');
	$p=$intro->input->get_post("p");
	$f=$intro->input->get_post("f");
	$path=$intro->input->get_post("path");
	$imgcat=$intro->input->get_post("imgcat");
	$imgname=$intro->input->get_post("imgname");
     navnav();

}
/*
function GetExt($Filename) {
$RetVal = explode ( '.', $Filename);
return $RetVal[count($RetVal)-1];
}
*/
function do_imgAdd(){
     global  $file,$db,$prefix,$path,$f,$p,$type,$prev,$folder,$intro;

     navnav();

    /* if (!$file){
     echo "<h3><font color=red>خطأ: <br>";
     echo "الحقل فارغ";
     exit();
     }*/
	
	 $folder=$intro->input->get_post("folder");
	$p=$intro->input->get_post("p");
	$f=$intro->input->get_post("f");
	$prev=$intro->input->get_post("prev");

		 $path = "../uploads/";
        
        $date = date("Ymd");
        

        $TempFile = $_FILES ['file']['tmp_name'];
        $FileName2 = $_FILES ['file']['name'];

        $TheExt = GetExt($FileName2);
        
        $FILENAME2 = md5(time());
        $FILENAME2= rand(0,999999999);
        $FILENAME2 = $date."_".$FILENAME2;
        if($p != ""){
          $FILENAME2 = $p."_".$FILENAME2;
        }
       if($folder==""){
        $ff2= $FILENAME2.".".$TheExt;
		}
		if($folder!=""){
			$ff2= $FILENAME2.".".$TheExt;
			$ff2 = "$folder/$ff2";
		}
		
		
		$ff= $FILENAME2.".".$TheExt;
	
        @move_uploaded_file ($TempFile, $path.$ff);

        echo "<br>File <a href=$path$ff>$ff</a> uploaded!<br>";
        
		if($type == 'bb'){
		
		echo '<script language=javascript> 
				 
				 var file = (\'uploads/'.$ff2.'\'); 
				 var ToAdd =  \' \n\n[img] \'+ file +\'[/img] \n\n \'; 

				 opener.document.'.$f.'.value += ToAdd; 
				 alert(\'تم التحميل\'); 
				 window.close(); 
             </script>';
			 
		}else{
		
		
        echo "<script language=javascript> \n"
             ."opener.document.$f.value = ('uploads/$ff2'); \n";
			 if($prev == 1)
			 {
				 $form = explode(".", $f);
				 echo "opener.document.".$form[0].".imgPrev.src = ('../uploads/$ff'); \n"
				 ."opener.document.".$form[0].".imgPrev.height = 100; \n";
			 }
             
			 echo "alert('تم التحميل'); \n"
             ."window.close(); \n"
             ."</script>";
		}
}
function from_url(){
     global  $link_url,$db,$prefix,$path,$f,$p, $type,$intro;
$type=$intro->input->get_post("type");
$link_url=$intro->input->get_post("link_url");
$f=$intro->input->get_post("f");

		if($type == 'bb'){
		
		echo '<script language=javascript> 
				 
				var file = ("'.$link_url.'"); 
				var ToAdd =  \' \n\n[img] \'+ file +\'[/img] \n\n \'; 

				opener.document.'.$f.'.value += ToAdd; 
				alert(\'تم جلب الرابط\'); 
				window.close(); 
             </script>';
			 
		}else{
		
			


        echo "<script language=javascript> \n"
             ."opener.document.$f.value = ('$link_url'); \n"
			 
			 //."opener.document.".$form[0].".imgPrev.src = (\"../\"+file); \n"
			 //."opener.document.".$form[0].".imgPrev.width = 100; \n"
             
			 ."alert('تم جلب الرابط'); \n"
             ."window.close(); \n"
             ."</script>";
		}
}


function from_remote(){
     global  $remote_dl,$db,$prefix,$path,$f,$p, $type;

         
		 ob_clean();
		 ob_flush();
		 flush();
         
         echo "جاري سحب الملف: $remote_dl";
	
         ob_flush(); flush();


         $file_name = basename($remote_dl);
         $file_target = "../uploads/".$p."_".$file_name;

         if(download($remote_dl, $file_target) === false){
         die("فشل سحب الملف، يرجى التأكد جيدا من الرابط.");
         }
         
         $file_target = str_replace("../","",$file_target);

        if($type == 'bb'){
		
		echo '<script language=javascript> 
				 
				var file = ("'.$file_target.'"); 
				 var ToAdd =  \' \n\n[img] \'+ file +\'[/img] \n\n \'; 

				 opener.document.'.$f.'.value += ToAdd; 
				 alert(\'تم سحب الملف الي السيرفر\'); 
				 window.close(); 
             </script>';
			 
		}else{
		
		 echo "<script language=javascript> \n"
             ." opener.document.$f.value = ('$file_target'); \n"
             ." alert('تم سحب الملف الي السيرفر'); \n"
             ." window.close(); \n"
             ."</script>";
             
         flush();
		}
}
function download ($file_source, $file_target)
{

  ob_flush();
  echo " <br> جاري التحميل ... <br>";
  
  // Preparations
  $file_source = str_replace(' ', '%20', html_entity_decode($file_source)); // fix url format
  if (file_exists($file_target)) { chmod($file_target, 0777); } // add write permission

  // Begin transfer
  if (($rh = fopen($file_source, 'rb')) == FALSE) { return false; } // fopen() handles
  if (($wh = fopen($file_target, 'wb')) == FALSE) { return false; } // error messages.

  while (!feof($rh))
  {
    // unable to write to file, possibly because the harddrive has filled up
    if (fwrite($wh, fread($rh, 1024)) == FALSE) { fclose($rh); fclose($wh); return false; }

    print " . ";

    ob_flush(); flush();
    
  }
  echo " <br> انتهى ";
  // Finished without errors
  fclose($rh);
  fclose($wh);
  return true;
}
switch($maa) {

       	case "do_imgAdd":
             do_imgAdd();
             break;

        case "from_url":
             from_url();
             break;
             
        case "from_remote":
             from_remote();
             break;

       default:
             index();
             break;
}


####################################
// check-logged-in
}else{
      header("Location: login.php");
}


?>