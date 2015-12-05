/*
  Module scenejs de rendu 3D cotée client
  ...
*/

var module_scenejs = $.inherit (module_base,{
    
    __construction : function ()
    {
        this.canvas = $("#theCanvas");
    },
    
    test : function ()
    {
        alert("Test module scenejs client");
    },
    
    action : function (Instruction,id_app,t)
    { 
            switch(Instruction.Action)
	    {
		case "IMPORT":
		    if($(t).attr('log')) { log (Instruction.Type+" > Importation model 3D",id_app); }
		    eval($.base64Decode(Instruction.Data));
		    dd();
		    SceneJS.withNode("myScene").render();
		    break;
		
		case "Position2d":
		    if($(t).attr('log')) { log (Instruction.Type+" > Changement position 3D",id_app); }
		    SceneJS.withNode("pitch").set("angle", Instruction.X);
		    SceneJS.withNode("yaw").set("angle", Instruction.Y);
		    SceneJS.withNode("myScene").render();
		    break;
		
		default:
		    if($(t).attr('log')) { log (Instruction.Type+" > Action inconu",id_app); }
		    break;
	    }
            socket.send("OK");
    },
    
    prepare : function (t,id_app)
    {
    $(t).after('<div id="'+id_app+'_autre" style="width: 1024px; text-align: center; margin: 10px; margin-left:auto; margin-right:auto; background: transparent;">'
      +'<canvas id="theCanvas" width="1030" height="700" style="margin-left:auto; margin-right:auto;"></canvas> '
    +'</div>');
    }
});