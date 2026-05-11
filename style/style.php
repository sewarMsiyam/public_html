<?php


function whois_online() {
  global $intro;
  $ip = $_SERVER['REMOTE_ADDR'];
  #if (is_logged_in($user)) {
  #  $uinfo = CookieUser($user);
  #  $username = $uinfo['username'];
  #  $guest = 0;
  #} else {
    $username = $ip;
    $guest = 1;
  #}
  $past = time()-3600;

  $intro->db->query_fast("DELETE FROM ".PREFIX."_stats_online WHERE time < '$past'");

  $result =  $intro->db->query_fast("SELECT time FROM ".PREFIX."_stats_online WHERE username='".addslashes($username)."'");
  $ctime = time();
  if (!empty($username)) {
    $username = substr($username, 0,25);
    $row = $intro->db->fetch_assoc($result);
    if ($row) {
      $intro->db->query_fast("UPDATE ".PREFIX."_stats_online SET username='".addslashes($username)."', time='$ctime', ip='$ip', guest='$guest' WHERE username='".addslashes($username)."'");
    } else {
      $intro->db->query_fast("INSERT INTO ".PREFIX."_stats_online (username, time, ip, guest) VALUES ('".addslashes($username)."', '$ctime', '$ip', '$guest')");
      //$db->sql_query("UPDATE ".PREFIX."_stats_counter SET count=count+1 WHERE (type='visits') ");
    }
  }
  
}
//whois_online();






function pHeader( $header_data = array() ){
	global $home,$intro,$adv1,$CONF,$prod_cat,$stylecatid;
	$seo_title = $seo_link = $seo_image = $slide = $main_news_js = $site_bg = $date = $header_inside = "";
	$navigation = "";
	
	$home = $intro->home;
	$lang=$intro->maa->lang;
	$stylecss = $bootstrap = $time=  '';
	$currency =$intro->maa->Currency();
	
	if(isset($header_data['nav']) && $header_data['nav'] != ""){
		$navigation = $header_data['nav'];
	}
	
	$lang_en = $intro->base_url ."en/" . $intro->uri->app ."/" . $intro->uri->action ."/" . $intro->uri->id;
	$lang_ar = $intro->base_url ."ar/" . $intro->uri->app ."/" . $intro->uri->action ."/" . $intro->uri->id;
	
	$seo_title = (isset($header_data['seo_title']) !='' ? $header_data['seo_title'] : $intro->option['site_name'] ) ;
	$seo_link =  (isset($header_data['seo_link']) !='' ? $header_data['seo_link'] : $intro->option['site_url'] ) ;
	$seo_image = (isset($header_data['seo_image']) !='' ? $header_data['seo_image'] : $intro->option['site_url']."{$intro->base_url}style/img/logo.png" ) ;
	$seo_desc = (isset($header_data['seo_desc']) !='' ? $header_data['seo_desc'] : $intro->option['site_desc'] ) ;
	//get page title for seo
	
	$time = time();

	$page_title=$intro->option['site_name'];
	if(isset($header_data['seo_title']) && $header_data['seo_title'] != ''){
		$page_title=$page_title ." | ".$header_data['seo_title'];
	}
	$url = $intro->base_url . "index.php";
	
	$intro->option['site_keywords']=str_replace("،",",", $intro->option['site_keywords']);
	#echo meta

	if($lang=='ar'){
		$stylecss = "<link rel=\"stylesheet\" href=\"{$intro->base_url}style/css/style_ar.css?v=$time\">";
		$langchanged="
		<button type=\"button\" class=\"btn btn-sm btn-light dropdown-toggle\" data-toggle=\"dropdown\"> <img src='{$intro->base_url}style/img/flag_g.jpg'> AR</button>
            <div class=\"dropdown-menu dropdown-menu-right\">
                <a class=\"dropdown-item pl-2\" href=\"$lang_en\" > <img src='{$intro->base_url}style/img/flag_en.jpg'> EN </a>
           </div>";
	$alighn="text-left";
	$mr="ml-auto";
	$ml="mr-auto";
	
	}else{
		$stylecss = "";
		$mr = "mr-auto";
		$ml = "ml-auto";
		$alighn="text-right";
		$langchanged="<button type=\"button\" class=\"btn btn-sm btn-light dropdown-toggle\" data-toggle=\"dropdown\"> <img src='{$intro->base_url}style/img/flag_en.jpg'> EN</button>
            <div class=\"dropdown-menu dropdown-menu-right\">
                <a class=\"dropdown-item pl-2\" href=\"$lang_ar\" > <img src='{$intro->base_url}style/img/flag_g.jpg'>  AR</a>
           </div>";
	}
	



	eval("\$meta = \" " . $intro->style['meta'] . "\";");
	echo stripslashes($meta);
	
	if ($intro->auth->auth_user()==true)
		{
			$accountlink="	<li><a href=\"{$intro->uri_links('downloads','index',0,'')}\"><font color=#CC0000> {$intro->lang['downlods']}</font></a></li>
				<li><a href=\"{$intro->uri_links('account','index',0,'')}\"><font color=#0585BC>{$intro->lang['myaccount']}</font></a></li>
					<li><a href=\"{$intro->uri_links('login','Logout')}\"  ><font color=#0585BC>{$intro->lang['logout']} </font></a>  </li>
			";
		$sess_user = $intro->auth->sess_user();
		$welcom="<div class='welcome_account' $class_lnag>{$intro->lang['welcome']} : <span>$sess_user[fullname] </span></div>";
		
		$accountlink="<a href=\"{$intro->uri_links('downloads','index',0,'')}\" class=\"btn btn-sm 	btn-danger mr-2\"><small class=\"fas fa-download mr-1\"></small> {$intro->lang['downlods']}</a>
		
		 <div class=\"btn-group\" >
			<button type=\"button\" class=\"btn btn-sm btn-primary2  dropdown-toggle\" data-toggle=\"dropdown\"> <small class=\"fas fa-user-circle mr-1 \"></small> {$intro->lang['myaccount']}</button>
			<div class=\"dropdown-menu dropdown-menu-right\">
			 <a href=\"{$intro->uri_links('account','index',0,'')}\" class=\"dropdown-item\" ><small class=\"fas fa-user-circle mr-1 text-primary\"></small>{$intro->lang['myaccount']}</a>
				<a href=\"{$intro->uri_links('login','Logout',0,'')}\" class=\"dropdown-item\" ><small class=\"fas fa-sign-out-alt mr-1 text-primary\"></small>{$intro->lang['logout']}</a>
			</div>
		</div>";
	
		}else{
	
		$accountlink="<a href=\"{$intro->uri_links('login','index',0,'')}\" class=\"btn btn-sm btn-success 	mr-2	\"><small class=\"fas fa-lock mr-1\"></small> {$intro->lang['login']}</a>
					 <a href=\"{$intro->uri_links('register','index',0,'')}\" class=\"btn btn-sm btn-primary2\"><small class=\"fa fa-user-plus mr-1\"></small> {$intro->lang['register']}</a>";			
		$welcom='';
		}
	
	
	$cart_header=minicart();
	
	
	
     $currency_sql = $intro->db->query("SELECT * FROM ".PREFIX."_currences order by id desc");
	while($row_curr = $intro->db->fetch_assoc($currency_sql))
	{
	    $currences.=" <a class=\"dropdown-item currency-option\" href=\"#\" data-currency=\"".$row_curr['sygnal']."\">".$row_curr['title_en']."</a>";
	}
	
	$sql_cats = $intro->db->query("SELECT * from ".PREFIX."_products_cat where father=0 order by catid asc");
	while($caps = $intro->db->fetch_assoc($sql_cats))
	{
		$catnames=$caps['catname_'.$lang];
		$catids=$caps['catid'];
		$urls=$intro->uri_links('products','Cat',$catids,$catnames);
		
		$menuheader.=" <a href=\"$urls\" class=\"nav-item nav-link\">$catnames</a>";
	    $menuheader2.="<a href=\"$urls\" class=\"dropdown-item\">$catnames</a>";
		if($stylecatid==$catids){$checkedcat="checked";}else{$checkedcat='';}
		
		$prod_cat.=" <div class=\"custom-control custom-checkbox d-flex align-items-center justify-content-between py-3 border-bottom\">
						  <input type=\"checkbox\" name='cat_checked[]'  $checkedcat >
						  <label  >
							<a href='$urls' class='text-dark font-weight-bold'> $catnames</a>
						  </label>
						  	<span class=\"badge  font-weight-normal text-muted\"><i class=\"fas fa-bars  mx-2\"></i></span>

						</div>";			
	}

	
	$homeactive=$intro->uri->segments[1]=='home' || $intro->uri->segments[1]=='' ?'active':'';
	$aboutusactive=$intro->uri->segments[1]=='pages'?'active':'';
	$productsactive=$intro->uri->segments[1]=='products'?'active':'';
	$cartactive=$intro->uri->segments[1]=='cart'?'active':'';
	$faqsactive=$intro->uri->segments[1]=='faqs'?'active':'';
	$blogactive=$intro->uri->segments[1]=='blog'?'active':'';
	$contactactive=$intro->uri->segments[1]=='contact'?'active':'';
	
	
		$sql_marqee = $intro->db->query("SELECT * from ".PREFIX."_marquee order by w asc");
	while($rowm = $intro->db->fetch_assoc($sql_marqee))
	{
		@extract($rowm);
		
		$mtitle=$rowm['mtitle_'.$lang];
		$url=$rowm['url_'.$lang];
		$marqee.="<a href='$url' class='mr-1' style='color:#$color'>$mtitle</a> <img src=\"{$intro->base_url}style/img/dot.png\"  class='mr-1'> ";
					
	}
	
if($lang=='ar'){$drction="right";}else{$drction="left";}
$headermarq="<marquee dir=ltr class='mb-2 mt-3' direction=\"$drction\" onmouseover=\"this.stop();\" onmouseout=\"this.start();\">
                $marqee 
            </marquee>";
	
	
	eval("\$header = \" " . $intro->style['header'] . "\";");
	echo stripslashes($header);
}

function pFooter(){
	global $intro,$home,$start_time,$starttime;
	$chat=$zopim ='';
	$lang = $intro->maa->lang;
	
	$year=date('Y');
	

		
	$chat=$intro->style['chat'];
	
	$gettouch =get_msg('getintouch');
	
	eval("\$footer = \" " . $intro->style['footer'] . "\";");
	echo stripslashes($footer);

	if($intro->option['debug'] == 1)
	{		
		if ($intro->auth->auth_admin() == true) 
		{
			$end = microtime(true);
			$page_speed = $end - $start_time;
			echo ('<div style="text-align:center">' . $page_speed . '</div>' );
			$intro->db->show_debug_console();
		}			
	}

  
	


}

function TableOpen($titlepage="",$editnews="") {
   global $intro;
   
	eval("\$output_data = \" " . $intro->style['content_box_open'] . "\";");
	echo stripslashes($output_data);
}
function TableClose() {
	global $intro;
	
	$lang=$intro->maa->lang;
    $news=$albume=$images="";

	eval("\$output_data = \" " . $intro->style['content_box_close'] . "\";");
	echo stripslashes($output_data);
    
}

/*****************************
custom header
******************************/
function custom_header($title) {
    global  $intro;
	
	global $home,$intro,$adv1,$CONF;
	$home = $intro->home;
	$lang=$intro->maa->lang;
	


	?>
	<!DOCTYPE >
	<html  html dir=" <?=$intro->lang['dir']?> ">
	<head>
		<meta charset="utf-8" />
		<title><?=$intro->option['site_name']?> <?=$title?></title>
		<link rel="stylesheet" href="<?=$intro->base_url?>style/css/style.css"  />
		</head>
	<body>
	<?php	
	
}






function BlocksTheme($title, $content, $showtitle,$image, $file_theme) {
    global  $theme, $CONF, $style, $intro;

    $thefile = "\$r_file=\" " . $intro->style[$file_theme] . " \";";
    eval($thefile);
    print stripslashes($r_file);
}

//دالة المجموعات
function render_blocks($side,$blockfile,$title,$content,$bid,$topic,$topic_no,$move,$image,$imgw,$imgh,$file_theme,$showtitle){
         global $db, $prefix, $cur_issue, $intro;

	
    if ($topic == 0) {

		if ($blockfile == "") {
		
		    if ($side == "c") {
				echo $content;
		    } elseif ($side == "d") {
                                 echo $content;
		    } else {
				BlocksTheme($title, $content, $showtitle, $image, $file_theme);
		    }
		} else {
		
		
		
		    if ($side == "c") {
				blockfileinc($title, $blockfile, $showtitle, $image,$file_theme);
				
				
		    } elseif ($side == "d") {
				blockfileinc($title, $blockfile, $showtitle, $image,$file_theme);  //,1
		    } else {
		
				blockfileinc($title, $blockfile, $showtitle, $image,$file_theme);
		    }
		}
	} else {
                 $result22 = $intro->db->query("SELECT * from ".PREFIX."_news_cat where father='$topic' order by catid");
                   while($myrow22 = $intro->db->fetch_assoc($result22)){
                    $othertopics .=" or catid='$myrow22[catid]'   ";
                 }

                 $result = $intro->db->query("SELECT  * FROM ".PREFIX."_news where catid='$catid' $othertopics ORDER BY sid DESC limit $topic_no");
                 while ($row = $intro->db->fetch_assoc($result)) {
	           $s_sid = intval($row['sid']);
	           $s_title = stripslashes($row['title']);
	           $time = $row['time'];
	           $hometext = stripslashes($row['hometext']);
                   $image1 = $row['image1'];

                      if ($image1 != ""){

                      $imagep = "<center><img alt='شركة انتروتك' src='images/archive/$image1' width='$imgw' height='$imgh'></center>";

                      }else{
                      $imagep = "";
                      }
                      $s_title = "<a href=news.php?maa=View&amp;id=$s_sid>$s_title</a><br><font class=tiny>$time</font>";



                      $block_stuff .= "<table border='0' cellpadding='0' cellspacing='0' width='100%' id='table1'>
					<tr>
						<td align=center>$imagep</td>
                                         </tr>
                                         <tr>
						<td align=center>$s_title</td>
					</tr>
				</table><hr size=1 color=#c0c0c0 width=90%>";


                    }


                 if ($move == 1){
                          $content = "<marquee direction=\"up\" width=\"99%\" scrollamount=\"2\"  onmouseover=this.stop() onmouseout=this.start() height=\"150\">";
                          $content .= "$block_stuff";
                          $content .= "</marquee>";
                 }else{
                     $content = $block_stuff;
                 }
                if ($side == "c" OR $side == "d") {
                   BlocksTheme($title, $content, $showtitle, $image,$file_theme,1);
		} else {
                   BlocksTheme($title, $content, $showtitle, $image,$file_theme);
		}
    }
}
function blockfileinc($title, $blockfile, $showtitle,$image, $file_theme, $side=0) {
    $blockfiletitle = $title;
	//echo $blockfile;
    $file = @file("blocks/".$blockfile."");

    if (!$file) {
	$content = "الملف ($blockfile) غير موجود";
    } else {

        include("blocks/".$blockfile."");
		//echo "$content";
    }
    if ($content == "") {
    //يوجد مشكلة 2
	$content = "لايوجد محتوى";
    }
    if ($side == 1) {
	echo $content;
    } elseif ($side == 2) {
       echo $content;
    } else {
	BlocksTheme($blockfiletitle, $content, $showtitle, $image,$file_theme);
	
	
    }
}
function blocks($side,$home="",$page="") {
    global $storynum, $prefix, $intro;
	$qry='';
    if (strtolower($side[0]) == "l") {
		$pos = "l";
    } elseif (strtolower($side[0]) == "r") {
		$pos = "r";
    }  elseif (strtolower($side[0]) == "c") {
		$pos = "c";
    } elseif  (strtolower($side[0]) == "d") {
		$pos = "d";
    }
    $side = $pos;
	if($home!="") $qry.="and inhome=1";
	if($page!="") $qry.="and inpage=1";
	
	
    $sql = "SELECT * FROM ".PREFIX."_blocks WHERE bposition='$pos' AND active='1' $qry ORDER BY weight ASC";
    $result = $intro->db->query($sql);
    while($row = $intro->db->fetch_assoc($result)) {
	$bid = intval($row['bid']);
	$title = stripslashes($row['title']);
	$showtitle = $row['showtitle'];
        $topic = $row['topic'];
        $topic_no = $row['topic_no'];
        $move = $row['move'];
        $image = $row['image'];
        $imgw = $row['imgw'];
        $imgh = $row['imgh'];
        $file_theme = stripslashes($row['file_theme']);
		$content = stripslashes($row['content']);
		$url = stripslashes($row['url']);
		$blockfile = $row['blockfile'];
		$view = intval($row['view']);
		$expire = intval($row['expire']);
		$action = $row['action'];
        $action = substr("$action", 0,1);
	    $now = time();
	   
		if ($expire != 0 AND $expire <= $now) {
		    if ($action == "d") {
		            $intro->db->query("UPDATE ".PREFIX."_blocks SET active='0', expire='0' WHERE bid='$bid'");
		            return;
		    } elseif ($action == "r") {
		            $intro->db->query("DELETE FROM ".PREFIX."_blocks WHERE bid='$bid'");
		            return;
		    }
		}       
		if ($view == 0) {
		
			render_blocks($side,$blockfile,$title,$content,$bid,$topic,$topic_no,$move,$image,$imgw,$imgh,$file_theme,$showtitle);            
		}	    
    }
	
	
}

?>