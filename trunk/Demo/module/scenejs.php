<?php
/*
 Module de rendu scenejs
 Pasqualini jean | mek-in-gold@live.fr
*/
Class module_Scenejs extends ModuleBase implements IModuleBase
{

  public function LoadModel($file)
  {
    $data=array("Type" => "scenejs" , "Action" => "IMPORT" , "Data" => base64_encode(file_get_contents($file)));
    $this->send($data);
    if(!socket_recv($this->socket,$buffer,2048,0)) { return false; } else { return true; }
  }
  
  public function Position2d($x,$y)
  {
    $data=array("Type" => "scenejs" , "Action" => "Position2d" , "X" => $x , "Y" => $y);
    $this->send($data);
    if(!socket_recv($this->socket,$buffer,2048,0)) { return false; } else { return true; }
  }
}
?>