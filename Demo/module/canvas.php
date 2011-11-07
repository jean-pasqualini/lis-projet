<?php
/*
 Module de rendu visuelle canvas
 
 @author Jean pasqualini <jpasqualini@live.fr>
 @license GPL
 @version InDev
*/
Class module_Canvas extends ModuleBase implements IModuleBase
{
  /*
   @access private
   @var int La taille des ligne déssine
  */
  private $size;
  
  /*
   @access private
   @var string police d'écriture
  */
  private $font;
  
  /*
   @access private
   @var string Style de remplisage
  */
  private $FillStyle;
  
  /*
   @access private
   @var string Style de contour
  */
  private $StrokeStyle;
  
  /*
   Recupére le nom du module
   @access public
   @return string Le nom du module
  */
  public function GetModuleName()
  {
    return "Canvas";
  }

  /*
   Récupere la description du module
   @access public
   @return string La description du module
  */
  public function GetModuleDescription()
  {
      return "Gestion de l'affichage canvas";
  }
  
  /*
   Récupere la version du module
   @access public
   @return string La version du module
  */
  public function GetVersion()
  {
      return "1.0";
  }
  
  /*
   Récupere les dépendances module serveur
   @access public
   @return Array Les dépendances serveur
  */
  public function GetDependanceServer()
  {
    // Declare les modules dont serveur nous avons besoin
    return array();
  }
  
  /*
   Récupere les dépendances module client
   @access public
   @return Array Les dépendances client
  */
  public function GetDependanceClient()
  {
    return array(); 
  }
  
  /*
   Contructeur du module Canvas
   @access public
   @return Canvas Retourne l'instance du module
  */
  public function __construct()
  {
    parent::__construct();
    // A voir autre chose
    
    // On récupere les styles css de l'aplication
    $css=Object::SCss("Application");
    
    //Si la taille n'est pas vide
    if(!empty($css["taille"]))
    {
      list($w,$h)=explode("x",$css["taille"]);
      
      // Alors on redimensionne le canvas à la taille voulu
      $this->SetWidth($w);
      $this->SetHeight($h);
      
      // Si le fond n'est pas vide
      if(!empty($css["background"]))
      {
        // Alors on remplit le fond de la couleur voulu
        $this->FillStyle($css["background"]);
        $this->FillRect(0,0,$w,$h);
      }
    }
  }
  
  /*
    Commence la bufferisation du dessin
    @access public
    @return boolean Retourne true si commande bien éxécuter sinon false
  */
  public function beginPath()
  {
    $data=array("Type" => "view" , "Action" => "beginPath");
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }
  
  /*
    Ecrit le buffer et l'efface
    @access public
    @return boolean Retourne true si commande bien éxécuter sinon false
  */
  public function closePath()
  {
    $data=array("Type" => "view" , "Action" => "closePath");
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }
  
  /*
    Dessine un rectangle rempli (x, y, largeur, hauteur)
    @access public
    @param int $x la position en x du rectangle
    @param int $y la position en y du rectangle
    @param int $w la largeur du rectangle
    @param int $h la hauteur du rectangle
    @return boolean Retourne true si commande bien éxécuter sinon false
  */
  public function FillRect($x,$y,$w,$h)
  {
    $data=array("Type" => "view" , "Action" => "FillRect" , "X" => $x, "Y" => $y , "W" => $w , "H" => $h);
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }
  
  /*
    Dessine un contour rectangulaire (x, y, largeur, hauteur)
    @param int $x la position en x du rectangle
    @param int $y la postionn en y du rectangle
    @param int $w la largeur du rectangle
    @param int $h la hauteur du rectangle
    @access public
    @return boolean Retourne true si commande bien éxécuter sinon false
  */
  public function StrokeRect($x,$y,$w,$h)
  {
    $data=array("Type" => "view" , "Action" => "StrokeRect" , "X" => $x, "Y" => $y , "W" => $w , "H" => $h);
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }
  
  /*
    Efface la zone spécifiée et le rend complètement transparent (x, y, largeur, hauteur)
    @param int $x la position en x
    @param int $y la position en y
    @param int $w la largeur
    @param int $h la hauteur
    @access public
    @return boolean Retourne true si commande bien éxécuter sinon false
  */
  public function ClearRect($x,$y,$w,$h)
  {
    $data=array("Type" => "view" , "Action" => "ClearRect" , "X" => $x, "Y" => $y , "W" => $w , "H" => $h);
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }
  
  /*
    Paramétre la couleur de remplisage
    @param string $value la couleur de remplissage
    @access public
    @return boolean Retourne true si commande bien éxécuter sinon false
  */
  public function FillStyle($value)
  {
    if($this->FillStyle == $value) { return true; } else { $this->FillStyle = $value; }
    
    if(is_object($value)) { $gradient=true; $value=$value->Getname(); } else { $gradient=false; }
    $data=array("Type" => "view" , "Action" => "FillStyle" , "value" => $value , "Gradient" => $gradient);
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }
  
  /*
    Paramétre la couleur de contour
    @param string $value la couleur du contour
    @param boolean true si c'est un dégradé sinon false
    @access public
    @return boolean Retourne true si commande bien éxécuter sinon false
  */
  public function StrokeStyle($value,$gradient=false)
  {
    if($this->StrokeStyle == $value) { return true; } else { $this->StrokeStyle = $value; }
        
    $data=array("Type" => "view" , "Action" => "StrokeStyle", "value" => $value , "Gradient" => $gradient);
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }
  
  /*
    Déplace le curseur courant a une position x , y
    @param int $x la position en x
    @param int $y la position en y
    @access public
    @return boolean Retourne true si commande bien éxécuter sinon false
  */
  public function MoveTo($x,$y)
  {
    $data=array("Type" => "view" , "Action" => "MoveTo" , "X" => $x , "Y" => $y);
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }
  
  /*
    Dessine un Quadratic curve
    @param int $x la position en x du coin supérieur gauche
    @param int $y la position en y du coin supérieur gauche
    @param int $cp1x la postion en x du coin inférieur droit
    @param int $cp1y la position en y du coin inférieur droit
    @access public
    @return boolean Retourne true si commande bien éxécuter sinon false
  */
  public function QuadraticCurveTo($cp1x,$cp1y,$x,$y)
  {
    $data=array("Type" => "view" , "Action" => "QuadraticCurveTo" , "Cp1x" => $cp1x, "Cp1y" => $cp1y , "X" => $x, "Y" => $y);
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }

  /*
    Dessine un bezier curve
    @param int $cp1x Position en X du premier point de controle
    @param int $cp1y Position en Y du premier point de controle
    @param int $cp2x Position en X du deuxieme point de controle
    @param int $cp2y Position en Y du deuxieme point de controle
    @param int $x Position en x du bezier
    @param int $y Position en u du bezier
    @access public
    @return boolean Retourne true si commande bien éxécuter sinon false
  */
  public function BezierCurveTo($cp1x,$cp1y,$cp2x,$cp2y,$x,$y)
  {
    $data=array("Type" => "view" , "Action" => "BezierCurveTo" , "Cp1x" => $cp1x, "Cp1y" => $cp1y , "Cp2x" => $cp2x, "Cp2y" => $cp2y , "X" => $x , "Y" => $y);
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }

  /*
    Dessine un cercle (x, y, rayon, startAngle, endAngle, gauche);
    @param int $x position en x du carcle
    @param int $y postiion en y du cercle
    @param int $rayon rayon du cercle
    @param int $startAngle Début de l'angle
    @param int $endAngle Fin de l'angle
    @param boolean true pour un rayon dans le sans horaire d'une montre sinon false pour anti-horaire
    @access public
    @return boolean Retourne true si commande bien éxécuter sinon false
  */
  public function Arc($x,$y,$rayon,$startAngle,$endAngle,$gauche = "true")
  {
    $data=array("Type" => "view" , "Action" => "Arc" , "X" => $x , "Y" => $y,"Rayon" => $rayon, "StartAngle" => $startAngle, "EndAngle" => $endAngle, "Gauche" => $gauche);
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }

  /*
    Cree un Dégradée lineaire
    @param string $gradient nom du dégradé
    @param int $x Position en X du début
    @param int $y Position en Y du début
    @param int $x2 Position en X de la fin
    @param int $y2 Position en Y de la fin
    @access public
    @return boolean Retourne true si commande bien éxécuter sinon false
  */
  public function CreateLinearGradient($gradient,$x1,$y1,$x2,$y2)
  {
    $data=array("Type" => "view" , "Action" => "CreateLinearGradient" , "Gradient" => $gradient ,"X1" => $x1 , "Y1" => $y1 , "X2" => $x2 , "Y2" => $y2);
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }

  /*
    Cree un Dégradée circulaire
    @param string $gradient nom du dégradé
    @param int $x1 Position en X du début
    @param int $y1 Position en Y du début
    @param int $StartRayon Début du rayon
    @param int $x2 Position en X de la fin 
    @param int $y2 Position en Y de la fin
    @param int $EndRayon Fin du rayon
    @access public
    @return boolean Retourne true si commande bien éxécuter sinon false
  */
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

  /*
   Ajoute des couleur a un degradée
   @param string $gradient nom du dégradé
   @param string $value couleur en hexa
   @param int $offset position de la couleur dans le dégradé
   @access public
   @return boolean Retourne true si commande bien éxécuter sinon false
  */
  public function AddColorToGradient($gradient,$value,$offset=0)
  {
    $data=array("Type" => "view" , "Action" => "AddColorToGradient" , "Offset" => $offset , "Gradient" => $gradient , "value" => $value);
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }

  /*
    Change la police et la taille (10px sans-serif)
    @param int $taille taille de la police d'écriture en pixel(ex: 10px)
    @param string $police nom de la police d'écriture
    @access public
    @return boolean Retourne true si commande bien éxécuter sinon false
  */
  public function SetFont($taille,$police)
  {
    if($this->size == $taille && $this->font == $police) return true;
    $this->size = $taille;
    $this->font = $police;
    
    $data=array("Type" => "view" , "Action" => "font" , "value" => $taille." ".$police);
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }

  /*
    Ecrit un texte avec un fond
    @param string $value Contenu du texte
    @param int $x position en x du texte
    @param int $y position en y du texte
    @access public
    @return boolean Retourne true si commande bien éxécuter sinon false
  */
  public function TextFill($value,$x,$y)
  {
    $data=array("Type" => "view" , "Action" => "TextFill" , "value" => $value , "X" => $x , "Y" => $y);
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }

  /*
    Ecrit un contour de texte
    @param string $value Contenu du texte
    @param int $x position en x du texte
    @param int $y position en y du texte
    @access public
    @return boolean Retourne true si commande bien éxécuter sinon false
  */
  public function TextStroke($value, $x, $y)
  {
    $data=array("Type" => "view" , "Action" => "TextStroke" , "value" => $value , "X" => $x , "Y" => $y);
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }

  /*
    Parametre la largeur du canvas
    @param int $value largeur du canvas en pixel
    @access public
    @return boolean Retourne true si commande bien éxécuter sinon false
  */
  public function SetWidth($value)
  {
    $data=array("Type" => "view" , "Action" => "SetWidth" , "value" => $value);
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }

  /*
    Parametre la hauteur du canvas
    @param int $value hauteur du canvas en pixel
    @access public
    @return boolean Retourne true si commande bien éxécuter sinon false
  */
  public function SetHeight($value)
  {
    $data=array("Type" => "view" , "Action" => "SetHeight" , "value" => $value);
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }
  
  /*
    Parametre la composante alpha global 0.0 a 1.0
    @param float $value la valeur de la composante alpha
    @access public
    @return boolean Retourne true si commande bien éxécuter sinon false
  */
  public function SetGlobalAlpha($value)
  {
    $data=array("Type" => "view" , "Action" => "SetGlobalAlpha" , "value" => $value);
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }

  /*
   Parametre l'operation composite
   source-over , source-in , source-out , source-atop , destination-over , destination-in
   destination-out , destination-atop , lighter , copy , xor
   
   @param string $value type de composite
   @access public
   @return boolean Retourne true si commande bien éxécuter sinon false
  */
  public function SetCompositeOperation($value)
  {
    $data=array("Type" => "view" , "Action" => "SetCompositeOperation" , "value" => $value);
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }

  /*
    Paramatre la largeur des ligne
    @param int $value Taille des ligne déssiner
    @access public
    @return boolean Retourne true si commande bien éxécuter sinon false
  */
  public function SetLineWidth($value)
  {
    $data=array("Type" => "view" , "Action" => "SetLineWidth" , "value" => $value);
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }
  
  /*
    Permet de récuperer l'instance unique de l'application
    @access public
    @return ApplicationLIS Retourne l'instance de l'application
  */
  public function GetHandleApplication()
  {
    return ApplicationLis::GetInstance();
  }
  
}

?>