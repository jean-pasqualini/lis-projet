<?php
/*
 Ici on inclue l'api framework lis
 Et on instancie l'application
*/
include("ApplicationLis.php");
if ($_SERVER['argc']!=2)
{
    echo "Syntaxe argument incorect\r\n";
    exit();
}
if (!is_numeric($_SERVER['argv'][1]))
{
    echo "L'argument du port doit etre un nombre\r\n";
    exit();
}
//192.168.10.239
new Canvas("127.0.0.1",$_SERVER['argv'][1]);
?>