// ******************
// Ф-ция вызываемая, при MouseOver

function areaMM(el){
	if (mousedn){
		areaMD(el);
	}
	if (isDebugMode()){
		ttip="[i] id='"+el.id+"'";
		$(el).attr('title', ttip);
	}
};

// ******************
// Ф-ция вызываемая, при MouseClick

function areaClick(el){

};

// ******************
// Ф-ция вызываемая, при MouseUp

function areaMU(el){
	mousedn=false;
	refreshScreen();
};

// ******************
// Ф-ция вызываемая, при MouseDown

function areaMD(el){

	if (!activemode) {
		if ((el.id.indexOf("btn")+1)==true) {
			button_click(el.id,'');
		}
		return;
	}
	mousedn=true;
	/*
	numbers=el.id.replace('tab','').replace('race','').replace("_",",");
	for (i=0;i<numbers.length();i++){
		if (bets[i]==undefined){
			bets[i]=numbers[i];
		} else {
		
		}
	}
	*/
	if (el.id.indexOf("tab")+1==true){
		var bet_num=el.id.replace("tab","").split("_");
	}
	var possible=false;
	log("bet_num="+bet_num);

	//for (n=0;n<betnum.length;n++){
	if ((cr-curr_bet)<=0) return;
	if (bet_num!=undefined){
		if (bet_num.length==1){
			if (curr_bet!=-1) {
				if (bets[el.id]==undefined){
						bets[el.id]={
							el:el,
							numbers:el.id.replace('tab','').replace('race','').replace(/_/g,","),
							betvalue:curr_bet
						};
						possible=true;
				} else {
						if (bets[el.id].betvalue<=maxbets && bets[el.id].betvalue+curr_bet<=maxbets) {
							bets[el.id].betvalue=bets[el.id].betvalue+curr_bet;
							possible=true;
						};
				}
			} else if (curr_bet==-1 && (el.id.indexOf("btn")+1)!=true) {
				var crd=el.coords.split(",");
				var c={	// rectangle:
						x1:crd[0],
						y1:crd[1],
						x2:crd[2],
						y2:crd[3]
					}		
				var zx=+c.x2 - ((+c.x2-+c.x1)/2) - 16;
				var zy=+c.y2 - ((+c.y2-+c.y1)/2) - 16;		
				
				a_context.clearRect(zx-1,zy-1,34,34);
				cr=cr + +bets[el.id].betvalue;
				delete bets[el.id];
				render_credit_text();			
			}
		} else {
			if (curr_bet!=-1) {
				if (bets[el.id]==undefined){
						bets[el.id]={
							el:el,
							numbers:el.id.replace('tab','').replace('race','').replace(/_/g,","),
							betvalue:curr_bet
						};
						possible=true;
				} else {
						if ((bets[el.id].betvalue / bet_num.length)<=maxbets && bets[el.id].betvalue / bet_num.length +curr_bet / bet_num.length<=maxbets) {
							bets[el.id].betvalue=bets[el.id].betvalue+curr_bet;
							possible=true;
						};
				}
			} else if (curr_bet==-1 && (el.id.indexOf("btn")+1)!=true) {
				var crd=el.coords.split(",");
				var c={	// rectangle:
						x1:crd[0],
						y1:crd[1],
						x2:crd[2],
						y2:crd[3]
					}		
				var zx=+c.x2 - ((+c.x2-+c.x1)/2) - 16;
				var zy=+c.y2 - ((+c.y2-+c.y1)/2) - 16;		
				
				a_context.clearRect(zx-1,zy-1,34,34);
				cr=cr + +bets[el.id].betvalue;
				delete bets[el.id];
				render_credit_text();			
			}		
		};
	}
	if ((el.id.indexOf("btn")+1)!=true) {
		drawCoin(el);
		if (curr_bet!=-1) {
			if (possible) minusCredit(bet_num.length);
		}else {
			render_bet_text();
		}
	}else {
		button_click(el.id,'');
	}	
	if (cr<=0) bets[el.id].betvalue=0;
	//alert (el.shape);
	switch (el.shape){
		case 'rect':
			if (isDebugMode()){
				var c=el.coords.split(",");
				context.lineWidth = 1;
				context.strokeStyle = 'whitesmoke';		
				context.strokeRect(c[0],c[1],c[2]-c[0],c[3]-c[1]);
				context.stroke();
			}			
		break;
		
		case 'poly':
		
		break;
		
		case 'circle':
		
		break;
	}
/*	
	if (el.id.indexOf("tab")!=-1){
		//alert(el.coords.split(",")[0]);
		var c=el.coords.split(",");
		context.lineWidth = 1;
		context.strokeStyle = 'whitesmoke';		
		context.strokeRect(c[0],c[1],c[2]-c[0],c[3]-c[1]);
		context.stroke();	
	}
*/	
};