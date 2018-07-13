/**
 *
 * game_basics.js
 *
 *
 */
var nums=Array(); var maxnums=13; var blinkFlag = false;
var betsStack=Array(); var oldBetsStack=Array();
var allCells=Array();

/**
 * @method calculateCells
 * Расчет всех ячеек для подсвечивания
 */
function calculateCells(){
    var firstX=130;
    var secondX=383;
    var thirdX=636;

    var firstY=416;
    var w=64; var h=98;
    var num=1;

    for (var j=0;j<4;j++){
        for (var i=0;i<=2;i++){
            var rx=((firstX + (j*w))-(j*2));
            var ry=(firstY - (i*h)) + (i*2);
            var rw=w;
            var rh=h;
            var obj={
                 x: rx,
                 y: ry,
                 w: rw,
                 h: rh}
            //allCells[allCells.length]=obj;
            allCells[num]=obj;
            num++;
            //txtContext.strokeStyle='#ff0000';
            //txtContext.strokeRect(rx, ry, rw, rh);
        }
    }
    for (var j=0;j<4;j++){
        for (var i=0;i<=2;i++){
            var rx=((secondX + (j*w))-(j*2));
            var ry=(firstY - (i*h)) + (i*2);
            var rw=w;
            var rh=h;
            var obj={
                x: rx,
                y: ry,
                w: rw,
                h: rh}
            //allCells[allCells.length]=obj;
            allCells[num]=obj;
            num++;
            //txtContext.strokeStyle='#ff0000';
            //txtContext.strokeRect(rx, ry, rw, rh);
        }
    }
    for (var j=0;j<4;j++){
        for (var i=0;i<=2;i++){
            var rx=((thirdX + (j*w))-(j*2));
            var ry=(firstY - (i*h)) + (i*2);
            var rw=w;
            var rh=h;
            var obj={
                x: rx,
                y: ry,
                w: rw,
                h: rh}
            //allCells[allCells.length]=obj;
            allCells[num]=obj;
            num++;
            //txtContext.strokeStyle='#ff0000';
            //txtContext.strokeRect(rx, ry, rw, rh);
        }
    }
    allCells[0]={
        x:70,
        y:222,
        w:62,
        h:294
    }
}

/**
 * @method getCurrBet
 * Получаем номинал выбранной фишки
 * @return {Integer}
 */
function getCurrBet(){
    for (var i=0;i<6;i++){
        if (coin[i].enabled==true) {
            return coin[i].bet;
        }
    }
}

/**
 * @method changeGameState
 * Процедура смены состояния игры (можно/нельзя делать ставки)
 * @param new_state {Boolean}   - состояние игры.
 */
function changeGameState(new_state){
    if (new_state==true){
        game_state=true;
        $('#active').show();
        $('#inactive').hide();
        inf_text=inf_text_en;
        loadButton('replay','en');
        $('#btn_replay').attr('enabled', 'true');
    } else {
        game_state=false;
        $('#active').hide();
        $('#inactive').show();
        inf_text=inf_text_dis;
        loadButton('replay','dis');
        $('#btn_replay').attr('enabled', 'false');
        $('#btn_undoall').attr('enabled', 'false');
        $('#btn_undoone').attr('enabled', 'false');
    }
}

/**
 * @method buttonClick
 * Процедура обработки нажатия на кнопки.
 * @param el
 */
function buttonClick(el){
    var is_coin=false;

    var btn_name=el.id.replace('btn_','').replace('coin_','');
    if (el.id.indexOf('coin')>0) is_coin=true;

    //console.log(btn_name);
    // если это не фишка и не поле, то это какая-то кнопка. обрабатываем нажатие.
    if (is_coin==false){
        switch (btn_name){
            //
            case 'exit':
                if (socket!=undefined && socket.state!=0) {
                        socket.send("logoff");
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
                        log("An error occured when AJAX request was called in button_click() function (application.js)...",log_err);
                    }
                });
                break;

            case 'help':
                break;
            case 'undoone':
                rmBet();
                break;
            case 'undoall':
                rmAllBet();
                break;
            case 'replay':
                doubleBet();
                break;
            case 'previous':
                showLastScreen();
                break;
            case 'f5':
                setTimeout(function(){
                    location.href = 'http://'+document.domain+'/';
                }, 500);
                break;
        };
    // обрабатываем нажатие на фишки
    } else {
        var c_idx = +btn_name;
        if (coin[c_idx].active==true){
            for (var i=0;i<=5;i++){
                if (i==c_idx){
                    coin[i].enable(true);
                }else{
                    coin[i].enable(false);
                }
            }
        }
    }
}

/**
 * @method drawSelected
 * Процедура подсвечивания выбранной ячейки
 * @param el
 */
function drawSelected(el,last){
    if (last==undefined) last=false;
    var workContext;
    (last==true)?workContext=prevBetContext:workContext=pushContext;
    var numbers=el.id.replace('tab','').split('_');
    for (var i=0;i<numbers.length;i++){
        //var c=$('#tab'+numbers[i]).attr('coords').split(',');
        var x=allCells[numbers[i]].x;
        var y=allCells[numbers[i]].y;
        var w=allCells[numbers[i]].w;
        var h=allCells[numbers[i]].h;
        var imgData=selBoardContext.getImageData(x,y,w,h);
        //pushContext.putImageData(imgData,x,y);
        workContext.putImageData(imgData,x,y);
        //setInterval(pushContext.clearRect(0,0,1024,768),5000);
    }
}

/**
 * @method tabClick
 * Процедура обработки нажатия на ячейку
 * @param el
 */
function tabClick(el){
    if (cr!=0 && cr-getCurrBet()>=0 && game_state==true){
        // можно сделать ставку
        addBet(el);
    }
}

function blinkNum(){
    //if (nums.length=0) return;
    aniContext.clearRect(5,8,40,300);
    if (nums.length>maxnums){
        var yi=0;
        for (var i =nums.length-maxnums; i < nums.length; i++){
            // последний элемент. он или рисуется или нет. блымает, короче.
            if (i==nums.length-1){
                var c=getNumCoord(nums[i]);
                var cutData=numContext.getImageData(c.x, c.y, c.w, c.h);
                if (blinkFlag) aniContext.putImageData(cutData,5,8+(yi*20));
                blinkFlag=!blinkFlag;
            }else{
                var c=getNumCoord(nums[i]);
                var cutData=numContext.getImageData(c.x, c.y, c.w, c.h);
                aniContext.putImageData(cutData,5,8+(yi*20));
            }
            yi++;
        }
    } else {
        for (var i = 0; i < nums.length; i++){
            // последний элемент. он или рисуется или нет. блымает, короче.
            if (i==nums.length-1){
                var c=getNumCoord(nums[i]);
                var cutData=numContext.getImageData(c.x, c.y, c.w, c.h);
                if (blinkFlag) aniContext.putImageData(cutData,5,8+(i*20));
                blinkFlag=!blinkFlag;
            }else{
                var c=getNumCoord(nums[i]);
                var cutData=numContext.getImageData(c.x, c.y, c.w, c.h);
                aniContext.putImageData(cutData,5,8+(i*20));
            }
        }
    }
}

/**
 * @method winEvent()
 * Событие выигрыша
 * @param val {Integer} - сумма выигрыша
 */

function winEvent(val){
    winval=val;
    setTimeout(function(){
        cr= cr+ +val;
        winval=-1;
        //changeGameState(true);
    },5000);
}

/**
 * @method addNumber
 * Процедура добавления новой выпавшей цифры
 * @param num {Integer} - Выпавший номер (0-36!)
 */
function addNumber(num,fast){
    if (fast==undefined) fast=false;
    if (nums[nums.length-1]!=undefined) oldwinnum=nums[nums.length-1];
    nums[nums.length]=num;
    if (betsStack.length>0 && fast==false){
        blinkCell(num);
    }
}

/**
 * @method blinkCell
 * Процедура подсветки ячейки
 * @param num {Integer} - номер, для подсвечивания (0-36!)
 */
function blinkCell(num){
    var counter=0;
    var el = $('#tab'+num).get(0);
    var blinkTmr = setInterval(function(){
        drawSelected(el);
        setTimeout(function(){
            pushContext.clearRect(0,0,1024,768)
        },250);
        if (counter>=10) clearInterval(blinkTmr);
        counter++;
    },500);
}

/**
 * @method doubleBet
 * Удваивает предидущую ставку
 */
function doubleBet(){
    //console.log(betsStack.length);
    // проверяем были ли уже сделаны ставки, и если да, то удваиваем их (если возможно!)
    if (betsStack.length>0){
        var len=getBetsCount();

        if (cr-(len*betsStack[0].bet)<0) {
            show_msg('Недостаточно кредитов.');
            setTimeout(hide_msg,2000);
            return;
        }

        for (var i=0;i<len;i++){
            //addBet(betsStack[i].itm);
            var bt=betsStack[i].bet;
            console.log(bt);
            var it=betsStack[i].itm;
            cr=cr-bt;
            betsStack[betsStack.length]={itm:it, bet:bt};
            bets[betsStack[i].itm.id]=bets[betsStack[i].itm.id]+betsStack[i].bet;
            drawBet(it,false);
        }
    }
}

/**
 * @method addBet
 * Увеличение (уменьшение) ставки.
 * @param el {Object}       - объект-контейнер ячейки ставки
 * @param last {Boolean}    - (опционально) повышаются ли все последние ставки на поле вдвое (по-умолчанию false)
 * @see {@link doubleBet} for further information.
 * @see {@link http://github.com|GitHub}
 */
function addBet(el,last){
    if (last==undefined) last=false;
        // если выбрана фишка удаления ставки:
        //console.log(getCurrBet());
        if (last==false){
        if (getCurrBet()==-1) {
            rmBetByEl(el);
            return;
        }
        // в противном случае ставим ставку
        cr=cr-getCurrBet();
        if (bets[el.id]==undefined){
            bets[el.id]=getCurrBet();
        } else {
            bets[el.id]=bets[el.id]+getCurrBet();
        }
        betsStack[betsStack.length]={itm:el, bet:getCurrBet()};
    }
    //drawBet(el,last);
    reDrawBets();
}
/**
 * @method rmBetByEl
 * Удалить ставку, по текущему элементу (нажатие в определенную ячейку, с выбранной фишкой отмены ставки)
 * @param el
 */
function rmBetByEl(el){
    if (bets[el.id]==undefined) return;

    cr=cr+bets[el.id];
    var newStack=Array();
    for (var i=0;i<betsStack.length;i++){
        if (betsStack[i].itm.id!=el.id){
            newStack[newStack.length]=betsStack[i];
        }
    }
    betsStack=newStack;
    delete bets[el.id];
    reDrawBets();
}
/**
 * @method rmBet
 * Удаляет предыдущую ставку
 */
function rmBet(){
    if (betsStack.length==0) return;
    var rbitm=betsStack.pop();
    if (inArrayCnt(rbitm.itm.id, betsStack)==0){
        delete bets[rbitm.itm.id];
    } else {
        bets[rbitm.itm.id]=bets[rbitm.itm.id]-rbitm.bet;
    }
    cr=cr+ +rbitm.bet;
    reDrawBets();
}
/**
 * @method rmAllBet
 * Отменяет все ставки.
 */
function rmAllBet(returncr){
    if (returncr==undefined) returncr=false;
    if (!returncr) {
        coinContext.clearRect(0,0,1024,768);
        cr=cr+ +getArrBets(betsStack);
    }
    bets=new Array();
    betsStack=new Array();
}

/**
 * @method reDrawBets
 * Перерисовываем фишки ставок на экране.
 */
function reDrawBets(){
    coinContext.clearRect(0,0,1024,768);
    var drawed=Array(); //var counter=0;
    for (var i=0;i<betsStack.length;i++){
        if (inArray(betsStack[i].itm.id,drawed)==false) {
            drawBet(betsStack[i].itm);
            drawed[drawed.length]=betsStack[i].itm.id;
            //counter++;
        }
    }
    //console.log('redraw '+counter+' times.');
    /*
    var drawed=Array();

    for (var i=0;i<betsStack.length;i++){

        if (inArrayCnt(betsStack[i].itm.id,betsStack)==1 || isChipValue(betsStack[i].itm)==true){
            if (inArray(betsStack[i].itm.id,drawed)==false) {
                console.log('chip value: '+betsStack[i].bet);
                drawBet(betsStack[i].itm);
                drawed[drawed.length]=betsStack[i].itm.id;
            }
        }
    }
    var drawed=Array();
    for (var i=0;i<betsStack.length;i++){
        if (inArrayCnt(betsStack[i].itm.id,betsStack)>1){
            if (inArray(betsStack[i].itm.id,drawed)==false) {
                console.log('chip value: '+betsStack[i].bet);
                drawBet(betsStack[i].itm);
                drawed[drawed.length]=betsStack[i].itm.id;
            }
        }
    }
    */
}

/**
 * @method drawBet
 * Отрисовка одной фишки.
 * @param el
 */
function drawBet(el,last){
    var workContext=(last==true)?prevBetContext:coinContext;
    var coord=el.coords.split(",");

    if (el.shape=='rect'){
        var c={	// rectangle:
            x1:coord[0],
            y1:coord[1],
            x2:coord[2],
            y2:coord[3]
        }
    } else if (el.shape=='poly'){
        var helperObject=$('#h'+el.id.replace('tab','')).get(0);
        var hcoord=helperObject.coords.split(',');
        var c={
            x1:hcoord[0],
            y1:hcoord[1],
            x2:hcoord[2],
            y2:hcoord[3]
        }
    }
    // нарисовать фишку
    if (inArrayCnt(el.id, betsStack)==1 || isChipValue(el)==true) {
        var zx=+c.x2 - ((+c.x2-+c.x1)/2) - 20;
        var zy=+c.y2 - ((+c.y2-+c.y1)/2) - 20;
        workContext.drawImage(chipArr[getCurrChip(el)],zx,zy);
        workContext.fillStyle = "#00F";
        workContext.font = "bold normal 12pt AGFriquer";
        w=(zx)-(coinContext.measureText(bets[el.id].toString()).width/2)+16;
        workContext.fillText(bets[el.id], w, zy+20);
      } else {
          var zx=+c.x2 - ((+c.x2-+c.x1)/2) - 20;
          var zy=+c.y2 - ((+c.y2-+c.y1)/2) - 28;
        workContext.drawImage(chipX2Arr[getCurrChip(el)],zx,zy);
        workContext.fillStyle = "#00F";
        workContext.font = "bold normal 12pt AGFriquer";
        w=(zx)-(coinContext.measureText(bets[el.id].toString()).width/2)+20;
        workContext.fillText(bets[el.id], w, zy+24);
      }
}

// начальная позиция бегущей строки
var inf_text_pos=-235;
/**
 * @method renderFunc
 * Функция перерисовки текста
 */
function renderFunc(){
    // очистка всех областей для вывода текста и задание шрифта и т.п.
    txtContext.clearRect(526,724,76,25);
    txtContext.clearRect(20,724,94,25);
    txtContext.clearRect(336,724,94,25);
    txtContext.clearRect(0,0,1024,30);
    txtContext.fillStyle = "#f8b000";
    txtContext.font = '26px Palatino';
    txtContext.textAlign="right";
    txtContext.textBaseline="top";
    tmpContext.fillStyle = "#f8b000";
    tmpContext.font = '26px Palatino';
    tmpContext.clearRect(0,0,1024,30);
    // вывод суммы выигрыша
    if (winval!=-1){
        txtContext.fillText(winval.toString(),108,722);
    }
    // вывод суммы кредита
    txtContext.fillText(cr.toString(),428,722);
    // вывод бегущей строки
    tmpContext.fillText(inf_text,2,22);

    txtContext.putImageData(tmpContext.getImageData(inf_text_pos,0,235,30),625,722);
    inf_text_pos=inf_text_pos+3;
    if ((inf_text_pos-117)>235) inf_text_pos=-235;
    // подсчет всех ставок
    var summ=getBetSumm();
    // вывод суммы ставки
    txtContext.fillText(summ.toString(),602,722);
}

function renderStaticFunc(){
    // очистка всех областей для вывода текста и задание шрифта и т.п.
    txtContext.clearRect(526,724,76,25);
    txtContext.clearRect(20,724,94,25);
    txtContext.clearRect(336,724,94,25);
    txtContext.clearRect(0,0,1024,30);
    txtContext.fillStyle = "#f8b000";
    txtContext.font = '26px Palatino';
    txtContext.textAlign="right";
    txtContext.textBaseline="top";
    txtContext.clearRect(0,718,1024,30);
    // вывод суммы выигрыша
    if (oldwinnum!=-1){
        //txtContext.fillText(oldwinnum.toString(),108,722);
    }
    // вывод суммы кредита
    txtContext.fillText((oldcr==-1)?'?':oldcr.toString(),428,722);
    // вывод строки
    //txtContext.fillText(inf_text,625,722);

    var summ=getBetSumm(true);
    // вывод суммы выигрыша
    txtContext.font = '22px Palatino';
    txtContext.fillText(inf_text_inf+oldwinnum,855,725);
}

var prevCondition=false;
/**
 * @method checkButtons
 * Процедура проверки доступности кнопок.
 */
function checkButtons(){
    if (getBetSumm()>0 && prevCondition==false){
        $('#btn_undoone').attr('enabled','true');
        $('#btn_undoall').attr('enabled','true');
        loadButton('undoone','en');
        loadButton('undoall','en');
        prevCondition=true;
    } else if(getBetSumm()==0 && prevCondition==true) {
        $('#btn_undoone').attr('enabled','false');
        $('#btn_undoall').attr('enabled','false');
        loadButton('undoone','dis');
        loadButton('undoall','dis');
        prevCondition=false;
    }
}

var lastScreenFlag=false;
function showLastScreen(){
    lastScreenFlag=!lastScreenFlag;
    if (lastScreenFlag){
        prevBetContext.drawImage(imgLast,0,0);

        if (nums[nums.length-2]!=undefined) drawSelected($('#tab'+nums[nums.length-2]).get(0),true);
        if (oldBetsStack.length>0){
            for (var i=0; i<oldBetsStack.length; i++){
                addBet(oldBetsStack[i].itm,true);
            }
        }
        clearInterval(renderTmr);
        $('#prev').show();
    } else {
        prevBetContext.clearRect(0,0,1024,710);
        $('#prev').hide();
        renderTmr=setInterval(renderFunc,100);
    }
    renderStaticFunc();
}

function startCountdown(doscreen){
    if (doscreen==undefined) doscreen=false;
    aniContext.drawImage(imgProgress,976,40);
    var h=7; var counter=0;
    var t=40;

    var tmrId=setInterval(function(){
        aniContext.clearRect(976,t,30,h);
        t=t+h;
        if (counter>26){
            clearInterval(tmrId);
            counter=0;
            tmrId=setInterval(function(){
                aniContext.clearRect(976,t,30,h);
                t=t+h;
                if (counter>5){
                    clearInterval(tmrId);
                    counter=0;
                    tmrId=setInterval(function(){
                        aniContext.clearRect(976,t,30,h);
                        t=t+h;
                        if (counter>2){
                            clearInterval(tmrId);
                            if (doscreen){
                                changeGameState(false);
                                sendBets();
                            }
                        }
                        counter++;
                    },count3);
                }
                counter++;
            },count2);
        }
        counter++;
    },count1);
}