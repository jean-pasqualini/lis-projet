<?php
/*
  Module de rendu lisrendu
  Ce module g�n�re un rendu cot� serveur gourmant en ressource 
  
  @todo A ajouter implements IViewRender une fois l'interface de sp�cification du rendu cr�e visuelle
  @author Jean pasqualini <jpasqualini@live.fr>
  @license GPL
  @version InDev
*/
Class module_Lisrender extends ModuleBase implements IModuleBase {
  
    /*
     @access protected
     @var Ressource Ressource de l'image applatit
    */
    protected $image;
    
    /*
    @access protected
    @var string Type de souris d�finit dans css.css
    */
    protected $type_mouse="rond";
    
    /*
    @access protected
    @var Array Les parametre de la configuration de la souris
    */
    protected $config_mouse=array();
    
    /*
    @access protected
    @var string Le type d'application (specifique a lisrender)
    */
    protected $type_app;
    
    /*
    @access protected
    @var int Qualiti� de compression de sortie du format jpg (specifique a lisrender)
    */
    protected $quality_app;
    
    /*
     @access protected
     @var Ressource Ressource image de la souris
    */
    protected $souris;
    
    /*
    @access protected
    @var Ressource Ressource du calque conteneur ou sont applatit le autre claque
    */
    protected $conteneur;
    
    /*
    @access protected
    @var Array Tableau des ressource image des calques
    */
    protected $RessourceCalque=array();
    
    /*
    @access protected
    @var Array Tableau de propri�t� des calque
    */
    protected $ProprieteCalque=array();
    
    /*
    @access protected
    @var int Largeur de l'application
    */
    protected $width;
    
    /*
    @access protected
    @var int Hauteur de l'application
    */
    protected $height;
    
    /*
    @access protected
    @var Array Contient les propri�t� des css importer
    */
    protected $css=array();
    
    /*
    @access protedted
    @var Array Permet d'utiliser des variable suppl�mentaire (a suprimer peut etre)
    */
    protected $var_sup=array();
    
    /*
     @access protedted
     @var Array Couleurs utilis�e par l'application
    */
    protected $couleur=array();
    
    /*
    @access private
    @var int Contient le timestamp du dernier appel a la m�thode OutputScreen
    */
    private $curent_time; 
  
  /* 
    Constructeur du module lisrender
    @access public
    @return lisrender Retourn l'instance du module
  */
  public function __construct()
  {
    // ON appele le constructeur
    parent::__construct();
    
    // On recupere le timestamp en microseconde
    $this->curent_time=microtime(true);
    
    // ON importe le css
    $this->ImportCss("css.css");
  
    // On cr�e l'application
    $this->CreateApplication();
  }
  
 /*
 Importe le css pass� en param�tre dans la propri�t� css
 @access public
 @param string $file Le fichier css � importer
 */
 public function ImportCss($file)
   {
        // Initalsie le parseur css
        $css = new cssparser();
        
        // R�cupere les propri�t� css en parsant le css
        $t=$css->Parse("theme/default/css/".$file);
        
        // Parcoeur les elements
        foreach($t as $key => $value)
        {
            // Parcour leur propri�t�
            $this->css[$key]=array();
            
            foreach ($value as $cle => $valeur)
            {
                // Les empile dans le tableau css
                $this->css[$key][$cle]=$valeur;
            }
        }
        
        // Supprime le tableau pars� retourner par le parseur
        unset($t);
   }
   
    /*
      Si appeler verifie le timestamp du dernier appel de la m�thode output screen
      et la rapelle si le temp d�passe les 40 ms (25 fps)
      @access public
      @return boolean retourne true si le d�lais impartie est pass� sinon false
    */
    public function LimitFps()
    {
        // Temp en microsecodne devant au minimum s'ecouter
        $t = 0.040;
        
        // On calcule le temp actuelle
        $time=microtime(true);
        
        // Si le temps actuelle plus le dernier temp d'execution de limitfps est plus grand que le temp limiete
        if ($time-$this->curent_time>$t)
        {
            // On met a jour le temp de la derneire execution de limitfps
            $this->curent_time=microtime(true);
            
            // On dit que la sortie peut etre d�ssiner
            $this->OutputScreen(true);
            
            // On retourne true
            return true;
        }
        
        // Sinon on retourne false
        return false;
    }

    /*
    Configure le type de souris d�finie dans le css
    @access public
    @param string $type Type de souris (rond, etoile)
    @param string $color Couleur de la souris sous forme R|G|B
    @param Ressource $res_ext Ressource image pour la souris 
    */
    public function SetTypeMouse($type="rond",$color=BLEU,$res_ext="not")
    {
        // Si le curseur est une iamge
        if (substr($this->css["Application"]["cursor"],0,3)=="url")
        {
            // Alors on extrai le chemin de l'image
            $res_ext=substr($this->css["Application"]["cursor"],4,strlen($this->css["Application"]["cursor"])-5);
        }
        else
        {
            // Sinon ne 'est pas une ressource image
            $res_ext="not";
            
            // Et il doit �tre alors le type plus la couleur (ex: rond 255|0|0)
            list($type,$color)=explode(" ",$this->css["Application"]["cursor"]);
        }
        // Si c'est une ressource externe image
        if ($res_ext!="not")
        {
            // On recupere les dimenesion du fichier image
            list($width, $height) = getimagesize($res_ext);
            // Et si l'image est plus grand que 40x40 pixel
            if ($width>40 || $height>40)
            {
                // Alors on ne prend pas la ressource image
                $res_ext="not";
            }
        }
        
        // Si le calque du curseur souris n'existe pas
        if(!isset($this->RessourceCalque["souris"]))
        {
          // Alors on initionalise le calque souris
          $this->RessourceCalque["souris"]=array();
          
          // Si c'est une image
          if ($res_ext!="not")
          {
            // Auquel on attache le calque souris en image
            $this->AddCalque("souris",40,40,40,40,0,0,"cur.png",true);
          }
          else
          {
            // Auquel on attaque le calque souris sans image
            $this->AddCalque("souris",25,25,25,25,0,0);
          }
        }
        
        // On extrait les composant R,G,B de la couleur
        list($R,$G,$B) = explode("|",$color);
        
        // On parametre le type de soruis
        $this->config_mouse["type"]=$type;
        
        // et sa couleur
        $this->config_mouse["color"]=imagecolorallocate($this->RessourceCalque["souris"],$R,$G,$B);
        
        // Si ce n'est pas une image
        if ($res_ext=="not")
        {
          // Alors on v�rifie le type de forme chosir
          switch ($type)
          {
            // Si c'est une �toile alors on la d�ssine grace a huit point relier
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
              
              // Si c'est un rond alors on d�sine un rond avec une bordure rouge mais de la couleur d�sir�
              case "rond":
                  ImageFilledEllipse ($this->RessourceCalque["souris"], 10, 10, 20, 20, imagecolorallocate($this->RessourceCalque["souris"],255,0,0));
                  ImageFilledEllipse ($this->RessourceCalque["souris"], 10, 10, 10, 10, imagecolorallocate($this->RessourceCalque["souris"],$R,$G,$B));
              break;
          }
        }
    }
    
    /*
    cr�e le calque conteneur avec les parametre de sortie
    et de taille de l'application d�finit dans le css
    @access public
    */
    public function CreateApplication()
    {
      // On r�cupere les dimension de l'aplicaiton dans le css
      list($this->width,$this->height) = explode("x",$this->css["Application"]["taille"]);
      
      // Si la sortie de l'application est du type gif
      if ($this->css["Application"]["type"]=="gif")
      {
          // Alors le conteur sera une ressource gif
          $this->conteneur = imagecreate($this->width,$this->height);
      }
      else
      {
          // Sinon le contenur sera une ressource jpeg
          $this->conteneur = imagecreatetruecolor($this->width,$this->height);
      }
      
      // On peint le fon de l'image en blanc
      imagecolorallocate($this->conteneur, 0,0,0);
    }
    
    /* 
      Ajoute un calque
      @access public
      @param string $nom Le nom du calque
      @param int $largeur La largeur maximum du calque
      @param int $hauteur La hauteur maximum du calque
      @param int $width La largeur du calque
      @param int $height La hauteur du calque
      @param int $x La position en X du calque
      @param int $y La position en Y du calque
      @param boolean $ressource Si le calque est une ressource alors nom du fichie ressource sinon false
      @param boolean $share True si le calque est partag� par toutes les instance de l'application sinon false s'il est unique � cette instance
    */
    public function AddCalque($nom,$largeur=0,$hauteur=0,$width=0,$height=0,$x=0,$y=0,$ressource=false,$share=false)
    {
      
        // Si la largeur est z�ro alors la largeur sera de la largeur de l'application
        if ($width==0) { $width=$this->width; }
        
        // Si la hauteur est z�ro alors la hauteur sera de la hauteur de l'application
        if ($height==0) { $height=$this->height; }
        
        // Si la largeur est z�ro alors la largeur sera de la largeur de l'application
        if ($largeur==0) { $largeur=$this->width; }
        
        // Si la hauteur est z�ro alors la hauteur sera de la hauteur de l'application
        if ($hauteur==0) { $hauteur=$this->height; }
        
        $this->RessourceCalque[$nom]=array();
        
        // Si le calque n'et pas une image import� alors 
        if (!$ressource)
        {
            // On lui associ� une ressource cr�e � la vol�e
            $this->RessourceCalque[$nom]=imagecreate($largeur,$hauteur);
            
            // Qu'on peint en blanc
            imagecolorallocate($this->RessourceCalque[$nom],0,0,0);
        }
        else
        {
            // Sinon on cr�e une ressource � la vol�e avec l'image pr�charger dedans
            $this->RessourceCalque[$nom]=imagecreatefrompng(RESSOURCE_ROOT."ressource/".$ressource);
            
            // Et ont r�cupere les dimension de l'image charger
            list($width, $height) = getimagesize(RESSOURCE_ROOT."ressource/".$ressource);
        }
        
        // On initialise les propri�t� du calque
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
    
    /*
      Met a jour la souris et l'inclus si elle n'est pas d�ja inclus
      @access public
      @param int $x La position en X de la souris
      @param int $y La position en Y de la souris
    */
    public function UpdateMouse($x,$y)
    {
        // On limite le fps pour ne pas d�passer les 25 fps
        $this->LimitFps(); // IMPORTANT
        
        // Si la souris n'est pas encore g�n�r� alors on la g�n�re
        if (!count($this->config_mouse)) { $this->SetTypeMouse(); }
        
        // On met � jour la position de la souris
        $this->ProprieteCalque["souris"]["X"]=$x;
        $this->ProprieteCalque["souris"]["Y"]=$y;
    }
    
    
    /*
      Applatit tout le calque a l'exeption des calque 'hide'
      et envoie la sortie selont les parametre de l'application (ex : gif , jpg 85%)
      @access public
      @param boolean $destroy_screen Si true le conteneur est supprim� apr�s chaque frame
      @return boolean Retourne true si l'envoie de l'image s'est bien pass� sinon false
    */
    public function OutputScreen($destroy_screen=0)
    {
        // ICI ON APPLATIE TOUS LES CALQUES
        // On parcours les calque
        foreach($this->RessourceCalque as $key => $value )
        {
            // Si le calque n'est pas cach�
            if ($this->ProprieteCalque[$key]["hide"]==false)
            {
              // Alors on l'insere dans le contenru
              imagecopy($this->conteneur, $this->RessourceCalque[$key], $this->ProprieteCalque[$key]["X"], $this->ProprieteCalque[$key]["Y"], 0, 0, $this->ProprieteCalque[$key]["width"],$this->ProprieteCalque[$key]["height"]); 
            }
        }
         
        // On pr�pare la buff�risation
        ob_start();
        
        // Si l'application est de sortie gif
        if ($this->css["Application"]["type"]=="gif")
        {
            // Alors on bufferise l'image gif g�n�r�
            imagegif($this->conteneur);
        }
        else
        {
            // Sinon on bufferise l'image jpg g�nr� avec la qualit� shouait�
            imagejpeg($this->conteneur,NULL,$this->css["Application"]["quality"]);
        }
        
        // On r�cupere la buff�risation dans $img
        $img=ob_get_clean();
        
        // Qu'on envoie au client dans une commande
        // Sachant que l'image est encore en base64
        $data = array("Type" => "view" , "Data" => base64_encode($img));
        
        $this->send($data);
        
        // Si destroy_screen alors on d�truit le conteneur
        if ($destroy_screen) { imagedestroy($this->conteneur); $this->CreateApplication(); }
        
        // Puis on retourne si il y a retour
        if(!socket_recv($this->socket,$buffer,2048,0)) { return false; } else { return true; }
    }
    
    /*
    Insert un texte dans le calque dont le nom est pass� en parametre avec une valeur une position
    et une couleur aussi pass� en parametre
    @access public
    @param string $res Nom du calque de destination
    @param string $texte Contenu du texte
    @param int $x La position en X du texte
    @param int $y La position en Y du texte
    @param string $couleur La couleur du texte en R|G|B
    */
    public function AfficherTexte($res,$texte,$x,$y,$couleur)
    {
        // On limite le fps poru ne aps d�passer les 25 fps
        $this->LimitFps(); // IMPORTANT
        
        // On signale que le calque � �t� modifi�
        $this->ProprieteCalque[$res]["modified"]=true;
        
        // On extrait les composante R,G,B de la couleur
        list($R,$G,$B) = explode("|",$couleur);
        
        // On d�ssine le texte
        imagestring($this->RessourceCalque[$res], 4, $x, $y, $texte, imagecolorallocate($this->RessourceCalque[$res],$R,$G,$B));
    }
    
    /*
     A voir :::
     if(!isset($this->RessourceCalque[$res]["color"][$couleur])) {
            $this->RessourceCalque[$res]["color"][$couleur]=imagecolorallocate($this->RessourceCalque[$res],$R,$G,$B);
            }
        imagestring($this->RessourceCalque[$res], 4, $x, $y, $texte, $this->RessourceCalque[$res]["color"][$couleur]);
   */
        
    /*
    Insert un texte dans le calque dont le nom est pass� en parametre avec une position une taille et une couleur
    @access public
    @param string $res Nom du calque de destination
    @param int $x_1 Position en X du coin sup�rieur gauche
    @param int $y_1 Position en Y du coin sup�rieur gauche
    @param int $x_2 Position en X du coin inf�rieur droit
    @param int $y_2 Position en Y du coin inf�rieur droit
    */
    public function insertRectangle($res,$x_1,$y_1,$x_2,$y_2,$couleur)
    {
        // On limite le fps poru ne aps d�passer les 25 fps
        $this->LimitFps(); // IMPORTANT
        
        // On signale que le calque � �t� modifi�
        $this->ProprieteCalque[$res]["modified"]=true;
        
        // On extrait les composante R,G,B de la couleur
        list($R,$G,$B) = explode("|",$couleur);
        
        // On d�ssine le rectangle
        imagefilledrectangle($this->RessourceCalque[$res], $x_1,$y_1,$x_2,$y_2, imagecolorallocate($this->RessourceCalque[$res],$R,$G,$B));
    }
    
    /*
    Insert un texte dans le calque dont le nom est pass� en parametre avec une position une taille et une couleur
    @access public
    @param string $res Nom du calque de destination
    @param int $x_1 Position en X du coin sup�rieur gauche
    @param int $y_1 Position en Y du coin sup�rieur gauche
    @param int $x_2 Position en X du coin inf�rieur droit
    @param int $y_2 Position en Y du coin inf�rieur droit
    */
    public function insertLine($res,$x_1,$y_1,$x_2,$y_2)
    {
        // On limite le fps poru ne aps d�passer les 25 fps
        $this->LimitFps(); // IMPORTANT
        
        // On signale que le calque � �t� modifi�
        $this->ProprieteCalque[$res]["modified"]=true;

        imageline($this->RessourceCalque[$res],$x_1,$y_1,$x_2,$y_2,imagecolorallocate($this->RessourceCalque[$res],0,0,0));
    }
        
    /*
    Insert un texte dans le calque dont le nom est pass� en parametre avec une position une taille et une couleur
    @access public
    @param string $res Nom du calque de destination
    @param string $color Couleur du bouton en R|G|B
    @param int $position_x Position en X du bouton
    @param int $poxition_y Position en Y du bouton
    @param string $value Contenu textuelle du bouton
    @param int $width Largeur du bouton
    @param int $height Hauteur du bouton
    */
    public function CreateSubmit($res,$color,$position_x,$poxition_y,$value,$width=0,$height=0)
    {
        // On limite le fps poru ne aps d�passer les 25 fps
        $this->LimitFps(); // IMPORTANT
        
        // On signale que le calque � �t� modifi�
        $this->ProprieteCalque[$res]["modified"]=true;
        
        // On extrait les composante R,G,B de la couleur
        list($R,$G,$B) = explode("|",$couleur);
        
        // Calculer la taille du texte
        $taille_textbox=strlen($value);
        
        // Et en deduit la taille du bouton
        if ($width==0) { $width=$taille_textbox*10; }
        
        // Par d�faut la hauteur du bouton est 20 pixel
        if ($height==0) { $height=20; }
        
        // On d�ssine le rectangle repr�sentant le bouton
        imagefilledrectangle($this->RessourceCalque[$res], $position_x, $poxition_y, $position_x+$width, $position_y+$height, imagecolorallocate($this->RessourceCalque[$res], $R,$G,$B));
        
        // Et son texte � l'int�rieur
        imagestring($this->RessourceCalque[$res], 5, $position_x+4, $poxition_y+2, $value, imagecolorallocate($this->RessourceCalque[$res], 255, 255, 255));
    }
  
}
?>