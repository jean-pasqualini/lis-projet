<?php
/**
  * Ce module permet de charger un WebModule écrit ou pas dans un language différent que celui de l'application
  * @author Jean pasqualini <jpasqualini75@gmail.com>
  * @license GPL
  * @version InDev
  * @package ModuleLis
*/
class module_WebModule extends ModuleBase implements IModuleBase
{
	/**
	 * Contructeur du module 'WebModule'
	 * @access public
	 * @return module_WebModule Retourne l'instance du module WebModule
	*/
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * Permet d'ajoute un webmodule comme un module standart et de recuperer son instance à travers
	 le module WebModuleInstance pour le manipuler de facon transparente
	 * @access public
	 * @param string $name Nom du module à charger
	 * @return module_WebModuleInstance Retoure une instance de WebModuleInstance
	*/
	public function AddModule($name)
	{
		return ApplicationLIS::GetInstance()->AddModule("WebModuleInstance",$parametres);
	}

}
?>