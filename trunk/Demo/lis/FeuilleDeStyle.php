<?php
Class FeuilleDeStyle {
	private static $theme = "Default";
	private static $DefinitionsCss = array();
	private static $Folder = "theme/";
	
	public static function SetTheme($theme)
	{
		$this->theme = $theme;
		
		foreach(FeuilleDeStyle::$DefinitionsCss as $File) FeuilleDeStyle::AddFile($file);
	}
	
	public static function GetLinkFile($file)
	{
		return FeuilleDeStyle::$Folder.FeuilleDeStyle::$theme."/css/".$file.".css";
	}
	
	public static function AddFile($file)
	{
		$this->DefinitionsCss[$file] = CssParseur::GetInstance() -> Parse(FeuilleDeStyle::GetLinkFile($file));
	}
	
	public static function RemoveFile()
	{
		unset($this->DefinitionsCss[$file]);
	}
	
	public static function SwitchFile($ancien,$nouveau)
	{
		$this->DefinitionsCss[$ancien] = $nouveau;
	}
	
    // Selecteur d'objet 
    public static function SFromObject($objet)
    {
       
        $proprietes=array();
            foreach($this->DefinitionsCss as $File)
            {
                foreach($File as $FileCss)
                {
                	if($FileCss->IssetNom($objet->GetNom()))
                	{
                		if(empty($FileCss->GetIds()) || $FileCss->IssetId($objet->GetId()))
                		{
                			if(empty($FileCss->GetClasses()))
                			{
                				if(array_diff($FileCss->GetClasses(),$objet->GetClasses()))
                				{
                					$propriete = array_merge($propriete,$FileCss->GetProprietes()); 
                				}
                			}
                		}
                	}

                }
            }

        //if(count($objets)==1) { $objets=$objets[0]; }
        return $proprietes;
    }
	
}
?>