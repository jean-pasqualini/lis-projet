<?php

Class module_CanvasObject extends ModuleBase implements IModuleBase
{
  public function GetModuleName()
  {
    return "CanvasObject";
  }

  public function GetModuleDescription()
  {
      return "Gestion 2D en objet";
  }
  
  public function GetVersion()
  {
      return "1.0";
  }
  
  public function GetDependanceServer()
  {
    // Declare les modules serveur  dont nous avons besoin
    return array(
          "Canvas"  => array("Version" => "1.0"  , "Url" => ""),
          "InterfaceKM" => array("Version" => "1.0" , "Url" => "")
    );
  }
  
  public function GetDependanceClient()
  {
    return array(); 
  }
  
  public function __construct()
  {
    parent::__construct();
    Object::RegisterObject(new RECTANGLE());
    Object::RegisterObject(new LIGNE());
    Object::RegisterObject(new ROND());
    Object::RegisterObject(new TEXTE());
  }
}

// Objet primaire : Le rectangle
Class RECTANGLE extends Object2D
{
  public function Set($x,$y,$w,$h)
  {
        //On informe de la mise a jour de l'objet
        $this->SetUpdated(true);
        
        $this->PositionX=$x;
        $this->PositionY=$y;
        $this->Height=$h;
        $this->Width=$w;
        return $this;
  }
    
  public function DrawnObject()
  {
    ApplicationLIS::GetModule("Canvas")->FillStyle($this->Background);
    ApplicationLIS::GetModule("Canvas")->FillRect($this->GetAbsolutePositionX(), $this->GetAbsolutePositionY(), $this->Width, $this->Height);
  }
}

Class TEXTE extends Object2D
{
  private $text;
  private $size;
  private $font;
  const ReturnLine = "\r\n";
  
  public function SetSize($value)
  {
    $this->size = $value;
    return $this;
  }
  
  public function SetFont($value)
  {
    $this->font = $value;
    return $this;
  }
  
  public function SetText($text)
  {
    $this->text = $text;
    return $this;
  }
  
  public function AddText($text)
  {
	$this->text .= $text;
	return $this;
  }
  
  public function AddTextL($text)
  {
	return $this->AddText($text.TEXTE::ReturnLine);
  }
  
  public function Set($x,$y)
  {
         //On informe de la mise a jour de l'objet
        $this->SetUpdated(true);
        
        $this->PositionX=$x;
        $this->PositionY=$y;
        
        return $this;
  }
  
  public function DrawnObject()
  {
    ApplicationLIS::GetModule("Canvas")->SetFont($this->size,$this->font);
    ApplicationLIS::GetModule("Canvas")->TextFill($this->text,$this->GetAbsolutePositionX(),$this->GetAbsolutePositionY());
  }
}

// Objet primaire : La ligne
Class LIGNE extends Object2D
{
  public function DrawnObject()
  {
    
  }
}

// Objet primaire : Le rond
Class ROND extends Object2D {
  public function DrawnObject()
  {
    ApplicationLIS::GetModule("Canvas")->FillStyle($this->Background);
    ApplicationLIS::GetModule("Canvas")->Arc($this->GetAbsolutePositionX(), $this->GetAbsolutePositionY(),20,0,2*pi());
  }
}

Class CreateGradient {
  protected $gradient=array();
  private $offset=0;
  private $publied=false;
  protected $handle_canvas;
   
  public function AddColorToGradient($value,$offset=0)
  {
    if(empty($offset))
    {
      $this->offset+=0.1;
      $offset=$this->offset;
    }
    
    $this->offset=$offset;

    $this->gradient["Couleur"][]=array("Couleur" => $value , "Offset" => $offset);
    return $offset;
  }
  
  public function Getname()
  {
    if(!$this->publied) { $this->publish(); }
    return $this->gradient["Name"];
  }
  
  public function RemoveColorToGradient($offset)
  {
    
  }
  
  public function publish()
  {
    foreach($this->gradient["Couleur"] as $couleur)
    {
      ApplicationLIS::GetModule("Canvas")->AddColorToGradient($this->gradient["Name"] ,$couleur["Couleur"] ,$couleur["Offset"]);
    }
    $this->publied=true;
    return true;
  }
  
}

// On desactive les degrade pour l'instant

Class CreateRadialGradient extends CreateGradient {
  public function __construct ($x1,$y1,$StartRayon,$x2,$y2,$EndRayon,$handle_canvas)
  {
    $this->handle_canvas=$handle_canvas;
    $this->gradient=array("Name" => "Gradien_".uniqid() ,"X1" => $x1 , "Y1" => $y1 , "StartRayon" => $StartRayon , "X2" => $x2 , "Y2" => $y2 , "EndRayon" => $EndRayon , "Couleur" => array());
  }
  
  public function publish()
  {
    ApplicationLIS::GetModule("Canvas")->CreateRadialGradient($this->gradient["Name"] ,$this->gradient["X1"] ,$this->gradient["Y1"] ,$this->gradient["StartRayon"] ,$this->gradient["X2"] ,$this->gradient["Y2"] ,$this->gradient["EndRayon"]);    
    return parent::publish();
  }
}
?>