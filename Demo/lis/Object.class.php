<?php
/*
 Classe Regroupant les objet 2D et 3D
 Elle permet de gerer des element graphique en tant qu'objet
 Elle est notament utiliser Par CanvasObject
 @author Jean pasqualini <jpasqualini75@gmail.com>
 @license GPL
 @version InDev
*/
Class Object {
    
    /*
     @access public
     @var EventProxy Contient les handles pour l'event de position de la position de l'objet
    */
    public $HandleMouseMove = null;
    
    /*
     @access public
     @var EventProxy Contient les handles pour l'event de simple clic sur l'objet
    */
    public $HandleMouseClick = null;
    
    /*
     @access public
     @var EventProxy Contient les handles pour l'event de double click sur l'objet
    */
    public $HandleMouseDblClick = null;
    
    /*
     @access public
     @var Array $childrens Liste des enfants de l'objet
    */
    public $childrens = array();
    
    /*
     @access public
     @var Array $ListObjects Liste de tous les objets instanciée quelque soit leurs types
    */
    public static $ListObjects=array();
    

    // Propriétés de l'ojbet
    
    /*
      @access protected
      @var Object2D Parent de l'objet
    */
    protected $parent=null;
    
    /*
     @access protected
     @var integer Id de l'objet
    */
    protected $id;
    
    /*
     @access protected
     @var Array Classss de l'élément
    */
    protected $classes=array();
    
    /*
     @access protected
     @var string Position Absolute, Fixed ou Relative de l'élément
    */
    protected $Position = "Fixed";
    
    /*
     @access protected
     @var integer Postion en X de l'élément
    */
    protected $PositionX=0;
    
    /*
      @access protected
      @var integer Position en Y de l'élément
    */
    protected $PositionY=0;
    
    /*
     @access protect
     @var integer Hauteur de l'élément
    */
    protected $Height=5;
    
    /*
     @access protected
     @var int Largeur de l'élément
    */
    protected $Width=5;
    
    /*
     @access protected
     @var ApplicationLIS Instance de l'application
    */
    protected $handle_application;
    
    /*
     @access protected
     @static
     @staticvar Array Liste des types d'éléments
    */
    protected static $Register_object=array();
    
    /*
     @access protected
     @var boolean Défini si l'élement a été mis a jour.
     Si l'élément à été mis a jour il vaudra true et il sera redessiner l'ors du prochain passage de Ojbect::DrawnAllObjects()
    */
    protected $Updated=true;
    
    /*
     @access private
     @static
     @staticvar int Contient le timestamp de la dernière fois ou l'objet a été déssiné
    */
    private static $LastDrawnTime=0;
    
    /*
     @access protected
     @var Array Contient les propriété des bordure dont la taille(widht), couleur(Color), type(Type) pour le haut(Top), bas(Bottom), gauche(Left), droit(Right)
    */
    protected $Border=array("Left" => array("Widht" => 0 , "Color" => "" , "Type" => 1),
                            "Right" => array("Widht" => 0 , "Color" => "" , "Type" => 1),
                            "Top" => array("Widht" => 0 , "Color" => "" , "Type" => 1),
                            "Bottom" => array("Widht" => 0 , "Color" => "" , "Type" => 1)
                           );

    /*
     @access protected
     @var Array Contient les propriété des marges éxterieure dont la taille(widht), couleur(Color), type(Type) pour le haut(Top), bas(Bottom), gauche(Left), droit(Right)
    */
    protected $Margin=array("Left" => array("Widht" => 0),
                            "Right" => array("Widht" => 0),
                            "Top" => array("Widht" => 0),
                            "Bottom" => array("Widht" => 0)
                           );
    
    /*
     @access protected
     @var Array Contient les propriété des marges intérieure dont la taille(widht) pour le haut(Top), bas(Bottom), gauche(Left), droit(Right)
    */
    protected $Padding=array("Left" => array("Widht" => 0),
                             "Right" => array("Widht" => 0),
                             "Top" => array("Widht" => 0),
                             "Bottom" => array("Widht" => 0)
                           );
    
    /*
     @access protected
     @var string Fond de l'objet en couleur html
    */
    protected $Background="white";
    
    
    
    /*
     Enregistrer un nouveau type d'objet
     @access public
     @param Object Instance du nouveau Type d'objet
     @return Object Retour l'instance de l'objet
    */
    public static function RegisterObject($object)
    {
        Object::$Register_object[get_class($object)]=$object;
        return $object;
    }
    
    /*
     @access public
     Informe que l'objet à été mis à jour
     @param boolean $bool définir true si l'objet à été mis a jour sinon false
     @param boolean $recursive Definir true si les objet doivent eu aussi être mis à jour sinon false
    */
    public function SetUpdated($bool,$recursive = true)
    {
		// Si le paramétre $recursive est true
		if($recursive)
		{
			// Récupère et liste tous les enfants
			$childrens = $this->GetChildrens();
			foreach($childrens as $children)
			{
				// Met à jour les enfants
				$children->SetUpdated(true);
			}
		}
        $this->Updated=$bool;
    }
    
    /*
      @access public 
      Retourne true si l'objet à été mis à jour sinon false
      @return boolean Retourni true si l'objet à été mis à jour sinon false
    */
    public function GetUpdated()
    {
        return $this->Updated;
    }
    
    
    /*
     Instancie un objet
     @access public
     @param Object $parent Parent de l'objet
     @param string $name Nom de l'objet
     @return Object Retour l'instance de l'objet instancier
    */
    public function AddObject($parent = null,$name = "")
    {
       echo "[INFO] Un Objet (".get_class($this).") instancier\r\n";
       
       // Recupere le nom de la classe de l'objet courant
       $class=get_class($this);
       
       // Et crée une nouvelle instance de cette objet
       return new $class(false,$parent,$name);
    }
    
    
    /*
     Permet de recuperer un objet enregistrer afin de l'instancier
     @access public
     @param string $name_oject Nom de l'objet
     @return Object Retourne l'instance du type d'objet afin de pouvoir instance un objet
     de ce type avec AddObject($parent = null, $name = "") ou null s'il n'existe pas
    */
    public static function GetObject($name_object)
    {
	// Si l'objet Existe
        if(isset(Object::$Register_object[$name_object]))
        {
	    // Le retourne
            return Object::$Register_object[$name_object];
        }
        else // Sinon
        {
	    // Genere une erreur puis retourne nul
            trigger_error("[ERREUR] Objet (".$name_object.") non trouver par getobjet!!!!",E_USER_NOTICE);
            return null;
        }
    }
        
    /*
     Permet de selection un ObjetCss Comme pour un Objet Standart (ex: div#id.class)
     @access public
     @static
     @param string $selecteur Le selecteur de l'objetCss , Il est possible de passer une instance d'un Object
     @return Array Retourne les définitions de l'objet
    */
    public static function SCss($selecteur)
    {
	// Si le selecteur est un objet 
    	if(is_object($selecteur))
    	{
		// Alors le selecteur est le nom de classe (type) de l'objet
    		$selecteur=get_class($selecteur);
    	}
    	
    	$definitions=array();
	
	// Liste toutes les élement css
    	foreach(ApplicationLIS::GetCss() as $name => $value)
    	{
		// Et les empile dans un tableau s'il correspond au sélecteur
    		if(preg_match("/".$selecteur."/i",$name))
    		{
    			$definitions=array_merge($definitions,$value);
    		}
    	}
    	
	// Puis renvoie le tableau
    	return $definitions;
    }
    
    /*
     Permet de récuperer les déffinition css de l'objet courant
     @access public
     @return Array Retourne les définitions de l'objet
    */
    public function GetDefinitionsCss()
    {
		return Object::Scss($this);
    }
    
    /*
     C'est le constructeur d'un objet 2D ou 3D
     @access public
     @param Object $parent Objet parent
     @param string $name Nom de l'objet
     @return Object Retourne l'instance de l'objet
    */
    public function __construct($parent=null,$name="")
    {
	// L'id de l'objet est par défaut égale a un id aléatoire
        $this->id=uniqid();
        
        // Penser a verifier que le parent est bien un objet
        
        $this->parent = $parent;
        
        // Quand l'objet est declarer il herite des propriete css qui ecrase ceux par defaut
        $definitions=$this->GetDefinitionsCss();
        
	// Si une définition css existe pour l'objet alors elle est directement appliqué sur les propriété de celui ci
        if(!empty($definitions["Position"]))   $this->Position   = $definitions["Position"];
        if(!empty($definitions["PositionX"]))  $this->PositionX  = $definitions["PositionX"];
        if(!empty($definitions["PositionY"]))  $this->PositionY  = $definitions["PositionY"];
        if(!empty($definitions["Height"]))     $this->Height     = $definitions["Height"];
        if(!empty($definitions["Width"]))      $this->Width      = $definitions["Width"];
        if(!empty($definitions["Background"])) $this->Background = $definitions["Background"]; 
        
        // Si l'objet n'a pas de parent 
        if($parent == null)
        {
	    //Alors il est empilé à la racine du tableau Object::$ListObject
            Object::$ListObjects[]=$this;
        }
        else
        {
	    //Sinon on attache l'objet en tant qu'enfant a l'objet parent
            $parent->AddChildren($this,$name);
        }
        
        
        // Puis on retourne l'instance de l'objet
        return $this;
    }
    
    /*
     Permet d'effacer l'objet courant
     @access public
    */
    public function ClearObject()
    {
    	$positionX = $this->PositionX;
    	$positionY = $this->PositionY;
	
	// On efface la partie de l'objet sur la sortie 
        ApplicationLIS::GetModule("Canvas")->ClearRect($positionX,$positionY,$positionX + $this->Width,$positionY + $this->Height);
    }
    
    /*
     Permet de retourner les classes sous forme de classe contaténé (ex: .class1.class2.class3)
     @access public
     @return string retourne les classes sous forme de classe contaténé (ex: .class1.class2.class3)
    */
    public function ToStringClasses()
    {
		// On concatène les nom de classes de l'objet dans une string avec '.' comme glue et on retourne cette string qui pourra servir pour une séléction d'objet
        return ".".implode(".", $this->classes);
    }
    
    /*
     Défini l'id de l'objet
     @access public
     @param string $value L'id de l'objet
     @return Object Retourne l'instance de l'objet courant
    */
    public function SetId($valeur)
    {
        $this->id=$valeur;
        return $this;
    }
    
    /*
     Ajoute une classe à l'objet
     @access public
     @param string $valeur Classe de l'objet
     @return Object Retourne l'instance de l'objet courant
    */
    public function addClass($valeur)
    {
	// Si la classe n'est pas déja déclarer pour cet objet
        if(!in_array($valeur,$this->classes))
        {
	    // Alors on l'empile dans le tableau classes
            $this->classes[]=$valeur;
        }
	
	// Puis on retourne l'instance de l'objet courant
        return $this;
    }
    
    /*
     Supprime une des classes à l'objet
     @access public
     @param string $valeur Classe à supprimer
     @return Object Retourne l'instance de l'objet courant
    */
    public function removeClass($valeur)
    {
	// Si la classe est dans le tableau classes
        if(in_array($valeur,$this->classes))
        {
	    // Alors on la dépile
            unset($this->classes[array_search($valeur,$this->classes)]);
        }
	
	// Puis on retourne l'instance de l'objet courant
        return $this;
    }
    
    /*
      Permet de supprimer une classe à une autre
      @access public
      @param string $ancienne Ancienne classe
      @param string $nouvelle Nouvelle classe
      @return Object Retourne l'instance de l'objet couratn
    */
    public function switchClass($ancienne,$nouvelle)
    {
	// Si la classe est dans le tableau
        if(in_array($ancienne,$this->classes))
        {
	    // Alors on la remplace par la nouvelle
            $this->classes[array_search($ancienne,$this->classes)]=$nouvelle;
        }
	
	// Puis on retourne l'instance de l'objet courant
        return $this;
    }
    
    /*
     Permet de supprimer un type d'objet enregister
     @access public
     @param string $name Type de l'objet a supprimé
     @todo Implémenté l'objet
    */
    public static function UnregisterObject($name)
    {
	// Cette fonctionalité n'est pas encore implémenté. Function not implemented
	throw new Exception("Cette fonction n'est pas encore implémenté. Function not implemented");
    }
    
    /*
	Destructeur de l'objet
	Détruit l'objet en mémoire et sur l'affichage
	@access public
    */
    public function __destruct()
    {
        // On efface l'objet de l'ecran avant qu'il soit efface de la memoire
        $this->ClearObject();
    }
    
    /*
       Récupere l'id de l'objet
       @access public
       @return integer Retourne l'id de l'objet
    */
    public function GetId()
    {
        $this->id=$id;
    }
        
    /*
      Récupére les classes de l'objet
      @access public
      @return Array Retourne les classes de l'objet
    */
    public function GetClass()
    {
        return $this->classes;
    }
    
    /*
     Récupére le nom de l'objet
     @access public
     @return string Retourne le nom de l'objet
    */
    public function GetNom()
    {
	// Retourne le nom de la classe(Type) de l'objet courant
    	return get_class($this);
    }
    
    /*
     Méthode déssinant l'objet par défaut
     @todo Crée une interface Object et ajouter cette méthode
     @access public
    */
    public function DrawnObject()
    {
        
    }
    
    /*
      Permet de sélectione un objet unique avec un sélécteur d'objet (ex: RECTANGLE#gauche.box)
      @access public
      @static
      @param string sélécteur de l'objet
      @return Object Retourne l'instance de l'objet selectionée
    */
    public static function S1($selecteur)
    {
	// Retourne la premiere occuperence du tableau retourner par le selecteur d'objet
        return current(Object::S($selecteur));
    }
    
    /*
      Permet de sélectioner plusieur objet et de récuperer un Proxy dé sélection (ex: RECTANGLE#gauche.box)
      afin d'apliquer des action globale sur les objets séléctioner
      @access public
      @static
      @param string sélecteur de l'objet
      @return SProxy Retourne un Proxy de séléction permettatn d'appliquer une action globale sur les objets sélectioner
    */
    public static function SProxy($selecteurs)
    {
	// Retour un proxy de sélection avec pour objet les objets retourner par le sélecteur d'objet
    	return new SProxy(Object::S($selecteurs));
    }
    
    /*
      Permet de sélectioner plus objet avec un sélecteur d'objet (ex: RECTANGLE#gauche.box)
      @access public
      @static
      @param string sélecteur de l'objet
      @return Array Retourne un tableau d'objets correspondant à la séléction
    */
    public static function S($selecteurs)
    {
	// Si le selecteur est un objet alors on retourne cette objet
        if(is_object($selecteurs)) { return $selecteurs; }
        
        $TabSelecteurs=array();
        $i=0;
	// On separe les different selecteur du selecteur (ex: Selecteur1,Selecteur2)
        foreach(explode(",",$selecteurs) as $value)
        {
	    // On initialise les paramétre nom , id et classes
            $TabSelecteurs[$i]=array(
                    "NOM" => array(),
                    "ID" => array(),
                    "CLASSES" => array()
            );
            
	    // On récupère le nom et on l'ajoute dans le tableau de nom
            if(preg_match("/^([A-Za-z]{1,})(.*)/",$value,$matches))
            {
                $TabSelecteurs[$i]["NOM"][]=$matches[1];
                $value=substr($value,strlen($matches[1]));
            }
            // On récupre l'id s'il y'en a et ont l'ajoute dans le tableau d'id
            if(preg_match("/^[#]{1}([A-Za-z]{1,})/",$value,$matches))
            {
                $TabSelecteurs[$i]["ID"][]=$matches[1];
            }
            elseif(preg_match_all("/[.]{1}([A-Za-z]{1,})/",$value,$matches)) // Sinon on récupere les classes et ont les rajoutes dans le tableau des classes
            {
                foreach($matches[1] as $classe)
                {
                    $TabSelecteurs[$i]["CLASSES"][]=$classe;
                }
            }
            $i++;
        }
        
	// Une fois qu'on a notre selecteur textuelle transformer en tableau content les information pour la sélection
	
        $objets=array();
	    // On parcours tous les objet
            foreach(Object::$ListObjects as $name => $objet)
            {
                foreach($TabSelecteurs as $selecteur)
                {
                    $SELECTEUR_NOM=false;
                    
                    // VERIFIE LE NOM (Le nom est optionelle)
		    // Si le selecteur contient un nom alors on le compare au nom de l'objet du parcours d'objet
                    if(count($selecteur["NOM"]) && $selecteur["NOM"][0]==get_class($objet))
                    {
			// Si il y a un selecteur d'id ou de classe alros le selecteur de nom est passé a true 
                        if((count($selecteur["ID"]) || count($selecteur["CLASSES"])))
                        {
                            $SELECTEUR_NOM=true;
                        }
                        else
                        {
			    // Sinon l'objet est empilé au tableau d'objet et on passe a la prochaine itération du parcour d'obet
                            $objets[]=$objet;
                            continue;
                        }
                    }
		    
		    //Alors que c'est l'id ou classe qui est utilisé
                    
                    // VERIFIE L'ID
		    // Si le selecteur possede un id et que cet id existe dans l'objet courant du parcour d'objet
		    // Et que il y a un selecteur de nom présent et que le nom existe dans l'objet ou que il n'y pas pas de selecteur de nom présent
                    if(count($selecteur["ID"]) && $selecteur["ID"][0]==$objet->GetId() && (($SELECTEUR_NOM==true && count($selecteur["NOM"])) || !count($selecteur["NOM"])))
                    {
			// Alors l'objet est empilé dans le tableau d'objet
                        $objets[]=$objet;
                    }
                    
                    //VERIFIE LES CLASSES
		    // S'il y a un selecteur de classe et que il n'y a pas de classe du selecteur de classe qui ne sont pas présente dans les classe de l'objet courant du parcour d'objet
		    // Et que Il y a un selecteur de nom et que ce nom est contenu dans l'objet courant ou s'il n'y a pas de selecteur de nom
                    if(count($selecteur["CLASSES"]) && !count(array_diff($selecteur["CLASSES"],$objet->GetClass())) && (($SELECTEUR_NOM==true && count($selecteur["NOM"])) || !count($selecteur["NOM"])))
                    {
			// Alors l'objet est empilé dans le tableau d'objet
                        $objets[]=$objet;
                    }
                }
            }

        //if(count($objets)==1) { $objets=$objets[0]; }
        return $objets;
    }
    
    /*
     Permet de récuperer la listes des enfant d'un objet
     @access public
     @return Array Retourne un tableau d'objet
    */
    public function GetChildrens()
    {
        return $this->childrens;
    }
    
    /*
      Permet de récuperer un enfant de l'objet par son nom($name) s'il n'existe pas sa renvoie null
      @access public
      @param string $name nom de l'objet
      @return Object Retourne l'enfant ou null s'il n'existe pas
    */
    public function GetChildren($name)
    {
        if(empty($this->childrens[$name])) return null;
        return $this->childrens[$name];
    }
    
    /*
     Permet d'attacher un objet à un autre en tant qu'enfant
     @access public
     @param Object $childrens Instance de l'objet enfant
     @param string $name Nom de l'objet
    */
    public function AddChildren($children,$name="")
    {
        if(empty($name)) { $name=uniqid(); }
	
	// On ajoute l'enfant dans le tableau childrens avec pour clé le paramètre $name
        $this->childrens[$name] = $children;
    }
    
    /*
     Permet de détacher un enfant à un parent (c'est cruelle hinhin...)
     @access public
     @param string $name Nom de l'objet
     @return Boolean
    */
    public function RemoveChildren($name)
    {
        if(empty($this->childrens[$name])) return false;
	
	// On supprime l'enfant dans le tableau childrens qui à pour mot clé $name
        unset($this->childrens[$name]);
        return true;
    }
    
    /*
      Déssine tous les objets mis à jour y compris les enfants
      @access public
      @static
      @param Object $parent Branche parent d'ou on part récursivement vers les enfants , null si ont part de l'objet principale
    */
    public static function DrawnAllObjects($parent=null)
    {
	// Si ont part de la racine des objet
        if($parent == null)
        {
            echo "[INFO] Dessin de tous les objets\n";
	    
	    // Alors on vérifie que le rafraichissement des objet ne dépasse
	    // par les 25 fps et on skype les frames supplémentaires
            if(microtime(true) - Object::$LastDrawnTime < 0.04)
            {
                return;
            }
	    
	    // Puis on met a jour valeur du dernier affichage
            Object::$LastDrawnTime=microtime(true);
            
	    // L'objet à analyser devient alors la liste d'objet sans parent de $ListObjects
            $objets = Object::$ListObjects;
        }
        else
        {
	    // Sinon les objet sont les enfant du parent
            $objets = $parent;
        }
        
	// On parcour la lsite d'objet
        foreach($objets as $objet)
        {
            // Ne redesine l'objet que s'il a été mis a jour
            if($objet->GetUpdated())
            {
                //On efface l'objet
                $objet->ClearObject();
                
                // On redessine tout les objets
                $objet->DrawnObject();
                
                // On informe que l'objet de ne pas redisiner l'objet
                $objet->SetUpdated(false);
            }
            
	    // On Desine les objet objet recursivement
            Object::DrawnAllObjects($objet->GetChildrens());
        }
    }

    /*
     Permet de savoir si la souris est sur un objet avec l'objet en parametre
     @access public
     @param Object $objet Instance de l'objet
     @return boolean Retourne true si la souris est sur l'objet sinon false
    */
    private function IsMouseInObject($objet)
    {
	// On Récupére la position de la souris et on retourne true si la souris est sur l'objet sinon false
        list($x,$y)=$this->GetPosition();
	
        if(
	         (
				// Si la position X de la souris est plus grande ou égale que la position Absolute de l'objet en X
		        	$x >= $objet -> GetAbsolutePositionX() &&
				// Et que la position en X de la souris est plus Petite que La position Absolute de l'objet en X + Sa largeur
		        	($x <= $objet -> GetAbsolutePositionX() + $objet -> GetWidth())
	         ) &&
	         (
				// Si la position Y de la souris est plus grande ou égale que la position Absolute de l'objet en Y
		         	$y >= $objet -> GetAbsolutePositionY() &&
				// Et que la position en Y de la souris est plus Petite que La position Absolute de l'objet en Y + Sa hauteur
		         	($y <= $objet -> GetAbsolutePositionY() + $objet -> GetHeight())
	         )
         )
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    /*
     Permet de définir les handle pour un objet
     @access public
     @param EventProxy $mousemove Définit l'handle pour la position de la souris
     @param EventProxy $mouseclick Définit l'handle pour le click de la souris
     @param EventProxy $mousedblclick Défini l'handle pour le doubleclick de la souris
     @return Object Retourne l'instance de l'objet courant
    */
    public function SetHandleMouse($mousemove = null,$mouseclick = null,$mousedblclick = null)
    {
	//On parametre les handles
        $this->SetHandleMouseMove($mousemove);
        $this->SetHandleMouseClick($mouseclick);
        $this->SetHandleMouseDblClick($mousedblclick);
        return $this;
    }
    
    /*
     Permet de définir l'handle pour la position de la souris
     @access public
     @param EventProxy $mousemove Définit l'handle pour la position de la souris
     @return Object Retourne l'instance de l'objet courant
    */
    public function SetHandleMouseMove($mousemove)
    {
        $this->HandleMouseMove = $mousemove;
        return $this;
    }
    
    /*
    Permet de définir l'handle pour le click de la souris
    @access public
    @param EventProxy $mouseclick Définit l'handle pour le click de la souris
    @return Object Retourne l'instance de l'objet courant
    */
    public function SetHandleMouseClick($mouseclick)
    {
        $this->HandleMouseClick = $mouseclick;
        return $this;
    }
    
    /*
    Permet de définir l'handle  pour le doubleclick de la souris
    @access public
    @param EventProxy $mouseclick Définit l'handle pour le click de la souris
    @return Object Retourne l'instance de l'objet courant
    */
    public function SetHandleMouseDblClick($mousedblclick)
    {
        $this->HandleMouseDblClick = $mousedblclick;
        return $this;
    }
    
    /*
     Permet d'informer que la souris à cliquer à position donner
     La fonction s'occupe d'informer le(s) objet(s) concernée
     @access public
     @param integer $x Position X de la souris
     @param integer $y Position Y de la souris
    */
    public static function MouseClick($x,$y)
    {
	// On parcour la liste d'objet
    	foreach(Object::$ListObjects as $objet)
    	{
		// Si un handle pour le click de souris à été défini
	    	if(!empty($objet->HandleMouseClick))
	    	{
	    		// Appel de la methode de l'objet dont un handle à ete enregistrer
	    		if(is_callable($objet->HandleMouseClick))
	    		{
	    			call_user_func_array($this->HandleMouseClick,array($this->positionX,$this->positionY));
	    		}
	    		else
	    		{
	    			call_user_func_array(array($this->handle_application,$this->HandleMouseClick),array($this->positionX,$this->positionY));
	    		}
	    	}
    	}
    }
    
    /*
     Permet d'informer que la souris à double cliquer à position donner
     La fonction s'occupe d'informer le(s) objet(s) concernée
     @access public
     @param integer $x Position X de la souris
     @param integer $y Position Y de la souris
    */
    public static function MouseDblClick($x,$y)
    {
    	foreach(Object::$ListObjects as $objet)
    	{
		// Si un handle pour le double click de souris à été défini
	    	if(!empty($objet->HandleMouseDblClck))
	    	{
	    		// Appel de la methode de l'objet dont un handle à ete enregistrer
	    		if(is_callable($objet->HandleMouseDblClck))
	    		{
	    			call_user_func_array($this->HandleMouseDblClck,array($this->positionX,$this->positionY));
	    		}
	    		else
	    		{
	    			call_user_func_array(array($this->handle_application,$this->HandleMouseDblClck),array($this->positionX,$this->positionY));
	    		}
	    	}
    	}
    }
    
    /*
     Permet d'informer que la souris bouger à position donner
     La fonction s'occupe d'informer le(s) objet(s) concernée
     @access public
     @param integer $x Position X de la souris
     @param integer $y Position Y de la souris
    */
    public static function MouseMove($x,$y)
    {
        foreach(Object::$ListObjects as $objet)
        {
            //Verifie que la souris est sur l'objet
            if($objet->IsMouseInObject($objet))
            {
		// Et qu'un handle à été défini pour la posiition de la souris
                if(!empty($objet->HandleMouseMove))
                {
                    // Appel de la methode de l'objet dont un handle à ete enregistrer  
                    if(is_callable($objet->HandleMouseMove))
                    {
                        call_user_func_array($this->HandleMouseMove,array($this->positionX,$this->positionY));
                    }
                    else
                    {
                        call_user_func_array(array($this->handle_application,$this->HandleMouseMove),array($this->positionX,$this->positionY));  
                    }
                }                            
            }
        }
    }
    
}
?>