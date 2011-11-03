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
	 @var Array Contient l'id de l'élément
	*/
	private $IDS = array();
	
	/*
	 @access private
	 @var Array Contient les classes de l'élément
	*/
	private $CLASSES = array();
	
	/*
	 @access private
	 @var Array Contient les prorpriété de l'élement paire nom : valeur (ex: background: red)
	*/
	private $PROPRIETES = array();
	
	/*
	 Ajoute un id à l'element
	 @access public
	 @param string $id Id de l'élément
	*/	 
	public function AddId($id)
	{
		$this->IDS[] = $id;
	}
	
	/*
	 Ajoute un nom à l'élément
	 @access public
	 @param string $nom Nom de l'élement
	*/
	public function AddNom($nom)
	{
		$this->NOMS[] = $nom;
	}
	
	/*
	 Ajoute une classe à l'élément
	 @access public
	 @param string $classe Classe à ajouter
	*/
	public function AddClasse($classe)
	{
		$this->CLASSES[] = $classe;
	}
	
	/*
	 Vérifie si l'élément posséde l'id $id
	 @access public
	 @param înteger $id Id de l'élément
	 @return boolean retourne true si l'élément posséde l'id $id sinon false
	*/
	public function IssetId($id)
	{
		return isset($this->IDS[$id]);
	}
	
	/*
	 Vérifie si l'élément posséde le nom $nom
	 @access public
	 @param string $nom le nom de l'élément
	 @return boolean retourne true si l'élément posséde le nom $nom sinon false
	*/
	public function IssetNom($nom)
	{
		return isset($this->NOMS[$nom]);
	}
	
	/*
	  Vérifie si l'élément posséde la classe $classe
	  @access public
	  @param string $classe la classe de l'élément
	  @return boolean retour true si l'élément posséde la classe $classe sinon false
	*/
	public function IssetClasse($classe)
	{
		return isset($this->CLASSES[$classe]);
	}
	
	/*
	  Retournes les ids de l'élément
	  @access public
	  @return Array Les ids de l'élément
	*/
	public function GetIds()
	{
		return $this->IDS;
	}
	
	/*
	 Retourne les noms de l'élément
	 @access public
	 @return Array Les noms de l'élément
	*/
	public function GetNoms()
	{
		return $this->NOMS;
	}
	
	/*
	 Retoures les classes de l'élément
	 @access public
	 @return Array Les classes de l'élément
	*/
	public function GetClasses()
	{
		return $this->CLASSES;
	}
	
	/*
	 Retourne une propriétés de l'élément selont son nom
	 @access public
	 @return string Valeur de la propriété
	*/
	public function GetPropriete($name)
	{
		if(isset($this->PROPRIETES[$nom])) return $this->PROPRIETES[$nom]; 
		throw new Exception("Propriété ou valeur invalide");
	}
	
	/*
	 Ajoute ou remplace une propriété de l'élément
	 @access public
	 @param string $nom La propriété
	 @param string $value La valeur
	*/
	public function SetPropriete($nom , $value)
	{
		$this->PROPRIETES[$nom] = $value;
	}
	
	/*
	 Retourne les propriétés de l'élément
	 @access public
	 @return Les propriétes de l'élément
	*/
	public function GetProprietes()
	{
		return $this->PROPRIETES;
	}
	
	/*
	 Vérifie si la propriété existe
	 @access public
	 @param string $name Nom de la propriété
	 @return boolean Retourne true si la propriété existe
	*/
	public function IssetPropriete($name)
	{
		return isset($this->PROPRIETES[$nom]);
	}
	
	/*
	 @access public
	 @property string votre_propriete Nom de la propriété
	 @param string $name Nom de la propriété
	 @return string valeur de la propriété
	*/
	public function __get($name)
	{
		return $this->GetPropriete($name);
	}
	
	/*
	 @access public
	 @property string votre_propriete Nom de la propriété
	 @param string $name Nom de la propriété
	 @param string value Valeur de la propriété
	*/
	public function __set($name,$value)
	{
		$this->SetPropriete($name, $value);
	}
	
	/*
	 @access public
	 @property string Votre_propriété Nom de la propriété
	 @param string $name Nom de la propriété
	 @return boolean Retourne true si la propriété existe sinon false
	*/
	public function __isset($name)
	{
		return $this->IssetPropriete($name);
	}

}
?>