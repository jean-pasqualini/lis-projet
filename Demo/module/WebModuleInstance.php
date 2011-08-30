<?php 
class module_WebModuleInstance extends ModuleBase implements IModuleBase {
	private $socket;

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

	public function GetModuleDescription()
	{
		return "rien";
	}
	
	public function GetVersion()
	{
		return "1.0";
	}
	
	public function GetDependanceServer()
	{
		return array(
		          "WebModule"  => array("Version" => "1.0"  , "Url" => "")
		);
	}
	public function GetDependanceClient()
	{
		return array();
	}
	
	public function AddModule()
	{

	}

	private function disconnect()
	{
		socket_close($this->socket);
	}

	public function __set($name,$value)
	{

	}

	public function __get($name)
	{

	}

	public function __isset($name)
	{

	}

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