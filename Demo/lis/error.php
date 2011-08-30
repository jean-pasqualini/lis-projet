<?php
// Gestionnaire d'erreurs
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

    /* Ne pas ex�cuter le gestionnaire interne de PHP */
    return true;
}

// Configuration du gestionnaire d'erreurs
set_error_handler("myErrorHandler");


?>
