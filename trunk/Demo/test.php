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
        echo "Arr�t...<br />\n";
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

    /* Ne pas ex�cuter le gestionnaire interne de PHP */
    return true;
}

// Fonction pour tester la gestion d'erreur
function scale_by_log($vect, $scale)
{
    if (!is_numeric($scale) || $scale <= 0) {
        trigger_error("log(x) for x <= 0 is undefined, you used: scale = $scale", E_USER_ERROR);
    }

    if (!is_array($vect)) {
        trigger_error("Type d'entr�e incorrect, tableau de valeurs attendu", E_USER_WARNING);
        return null;
    }

    $temp = array();
    foreach($vect as $pos => $value) {
        if (!is_numeric($value)) {
            trigger_error("La valeur � la position $pos n'est pas un nombre, utilisation 0 (z�ro)", E_USER_NOTICE);
            $value = 0;
        }
        $temp[$pos] = log($scale) * $value;
    }
    return $temp;
  }

// Configuration du gestionnaire d'erreurs
$old_error_handler = set_error_handler("myErrorHandler");

// G�n�ration de quelques erreurs. Commen�ons par cr�er un tableau
echo "vector a\n";
$a = array(2, 3, "foo", 5.5, 43.3, 21.11);
print_r($a);

// G�n�rons maintenant un second tableau
echo "----\nvector b - a notice (b = log(PI) * a)\n";
/* Valeur � la position $pos n'est pas un nombre, utilisation de 0 (z�ro) */
$b = scale_by_log($a, M_PI);
print_r($b);

// Ceci est un probl�me, nous avons utilis� une cha�ne au lieu d'un tableau
echo "----\nvector c - a warning\n";
/* Type d'entr�e incorrect, tableau de valeurs attendu */
$c = scale_by_log("non un tablau", 2.3);
var_dump($c); // NULL

// Ceci est une erreur critique : le logarithme de z�ro ou d'un nombre n�gatif est ind�fini
echo "----\nvector d - fatal error\n";
/* log(x) pour x <= 0 est ind�fini, vous utilisez : scale = $scale" */
$d = scale_by_log($a, -2.5);
var_dump($d); // Jamais atteint
?>
