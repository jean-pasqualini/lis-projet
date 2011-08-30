<?php
class Canvas extends ApplicationLIS {
    

    private $temp_limite;
    private $max=30; // Demonstration limite a 30 seconde ...
    private $text;
    private $souris;
        
    function __construct ($address,$port)
    { 
        //Lance l'attende de la connection d'un client
        parent::__construct($address,$port);
   
		// Ajout a la volée du module d'interaction utilisateur    
		$this->AddModule("InterfaceKM");
		$this->AddModule("ModuleClientProxy");
	
		$this->SetHandle_mouse_move(new EventProxy(function($x,$y)
		{
			echo "La souris a bouger a ".$x.",".$y;
		}));
	
		$this->AddModule("CanvasObject");
		$this->AddModule("UserInterface");
			
		//Cree l'objet souris
		$this->souris=Object::GetObject("ROND")->AddObject();
		
		// Cree un rectangle
		$Rectangle1=Object::GetObject("RECTANGLE")->AddObject()->Set(0,0,150,150)->addClass("max");
		
		Object::DrawnAllObjects();
		
		while(1)
		{
		   // Object::SProxy("RECTANGLE.max")->SetBackground("red"); 	    
		}
	
    }
    
    public function KeyPress($ascii_code)
    {
		$data = ApiManageLis::GetApps();
		
		$texte = Object::GetObject("TEXTE")->AddObject()->Set(80,80);
		$texte -> SetText(count($data)." Applications lancer\n");
		foreach($data as $instance)
		{
			$texte -> AddTextL("Application : ".$instance["application"]);
		}
    }
    
    public function MouseMove($positionX,$positionY)
    {
		$this->souris->MoveTo($positionX,$positionY);
		Object::DrawnAllObjects();
    }

    public function MouseClick($x,$y)
    {
		$this->FillStyle("blue");
		$this->TextFill("Click !!!",50,50);
		
			// Joue une note (ref : tableau de frequence ci dessus)
			$this->PlayNote(rand(293.6648,391.9954));
			$this->StopNote();
		ApiManageLis::SendMsgToApps("yeeeeh");
    }
    
    public function quitter($x,$y)
    {
	    $this->TextFill("Vous avez quittez l'application",50,50);
	    exit();
	    //$this->ExitApplication();
    }
    
}

Class ApiManageLis {
    public static function GetApps()
    {
		return json_decode(file_get_contents("http://127.0.0.1:3809/GetApps.json"),true);
    }
    
    public static function Shutdown()
    {
		file_get_contents("http://127.0.0.1:3809/Shutdown.json");
    }
    
    public static function SendMsgToApps($texte)
    {
		file_get_contents("http://127.0.0.1:3809/SendMsgToApps.json?msg=".$texte);
    }
}
?>