<?php
/**
  * Module de rendu visuelle HTML (non utile) 
  * @author Jean pasqualini <jpasqualini75@gmail.com>
  * @license GPL
  * @version InDev
  * @package ModuleLis
*/
Class module_HtmlObject extends ModuleBase implements IModuleBase
{

	/**
	 * Recupére le nom du module
	 * @access public
	 * @return string Le nom du module
	*/
	public function GetModuleName()
	{
		return "HtmlObject";
	}
	
	/**
	 * Récupere la description du module
	 * @access public
	 * @return string La description du module
	*/
	public function GetModuleDescription()
	{
		return "Gestion d'objet html";
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
	 * Le constructeur du module HTMLObject
	 * @access public
	 * @return HtmlObject Retourne l'instance de HtmlObject
	*/
	public function __construct()
	{
		parent::__construct();
		
		// Enregistre l'objet 2D DIV
		Object::RegisterObject(new DIV());
		
		// Enregistre l'objet 2D INPUT
		Object::RegisterObject(new INPUT());
		
		// Enregistre l'objet 2D TEXTAREA
		Object::RegisterObject(new TEXTAREA());
		
		// Enregistre l'objet 2D Select
		Object::RegisterObject(new Select());
		
		// Enregistre l'objet 2D CANVAS
		Object::RegisterObject(new CANVAS());
	}	
}

/**
  * L'objet 2D CANVAS
  * @author Jean pasqualini <jpasqualini75@gmail.com>
  * @license GPL
  * @version InDev
*/
Class CANVAS extends Object2D {
	
}

/**
  * L'objet 2D DIV
  * @author Jean pasqualini <jpasqualini75@gmail.com>
  * @license GPL
  * @version InDev
*/
Class DIV extends Object2D {
	
}

/**
  * L'objet 2D INPUT
  * @author Jean pasqualini <jpasqualini75@gmail.com>
  * @license GPL
  * @version InDev
*/
Class INPUT extends Object2d {
	
}

/**
  * L'objet 2D TEXTAREA
  * @author Jean pasqualini <jpasqualini75@gmail.com>
  * @license GPL
  * @version InDev
*/
Class TEXTAREA extends Object2D {
	
}

/**
  * L'objet 2D SELECT
  * @author Jean pasqualini <jpasqualini75@gmail.com>
  * @license GPL
  * @version InDev
*/
Class SELECT extends Object {
	
}

/**
  * L'objet 2D OPTION
  * @author Jean pasqualini <jpasqualini75@gmail.com>
  * @license GPL
  * @version InDev
*/
Class Option {
	
}


