<?php
	if (!defined('PHPUnit_MAIN_METHOD')) {
    	define('PHPUnit_MAIN_METHOD', '_UNIT_Canvas::main');
		$dir_include = get_include_path().":".getcwd()."/../Demo/";

		set_include_path($dir_include);
		
		chdir(getcwd()."/../Demo/");
		
		require_once "_UNIT_Canvas.application.class.php";
	    require_once "ApplicationLis.php";

	}

	//require_once "Canvas.application.class.php";
	
	/**
	 * Class pour les test unitaire de l'application canvas
	 * 
	 * @author jean pasqualini <jpasqualini75@gmail.com>
	 * @license GPL
	 * @version InDev
	 */
	class _UNIT_Canvas extends PHPUnit_Framework_TestCase implements PHPUnit_Framework_Test {
		
		
		public static function main(){
			$suite = new PHPUnit_Framework_TestSuite("_UNIT_Canvas");
			$result = PHPUnit_TextUI_TestRunner::run($suite);
			
			return $suite;
		}
		
		/**
		 * Cette méthode est appelé avant l'exécution des tests
		 * @access public
		 */
		public function setUp(){
			/*
				$temp = new ReflectionClass("Canvas");
				$this->test = $temp->newInstanceWithoutConstructor();
			*/
						
			// On instancie l'application canvas
			$this->test = Canvas::NotConstructor();
		}
		
		/**
		 * Le test de la méthode qui récupere l'instance de l'application
		 * @access public
		 */
		public function testIntance(){
			//$this->setExpectedException('LisException');	
			
			$instance =  ApplicationLIS::GetInstance();
			
			$this->assertNotNull($instance, "L'instance est null");
			$this->assertInstanceOf("ApplicationLIS", $instance);
		}

		//$this->setExpectedException("ModuleNotLoadedException");
		/**
		 * Le test de l'ajout de module a chaud
		 * @access public
		 * @expectedException ModuleNotLoadedException
		 */
		public function testModuleNotModuleLoadedException()
		{
			$module = $this->test->AddModule("test");			
		}
		
		public function testAddModule()
		{
			$module = $this->test->AddModule("canvas");
			
			$this->assertNotNull($module);
			$this->assert
		}
		
	}
		
// Call MyClassTest::main() if this source file is executed directly.
if(PHPUnit_MAIN_METHOD == '_UNIT_Canvas::main') {
    _UNIT_Canvas::main();
}
	
?>