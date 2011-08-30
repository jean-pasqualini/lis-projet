<?php
/*
  lis est en pleine transformation certaine fonction
  vont disparaite pendant l'implementation du concept experimental
  $this->view->SetTypeMouse("rond",ROUGE,"cur.png");
*/
/*
  Pense-bete : implémenter le rendu exterieure 'canvas' pour présentation
*/

class Demo extends ApplicationLIS {
    

    private $temp_limite;
    private $max=30; // Demonstration limite a 30 seconde ...
    
    function __construct ($address,$port)
    { 
        //Lance l'attende de la connection d'un client
        parent::run($address,$port);

        $this->temp_limite=time();
	/*
        while(1)
		{
	$t=json_encode(array("Type" => "audioapi","Note" => "293.6648"))."\r\n";
	socket_write($this->instance,$t,strlen($t));
	usleep(500000);
		}
	*/
	// Charge le module scenejs
        $this->AddModule("xd","scenejs");
        
        // Importe un modele d'avion 3d
        $this->xd->LoadModel("../ClientR76/seymour-plane.js");
        
        //Importe un css 
        // $this->view->ImportCss("css.css");
        
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
                    for ($o=0;isset($chanson[$o]);$o++)
                    {
                        $aleatoirex=rand(1,1024);
                        $aleatoirey=rand(1,600);
                        
                        // Met a jour la position de la souris
                        
                        // $this->view->UpdateMouse($aleatoirex,$aleatoirey);
                         $this->xd->Position2d($x,$y);
                        // OU
                        // $this->UpdateMouse($aleatoirex,$aleatoirey);
                        
                        // Les deux sont viable mais la premiere est plus rapide
                        
                        // possible probleme avec tampom de calque du lisrender (gd2)
                        
                        //Creée dans le calque mis un rectangle de couleur bleu puis ajoute un texte blanc dessus  
                        // $this->view->InsertRectangle("Mee",0,0,160,15,BLEU);
                        // $this->view->AfficherTexte("Mee",($this->max-(time()-$this->temp_limite))."S => ".$chanson[$o],0,0,BLANC);
                       // $this->view->ProprieteCalque["Mee"]["X"]=$x;
                        //$this->view->ProprieteCalque["Mee"]["Y"]=$y;
                        
                        // Joue une note (ref : tableau de frequence ci dessus)
                        $this->audio->PlayNote($chanson[$o]);
            
                        //usleep(500000);
                        
                        $this->audio->StopNote();
                        if(time()-$this->temp_limite>$this->max) { return; }
                       // usleep(500000);
                      
                        if ($x>599) { $x=$y=0; } else { $x=$y++; }
                        if(time()-$this->temp_limite>$this->max) { return; }
                    }
                    
              }
        
    }

    
    
}
?>