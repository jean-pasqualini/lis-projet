// note, io(<port>) will create a http server for you
var spawn = require("child_process").spawn, child;
var sleep = require('sleep');

child = spawn('php', ['canvas.php', '1701']);

child.stdout.on('data', function(data){
    console.log('' + data);
});

child.stderr.on('data', function(data){
    console.log('' + data);
});

child.on('close', function(code)
{
console.log("#######Fermeture de l'application");
});

var net = require("net");

var client = new net.Socket();

var io = require('socket.io')(1601);

io.on('connection', function (socket) {


client.connect(1701, '127.0.0.1', function()
{
    console.log('Connected');
});

  console.log("VOUS ETES CONNECTER");

  socket.on('evenement', function(message)
  {
  		client.write(""+message);
  });

  client.on("data", function(data)
  {
     console.log("php : ");
     var data = ""+data;
     var data = data.split('\r\n');
     
     data.forEach(function(dataItem)
     {
     	console.log(":"+dataItem+":");
     	if(dataItem.length != 0)
     	{
	     	var dataItemParsed = JSON.parse(dataItem);
	     	io.emit('instruction', dataItemParsed);
     	}
     });
     //console.log(typeof data);
     //io.emit('instruction', data);
  });
/**
  io.emit('instruction', { 
  	Type: 'Canvas',
  	Action : 'TextFill',
  	value: 'moiii',
  	X: 10,
  	Y: 10
  });
*/
  socket.on('private message', function (from, msg) {
    console.log('I received a private message by ', from, ' saying ', msg);
  });

  socket.on('disconnect', function () {
    io.emit('user disconnected');
  });
});
