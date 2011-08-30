<?php
/* module de rendu audio LISRENDER */
// A ajouter implements ViewRender une fois l'interface de spécification du rendu crée
/*
 Module de rendu lisrendu
 Pasqualini jean | mek-in-gold@live.fr
 Gpl licences
*/
Class module_Lisrender extends ModuleBase implements IModuleBase {
  
  //LimitFps()
    protected $image;
    protected $type_mouse="rond"; // Type de souris définit dans css.css
    protected $config_mouse=array(); // Les parametre de la configuration de la souris
    protected $type_app; // Le type d'application (specifique a lisrender)
    protected $quality_app; // Qualitié de compression de sortie du format jpg (specifique a lisrender)
    protected $souris; 
    protected $conteneur; // Ressource du calque conteneur ou sont applatit le autre claque 
    protected $RessourceCalque=array(); // Tableau des ressource image des calques
    protected $ProprieteCalque=array(); // Tableau de propriété des calque
    protected $width; // Taille de l''application final
    protected $height; // -----------------------------
    protected $css=array(); // Contient les propriété des css importer
    protected $var_sup=array(); // Permet d'utiliser des variable supplémentaire (a suprimer peut etre)
    protected $couleur=array();
    private $curent_time; // Contient le timestamp du dernier appel a la méthode OutputScreen
  
    // Recoie la ressource cliente(websocket) qui recevras les instruction de rendu
  public function __construct()
  {
    parent::__construct();
    $this->curent_time=microtime(true);
    $this->ImportCss("css.css");
    $this->CreateApplication();
  }
  
  /* Importe le css passé en paramètre dans la propriété css */
 public function ImportCss($file)
   {
        $css = new cssparser();
        $t=$css->Parse("theme/default/css/".$file);
        foreach($t as $key => $value)
        {
            $this->css[$key]=array();
            foreach ($value as $cle => $valeur)
            {
                $this->css[$key][$cle]=$valeur;
            }
        }
        unset($t);
   }
   
   // A supprimer
    public function EcouterPeripherique()
    {
        if(method_exists($this,'mouse_move'))
        {
            if ($_GET["mouse_move"])
            {
            $this->mouse_move(1);
            }
        }
       
    }

 
    /*
      Si appeler verifie le timestamp du dernier appel de la méthode output screen
      et la rapelle si le temp dépasse les 40 ms (25 fps)
    */
    public function LimitFps()
    {
        $t = 0.040;
        $time=microtime(true);
if ($time-$this->curent_time>$t)
{
    $this->curent_time=microtime(true);
    $this->OutputScreen(true);
    return true;
}
    }

/* Configure le type de souris définie dans le css */
    public function SetTypeMouse($type="rond",$color=BLEU,$res_ext="not")
    {
        if (substr($this->css["Application"]["cursor"],0,3)=="url")
        {
            $res_ext=substr($this->css["Application"]["cursor"],4,strlen($this->css["Application"]["cursor"])-5);
        }
        else
        {
            $res_ext="not";
            list($type,$color)=explode(" ",$this->css["Application"]["cursor"]);
        }
        if ($res_ext!="not")
        {
            list($width, $height) = getimagesize($res_ext);
            if ($width>40 || $height>40)
            {
                $res_ext="not";
            }
        }
        if(!isset($this->RessourceCalque["souris"]))
        {
        $this->RessourceCalque["souris"]=array();
        if ($res_ext!="not")
        {
        $this->AddCalque("souris",40,40,40,40,0,0,"cur.png",true);
        }
        else
        {
        $this->AddCalque("souris",25,25,25,25,0,0);
        }
    
        }
        list($R,$G,$B) = explode("|",$color);
        $this->config_mouse["type"]=$type;
        $this->config_mouse["color"]=imagecolorallocate($this->RessourceCalque["souris"],$R,$G,$B);
        if ($res_ext=="not")
        {
        switch ($type)
        {
            case "etoile":
                $values = array(
                    0,   0,  // Point 1 (x, y)
                    0,   20, // Point 2 (x, y)
                    10,  15,  // Point 3 (x, y)
                    20,  30,  // Point 4 (x, y)
                    25,  30,  // Point 5 (x, y)
                    15,  15,  // Point 6 (x, y)
                    35,  20,   // Point 7 (x, y)
                    35,  20   // Point 8 (x, y)
                    );
                imagefilledpolygon($this->RessourceCalque["souris"], $values, 7, imagecolorallocate($this->RessourceCalque["souris"],255,0,0));
            break;
            case "rond":
                ImageFilledEllipse ($this->RessourceCalque["souris"], 10, 10, 20, 20, imagecolorallocate($this->RessourceCalque["souris"],255,0,0));
                ImageFilledEllipse ($this->RessourceCalque["souris"], 10, 10, 10, 10, imagecolorallocate($this->RessourceCalque["souris"],$R,$G,$B));
            break;
        }
        }
    }
    
    /*
    crée le calque conteneur avec les parametre de sortie
    et de taille de l'application définit dans le css
    */
    public function CreateApplication ()
    {
    list($this->width,$this->height) = explode("x",$this->css["Application"]["taille"]);
    if ($this->css["Application"]["type"]=="gif")
    {
        $this->conteneur = imagecreate($this->width,$this->height);
    }
    else
    {
        $this->conteneur = imagecreatetruecolor($this->width,$this->height);
    }
    imagecolorallocate($this->conteneur, 0,0,0);
    }
    
    /* 
      Ajoute un calque avec en parametre
      le nom
      la largeur et hauteur
      la possition
      le fichier ressource precalculé si s'en est un
      si c'est une ressource partagé (a voir si encore utile)
    */
    public function AddCalque($nom,$largeur=0,$hauteur=0,$width=0,$height=0,$x=0,$y=0,$ressource=false,$share=false)
    {
        if ($width==0) { $width=$this->width; }
        if ($height==0) { $height=$this->height; }
        if ($largeur==0) { $largeur=$this->width; }
        if ($hauteur==0) { $hauteur=$this->height; }
        $this->RessourceCalque[$nom]=array();
        if (!$ressource)
        {
            $this->RessourceCalque[$nom]=imagecreate($largeur,$hauteur);
            imagecolorallocate($this->RessourceCalque[$nom],0,0,0);
        }
        else
        {
            $this->RessourceCalque[$nom]=imagecreatefrompng(RESSOURCE_ROOT."ressource/".$ressource);
            list($width, $height) = getimagesize(RESSOURCE_ROOT."ressource/".$ressource);
        }
        $this->ProprieteCalque[$nom]=array();
        $this->ProprieteCalque[$nom]["X"]=$x;
        $this->ProprieteCalque[$nom]["Y"]=$y;
        $this->ProprieteCalque[$nom]["width"]=$width;
        $this->ProprieteCalque[$nom]["height"]=$height;
        $this->ProprieteCalque[$nom]["hide"]=false;
        $this->ProprieteCalque[$nom]["ressource"]=$ressource;
        $this->ProprieteCalque[$nom]["share"]=$share;
        $this->ProprieteCalque[$nom]["modified"]=true;
    }
    
    /* Met a jour la souris et l'inclus si elle n'est pas déja inclus */
    public function UpdateMouse($x,$y)
    {
        $this->LimitFps(); // IMPORTANT
        if (!count($this->config_mouse)) { $this->SetTypeMouse(); }
        $this->ProprieteCalque["souris"]["X"]=$x;
        $this->ProprieteCalque["souris"]["Y"]=$y;
    }
    
    
    /*
      Applatit tout le calque a l'exeption des calque 'hide'
    et envoie la sortie selont les parametre de l'application (ex : gif , jpg 85%)
    */
    public function OutputScreen($destroy_screen=0)
    {
        // ICI ON APPLATIE TOUS LES CALQUES
         
        foreach($this->RessourceCalque as $key => $value )
        {
            if ($this->ProprieteCalque[$key]["hide"]==false)
            {
            imagecopy($this->conteneur, $this->RessourceCalque[$key], $this->ProprieteCalque[$key]["X"], $this->ProprieteCalque[$key]["Y"], 0, 0, $this->ProprieteCalque[$key]["width"],$this->ProprieteCalque[$key]["height"]); 
            }
        }
         

        ob_start();
        if ($this->css["Application"]["type"]=="gif")
        {
            imagegif($this->conteneur);
        }
        else
        {
            imagejpeg($this->conteneur,NULL,$this->css["Application"]["quality"]);
        }
        $img=ob_get_clean();
        $data = array("Type" => "view" , "Data" => base64_encode($img));
        $this->send($data);
        if ($destroy_screen) { imagedestroy($this->conteneur); $this->CreateApplication(); }
        if(!socket_recv($this->socket,$buffer,2048,0)) { return false; } else { return true; }
    }
    
    /*
    insert un texte dans le calque dont le nom est passé en parametre avec une valeur une position
    et une couleur aussi passé en parametre
    */
    public function AfficherTexte($res,$texte,$x,$y,$couleur)
    {
        $this->LimitFps(); // IMPORTANT
        $this->ProprieteCalque[$res]["modified"]=true;
        list($R,$G,$B) = explode("|",$couleur);
        imagestring($this->RessourceCalque[$res], 4, $x, $y, $texte, imagecolorallocate($this->RessourceCalque[$res],$R,$G,$B));
    }
    
    /*
     A voir :::
     if(!isset($this->RessourceCalque[$res]["color"][$couleur])) {
            $this->RessourceCalque[$res]["color"][$couleur]=imagecolorallocate($this->RessourceCalque[$res],$R,$G,$B);
            }
        imagestring($this->RessourceCalque[$res], 4, $x, $y, $texte, $this->RessourceCalque[$res]["color"][$couleur]);
   */
        
    /* insert un texte dans le calque dont le nom est passé en parametre avec une position une taille et une couleur */
    public function InsertRectangle($res,$x_1,$y_1,$x_2,$y_2,$couleur)
    {
        $this->LimitFps(); // IMPORTANT
        $this->ProprieteCalque[$res]["modified"]=true;
        list($R,$G,$B) = explode("|",$couleur);
        imagefilledrectangle($this->RessourceCalque[$res], $x_1,$y_1,$x_2,$y_2, imagecolorallocate($this->RessourceCalque[$res],$R,$G,$B));
    }
    
    /* insert un texte dans le calque dont le nom est passé en parametre avec une position une taille et une couleur */
    public function InsertLine($res,$x_1,$y_1,$x_2,$y_2)
    {
        $this->LimitFps(); // IMPORTANT
        $this->ProprieteCalque[$res]["modified"]=true;
        imageline($this->RessourceCalque[$res],$x_1,$y_1,$x_2,$y_2,imagecolorallocate($this->RessourceCalque[$res],0,0,0));
    }
        
    /* insert un texte dans le calque dont le nom est passé en parametre avec une position une taille et une couleur */
    public function CreateSubmit($res,$color,$position_x,$poxition_y,$value,$width=0,$height=0)
    {
        $this->LimitFps(); // IMPORTANT
        $this->ProprieteCalque[$res]["modified"]=true;
        list($R,$G,$B) = explode("|",$color);
        $taille_textbox=strlen($value);
        if ($width==0) { $width=$taille_textbox*10; }
        if ($height==0) { $height=20; }
        imagefilledrectangle($this->RessourceCalque[$res], $position_x, $poxition_y, $position_x+$width, $position_y+$height, imagecolorallocate($this->RessourceCalque[$res], $R,$G,$B));
        imagestring($this->RessourceCalque[$res], 5, $position_x+4, $poxition_y+2, $value, imagecolorallocate($this->RessourceCalque[$res], 255, 255, 255));
    }
  
}
?>