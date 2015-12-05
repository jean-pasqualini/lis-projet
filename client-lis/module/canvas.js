/*
  Module canvas de rendu 2D cotée client
  ...
*/

var module_canvas = $.inherit (module_base,{
    
    __constructor : function (id_app)
    {
	this.id_app=id_app;
	this.gradients=new Array();
	this.positionX=0;
	this.positionY=0;
	this.ismouseover=false;
    },
    
    GetName : function()
    {
    	return "Canvas";
    },
    
    test : function ()
    {
        alert("Test module canvas client");
    },
    
    action : function (Instruction,id_app,t)
    {
	    switch(Instruction.Action)
	    {
		// Commence la bufferisation du dessin
		case "beginPath":
		    this.context.beginPath();
		    break;
		
		// Ecrit le buffer et l'efface
		case "closePath":
		    this.context.closePath();
		    break;
		
		// Dessine un rectangle rempli (x, y, largeur, hauteur)
		case "FillRect":
		    if($(this.instance).attr('log')) { log (Instruction.Type+" > Ajout d'un rectangle plein",id_app); }
		    this.context.fillRect(Instruction.X, Instruction.Y, Instruction.W, Instruction.H);
		    break;
		
		// Dessine un contour rectangulaire (x, y, largeur, hauteur)
		case "StrokeRect":
		    if($(this.instance).attr('log')) { log (Instruction.Type+" > Dessine un contour rectangulaire",id_app); }
		    this.context.fillRect(Instruction.X, Instruction.Y, Instruction.W, Instruction.H);
		    break;
		
		// Efface la zone spécifiée et le rend complètement transparent (x, y, largeur, hauteur)
		case "ClearRect":
		    if($(this.instance).attr('log')) { log (Instruction.Type+" > Efface la zone spécifiée et le rend complètement transparent",id_app); }
		    this.context.clearRect(Instruction.X, Instruction.Y, Instruction.W, Instruction.H);
		    break;
		
		// Parametre la couleur de remplicage
		case "FillStyle":
		    if($(this.instance).attr('log')) { log (Instruction.Type+" > Changement couleur fond",id_app); }
		    if (Instruction.Gradient)
		    {
			this.context.fillStyle = this.gradients[Instruction.value];
		    }
		    else
		    {
			this.context.fillStyle = Instruction.value;
		    }
		    break;
		
		// Parametre la couleur de contour
		case "StrokeStyle":
		    if($(this.instance).attr('log')) { log (Instruction.Type+" > Changement couleur contour",id_app); }
		    if (Instruction.Gradient)
		    {
			this.context.strokeStyle = this.gradients[Instruction.value];
		    }
		    else
		    {
			this.context.strokeStyle = Instruction.value;
		    }
		break;
		
		// Deplace le curseur courant a une position x , y 
		case "MoveTo":
		    if($(this.instance).attr('log')) { log (Instruction.Type+" > Deplace le curseur courant a une position x , y",id_app); }
		    this.context.moveTo(Instruction.X,Instruction.Y);
		break;
	    
		// Dessine un Quadratic curve
		case "QuadraticCurveTo":
		    if($(this.instance).attr('log')) { log (Instruction.Type+" > Dessine un Quadratic curve",id_app); }
		    this.context.quadraticCurveTo (Instruction.Cp1x,Instruction.Cp1y,Instruction.X,Instruction.Y);
		break;
	    
		// Dessine un bezier curve
		case "BezierCurveTo":
		    if($(this.instance).attr('log')) { log (Instruction.Type+" > Dessine un bezier curve",id_app); }
		    this.context.bezierCurveTo (Instruction.Cp1x,Instruction.Cp1y,Instruction.Cp2x,Instruction.Cp2y,Instruction.X,Instruction.Y);
		break;
	    
		// Dessine un cercle (x, y, rayon, startAngle, endAngle, gauche);
		case "Arc":
		    if($(this.instance).attr('log')) { log (Instruction.Type+" > Dessine un cercle",id_app); }    
		    this.context.arc(Instruction.X,Instruction.Y, Instruction.Rayon, Instruction.StartAngle, 
			    Instruction.EndAngle,Instruction.Gauche);
		    this.context.fill();
		    this.context.stroke();
		break;
		
		// Cree un Dégradée lineaire
		case "CreateLinearGradient":
		    if($(this.instance).attr('log')) { log (Instruction.Type+" > Cree un Dégradée lineaire",id_app); }
		    this.gradients[Instruction.Gradient]=this.context.createLinearGradient(Instruction.X1,Instruction.Y1,Instruction.X2,Instruction.Y2);
		break;
	    
		// Cree un Dégradée circulaire
		case "CreateRadialGradient":
		    if($(this.instance).attr('log')) { log (Instruction.Type+" > Cree un Dégradée circulaire",id_app); }
		    this.gradients[Instruction.Gradient]=this.context.createRadialGradient(Instruction.X1,Instruction.Y1,Instruction.StartRayon,Instruction.X2,Instruction.Y2,Instruction.EndRayon); 
		break;
	    
		// Ajoute des couleur a un degradée
		case "AddColorToGradient":
		    if($(this.instance).attr('log')) { log (Instruction.Type+" > Ajoute des couleur a un degradée",id_app); }
		    this.gradients[Instruction.Gradient].addColorStop(Instruction.Offset, Instruction.value);
		break;
		
		// Change la police et la taille (10px sans-serif)
		case "font":
		    if($(this.instance).attr('log')) { log (Instruction.Type+" > Change la police et la taille",id_app); }
		    this.context.font=Instruction.value;
		break;
		
		// Ecrit un texte avec un fond
		case "TextFill":
		    if($(this.instance).attr('log')) { log (Instruction.Type+" > Ecrit un texte avec un fond",id_app); }
		    this.context.fillText(Instruction.value,Instruction.X,Instruction.Y);
		break;
		
		// Ecrit un contour de texte
		case "TextStroke":
		    if($(this.instance).attr('log')) { log (Instruction.Type+" > Ecrit un contour de texte",id_app); }
		    this.context.strokeText(Instruction.value,0,1);
		break;
		
		// Parametre la largeur du canvas
		case "SetWidth":
		    if($(this.instance).attr('log')) { log (Instruction.Type+" > Parametre la largeur du canvas",id_app); }
		    this.canvas.width=Instruction.value;
		break;
		
		// Parametre la hauteur du canvas
		case "SetHeight":
		    if($(this.instance).attr('log')) { log (Instruction.Type+" > Parametre la hauteur du canvas",id_app); }
		    this.canvas.height=Instruction.value;
		break;
		
		// Parametre la composante alpha global 0.0 a 1.0
		case "SetGlobalAlpha":
		    if($(this.instance).attr('log')) { log (Instruction.Type+" > Parametre la composante alpha global 0.0 a 1.0",id_app); }
		    this.context.globalApha=Instruction.value;
		break;
		
		// Parametre l'operation composite
		case "SetCompositeOperation":
		    /*
		     source-over , source-in , source-out , source-atop , destination-over , destination-in
		     destination-out , destination-atop , lighter , copy , xor
		    */
		    if($(this.instance).attr('log')) { log (Instruction.Type+" > Changement de composite",id_app); }
		    this.context.globalCompositeOperation=Instruction.value;
		break;
		
		// Paramatre la largeur des ligne 
		case "SetLineWidth":
		    if($(this.instance).attr('log')) { log (Instruction.Type+" > Paramatre la largeur des ligne 0.0 a 1.0",id_app); }
		    this.context.lineWidth=Instruction.value;
		break;
	    
		case "GetPosition":
		    if($(this.instance).attr('log')) { log (Instruction.Type+" > Envoie la position de la souris",id_app); }
		    this.context.lineWidth=Instruction.value;
		    socket.send('{"X": "'+this.positionX+'","Y": "'+this.positionY+'"}');
		    return;
		break;
		
		
		default:
		    if($(this.instance).attr('log')) { log (Instruction.Type+" > Action inconu",id_app); }
		break;
	    }
            socket.send('{"Action" : "Retour","Etat" : true}');
    },
    
    prepare : function (t,id_app)
    {
	$(t).after('<canvas id="canvas'+id_app+'" style="text-align: center; margin: 10px; margin-left:auto; margin-right:auto;">'
	  +'<h3>Canvas non supportée</h3>'
	+'</canvas>');
	var example = document.getElementById("canvas"+id_app)
	this.canvas=example;
	
	$(example).mousemove(this,this.mousemove);
	$(example).click(this.mouseclick);
	$(example).dblclick(this.mousedblclick);
	
	$(document).keypress(this,this.eventkey);
	$(example).mouseover(this,this.mouseover);
	$(example).mouseout(this,this.mouseout);
	
	this.context = example.getContext('2d');
	this.context.fillStyle = "rgb(255,0,0)";
	this.instance=t;
    },
    
    mouseover : function(e)
    {
	e.data.ismouseover=true;
    },
    
    mouseout : function(e)
    {
	e.data.ismouseover=false;
    },
    
    eventkey : function(e)
    {
	if(e.data.ismouseover)
	{
	    e.data.keypress(e)
	}
	
    },
    
    mousemove : function (e)
    {
	var position = $(e.data.canvas).offset();
	e.pageX-=position.left;
	e.pageY-=position.top;
	
	var pageCoords = "( " + (e.pageX) + ", " + (e.pageY) + " )";
	$("#LBL_pos").html("( e.pageX, e.pageY ) - " + pageCoords);
	
	if(e.data.positionX!=Math.round(e.pageX) && e.data.positionY!=Math.round(e.pageY))
	{
	    e.data.positionX=Math.round(e.pageX);
	    e.data.positionY=Math.round(e.pageY);
	    
	    socket.send($.toJSON({
		"Action" : "Evenement",
		"Module": "InterfaceKM",
		"Method": "MouseMove" ,
		"Params" : {
				"X" : Math.round(e.pageX),
				"Y" : Math.round(e.pageY)
			      }
		}));
	}
    },
    
    mouseclick : function (e)
    {    
	    socket.send($.toJSON({
		"Action" : "Evenement",
		"Module": "InterfaceKM",
		"Method": "MouseClick"
		}));  
	
    },
    
    mousedblclick : function (e)
    {
	    
	    socket.send($.toJSON({
		"Action" : "Evenement",
		"Module": "InterfaceKM",
		"Method": "MouseDblClick"
		}));  
	
    },
    
    keypress : function(e)
    {
	    socket.send($.toJSON({
		"Action" : "Evenement",
		"Module": "InterfaceKM",
		"Method": "KeyPress",
		"Params" : {
				"Code" : e.charCode
			   }
		}));  
    }
    
});