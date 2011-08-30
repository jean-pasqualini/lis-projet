<?php
/*
 Mr pasqualini jean | 28/09/2010 
 Version de lis en expérimentation non stable .
 
 Pensez a ajouter la négociation par rapport
 au module du rendu
 a la version du client et du serveur 
*/
Abstract Class ApplicationLIS {
    
    public static $UniqueInstance = null;
    public $instance;
    public static $modules=array();
    private static $css=array(); // Contient les propriété des css importer
    private $theme="default";
    private $time;
    private $time_thread = 5; //exprime en seconde
    private $events = array();
    private $returns = array();
    
  public function __construct($address,$port){
    
    ApplicationLIS::$UniqueInstance = $this;
        
    global $_CONFIGURATION; // Permet d'acceder au donée de config déclarer 
    global $error;
    
    if(file_exists("theme/".$this->theme."/css/application.css")) { $this->ImportCss("application.css"); }
    // IMPORTANT : SUPPRIME LA BARIERE DES 30 SECONDES
    set_time_limit(0);
    // L'envoi implicite signifie que toute fonction qui envoie des données au navigateur verra ses données envoyées immédiatement
    ob_implicit_flush();
    
//On crée la socket
if(($socket = socket_create(AF_INET, SOCK_STREAM, 0)) === false)
        echo 'La création de la socket a échoué : '.socket_strerror($socket)."\n<br />";
        
//On assigne la socket à une adresse et à un port, que l'on va écouter par la suite.
if(($assignation = socket_bind($socket, $address, $port)) < 0)
        echo "L'assignation de la socket a échoué : ".socket_strerror($assignation)."\n<br />";
        
//On prépare l'écoute.
if(($ecoute = socket_listen($socket)) === false)
        echo "L'écoute de la socket a échoué : ".socket_strerror($ecoute)."\n<br />";

          if(($client = socket_accept($socket)) === false)
          {
            echo "Le client n'a pas pu se connecter : ".socket_strerror($client)."\n<br />";
            
            $this->disconnect();
            
            return false;
          }
          
          $this->instance=$client;
              
              /* Ici on charge les modules de rendu (négociation client/server a implementer) */
              
              /* on verifie la configuration des modules principaux */
              if (!count($_CONFIGURATION['MODULE']['audio'])) {  trigger_error("Module de rendu audio non configurée",E_USER_NOTICE); }
              if (!count($_CONFIGURATION['MODULE']['view'])) {  trigger_error("Module de rendu view non configurée",E_USER_NOTICE); }
    
            /* filtre tout les modules de rendus et les integres si disponible */
            foreach($_CONFIGURATION['MODULE'] as $name_module => $liste_rendu)
            {
                foreach ($liste_rendu as $module)
                {
                    // Appel de la méthode add module 
                    if($this->AddModule($module)!=null) { break; }
                }
                
            }
              
            
			declare(ticks=5);
			register_tick_function(array(&$this, 'handle_event'), true);
			register_tick_function(array(&$this, 'handle_recv'), true);  
          
            return true;
}


public static function GetInstance()
{
	if (ApplicationLIS::$UniqueInstance == null) {
		trigger_error("Erreur application non initialiser",E_USER_ERROR); 
		exit();
	}
	return ApplicationLIS::$UniqueInstance;
}

public static function GetCss()
{
    return ApplicationLIS::$css;
}

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



// Ajoute un module a chaud
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
                trigger_error("module ".$module." dependence ".$dependence_name." non statisfaite pour la version demandée!",E_USER_NOTICE);
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

// Supprime un module a chaud
public function RemoveModule($type)
{
    // Penser a vérifie que le parametre est bien un objet et une instance de rendu
    unset($this->modules[$type]);
}

// Interchange un module a chaud
public function SwitchModule($type,$module)
{
    $this->RemoveModule($type);
    $this->AddModule($type,$module);
}
  
/* Importe le css passé en paramètre dans la propriété css */
public function ImportCss($file)
{
    $css = new CssParseur();
    $t=$css->Parse("theme/".$this->theme."/css/".$file);
    var_dump($t);
    unset($t);
}

public function send($msg){
	// Ici comme les ressource envoyÃ© sont de type audio et video en multiplexage
	// On doit typer la ressource elle meme en envoyÃ© les donÃ©e sous format json
	// pour une interpretation native des donÃ©e recus par le module d'interface du client
	// Qui se charge de dispatcher les diffÃ©rentes ressource dans ce qu'il recoie
	//$this->recv_evenementiel();
	$msg=json_encode($msg)."\r\n";
	socket_write($this->instance,$msg,strlen($msg));
}

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

public function disconnect(){
	socket_close($this->instance);
}


  
}



?>