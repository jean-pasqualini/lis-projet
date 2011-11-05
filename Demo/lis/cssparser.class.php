<?php
/*
 Classe pour parser les fichier de définition de design css
 @author internet
 @version InDev
 @license GPL
*/
class CssParseur {
  
  /*
   @access public
   @var Array Contient les définitions css
  */
  public $css;
  
  /*
   @access private
   @staticvar CssParseur Contient l'instance actuelle du parseur
  */
  private static $instance;
  
  /*
   Parse une chaine contenant du css et retourne un tableau de propriété
   @access public
   @param string $str chaine css
   @return Array Retourne les propriétés css
  */
  public function ParseStr($str) {
    $selecteur=array();
    // Remove comments
    $str = preg_replace("/\/\*(.*)?\*\//Usi", "", $str);
    // Parse this damn csscode
    $parts = explode("}",$str);
    if(count($parts) > 0) {
      foreach($parts as $part) {
        @list($keystr,$codestr) = explode("{",$part);
        $keys = explode(",",trim($keystr));
        if(count($keys) > 0) {
          foreach($keys as $key) {
            if(strlen($key) > 0) {
              $key = str_replace("\n", "", $key);
              $key = str_replace("\\", "", $key);
                        
              	$selecteur[$key]=new ObjectCss();

                $value = $key;
              	if(preg_match("/^([A-Za-z]{1,})(.*)/",$value,$matches))
              	{
              		$selecteur[$key]->AddNom($matches[1]);
              		$value=substr($value,strlen($matches[1]));
              	}
              
              	if(preg_match("/^[#]{1}([A-Za-z]{1,})/",$value,$matches))
              	{
              		$selecteur[$key]->AddId($matches[1]);
              	}
              	elseif(preg_match_all("/[.]{1}([A-Za-z]{1,})/",$value,$matches))
              	{
              		foreach($matches[1] as $classe)
              		{
              			$selecteur[$key]->AddClasse($classe);
              		}
              	}




              
    $codes = explode(";",$codestr);
    if(count($codes) > 0) {
      foreach($codes as $code) {
        $code = trim($code);
        @list($codekey, $codevalue) = explode(":",$code);
        if(strlen($codekey) > 0) {
          $selecteur[$key]->SetPropriete($codekey,trim($codevalue));
        }
      }
    }
    print_r($selecteur[$key]);
            }
          }
        }
      }
    }
    //
    return $selecteur;
  }
  
  /*
   Recupére l'instance unique du parseur css
   @return CssParseur Retourne l'instance unique du parseur
  */
  public static function GetInstance()
  {
	// Si le parseur est déjà inititialisé alors on retourne son instance
  	if(CssParseur::$instance != null) return CssParseur::$instance;
	
	// Sinon on initialise le parseur et on retourne son instance
  	return new CssParseur();
  }
  
  /*
    Méthode pour parser un css qui retourne un tableau de propriété
    @static
    @access public
    @param string $filename
    @return Array Retourne le tableau de propriété
  */
  public function Parse($filename) {
      return $this->ParseStr(file_get_contents($filename));
  }
  

}
?>
