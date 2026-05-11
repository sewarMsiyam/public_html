<?php
$config['base_url'] = '/';
#database
$config['db']['hostname'] = 'localhost';
$config['db']['username'] = 'buyform1_web';
$config['db']['password'] = 'gE9MXNgeEUut';
$config['db']['database'] = 'buyform1_web';
###########3
/*
$config['db']['username'] = 'root';
$config['db']['password'] = '';
$config['db']['database'] = '2015_buyformula';
*/
$config['db']['charset'] = 'utf8';
$config['db']['collation'] = 'utf8_general_ci';

#first path is lang www.maa.com/ar/app/index
$config['lang_is_prefix'] = true;

$config['timer'] = true;
#site defaults

$config['site']['cache_path'] = '';
$config['site']['gzip'] = false;

define("PREFIX", "maa");
define("_CHARSET", "utf8");
define("MBSTRING", "true");

/* URL routing, use preg_replace() compatible syntax */
$config['routing']['search'] =  array();
$config['routing']['replace'] = array();
/* set this to force controller and method instead of using URL params */
$config['root_app'] = null;
$config['root_action'] = null;
/* name of default controller/method when none is given in the URL */
$config['default_app'] = 'home';
$config['default_action'] = 'index';
$config['default_lang'] = 'ar';

$config['lang'] = 'ar';
$config['permitted_uri_chars'] = '�-�a-z 0-9~%.:_\-';
$config['enable_query_strings'] = FALSE;

?>