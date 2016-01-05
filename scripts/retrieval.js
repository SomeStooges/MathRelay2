function helloWorld(someMessage){
	console.log(someMessage);
}

//setRelayOption automatically passes the newly added variable back
function setRelayOption(name,value,callback){
	obj = new Object;
	obj.name = name;
	obj.value = value;
	obj.action = "setRelayOption";
	$.post('/MathRelay/server/Admin.php',obj,function(data){
		data = JSON.parse(data);
		callback(data);
	});
}

//getRelayOption returns value within callback
function getRelayOption(name,callback){
	obj = new Object;
	obj.name = name;
	obj.action = "getRelayOption";
	$.post('/MathRelay/server/Admin.php',obj,function(data){
		data = JSON.parse(data);
		callback(data);
	});
}