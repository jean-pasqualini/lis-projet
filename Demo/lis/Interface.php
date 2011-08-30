<?php
/* Ici on configure les spécification des module de rendu */
Interface AudioRender // Interface audio
{
    public function __construct($socket);
    public function PlayNote($note);
    public function StopNote();
}

// Interface pour les mods
Interface IModuleBase
{
    public function GetModuleName();
    public function GetModuleDescription();
    public function GetVersion();
    public function GetDependanceServer();
    public function GetDependanceClient();
}
?>