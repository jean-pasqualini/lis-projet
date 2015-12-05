/*
  Module scenejs de rendu 3D cotée client
  ...
*/

var module_lisrender = $.inherit (module_base,{
    
    __construction : function ()
    {

    },
    
    test : function ()
    {
        alert("Test module scenejs client");
    },
    
    action : function (Instruction,id_app,t)
    {
	    
            if($(t).attr('log')) { log (Instruction.Type+" > LISRENDER BASE64",id_app); }

            $("#"+id_app+"_renderphp").attr("src","data:image/jpg;base64,"+Instruction.Data,id_app);
	    socket.send("OK");
    },
    
    prepare : function (t,id_app)
    {
    $(t).after('<div id="'+id_app+'_autre" style="width: 1024px; text-align: center; margin: 10px; margin-left:auto; margin-right:auto;">'
      +'<img src="planeDiffuse.png" id="'+id_app+'_renderphp">'
    +'</div>');
    }
});