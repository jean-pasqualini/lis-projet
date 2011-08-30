<?php
/*
 Module de rendu lisrendu
 Pasqualini jean | mek-in-gold@live.fr
*/
Class module_audioapi extends ModuleBase implements IModuleBase {

  public function GetModuleName()
  {
    return "Audioapi";
  }

  public function GetModuleDescription()
  {
      return "Genere du son";
  }
  
  public function GetVersion()
  {
      return "1.0";
  }
  
  public function GetDependanceServer()
  {
    // Declare les modules dont serveur nous avons besoin
    return array();
  }
  
  public function GetDependanceClient()
  {
    return array(); 
  }
  
  // Joue la note dont la fréquence en passé en parametre
  public function PlayNote($note)
  {
    $data = array ("Type" => "audio" , "Note" => $note);
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }
  
  // Stop une note si actuellement jouée
  public function StopNote()
  {
    $data = array ("Type" => "audio" , "Note" => "stop");
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }
  
}
?>