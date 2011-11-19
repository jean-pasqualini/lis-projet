<?php
/**
  * Ce module est le faux module faisant interfae de webmodule pour utiliser un WebModule
  * Comme un module et cela de faéon transparente il est initialisé par WebModule et ne doit pas l'étre par l'application elle méme
  * @author Jean pasqualini <jpasqualini75@gmail.com>
  * @license GPL
  * @version InDev
  * @package ModuleLis
*/
class module_WebModuleInstance extends ModuleBase implements IModuleBase {
	
	/**
	 * @access private
	 * @var Socket Resource de la socket de connection du module distant
	*/
	private $socket;

	/**
	 * Constructeur du module WebModuleInstance
	 * @access public
	 * @return module_WebModuleInstance Retourne l'instance du module
	*/
	public function __construct()
	{
		echo "AddModule";
		//php ".$name.".php"
		$cmd = "start G:/wamp/bin/php/php5.3.4/php canvas.php";
		$cmd = "dir";
		echo $cmd;
		exec($cmd,$outputs);

		foreach($outputs as $output)
		{
			echo $output."<BR>";
		}
		$this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die('Création de socket refusée');
		socket_connect($this->socket,"127.0.0.1",81) or die('Connexion impossible');
	}

	/**
	 * Récupere la description du module
	 * @access public
	 * @return string La description du module
	*/
	public function GetModuleDescription()
	{
		return "rien";
	}
	
	/**
	 * Récupere la version du module
	 * @access public
	 * @return string La version du module
	*/
	public function GetVersion()
	{
		return "1.0";
	}
	
	/**
	 * Récupere les dépendances module serveur
	 * @access public
	 * @return Array Les dépendances serveur
	*/
	public function GetDependanceServer()
	{
		return array(
		          "WebModule"  => array("Version" => "1.0"  , "Url" => "")
		);
	}
	
	/**
	 * Récupere les dépendances module client
	 * @access public
	 * @return Array Les dépendances client
	*/
	public function GetDependanceClient()
	{
		return array();
	}
	
	/**
	 * Permet d'ajoute un module
	 * @access public
	*/
	public function AddModule()
	{

	}
	
	/**
	 * Permet de se déconnecter du module distant
	 * @access public
	*/
	private function disconnect()
	{
		socket_close($this->socket);
	}

	/**
	 * @access public
	 * Méthode magique permettant de modifier les variable du module distant de facon transparente
	*/
	public function __set($name,$value)
	{

	}

	/**
	 * @access public
	 * Méthode magique permettant de lire les variable du module distant de facon transparente
	*/
	public function __get($name)
	{

	}

	/**
	 * @access public
	 * Méthode magique permettant de vérifié l'existance d'une variable du module distant de facon transparente
	*/
	public function __isset($name)
	{

	}

	/**
	 * @access public
	 * Méthode magique permettant d'appeler une méthode du module distant de facon transparente
	*/
	public function __call($method,$arguments)
	{
		$data = array("method" => $method,
		                      "arguments" => $arguments);
		socket_write($this->socket,json_encode($data)."\n");
		$retour=json_decode(socket_read($this->socket,2048),true);
		return $retour["Retour"];
	}
}
?>