<?php
class CssParseur {
  public $css;
  private static $instance;
  
  function ParseStr($str) {
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
  
  public static function GetInstance()
  {
  	if(CssParseur::$instance != null) return CssParseur::$instance;
  	return new CssParseur();
  }
  
  function Parse($filename) {
      return $this->ParseStr(file_get_contents($filename));
  }
  

}
?>
