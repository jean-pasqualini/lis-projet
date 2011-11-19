<?php
/**
 * Classe proxy permet d'attacher des objet de meme type
 * et d'executer une action sur ce groupe d'objet
 * @todo Implementer SplSubject du pattern observer
 * @author Jean pasqualini <jpasqualini75@gmail.com>
 * @version InDev
 * @license GPL
 * @package FrameworkLis
*/
class SProxy {
	
	/**
	 * @access private;
	 * @var Array $object_ Contients les instances d'objets
	*/
	private $objets_ = array();
	
	/**
	  * Contructeur de Sproxy
	  * @access public
	  * @param Array $objets_ Listes d'objet
	  * @return SProxy Instance de Sproxy
	*/
	public function __construct($objets = array())
	{
		// Définit les objets
		$this->objets_ = $objets;
	}
	
	/**
	 * Méthode maguique __call permettant d'appliquer une action de facon globale sur les objets
	 * @access public
	 * @method null votremethode()
	 * @param string $methode Méthode a appeler
	 * @param Array $arguments Listes des arguments
	*/
	public function __call($method,$arguments)
	{
		// Parcour les objets
		foreach($this->objets_ as $objet)
		{
			//Appelle l'action sur chaque objet
			call_user_func(array($objet,$method),$arguments);
		}
	}
	
	/**
	 * Permet d'ajouter un objet au proxy
	 * @access public
	 * @param Object $objet L'instance de l'objet
	*/
	public function SProxy_Add($objet)
	{
		// Ajoute u objet au tableau
		$this->objets_[] = $objet;
	}
}
?>