<?php
/*
  Module de rendu audio n�sc�citant mozilla cot� client
  Sauf si adaptation par un module client ayant la meme interface audio
  
  @author Jean pasqualini <jpasqualini@live.fr>
  @license GPL
  @version InDev
*/
Class module_audioapi extends ModuleBase implements IModuleBase {

  /*
   Recup�re le nom du module
   @access public
   @return string Le nom du module
  */
  public function GetModuleName()
  {
    return "Audioapi";
  }

  /*
   R�cupere la description du module
   @access public
   @return string La description du module
  */
  public function GetModuleDescription()
  {
      return "Genere du son";
  }
  
  /*
   R�cupere la version du module
   @access public
   @return string La version du module
  */
  public function GetVersion()
  {
      return "1.0";
  }
  
  /*
   R�cupere les d�pendances module serveur
   @access public
   @return Array Les d�pendances serveur
  */
  public function GetDependanceServer()
  {
    // Declare les modules dont serveur nous avons besoin
    return array();
  }

  /*
   R�cupere les d�pendances module client
   @access public
   @return Array Les d�pendances client
  */
  public function GetDependanceClient()
  {
    return array(); 
  }
  
  /*
   Joue la note dont la fr�quence en pass� en parametre
   @access public
   @param float $note frquence de la note
   @return boolean Retourne true si la note � �t� jouer avec succ�s sinon false
  */
  public function PlayNote($note)
  {
    // Envoie la commande
    $data = array ("Type" => "audio" , "Note" => $note);
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }
  
  /*
    Stop une note si actuellement jou�e
    @access public
    @return boolean Retourne true si la note � arr�t� de jouer avec succ�s sinon false
  */
  public function StopNote()
  {
    // Envoie la commande
    $data = array ("Type" => "audio" , "Note" => "stop");
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }
  
}
?>