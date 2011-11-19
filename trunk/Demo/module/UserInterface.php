<?php
/**
  * Module de rendu d'interface utilisateur pour faire des élement d'interface utilisateur avec le rendu canvasobjet
  * @author Jean pasqualini <jpasqualini75@gmail.com>
  * @license GPL
  * @version InDev
  * @package ModuleLis
*/
Class module_UserInterface extends ModuleBase implements IModuleBase
{
  
  /**
   * Recupére le nom du module
   * @access public
   * @return string Le nom du module
  */
  public function GetModuleName()
  {
    return "UserInterface";
  }

  /**
   * Récupere la description du module
   * @access public
   * @return string La description du module
  */
  public function GetModuleDescription()
  {
      return "Gestion d'interface utilisateur";
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
          "CanvasObject"  => array("Version" => "1.0"  , "Url" => "")
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
   * Constructeur du module 'UserInterface'
   * @access public
   * @param Socket Ressource de la socket cliente(a supprimé)
   * @param ApplicationLis Intance de l'application (a supprimé)
   * @return UserInterface Retourne l'instance du module
  */
  public function __construct($socket,$handle_application="")
  {
    parent::__construct($socket,$handle_application);
    Object::RegisterObject(new BOUTON());
    Object::RegisterObject(new CONTENEUR());
  }
}

/**
  * Object 2D CONTENEUR
  * @author Jean pasqualini <jpasqualini75@gmail.com>
  * @license GPL
  * @version InDev
*/
class CONTENEUR extends Object2D
{
  
}

/**
  * Objet 2D interactif Le bouton
  * @author Jean pasqualini <jpasqualini75@gmail.com>
  * @license GPL
  * @version InDev
*/
Class BOUTON extends Object2D
{
  /**
   * @access private
   * @var RECTANGLE Instance du rectangle représentant le bouton
  */
  private $Rectangle;
  
  /**
   * @access private
   * @var TEXTE Instance du texte du bouton
  */
  private $text;
  
  /**
   * Constructeur du bouton
   * @access public
   * @param boolean $register True si c'est l'instance representant l'enregistrement du bouton , false sinon
   * @param Object $parent Parent du bouton
   * @return BOUTON Retourne l'instance du bouton
  */
  public function __construct($register=true,$parent = null)
  {
      // Ajoute un rectangle bleu en tant qu'enfant du bouton
      $this->Rectangle = Object::GetObject("RECTANGLE")->AddObject($this)->SetBackgroundColor("blue")->Set(0,0,$this->Height,$this->Width);
      
      // Ajouter un texte de 10px en tant qu'enfant du rectangle bleu
      $this->text = Object::GetObject("TEXTE")->AddObject($this->Rectangle)->Set(5,5)->SetSize("10px");
      
      // Appelle le constructeur parent
      parent::__construct($register,$parent);
  }
  
  /**
   * Modifie la position et la taille du bouton
   * @access public
   * @param int $x La position en X du bouton
   * @param int $y La position en Y du bouton
   * @param int $w La largeur du bouton
   * @param int $h La hauteur du bouton
   * @return BOUTON Retourne l'instance du bouton
  */
  public function Set($x,$y,$w,$h)
  {
        //On informe de la mise a jour de l'objet
        $this->SetUpdated(true);
        
	// Modifie la taille et positon
        $this->PositionX=$x;
        $this->PositionY=$y;
        $this->Height=$h;
        $this->Width=$w;
        
	// Ainsi que de ces enfant
        $this->Rectangle->Set(0,0,$w,$h);
	
	// Puis retourne l'instace du bouton
        return $this;
  }
  
  /**
   * Modifie le texte du bouton
   * @access public
   * @param string $value La valeur du texte du bouton
   * @return BOUTON Retourne l'instance du bouton
  */
  public function SetText($value)
  {
    // Modifie le texte
    $this->text->SetText($value);
    
    // Puis retourne l'instance du bouton
    return $this;
  }
}


?>