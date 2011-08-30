<?php
/*
 Module de rendu canvas
 Pasqualini jean | mek-in-gold@live.fr
*/
Class module_Canvas extends ModuleBase implements IModuleBase
{
  private $size;
  private $font;
  private $FillStyle;
  private $StrokeStyle;
  
  public function GetModuleName()
  {
    return "Canvas";
  }

  public function GetModuleDescription()
  {
      return "Gestion de l'affichage canvas";
  }
  
  public function GetVersion()
  {
      return "1.0";
  }
  
  public function GetDependanceServer()
  {
    // Declare les modules dont serveur nous avons besoin
    return array();
  }
  
  public function GetDependanceClient()
  {
    return array(); 
  }
  
  public function __construct()
  {
    parent::__construct();
    // A voir autre chose
    $css=Object::SCss("Application");
    if(!empty($css["taille"]))
    {
      list($w,$h)=explode("x",$css["taille"]);
      $this->SetWidth($w);
      $this->SetHeight($h);
      if(!empty($css["background"]))
      {
        $this->FillStyle($css["background"]);
        $this->FillRect(0,0,$w,$h);
      }
    }
  }
  
  // Commence la bufferisation du dessin
  public function beginPath()
  {
    $data=array("Type" => "view" , "Action" => "beginPath");
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }
  
  // Ecrit le buffer et l'efface
  public function closePath()
  {
    $data=array("Type" => "view" , "Action" => "closePath");
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }
  
  // Dessine un rectangle rempli (x, y, largeur, hauteur)
  public function FillRect($x,$y,$w,$h)
  {
    $data=array("Type" => "view" , "Action" => "FillRect" , "X" => $x, "Y" => $y , "W" => $w , "H" => $h);
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }
  
  // Dessine un contour rectangulaire (x, y, largeur, hauteur)
  public function StrokeRect($x,$y,$w,$h)
  {
    $data=array("Type" => "view" , "Action" => "StrokeRect" , "X" => $x, "Y" => $y , "W" => $w , "H" => $h);
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }
  
  // Efface la zone spécifiée et le rend complètement transparent (x, y, largeur, hauteur)
  public function ClearRect($x,$y,$w,$h)
  {
    $data=array("Type" => "view" , "Action" => "ClearRect" , "X" => $x, "Y" => $y , "W" => $w , "H" => $h);
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }
  
  // Parametre la couleur de remplicage
  public function FillStyle($value)
  {
    if($this->FillStyle == $value) { return true; } else { $this->FillStyle = $value; }
    
    if(is_object($value)) { $gradient=true; $value=$value->Getname(); } else { $gradient=false; }
    $data=array("Type" => "view" , "Action" => "FillStyle" , "value" => $value , "Gradient" => $gradient);
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }
  
  // Parametre la couleur de contour
  public function StrokeStyle($value,$gradient=false)
  {
    if($this->StrokeStyle == $value) { return true; } else { $this->StrokeStyle = $value; }
        
    $data=array("Type" => "view" , "Action" => "StrokeStyle", "value" => $value , "Gradient" => $gradient);
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }
  
  // Deplace le curseur courant a une position x , y 
  public function MoveTo($x,$y)
  {
    $data=array("Type" => "view" , "Action" => "MoveTo" , "X" => $x , "Y" => $y);
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }
  
  // Dessine un Quadratic curve
  public function QuadraticCurveTo($cp1x,$cp1y,$x,$y)
  {
    $data=array("Type" => "view" , "Action" => "QuadraticCurveTo" , "Cp1x" => $cp1x, "Cp1y" => $cp1y , "X" => $x, "Y" => $y);
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }

  // Dessine un bezier curve
  public function BezierCurveTo($cp1x,$cp1y,$cp2x,$cp2y,$x,$y)
  {
    $data=array("Type" => "view" , "Action" => "BezierCurveTo" , "Cp1x" => $cp1x, "Cp1y" => $cp1y , "Cp2x" => $cp2x, "Cp2y" => $cp2y , "X" => $x , "Y" => $y);
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }

  // Dessine un cercle (x, y, rayon, startAngle, endAngle, gauche);
  public function Arc($x,$y,$rayon,$startAngle,$endAngle,$gauche = "true")
  {
    $data=array("Type" => "view" , "Action" => "Arc" , "X" => $x , "Y" => $y,"Rayon" => $rayon, "StartAngle" => $startAngle, "EndAngle" => $endAngle, "Gauche" => $gauche);
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }

  // Cree un Dégradée lineaire
  public function CreateLinearGradient($gradient,$x1,$y1,$x2,$y2)
  {
    $data=array("Type" => "view" , "Action" => "CreateLinearGradient" , "Gradient" => $gradient ,"X1" => $x1 , "Y1" => $y1 , "X2" => $x2 , "Y2" => $y2);
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }

  // Cree un Dégradée circulaire
  public function CreateRadialGradient($gradient,$x1,$y1,$StartRayon,$x2,$y2,$EndRayon)
  {
    $data=array(array(
                  "Type" => "view" ,
                  "Action" => "CreateRadialGradient" ,
                  "Gradient" => $gradient ,
                  "X1" => $x1 ,
                  "Y1" => $y1 ,
                  "StartRayon" => $StartRayon ,
                  "X2" => $x2 ,
                  "Y2" => $y2 ,
                  "EndRayon" => $EndRayon
                  ));
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }

  // Ajoute des couleur a un degradée
  public function AddColorToGradient($gradient,$value,$offset=0)
  {
    $data=array("Type" => "view" , "Action" => "AddColorToGradient" , "Offset" => $offset , "Gradient" => $gradient , "value" => $value);
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }

  // Change la police et la taille (10px sans-serif)
  public function SetFont($taille,$police)
  {
    if($this->size == $taille && $this->font == $police) return true;
    $this->size = $taille;
    $this->font = $police;
    
    $data=array("Type" => "view" , "Action" => "font" , "value" => $taille." ".$police);
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }

  // Ecrit un texte avec un fond
  public function TextFill($value,$x,$y)
  {
    $data=array("Type" => "view" , "Action" => "TextFill" , "value" => $value , "X" => $x , "Y" => $y);
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }

  // Ecrit un contour de texte
  public function TextStroke()
  {
    $data=array("Type" => "view" , "Action" => "TextStroke" , "value" => $value , "X" => $x , "Y" => $y);
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }

  // Parametre la largeur du canvas
  public function SetWidth($value)
  {
    $data=array("Type" => "view" , "Action" => "SetWidth" , "value" => $value);
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }

  // Parametre la hauteur du canvas
  public function SetHeight($value)
  {
    $data=array("Type" => "view" , "Action" => "SetHeight" , "value" => $value);
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }
  
  // Parametre la composante alpha global 0.0 a 1.0
  public function SetGlobalAlpha()
  {
    $data=array("Type" => "view" , "Action" => "SetGlobalAlpha" , "value" => $value);
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }

  /*
      source-over , source-in , source-out , source-atop , destination-over , destination-in
      destination-out , destination-atop , lighter , copy , xor
  */
  // Parametre l'operation composite
  public function SetCompositeOperation()
  {
    $data=array("Type" => "view" , "Action" => "SetCompositeOperation" , "value" => $value);
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }

  // Paramatre la largeur des ligne 
  public function SetLineWidth($value)
  {
    $data=array("Type" => "view" , "Action" => "SetLineWidth" , "value" => $value);
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }
  
  public function GetHandleApplication()
  {
    return ApplicationLis::GetInstance();
  }
  
}

?>