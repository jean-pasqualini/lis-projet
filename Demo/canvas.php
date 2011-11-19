<?php
/**
  * Script principale de l'application canvas
  * C'est lui qui instancie la classe principale de l'application
  * @author Jean pasqualini <jpasqualini75@gmail.com>
  * @version InDev
  * @license GPL
  * @package Canvas
*/

// Inclu le script d'inclusion du framework 'LIS' et ses dépendances
include("ApplicationLis.php");

// L'application requis toujours d'être lancée avec deux arguments
if ($_SERVER['argc']!=2)
{
    echo "Syntaxe argument incorect\r\n";
    exit();
}

// Le deuxieme argument doit être numéric
if (!is_numeric($_SERVER['argv'][1]))
{
    echo "L'argument du port doit etre un nombre\r\n";
    exit();
}

// On instancie l'application sur l'adresse d'ecouté 127.0.0.1 avec le port passé en paramétre
new Canvas("127.0.0.1",$_SERVER['argv'][1]);
?>