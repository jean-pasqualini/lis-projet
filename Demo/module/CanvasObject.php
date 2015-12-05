<?php
/**
  Module de gestion en tant qu'objet des élement déssiner par le module 'Canvas'
  Il permet de manipuler leur propiété comme un objet
  
  * @author Jean pasqualini <jpasqualini75@gmail.com>
  * @license GPL
  * @version InDev
*/
Class module_CanvasObject extends ModuleBase implements IModuleBase
{

  /**
   Recupére le nom du module
   * @access public
   * @return string Le nom du module
  */
  public function GetModuleName()
  {
    return "CanvasObject";
  }

  /**
   Récupere la description du module
   * @access public
   * @return string La description du module
  */
  public function GetModuleDescription()
  {
      return "Gestion 2D en objet";
  }
  
  /**
   Récupere la version du module
   * @access public
   * @return string La version du module
  */
  public function GetVersion()
  {
      return "1.0";
  }
  
  /**
   Récupere les dépendances module serveur
   * @access public
   * @return Array Les dépendances serveur
  */
  public function GetDependanceServer()
  {
    // Declare les modules serveur  dont nous avons besoin
    return array(
          "Canvas"  => array("Version" => "1.0"  , "Url" => ""),
          "InterfaceKM" => array("Version" => "1.0" , "Url" => "")
    );
  }
  
  /**
   Récupere les dépendances module client
   * @access public
   * @return Array Les dépendances client
  */
  public function GetDependanceClient()
  {
    return array(); 
  }
  
  /**
   Contructeur du module
   * @access public
   * @return CanvasObject Retourne l'instance du module
  */
  public function __construct()
  {
    // On appele le constructeur parent
    parent::__construct();
    
    // On enregistre l'objet 2D RECTANGLE
    Object::RegisterObject(new RECTANGLE());
    
    // On enregistre l'objet 2D LIGNE
    Object::RegisterObject(new LIGNE());
    
    // On enregistre l'objet 2D ROND
    Object::RegisterObject(new ROND());
    
    // On enregistre l'objet 2D TEXTE
    Object::RegisterObject(new TEXTE());
  }
}

/**
 Objet 2D primaire : Le rectangle
 * @author Jean pasqualini <jpasqualini75@gmail.com>
 * @license GPL
 * @version InDev
*/
Class RECTANGLE extends Object2D
{
  /**
   Modifie la position et la taille du rectangle
   * @access public
   * @param int $x La position en X
   * @param int $y La position en Y
   * @param int $w La largeur
   * @param int $h La hauteur
   * @return RECTANGLE Retourne l'instance du rectangle
  */
  public function Set($x,$y,$w,$h)
  {
        //On informe de la mise a jour de l'objet
        $this->SetUpdated(true);
        
	// On modifie la position et la taille
        $this->PositionX=$x;
        $this->PositionY=$y;
        $this->Height=$h;
        $this->Width=$w;
	
	// Et on retourne l'instance du rectangle
        return $this;
  }
    
  /**
   Dessine l'objet
   * @access public
  */
  public function DrawnObject()
  {
    // On parametre la couleur de fond
    ApplicationLIS::GetModule("Canvas")->FillStyle($this->Background);
    
    echo "ICIII";
    // On désssine le rectangle
    ApplicationLIS::GetModule("Canvas")->FillRect($this->GetAbsolutePositionX(), $this->GetAbsolutePositionY(), $this->Width, $this->Height);
  }
}

/**
 Objet 2D : Un texte
 * @author Jean pasqualini <jpasqualini75@gmail.com>
 * @license GPL
 * @version InDev
*/
Class TEXTE extends Object2D
{
  /**
   * @access private
   * @var string Contenu du texte
  */
  private $text;
  
  /**
   * @access private
   * @var int La taille du texte en pixel
  */
  private $size;
  
  /**
   * @access private
   * @var string La police du texte
  */
  private $font;
  
  /**
   * @access public
   * @staticvar string Le type de retour a la ligne
  */
  const ReturnLine = "\r\n";
  
  /**
   * Modifie la taille du texte
   * @access public
   * @param int $value La taille du texte en pixel
   * @return TEXTE Retourne l'instance du texte
  */
  public function SetSize($value)
  {
    $this->size = $value;
    return $this;
  }
  
  /**
   * Modifie la police du texte
   * @access public
   * @param string $value La police du texte
   * @return TEXTE Retourne l'instance du texte
  */
  public function SetFont($value)
  {
    $this->font = $value;
    return $this;
  }
  
  /**
   * Modifie le contenu du texte
   * @access public
   * @param string $text Le contenu du texte
   * @return TEXTE Retourne l'instance du texte
  */
  public function SetText($text)
  {
    $this->text = $text;
    return $this;
  }
  
  /**
   * Ajoute du contenu au contenu courant
   * @access public
   * @param string $text Le contenu à ajouter
   * @return TEXTE Retourne l'instance du texte
  */
  public function AddText($text)
  {
	$this->text .= $text;
	return $this;
  }
  
  /**
   * Ajoute une ligne de texte au contenu courant
   * @access public
   * @param stirng $text Le contenu de la ligne de texte à ajouter
   * @return TEXTE Retourne l'instance du texte
  */
  public function AddTextL($text)
  {
	// Ajoute le texte plus un retour à la ligne
	return $this->AddText($text.TEXTE::ReturnLine);
  }
  
  /**
   * Modifie la position du texte
   * @access public
   * @param int $x La position en X du texte
   * @param int $y La position en Y du texte
   * @return TEXTE Retourne l'instance du texte
  */
  public function Set($x,$y)
  {
    //On informe de la mise a jour de l'objet
    $this->SetUpdated(true);
        
	// On modifie la position
        $this->PositionX=$x;
        $this->PositionY=$y;
        
	// On retourne l'instance du texte
        return $this;
  }
  
  /**
    * Déssine le texte
    * @access public
  */
  public function DrawnObject()
  {
    // On parametre la police
    ApplicationLIS::GetModule("Canvas")->SetFont($this->size,$this->font);
    
    // On écrit le contenu du texte à la position don,ée
    ApplicationLIS::GetModule("Canvas")->TextFill($this->text,$this->GetAbsolutePositionX(),$this->GetAbsolutePositionY());
  }
}

/**
  * Objet 2D primaire : La ligne
  * @author Jean pasqualini <jpasqualini75@gmail.com>
  * @license GPL
  * @version InDev
*/
Class LIGNE extends Object2D
{
  /**
   * Déssine la ligne
   * @access public
  */
  public function DrawnObject()
  {
    
  }
}

/**
  * Objet 2D primaire : Le rond
  * @author Jean pasqualini <jpasqualini75@gmail.com>
  * @license GPL
  * @version InDev
*/
Class ROND extends Object2D {
  
  /**
   * Déssine le rond
   * @access public
  */
  public function DrawnObject()
  {
    // Parametre la couleur de remplisage
    ApplicationLIS::GetModule("Canvas")->FillStyle($this->Background);
    
    // Déssine un arc qui se rejoint pour faire un rond
    ApplicationLIS::GetModule("Canvas")->Arc($this->GetAbsolutePositionX(), $this->GetAbsolutePositionY(),20,0,2*pi());
  }
}

/**
 * Cette classe permet de crée des dégradé en tant qu'objet manipulable
 * @abstract
 * @author Jean pasqualini <jpasqualini75@gmail.com>
 * @license GPL
 * @version InDev
 * @package ModuleLis
*/
Abstract Class CreateGradient {
  
  /**
   * @access protected
   * @var Array Contient les couleur du dégradé
  */
  protected $gradient=array();
  
  /**
   * @access private
   * @var int Contient la position courante de la couleur
  */
  private $offset=0;
  
  /**
   * @access private
   * @var boolean Permet sa s'avoir si le dégradé à été déclarer coté client
  */
  private $publied=false;
  
  /**
   * @access private
   * @var Canvas  Désormait inutilse
  */
  protected $handle_canvas;
   
  /**
   * Permet d'ajouter une couleur au dégradé
   * @access public
   * @param string $value Couleur à ajouter en hexa
   * @param int $offset Position de la couleur dans le dégradé
   * @return int Retourne la position de la couleur dans le dégradé
  */
  public function AddColorToGradient($value,$offset=0)
  {
    // Si la position de la couleur n'est pas spécifié
    if(empty($offset))
    {
      // La couleur sera juste après la précédente couleur
      $this->offset+=0.1;
      $offset=$this->offset;
    }
    
    $this->offset=$offset;

    // On empile les information de la couleur dans le tableau grandient
    $this->gradient["Couleur"][]=array("Couleur" => $value , "Offset" => $offset);
    
    // On retourne la position de la couleur
    return $offset;
  }
  
  /**
   * Récupérer l'identifiant du dégradé
   * @access public
   * @return string L'identifiant du dégradé
  */
  public function Getname()
  {
    // Si le dégradé n'est pas publié alors on le publie
    if(!$this->publied) { $this->publish(); }
    
    // Retourne le nom du dégradé
    return $this->gradient["Name"];
  }
  
  /**
   * Permet de supprimé une couleur du dégradé par sa position
   * @access public
   * @param int $offset Position de la couleur à supprimé
  */
  public function RemoveColorToGradient($offset)
  {
    
  }
  
  /**
   * Permet de déclarer au client le dégradé
   * @access public
   * @return boolean Retourne true si le dégradé à bien été déclarer avec succès sinon false
  */
  public function publish()
  {
    // Parcour les couleur du dégradé
    foreach($this->gradient["Couleur"] as $couleur)
    {
      // Ajoute a la déclaration cliente les couleur du dégraté
      ApplicationLIS::GetModule("Canvas")->AddColorToGradient($this->gradient["Name"] ,$couleur["Couleur"] ,$couleur["Offset"]);
    }
    
    // Informe que le dégradé à été déclarer auprès du client avec succès
    $this->publied=true;
    
    // Retourne cette information
    return true;
  }
  
}

/**
 * Cette classé dérivé de la création de dégradé
 * Permet de faire des dégradé radial
 * @author Jean pasqualini <jpasqualini75@gmail.com>
 * @license GPL
 * @version InDev
 * @package ModuleLis
*/
Class CreateRadialGradient extends CreateGradient {
  
  /**
   * Constructeur du dégradé radial
   * @access public
   * @param int $x1 Position en X du début du dégradé
   * @param int $y1 Position en Y du début du dégradé
   * @param int $StartRayon Début du rayon
   * @param int $x2 Position en X de la fin du dégradé
   * @param int $y2 Position en Y de al fin du dégradé
   * @param int $EndRayon Fin du rayon
   * @param Canvas Instance du canvas (Désormat inutile à supprimé)
   * @return CreateRadialGradient Retourne l'instance du dégradé
  */
  public function __construct ($x1,$y1,$StartRayon,$x2,$y2,$EndRayon,$handle_canvas)
  {
    $this->handle_canvas=$handle_canvas;
    
    // Initialise les parametre du dégradé dont un identifiant (Name) Aléatoire
    $this->gradient=array("Name" => "Gradien_".uniqid() ,"X1" => $x1 , "Y1" => $y1 , "StartRayon" => $StartRayon , "X2" => $x2 , "Y2" => $y2 , "EndRayon" => $EndRayon , "Couleur" => array());
  }
  
  /**
   * Permet de déclarer le dégradé au client
   * @access public
   * @return boolean Retourne true si le dégradé à bien été déclarer avec succès sinon false
  */
  public function publish()
  {
    // Crée le dégradé radial
    ApplicationLIS::GetModule("Canvas")->CreateRadialGradient($this->gradient["Name"] ,$this->gradient["X1"] ,$this->gradient["Y1"] ,$this->gradient["StartRayon"] ,$this->gradient["X2"] ,$this->gradient["Y2"] ,$this->gradient["EndRayon"]);    
    return parent::publish();
  }
}
?>