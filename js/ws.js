/*
*******************************************
*
*	ws.js -- класс для работы с веб-сокетом
*
*/
var socket;
var tmr_id=0;
var recon_int=15000; recon_num=3; recon_cnt=0;
/**
 * Устанавливает подключение к веб-сокет серверу
 * @param protocol {String} - протокол подключения (безопасный/не безопасный). по-умолчанию не безопасный.
 */
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
			log("Connection success!");
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

/**
 * Событие удачного подключения к серверу
 */
function sockOpened(){
	socket.send("init "+$("#trm").val());
};

/**
 * Событие закрытия подключения
 * @param event {Object}
 */
function sockClosed(event){

};
/**
 * Событие получения данных от сервера
 * @param event {Object}
 */
function sockRecieved(event){
	var cmd = event.data.split(" ");
	console.log("Recivied: "+event.data);
	switch (cmd[0])
	{
		case "number":
			log('number event ['+cmd[1]+']');
			addNumber(cmd[1]);
            /*setInterval(function(){
                changeGameState(true);
            },5000)*/
			break;
		case "endisp":
			log('enable display');
            rmAllBet(true);
            reDrawBets();
            changeGameState(true);
            startCountdown();
            break;
		case "disdisp":
			log('disable display');
            changeGameState(false);
			sendBets();
			break;
		case "win":
			var winval=cmd[1];
            winEvent(winval);
			log("winval="+winval);
			break;
		case "cr":
            cr= +cmd[1];
			break;
		case "refresh":
			location.href = 'http://'+document.domain+'/';
			break;
		case "ping":
			socket.send("pong");
			break;
        case "sysmsg":
            alert("Server message:\n"+event.data.replace("sysmsg ",""));
            break;
        case "close":
            if (socket!=undefined) {
                socket.send("logoff");
                socket.close();
            }
            $.ajax({
                type: "POST",
                url: "/game-root/inc/auth.php?action=exit",
                dataType: "script",
                success: function(data) {
                    //
                },
                error: function() {
                    //
                    log("An error occured when AJAX request was called in sockRecieved() function (ws.js)...",log_err);
                }
            });
            break;
        case "server_down":
            show_msg('Отсутствует связь с основным сервером. Попробуйте позже.<br /><input type="button" value="Обновить" onclick="refreshMe();" />');
            break;
        case "server_up":
            hide_msg();
            break;
        case "init":
            //log(cmd[1]);
            var sub_cmd=cmd[1].split(';');
            console.log(sub_cmd);
            if (sub_cmd[0]=='endisp') {
                changeGameState(true);
            }else{
                changeGameState(false);
            }

            var nms=sub_cmd[1].split(',');
            console.log(nms);

            for (var i=12;i>=0;i--){
                addNumber(+nms[i]);
            }
            break;
	}
};

/**
 * Событие ошибки подключения
 * @param error {Object}
 */
function sockErr(error){
	//show_msg (error);
};

/**
 * Процедура попытки переподключения к серверу.
 * Количество попыток переподключения задаётся в переменной recon_num
 */
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