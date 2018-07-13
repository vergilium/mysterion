/*
*
*	application.js
*		своего рода "ядро" нашего колеса
*		тут происходит соединение вместе всех модулей
*		терминала, и управление ими.
*
*/

// -----------------------------
// контекст для рисования (экран)
var canvas = document.getElementById('mycanvas');
var context = canvas.getContext('2d');
// вспомогательный контекст для рисования
var canvas = document.getElementById('helpcanvas');
var h_context = canvas.getContext('2d');
// невидимый контекст (скрытый экран)
var canvas = document.getElementById('invcanvas');
var i_context = canvas.getContext('2d');
// контекст для рисовании анимации и всяких элементов
var canvas = document.getElementById('anicanvas');
var a_context = canvas.getContext('2d');
// вспомогательный контекст для рисовании анимации и всяких элементов
var canvas = document.getElementById('helpanicanvas');
var help_a_context = canvas.getContext('2d');
// контекст для рисовании всех фишек на поле
var canvas = document.getElementById('coincanvas');
var c_context = canvas.getContext('2d');
// контекст для рисовании последних выпавших чисел
var canvas = document.getElementById('lastnum');
var ln_context = canvas.getContext('2d');
// контекст для рисования нерабочего поля
var canvas = document.getElementById('disablecanvas');
var dis_context = canvas.getContext('2d');
// контекст для рисования рабочего поля
var canvas = document.getElementById('enablecanvas');
var enb_context = canvas.getContext('2d');
// контекст для рисования предыдущей ставки
var canvas = document.getElementById('prewcanvas');
var previous_context = canvas.getContext('2d');
// контекст для рисования предыдущей ставки
var canvas = document.getElementById('helptextcanvas');
var text_context = canvas.getContext('2d');
// -----------------------------

var bets = Array(); var old_bets = Array();
/*
bets = {
	el,			<- графический элемент
	zone,		<- где поставили (числа или дорожка/race)
	numbers,	<- массив чисел на которые поставили
	betvalue	<- значение ставки
}

*/
var maxbets=25;
var activemode=false;

var nownum=0; winval=0; firstrun=true;

var activeBg = new Image(); var inactiveBg = new Image();

var mchip=Array();
mchip[1] = new Image();
mchip[2] = new Image();
mchip[3] = new Image();
mchip[4] = new Image();
mchip[5] = new Image();

var lnumbers = new Image();
var tmr_id;
var ws; var mousedn=false; var inf_text="Делайте ваши ставки.";
var inf_text_w; var inf_text_pos=-235;

activeBg.onload = function() {
	context.drawImage(activeBg, 0, 0);
	i_context.drawImage(activeBg, 0, 0);
	dis_context.drawImage(inactiveBg, 0, 0);
	enb_context.drawImage(activeBg, 0, 0);
};
activeBg.src = 'img/bg.png';
inactiveBg.src = 'img/bg_dis.png';

mchip[1].src='img/min_chip1.png';
mchip[2].src='img/min_chip2.png';
mchip[3].src='img/min_chip3.png';
mchip[4].src='img/min_chip4.png';
mchip[5].src='img/min_chip5.png';
lnumbers.src='img/lastnum16.png';

setInterval(render_inf_text,100);
printDebugInfo();

/*
if(window.File && window.FileReader && window.FileList && window.Blob) {
  console.log("supports!!");
}
*/

openSocket();

function sendBets(){
try {
	var ret='';
	var retArr=Array();
	for(var i=0;i<37;i++){
		retArr[i]=0;
	}
	
	for (var key in bets)
	{
		//console.log(bets[key].numbers);
		var numbers=Array();
		numbers=bets[key].numbers.split(",");
		
		//console.log(numbers);
		//console.log("numbers.length="+numbers.length);
		if (numbers.length>1) {
			var sp_value=bets[key].betvalue / (numbers.length);
			//console.log("sp_val="+sp_value);
			for (var i=0;i<numbers.length;i++){
				retArr[numbers[i]]= +retArr[numbers[i]] + +sp_value;
			}		
		} else {
			//console.log("betvalue:"+bets[key].betvalue);
			retArr[numbers[0]]= +retArr[numbers[0]] + +bets[key].betvalue;
		}
		
	   //ret=ret+bets[key].numbers+';'+bets[key].betvalue+'|';
	   //log(bets[key]);
	}
	
	//ret=ret.substr(0,ret.length-1);
	ret=retArr.join(";");
	//console.log(retArr);
	socket.send("bets "+ret);
	old_bets=bets;
} catch (e){
	log("Error in sendBets() function in application.js;" + e);
	}
};

function render_bet_text(){
	a_context.clearRect(526,724,76,25);
	//
	a_context.fillStyle = "#f8b000";
	a_context.font = '26px Palatino';
	a_context.textAlign="right";
	a_context.textBaseline="top";
	var summ=0;

	for (var key in bets)
	{
	   summ+=bets[key].betvalue;
	}	

	//if (curr_bet!=-1) a_context.fillText(curr_bet.toString(),602,722);
	a_context.fillText(summ.toString(),602,722);
};

function render_win_text(){
	a_context.clearRect(20,724,94,25);
	//
	a_context.fillStyle = "#f8b000";
	a_context.font = '26px Palatino';
	a_context.textAlign="right";
	a_context.textBaseline="top";
	a_context.fillText(winval.toString(),108,722);	
};

function render_credit_text(){
	a_context.clearRect(336,724,94,25);
	//
	a_context.fillStyle = "#f8b000";
	a_context.font = '26px Palatino';
	a_context.textAlign="right";
	a_context.textBaseline="top";
	a_context.fillText(cr.toString(),428,722);	
};

function render_inf_text(){
	text_context.clearRect(0,0,1024,30);
	text_context.fillStyle = "#f8b000";
	text_context.font = '26px Palatino';
	text_context.fillText(inf_text,2,22);
	var img=text_context.getImageData(inf_text_pos,0,235,30);
	a_context.putImageData(img,625,722);
	//console.log(inf_text_pos);
	inf_text_pos=inf_text_pos+3;
	if ((inf_text_pos-117)>235) inf_text_pos=-235;
};

/*function tryConnect(){
	//console.warn(ws.readyState);
	if (ws.readyState==0 || ws.readyState==3) {
		ws = new WebSocket("ws://192.168.10.13:16669");
		//console.warn('try reconnect');
		tmr_id=setTimeout('tryConnect();', 5000);
	}
};*/

function addNumber(num){
var w=32, h=num*16;
var y=nownum*18, x=0;
// 26px +
// 17 pcx
// 44 x 314
switch (parseInt(num,10)){
	case 0:
		x=6;
	break;
	case 1:
	case 3:
	case 5:
	case 7:
	case 9:
	case 12:
	case 14:
	case 16:
	case 18:
	case 19:
	case 21:
	case 23:
	case 25:
	case 27:
	case 30:
	case 32:
	case 34:
	case 36:
		x=10;
	break;
}
//console.warn(x);
// 18!!!
h_context.drawImage(activeBg,0,0);
img_bg_orig = h_context.getImageData(4, 8, 44, 22);
h_context.clearRect(0,0,1024,768);

if (nownum>16) {
	img_bg = context.getImageData(4, 22, 44, 306);
} else {
	img_bg = context.getImageData(4, 4, 44, 306);
}

// получили цифру-изображение
h_context.drawImage(lnumbers,0,0);
img = h_context.getImageData(0, h, 32, 16);
/////////////////////////////

//ln_context.putImageData(img_bg_orig,0,0);
if (nownum>16) {
	ln_context.putImageData(img_bg,0,0);
	ln_context.putImageData(img_bg_orig,0,286);
	ln_context.putImageData(img,x,288);
	img = ln_context.getImageData(0,0,44,306);
} else {
	ln_context.putImageData(img_bg,0,0);
	ln_context.putImageData(img,x,y);
	img = ln_context.getImageData(0,0,44,306);
}
i_context.putImageData(img,4,4);
dis_context.putImageData(img,4,4);
enb_context.putImageData(img,4,4);
refreshScreen();
/*
context.lineWidth = 1;
context.strokeStyle = 'whitesmoke';		
context.strokeRect(4,4,44,306);
context.stroke();
*/
nownum++;
};
/*
function possible(){
	if (bet_num)
}*/
function loadScreen(state){
if(state==true){
		//old_bets=bets;
		var img = i_context.getImageData(0, 0, 1024, 768);
		previous_context.putImageData(img,0,0);
		
		bets=new Array();
		//context.drawImage(activeBg, 0, 0);
		//i_context.drawImage(activeBg, 0, 0);
		//dis_context.drawImage(activeBg, 0, 0);
		var img = enb_context.getImageData(0, 0, 1024, 768);
		i_context.putImageData(img,0,0);
		//h_context.clearRect(0,0,1024,620);
		if (winval>0) {
			cr=cr + +winval;
			winval=0;
			a_context.clearRect(20,724,94,25);
		}
		a_context.clearRect(75,20,950,600);
		render_bet_text();
		render_credit_text();
		inf_text="Делайте ваши ставки.";
	} else {
		//context.drawImage(inactiveBg, 0, 0);
		//i_context.drawImage(inactiveBg, 0, 0);
		var img = dis_context.getImageData(0, 0, 1024, 768);
		context.putImageData(img,0,0);
		i_context.putImageData(img,0,0);
		
		inf_text="Ставки приняты.";
	}
	
	inf_text_w = text_context.measureText(inf_text).width;
	refreshScreen();
};

function button_click(btnName,nofcell){
console.log(btnName);

if (btnName.indexOf('coin')>0){
	sel_coin=btnName.replace('btn_','').replace('coin_','').replace('x','0');
	//console.log('coin click:'+sel_coin);
	if (cr>=coin[sel_coin].bet){
		for (i=0;i<6;i++){
			if (i!=sel_coin) coin[i].enable(false);
		}
		if (coin[sel_coin].enabled==false) coin[sel_coin].enable(true);
		curr_bet=coin[sel_coin].bet;
		
	};
	render_bet_text();
	render_credit_text();
}
//
switch(btnName){
	case 'btn_previous':
	try {
		if (old_bets!=undefined && old_bets.length>0){
			var img=previous_context.getImageData(0, 0, 1024, 768);
			context.putImageData(img,0,0);
		}
	} catch(e){}
	break;
	case 'btn_exit':
		socket.send("logoff");
		$.ajax({
			type: "POST",
			url: "/game-root/inc/auth.php?action=exit",
			dataType: "script",
			success: function(data) {
				//
			},
			error: function() {
				//
				log("An error occured when AJAX request was called in button_click() function (application.js)...",log_err);
			}
		});	
	break;
	case "btn_replay":
	break;
	case "btn_undoall":
	break;
	case "btn_undoone":
	break;
	case "btn_f5":
		setTimeout(function(){
			//alert("RE!");
			location.href = 'http://'+document.domain+'/';
		}, 500);
	break;
}
};

function refreshScreen(){
	printDebugInfo();
	img = i_context.getImageData(0, 0, 1024, 768);
	context.putImageData(img,0,0);
};

function minusCredit(nofcell){
	if (cr>0 && cr-curr_bet>=0) cr=cr-curr_bet;
	var flag=false;
	
		for (i=5;i>=3;i--){

				if (coin[i].bet>cr) {
					coin[i].enable(false);
				} else if (coin[i].bet<=cr) {
					//curr_bet=coin[i].bet;
				}

		}
	
	//console.log('credit:='+cr);
	render_bet_text();
	render_credit_text();
};

// ******************
// Ф-ция рисования на контексте фишки

function drawCoin(el){
var cO=el.coords.split(",");
	switch (el.shape){
		case 'rect':
			var c={	// rectangle:
				x1:cO[0],
				y1:cO[1],
				x2:cO[2],
				y2:cO[3]
			}		
			var zx=+c.x2 - ((+c.x2-+c.x1)/2) - 16;
			var zy=+c.y2 - ((+c.y2-+c.y1)/2) - 16;
			
			if (bets[el.id]!=undefined){
			
			if (bets[el.id]['betvalue']>0 && bets[el.id]['betvalue']<coin[2].bet){
				curr_chip=1;
			} else if (bets[el.id]['betvalue']>=coin[2].bet && bets[el.id]['betvalue']<coin[3].bet) {
				curr_chip=2;
			} else if (bets[el.id]['betvalue']>=coin[3].bet && bets[el.id]['betvalue']<coin[4].bet) {
				curr_chip=3;
			} else if (bets[el.id]['betvalue']>=coin[4].bet && bets[el.id]['betvalue']<coin[5].bet) {
				curr_chip=4;
			} else if (bets[el.id]['betvalue']>=coin[5].bet) {
				curr_chip=5;
			} else {
				curr_chip=-1;
			}			
			
			if (bets[el.id]==undefined){	
				img = enb_context.getImageData(c.x1, c.y1, c.x2-c.x1, c.y2-c.y1);		
				//h_context.putImageData(img, 0, 0);
				i_context.putImageData(img,c.x1,c.y1);
				context.putImageData(img,c.x1,c.y1);			
				return;
			}
			
			//img = i_context.getImageData(c.x1, c.y1, c.x2-c.x1, c.y2-c.y1);
						
			//h_context.putImageData(img, 0, 0);
			a_context.clearRect(zx,zy,32,32);
			//a_context.strokeRect(zx,zy,32,32);
			//console.log("x:"+zx+" y:"+zy);
			a_context.drawImage(mchip[curr_chip],zx,zy);
			a_context.fillStyle = "#00F";
			a_context.font = "bold normal 12pt AGFriquer";
			
			//if (bets[el.id]['betvalue']>9){ var w=8} else {var w=4};
			w=(zx+16)+(a_context.measureText(bets[el.id]['betvalue']).width/2);
			//console.log(w);
			a_context.fillText(bets[el.id]['betvalue'], w, zy+6);
			//console.log(c);
			//img = h_context.getImageData(0, 0, c.x2-c.x1, c.y2-c.y1);
			}
			//i_context.putImageData(img,c.x1,c.y1);
			//context.putImageData(img,c.x1,c.y1);
			//--------------------------------------------------------------
			//img = dis_context.getImageData(c.x1, c.y1, c.x2-c.x1, c.y2-c.y1);
						
			//h_context.putImageData(img, 0, 0);
			//h_context.drawImage(mchip[curr_chip],((c.x2-c.x1)/2)-18,(( c.y2-c.y1)/2)-18);
			//h_context.fillStyle = "#00F";
			//h_context.font = "bold normal 12pt AGFriquer";
			
			//if (bets[el.id]['betvalue']>9){ var w=8} else {var w=4};
			//h_context.fillText(bets[el.id]['betvalue'], w, (( c.y2-c.y1)/2)+5);
						
			//img = h_context.getImageData(0, 0, c.x2-c.x1, c.y2-c.y1);
			
			//dis_context.putImageData(img,c.x1,c.y1);
			
		break;
		
		case 'poly':
			var h_id='h'+el.id.replace('tab','').replace('race','');
			var helpel=document.getElementById(h_id);
			if(helpel==null) return;
			
			var c=helpel.coords.split(",");
			context.lineWidth = 1;
			context.strokeStyle = 'whitesmoke';		
			context.strokeRect(c[0], c[1], c[2]-c[0], c[3]-c[1]);
			context.stroke();				
			
			img = i_context.getImageData(c[0], c[1], c[2]-c[0], c[3]-c[1]);
			h_context.putImageData(img, 0, 0);
			h_context.drawImage(mchip[curr_chip],((c[2]-c[0])/2)-18,(( c[3]-c[1])/2)-18);
			h_context.fillStyle = "#00F";
			h_context.font = "bold normal 12pt AGFriquer";
			//if (bets[el.id]['betvalue']>9){ var w=8} else {var w=4};
			
			w=((c[2]-c[0])/2)-(h_context.measureText(bets[el.id]['betvalue']).width/2);
			
			h_context.fillText(bets[el.id]['betvalue'], w, (( c[3]-c[1])/2)+5);
			img = h_context.getImageData(0, 0, c[2]-c[0], c[3]-c[1]);
			i_context.putImageData(img,c[0],c[1]);
			context.putImageData(img,c[0],c[1]);	
			
		break;
	}	
};

function addBet(el){
	/*
	var bet_num=el.id.replace("tab","").split("_");
	for (n=0;n<betnum.length;n++){
		if (curr_bet!=-1){
			if (bets[n]==undefined){
				bets[n]=curr_bet / bet_num.length;
			} else {
				if (bets[n]*bet_num.length<=maxbets && bets[n] + curr_bet / bet_num.length<=maxbets){
					bets[n]=bets[n]+curr_bet / bet_num.length;
				}
			}
		}
	}
	*/
};

// разгребсти!!!