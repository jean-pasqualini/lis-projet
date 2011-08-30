<?php
// Gestionnaire d'erreurs
function myErrorHandler($errno, $errstr, $errfile, $errline)
{
   if (!(error_reporting() & $errno)) {
        // Ce code d'erreur n'est pas inclus dans error_reporting()
        return;
    }

    switch ($errno) {
    case E_USER_ERROR:
        echo "<b>Mon ERREUR</b> [$errno] $errstr<br />\n";
        echo "  Erreur fatale sur la ligne $errline dans le fichier $errfile";
        echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
        echo "Arrêt...<br />\n";
        exit(1);
        break;

    case E_USER_WARNING:
        echo "<b>Mon ALERTE</b> [$errno] $errstr<br />\n";
        break;

    case E_USER_NOTICE:
        echo "<b>Mon AVERTISSEMENT</b> [$errno] $errstr<br />\n";
        break;

    default:
        echo "Type d'erreur inconnu : [$errno] $errstr<br />\n";
        break;
    }

    /* Ne pas exécuter le gestionnaire interne de PHP */
    return true;
}

// Fonction pour tester la gestion d'erreur
function scale_by_log($vect, $scale)
{
    if (!is_numeric($scale) || $scale <= 0) {
        trigger_error("log(x) for x <= 0 is undefined, you used: scale = $scale", E_USER_ERROR);
    }

    if (!is_array($vect)) {
        trigger_error("Type d'entrée incorrect, tableau de valeurs attendu", E_USER_WARNING);
        return null;
    }

    $temp = array();
    foreach($vect as $pos => $value) {
        if (!is_numeric($value)) {
            trigger_error("La valeur à la position $pos n'est pas un nombre, utilisation 0 (zéro)", E_USER_NOTICE);
            $value = 0;
        }
        $temp[$pos] = log($scale) * $value;
    }
    return $temp;
  }

// Configuration du gestionnaire d'erreurs
$old_error_handler = set_error_handler("myErrorHandler");

// Génération de quelques erreurs. Commençons par créer un tableau
echo "vector a\n";
$a = array(2, 3, "foo", 5.5, 43.3, 21.11);
print_r($a);

// Générons maintenant un second tableau
echo "----\nvector b - a notice (b = log(PI) * a)\n";
/* Valeur à la position $pos n'est pas un nombre, utilisation de 0 (zéro) */
$b = scale_by_log($a, M_PI);
print_r($b);

// Ceci est un problème, nous avons utilisé une chaîne au lieu d'un tableau
echo "----\nvector c - a warning\n";
/* Type d'entrée incorrect, tableau de valeurs attendu */
$c = scale_by_log("non un tablau", 2.3);
var_dump($c); // NULL

// Ceci est une erreur critique : le logarithme de zéro ou d'un nombre négatif est indéfini
echo "----\nvector d - fatal error\n";
/* log(x) pour x <= 0 est indéfini, vous utilisez : scale = $scale" */
$d = scale_by_log($a, -2.5);
var_dump($d); // Jamais atteint
?>
