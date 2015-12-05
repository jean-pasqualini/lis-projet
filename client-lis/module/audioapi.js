/*
  Module scenejs de rendu 3D cotée client
  ...
*/

var module_audioapi = $.inherit (module_base,{
    
    __construction : function ()
    {

    },
    
    GetName : function()
    {
    	return "AudioApi";
    },
    
    test : function ()
    {
        alert("Test module scenejs client");
    },
    
    action : function (Instruction,id_app,t)
    { 
          if (Instruction.Note=="stop")
          {
            if($(t).attr('log')) { log(Instruction.Type+" > Stop",id_app); }
          }
          else
          {
            play(Instruction.Note);
            if($(t).attr('log')) { log(Instruction.Type+" > "+Instruction.Note,id_app); }
          }
	  socket.send('{"Action" : "Retour","Etat" : true}');
    },
    
    prepare : function (t,id_app)
    {
	
    }
});