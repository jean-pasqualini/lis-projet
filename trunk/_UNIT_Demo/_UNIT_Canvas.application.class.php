<?php
require_once("_UNIT_Configuration.php");

class InterceptCommunicationLis {
	private $instanceLis;
	private $bufferread;
	//private $bufferwrite;
	
	public function __construct($instanceLis)
	{
		$this->instanceLis = $instanceLis;	
	}
	
	public function write($msg)
	{
		echo "Ecriture du buffer : \r\n".print_r(json_decode($msg), true)."\r\n";
		
		$this->bufferread .= $msg;
	}
	
	public function isSocket()
	{
		if(!empty($this->bufferread));
	}
		
	public function read($taille = 2048)
	{
		return $this->bufferread;
	}
	
	public function close()
	{
		socket_close($this->socket);
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