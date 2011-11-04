<?php
/*
 @author Jean pasqualini <jpasqualini75@gmail.com>
 @version InDev
 @license GPL
 Mr pasqualini jean | 28/09/2010 
 Version de lis en exp�rimentation non stable .
 
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
        
    global $_CONFIGURATION; // Permet d'acceder au don�e de config d�clarer 
    global $error;
    
    if(file_exists("theme/".$this->theme."/css/application.css")) { $this->ImportCss("application.css"); }
    // IMPORTANT : SUPPRIME LA BARIERE DES 30 SECONDES
    set_time_limit(0);
    // L'envoi implicite signifie que toute fonction qui envoie des donn�es au navigateur verra ses donn�es envoy�es imm�diatement
    ob_implicit_flush();
    
//On cr�e la socket
if(($socket = socket_create(AF_INET, SOCK_STREAM, 0)) === false)
        echo 'La cr�ation de la socket a �chou� : '.socket_strerror($socket)."\n<br />";
        
//On assigne la socket � une adresse et � un port, que l'on va �couter par la suite.
if(($assignation = socket_bind($socket, $address, $port)) < 0)
        echo "L'assignation de la socket a �chou� : ".socket_strerror($assignation)."\n<br />";
        
//On pr�pare l'�coute.
if(($ecoute = socket_listen($socket)) === false)
        echo "L'�coute de la socket a �chou� : ".socket_strerror($ecoute)."\n<br />";

          if(($client = socket_accept($socket)) === false)
          {
            echo "Le client n'a pas pu se connecter : ".socket_strerror($client)."\n<br />";
            
            $this->disconnect();
            
            return false;
          }
          
          $this->instance=$client;
              
              /* Ici on charge les modules de rendu (n�gociation client/server a implementer) */
              
              /* on verifie la configuration des modules principaux */
              if (!count($_CONFIGURATION['MODULE']['audio'])) {  trigger_error("Module de rendu audio non configur�e",E_USER_NOTICE); }
              if (!count($_CONFIGURATION['MODULE']['view'])) {  trigger_error("Module de rendu view non configur�e",E_USER_NOTICE); }
    
            /* filtre tout les modules de rendus et les integres si disponible */
            foreach($_CONFIGURATION['MODULE'] as $name_module => $liste_rendu)
            {
                foreach ($liste_rendu as $module)
                {
                    // Appel de la m�thode add module 
                    if($this->AddModule($module)!=null) { break; }
                }
                
            }
              
            
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
	if (ApplicationLIS::$UniqueInstance == null) {
		trigger_error("Erreur application non initialiser",E_USER_ERROR); 
		exit();
	}
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
	echo "[INFO] demande du monde '".$name_module."'\n";
	
    if(isset(ApplicationLIS::$modules[$name_module]))
    {
        return ApplicationLIS::$modules[$name_module];
    }
    else
    {
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
    if(empty(ApplicationLIS::$modules[$name_module]))
    {
        return false;
    }
    elseif(!is_object(ApplicationLIS::$modules[$name_module]))
    {
        echo "\n !!!! ".var_dump(ApplicationLIS::$modules[$name_module])." !!!! \n";
        return false;
    }
    else
    {
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
    foreach (ApplicationLIS::$modules as $propriete => $value)
    {
      if(is_object($value))
      {
        if (method_exists ($value,$name))
        {
            return call_user_func_array(array(ApplicationLIS::$modules[$propriete], $name), $arguments);
        }
      }
    }
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
    if(file_exists("module/".$module.".php"))
    {
    	echo "[INFO] chargement du module '".$module."'\n";
        include("module/".$module.".php");
        $render="module_".$module;
        $instance_module=new $render($this->instance,$this);
        foreach($instance_module->GetDependanceServer() as $dependence_name => $dependence_values)
        {
            echo "[INFO] dependance '".$dependence_name."'\n";
            if(!ApplicationLIS::IssetModule($dependence_name))
            {
                trigger_error("module ".$module." dependence ".$dependence_name." non satisfaite !",E_USER_NOTICE);
                return null;
            }
            elseif(ApplicationLIS::GetModule($dependence_name)->GetVersion()!=$dependence_values["Version"])
            {
                trigger_error("module ".$module." dependence ".$dependence_name." non statisfaite pour la version demand�e!",E_USER_NOTICE);
                return null;
            }
        }
        
        if(strpos(get_class($instance_module),"module_")===false)
        {
            trigger_error("module (".$name_module.") not module compatible !",E_USER_NOTICE);
            return null;
        }
        
        // On retourne l'instance du module
        return ApplicationLIS::$modules[$instance_module->GetModuleName()]=$instance_module;
    }
    else
    {
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
    $this->RemoveModule($type);
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
 Envoie des don�e brute au client
 @access public
 @param $msg message a envoy�
*/
public function send($msg){
	// Ici comme les ressource envoyé sont de type audio et video en multiplexage
	// On doit typer la ressource elle meme en envoyé les donée sous format json
	// pour une interpretation native des donée recus par le module d'interface du client
	// Qui se charge de dispatcher les différentes ressource dans ce qu'il recoie
	//$this->recv_evenementiel();
	$msg=json_encode($msg)."\r\n";
	socket_write($this->instance,$msg,strlen($msg));
}

/*
 Permet de simuler un timeout de socket
 @param Array Tableau de socket
 @return boolean true si la socket a toujour des don�e sinon false
*/
public function is_socket($socket)
{
	$socks = array($socket);
	$TMP=array();
	socket_select($socks,$TMP,$TMP,0,0);

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
	$chrono=time();
	 
	while(1)
	{
		foreach($this->returns as $cle => $return)
		{
			if($return["id"] == 0)
			{
				$findme = $return;
				return "OK";
			}
		}
		 
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
	$chrono=time();
	 
	foreach($this->events as $cle => $event)
	{
		$findme = $event;
		unset($this->events[$cle]);
		call_user_func_array(array(ApplicationLIS::GetModule($findme["Module"]), $findme["Method"]),$findme["Params"]);
		 
		if((time()-$chrono)>$this->time_thread) {
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
	$chrono=time();
	while($this->is_socket($this->instance))
	{
		 
		$data=socket_read($this->instance,$taille,PHP_NORMAL_READ);
		 
		//echo "Data : ".$data."\n";
		$data = json_decode($data,true);
		//echo print_r($data,true);
		switch($data["Action"])
		{
			case "Retour":
				$this->returns[] = "OK";
				break;
				 
			case "Evenement":
				if(empty($data["Params"]))
				{
					$data["Params"]=array();
				}
				// var_dump($data["Params"]);
					
				$this->events[] = $data;

					
				echo "C'est un evenement\n";
				break;
				 
			default:
				echo "C'est un inconu\n";
			return false;
			break;
			 
		}
		 
		// Si le temp par thread est depasser alors on continue
		if((time()-$chrono)>$this->time_thread) {
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
	socket_close($this->instance);
}


  
}



?>