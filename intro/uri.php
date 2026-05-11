<?php


class Uri
{
	var	$keyval			= array();
	var $uri_string;
	var $segments		= array();
	var $rsegments		= array();
	var $lang;
	var $app;
	var $action;
	var $title;
	var $id=0;
	var $page=1;
	
	function __construct()
	{
		$this->_fetch_uri_string();
		$this->_explode_segments();
		$this->_set_segments();
	}
	function _fetch_uri_string()
	{
		
		// Is the request coming from the command line?
		if (php_sapi_name() == 'cli' or defined('STDIN'))
		{
			$this->_set_uri_string($this->_parse_cli_args());
			return;
		}

		// Let's try the REQUEST_URI first, this will work in most situations
		if ($uri = $this->_detect_uri())
		{
			$this->_set_uri_string($uri);
			return;
		}

		// Is there a PATH_INFO variable?
		// Note: some servers seem to have trouble with getenv() so we'll test it two ways
		$path = (isset($_SERVER['PATH_INFO'])) ? $_SERVER['PATH_INFO'] : @getenv('PATH_INFO');
		if (trim($path, '/') != '' && $path != "/".SELF)
		{
			$this->_set_uri_string($path);
			return;
		}

		// No PATH_INFO?... What about QUERY_STRING?
		$path =  (isset($_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : @getenv('QUERY_STRING');
		if (trim($path, '/') != '')
		{
			$this->_set_uri_string($path);
			return;
		}

		// As a last ditch effort lets try using the $_GET array
		if (is_array($_GET) && count($_GET) == 1 && trim(key($_GET), '/') != '')
		{
			$this->_set_uri_string(key($_GET));
			return;
		}

		// We've exhausted all our options...
		$this->uri_string = '';
		return;
	}
	function _set_uri_string($str)
	{
		// Filter out control characters
		$str = remove_invisible_characters($str, FALSE);

		// If the URI contains only a slash we'll kill it
		$this->uri_string = ($str == '/') ? '' : $str;
	}
	private function _detect_uri()
	{
		if ( ! isset($_SERVER['REQUEST_URI']) OR ! isset($_SERVER['SCRIPT_NAME']))
		{
			return '';
		}

		$uri = $_SERVER['REQUEST_URI'];
		if (strpos($uri, $_SERVER['SCRIPT_NAME']) === 0)
		{
			$uri = substr($uri, strlen($_SERVER['SCRIPT_NAME']));
		}
		elseif (strpos($uri, dirname($_SERVER['SCRIPT_NAME'])) === 0)
		{
			$uri = substr($uri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
		}

		// This section ensures that even on servers that require the URI to be in the query string (Nginx) a correct
		// URI is found, and also fixes the QUERY_STRING server var and $_GET array.
		if (strncmp($uri, '?/', 2) === 0)
		{
			$uri = substr($uri, 2);
		}
		$parts = preg_split('#\?#i', $uri, 2);
		$uri = $parts[0];
		if (isset($parts[1]))
		{
			$_SERVER['QUERY_STRING'] = $parts[1];
			parse_str($_SERVER['QUERY_STRING'], $_GET);
		}
		else
		{
			$_SERVER['QUERY_STRING'] = '';
			$_GET = array();
		}

		if ($uri == '/' || empty($uri))
		{
			return '/';
		}

		$uri = parse_url($uri, PHP_URL_PATH);

		// Do some final cleaning of the URI and return it
		return str_replace(array('//', '../'), '/', trim($uri, '/'));
	}
	private function _parse_cli_args()
	{
		$args = array_slice($_SERVER['argv'], 1);

		return $args ? '/' . implode('/', $args) : '';
	}
	function _filter_uri($str)
	{
		global $config;
		if ($str != '' && $config['permitted_uri_chars'] != '' && $config['enable_query_strings'] == FALSE)
		{
			// preg_quote() in PHP 5.3 escapes -, so the str_replace() and addition of - to preg_quote() is to maintain backwards
			// compatibility as many are unaware of how characters in the permitted_uri_chars will be parsed as a regex pattern
			if ( ! preg_match("|^[".str_replace(array('\\-', '\-'), '-', preg_quote($config['permitted_uri_chars'], '-'))."]+$|i", $str))
			{
				//die('The URI you submitted has disallowed characters.');
			}
		}

		// Convert programatic characters to entities
		$bad	= array('$',		'(',		')',		'%28',		'%29');
		$good	= array('&#36;',	'&#40;',	'&#41;',	'&#40;',	'&#41;');

		return str_replace($bad, $good, $str);
	}
	function _explode_segments()
	{
		foreach (explode("/", preg_replace("|/*(.+?)/*$|", "\\1", $this->uri_string)) as $val)
		{
			// Filter segments for security
			$val = trim($this->_filter_uri($val));

			if ($val != '')
			{
				$this->segments[] = $val;
			}
		}
	}
	function _set_segments()
	{
		global $config;
		
		
		if($config['lang_is_prefix'] == true){
			
			$this->lang = isset($this->segments[0])?$this->segments[0]:'en';
			
			if($this->lang  != "ar" && $this->lang != "en")
				$this->lang = 'en';
			
			$this->app = isset($this->segments[1])?$this->segments[1]:$config['default_app'];
			$this->action = isset($this->segments[2])?$this->segments[2]:$config['default_action'];
			$this->id = isset($this->segments[3])?$this->segments[3]:'';
			$this->title = isset($this->segments[4])?$this->segments[4]:'';
			$this->page = isset($this->segments[5])?$this->segments[5]:1;
		}else{
			$this->lang = '';
			$this->app = isset($this->segments[0])?$this->segments[0]:$config['default_app'];
			$this->action = isset($this->segments[1])?$this->segments[1]:$config['default_action'];
			$this->id = isset($this->segments[2])?$this->segments[2]:'';
			$this->title = isset($this->segments[2])?$this->segments[2]:'';
			$this->page = isset($this->segments[3])?$this->segments[3]:1;
		}
	}
	function _reindex_segments()
	{
		array_unshift($this->segments, NULL);
		array_unshift($this->rsegments, NULL);
		unset($this->segments[0]);
		unset($this->rsegments[0]);
	}
	function segment($n, $no_result = FALSE)
	{
		return ( ! isset($this->segments[$n])) ? $no_result : $this->segments[$n];
	}
	function rsegment($n, $no_result = FALSE)
	{
		return ( ! isset($this->rsegments[$n])) ? $no_result : $this->rsegments[$n];
	}
	
}

?>
