<?php

    class _UNIT_module_Canvas extends PHPUnit_Framework_TestCase implements PHPUnit_Framework_Test {
    	
    	public static function main(){
			$suite = new PHPUnit_Framework_TestSuite("_UNIT_module_Canvas");
			$result = PHPUnit_TextUI_TestRunner::run($suite);
			
			return $suite;
		}
		
		/**
		 * Cette méthode est appelé avant l'exécution des tests
		 * @access public
		 */
		public function setUp(){
			$this->test = ApplicationLIS::GetModule("Canvas");
		}
		
		public function testFillRect()
		{
			$this->test->FillRect(5,5,40,40);
		}
		
    }
?>