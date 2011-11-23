<?php
/**
 * Classe pour parser les fichier de définition de design css
 * @author internet
 * @version InDev
 * @license GPL
 * @package FrameworkLis
*/
class CssParseur {
  
  /**
   * @access public
   * @var Array Contient les définitions css
  */
  public $css;
  
  /**
   * @access private
   * @staticvar CssParseur Contient l'instance actuelle du parseur
  */
  private static $instance;
  
  /**
   * @access private
   * @var string contient le chemin d'acces au fichier css 
   */
  private $file;
  
  /**
   * @access private
   * @var int numéro de ligne en cour de parse
   */
  private $ligne  = 0;
  
  /**
   * Parse une chaine contenant du css et retourne un tableau de propriété
   * @access public
   * @param string $str chaine css
   * @return Array Retourne un tableau de ObjectCss qui contient chacun une définition selecteur { propriété1: valeur; propriété2: valeur; }
  */
  public function ParseStr($str) {
    $selecteur=array();
    
    // Supprime les commentaire de la chaine css
    
    $str = preg_replace("/\/\*(.*)?\*\//Usi", "", $str);
    
    // Parse le code css présent
    $parts = explode("}",$str);
    	
    // S'il ya des élement css alors...
    if(count($parts) > 0) {
      
      // On parcour ces élement
      foreach($parts as $part) {
				
	 $this->ligne++;	
				
	// On récupere le sélecteur et les propriété en les séparent par {
	// Le sélecteur dans $keystr et les propriété dans $codestr
       $temp = explode("{", $part);
       
	   if(count($temp) != 2) throw new CssParseException($this, "Le fichier ne contient aucune définition");
	   
	   
       list($keystr,$codestr) = $temp;
	
	// On récupere dans un tableau les sélecteurs multipe du sélecteur séparré par une virgule
        $keys = explode(",",trim($keystr));
        
	// S'il y a bien un sélecteur alors
	if(count($keys) > 0) {
	  
	  // On parcours les sélecteur multiple du sélecteur
          foreach($keys as $key) {
	    
	    // On vérifie bien que le sélecteur n'est pas vide
            if(strlen($key) > 0) {
	      
	      // ON supprime les saut de ligne
              $key = str_replace("\n", "", $key);
              $key = str_replace("\\", "", $key);
                  
		// On instancie un objet css qu'on empile dans un tableau de sélecteur avec pour clé le selecteur
              	$selecteur[$key]=new ObjectCss();

                $value = $key;
		
		// On décompose le selecteur
		
		// On récupere le ou les nom d'element s'il yen a
              	if(preg_match("/^([A-Za-z]{1,})(.*)/",$value,$matches))
              	{
			// Puis on ajoute le nom a l'bjet css
              		$selecteur[$key]->AddNom($matches[1]);
              		$value=substr($value,strlen($matches[1]));
              	}
              
		// On récupere l'id s'il yen a 
              	if(preg_match("/^[#]{1}([A-Za-z]{1,})/",$value,$matches))
              	{
			// Puis on ajoute l'id à l'objet css
              		$selecteur[$key]->AddId($matches[1]);
              	}
              	elseif(preg_match_all("/[.]{1}([A-Za-z]{1,})/",$value,$matches))
              	{
			// On récupere les classe 
              		foreach($matches[1] as $classe)
              		{
						// Et on les ajoute à l'objet css
              			$selecteur[$key]->AddClasse($classe);
              		}
              	}
		
		// On sépare les ligne des propriété css avec ;
		$codes = explode(";",$codestr);
		
		// On vérifié qu'il ya bien des ligne de propriété css
		if(count($codes) > 0) {
		  
		  // On parcour ces ligne
		  foreach($codes as $code) {
		    	
		    $this->ligne++;			
			
		    // Suprimé les espace ou autre caractère invisible
		    $code = trim($code);
		    
		    // On récupere la propriété de chaque ligne dans $codekey et la valeur dans $codevalue en séparant avec :
		    $tempcode = explode(":",$code);
		    
		    if(count($tempcode) != 2) {
		    	throw new CssParseException($this, "Erreur dans la chaine css");
		    }
		    
		    list($codekey, $codevalue) = $tempcode;
		    
		    // On vérifie bien que la propriété n'est pas vie
		    if(strlen($codekey) > 0) {
		      
		      // On ajoute cette propriété à l'obje tcss
		      $selecteur[$key]->SetPropriete($codekey,trim($codevalue));
		    }
		  }
		}
		
		//print_r($selecteur[$key]);
            }
          }
        }
      }
    }
    
    // Puis on retourne tableau d'objet css
    return $selecteur;
  }
  
  /**
   * Recupére l'instance unique du parseur css
   * @access public
   * @static
   * @return CssParseur Retourne l'instance unique du parseur
  */
  public static function GetInstance()
  {
	// Si le parseur est déjà inititialisé alors on retourne son instance
  	if(CssParseur::$instance != null) return CssParseur::$instance;
	
	// Sinon on initialise le parseur et on retourne son instance
  	return new CssParseur();
  }
  
  /**
   * Recupere le numéro de ligne en cours de parse
   * @access public
   * @return int Le numéro de ligne
   */
  public function getLine()
  {
	return $this->ligne;
  }
  
  /**
   * Recupere le nom du fichier css
   * @access public
   * @return string Le nom du fichier css
   */
  public function getFile()
  {
  	return $this->file;
  }
  
  /**
    * Méthode pour parser un css qui retourne un tableau de propriété
    * @static
    * @access public
    * @param string $filename
    * @return Array Retourne le tableau de propriété
  */
  public function Parse($filename) {
  	  $this->file = $filename;
	
      return $this->ParseStr(file_get_contents($filename));
  }
  

}
?>
