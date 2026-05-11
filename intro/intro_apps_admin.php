<?php


class Intro_AppsAdmin
{

	function __construct()
	{

		/* save app instance */
		intro::instance($this,'app');
		
		

	}

	function index() { 
		//default empty
	}

	#called when an unspecified method is used   
	function __call($function, $args) {
        	throw new Exception("Unknown app_admin method '{$function}'");

	}
	

  
  
  
}

?>
