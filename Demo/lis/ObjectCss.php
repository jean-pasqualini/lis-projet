<?php
class ObjectCss {
	private $NOMS = array();
	private $IDS = array();
	private $CLASSES = array();
	private $PROPRIETES = array();
	
	public function AddId($id)
	{
		$this->IDS[] = $id;
	}
	
	public function AddNom($nom)
	{
		$this->NOMS[] = $nom;
	}
	
	public function AddClasse($classe)
	{
		$this->CLASSES[] = $classe;
	}
	
	public function IssetId($id)
	{
		return isset($this->IDS[$id]);
	}
	
	public function IssetNom($nom)
	{
		return isset($this->NOMS[$nom]);
	}
	
	public function IssetClasse($classe)
	{
		return isset($this->CLASSES[$classe]);
	}
	
	public function GetIds()
	{
		return $this->IDS;
	}
	
	public function GetNoms()
	{
		return $this->NOMS;
	}
	
	public function GetClasses()
	{
		return $this->CLASSES;
	}
	
	public function GetPropriete($name)
	{
		if(isset($this->PROPRIETES[$nom])) return $this->PROPRIETES[$nom];
		throw new Exception("Propriété ou valeur invalide");
	}
	
	public function SetPropriete($nom , $value)
	{
		$this->PROPRIETES[$nom] = $value;
	}
	
	public function GetProprietes()
	{
		return $this->PROPRIETES;
	}
	
	public function IssetPropriete($name)
	{
		return isset($this->PROPRIETES[$nom]);
	}
	
	public function __get($name)
	{
		return $this->GetPropriete($name);
	}
	
	public function __set($name,$value)
	{
		$this->SetPropriete($name, $value);
	}
	
	public function __isset($name)
	{
		return $this->IssetPropriete($name);
	}

}
?>