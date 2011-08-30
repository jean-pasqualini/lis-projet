<?php
/**
 * User class.
 *
 * @author     Mat3910
 */

class User
{
	private $id;
	private $socket;
	private $handshake;

	public function __construct($id)
	{
		$this->setId($id);
	}
	
	private function setId($id)
	{
		$this->id = intval($id);
	}
	
	public function setSocket($socket)
	{
		$this->socket = $socket;
	}
	
	public function setHandshake($handshake)
	{
		$this->handshake = $handshake;
	}
	
	public function getId()
	{
		return $this->id;
	}
	
	public function getSocket()
	{
		return $this->socket;
	}
	
	public function getHandshake()
	{
		return $this->handshake;
	}
}

?>