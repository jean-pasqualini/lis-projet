<?php
class module_WebModule extends ModuleBase implements IModuleBase
{
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function AddModule($name)
	{
		return ApplicationLIS::GetInstance()->AddModule("WebModuleInstance",$parametres);
	}

}
?>