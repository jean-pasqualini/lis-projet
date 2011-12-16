<?php
    class SocketClient {
    	private $socket;
		
		public function __construct($socket)
		{
			$this->socket = $socket;
		}
		
		public function write($msg)
		{
			socket_write($this->socket,$msg,strlen($msg));
		}
		
		public function isSocket()
		{
			// On stocke la socket dans un tableau
			$socks = array($this->socket);
			$TMP=array();
			
			// On appele la selection de la socket 
			socket_select($socks,$TMP,$TMP,0,0);
		
			// Et on retourne true si la socket est tjr dans le tableau de retour $TMP
			if(in_array($this->socket, $socks)) {
				return true;
			} else {
				 return false;
			}
		}
		
		public function read($taille = 2048)
		{
			return socket_read($this->socket,$taille,PHP_NORMAL_READ);
		}
		
		public function close()
		{
			socket_close($this->socket);
		}
		
		public function __destruct()
		{
			$this->close();
		}
    }
?>