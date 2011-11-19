<?php
/**
 * Module de Gestion d'element d'interface utilisateur du module de rendu Html
 * Comme les Tabs , Dialog etc...
 * @author Jean pasqualini <jpasqualini75@gmail.com>
 * @license GPL
 * @version InDev
 * @package ModuleLis
*/
Class module_JqueryObject extends ModuleBase implements IModuleBase {

	/**
	 * Recupére le nom du module
	 * @access public
	 * @return string Le nom du module
	*/
	public function GetModuleName()
	{
		return "JqueryObject";
	}
	
	/**
	 * Récupere la description du module
	 * @access public
	 * @return string La description du module
	*/
	public function GetModuleDescription()
	{
		return "Gestion d'objet jquery";
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
		// Declare les modules serveur  dont nous avons besoin
		return array(
	          "HtmlObject"  => array("Version" => "1.0"  , "Url" => "")
		);
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
	 * Le constructeur du module JqueryObject
	 * @access public
	 * @return JqueryObject Retourn l'instance de JqueryObject
	*/
	public function __construct()
	{
		// Appelle le constructeur parent
		parent::__construct();
		
		// Enregistre l'objet 2D TABS
		Object::RegisterObject(new TABS());
		
		// Enregistre l'objet 2D MODAL
		Object::RegisterObject(new MODAL());
	}
	
}

/**
  * L'objet 2D TABS
  * @author Jean pasqualini <jpasqualini75@gmail.com>
  * @license GPL
  * @version InDev
*/
Class TABS extends Object2D {
	
}

/**
  * L'objet 2D MODAL
  * @author Jean pasqualini <jpasqualini75@gmail.com>
  * @license GPL
  * @version InDev
*/
Class MODAL extends Object2D {
	
}