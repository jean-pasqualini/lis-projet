<?php
/*
 Module de rendu d'image 3D compatible mais sans scpécification pour les rendu 3D
 
 @author Jean pasqualini <jpasqualini@live.fr>
 @license GPL
 @version InDev
*/
Class module_Scenejs extends ModuleBase implements IModuleBase
{

  /*
   Permet de charger un model scenejs au format json
   @access public
   @param string Chemin vers le model
   @return boolean Retourne true si le model est bien chargé sinon false
  */
  public function LoadModel($file)
  {
    $data=array("Type" => "scenejs" , "Action" => "IMPORT" , "Data" => base64_encode(file_get_contents($file)));
    $this->send($data);
    if(!socket_recv($this->socket,$buffer,2048,0)) { return false; } else { return true; }
  }
  
  /*
   Modifie la position 2D du module actuelle
   @access public
   @param int $x La position en X
   @param int $y La posiition en Y
   @return boolean Retourne true si la position à bien été changé sinon false
  */
  public function Position2d($x,$y)
  {
    $data=array("Type" => "scenejs" , "Action" => "Position2d" , "X" => $x , "Y" => $y);
    $this->send($data);
    if(!socket_recv($this->socket,$buffer,2048,0)) { return false; } else { return true; }
  }
}
?>