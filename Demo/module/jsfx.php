<?php
/*
 Module de rendu AUDIO COMPATIBLE MOZILLA , GOOGLE CHROME ET OPERA
 
 @author Jean pasqualini <jpasqualini@live.fr>
 @license GPL
 @version InDev
*/
Class module_Jsfx extends ModuleBase implements IModuleBase {
	
	// Definition de quelque note par defaut
	CONST DO = 1;
	CONST RE = 2;
	CONST MI = 3;
	CONST FA = 4;
	CONST SOL = 5;
	CONST LA = 6;
	CONST SI = 7;
	    
    /*
     Permet de jouer une note
     @access public
     @param int $NOTE Note a jouer
     @return boolean Retourne true si la note à été bien jouée sinon false
    */
    public function PlayNote($note)
    {
        $data=array("Type" => "audio" , "Action" => "PlayNote" , "X" => $x, "Y" => $y , "W" => $w , "H" => $h);
        $this->send($data);
        if(!$this->recv()) { return false; } else { return true; }
    }
    
}
?>