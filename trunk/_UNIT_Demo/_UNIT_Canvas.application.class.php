<?php
	$dir_include = getcwd()."/../Demo/:".getcwd()."/";

	set_include_path($dir_include);
	
    require_once "ApplicationLis.php";
	require_once "Canvas.application.class.php";
	
	class _UNIT_Canvas extends PHPUnit_Framework_TestCase {
		
		public function setUp(){
			$this->test = new Canvas("127.0.0.1", 1601);
		}
		
		public function testName(){
			$jason = $this->test->getName();
			$this->assertTrue($jason == "Jason");
		}
	}
	
?>