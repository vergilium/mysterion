/*
*******************************************
*
*	ws.js -- класс для работы с веб-сокетом
*
*/
var socket;
var tmr_id=0;
var recon_int=15000; recon_num=3; recon_cnt=0;

function openSocket(protocol){
	if (protocol==undefined) {
		protocol='ws://'}
	else {
		protocol='wss://'};
	
	var cli_ip=$("#c_ip").val();
	var srv_ip=$("#s_ip").val();
	var prt=$("#s_port").val();	
	
	var constr=protocol+srv_ip+':'+prt;
	
	if(typeof(WebSocket)=="undefined") { 
	   // если не поддерживает - сообщаем об этом пользователю 
	   show_msg("Your browser does not support WebSockets. Try to use Chrome or Safari."); 
	   log("ClientBrowserDoesNotSupportWebsocket Error",log_err);
	} else { 
		socket=new WebSocket(constr);
		
		socket.onopen = function() { 
			log("Connection success.");
			clearInterval(tmr_id);
			clearTimeout(tmr_id);
			sockOpened();
			hide_msg();
		};

		socket.onclose = function(event) { 
			if (event.wasClean) {
				log('Connection closed clear');
			} else {
				tmr_id=setTimeout('tryConnect();', recon_int);
				log('Disconnected...'); // например, "убит" процесс сервера
			}
			log('Code: ' + event.code + ' reason: ' + event.reason);
			if (event.code==1006){
				show_msg("Ошибка подключения. Попытка восстановления подключения ["+recon_cnt+"]");
			}
			sockClosed(event);
		};
		 
		socket.onmessage = function(event) { 
			log("Recivied data: " + event.data);
			sockRecieved(event);
		};

		socket.onerror = function(error) { 
			log("Error " + error.message);
			sockErr(error);
		};
		
	}
};

function sockOpened(){
	socket.send("init "+$("#trm").val());
};

function sockClosed(event){

};

function sockRecieved(event){
	var cmd = event.data.split(" ");
	console.log("Recivied: "+event.data);
	switch (cmd[0])
	{
		case "number":
			log('number event ['+cmd[1]+']');
			addNumber(cmd[1]);
			break;
		case "endisp":
			log('enable display');
			activemode=true;
			if (firstrun==true){
				loadScreen(activemode);
				firstrun=false;
			} else {
				setTimeout(function(){
					loadScreen(activemode);
				},2000);			
			}
			break;
		case "disdisp":
			log('disable display');
			activemode=false;
			loadScreen(activemode);
			sendBets();
			break;
		case "win":
			winval=cmd[1];
			render_win_text();
			log("winval="+winval);
			log("cmd[1]="+cmd[1]);
			break;
		case "cr":
			cr= +cmd[1];

			render_credit_text();
			break;
			
		case "refresh":
			location.href = 'http://'+document.domain+'/';
			break;
		case "ping":
			socket.send("pong");
			break;
	}
};

function sockErr(error){
	//show_msg (error);
};

function tryConnect(){
	clearInterval(tmr_id);
	clearTimeout(tmr_id);

	console.log(recon_cnt);
	if (recon_cnt>=recon_num) {
		show_msg('Не удаётся восстановить соединение. Обратитесь к администратору. <a href="javascript:LogOff();">Выйти.</a>');
		return;
		}
	recon_cnt=recon_cnt+1;
	
	if (socket==undefined){
		openSocket();
		return;
	};
	if (socket.readyState==0 || socket.readyState==3) {
		openSocket();
		tmr_id=setTimeout('tryConnect();', recon_int);
	}
};