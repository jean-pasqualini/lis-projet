<?php
/*
 Clase principale du framework 
 @author Jean pasqualini <jpasqualini75@gmail.com>
 @version InDev
 @license GPL
 @todo N�gociation de module avec le client � faire
 @todo Impl�menter la gestion des erreurs en exception
*/
Abstract Class ApplicationLIS {
    
    /*
     @access public
     @static
     @staticvar ApplicationLIS Contient la single instance (unique) de l'application
    */
    public static $UniqueInstance = null;
    
    /*
     @access public
     @var  Socket Contient la connection socket
    */
    public $instance;
    
    /*
     @access public
     @static
     @staticvar Array Contients les instances des modules charg�e
    */
    public static $modules=array();
    
    /*
     @access private
     @static
     @staticvar Array Contient les propri�t� css importer
    */
    private static $css=array(); // Contient les propri�t� des css importer
    
    /*
     @access private
     @var string Contient le th�me courant
    */
    private $theme="default";
    
    /*
     @access private
     @var integer Contient le temp
    */
    private $time;
    
    /*
     @access private
     @var integer contient le temp en seconde autoriser par thread
    */
    private $time_thread = 5; //exprime en seconde
    
    /*
     @access private
     @var Array Contient les commande �venementiel recu en attente de lecture
    */
    private $events = array();
    
    /*
     @access private
     @var Array Contient les retours recu en attente de lecture
    */
    private $returns = array();
    
  /*
   Le constructeur de l'application initialise l'appplication
   @access public
   @param string $address L'adresse d'attente d'un client de l'application
   @param integer $port Le port d'attente d'un client de l'appplication
   @return ApplicationLIS Retourne l'instance courante de l'application
  */
  public function __construct($address,$port){
    
    ApplicationLIS::$UniqueInstance = $this;
        
    global $_CONFIGURATION; // Permet d'acceder de facon global au donn�e de configuration d�clarer 
    global $error; // Permer d'acc�der de facon globale au tableau d'erreurs $error
    
    //On importe le css globale de l'application si celui-ci existe
    if(file_exists("theme/".$this->theme."/css/application.css")) { $this->ImportCss("application.css"); }
   
    // IMPORTANT : SUPPRIME LA BARIERE DES 30 SECONDES
    set_time_limit(0);
   
    // L'envoi implicite signifie que toute fonction qui envoie des donn�es au navigateur verra ses donn�es envoy�es imm�diatement
    ob_implicit_flush();
    
    //On cr�e la socket
    if(($socket = socket_create(AF_INET, SOCK_STREAM, 0)) === false)
    {
	    // Si elle ne se charge pas alors on affiche une erreur indiquant que la socket n'a pas pu �tre cr�e
	    echo 'La cr�ation de la socket a �chou� : '.socket_strerror($socket)."\n<br />";
    }
        
    //On assigne la socket � une adresse et � un port, que l'on va �couter par la suite.
    if(($assignation = socket_bind($socket, $address, $port)) < 0)
    {
	    // Si elle ne s'assigne pas alors on affiche une erreur indiquant que l'assignation � �chou�
	    echo "L'assignation de la socket a �chou� : ".socket_strerror($assignation)."\n<br />";
    }
        
    //On pr�pare l'�coute.
    if(($ecoute = socket_listen($socket)) === false)
    {
	    echo "L'�coute de la socket a �chou� : ".socket_strerror($ecoute)."\n<br />";
	  
	  // Si le client n'a pas pu se connecter alors...
          if(($client = socket_accept($socket)) === false)
          {
	    // On affiche un message d'information pour dire que le client n'a pas pu se connecter
            echo "Le client n'a pas pu se connecter : ".socket_strerror($client)."\n<br />";
            
            $this->disconnect();
            
            return false;
          }
          
	  // On strocke la ressource socket cliente dans la propri�t� $instance
          $this->instance=$client;
              
          // Ici charge les modules de rendu (n�gociation client/server a implementer) 
              
          // On verifie la configuration des modules principaux pour l'audio et l'affichage et si ceux-ci ne se chargent pas alors on g�n�re une erreur
          if (!count($_CONFIGURATION['MODULE']['audio'])) {  trigger_error("Module de rendu audio non configur�e",E_USER_NOTICE); }
          if (!count($_CONFIGURATION['MODULE']['view'])) {  trigger_error("Module de rendu view non configur�e",E_USER_NOTICE); }
    
            // On parcour tous les modules de rendu disponible tout les modules
            foreach($_CONFIGURATION['MODULE'] as $name_module => $liste_rendu)
            {
		// Et charge une de leur altrernative si disponible
                foreach ($liste_rendu as $module)
                {
                    // On ajoute en memoire le module
                    if($this->AddModule($module)!=null) { break; }
                }
                
            }
              
            /*
	      On declare que les handle de reception de traitement �venementiel
	      seront appel�e tous 5 appel d'instruction php (tick)
	      
	      Cela permet de faire une sorte de setInterval pour ceux qui connaisse javascript
	      Cette fonction est d�sormait depreaced et c'est bien domage
	    */
	    	    
	    declare(ticks=5);
	    register_tick_function(array(&$this, 'handle_event'), true);
	    register_tick_function(array(&$this, 'handle_recv'), true);  
          
            return true;
}

/*
 Retourne l'instance unique de l'application 
 @access public
 @static
 @return ApplicationLis Retourne l'instance unique de l'application courante
*/
public static function GetInstance()
{
	// Si l'application n'a pas encore �t� instancie alors...
	if (ApplicationLIS::$UniqueInstance == null) {
		/*
		 On genere une erreur pour dire que l'application n'est pas initialiser
		 et donc qu'on ne peut pas retourner l'instance
		*/
		trigger_error("Erreur application non initialiser",E_USER_ERROR); 
		exit();
	}
	// Sinon on retourne l'instance
	return ApplicationLIS::$UniqueInstance;
}

/*
 Retourne les propri�t� css d�clar� de l'application
 @access public
 @static
 @return Array Retourne les propri�t� css d�clar� de l'application
*/
public static function GetCss()
{
    return ApplicationLIS::$css;
}

/*
 Retourne l'instance d'un module charge
 @access public
 @static
 @param string $name_module Nom du module
 @return ModuleBase Retourne l'instance du module
*/
public static function GetModule($name_module)
{
    // On affiche un message d'information pour dire que le module est demadn�e
    echo "[INFO] demande du module '".$name_module."'\n";
	
    // Si le module existe alors
    if(isset(ApplicationLIS::$modules[$name_module]))
    {
	// On le retourne
        return ApplicationLIS::$modules[$name_module];
    }
    else
    {
	// Sinon on g�n�re une erreur pour dire que le module requis n'est pas charg�e
        trigger_error("[GRAVE] Le module requis (".$name_module.") n'est pas charge actuellement !!!",E_USER_ERROR);
    }
}

/*
 Retourne true si le module existe sinon false
 @access public
 @static
 @param string $name_module Nom du module
 @return boolean Retourne true si le module existe sinon false
*/
public static function IssetModule($name_module)
{
    // Si le module n'existe pas alors
    if(empty(ApplicationLIS::$modules[$name_module]))
    {
	// On retourne false pour dire que la condition est fause
        return false;
    }
    elseif(!is_object(ApplicationLIS::$modules[$name_module]))
    {
	// S'il existe masi qu'il n'est pas un objet alors on g�n�re une erreur
        echo "\n !!!! ".var_dump(ApplicationLIS::$modules[$name_module])." !!!! \n";
        return false;
    }
    else
    {
	// S'il existe et est un objet alors on retourne true pour dire que la condition est vrai
        return true;
    }
}

/*
 Permet de simplifier le travail des devellopeur d'application
 Si besoin permet d'appeler les methode d'un module sans connaitre son nom
 ex : $this->view->UpdateMouse(); devient $this->UpdateMouse();
 
 Attention diminue forcement les performance globale de l'application
 @access public
 @param string $name Nom de la m�thode
 @param Array Liste des param�tre
 @return inconu Retourne false si la m�thode n'a pas �t� trouver sinon retourne le retour de la m�thode
*/
public function __call($name,$arguments)
{
    // On parcours tous les modules
    foreach (ApplicationLIS::$modules as $propriete => $value)
    {
      // Si le module est un objet
      if(is_object($value))
      {
	// et que la methode pass� en argument $name existe dans se module
        if (method_exists ($value,$name))
        {
	    // Alors on retourne le retour de l'appel de la m�thode du module avec pour param�tre $arguments
            return call_user_func_array(array(ApplicationLIS::$modules[$propriete], $name), $arguments);
        }
      }
    }
    // Sinon on retourne false 
    return false;
}



/*
  Ajoute un module � chaud
  Les modules se trouvent dans le dossier module et
  ont pour nom  '___nommodule___.php' et pour nom de classe 'module__nommodule'

  @access public
  @param string $module Nom du module a charger 
  @param Array $parametre Liste des param�tre � passer au contructeur du module
  @return ModuleBase Retourne l'instance du module ou null si le module n'a pas pu �tre charger
*/  
public function AddModule($module,$parametre = array())
{
    // Si le fichier du moduel existe
    if(file_exists("module/".$module.".php"))
    {
	// On affiche un message d'informtation pour indique que le module ce charge
    	echo "[INFO] chargement du module '".$module."'\n";
	
	// Alors on l'inclu
        include("module/".$module.".php");
	
	// On instancie la classe du module 
        $render="module_".$module;
        $instance_module=new $render($this->instance,$this);
	
	// On interoge le module sur ces dependances serveur qui nous renvoie une liste qu'on parcour
        foreach($instance_module->GetDependanceServer() as $dependence_name => $dependence_values)
        {
	    // ON affiche un message d'information indiquant que la d�pendence se charge
            echo "[INFO] dependance '".$dependence_name."'\n";
	    
	    // Si la d�pendence n'existe pas alors...
            if(!ApplicationLIS::IssetModule($dependence_name))
            {
		// On g�n�re une erreur
                trigger_error("module ".$module." dependence ".$dependence_name." non satisfaite !",E_USER_NOTICE);
                return null;
            }
            elseif(ApplicationLIS::GetModule($dependence_name)->GetVersion()!=$dependence_values["Version"])
            {
		// Si la d�pendence existe mais que la version de la d�pendance exig�e n'est pas celle de la d�pendence existance
		// Alors on g�n�re une erreur
                trigger_error("module ".$module." dependence ".$dependence_name." non statisfaite pour la version demand�e!",E_USER_NOTICE);
                return null;
            }
        }
        
	/*
	 On v�rife que le module est bien un module lis
	 gr�ce �
	 - la nomenclature du nom de la classe
	 - L'h�ritage de la classe ModuleBase
	 - L'implementation de l'interface IModuleBase
	*/
        if(strpos(get_class($instance_module),"module_")===false && $instance_module instanceof IModuleBase && $instance_module instanceof ModuleBase )
        {
	    // On g�nere une erreur pour dire que le moduel n'est pas compatible
            trigger_error("module (".$name_module.") not module compatible !",E_USER_NOTICE);
            return null;
        }
        
        // On retourne l'instance du module
        return ApplicationLIS::$modules[$instance_module->GetModuleName()]=$instance_module;
    }
    else
    {
	// Sinon on g�nere une erreur pour dire que le module n'est pas disponible
        trigger_error("module ".$module." not aviable !",E_USER_NOTICE);
        return null;
    }
    
}

/*
  Supprime un module a chaud
  @access public
  @param string $type nom du module
  @todo remplacer $type par $module
*/
public function RemoveModule($type)
{
    // Penser a v�rifie que le parametre est bien un objet et une instance de rendu
    unset($this->modules[$type]);
}

/*
  Interchange un module a chaud
  @access public
  @param string $type nom de l'ancien module
  @param string $module nom du nouveau module
  @todo remplacer $type par $old_module et $module par $new_module
*/
public function SwitchModule($type,$module)
{
    // On supprime l'ancien module
    $this->RemoveModule($type);
    
    // Puis on ajoute  le nouveau module
    $this->AddModule($type,$module);
}
  
/*
  Importe le css pass� en param�tre dans la propri�t� css
  @access public
  @param string $file nom du fichier a charger depuis themes/__nom__theme__/__nom_fichier__
*/
public function ImportCss($file)
{
    $css = new CssParseur();
    $t=$css->Parse("theme/".$this->theme."/css/".$file);
    var_dump($t);
    unset($t);
}

/*
 Envoie des donn�es json au client
 @access public
 @param Array $msg donn�e � envoy�e
*/
public function send($msg){
	// Qui se charge de dispatcher les différentes ressource dans ce qu'il recoie
	//$this->recv_evenementiel();
	
	// On envoie les don�e au client sous forme json
	$msg=json_encode($msg)."\r\n";
	socket_write($this->instance,$msg,strlen($msg));
}

/*
 Permet de simuler un timeout de socket
 @param Socket Ressource de la socket
 @return boolean true si la socket a toujour des don�e sinon false
*/
public function is_socket($socket)
{
	// On stocke la socket dans un tableau
	$socks = array($socket);
	$TMP=array();
	
	// On appele la selection de la socket 
	socket_select($socks,$TMP,$TMP,0,0);

	// Et on retourne true si la socket est tjr dans le tableau de retour $TMP
	if(in_array($socket, $socks)) {
		return true;
	} else { return false;
	}
}

/*
 Permet de recevoir des don�e mon non utilis� visiblement ici
 @access public
 @param integer Taille de r�ceptione des donn�e par d�faut 2048
 @return boolean retour false ou OK
 @todo termin� ou supprim� cette m�thode
*/
public function recv($taille=2048)
{
	// On encoregistre le chrono au timestamp
	$chrono=time();
	 
	while(1)
	{
		// On parcour les don�e du tableau de retour $returns
		foreach($this->returns as $cle => $return)
		{
			// Si l'id du retour est �gale a "0"
			if($return["id"] == 0)
			{
				// Alors on retourne ok
				$findme = $return;
				return "OK";
			}
		}
		 
		/*
		   Si le temp pass� depuis le d�but de l'�xecution de la fonction
		   D�passe le temp accord� par thread de la propri�t� time_thread
		   Alors on finit la boucle et on affiche un message d'information
		*/
		if((time()-$chrono)>$this->time_thread) {
			echo "Temp thread depasse\n"; break;
		}
	}

	return false;
}

/*
 Est appeler par les ticks � interval r�gulier pour appeler les methode des event recu
 @access public
*/
public function handle_event()
{
    	// On encoregistre le chrono au timestamp
	$chrono=time();
	 
	 // On parcour le tableau des �venement recu
	foreach($this->events as $cle => $event)
	{
		$findme = $event;
		
		// On supprime l'evenement de ce tableau
		unset($this->events[$cle]);
		
		// On appele l'handle attach� a l'evenement en question
		call_user_func_array(array(ApplicationLIS::GetModule($findme["Module"]), $findme["Method"]),$findme["Params"]);
		 
		/*
		   Si le temp pass� depuis le d�but de l'�xecution de la fonction
		   D�passe le temp accord� par thread de la propri�t� time_thread
		   Alors on finit la boucle et on affiche un message d'information
		*/
		if((time()-$chrono)>$this->time_thread) {
			// On affiche que le temp autoris� par thread est d�psas�
			echo "Temp thread depasse\n"; break;
		}
	}
}

/*
 Est appeler par les ticks � interval r�gulier pour a stocker les events dans un tableau a traiter plus tard
 @access public
*/
public function handle_recv()
{
	$taille = 2048;
	
	// On encoregistre le chrono au timestamp
	$chrono=time();
	
	// On utilise le simulateur de timeout pour savoir quand il n'y a plus de don�e � tratier
	while($this->is_socket($this->instance))
	{
		// On lit les don�e		 
		$data=socket_read($this->instance,$taille,PHP_NORMAL_READ);
		 
		//echo "Data : ".$data."\n";
		
		// On transforme les don�e json en tableau
		$data = json_decode($data,true);
		//echo print_r($data,true);
		
		// On analyse la propri�t� action de ce tableau
		switch($data["Action"])
		{
			// Si elle est �gale a retour
			case "Retour":
				// On stocke le retour dans le tableau des retours
				$this->returns[] = "OK";
			break;
			
			// Si elle est �gale a evenemetn
			case "Evenement":
				if(empty($data["Params"]))
				{
					$data["Params"]=array();
				}
				// var_dump($data["Params"]);
				
				// On stocke l'�venement dans le tableau d'�venement	
				$this->events[] = $data;

				// On affiche un message d'information indiquant que c'est un �venemtn
				echo "C'est un evenement\n";
			reak;
			
			// Sinon
			default:
				// On affiche un message d'information pour dire que c'est une action de type inconu
				echo "C'est un inconu\n";
				return false;
			break;
			 
		}
		 
		/*
		   Si le temp pass� depuis le d�but de l'�xecution de la fonction
		   D�passe le temp accord� par thread de la propri�t� time_thread
		   Alors on finit la boucle et on affiche un message d'information
		*/
		if((time()-$chrono)>$this->time_thread) {
			// On affiche que le temp autoris� par thread est d�psas�
			echo "Temp thread depasse\n"; break;
		}
		 
	}
	 
	if (!empty($retour))
	{
		return $retour;
	}
	else
	{
		return false;
	}
}

/*
 Permet de se deconecter du client
 @access public
*/
public function disconnect(){
    
	// On ferme la socket de connection cliente
	socket_close($this->instance);
}


  
}



?>