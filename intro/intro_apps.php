<?php


class Intro_Apps
{

	
	
	function __construct()
	{
	
	/* save app instance */
	intro::instance($this,'app');

	/* instantiate load library */
	//$this->load = new TinyMVC_Load;  

	/* instantiate view library */
	//$this->view = new TinyMVC_View;
	}
	/**
	 * index
	 *
	 * the default controller method
	 *
	 * @access	public
	 */    
  function index() { }

	/**
	 * __call
	 *
	 * gets called when an unspecified method is used
	 *
	 * @access	public
	 */    
  function __call($function, $args) {
	
	pHeader();
	
    echo "<h2>404 Error: the page ({$function}) is not found.</h2>";
	
	pFooter();

  }
  
}

?>
