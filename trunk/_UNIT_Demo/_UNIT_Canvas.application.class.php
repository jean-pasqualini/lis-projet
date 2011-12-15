<?php
	if (!defined('PHPUnit_MAIN_METHOD')) {
    	define('PHPUnit_MAIN_METHOD', '_UNIT_Canvas::main');
		$dir_include = get_include_path().":".getcwd()."/../Demo/";

		set_include_path($dir_include);
		
		chdir(getcwd()."/../Demo/");
		
		require_once "_UNIT_Canvas.application.class.php";
	    require_once "ApplicationLis.php";

	}

class InterceptCommunicationLis {
	private $instanceLis;
	
	public function __construct($instanceLis)
	{
		$this->instanceLis = $instanceLis;	
	}
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
			
			$this->test->setSocket(new InterceptCommunicationLis($this->test));
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
		 * Le test de l'ajout de module non existant a chaud
		 * @access public
		 * @expectedException ModuleNotLoadedException
		 */
		public function testModuleNotModuleLoadedException()
		{
			$module = $this->test->AddModule("test");			
		}
		
		/**
		 * Le test de l'ajout du module Canvas a chaud 
		 * @access public 
		 */
		public function testAddModuleCanvas()
		{
			$module = $this->test->AddModule("canvas");
			
			$this->assertNotNull($module);
			$this->assertInstanceOf("IModuleBase", $module);
			$this->assertInstanceOf("ModuleBase", $module);
			$this->assertInstanceOf("module_canvas", $module);
		}

		/**
		 * Le test de l'ajout du module CanvasObject a chaud 
		 * @access public 
		 */
		public function testAddModuleCanvasObject()
		{
			$module = $this->test->AddModule("CanvasObject");
			
			$this->assertNotNull($module);
			$this->assertInstanceOf("IModuleBase", $module);
			$this->assertInstanceOf("ModuleBase", $module);
			$this->assertInstanceOf("module_CanvasObject", $module);
		}

		/**
		 * Le test de l'ajout du module InterfaceKM a chaud 
		 * @access public 
		 */
		public function testAddModuleInterfaceKM()
		{
			$module = $this->test->AddModule("InterfaceKM");
			
			$this->assertNotNull($module);
			$this->assertInstanceOf("IModuleBase", $module);
			$this->assertInstanceOf("ModuleBase", $module);
			$this->assertInstanceOf("module_InterfaceKM", $module);
		}
		
		/**
		 * Le test de l'ajout du module UserInterface a chaud 
		 * @access public 
		 */
		public function testAddModuleUserInterface()
		{
			$module = $this->test->AddModule("UserInterface");
			
			$this->assertNotNull($module);
			$this->assertInstanceOf("IModuleBase", $module);
			$this->assertInstanceOf("ModuleBase", $module);
			$this->assertInstanceOf("module_UserInterface", $module);
		}	
	}
		
// Call MyClassTest::main() if this source file is executed directly.
if(PHPUnit_MAIN_METHOD == '_UNIT_Canvas::main') {
    _UNIT_Canvas::main();
}
	
?>