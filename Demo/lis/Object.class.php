<?php
/*
 Permet la manipulation de tout les objet
*/
Class Object {
    
    public $HandleMouseMove = null;
    public $HandleMouseClick = null;
    public $HandleMouseDblClick = null;
    
    public $childrens = array();
    public static $ListObjects=array();
    /*
     Ces propriete peuvent etre redefinie par le css
    */
    protected $parent=null;
    protected $id;
    protected $classes=array();
    protected $Position = "Fixed";
    protected $PositionX=0;
    protected $PositionY=0;
    protected $Height=5;
    protected $Width=5;
    protected $handle_application;
    protected static $Register_object=array();
    protected $Updated=true;
    private static $LastDrawnTime=0;
    
    protected $Border=array("Left" => array("Widht" => 0 , "Color" => "" , "Type" => 1),
                            "Right" => array("Widht" => 0 , "Color" => "" , "Type" => 1),
                            "Top" => array("Widht" => 0 , "Color" => "" , "Type" => 1),
                            "Bottom" => array("Widht" => 0 , "Color" => "" , "Type" => 1)
                           );
    
    protected $Margin=array("Left" => array("Widht" => 0),
                            "Right" => array("Widht" => 0),
                            "Top" => array("Widht" => 0),
                            "Bottom" => array("Widht" => 0)
                           );
    
    protected $Padding=array("Left" => array("Widht" => 0),
                             "Right" => array("Widht" => 0),
                             "Top" => array("Widht" => 0),
                             "Bottom" => array("Widht" => 0)
                           );
    protected $Background="white";
    
    
    
    //Enregistre un type d'objet
    public static function RegisterObject($object)
    {
        Object::$Register_object[get_class($object)]=$object;
        return $object;
    }
    
    public function SetUpdated($bool,$recursive = true)
    {
		if($recursive)
		{
			$childrens = $this->GetChildrens();
			foreach($childrens as $children)
			{
				$children->SetUpdated(true);
			}
		}
        $this->Updated=$bool;
    }
    
    public function GetUpdated()
    {
        return $this->Updated;
    }
    
    // A voir si static ou non instance de l'objet
    public function AddObject($parent = null,$name = "")
    {
       echo "[INFO] Un Objet (".get_class($this).") instancier\r\n";
       $class=get_class($this);
       return new $class(false,$parent,$name);
    }
    
    
    //Permet de recuperer un objet enregistrer afin de l'instancier
    public static function GetObject($name_object)
    {
        if(isset(Object::$Register_object[$name_object]))
        {
            return Object::$Register_object[$name_object];
        }
        else
        {
            trigger_error("[ERREUR] Objet (".$name_object.") non trouver par getobjet!!!!",E_USER_NOTICE);
            return null;
        }
    }
        
    public static function SCss($selecteur)
    {
    	if(is_object($selecteur))
    	{
    		$selecteur=get_class($selecteur);
    	}
    	
    	$definitions=array();
    	foreach(ApplicationLIS::GetCss() as $name => $value)
    	{
    		// Recuperation des definition css que l'objet herite
    		if(preg_match("/".$selecteur."/i",$name))
    		{
    			$definitions=array_merge($definitions,$value);
    		}
    	}
    	
    	return $definitions;
    }
    
    // Recupere les definitions css
    public function GetDefinitionsCss()
    {
		return Object::Scss($this);
    }
    
    public function __construct($parent=null,$name="")
    {
        $this->id=uniqid();
        
        // Penser a verifier que le parent est bien un objet
        
        $this->parent = $parent;
        
        // Quand l'objet est declarer il herite des propriete css qui ecrase ceux par defaut
        $definitions=$this->GetDefinitionsCss();
        
        if(!empty($definitions["Position"]))   $this->Position   = $definitions["Position"];
        if(!empty($definitions["PositionX"]))  $this->PositionX  = $definitions["PositionX"];
        if(!empty($definitions["PositionY"]))  $this->PositionY  = $definitions["PositionY"];
        if(!empty($definitions["Height"]))     $this->Height     = $definitions["Height"];
        if(!empty($definitions["Width"]))      $this->Width      = $definitions["Width"];
        if(!empty($definitions["Background"])) $this->Background = $definitions["Background"]; 
        
        // Recuperation des declaration css
        if($parent == null)
        {
            Object::$ListObjects[]=$this;
        }
        else
        {
            $parent->AddChildren($this,$name);
        }
        
        
        // On retourne l'instance de l'objet
        return $this;
    }
    
    //Eface l'objet 
    public function ClearObject()
    {
    	$positionX = $this->PositionX;
    	$positionY = $this->PositionY;
        ApplicationLIS::GetModule("Canvas")->ClearRect($positionX,$positionY,$positionX + $this->Width,$positionY + $this->Height);
    }
    
    public function ToStringClasses()
    {
        $String_classes="";
        foreach($this->classes as $classe) { $String_classes.=".".$classe; }
        return $String_classes;
    }
    
    public function SetId($valeur)
    {
        $this->id=$valeur;
        return $this;
    }
    
    public function addClass($valeur)
    {
        if(!in_array($valeur,$this->classes))
        {
            $this->classes[]=$valeur;
        }
        return $this;
    }
    
    public function removeClass($valeur)
    {
        if(in_array($valeur,$this->classes))
        {
            unset($this->classes[array_search($valeur,$this->classes)]);
        }
        return $this;
    }
    
    public function switchClass($ancienne,$nouvelle)
    {
        if(in_array($ancienne,$this->classes))
        {
            $this->classes[array_search($ancienne,$this->classes)]=$nouvelle;
        }
        return $this;
    }
    
    public static function UnregisterObject($name)
    {
        //Suprimer la déclaration d'un objet
    }
    
    public function __destruct()
    {
        // On efface l'objet de l'ecran avant qu'il soit efface de la memoire
        $this->ClearObject();
    }
    
    public function GetId()
    {
        $this->id=$id;
    }
        
    public function GetClass()
    {
        return $this->classes;
    }
    
    public function GetNom()
    {
    	return get_class($this);
    }
    
    // Methode dessinant l'objet par defaut    
    public function DrawnObject()
    {
        
    }
    
    public static function S1($selecteur)
    {
        return current(Object::S($selecteur));
    }
    
    public static function SProxy($selecteurs)
    {
    	return new SProxy(Object::S($selecteurs));
    }
    
    // Selecteur d'objet 
    public static function S($selecteurs)
    {
        if(is_object($selecteurs)) { return $selecteurs; }
        
        $TabSelecteurs=array();
        $i=0;
        foreach(explode(",",$selecteurs) as $value)
        {
            $TabSelecteurs[$i]=array(
                    "NOM" => array(),
                    "ID" => array(),
                    "CLASSES" => array()
            );
            
            if(preg_match("/^([A-Za-z]{1,})(.*)/",$value,$matches))
            {
                $TabSelecteurs[$i]["NOM"][]=$matches[1];
                $value=substr($value,strlen($matches[1]));
            }
            
            if(preg_match("/^[#]{1}([A-Za-z]{1,})/",$value,$matches))
            {
                $TabSelecteurs[$i]["ID"][]=$matches[1];
            }
            elseif(preg_match_all("/[.]{1}([A-Za-z]{1,})/",$value,$matches))
            {
                foreach($matches[1] as $classe)
                {
                    $TabSelecteurs[$i]["CLASSES"][]=$classe;
                }
            }
            $i++;
        }
        
        $objets=array();
            foreach(Object::$ListObjects as $name => $objet)
            {
                foreach($TabSelecteurs as $selecteur)
                {
                    $SELECTEUR_NOM=false;
                    
                    // VERIFIE LE NOM
                    if(count($selecteur["NOM"]) && $selecteur["NOM"][0]==get_class($objet))
                    {
                        if((count($selecteur["ID"]) || count($selecteur["CLASSES"])))
                        {
                            $SELECTEUR_NOM=true;
                        }
                        else
                        {
                            $objets[]=$objet;
                            continue;
                        }
                    }
                    
                    // VERIFIE L'ID
                    if(count($selecteur["ID"]) && $selecteur["ID"][0]==$objet->GetId() && (($SELECTEUR_NOM==true && count($selecteur["NOM"])) || !count($selecteur["NOM"])))
                    {
                        $objets[]=$objet;
                    }
                    
                    //VERIFIE LES CLASSES
                    if(count($selecteur["CLASSES"]) && !count(array_diff($selecteur["CLASSES"],$objet->GetClass())) && (($SELECTEUR_NOM==true && count($selecteur["NOM"])) || !count($selecteur["NOM"])))
                    {
                        $objets[]=$objet;
                    }
                }
            }

        //if(count($objets)==1) { $objets=$objets[0]; }
        return $objets;
    }
    
    // Par defaut un objet n'a pas d'enfants   
    public function GetChildrens()
    {
        return $this->childrens;
    }
    
    public function GetChildren($name)
    {
        if(empty($this->childrens[$name])) return null;
        return $this->childrens[$name];
    }
    
    public function AddChildren($children,$name="")
    {
        if(empty($name)) { $name=uniqid(); }
        $this->childrens[$name] = $children;
    }
    
    public function RemoveChildren($name)
    {
        if(empty($this->childrens[$name])) return false;
        unset($this->childrens[$name]);
        return true;
    }
    
    // Dessine tout les objet y compris les objets enfants
    public static function DrawnAllObjects($parent=null)
    {
        if($parent == null)
        {
            echo "[INFO] Dessin de tous les objets\n";
            if(microtime(true) - Object::$LastDrawnTime < 0.04)
            {
                return;
            }
            Object::$LastDrawnTime=microtime(true);
            
            $objets = Object::$ListObjects;
        }
        else
        {
            $objets = $parent;
        }
        
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
            
            Object::DrawnAllObjects($objet->GetChildrens());
        }
    }

    
    private function IsMouseInObject($objet)
    {
        list($x,$y)=$this->GetPosition();
        if(
	         (
		        	$x >= $objet -> GetAbsolutePositionX() && 
		        	($x <= $objet -> GetAbsolutePositionX() + $objet -> GetWidth())
	         ) &&
	         (
		         	$y >= $objet -> GetAbsolutePositionY() && 
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
    
    public function SetHandleMouse($mousemove = null,$mouseclick = null,$mousedblclick = null)
    {
        $this->SetHandleMouseMove($mousemove);
        $this->SetHandleMouseClick($mouseclick);
        $this->SetHandleMouseDblClick($mousedblclick);
        return $this;
    }
    
    public function SetHandleMouseMove($mousemove)
    {
        $this->HandleMouseMove = $mousemove;
        return $this;
    }
    
    public function SetHandleMouseClick($mouseclick)
    {
        $this->HandleMouseClick = $mouseclick;
        return $this;
    }
    
    public function SetHandleMouseDblClick($mousedblclick)
    {
        $this->HandleMouseDblClick = $mousedblclick;
        return $this;
    }
    
    public static function MouseClick($x,$y)
    {
    	foreach(Object::$ListObjects as $objet)
    	{
	    	if(!empty($objet->HandleMouseClick))
	    	{
	    		// Appel de la methode de l'objet dont un handle a ete enregistrer
	    	
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
    
    public static function MouseDblClick($x,$y)
    {
    	foreach(Object::$ListObjects as $objet)
    	{
	    	if(!empty($objet->HandleMouseDblClck))
	    	{
	    		// Appel de la methode de l'objet dont un handle a ete enregistrer
	    	
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
    
    public static function MouseMove($x,$y)
    {
        foreach(Object::$ListObjects as $objet)
        {
            //Verifie que 
            if($objet->IsMouseInObject($objet))
            {
                if(!empty($objet->HandleMouseMove))
                {
                    // Appel de la methode de l'objet dont un handle a ete enregistrer
                            
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