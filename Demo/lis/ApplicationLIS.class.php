<?php
/**
 * Clase principale du framework 
 * @author Jean pasqualini <jpasqualini75@gmail.com>
 * @version InDev
 * @license GPL
 * @todo Négociation de module avec le client é faire
 * @todo Implémenter la gestion des erreurs en exception
 * @package FrameworkLis
*/
Abstract Class ApplicationLIS {
    
    /**
     * @access public
     * @static
     * @staticvar ApplicationLIS Contient la single instance (unique) de l'application
    */
    public static $UniqueInstance = null;
    
    /**
     * @access public
     * @var  Socket Contient la connection socket
    */
    public $instance;
    
    /**
     * @access public
     * @static
     * @staticvar Array Contients les instances des modules chargée
    */
    public static $modules=array();
    
    /**
     * @access private
     * @static
     * @staticvar Array Contient les propriété css importer
    */
    private static $css=array(); // Contient les propriété des css importer
    
    /**
     * @access private
     * @var string Contient le théme courant
    */
    private $theme="default";
    
    /**
     * @access private
     * @var integer Contient le temp
    */
    private $time;
    
    /**
     * @access private
     * @var integer contient le temp en seconde autoriser par thread
    */
    private $time_thread = 5; //exprime en seconde
    
    /**
     * @access private
     * @var Array Contient les commande évenementiel recu en attente de lecture
    */
    private $events = array();
    
    /**
     * @access private
     * @var Array Contient les retours recu en attente de lecture
    */
    private $returns = array();
    
  /**
   * Le constructeur de l'application initialise l'appplication
   * @access public
   * @param string $address L'adresse d'attente d'un client de l'application
   * @param integer $port Le port d'attente d'un client de l'appplication
   * @return ApplicationLIS Retourne l'instance courante de l'application
  */
  public function __construct($address,$port){
		
    ApplicationLIS::$UniqueInstance = $this;
        
    global $_CONFIGURATION; // Permet d'acceder de facon global au donnée de configuration déclarer 
    global $error; // Permer d'accéder de facon globale au tableau d'erreurs $error
    
    //On importe le css globale de l'application si celui-ci existe
    if(file_exists("theme/".$this->theme."/css/application.css")) { $this->ImportCss("application.css"); }
   
    // IMPORTANT : SUPPRIME LA BARIERE DES 30 SECONDES
    set_time_limit(0);
      
    // L'envoi implicite signifie que toute fonction qui envoie des données au navigateur verra ses données envoyées immédiatement
    ob_implicit_flush();
    
    //On crée la socket
    if(($socket = socket_create(AF_INET, SOCK_STREAM, 0)) === false)
    {
	    // Si elle ne se charge pas alors on affiche une erreur indiquant que la socket n'a pas pu étre crée
	    throw SocketException($socket, 'La création de la socket a échoué : '.socket_strerror($socket)."\n<br />");
    }
        
    //On assigne la socket é une adresse et é un port, que l'on va écouter par la suite.
    if(($assignation = socket_bind($socket, $address, $port)) < 0)
    {
	    // Si elle ne s'assigne pas alors on affiche une erreur indiquant que l'assignation é échoué
	    throw SocketException($socket, "L'assignation de la socket a échoué : ".socket_strerror($assignation)."\n<br />");
    }
        
    //On prépare l'écoute.
    if(($ecoute = socket_listen($socket)) !== false)
    {
	    //throw SocketException($socket, "L'écoute de la socket a échoué : ".socket_strerror($ecoute)."\n<br />");
	  
	  // Si le client n'a pas pu se connecter alors...
          if(($client = socket_accept($socket)) === false)
          {
	    // On affiche un message d'information pour dire que le client n'a pas pu se connecter
            throw SocketException($client, "Le client n'a pas pu se connecter : ".socket_strerror($client)."\n<br />");
            
            $this->disconnect();
            
            return false;
          }
          
	  // On strocke la ressource socket cliente dans la propriété $instance
          $this->instance=new SocketClient($client);
              
			
			  
          // Ici charge les modules de rendu (négociation client/server a implementer) 
              
          // On verifie la configuration des modules principaux pour l'audio et l'affichage et si ceux-ci ne se chargent pas alors on génére une erreur
          if (!count($_CONFIGURATION['MODULE']['audio'])) {  throw new LisException("Module de rendu audio non configurée"); }
          if (!count($_CONFIGURATION['MODULE']['view'])) {  throw new LisException("Module de rendu view non configurée"); }
    
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
              
            /**
	      On declare que les handle de reception de traitement évenementiel
	      seront appelée tous 5 appel d'instruction php (tick)
	      
	      Cela permet de faire une sorte de setInterval pour ceux qui connaisse javascript
	      Cette fonction est désormait depreaced et c'est bien domage
	    */
	    	    
	    declare(ticks=5);
	    register_tick_function(array(&$this, 'handle_event'), true);
	    register_tick_function(array(&$this, 'handle_recv'), true);  
          
            return true;
	}
	else 
	{
echo PHP_EOL."@@@@".gettype($socket)."@@@@@".PHP_EOL;
		throw new SocketException($socket, "Aucune client ne s'est rattaché à l'application");
	}
}

/**
 * Retourne l'instance unique de l'application 
 * @access public
 * @static
 * @return ApplicationLis Retourne l'instance unique de l'application courante
*/
public static function GetInstance()
{
	// Si l'application n'a pas encore été instancie alors...
	if (ApplicationLIS::$UniqueInstance === null) {
		/**
		 On genere une erreur pour dire que l'application n'est pas initialiser
		 et donc qu'on ne peut pas retourner l'instance
		*/

		//return null;
		throw new LisException("application non initialiser"); 
	}
	// Sinon on retourne l'instance
	return ApplicationLIS::$UniqueInstance;
}

public static function AppAutoLoad($classname)
{
	if(file_exists($classname.".application.class.php"))
	{
		require_once($classname.".application.class.php");
	}
}

/**
 * Retourne les propriété css déclaré de l'application
 * @access public
 * @static
 * @return Array Retourne les propriété css déclaré de l'application
*/
public static function GetCss()
{
    return ApplicationLIS::$css;
}

/**
 * Retourne l'instance d'un module charge
 * @access public
 * @static
 * @param string $name_module Nom du module
 * @return IModuleBase Retourne l'instance du module
*/
public static function GetModule($name_module)
{
    // On affiche un message d'information pour dire que le module est demadnée
    echo "[INFO] demande du module '".$name_module."'\n";
	
    // Si le module existe alors
    if(isset(ApplicationLIS::$modules[$name_module]))
    {
      echo $name_module." => ".get_class(ApplicationLIS::$modules[$name_module]).PHP_EOL;
	// On le retourne
        return ApplicationLIS::$modules[$name_module];
    }
    else
    {
	// Sinon on génére une erreur pour dire que le module requis n'est pas chargée
        throw new ModuleNotLoadedException($name_module, null, "Le module requis (".$name_module.") n'est pas charge actuellement !!!");
    }
}

/**
 * Retourne true si le module existe sinon false
 * @access public
 * @static
 * @param string $name_module Nom du module
 * @return boolean Retourne true si le module existe sinon false
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
	// S'il existe masi qu'il n'est pas un objet alors on génére une erreur
        echo "\n !!!! ".var_dump(ApplicationLIS::$modules[$name_module])." !!!! \n";
        return false;
    }
    else
    {
	// S'il existe et est un objet alors on retourne true pour dire que la condition est vrai
        return true;
    }
}

/**
 * Permet de simplifier le travail des devellopeur d'application
 * Si besoin permet d'appeler les methode d'un module sans connaitre son nom
 * ex : $this->view->UpdateMouse(); devient $this->UpdateMouse();
 * <B>Attention</B> : diminue forcement les performance globale de l'application
 * @access public
 * @param string $name Nom de la méthode
 * @param Array Liste des paramétre
 * @return inconu Retourne false si la méthode n'a pas été trouver sinon retourne le retour de la méthode
*/
public function __call($name,$arguments)
{
    // On parcours tous les modules
    foreach (ApplicationLIS::$modules as $propriete => $value)
    {
      // Si le module est un objet
      if(is_object($value))
      {
	// et que la methode passé en argument $name existe dans se module
        if (method_exists ($value,$name))
        {
	    // Alors on retourne le retour de l'appel de la méthode du module avec pour paramétre $arguments
            return call_user_func_array(array(ApplicationLIS::$modules[$propriete], $name), $arguments);
        }
      }
    }
    // Sinon on retourne false 
    return false;
}

/**
 * Récupere les noms des modules chargées par l'application
 * @access public
 * @return Array les noms des modules chargées par l'application
 */
public static function getNameModules()
{
	return array_keys(ApplicationLIS::$modules);	
}

/**
 * Récupére les instances de modules chargées par l'application
 * @access public
 * @return Array les instances de modules chargées par l'applicaiton
 */
public static function getModules()
{
	return ApplicationLIS::$modules;
}

/**
 * Récupere l'ip de l'applicaiton 
 * @access public
 * @return string L'ip de l'application
 */
public function getIp()
{
	return "0.0.0.0";	
}

/**
 * Récupere le port de l'application
 * @access public
 * @return int Le port de l'application
 */
public function getPort()
{
	return "0";	
}

/**
 * Définit la ressource socket client
 * @access public
 * @param Socket Ressour ce la socket
 */
public function setSocket($value)
{
	$this->instance = $value;
}


/**
  * Ajoute un module é chaud
  * Les modules se trouvent dans le dossier module et
  * ont pour nom  '___nommodule___.php' et pour nom de classe 'module__nommodule'
  * @access public
  * @param string $module Nom du module a charger 
  * @param Array $parametre Liste des paramétre é passer au contructeur du module
  * @return ModuleBase Retourne l'instance du module ou null si le module n'a pas pu étre charger
*/  
public function AddModule($module,$parametre = array())
{
    // Si le fichier du moduel existe
    if(file_exists("module/".$module.".php"))
    {
	// On affiche un message d'informtation pour indique que le module ce charge
    	echo "[INFO] chargement du module '".$module."'\n";
	
	// Alors on l'inclu
        require_once("module/".$module.".php");
	
	// On instancie la classe du module 
        $render="module_".$module;
        $instance_module=new $render($this->instance,$this);
	
	// On interoge le module sur ces dependances serveur qui nous renvoie une liste qu'on parcour
        foreach($instance_module->GetDependanceServer() as $dependence_name => $dependence_values)
        {
	    // ON affiche un message d'information indiquant que la dépendence se charge
            echo "[INFO] dependance '".$dependence_name."'\n";
	    
	    // Si la dépendence n'existe pas alors...
            if(!ApplicationLIS::IssetModule($dependence_name))
            {
		// On génére une erreur
                throw new ModuleNotLoadedException($module, $instance_module, "module ".$module." dependence ".$dependence_name." non satisfaite !");
                return null;
            }
            elseif(ApplicationLIS::GetModule($dependence_name)->GetVersion()!=$dependence_values["Version"])
            {
		// Si la dépendence existe mais que la version de la dépendance exigée n'est pas celle de la dépendence existance
		// Alors on génére une erreur
                throw new ModuleNotLoadedException($module, $instance_module, "module ".$module." dependence ".$dependence_name." non statisfaite pour la version demandée!");
                return null;
            }
        }
        
	/**
	 On vérife que le module est bien un module lis
	 gréce é
	 - la nomenclature du nom de la classe
	 - L'héritage de la classe ModuleBase
	 - L'implementation de l'interface IModuleBase
	*/
        if(strpos(get_class($instance_module),"module_")===false && $instance_module instanceof IModuleBase && $instance_module instanceof ModuleBase )
        {
	    // On génere une erreur pour dire que le moduel n'est pas compatible
            throw new ModuleNotLoadedException($module, null, "module (".$name_module.") not module compatible !");
            return null;
        }
        
        // On retourne l'instance du module
        return ApplicationLIS::$modules[$instance_module->GetModuleName()]=$instance_module;
    }
    else
    {
	// Sinon on génere une erreur pour dire que le module n'est pas disponible
        throw new ModuleNotLoadedException($module, null, "module ".$module." not aviable !");
        return null;
    }
    
}

/**
  * Supprime un module a chaud
  * @access public
  * @param string $type nom du module
  * @todo remplacer $type par $module
*/
public function RemoveModule($type)
{
    // Penser a vérifie que le parametre est bien un objet et une instance de rendu
    unset($this->modules[$type]);
}

/**
  * Interchange un module a chaud
  * @access public
  * @param string $type nom de l'ancien module
  * @param string $module nom du nouveau module
  * @todo remplacer $type par $old_module et $module par $new_module
*/
public function SwitchModule($type,$module)
{
    // On supprime l'ancien module
    $this->RemoveModule($type);
    
    // Puis on ajoute  le nouveau module
    $this->AddModule($type,$module);
}
  
/**
  * Importe le css passé en paramétre dans la propriété css
  * @access public
  * @param string $file nom du fichier a charger depuis themes/__nom__theme__/__nom_fichier__
*/
public function ImportCss($file)
{
    $css = new CssParseur();
    $t=$css->Parse("theme/".$this->theme."/css/".$file);
    var_dump($t);
    unset($t);
}

/**
 * Envoie des données json au client
 * @access public
 * @param Array $msg donnée é envoyée
*/
public function send($msg){
	// Qui se charge de dispatcher les différentes ressource dans ce qu'il recoie
	//$this->recv_evenementiel();
	// On envoie les donée au client sous forme json
	$msg=json_encode($msg)."\r\n";
	$this->instance->write($msg);
}

/**
 * Permet de recevoir des donée mon non utilisé visiblement ici
 * @access public
 * @param integer Taille de réceptione des donnée par défaut 2048
 * @return boolean retour false ou OK
 * @todo terminé ou supprimé cette méthode
*/
public function recv($taille=2048)
{
	// On encoregistre le chrono au timestamp
	$chrono=time();
	 
	while(1)
	{
		// On parcour les donée du tableau de retour $returns
		foreach($this->returns as $cle => $return)
		{
			// Si l'id du retour est égale a "0"
			if($return["id"] == 0)
			{
				// Alors on retourne ok
				$findme = $return;
				return "OK";
			}
		}
		 
		/**
		   Si le temp passé depuis le début de l'éxecution de la fonction
		   Dépasse le temp accordé par thread de la propriété time_thread
		   Alors on finit la boucle et on affiche un message d'information
		*/
		if((time()-$chrono)>$this->time_thread) {
			echo "Temp thread depasse\n"; break;
		}
	}

	return false;
}

/**
 * Est appeler par les ticks é interval régulier pour appeler les methode des event recu
 * @access public
*/
public function handle_event()
{
    	// On encoregistre le chrono au timestamp
	$chrono=time();
	 
	 // On parcour le tableau des évenement recu
	foreach($this->events as $cle => $event)
	{
		$findme = $event;
		
		// On supprime l'evenement de ce tableau
		unset($this->events[$cle]);
		
		// On appele l'handle attaché a l'evenement en question
		call_user_func_array(array(ApplicationLIS::GetModule($findme["Module"]), $findme["Method"]),$findme["Params"]);
		 
		/**
		   Si le temp passé depuis le début de l'éxecution de la fonction
		   Dépasse le temp accordé par thread de la propriété time_thread
		   Alors on finit la boucle et on affiche un message d'information
		*/
		if((time()-$chrono)>$this->time_thread) {
			// On affiche que le temp autorisé par thread est dépsasé
			echo "Temp thread depasse\n"; break;
		}
	}
}

/**
 * Est appeler par les ticks é interval régulier pour a stocker les events dans un tableau a traiter plus tard
 * @access public
*/
public function handle_recv()
{
	$taille = 2048;
	
	// On encoregistre le chrono au timestamp
	$chrono=time();
	
	// On utilise le simulateur de timeout pour savoir quand il n'y a plus de donée é tratier
	while($this->instance->isSocket())
	{
		// On lit les donée		 
		$data=$this->instance->read();

    if(empty($data))
    {
      return;
    }
		 
		//echo "Data : '".$data."'\n";
		
		// On transforme les donée json en tableau
		$data = json_decode($data,true);
		//echo print_r($data,true);
		
		// On analyse la propriété action de ce tableau
		switch($data["Action"])
		{
			// Si elle est égale a retour
			case "Retour":
				// On stocke le retour dans le tableau des retours
				$this->returns[] = "OK";
			break;
			
			// Si elle est égale a evenemetn
			case "Evenement":
				if(empty($data["Params"]))
				{
					$data["Params"]=array();
				}
				// var_dump($data["Params"]);
				
				// On stocke l'évenement dans le tableau d'évenement	
				$this->events[] = $data;

				// On affiche un message d'information indiquant que c'est un évenemtn
				echo "C'est un evenement\n";
			break;
			
			// Sinon
			default:
				// On affiche un message d'information pour dire que c'est une action de type inconu
				echo "C'est un inconu\n";
				return false;
			break;
			 
		}
		 
		/**
		   Si le temp passé depuis le début de l'éxecution de la fonction
		   Dépasse le temp accordé par thread de la propriété time_thread
		   Alors on finit la boucle et on affiche un message d'information
		*/
		if((time()-$chrono)>$this->time_thread) {
			// On affiche que le temp autorisé par thread est dépsasé
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

/**
 * Permet de se deconecter du client
 * @access public
*/
public function disconnect(){
    
	// On ferme la socket de connection cliente
	$this->instance->close();
}


  
}



?>
