<?php
/*
  Ce module est le faux module faisant interfae de webmodule pour utiliser un WebModule
  Comme un module et cela de fa�on transparente il est initialis� par WebModule et ne doit pas l'�tre par l'application elle m�me
  
  @author Jean pasqualini <jpasqualini@live.fr>
  @license GPL
  @version InDev
*/
class module_WebModuleInstance extends ModuleBase implements IModuleBase {
	
	/*
	 @access private
	 @var Socket Resource de la socket de connection du module distant
	*/
	private $socket;

	/*
	 Constructeur du module WebModuleInstance
	 @access public
	 @return module_WebModuleInstance Retourne l'instance du module
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
		$this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die('Cr�ation de socket refus�e');
		socket_connect($this->socket,"127.0.0.1",81) or die('Connexion impossible');
	}

	/*
	 R�cupere la description du module
	 @access public
	 @return string La description du module
	*/
	public function GetModuleDescription()
	{
		return "rien";
	}
	
	/*
	 R�cupere la version du module
	 @access public
	 @return string La version du module
	*/
	public function GetVersion()
	{
		return "1.0";
	}
	
	/*
	 R�cupere les d�pendances module serveur
	 @access public
	 @return Array Les d�pendances serveur
	*/
	public function GetDependanceServer()
	{
		return array(
		          "WebModule"  => array("Version" => "1.0"  , "Url" => "")
		);
	}
	
	/*
	 R�cupere les d�pendances module client
	 @access public
	 @return Array Les d�pendances client
	*/
	public function GetDependanceClient()
	{
		return array();
	}
	
	/*
	 Permet d'ajoute un module
	 @access public
	*/
	public function AddModule()
	{

	}
	
	/*
	 Permet de se d�connecter du module distant
	 @access public
	*/
	private function disconnect()
	{
		socket_close($this->socket);
	}

	/*
	 @access public
	 M�thode magique permettant de modifier les variable du module distant de facon transparente
	*/
	public function __set($name,$value)
	{

	}

	/*
	 @access public
	 M�thode magique permettant de lire les variable du module distant de facon transparente
	*/
	public function __get($name)
	{

	}

	/*
	 @access public
	 M�thode magique permettant de v�rifi� l'existance d'une variable du module distant de facon transparente
	*/
	public function __isset($name)
	{

	}

	/*
	 @access public
	 M�thode magique permettant d'appeler une m�thode du module distant de facon transparente
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