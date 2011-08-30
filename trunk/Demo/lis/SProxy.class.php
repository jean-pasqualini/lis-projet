<?php
class SProxy {
	private $objets_ = array();
	
	public function __construct($objets = array())
	{
		$this->objets_ = $objets;
	}
	
	public function __call($method,$arguments)
	{
		foreach($this->objets_ as $objet)
		{
			call_user_func(array($objet,$method),$arguments);
		}
	}
	
	public function SProxy_Add($objet)
	{
		$this->objets_[] = $objet;
	}
}
?>