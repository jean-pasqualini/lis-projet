<?php
/**
 * Class de proxy évenementiel afin d'enregistrer plusieur handler sur des évenement
 * Et de les notifier tous à la fois
 * @todo Implementer SplSubject du pattern observer
 * @author Jean pasqualini <jpasqualini75@gmail.com>
 * @version InDev
 * @license GPL
 * @package FrameworkLis
*/
class EventProxy {
	
	/**
	 * @access private
	 * @var Instance contient l'instance d'une classe du handler
	*/
	private $instance = null;
	
	/**
	 * @access private
	 * @var string contient le nom de la méthode du handler
	*/
	private $methode = null;
	
	/**
	 * Contructeur de l'event proxy
	 * @access public
	 * @param Instance $instance Instance de l'handler
	 * @param string $methode Methode du handleur
	 * @return EventProxy Retourne l'instance courante
	*/
	public function __construct($instance,$methode = null)
	{
		$this->instance = $instance;
		$this->methode = $methode;
	}
	
	/**
	 * Appelle une methode magique sur le proxy avec __call
	 * @access public
	 * @param string $methode Methode appeler sur l'instance d'handler
	 * @param Array $arguments Tableau de paramétre à passer a la méthode
	*/
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