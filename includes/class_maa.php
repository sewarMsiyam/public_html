<?php

if (!defined('CHECK_ME')) { exit; }

class maa extends intro
{

	public $db;
	public $lang = null;
	

    public function __construct($db)
    {
        $this->db = $db;
    }
	
	function obtain_config()
	{		
		global $intro;
		
		$sql = 'SELECT option_name, option_value
				FROM ' . PREFIX . '_options';
		$result = $intro->db->query($sql ,'', true);

		while ($row = $intro->db->fetch_assoc($result))
		{
			$config[$row['option_name']] = $row['option_value'];
		}

		
		return $config;		
	}
	
	/**
	* Get language values
	*/
	function obtain_lang($uri_lang='')
	{
		global $intro;
		
		if($uri_lang == ""){
			$the_lang = $this->dected_lang();
			//echo "Dected lang = $the_lang<hr>";
		}else{
			$the_lang = $uri_lang;
		}
		$this->lang = $the_lang;
		if($the_lang == 'ar'){
			$lang_cat = 1;
			$this->lang = "ar";
		}else{
			$lang_cat = 2;
			$this->lang = "en";
		}
		//echo "class lang = $this->lang<hr>";
		
		$result = $intro->db->query("SELECT varname, text FROM " . PREFIX . "_lang where catid='$lang_cat' ");

		while ($row = $intro->db->fetch_assoc($result))
		{
			$lang[$row['varname']] = stripslashes($row['text']);
		}
		

		return $lang;
	}
	function dected_lang()
	{
		global $intro;
		
		if( isSet($_REQUEST['newlang']) && $_REQUEST['newlang'] != '')
		{
			$newlang = $_REQUEST['newlang'];
			if($newlang == 'ar' || $newlang == 'en'){
				$newlang = $newlang;
			}else{
				$newlang = 'en';
			}
			$_SESSION['lang'] = $newlang;
			setcookie('lang', $newlang, time() + (3600 * 24 * 30));
		}
		else if(isSet($_SESSION['lang']))
		{
			$newlang = $_SESSION['lang'];
			//var_dump($_SESSION , $_COOKIE);
		}
		else if(isSet($_COOKIE['lang']))
		{
			$newlang = $_COOKIE['lang'];
		}
		else
		{
			$browser_lang = @substr($intro->input->server('HTTP_ACCEPT_LANGUAGE'), 0, 2);
			if($browser_lang == 'ar'){
				$newlang = 'ar';
			}else{
				$newlang = 'en';
			}
			
			$newlang = 'en';
			$_SESSION['lang'] = $newlang;
			setcookie('lang', $newlang, time() + (3600 * 24 * 30));
		}
		/*
		echo "<!--";
		echo $intro->input->server('HTTP_ACCEPT_LANGUAGE') . "<hr>\n\n";
		var_dump($_COOKIE); echo "<hr>\n\n";
		var_dump($_SESSION); echo "-->";*/
        //$newlang='ar';		
		return $newlang;
	}
	
	
	function Currency()
	{
		global $intro;
		$newlang="Default";
		
		if (isset($_POST['currency'])) {
			$newlang = $_POST['currency'];
			$_SESSION['currency'] = $_POST['currency'];
			

		} else {
			$newlang= "لم يتم اختيار عملة.";
		}	
		$currentCurrency = isset($_SESSION['currency']) ? $_SESSION['currency'] : 'USD';		
		return $currentCurrency;
	}
	
		function Currency_amount($amount = 1)
		{
			global $intro;

			if (!isset($_SESSION['currency'])) {
				return $amount." $" ; 
			}

			$currency = $_SESSION['currency'];
			

			$currency_sql = $intro->db->query("SELECT * FROM " . PREFIX . "_currences WHERE sygnal = '$currency' LIMIT 1");
			if (@mysqli_num_rows($currency_sql) == 0) {
				return "No Currency Accoured"; 
			}

			$row_curr = $intro->db->fetch_assoc($currency_sql);
			$cur_per_dollar = floatval($row_curr['cur_per_dollar']);

			if ($cur_per_dollar <= 0) {
				return $amount; 
			}

			// التحويل
			$converted = intval($amount * $cur_per_dollar); // يحذف الكسور
            return $converted . ' ' . $row_curr['symbol'];
			
		}

	
	
	/**
	* Get style and templates, css
	*/
	function obtain_style()
	{
		global $intro;
		$style = array();

		$result = $this->db->query("SELECT varname,html FROM " . PREFIX . "_style;");
		while ($row = $this->db->fetch_assoc($result))
		{
			$template =  str_replace("\"","\\\"",stripslashes($row['html']));
			$style[$row['varname']] = $template;
		}
		
		return $style;
	}

        
	public function ip2country($ipn)
	{
	   global $db;
	   
	   $result = $this->db->query("SELECT iso_code, (ip_to - ip_from) AS d  FROM ".PREFIX."_country_ip WHERE ip_to >='$ipn' AND ip_from<='$ipn' ORDER BY d ASC LIMIT 0,1");
	   list($c,$d)= $this->db->fetch_assoc($result);

	   $this->db->free_result($result);

	   return $c;
	   
	}
	function filter($appname,$cols=array()){
		global $intro;
		$filter_pages = '';
		
		if(is_array($cols))
		{
			$i = 0;
			$where = "";
			$filter_form = "";
			foreach($cols AS $index=>$condition)
			{
				$val = trim($intro->input->get_post($index));
				
				$filter_form .="<input placeholder=\"".$intro->lang[$appname.'_'.$index]."\" type=\"text\" name=\"$index\" value=\"$val\" size=\"20\"> ";
				$filter_pages .="&$index=$val";

				if( strlen($val) >= 1 )
				{
					if($i == 0) $op = "WHERE"; else $op = "AND";
					switch($condition)
					{
						case "CONTAINS":
							$where .= " $op " . $index . " LIKE '%" . $val ."%'";
							break;
						case "DOES_NOT_CONTAIN":
							$where .= " $op " . $index . " NOT LIKE '%" . $val ."%'";
							break;
						case "EQUAL":
							$where .= " $op " . $index . " = '" . $val ."'";
							break;
						case "NOT_EQUAL":
							$where .= " $op " . $index . " <> '" . $val ."'";
							break;
						case "GREATER_THAN":
							$where .= " $op " . $index . " > '" . $val ."'";
							break;
						case "LESS_THAN":
							$where .= " $op " . $index . " < '" . $val ."'";
							break;
						case "GREATER_THAN_OR_EQUAL":
							$where .= " $op " . $index . " >= '" . $val ."'";
							break;
						case "LESS_THAN_OR_EQUAL":
							$where .= " $op " . $index . " <= '" . $val ."'";
							break;
						case "STARTS_WITH":
							$where .= " $op " . $index . " LIKE '" . $val ."%'";
							break;
						case "ENDS_WITH":
							$where .= " $op " . $index . " LIKE '%" . $val ."'";
							break;
					}
					//$this->filter_qry .="$op $index LIKE '%$val%' ";
					
					$i++;
				}
			}
			
			return array('form'=> $filter_form , 'where'=> $where, 'filter_pages'=> $filter_pages);
		}
	
	}
		
}

?>