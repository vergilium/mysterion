console.log(121212);
var isMouseDown=false;
var pushButton=undefined;
// ******************
// Ф-ция вызываемая, при MouseOver
function areaMM(el){
	if (isMouseDown){
		if (cr>0) areaMD(el);
	}
};

// ******************
// Ф-ция вызываемая, при MouseClick

function areaClick(el){
    //
    if (lastScreenFlag) {
        showLastScreen();
        return;
    }
    if ((el.id.indexOf('btn')+1)==true){
       buttonClick(el);
    } else if ((el.id.indexOf('tab')+1)==true){
       //tabClick(el);
    }
};

// ******************
// Ф-ция вызываемая, при MouseUp

function areaMU(el){
    /*if ((el.id.indexOf('btn')+1)==true){
        if (el.id.indexOf('coin',1)==-1 && el.id.indexOf('f5',1)==-1 && $('#'+el.id).attr('enabled')=='true'){
            loadButton(el.id.replace('btn_',''),'en');
        }
    }*/
    if (lastScreenFlag) {
        //showLastScreen();
        isMouseDown=false;
        return;
    }
    if (pushButton!=undefined){
        loadButton(pushButton.replace('btn_',''),'en');
        pushButton=undefined;
    }
    //pushContext.clearRect(0,0,1024,768);
    isMouseDown=false;
};

// ******************
// Ф-ция вызываемая, при MouseDown

function areaMD(el){
    if (lastScreenFlag) {
        //showLastScreen();
        return;
    }
    if ((el.id.indexOf('btn')+1)==true){
        if (el.id.indexOf('coin',1)==-1 && el.id.indexOf('f5',1)==-1 && $('#'+el.id).attr('enabled')=='true'){
            pushButton=el.id;
            loadButton(el.id.replace('btn_',''),'dn');
        }
    }
    if ((el.id.indexOf('tab')+1)==true && cr>0 && game_state==true){
        tabClick(el);
        drawSelected(el);
        isMouseDown=true;
        setTimeout(function(){
            pushContext.clearRect(0,0,1024,768)
        },500);

    }
};
