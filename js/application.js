/*
 *
 *	application.js
 *		своего рода "ядро" нашего колеса
 *		тут происходит соединение вместе всех модулей
 *		терминала, и управление ими.
 *
 */

/*
 * Глобальные необходимые переменные
 */
console.log(getCurrentTime()+' :: MisterionOnline Initialize...');

var game_state = false;
var inf_text = 'Подождите... Инициализация клиента...';
var inf_text_en = 'Делайте ваши ставки.';
var inf_text_dis = 'Ставки приняты.';
var inf_text_inf = 'Выигрышный номер ';

var bets = Array(); var old_bets = Array();
/*
 * Объявляем все необходимые слои canvas
 */

var txtContext=getCtx('text')
var aniContext=getCtx('anim');
var numContext=getCtx('nums');
var prevBetContext=getCtx('prev');
var coinContext=getCtx('coin');
var enBoardContext=getCtx('active');
var disBoardContext=getCtx('inactive');
var selBoardContext=getCtx('lighted');
var tmpContext=getCtx('temp');
var pushContext=getCtx('push');
var buttonContext=getCtx('buttons');
//
var imgEn = new Image();
var imgDis = new Image();
var imgSel = new Image();
var imgLast = new Image();
//
var chipArr = new Array();
var chipX2Arr = new Array();
//
var btnImgArr = new Array();
//
var imgNum = new Image();
var imgProgress = new Image();

imgEn.onload = function(){
    enBoardContext.drawImage(imgEn,0,0);
}
imgDis.onload = function(){
    disBoardContext.drawImage(imgDis,0,0);
}
imgSel.onload = function(){
    selBoardContext.drawImage(imgSel,0,0);
}
imgEn.src='img/bg.png';
imgDis.src='img/bg_dis.png';
imgSel.src='img/bg_sel.png';
imgLast.src='img/bg_last.png';
//
for (var i=1;i<6;i++) {
    chipArr[i]=new Image();
    chipX2Arr[i]=new Image();
    chipArr[i].src='img/min_chip'+i+'.png';
    chipX2Arr[i].src='img/min_chip(x2)'+i+'.png';
}
loadStaticImages();
calculateCells();

imgNum.onload = function(){
    numContext.drawImage(imgNum,0,0);
}
imgNum.src='img/lastnum.png';
imgProgress.src='img/progress.png';
//
var renderTmr = setInterval(renderFunc,100);
setInterval(checkButtons,100);
setInterval(blinkNum,250);
console.log(getCurrentTime()+' :: Done!');
openSocket();