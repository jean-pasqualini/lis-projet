<?php
class EventProxy {
	
	private $instance = null;
	private $methode = null;
	
	public function __construct($instance,$methode = null)
	{
		$this->instance = $instance;
		$this->methode = $methode;
	}
	
	public function __call($method,$arguments)
	{
		if($this->methode != null) $method = $this->methode;
		
		if(is_object($this->instance))
		{
			call_user_func(array($this->instance,$method),$arguments);
		}
		elseif(is_callable($this->instance))
		{
			call_user_func($this->instance,$arguments);
		}
	}

}
?>