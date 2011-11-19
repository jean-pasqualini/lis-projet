<?php
/**
  * Module de rendu audio néscécitant mozilla coté client
  * Sauf si adaptation par un module client ayant la meme interface audio
  * @author Jean pasqualini <jpasqualini75@gmail.com>
  * @license GPL
  * @version InDev
  * @package ModuleLis
*/
Class module_audioapi extends ModuleBase implements IModuleBase {

  /**
   * Recupére le nom du module
   * @access public
   * @return string Le nom du module
  */
  public function GetModuleName()
  {
    return "Audioapi";
  }

  /**
   * Récupere la description du module
   * @access public
   * @return string La description du module
  */
  public function GetModuleDescription()
  {
      return "Genere du son";
  }
  
  /**
   * Récupere la version du module
   * @access public
   * @return string La version du module
  */
  public function GetVersion()
  {
      return "1.0";
  }
  
  /**
   * Récupere les dépendances module serveur
   * @access public
   * @return Array Les dépendances serveur
  */
  public function GetDependanceServer()
  {
    // Declare les modules dont serveur nous avons besoin
    return array();
  }

  /**
   * Récupere les dépendances module client
   * @access public
   * @return Array Les dépendances client
  */
  public function GetDependanceClient()
  {
    return array(); 
  }
  
  /**
   * Joue la note dont la fréquence en passé en parametre
   * @access public
   * @param float $note frquence de la note
   * @return boolean Retourne true si la note é été jouer avec succés sinon false
  */
  public function PlayNote($note)
  {
    // Envoie la commande
    $data = array ("Type" => "audio" , "Note" => $note);
    $this->send($data);
    if(!$this->recv()) { return false; } else { return true; }
  }
  
  /**
    * Stop une note si actuellement jouée
    * @access public
    * @return boolean Retourne true si la note é arrété de jouer avec succés sinon false
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