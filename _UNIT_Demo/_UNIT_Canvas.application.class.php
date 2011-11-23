<?php
	if (!defined('PHPUnit_MAIN_METHOD')) {
    	define('PHPUnit_MAIN_METHOD', '_UNIT_Canvas::main');
	}

	//require_once "Canvas.application.class.php";
	
	/**
	 * Class pour les test unitaire de l'application canvas
	 * @author jean pasqualini <jpasqualini75@gmail.com>
	 * @license GPL
	 * @version InDev
	 */
	class _UNIT_Canvas extends PHPUnit_Framework_TestCase {
		
		
		public static function main(){
			$suite = new PHPUnit_Framework_TestSuite("_UNIT_Canvas");
			$result = PHPUnit_TextUI_TestRunner::run($suite);
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
			echo "Instanciation de l'application...\r\n";
						
			// On instancie l'application canvas
			//$this->test = Canvas::NotConstructor();
		}
		
		/**
		 * Le test de la méthode qui récupere l'instance de l'application
		 * @access public
		 * @expectedException LisException
		 */
		public function testIntance(){
			
				$instance =  ApplicationLIS::GetInstance();

				$this->assertNotNull($instance, "L'instance est null");
				$this->assertInstanceOf("ApplicationLIS", $instance);
		}
	}
		
// Call MyClassTest::main() if this source file is executed directly.
if(PHPUnit_MAIN_METHOD == '_UNIT_Canvas::main') {
    _UNIT_Canvas::main();
}
	
?>