<?php
/**
 * Classe permettant de gérer les objets 2D hérité de Object
 * @author Jean pasqualini <jpasqualini75@gmail.com>
 * @version InDev
 * @license GPL
 * @package FrameworkLis
*/
Class Object2D extends Object {
    
    /**
     * Contructeur de l'objet 2D
     * @access public
     * @param boolean * @register Détermine si c'est pour ajouter un objet 2D ou pour enregistrer un nouveau type d'objet disponible
     * @param Object2D $parent Détermine le parent de l'objet soit un objet 2d soit null si c'est l'objet principal
     * @param string $name Donne un nom a l'objet
     * @return Object2D retourne l'instance de l'objet 2D
    */
    public function __construct($register=true,$parent = null,$name = "")
    {
        // Si l'instance est un enregistrement d'un nouveau type d'objet alors on le log
        if($register)
        {
            echo "[INFO] Objet (".get_class($this).") de type 2D initialiser\r\n";
        }
        
        // Par défaut si l'objet est un enfant sa position est relative a l'objet parent
        if($parent != null)
        {
            $this->Position = "Relative";
        }

        // On appele le parent        
        parent::__construct($parent,$name);
    }
    
    /**
     * Modifie la couleur de remplisage de l'objet
     * @access public
     * @param string $color Couleur de l'objet en couleur html
     * @return Object2D Retourne l'instance de l'objet 2D
    */
    public function SetBackgroundColor($color)
    {
        $this->Background=$color;
        return $this;
    }
    
    /**
     * Retourne la position en X abosolu de l'objet
     * @access public
     * @return int La position en X Absolu de l'objet
    */
    public function GetAbsolutePositionX()
    {
        if($this->parent!=null) return $this->parent->GetAbsolutePositionX() + $this->PositionX;
        return $this->PositionX;
    }
    
    /**
     * Retourne la position en Y Absolu de l'objet
     * @access public
     * @return int La position en Y Absolu de l'objet
    */
    public function GetAbsolutePositionY()
    {
        if($this->parent!=null) return $this->parent->GetAbsolutePositionY() + $this->PositionY;
        return $this->PositionY;
    }
    
    /**
     * Déplace l'objet a une position donnée via $x et $y en absolu
     * @access public
     * @param int $x La position en X de l'objet
     * @param int $y La position en Y de l'objet
     * @return Object2D Retourne l'instance de l'objet 2D
    */
    public function MoveTo($x,$y)
    {
        //On informe de la mise a jour de l'objet
        $this->SetUpdated(true);
        
        $this->PositionX=$x;
        $this->PositionY=$y;
        return $this;
    }
    
    /**
     * Déplace l'objet a uen position donée via $x et $y en relatif
     * @access public
     * @param int $x La position en X de l'objet
     * @param int $y La position en Y de l'objet
     * @return Object2D Retourne l'instance de l'objet 2D
    */
    public function MoveOf($x,$y)
    {
        //On informe de la mise a jour de l'objet
        $this->SetUpdated(true);
        
        $this->PositionX+=$x;
        $this->PositionY+=$y;
        return $this;
    }
}
?>