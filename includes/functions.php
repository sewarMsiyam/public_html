<?php

if (!defined('CHECK_ME'))
{
	echo "oops!.";
	exit;
}   

/*

function Intro_ErrorHandler($errno) {
  // do nothing if error reporting is turned off
  if (error_reporting() === 0)
  {
    return;
  }
  // be sure received error is supposed to be reported
  if (error_reporting() & $errno)
  {   
		throw new printException($errno);
  }
}
Intro_ErrorHandler($errno);
*/


function is_php($version = '5.0.0')
{
	static $_is_php;
	$version = (string)$version;

	if ( ! isset($_is_php[$version]))
	{
		$_is_php[$version] = (version_compare(PHP_VERSION, $version) < 0) ? FALSE : TRUE;
	}

	return $_is_php[$version];
}
function remove_invisible_characters($str, $url_encoded = TRUE)
{
	$non_displayables = array();
	
	// every control character except newline (dec 10)
	// carriage return (dec 13), and horizontal tab (dec 09)
	
	if ($url_encoded)
	{
		$non_displayables[] = '/%0[0-8bcef]/';	// url encoded 00-08, 11, 12, 14, 15
		$non_displayables[] = '/%1[0-9a-f]/';	// url encoded 16-31
	}
	
	$non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';	// 00-08, 11, 12, 14-31, 127

	do
	{
		$str = preg_replace($non_displayables, '', $str, -1, $count);
	}
	while ($count);

	return $str;
}
function GetDateOnly($VAL){

  $date_only = explode(" ", $VAL);
  $VAL = $date_only[0];
  return $VAL;

}
function GetTimeOnly($VAL){

  $time_only = explode(" ", $VAL);
  $VAL = $time_only[1];
  return $VAL;

}

function GetIP() {
        if (getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        }
        elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        }
        elseif (getenv('HTTP_X_FORWARDED')) {
            $ip = getenv('HTTP_X_FORWARDED');
        }
        elseif (getenv('HTTP_FORWARDED_FOR')) {
            $ip = getenv('HTTP_FORWARDED_FOR');
        }
        elseif (getenv('HTTP_FORWARDED')) {
            $ip = getenv('HTTP_FORWARDED');
        }
        else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
}

function is_logged_in_admin($admin) {
    global $db,$prefix,$session;

    if(!is_array($admin)) {
	$read_cookie = explode("|", base64_decode($admin));
        $adminid = $read_cookie[0];
	$passwd = $read_cookie[2];
    } else {
        $adminid = $read_cookie[0];
	$passwd = $read_cookie[2];
    }
    $adminid = addslashes($adminid);
        $adminid = intval($adminid);
    if ($adminid != "" AND $passwd != "") {
        $result = $db->sql_query("SELECT password FROM ".$prefix."_admin WHERE adminid='$adminid'");
	$row = $db->sql_fetchrow($result);
        $pass = $row['password'];
	if($pass == $passwd && $pass != "") {
           return 1;
	}
    }
    return 0;
}

function is_logged_user($user) {
    global $db,$prefix;

    if(!is_array($user)) {
	$read_cookie = explode("|", base64_decode($user));
        $userid = $read_cookie[0];
	$passwd = $read_cookie[2];
    } else {
        $userid = $read_cookie[0];
	$passwd = $read_cookie[2];
    }
    $userid = addslashes($userid);
        $userid = intval($userid);
    if ($userid != "" AND $passwd != "") {
        $result = $db->sql_query("SELECT password FROM ".$prefix."_users WHERE userid='$userid'");
	$row = $db->sql_fetchrow($result);
        $pass = $row['password'];
	if($pass == $passwd && $pass != "") {
           return 1;
	}
    }
    return 0;
}
function get_user_info($user) {
    global $my_info, $prefix, $db;
    //if the user is logged in then read the cookies.
    $read_cookie = explode("|", base64_decode(addslashes($user)));
    $username = $read_cookie[1];
    $password = $read_cookie[2];
    $result = $db->sql_query("SELECT * FROM ".$prefix."_users WHERE username='$username' AND password='$password'");
    if ($db->sql_numrows($result) == 1) {
    	$my_info = $db->sql_fetchrow($result);
        return $my_info;
    }
    return 0;
}
function get_user_id() {
    global $my_info, $prefix, $db,$user;
    //if the user is logged in then read the cookies.
    $read_cookie = explode("|", base64_decode(addslashes($user)));
    $userid = $read_cookie[0];

    return $userid;

}
function get_user_data($userid) {
    global $prefix, $db;

    $result = $db->sql_query("SELECT * FROM ".$prefix."_users WHERE userid='$userid'");
    if ($db->sql_numrows($result) == 1) {
    	$user_info = $db->sql_fetchrow($result);
        return $user_info;
    }
    return 0;
}

function get_online_u() {
    global $prefix, $db;

    $result = $db->sql_query("SELECT * FROM ".$prefix."_users WHERE userid='$userid'");
    $db->sql_numrows($result);
    $row = $db->db_host();

    return $row;
}


function msg_redirect($msg,$url,$seconds){
         global $site_name, $site_url;

         echo "<html dir=\"rtl\">\n"
              ."<head>\n"
              ."<title>$site_name</title>\n"
              ."<meta http-equiv=\"Refresh\" content=\"$seconds; URL=$url\">\n"
              ."<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">\n"
              ."<link rel=\"stylesheet\" href=\"style.css\" type=\"text/css\">\n"
              ."</head>\n\n"
              ."<body>\n"
              ."<br />\n"
              ."<br />\n"
              ."<br />\n"
              ."<br />\n\n\n"
              ."<div align=\"center\">\n"
              ."<table cellpadding=\"6\" cellspacing=\"1\" border=\"0\" width=\"70%\" bgcolor=\"#E1E1E1\">"
              ."<tr>"
	      ."<td bordercolor=\"#808080\">جاري اعادة التوجيه</td>"
              ."</tr> "
              ."<tr> "
	      ."<td align=\"center\" bgcolor=\"#FFFFFF\">"
	      ."<blockquote> "
              ."<p>&nbsp;</p>"
	      ."<p><h3>$msg</h3></p>"
              ."<p><a href=\"$url\"> "
	      ."اضغط هنا اذا لم ينقلك المتصفح</a></p><br />"
              ."</blockquote>"
	      ."</div>\n"
	      ."</td>\n"
              ."</tr>\n"
              ."</table>\n\n\n"
              ."</body>\n"
              ."</html>";
}


// Removes any "bad" characters (characters which mess with the display of a page, are invisible, etc) from user input
function remove_bad_characters()
{
	global $bad_utf8_chars;

	$bad_utf8_chars = array("\0", "\xc2\xad", "\xcc\xb7", "\xcc\xb8", "\xe1\x85\x9F", "\xe1\x85\xA0", "\xe2\x80\x80", "\xe2\x80\x81", "\xe2\x80\x82", "\xe2\x80\x83", "\xe2\x80\x84", "\xe2\x80\x85", "\xe2\x80\x86", "\xe2\x80\x87", "\xe2\x80\x88", "\xe2\x80\x89", "\xe2\x80\x8a", "\xe2\x80\x8b", "\xe2\x80\x8e", "\xe2\x80\x8f", "\xe2\x80\xaa", "\xe2\x80\xab", "\xe2\x80\xac", "\xe2\x80\xad", "\xe2\x80\xae", "\xe2\x80\xaf", "\xe2\x81\x9f", "\xe3\x80\x80", "\xe3\x85\xa4", "\xef\xbb\xbf", "\xef\xbe\xa0", "\xef\xbf\xb9", "\xef\xbf\xba", "\xef\xbf\xbb", "\xE2\x80\x8D");

	($hook = get_hook('fn_remove_bad_characters_start')) ? eval($hook) : null;

	function _remove_bad_characters($array)
	{
		global $bad_utf8_chars;
		return is_array($array) ? array_map('_remove_bad_characters', $array) : str_replace($bad_utf8_chars, '', $array);
	}

	$_GET = _remove_bad_characters($_GET);
	$_POST = _remove_bad_characters($_POST);
	$_COOKIE = _remove_bad_characters($_COOKIE);
	$_REQUEST = _remove_bad_characters($_REQUEST);
}

// Return all code blocks that hook into $hook_id
function get_hook($hook_id)
{
	global $forum_hooks;

	return !defined('FORUM_DISABLE_HOOKS') && isset($forum_hooks[$hook_id]) ? implode("\n", $forum_hooks[$hook_id]) : false;
}

//
// Pagination routine, generates
// page number sequence
//
function pagination3($base_url, $num_items, $per_page, $start_item, $add_prevnext_text = true)
{
	global $lang;

	$total_pages = ceil($num_items/$per_page);

	if ( $total_pages == 1 )
	{
		return '';
	}

	$on_page = $start_item;

	$page_string = '';
	if ( $total_pages > 10 )
	{
		$init_page_max = ( $total_pages > 3 ) ? 3 : $total_pages;
                //echo "[xx $on_page xx]";
		for($i = 1; $i < $init_page_max + 1; $i++)
		{
			$page_string .= ( $i == $start_item ) ? '<b>' . $i . '</b>' : '<a href="'. $base_url . "&amp;page=" . $i . '">' . $i . '</a>';
			if ( $i <  $init_page_max )
			{
				$page_string .= ", ";
			}
		}



		if ( $total_pages > 3 )
		{



                        if ( $on_page > 1  && $on_page < $total_pages )
			{
				$page_string .= ( $on_page > 5 ) ? ' ... ' : ', ';

				$init_page_min = ( $on_page > 4 ) ? $on_page : 5;
				$init_page_max = ( $on_page < $total_pages - 4 ) ? $on_page : $total_pages - 4;

				for($i = $init_page_min - 1; $i < $init_page_max + 2; $i++)
				{
					$page_string .= ($i == $on_page) ? '<b>' . $i . '</b>' : '<a href="' . $base_url . "&amp;page=" . $i . '">' . $i . '</a>';
					if ( $i <  $init_page_max + 1 )
					{
						$page_string .= ', ';
					}
				}

				$page_string .= ( $on_page < $total_pages - 4 ) ? ' ... ' : ', ';
			}
			else
			{
				$page_string .= ' ... ';
			}

			for($i = $total_pages - 2; $i < $total_pages + 1; $i++)
			{
				$page_string .= ( $i == $on_page ) ? '<b>' . $i . '</b>'  : '<a href="' . $base_url . "&amp;page=" . $i . '">' . $i . '</a>';
				if( $i <  $total_pages )
				{
					$page_string .= ", ";
				}
			}
		}
	}
	else
	{
		for($i = 1; $i < $total_pages + 1; $i++)
		{
			$page_string .= ( $i == $on_page ) ? '<b>' . $i . '</b>' : '<a href="' . $base_url . "&amp;page=" . $i . '">' . $i . '</a>';
			if ( $i <  $total_pages )
			{
				$page_string .= ', ';
			}
		}
	}

	if ( $add_prevnext_text )
	{
		if ( $on_page > 1 )
		{
			$page_string = ' <a href="' . $base_url . "&amp;page=" .  ( $on_page - 1 ) . '"> « </a>&nbsp;&nbsp;' . $page_string;
		}

		if ( $on_page < $total_pages )
		{
			$page_string .= '&nbsp;&nbsp;<a href="' . $base_url . "&amp;page=" . ( $on_page + 1 ) . '"> » </a>';
		}

	}

	//$page_string = ' الذهاب إلى صفحة: ' . $page_string;

	return $page_string;
}


function pagination_ul_li($base_url, $num_items, $per_page, $start_item, $add_prevnext_text = true)
{
	global $lang;

	$total_pages = ceil($num_items/$per_page);

	if ( $total_pages == 1 )
	{
		return '';
	}

	$on_page = $start_item;

	$page_string = '<ul class="pagination">';
	if ( $total_pages > 10 )
	{
		$init_page_max = ( $total_pages > 3 ) ? 3 : $total_pages;
                //echo "[xx $on_page xx]";
		for($i = 1; $i < $init_page_max + 1; $i++)
		{
			$page_string .= ( $i == $start_item ) ? '<li class="active">' . $i . '</li>' : '<li><a href="'. $base_url . "&amp;page=" . $i . '">' . $i . '</a></li>';
			if ( $i <  $init_page_max )
			{
				//$page_string .= ", ";
			}
		}



		if ( $total_pages > 3 )
		{



                        if ( $on_page > 1  && $on_page < $total_pages )
			{
				$page_string .= ( $on_page > 5 ) ? ' ... ' : ', ';

				$init_page_min = ( $on_page > 4 ) ? $on_page : 5;
				$init_page_max = ( $on_page < $total_pages - 4 ) ? $on_page : $total_pages - 4;

				for($i = $init_page_min - 1; $i < $init_page_max + 2; $i++)
				{
					$page_string .= ($i == $on_page) ? '<li class="active">' . $i . '</li>' : '<li><a href="' . $base_url . "&amp;page=" . $i . '">' . $i . '</a></li>';
					if ( $i <  $init_page_max + 1 )
					{
						//$page_string .= ', ';
					}
				}

				$page_string .= ( $on_page < $total_pages - 4 ) ? ' ... ' : ', ';
			}
			else
			{
				$page_string .= ' ... ';
			}

			for($i = $total_pages - 2; $i < $total_pages + 1; $i++)
			{
				$page_string .= ( $i == $on_page ) ? '<li class="active">' . $i . '</li>'  : '<li><a href="' . $base_url . "&amp;page=" . $i . '">' . $i . '</a></li>';
				if( $i <  $total_pages )
				{
					//$page_string .= ", ";
				}
			}
		}
	}
	else
	{
		for($i = 1; $i < $total_pages + 1; $i++)
		{
			$page_string .= ( $i == $on_page ) ? '<li class="active">' . $i . '</li>' : '<li><a href="' . $base_url . "&amp;page=" . $i . '">' . $i . '</a></li>';
			if ( $i <  $total_pages )
			{
				//$page_string .= ', ';
			}
		}
	}

	if ( $add_prevnext_text )
	{
		if ( $on_page > 1 )//previous
		{
			$page_string = '<ul class="pagination"> <li><a href="' . $base_url . "&amp;page=" .  ( $on_page - 1 ) . '"> &laquo; </a></li>' . str_replace('<ul class="pagination">','',$page_string);
		}

		if ( $on_page < $total_pages )//next
		{
			$page_string .= '<li class="next"><a href="' . $base_url . "&amp;page=" . ( $on_page + 1 ) . '"> &raquo; </a></li>';
		}

	}

	//$page_string = ' الذهاب إلى صفحة: ' . $page_string;

	return $page_string."</ul>";
}


function tomoney($string){

   $Negative = 0;
   //check to see if number is negative
    if(preg_match("/^-/",$string)){
     //setflag
     $Negative = 1;
     //remove negative sign
     $string = preg_replace("|-|","",$string);
    }

   //look for commas in the string and remove them.
   $string = preg_replace("|,|","",$string);
   // split the string into two parts First and Second
   // First is before decimal, second is after. format = First.Second
   $Full = split("[.]",$string);

   $Count = count($Full);

   if($Count > 1){
    $First = $Full[0];
    $Second = $Full[1];
     $NumCents = strlen($Second);
      if($NumCents == 2){
           //do nothing already at correct length
       }else if($NumCents < 2){
           //add an extra zero to the end
           $Second = $Second . "0";
       }else if($NumCents > 2){
           //either string off the end digits or round up
           // I say string everything but the first 3 digits and then round
        // since it is rare that anything after 3 digits effects the round
        // you can change if you need greater accurcy, I don't so I didn't
           // write that into the code.
           $Temp = substr($Second,0,3);
           $Rounded = round($Temp,-1);
           $Second = substr($Rounded,0,2);

       }

   }else{
    //there was no decimal on the end so add to zeros
    $First = $Full[0];
    $Second = "00";
   }

  $length = strlen($First);

  if( $length <= 3 ){
     //To Short to add a comma
    //combine the first part and the second.
    $string = $First . "." . $Second;

    if($Negative == 1){
      $string = "-" . $string;
     }

    return $string;
    }else{
    $loop_count = intval( ( $length / 3 ) );
    $section_length = -3;
    for( $i = 0; $i < $loop_count; $i++ ){
      $sections[$i] = substr( $First, $section_length, 3 );
      $section_length = $section_length - 3;
      }

    $stub = ( $length % 3 );
    if( $stub != 0 ){
      $sections[$i] = substr( $First, 0, $stub );
      }
    $Done = implode( ",", array_reverse( $sections ) );
    $Done = $Done . "." . $Second;

    if($Negative == 1){
      $Done = "-" . $Done;
     }

    return  $Done;
    }
}


function pagination3uri($base_url, $num_items, $per_page, $start_item, $add_prevnext_text = true)
{
	global $lang;

	$total_pages = ceil($num_items/$per_page);

	if ( $total_pages == 1 )
	{
		return '';
	}

	$on_page = $start_item;

	$page_string = '';
	if ( $total_pages > 10 )
	{
		$init_page_max = ( $total_pages > 3 ) ? 3 : $total_pages;
                //echo "[xx $on_page xx]";
		for($i = 1; $i < $init_page_max + 1; $i++)
		{
			$page_string .= ( $i == $start_item ) ? '<b>' . $i . '</b>' : '<a href="'. $base_url . "" . $i . '">' . $i . '</a>';
			if ( $i <  $init_page_max )
			{
				$page_string .= ", ";
			}
		}



		if ( $total_pages > 3 )
		{



                        if ( $on_page > 1  && $on_page < $total_pages )
			{
				$page_string .= ( $on_page > 5 ) ? ' ... ' : ', ';

				$init_page_min = ( $on_page > 4 ) ? $on_page : 5;
				$init_page_max = ( $on_page < $total_pages - 4 ) ? $on_page : $total_pages - 4;

				for($i = $init_page_min - 1; $i < $init_page_max + 2; $i++)
				{
					$page_string .= ($i == $on_page) ? '<b>' . $i . '</b>' : '<a href="' . $base_url . "" . $i . '">' . $i . '</a>';
					if ( $i <  $init_page_max + 1 )
					{
						$page_string .= ', ';
					}
				}

				$page_string .= ( $on_page < $total_pages - 4 ) ? ' ... ' : ', ';
			}
			else
			{
				$page_string .= ' ... ';
			}

			for($i = $total_pages - 2; $i < $total_pages + 1; $i++)
			{
				$page_string .= ( $i == $on_page ) ? '<b>' . $i . '</b>'  : '<a href="' . $base_url . "" . $i . '">' . $i . '</a>';
				if( $i <  $total_pages )
				{
					$page_string .= ", ";
				}
			}
		}
	}
	else
	{
		for($i = 1; $i < $total_pages + 1; $i++)
		{
			$page_string .= ( $i == $on_page ) ? '<b>' . $i . '</b>' : '<a href="' . $base_url . "" . $i . '">' . $i . '</a>';
			if ( $i <  $total_pages )
			{
				$page_string .= ', ';
			}
		}
	}

	if ( $add_prevnext_text )
	{
		if ( $on_page > 1 )
		{
			$page_string = ' <a href="' . $base_url . "" .  ( $on_page - 1 ) . '"> « </a>&nbsp;&nbsp;' . $page_string;
		}

		if ( $on_page < $total_pages )
		{
			$page_string .= '&nbsp;&nbsp;<a href="' . $base_url . "" . ( $on_page + 1 ) . '"> » </a>';
		}

	}

	//$page_string = ' الذهاب إلى صفحة: ' . $page_string;

	return $page_string;
}

?>