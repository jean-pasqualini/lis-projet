<?php
/*
 Interface pour les module de rendu audio
 @author Jean pasqualini <jpasqualini75@gmail.com>
 @license GPL
 @version InDev
*/
Interface AudioRender // Interface audio
{
    /*
     Le contructeur des modules de rendu audio
     @access public
     @param Socket $socket Contient une socket de connection
     @return ModuleBase Retour l'instance du module
    */
    public function __construct($socket);
    
    /*
     Permet de jouer une note donc la fréquence est passé en paramètre
     @access public
     @param float $note Fréquence de la note
    */ 
    public function PlayNote($note);
    
    /*
     Permet de stoper le son
     @access public
    */
    public function StopNote();
}

/*
 Interface pour les mods
 @author Jean pasqualini <jpasqualini75@gmail.com>
 @license GPL
 @version InDev
*/
Interface IModuleBase
{
    /*
      Permet de connaitre le nom du module
      @access public
      @return string Retourne le nom du module
    */
    public function GetModuleName();
    
    /*
     Permet de connaitre la desription du module
     @access public
     @return string Retourne la déscription du module
    */
    public function GetModuleDescription();
    
    /*
     Permet de récuperer la vercion du module
     @access public
     @return string Retourne la version du module
    */
    public function GetVersion();
    
    /*
     Permet de récuperer les dépendances modules serveur du module
     @access public
     @return Array Retourne la liste des modules serveur
    */
    public function GetDependanceServer();
    
    /*
     Permer de récuprer les dépendances modules client du module
     @access public
     @return Array Retourne la liste des modules client
    */
    public function GetDependanceClient();
}
?>