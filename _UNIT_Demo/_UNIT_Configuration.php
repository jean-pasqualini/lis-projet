<?php
	if (!defined('PHPUnit_MAIN_METHOD')) {
    	define('PHPUnit_MAIN_METHOD', '_UNIT_Canvas::main');
		
		$dir_include = get_include_path().":".getcwd()."/../Demo/";

		set_include_path($dir_include);
		
		chdir(getcwd()."/../Demo/");
		
		require_once "_UNIT_Canvas.application.class.php";
		
	    require_once "ApplicationLis.php";

	}
?>