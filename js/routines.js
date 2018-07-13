/**
 *
 * Некоторые глобальные переменные-константы. log_* для понятного вызова ф-ции log, count* - задержка отсчета времени столбца.
 */
var log_err=-1;
var log_debug=0;
var log_log=1;
var log_info=2;
var log_warn=3;

var count1=500;
var count2=940;
var count3=1500;

function refreshMe(){
    location.href = 'http://'+document.domain+'/';
}

/**
 * 
 * @method getUUID
 * Генерирует уникальный идентификатор
 * @returns {String} uuid - уникальный идетификатор
 **/
function getUUID() {
    // http://www.ietf.org/rfc/rfc4122.txt
    var s = [];
    var hexDigits = "0123456789ABCDEF";
    for (var i = 0; i < 32; i++) {
        s[i] = hexDigits.substr(Math.floor(Math.random() * 0x10), 1);
    }
    s[12] = "4";  // bits 12-15 of the time_hi_and_version field to 0010
    s[16] = hexDigits.substr((s[16] & 0x3) | 0x8, 1);  // bits 6-7 of the clock_seq_hi_and_reserved to 01
 
    var uuid = s.join("");
    return uuid;
};
/*
 * @method inArray
 * Поиск совпадений в массиве
 * @param {Object} search   - искомый объект
 * @param {Array} arr       - массив, в котором производится поиск
 * @returns {Boolean}       - тру, если совпадения есть, фолс, если нет
 */
function inArray(search,arr){
	if (arr==undefined) return false;
	for(i=0;i<arr.length;i++){
		if (arr[i]==search) {/*console.log("skip#"+search);*/ return true;};
	}
	return false;
};
/**
 * @method inArrayCnt
 * Подсчитывает количество элементов массива, соответствующих искомой строке
 * @param search {String}   - строка поиска
 * @param arr {Array}       - массив для поиска
 * @return {Number}         - возвращает 0, если не найдено ни одного соответствия, -1, если массив не объявлен
 */
function inArrayCnt(search,arr){
    if (arr==undefined) return -1;
    var itemCount=0;
    for(i=0;i<arr.length;i++){
        if (arr[i].itm.id==search){
            itemCount+=1;
        }
    }
    return itemCount;
}

function getArrBets(arr){
    var ret=0;
    if (arr==undefined) return -1;
    for(i=0;i<arr.length;i++){
        ret=ret+arr[i].bet;
    }
    return ret;
}

function log(s,log_type){
	if (log_type==undefined) log_type=0;
	
	switch (log_type){
		case 0:
			try {
				console.debug(getCurrentTime()+' :: '+s);
                //appendFile('DBG:'+getCurrentTime()+' :: '+s);
			} catch(e){
				console.log(getCurrentTime()+' :: '+s);
                //appendFile('DBG:'+getCurrentTime()+' :: '+s);
			}
			break;
		case 1:
			console.log(getCurrentTime()+' :: '+s);
            //appendFile('LOG:'+getCurrentTime()+' :: '+s);
			break;
		case 2:
			console.info(getCurrentTime()+' :: '+s);
            //appendFile('INF:'+getCurrentTime()+' :: '+s);
			break;
		case 3:
			console.warn(getCurrentTime()+' :: '+s);
            //appendFile('WRN:'+getCurrentTime()+' :: '+s);
			break;
		case -1:
			console.error(getCurrentTime()+' :: '+s);
            //appendFile('ERR:'+getCurrentTime()+' :: '+s);
			break;
	}
};
/*
 * @method idDebugMode
 * Проверяет, включен ли режим отладки, асинхронно вызывая сгенерированный сервером файл debug.php
 * @returns {Boolean}   - тру, если режим отладки включен
 */
function isDebugMode(){
	$.ajax({
	  type: 'POST',
	  url: '/game-root/inc/debug.php?get_dmode=1',
	  success: function(data){
		switch(parseInt(data)){
			case 0:
				return false;
				break;
			default:
				return true;
				break;
		};
	  }/*,
	  error: function() {
        //
		log("An error occured when AJAX request was called in isDebugMode() function in routines.js...",log_err);
    }*/
	});
}
/*
 * @method printDebugInfo
 * Выводит на экран (графический) иформацию о том, что включен режим отладки
 * @returns {undefined}
 */
function printDebugInfo(){
	$.ajax({
	  type: 'POST',
	  url: '/game-root/inc/debug.php?get_dmode=1',
	  success: function(data){
		var txt="";
		switch(parseInt(data)){
			case 0:
				break;
			case 1:
				txt="debug mode ON. level: 1 ";
				break;
			case 2:
				txt="debug mode ON. level: 2 ";
				break;
		};
		a_context.clearRect(60,0,150,14);
		a_context.fillStyle = "#f8b000";
		a_context.font = '10px Courier New';
		//a_context.textAlign="left";
		//a_context.textBaseline="top";		
		a_context.fillText(txt.toString(),200,0);
	  }/*,
	  error: function() {
        //
		log("An error occured when AJAX request was called in printDebugInfo() function in routines.js...",log_err);
    }*/
	});
	
	$.ajax({
	  type: 'POST',
	  url: '/game-root/inc/version.php?short',
	  success: function(data){
		a_context.clearRect(0,666,150,14);
		a_context.fillStyle = "#f8b000";
		a_context.font = '10px Courier New';
		//a_context.textAlign="left";
		//a_context.textBaseline="top";		
		a_context.fillText(data.toString(),70,666);
	  }/*,
	  error: function() {
        //
		log("An error occured when AJAX request was called in printDebugInfo() function in routines.js...",log_err);
    }*/
	});
	
};
/**
 * @method getBetSumm
 * Возвращает сумму выигрыша (либо предидущего выигрыша)
 * @param old {Boolean} - запрашивается старая сумма или новая. по-умолчанию новая
 * @return {Number}     - возвращает сумму выигрыша
 */
function getBetSumm(old){
    if (old==undefined) old=false;
    var summ=0;
    if (old==false){
        for (var key in bets)
        {
            summ+=bets[key];
        }
    } else {
        for (var key in old_bets)
        {
            summ+=old_bets[key];
        }
    }
    return summ;
}

function getCurrChip(el){
    var curr_chip;
    if (bets[el.id]>0 && bets[el.id]<coin[2].bet){
        curr_chip=1;
    } else if (bets[el.id]>=coin[2].bet && bets[el.id]<coin[3].bet) {
        curr_chip=2;
    } else if (bets[el.id]>=coin[3].bet && bets[el.id]<coin[4].bet) {
        curr_chip=3;
    } else if (bets[el.id]>=coin[4].bet && bets[el.id]<coin[5].bet) {
        curr_chip=4;
    } else if (bets[el.id]>=coin[5].bet) {
        curr_chip=5;
    } else {
        curr_chip=-1;
    }
    return curr_chip;
}

function loadStaticImages(){
//
    btnImgArr['exit_dn'] = new Image();
    btnImgArr['exit_dn'].src='img/btn/btn_ext2.png';
    btnImgArr['exit_en'] = new Image();
    btnImgArr['exit_en'].onload = function(){loadButton('exit','en');}
    btnImgArr['exit_en'].src='img/btn/btn_ext1.png';
    btnImgArr['help_dn'] = new Image();
    btnImgArr['help_dn'].src='img/btn/btn_hlp2.png';
    btnImgArr['help_en'] = new Image();
    btnImgArr['help_en'].onload = function(){loadButton('help','en');}
    btnImgArr['help_en'].src='img/btn/btn_hlp1.png';

    $('#btn_exit').attr('enabled','true');
    $('#btn_help').attr('enabled','true');
    $('#btn_undoone').attr('enabled','false');
    $('#btn_undoall').attr('enabled','false');
    $('#btn_replay').attr('enabled', 'false');
    $('#btn_previous').attr('enabled', 'true');

    btnImgArr['undoone_dis'] = new Image();
    btnImgArr['undoone_dis'].onload=function(){loadButton('undoone');}
    btnImgArr['undoone_dis'].src='img/btn/btn_und0.png';
    btnImgArr['undoone_en'] = new Image();
    btnImgArr['undoone_en'].src='img/btn/btn_und1.png';
    btnImgArr['undoone_dn'] = new Image();
    btnImgArr['undoone_dn'].src='img/btn/btn_und2.png';

    btnImgArr['undoall_dis'] = new Image();
    btnImgArr['undoall_dis'].onload=function(){loadButton('undoall');}
    btnImgArr['undoall_dis'].src='img/btn/btn_xbe0.png';
    btnImgArr['undoall_en'] = new Image();
    btnImgArr['undoall_en'].src='img/btn/btn_xbe1.png';
    btnImgArr['undoall_dn'] = new Image();
    btnImgArr['undoall_dn'].src='img/btn/btn_xbe2.png';

    btnImgArr['replay_dis'] = new Image();
    btnImgArr['replay_dis'].onload=function(){loadButton('replay');}
    btnImgArr['replay_dis'].src='img/btn/btn_reb0.png';
    btnImgArr['replay_en'] = new Image();
    btnImgArr['replay_en'].src='img/btn/btn_reb1.png';
    btnImgArr['replay_dn'] = new Image();
    btnImgArr['replay_dn'].src='img/btn/btn_reb2.png';

    btnImgArr['previous_en']=new Image();
    btnImgArr['previous_dn']=new Image();
    btnImgArr['previous_en'].onload=function(){loadButton('previous','en');}
    btnImgArr['previous_en'].src='img/btn/btn_hst1.png';
    btnImgArr['previous_dn'].src='img/btn/btn_hst1.png';
}

function loadButton(id,state){
    if (state==undefined) state='dis';
    if ($('#btn_'+id).attr('shape')=='circle'){
        cw=$('#btn_'+id).attr('coords').split(',')[2];
        cx=$('#btn_'+id).attr('coords').split(',')[0]-cw;
        cy=$('#btn_'+id).attr('coords').split(',')[1]-cw;
    } else if ($('#btn_'+id).attr('shape')=='rect'){
        cx=$('#btn_'+id).attr('coords').split(',')[0];
        cy=$('#btn_'+id).attr('coords').split(',')[1];
    }
    buttonContext.drawImage(btnImgArr[id+'_'+state],cx,cy);
}

function getNumCoord(num){
    if (num<0 || num>36) return;
    var coord={
        x:0,
        y:num * 16,
        w:40,
        h:16
    }
    return coord;
}

function getRandom(min,max){
    if (min==undefined) min=0;
    if (max==undefined) max=100;

    var rand = min + Math.random()*(max+1-min);
    rand = rand^0; // округление битовым оператором
    return rand;
}

function getBetsCount(){
    var cnt=0;
    for (var key in bets){
        cnt++;
    }
    return cnt;
}

function sendBets(){
    try {
        var ret='';
        var retArr=Array();
        for(var i=0;i<37;i++){
            retArr[i]=0;
        }
        console.log(bets);
        for (var key in bets)
        {
            var numbers=Array();
            //numbers=bets[key].numbers.split(",");
            numbers=key.replace('tab','').split('_');
            if (numbers.length>1) {
                var sp_value=bets[key] / (numbers.length);
                for (var i=0;i<numbers.length;i++){
                    retArr[numbers[i]]= +retArr[numbers[i]] + +sp_value;
                }
            } else {
                retArr[numbers[0]]= +retArr[numbers[0]] + +bets[key];
            }
        }
        ret=retArr.join(";");
        socket.send("bets "+ret);
        old_bets=bets;
        oldBetsStack=betsStack;
        //oldwinval=winval;
        oldcr=cr;
    } catch (e){
        log("Error in sendBets() function in application.js;" + e);
    }
}

function isChipValue(el) {
    for (var i=1;i<6;i++){
        if (bets[el.id]==coin[i].bet) return true;
    }
    return false;
}

function getCurrentTime(){
    var date = new Date();
    var time = date.getHours()+':'+date.getMinutes()+':'+date.getSeconds()+date.getMilliseconds();
    return time;
}