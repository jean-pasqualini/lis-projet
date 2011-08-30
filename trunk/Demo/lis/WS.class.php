<?php  

Abstract class ModuleBase {

  protected $handle_application;
  
  public function __construct()
  {
  	$this->handle_application=ApplicationLis::GetInstance();
  }
  
  public function send($msg){  
	$this->handle_application->send($msg);
  }
  
  public function recv($taille=2048)
  { 
	return $this->handle_application->recv($taille);
  }
  
	
	private function __call($method,$arguments)
	{
		$data=array_merge(array("Type" => $this->GetModuleName(), "Action" => $method) ,$arguments);
		$this->send($data);
	}
}

?>