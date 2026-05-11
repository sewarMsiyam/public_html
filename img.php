<?php

if($_GET['img'] == "") exit();

$path = $_SERVER['SCRIPT_FILENAME'];
$path = str_replace( array("img.php","index.php"),"",$path);
if($_GET['news']==1){
	$image = $path."uploads/news/".$_GET['img'];
}else{
	$image=$path.$_GET['img'];
}
$max_height = $_GET['h'];
$max_width = $_GET['w'];

if(!file_exists($image) || $_GET['img'] == ""){
	$image="style/img/logo.png";
}

if (!$max_width) $max_width = 80;
if (!$max_height) $max_height = 60;

define('DESIRED_IMAGE_WIDTH', $max_width);
define('DESIRED_IMAGE_HEIGHT', $max_height);

$source_path = $_FILES['Image1']['tmp_name'];

list($source_width, $source_height, $source_type) = @getimagesize($image);

switch ($source_type) {
    case IMAGETYPE_GIF:
        $source_gdim = imagecreatefromgif($image);
        break;
    case IMAGETYPE_JPEG:
        $source_gdim = imagecreatefromjpeg($image);
        break;
    case IMAGETYPE_PNG:
        $source_gdim = imagecreatefrompng($image);
        break;
}

@$source_aspect_ratio = $source_width / $source_height;
$desired_aspect_ratio = DESIRED_IMAGE_WIDTH / DESIRED_IMAGE_HEIGHT;

if ($source_aspect_ratio > $desired_aspect_ratio) {//if image is big
    $temp_height = DESIRED_IMAGE_HEIGHT;
    $temp_width = ( int ) (DESIRED_IMAGE_HEIGHT * $source_aspect_ratio);
} else {//if small
    $temp_width = DESIRED_IMAGE_WIDTH;
    @$temp_height = ( int ) (DESIRED_IMAGE_WIDTH / $source_aspect_ratio);
}
#resize image
$temp_gdim = @imagecreatetruecolor($temp_width, $temp_height);
@imagecopyresampled(
    $temp_gdim,
    $source_gdim,
    0, 0,
    0, 0,
    $temp_width, $temp_height,
    $source_width, $source_height
);
#crop image
$x0 = ($temp_width - DESIRED_IMAGE_WIDTH) / 2;
$y0 = ($temp_height - DESIRED_IMAGE_HEIGHT) / 2;
$desired_gdim = @imagecreatetruecolor(DESIRED_IMAGE_WIDTH, DESIRED_IMAGE_HEIGHT);
@imagecopy(
    $desired_gdim,
    $temp_gdim,
    0, 0,
    $x0, $y0,
    DESIRED_IMAGE_WIDTH, DESIRED_IMAGE_HEIGHT
);

@header('Content-type: image/jpeg');
@ImageJpeg($desired_gdim, null, 70);
#@ImageDestroy($src);
#@ImageDestroy($desired_gdim);

/*
@$x_ratio = $max_width / $width;
@$y_ratio = $max_height / $height;

if ( ($width <= $max_width) && ($height <= $max_height) ) {
  $tn_width = $width;
  $tn_height = $height;
}
else if (($x_ratio * $height) < $max_height) {
  $tn_height = ceil($x_ratio * $height);
  $tn_width = $max_width;
}
else {
  $tn_width = ceil($y_ratio * $width);
  $tn_height = $max_height;
}

$RetVal = explode ( '.', $image);
$file_type = $RetVal[count($RetVal)-1];

$file_type=strtolower($file_type);

//echo"$image/$file_type<br><hr>";

if($file_type == "jpg"){
    $src = @ImageCreateFromJPEG($image);
}elseif($file_type == "png"){
    $src = @ImageCreateFromPNG($image);
}elseif($file_type == "gif"){
    $src = @ImageCreateFromGIF($image);
}


$tn_width = $_GET['w'];
$tn_height = $_GET['h'];
           
//$src = ImageCreateFromJPEG($image);
$dst = @ImageCreateTrueColor($tn_width,$tn_height);
@ImageCopyResized($dst, $src, 0, 0, 0, 0,$tn_width,$tn_height,$width,$height);
@header('Content-type: image/jpeg');
@ImageJpeg($dst, null, 70);
@ImageDestroy($src);
@ImageDestroy($dst);
*/
?>
