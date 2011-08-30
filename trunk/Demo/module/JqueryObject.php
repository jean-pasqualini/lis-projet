<?php
Class module_JqueryObject extends ModuleBase implements IModuleBase {

	public function GetModuleName()
	{
		return "JqueryObject";
	}
	
	public function GetModuleDescription()
	{
		return "Gestion d'objet jquery";
	}
	
	public function GetVersion()
	{
		return "1.0";
	}
	
	public function GetDependanceServer()
	{
		// Declare les modules serveur  dont nous avons besoin
		return array(
	          "HtmlObject"  => array("Version" => "1.0"  , "Url" => "")
		);
	}
	
	public function GetDependanceClient()
	{
		return array();
	}
	
	public function __construct()
	{
		parent::__construct();
		Object::RegisterObject(new TABS());
		Object::RegisterObject(new MODAL());
	}
	
}

Class TABS extends Object2D {
	
}

Class MODAL extends Object2D {
	
}