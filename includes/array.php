<?php

			  
$array = array();
$array['nplace']=array('0'=>"بلا مكان",'1'=>"الخبر الرئيسي",'2'=>"خبر بالشريط المتحرك");	
$array['views']=array('1'=>"أخبار",'2'=>"صور");	
$array['ticket_status']=array('1'=>"رسالة جديدة",'2'=>"رد من الادارة ",'3'=>"رد من الزبون");	
$array['blocks'] =array('1'=>"ملف يأخد من مجلد blocks ",'2'=>"ملف html  ",'3'=>"ملف ياخذ من تقسيمات الموقع");
$array['blocks_places']=array('r'=>"يمين",'l'=>"يسار",'c'=>"بالاعلى",'d'=>"بالاسفل ");
	
$array['file_type'] = array(
	"application/pdf",
	"application/zip",
	"application/x-zip-compressed",
	"multipart/x-zip",
	"application/vnd.ms-powerpoint",
	"application/vnd.openxmlformats-officedocument.presentationml.presentation",
	"application/vnd.openxmlformats-officedocument.wordprocessingml.document",
	"application/postscript",
	"image/jpeg",
	"image/png",
	"image/gif",
);
$admin_typ=array(1=>"مشرف عام",2=>"قراءة فقط",3=>"صلاحيات");
$blocks_type=array('1'=>"ملف يأخد من مجلد blocks ",'2'=>"ملف html  ",'3'=>"ملف ياخذ من تقسيمات الموقع");
$blocks_places=array('r'=>"يمين",'l'=>"يسار",'c'=>"بالاعلى",'d'=>"بالاسفل ");
$news_array=array('1'=>"الخبر الرئيسي",'2'=>"خبر بشريط  الاخبار العاجلة",'3'=>"أخبار مصورة");
$ticket_status_array=array('1'=>"رسالة جديدة",'2'=>"رد من الادارة ",'3'=>"رد من الزبون");


$array['user_status'] = array(

	1 => 'Active',
	2 => 'Inactive',

);

$array['order_status'] = array(

	1 => 'Pending Payment',
	2 => 'Proccessing',
	3 => 'Completed',
	4 => 'Canceled',
);
$array['place'] = array(

	0 => 'None',
	1 => 'New',
	2 => 'Featured',
	3 => 'Beside Slider'
);

$array['langs'] = array(

	'ar' => 'Arabic',
	'en' => 'English',
	
);

//define("_REQUIRED", "مطلوب")	
?>