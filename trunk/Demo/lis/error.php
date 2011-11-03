<?php
/*
 Gestionaire d'erreur
 @author internet
 @license GPL
 @version InDev
*/

/*
 Gestionaire d'erreur
 @param integer $errno Numéro d'erreur
 @param string $errstr Message d'erreur
 @param string $errfile Fichier
 @param integer $errline Numéro de la ligne d'erreur
 @return boolean
*/
function myErrorHandler($errno, $errstr, $errfile, $errline)
{
   if (!(error_reporting() & $errno)) {
        // Ce code d'erreur n'est pas inclus dans error_reporting()
        return true;
    }
    
    $log=date("d/m/y h:i:s").": [$errno] \r\n$errstr\r\n"
        . "  Erreur sur la ligne $errline dans le fichier $errfile"
        . ", PHP " . PHP_VERSION . " (" . PHP_OS . ")\r\n";
        error_log($log, 3, "log.txt");
        echo $log;

    /* Ne pas exécuter le gestionnaire interne de PHP */
    return true;
}

// Configuration du gestionnaire d'erreurs
set_error_handler("myErrorHandler");


?>
