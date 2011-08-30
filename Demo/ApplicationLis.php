<?php

// Reporte toutes les erreurs php
error_reporting(E_ALL);

// Fonction d'inclusion de classe automatique
function __autoload($class_name)
{
    if (file_exists($class_name.".application.class.php"))
    {
        include($class_name.".application.class.php");
    }
    else
    {
        return false;
    }
}

// tableaux d'erreur 
$error=array();

// Redefinir les dossier d'inclusion (a faire)
include("lis/Interface.php");           // Fichier d'interface des methode obligatoire des module de rendu afin de les standarisé
include("lis/WS.class.php");            // Soccupe de la communiquation avec le client
include("lis/cssparser.class.php");     // Permet de parser les fichier css
include("lis/error.php");               // Prend le relais pour gerer les erreur php sout meme
include("config/color.application.php");// Declare les différence couleur primaire utiliser dans l'application (a voir l'utilité)
include("config/config.application.php");// Contient la configuration de l'application lis
include("lis/ApplicationLIS.class.php"); // Contient la classe application LIS
include("lis/Object.class.php"); // Permet de gerer tout en objet
include("lis/Object2D.class.php"); // Deriver pour la gestion des objet 2D
include("lis/Object3D.class.php"); // Deriver pour la gestion des objet 3D
include("lis/EventProxy.class.php"); // Proxy pour l'evenementiel qui permet d'enregister une methode d'instance appelable 
include("lis/SProxy.class.php"); // Proxy pour l'evenementiel qui permet d'associer a un objet plusieur objet pour application des action facilement sur ce groupe d'objet
include("lis/ObjectCss.php"); // ObjetsCss
?>