var socket;
var ips=0;
/*
  Configuration cliente
*/
var configuration=new Array();
configuration["module"]=new Array();
configuration["module"]["view"]=new Array("canvas");
//configuration["module"]["audio"]=new Array("audioapi");


/*
  Extension jquery pour utiliser une nouvelle balise 'application'
  pour appeler le client lis 
  @author Jean pasqualini <jpasqualini75@gmail.com>
  @version InDev
*/
var lisclient = $.inherit ({
    
/*
 Contructeur de l'application
 @param Dom Balise application
 @returns Retourne l'instance de lisclient
*/
__constructor : function (application)
    {
		this.application=application;
		var module = new Array();
		this.module = module;
		// Verifie si l'url respecte le format 
		var reg = new RegExp("app://([0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}):([0-9]{1,5})/(.*)","g");
		//var url="app://192.168.10.239:12345/chat";
		var url=$(this.application).attr("src");
		if(url.match(reg))
		{
		    // Stocke l'addresse le port et le nom de l'applicaiton dans un tableau app_info
		    var app_info=reg.exec(url);
		}
		else {
		    alert("Application "+$(this.application).attr("id")+" : Url non conforme");
		    return this.application;
		}
		
		// merge default and user parameters
		// params = $.extend( {minlength: 0, maxlength: 99999}, params);
                /* Prend les parametre du champ si définit */
                   
  var host = "ws://"+app_info[1]+":"+app_info[2]+"/"+app_info[3];
  try{
   var etat=0;
   
   /*
    A voir pour mettre apres la creation du socket
   */

  /*
   Chargement dynamique des module en cours d'elaboration
  */
  var id_app=$(this.application).attr("id"); // permet de recuper l'id application
  this.id_app=id_app;
  
    for (y in configuration["module"])
    {
		for (x in configuration["module"][y])
		{
		    this.AddModule(configuration["module"][y][x]);
		    break;
		}
    }
   
    socket = new WebSocket(host);
    //log('WebSocket - status '+socket.readyState);
    var t=$(this.application); // permet de globaliser this dans la variable t

    
    socket.onopen    = function(msg){
      etat=1;
      log("Welcome - status "+this.readyState,id_app);
      // Ajoute un cadre pour les log si l'atribue log de la balise aplication est égale a true
      if($(t).attr('log'))
      {
	      var s=document.createElement("div");
	      $(s).attr("id","log");
	      $(s).html('<div id="'+id_app+'_log" style="width: 100%; height: 215px; overflow: auto;padding: 5px;background: transparent;"></div>'
		       +'<div id="'+id_app+'_info" class="ui-corner-all" style="width: 100%; height: 20px;background: black; color: #8AFD6C; text-align: center;"></div>');
	      
	      $(s).dialog({title: "Historique des instruction"});
	      // Met a jour le nombre d'instruction de tout type recus par seconde
	       window.setInterval(function () {
			$("#"+id_app+"_info").html("<B>"+ips+" instruction par seconde</B>");
			ips=0;
			},1000);
      }
      // Permetera au module de preparer leur terrain
      for (x in module)
      {
    	  module[x].prepare(t,id_app);
      }
     
      };
            
      // Intercepte les message recus
    socket.onmessage = function(msg){
    $("#"+id_app+"_log").scrollTop($("#"+id_app+"_log").attr('scrollHeight'));
     ips++;
      
	// Parse les donée json puis les stocke formaté dans la variable Instruction 
	var Instruction = $.parseJSON(msg.data);
	module[Instruction.Type].action(Instruction,id_app,t);
      };
      
    socket.onclose   = function(msg){
      if (etat==0) { alert("Le serveur d'appplication ne repond pas"); }
      if($(t).attr('log')) {  log("Disconnected - status "+this.readyState,id_app); }
      };
      
  }
  catch(ex){ alert(ex); }
		

return true;
    },
    AddModule: function (name)
    {
	    eval( $.ajax({ url: "module/"+name+".js",  async: false }).responseText);
	    module_temp=new window["module_"+name](this.id_app);
	    this.module[module_temp.GetName()] = module_temp;
    }
    
});


var application=new Array();
$(document).ready(function() {
    
    $("div.fenetre").dialog({
                          width: 1100
                          });
    
    $("application").each(function ()
    {
    	application[$(this).attr("id")] = new lisclient(this);
    });
});


function quit(){
  log("Goodbye!");
  socket.close();
  socket=null;
}

function log(msg,id_app){
	$("#"+id_app+"_log").append("<br>"+msg);
  }
  


