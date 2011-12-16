<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'AllTests::main');
	
	$dir_include = get_include_path().":".getcwd()."/../Demo/";

	set_include_path($dir_include);
	
	chdir(getcwd()."/../Demo/");
	
	require_once "_UNIT_Canvas.application.class.php";
	require_once "module/_UNIT_Canvas.php";
    require_once "ApplicationLis.php";
	
}



class AllTests 
{
	public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }
	
	public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('All Tests');
        $suite->addTest(_UNIT_Canvas::main());
		$suite->addTest(_UNIT_module_Canvas::main());

    }
}

if (PHPUnit_MAIN_METHOD == 'AllTests::main') {
   AllTests::main();
}
?>