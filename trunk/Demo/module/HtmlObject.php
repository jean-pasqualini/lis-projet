<?php
Class module_HtmlObject extends ModuleBase implements IModuleBase
{

	public function GetModuleName()
	{
		return "HtmlObject";
	}
	
	public function GetModuleDescription()
	{
		return "Gestion d'objet html";
	}
	
	public function GetVersion()
	{
		return "1.0";
	}
	
	public function GetDependanceServer()
	{
		// Declare les modules serveur  dont nous avons besoin
		return array();
	}
	
	public function GetDependanceClient()
	{
		return array();
	}
	
	public function __construct()
	{
		parent::__construct();
		Object::RegisterObject(new DIV());
		Object::RegisterObject(new INPUT());
		Object::RegisterObject(new TEXTAREA());
		Object::RegisterObject(new Select());
		Object::RegisterObject(new CANVAS());
	}	
}

Class CANVAS extends Object2D {
	
}

Class DIV extends Object2D {
	
}

Class INPUT extends Object2d {
	
}

Class TEXTAREA extends Object2D {
	
}

Class SELECT extends Object {
	
}

Class Option {
	
}


