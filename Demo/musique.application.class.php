<?php
/*
  lis est en pleine transformation certaine fonction
  vont disparaite pendant l'implementation du concept experimental
  $this->view->SetTypeMouse("rond",ROUGE,"cur.png");
*/
/*
  Pense-bete : implémenter le rendu exterieure 'canvas' pour présentation
*/

class musique extends ApplicationLIS {
    

    private $temp_limite;
    private $max=30; // Demonstration limite a 30 seconde ...
    
    function __construct ($address,$port)
    { 
        //Lance l'attende de la connection d'un client
        parent::run($address,$port);

        $this->temp_limite=time();
        
        // Un tableau de frequence de note (Do Do Re Re ...)
        $chanson=array("293.6648","293.6648",
                   "349.2282","349.2282",
                   "293.6648","293.6648",
                   "440.0000","329.6276",
                   "329.6276","261.6256",
                   "261.6256","329.6276",
                   "329.6276","391.9954");
        
        // Ajoute le casque Mee
        // $this->view->AddCalque("Mee",160,15,160,15,10,10);
       $x=$y=0;
         while(1)
              {
 shuffle($chanson);
                    for ($o=0;isset($chanson[$o]);$o++)
                    {
                        
                        // Joue une note (ref : tableau de frequence ci dessus)
                        $this->audio->PlayNote($chanson[$o]);
                        $this->audio->StopNote();
                        if(time()-$this->temp_limite>$this->max) { return; }
                        if ($x>599) { $x=$y=0; } else { $x=$y++; }
                        if(time()-$this->temp_limite>$this->max) { return; }
                    }
                    
              }
        
    }

    
    
}
?>