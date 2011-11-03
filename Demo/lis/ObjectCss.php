<?php
/*
  Cette classe permet de gerer le fichier css en tant qu'obhet 'ObjectCSS'
  @todo Non finit
  @author Jean pasqualini <jpasqualini75@gmail.com>
  @version InDev
  @license GPL
*/
class ObjectCss {
	
	/*
	 @access private
	 @var Array Contient le nom de l'element
	*/
	private $NOMS = array();
	
	/*
	 @access private
	 @var Array Contient l'id de l'�l�ment
	*/
	private $IDS = array();
	
	/*
	 @access private
	 @var Array Contient les classes de l'�l�ment
	*/
	private $CLASSES = array();
	
	/*
	 @access private
	 @var Array Contient les prorpri�t� de l'�lement paire nom : valeur (ex: background: red)
	*/
	private $PROPRIETES = array();
	
	/*
	 Ajoute un id � l'element
	 @access public
	 @param string $id Id de l'�l�ment
	*/	 
	public function AddId($id)
	{
		$this->IDS[] = $id;
	}
	
	/*
	 Ajoute un nom � l'�l�ment
	 @access public
	 @param string $nom Nom de l'�lement
	*/
	public function AddNom($nom)
	{
		$this->NOMS[] = $nom;
	}
	
	/*
	 Ajoute une classe � l'�l�ment
	 @access public
	 @param string $classe Classe � ajouter
	*/
	public function AddClasse($classe)
	{
		$this->CLASSES[] = $classe;
	}
	
	/*
	 V�rifie si l'�l�ment poss�de l'id $id
	 @access public
	 @param �nteger $id Id de l'�l�ment
	 @return boolean retourne true si l'�l�ment poss�de l'id $id sinon false
	*/
	public function IssetId($id)
	{
		return isset($this->IDS[$id]);
	}
	
	/*
	 V�rifie si l'�l�ment poss�de le nom $nom
	 @access public
	 @param string $nom le nom de l'�l�ment
	 @return boolean retourne true si l'�l�ment poss�de le nom $nom sinon false
	*/
	public function IssetNom($nom)
	{
		return isset($this->NOMS[$nom]);
	}
	
	/*
	  V�rifie si l'�l�ment poss�de la classe $classe
	  @access public
	  @param string $classe la classe de l'�l�ment
	  @return boolean retour true si l'�l�ment poss�de la classe $classe sinon false
	*/
	public function IssetClasse($classe)
	{
		return isset($this->CLASSES[$classe]);
	}
	
	/*
	  Retournes les ids de l'�l�ment
	  @access public
	  @return Array Les ids de l'�l�ment
	*/
	public function GetIds()
	{
		return $this->IDS;
	}
	
	/*
	 Retourne les noms de l'�l�ment
	 @access public
	 @return Array Les noms de l'�l�ment
	*/
	public function GetNoms()
	{
		return $this->NOMS;
	}
	
	/*
	 Retoures les classes de l'�l�ment
	 @access public
	 @return Array Les classes de l'�l�ment
	*/
	public function GetClasses()
	{
		return $this->CLASSES;
	}
	
	/*
	 Retourne une propri�t�s de l'�l�ment selont son nom
	 @access public
	 @return string Valeur de la propri�t�
	*/
	public function GetPropriete($name)
	{
		if(isset($this->PROPRIETES[$nom])) return $this->PROPRIETES[$nom]; 
		throw new Exception("Propri�t� ou valeur invalide");
	}
	
	/*
	 Ajoute ou remplace une propri�t� de l'�l�ment
	 @access public
	 @param string $nom La propri�t�
	 @param string $value La valeur
	*/
	public function SetPropriete($nom , $value)
	{
		$this->PROPRIETES[$nom] = $value;
	}
	
	/*
	 Retourne les propri�t�s de l'�l�ment
	 @access public
	 @return Les propri�tes de l'�l�ment
	*/
	public function GetProprietes()
	{
		return $this->PROPRIETES;
	}
	
	/*
	 V�rifie si la propri�t� existe
	 @access public
	 @param string $name Nom de la propri�t�
	 @return boolean Retourne true si la propri�t� existe
	*/
	public function IssetPropriete($name)
	{
		return isset($this->PROPRIETES[$nom]);
	}
	
	/*
	 @access public
	 @property string votre_propriete Nom de la propri�t�
	 @param string $name Nom de la propri�t�
	 @return string valeur de la propri�t�
	*/
	public function __get($name)
	{
		return $this->GetPropriete($name);
	}
	
	/*
	 @access public
	 @property string votre_propriete Nom de la propri�t�
	 @param string $name Nom de la propri�t�
	 @param string value Valeur de la propri�t�
	*/
	public function __set($name,$value)
	{
		$this->SetPropriete($name, $value);
	}
	
	/*
	 @access public
	 @property string Votre_propri�t� Nom de la propri�t�
	 @param string $name Nom de la propri�t�
	 @return boolean Retourne true si la propri�t� existe sinon false
	*/
	public function __isset($name)
	{
		return $this->IssetPropriete($name);
	}

}
?>