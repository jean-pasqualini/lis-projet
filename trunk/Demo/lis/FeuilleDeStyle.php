<?php
/**
 * Classe qui contient les feuille des styles
 * @author Jean pasqualini <jpasqualini75@gmail.com>
 * @version InDev
 * @license GPL
 * @package FrameworkLis
*/
Class FeuilleDeStyle {
	
	/**
	 * @access private
	 * @static
	 * @staticvar string Contient le nom du theme actuelle par défaut 'Défault'
	*/
	private static $theme = "Default";
	
	/**
	 * @access private
	 * @static
	 * @staticvar Array Toutes les définitions css de la feuille de style
	*/
	private static $DefinitionsCss = array();
	
	/**
	  * @access private
	  * @static
	  * @staticvar string Dossier ou sont stocké les thèmes par défaut 'themes/'
	*/
	private static $Folder = "theme/";
	
	/**
	 * Configure le thème actuelle
	 * @access public
	 * @static
	 * @param string $theme Nom du thème
	*/
	public static function SetTheme($theme)
	{
		$this->theme = $theme;
		
		foreach(FeuilleDeStyle::$DefinitionsCss as $File) FeuilleDeStyle::AddFile($file);
	}
	
	/**
	 * Recupere le chemin d'accès d'une feuille de style
	 * @static
	 * @access public
	 * @param string $file Nom de la feuille de style
	 * @return string Retourne le chemin de la feuille de style
	*/
	public static function GetLinkFile($file)
	{
		return FeuilleDeStyle::$Folder.FeuilleDeStyle::$theme."/css/".$file.".css";
	}
	
	/**
	 * Ajoute une feuille de style
	 * @static
	 * @access public
	 * @param string $file Nom de la feuille de style
	*/
	public static function AddFile($file)
	{
		$this->DefinitionsCss[$file] = CssParseur::GetInstance() -> Parse(FeuilleDeStyle::GetLinkFile($file));
	}
	
	/**
	  * Supprime une feuille de style
	  * @static
	  * @access public
	  * @param string $file Nom de la feuille de style
	*/
	public static function RemoveFile($file)
	{
		unset($this->DefinitionsCss[$file]);
	}
	
	/**
	  * Remplace une feuille de style
	  * @static
	  * @access public
	  * @param string $ancien Nom de l'ancienne feuille de style
	  * @param string $nouveau Nom de la nouvelle feuille de style
	*/
	public static function SwitchFile($ancien,$nouveau)
	{
		$this->DefinitionsCss[$ancien] = $nouveau;
	}
	
    /**
     * Selection les style depuis un objet
     * @access public
     * @static
     * @param Object $objet Instance de l'objet
     * @return Array Retourne les propriété css de l'objet
    */
    public static function SFromObject($objet)
    {
       
        $proprietes=array();
	// On parcour les definitions css
            foreach($this->DefinitionsCss as $File)
            {
		// On parcour les fichier css
                foreach($File as $FileCss)
                {
			// On verifie si un objet avec le nom existe dans le définition css
                	if($FileCss->IssetNom($objet->GetNom()))
                	{
				// Si l'id est vide ou si l'id existe dans un des element du définition css
                		if(empty($FileCss->GetIds()) || $FileCss->IssetId($objet->GetId()))
                		{
					// Et que il n'y a pas de classe
                			if(empty($FileCss->GetClasses()))
                			{
						// et que l'objet css n'a pas de classe que la définition css n'a pas
                				if(array_diff($FileCss->GetClasses(),$objet->GetClasses()))
                				{
							// Alors on empile ces parametres dasn le tableau $proprietes
                					$propriete = array_merge($propriete,$FileCss->GetProprietes()); 
                				}
                			}
                		}
                	}

                }
            }

        //if(count($objets)==1) { $objets=$objets[0]; }
	// Puis on retourne ce tableau
        return $proprietes; 
    }
	
}
?>