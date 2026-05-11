<?php
// Location and prefix for cache files // preferably use absolute path to cache directory
// while using mod rewrite relative path to cache directory might not work - so use absolute path
// absolute path to cache directory
//define('CACHE_PATH', $_SERVER["DOCUMENT_ROOT"]."/ca/");
// relative path to cache directory
define('CACHE_PATH', "./includes/cache/files/");

$PATH_INFO = !empty($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : (!empty($_SERVER['ORIG_PATH_INFO']) ? $_SERVER['ORIG_PATH_INFO'] : '');
$segment = !empty($PATH_INFO) ? array_filter(explode('/',$PATH_INFO)) : null;
if($segment[1] != NULL){

	if(!file_exists('apps/'.$segment[1].'.php') ){		
		/*header("HTTP/1.0 404 Not Found");
		define('CHECK_ME', true);
		include("style/style.php");
		custom_header("404 Not Found");
		die("<center>404 Not Found <br/> الصفحة التي تحاول عرضها غير موجودة </center>");*/
		$pagename = "index2";
	}
	if($segment[1] == 'rss') $nocache = 'off';
	if($segment[1] == 'search') $nocache = 'off';
}
if($PATH_INFO == ''){
	// get page name to use in the cached file name
	$pagepath = basename($_SERVER['PHP_SELF']);
	$pagename = basename($pagepath);
	$pagename = basename($pagepath, ".php");
}else{
	$pagename = str_replace("/","_",$PATH_INFO);
	$pagename = mb_ereg_replace('[^A-Za-z0-9]', '', $pagename);
}
define('PAGE_NAME', $pagename);
/*
echo "<hr>PAGE_NAME = ". PAGE_NAME . "<hr>
segment=" . $segment[1] . "<hr>PATH_INFO = ". $_SERVER['PATH_INFO']
."<hr>ORIG_PATH_INFO". $_SERVER['ORIG_PATH_INFO'];
die();*/
//echo PAGE_NAME;

// return location and name for cache file
function cache_file()
{
	//return CACHE_PATH . "cache_" .md5($_SERVER['QUERY_URI']) . "_" . PAGE_NAME . ".html";
	return CACHE_PATH . PAGE_NAME . ".html";
	// if you don't want to use cache_, pagename and .html for cache file names - use this below	and change this in delete-single.php as well 
	// return CACHE_PATH . md5($_SERVER['REQUEST_URI']);
}
function sanitize_output($buffer)
{
    $search = array(
        '/\>[^\S ]+/s', //strip whitespaces after tags, except space
        '/[^\S ]+\</s', //strip whitespaces before tags, except space
        '/(\s)+/s'  // shorten multiple whitespace sequences
        );
    $replace = array(
        '>',
        '<',
        '\\1'
        );
    $buffer = preg_replace($search, $replace, $buffer);

    return $buffer;
}
// if form is submitted and post is set - delete current cached page
if($_SERVER["REQUEST_METHOD"] == 'POST') { 
	// delete current page cache
	$filename = cache_file();
	if(file_exists($filename)) { @unlink($filename); }
}

$mycache="on";
if($mycache=='on' && $nocache!='on' && $_SERVER["REQUEST_METHOD"] != 'POST')
{

	// Time to keep the cache files in hours
	define('CACHE_TIME', 1);
	// display cached file if present and not expired
	function cache_display()
	{
		$file = cache_file();
		// check that cache file exists and is not too old
		if(!file_exists($file)) return;
		if(filemtime($file) < time() - CACHE_TIME * 3600) return;
		// if so, display cache file and stop processing
		readfile($file);
		exit;
	}
	// write to cache file
	function cache_page($content)
	{
		//echo "file = ".cache_file() ."";
		if(false !== ($f = @fopen(cache_file(), 'w')))
		{		
			$content = sanitize_output($content);
			fwrite($f, $content);
			fclose($f);
		}
		return $content;
	}
	
	cache_display();
	// enable output buffering and create cache file
	ob_start('cache_page');
	
	
}
?>