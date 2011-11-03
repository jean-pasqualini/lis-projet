<?php  
/*
 Cette classe contient des methode permettant au modules d'utiliser d'appeler des methode du modules partie client
 @author Jean pasqualini <jpasqualini75@gmail.com>
 @license GPL
 @version InDev
 @abstract
*/
Abstract class ModuleBase {

  /*
   @access protected
   @var ApplicationLIS Contient l'instance de l'application
  */
  protected $handle_application;
  
  /*
    Constructeur du module
    @access public
    @return ModuleBase Instance de ModuleBAse
  */
  public function __construct()
  {
	// Définie l'handle application pour être compatible avec les modules non encore changer appeleant directement GetInstance()
  	$this->handle_application=ApplicationLis::GetInstance();
  }
  
  /*
   Envoie des données sous forme json au client
   @access public
   @param Array $msg Tableau des donée à formater puis à envoyée
  */
  public function send($msg){
        // Envoie des données sous forme json au client
	$this->handle_application->send($msg);
  }
  
  /*
   Recoit des données du client
   @access public
   @param int $taille Taille maximale des donné recu par défaut 2048
   @return boolean Inutile pour l'instant
  */
  public function recv($taille=2048)
  {
        // Recoit des données du client
	return $this->handle_application->recv($taille);
  }
  
  /*
   Appelle une méthode d'un module client directement via l'appel d'une méthode magique __call
   @method null votremetode() Appele une methode du module client et retourne le résultat de facon transparente comme un webservice
   */
  private function __call($method,$arguments)
  {
      $data=array_merge(array("Type" => $this->GetModuleName(), "Action" => $method) ,$arguments);
      $this->send($data);
  }
}

?>