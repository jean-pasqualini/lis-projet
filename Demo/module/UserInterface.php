<?php

Class module_UserInterface extends ModuleBase implements IModuleBase
{
  public function GetModuleName()
  {
    return "UserInterface";
  }

  public function GetModuleDescription()
  {
      return "Gestion d'interface utilisateur";
  }
  
  public function GetVersion()
  {
      return "1.0";
  }
  
  public function GetDependanceServer()
  {
    // Declare les modules serveur  dont nous avons besoin
    return array(
          "CanvasObject"  => array("Version" => "1.0"  , "Url" => "")
    );
  }
  
  public function GetDependanceClient()
  {
    return array(); 
  }
  
  public function __construct($socket,$handle_application="")
  {
    parent::__construct($socket,$handle_application);
    Object::RegisterObject(new BOUTON());
    Object::RegisterObject(new CONTENEUR());
  }
}

// CECI EST UN CONTENEUR
class CONTENEUR extends Object2D
{
  
}

// Objet d'interface le bouton a cliquer c'est un calque vide
Class BOUTON extends Object2D
{
  private $Rectangle;
  private $text;
  
  public function __construct($register=true,$parent = null)
  {
      $this->Rectangle = Object::GetObject("RECTANGLE")->AddObject($this)->SetBackgroundColor("blue")->Set(0,0,$this->Height,$this->Width);
      $this->text = Object::GetObject("TEXTE")->AddObject($this->Rectangle)->Set(5,5)->SetSize("10px");
      
      parent::__construct($register,$parent);
  }
  
  public function Set($x,$y,$w,$h)
  {
        //On informe de la mise a jour de l'objet
        $this->SetUpdated(true);
        
        $this->PositionX=$x;
        $this->PositionY=$y;
        $this->Height=$h;
        $this->Width=$w;
        
        $this->Rectangle->Set(0,0,$w,$h);
        return $this;
  }
  
  public function SetText($value)
  {
    $this->text->SetText($value);
	return $this;
  }
}


?>