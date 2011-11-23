<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'AllTests::main');
}

	$dir_include = getcwd()."/../Demo/:".getcwd()."/";

	set_include_path($dir_include);
	
	chdir(getcwd()."/../Demo/");
	
    require_once "ApplicationLis.php";
	require_once "_UNIT_Canvas.application.class.php";

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

    }
}

if (PHPUnit_MAIN_METHOD == 'AllTests::main') {
   AllTests::main();
}
?>