<?php
/**
 * Ce module un peu bizare permet d'appeler des méthode de module client directement depuis l'application
 * @author Jean pasqualini <jpasqualini75@gmail.com>
 * @license GPL
 * @version InDev
 * @package ModuleLis
*/
class module_ModuleClientProxy extends ModuleBase implements IModuleBase
{
	  /**
	   * Recupère le nom du module
	   * @access public
	   * @return string Le nom du module
	  */
	  public function GetModuleName()
	  {
	    return "Canvas";
	  }
	
	  /**
	   * Récupere la description du module
	   * @access public
	   * @return string La description du module
	  */
	  public function GetModuleDescription()
	  {
	      return "Proxy direct vers l'appelle des méthode des modules client";
	  }
	  
	  /**
	   * Récupere la version du module
	   * @access public
	   * @return string La version du module
	  */
	  public function GetVersion()
	  {
	      return "1.0";
	  }
	  
	  /**
	   * Récupere les dépendances module serveur
	   * @access public
	   * @return Array Les dépendances serveur
	  */
	  public function GetDependanceServer()
	  {
	    // Declare les modules dont serveur nous avons besoin
	    return array();
	  }
	  
	  /**
	   * Récupere les dépendances module client
	   * @access public
	   * @return Array Les dépendances client
	  */
	  public function GetDependanceClient()
	  {
	    return array(); 
	  }
	
	
	/**
	 * @access public
	 * Méthode magique permettant d'appelr des méthode d'un module client directement depuis l'application
	*/
	public function __call($method,$arguments)
	{
		parent::__call($method,$arguments);
	}
}
?>