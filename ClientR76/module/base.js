/*
  Le module base est un module spécial
  ...
*
*/

if(typeof jQuery == 'undefined')
{
	alert("La librairie jquery n'est pas accèsible");
}
else
{
	alert("youuupe");
}

var module_base = $.inherit ({
    
    __constructor : function (id_app)
    {
    	this.id_app=id_app;
    },
    
    GetName : function()
    {
    	return "Base";
    },
    
    test : function ()
    {
        alert("Test module base client");
    },
    
    action : function (Instruction,id_app,t)
    {
	    switch(Instruction.Action)
	    {	
		// Dessine un rectangle rempli (x, y, largeur, hauteur)
		case "SendMsg":
		    if($(this.instance).attr('log')) { log (Instruction.Type+" > Message du serveur",id_app); }
		    alert(Instruction.Msg);
		    break;	
		
		default:
		    if($(this.instance).attr('log')) { log (Instruction.Type+" > Action inconu",id_app); }
		break;
	    }
            socket.send('{"Action" : "Retour","Etat" : true}');
    },
    
    prepare : function (t,id_app)
    {
    	this.instance=t;
    },
    
    // Permet au serveur de demander au client le chargement de certain module
    AddModule: function (name)
    {
    	if(!confirm("Le serveur d'application demande l'autorisation pour charger le module client " + name + " ? ")) return;
    	this.instance.AddModule(name);
    }
    
});