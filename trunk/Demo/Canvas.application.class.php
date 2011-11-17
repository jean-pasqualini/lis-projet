<?php
/*
 Classe principale de l'application 'canvas'
 @author Jean pasqualini <jpasqualini75@gmail.com>
 @version InDev
 @license GPL
*/
class Canvas extends ApplicationLIS {
    
    /*
     @access private
     @var integer D�fini le temp limite d'�x�cution de la D�monstration
    */
    private $temp_limite;
    
    /*
     @access private
     @var string Contient un texte qui sera afficher
    */
    private $text;
    
    /*
     @access private
     @var Object Contient la souris sous forme d'objet afficher a l'�cran
    */
    private $souris;
        
    /*
     Le contructeur de l'application canvas
     @access public
     @param string $address L'adresse d'�coute pour l'application d� pr�f�rence 127.0.0.1
     @param integer $port Le port d'�coute de l'application
     @return Canvas Retourne l'instance de l'application 'Canvas'
    */
    public function __construct ($address,$port)
    {
        // Lance l'attente de la connection d'un client lis
        parent::__construct($address,$port);
	
		// Ajout du module 'InterfaceKM' d'interaction utilisateur   
		$this->AddModule("InterfaceKM");
		
		// Ajout du Module ClientProxy (inutile ici)
		$this->AddModule("ModuleClientProxy");
		
		// D�finit pour handle de position de la souris un objet de type EventProxy qui contient ici une fonction anonyme
		$this->SetHandle_mouse_move(new EventProxy(function($x,$y)
		{
			echo "La souris a bouger a ".$x.",".$y;
		}));
		
		// Ajout du module 'CanvasObject' pour la gestion des canvas en objet 
		$this->AddModule("CanvasObject");
		
		// Ajout du module 'UserInterface' pour la cr�ation d'interface utilisateur
		$this->AddModule("UserInterface");
			
		// Cree un objet de type 'ROND' qui sera la souris
		$this->souris=Object::GetObject("ROND")->AddObject();
		
		// Cree un rectangle de 150x150 avec pour classe max
		$Rectangle1=Object::GetObject("RECTANGLE")->AddObject()->Set(0,0,150,150)->addClass("max");
		
		// Ordonne de dessiner tous les ojbet pr�c�dament cr�e sur la sortie de l'application
		Object::DrawnAllObjects();
		
		while(1)
		{
		   // Object::SProxy("RECTANGLE.max")->SetBackground("red"); 	    
		}
	
    }
    
    /*
     Keypress � �t� enregistrer en tant que handle pour recevoir les entr�e clavier de l'utilisateur
     @access public
     @param string @ascii_code La lettre tap� par l'utilisateur
    */
    public function KeyPress($ascii_code)
    {
		// Recupere la listes des application lanc�e;
		$data = ApiManageLis::GetApps();
		
		// D�clare un objet de type texte pour position 80x80
		$texte = Object::GetObject("TEXTE")->AddObject()->Set(80,80);
		
		// Affiche le nombre d'application lancer
		$texte -> SetText(count($data)." Applications lancer\n");
		
		foreach($data as $instance)
		{
			// Ajouter une ligne pour le nom de chaque application lancer
			$texte -> AddTextL("Application : ".$instance["application"]);
		}
    }
    
    /*
      MouseMove � �t� enregister en tant que handle pour recevoir les changements de position de la souris
      @access public
      @param integer $positionX La position en X de la souris
      @param integer $positionY La position en Y de la souris
    */
    public function MouseMove($positionX,$positionY)
    {
		// D�place la souris
		$this->souris->MoveTo($positionX,$positionY);
			
		// Redessine tous les objets
		Object::DrawnAllObjects();
    }

    /*
      MouseCLick � �t� enregistrer en tant que handle pour recevoir les click de la souris
      @access public
      @param integer $x La position en X de la souris
      @param integer $y La position en Y de la souris
    */
    public function MouseClick($x,$y)
    {
	/*
	 @todo Utilise encore le module de bas niveau 'Canvas'
	       Utiliser le module de haut niveau 'CanvasObject'
	*/
	
		// D�fini la couleur de remplisage du canvas 
		$this->FillStyle("blue");
		
		// Ecrit un texte a la position 50x50
		$this->TextFill("Click !!!",50,50);
		
		// Joue une note (ref : tableau de frequence ci dessus)
		$this->PlayNote(rand(293.6648,391.9954));
		
		// Stop la note
		$this->StopNote();
		
		// Envoie un message a tous les utilisateur connecter au serveur d'application
		ApiManageLis::SendMsgToApps("yeeeeh");
    }
    
    /*
     Quitte l'application
     
     @access public
     @param integer $x La position en X de la souris
     @param integer $y La position en Y de la souris
    */
    public function quitter($x,$y)
    {
	    $this->TextFill("Vous avez quittez l'application",50,50);
	    exit();
	    //$this->ExitApplication();
    }
    
}


/*
  Api de gestion du serveur d'application depuis l'application
  @author Jean Pasqualini <jpasqualini75@gmail.com>
  @version InDev
  @license GPL
*/
Class ApiManageLis {
    
    /*
     Cette fonction retour la listes des application lanc� sur le serveur d'application
     @access public
     @static
     @return Array La liste des applications lanc�e sur le serveur d'application
    */
    public static function GetApps()
    {
		return json_decode(file_get_contents("http://127.0.0.1:3809/GetApps.json"),true);
    }
    
    /*
     Cette fonction ordonne l'arret du serveur d'application
     @access public
     @static
    */
    public static function Shutdown()
    {
		file_get_contents("http://127.0.0.1:3809/Shutdown.json");
    }
    
    /*
     Cette fonction envoie un message a tous les utilisateur connect� au serveur d'application
     @access public
     @static
    */
    public static function SendMsgToApps($texte)
    {
		file_get_contents("http://127.0.0.1:3809/SendMsgToApps.json?msg=".$texte);
    }
}
?>