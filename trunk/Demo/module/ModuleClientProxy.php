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
	 * @access public
	 * Méthode magique permettant d'appelr des méthode d'un module client directement depuis l'application
	*/
	public function __call($method,$arguments)
	{
		__call($method,$arguments);
	}
}
?>