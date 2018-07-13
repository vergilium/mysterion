/******************************************************************
	Функции формирования изображения

*******************************************************************
*/
var scrCtx;

function createScr(){
	scrCtx=createCtx( {w:1024, h:768},{visible:true, zorder:1});
	
}
function refreshScr(){

}


/******************************************************************
	Функции контекста

*******************************************************************
*/

// Массив всех созданных контекстов
var arr_context=Array();

/*
 * @method clearCtx
 * Очистить (полностью!) указанный контекст
 * @param {context} ctx    - контекст
 */
function clearCtx(ctx){
	//ctx.width=ctx.width;
    ctx.clearRect(0, 0,ctx.width,ctx.height);
}

/*
 * @method createCtx
 * Создает контекст по указанным параметрам
 * @param {Array} size  - размеры контекста
 * @param {Array} attr  - необходимые атрибуты
 * @returns {Array}     - объект содержащий Id в локальном массиве и сам контекст
 */
function createCtx(size,attr){
    if (attr==undefined) {
        attr={
            visible:false,
            zorder:0
        };
    }
    if (size==undefined || size.w==undefined || size.h==undefined){
        size={
            w:1024,
            h:768
        };
    }
    
    var uid=getUUID();
    $('<canvas id="canv_'+uid+'" width="'+size.w+'" height="'+size.h+'" style="position:absolute; left:0; top:0; z-index:'+attr.zorder+'; user-select:none; -webkit-user-select: none; -moz-user-select: none; '+(attr.visible ? '' : 'display:none;')+'"></canvas>').appendTo('#main');
    var canvas = document.getElementById('canv_'+uid);
    var context = canvas.getContext('2d');
    var ret={
        id: uid,
        ctx: context
    };
    arr_context[arr_context.length]=ret;
    return ret;		
}

/*
 * @method getCtx
 * Возвращает контекст, по заданному id канваса
 * @param {String} id   - идентификатор
 * @returns {unresolved} - контекст
 */
function getCtx(id){
	var canvas = document.getElementById(id);
	var context = canvas.getContext('2d');
	return context;
}

/*
 * @method deleteCtx
 * функция удаляющая контекст
 * @param {string} c    - ID контекста для удаления из DOM
 */
function deleteCtx(c){
	$(c.id).remove();
}

/**
 * @method deleteAllCtx
 * удаляет все автоматически созданные контексты (те, что созданы при помощи createCtx)
 */
function deleteAllCtx(){
	$("canvas [id*='canv_']").remove();
}

/**
 * @method imgGetRegion
 * функция для вырезания определенной области из оригинальной картинки
 * @param r {Array}     - область для вырезания [x,y,]width,height[,dstx,dsty]
 * @param src {Canvas}  - канвас или файл оригинал
 * @param dest {Canvas} - канвас приёмник
 * @return {*}
 */
function imgGetRegion(r,src,dest){
	if (r.x==undefined || r.y==undefined) {r.x=0;r.y=0;}
	if (r.dstx==undefined || r.dsty==undefined) {r.dstx=0; r.dsty=0;}
	
	if (typeof(src)==string){
		im=loadImageTmp(src,function(){
			if (dest!=undefined){
				dest.drawImage(im,r.dx,r.y,r.width,r.height,r.dstx,r.dsty,r.width,r.height);
			}else{
				return im;
			}
		});
	}
	else {
		im=src.getImageData(r.x,r.y,r.width,r.height);
		if (dest!=undefined){
			dest.drawImage(im,r.dstx,r.dsty);
		}else{
			return im;
		}
	}
	return;
}
/******************************************************************
	Функции изображений

*******************************************************************
*/
var img_arr=Array();

/**
 * @method loadAniImage
 * Функция создаёт объект анимации.
 * @param fi {String}       - имя файла картинки
 * @param attr {Array}      - массив параметров {
 *     w,h,x,y // ширина/высота изображения (одного кадра!!!!) + координаты места вывода анимации
 *    farmesCount, // количество нужных кадров (в картинке кадры ДОЛЖНЫ быть расположены по вертикали!!!)
 *    [,visible,enable] //
 *    [interval, // скорость смены изображения
 *    timerFunction(), // переопределение функции таймера анимации
 *    skipFrames // не анимируемые кадры (массив)]
 * }
 * @param destctx {Context} - контекст размещения
 * @return {Object}         - aniObj{
 *    visible:true/false,
 *    enable(true/false);
 *    enabled:true/false // типа для чтения
 *    frames,
 *    skipframes,
 *    frame(idx), // конкретный кадр
 *    ctx:Context,
 *    x,y,w,h
 *    }
 */
function loadAniImage(fi,attr,destctx){
    var prevState;
	var aniobj = {
		visible:(attr.visible==undefined ? false : attr.visible),
		interval:(attr.interval==undefined ? 200 : attr.interval),
		enabled:(attr.enable==undefined ? true : attr.enable),
		ctx:destctx,
		x:attr.x,
		y:attr.y,
		w:attr.w,
		h:attr.h,
		bet:attr.bet,
		iscoin:(attr.iscoin==undefined ? true : attr.iscoin),
		anictx:createCtx({w:attr.w, h:attr.h*(attr.framesCount+1)}), // контекст со всеми кадрами анимации
		frames:attr.framesCount,
		curframe:0,
		skipframes:(attr.skipFrames==undefined ? -666 : attr.skipFrames),
		userfunc:function(){
			if (attr.timerFunction!=undefined){
				attr.timerFunction(this);
			}
		},	
		tmrfunc:function(){
			if (this.anictx!=undefined){
				if (this.visible==false) return;
				
				this.curframe++; if (this.curframe>this.frames) this.curframe=0;
				if (this.skipframes!=-666) {
					while(inArray(this.curframe,this.skipframes.split(";")))  {
						//console.log("skipping:"+this.curframe);
						this.curframe++;
						if (this.curframe>this.frames) this.curframe=0;
						}
				}
				
				//if (inArray(this.curframe,skipframes)!=true){
					tmpimg=this.anictx.ctx.getImageData(0,this.h*this.curframe,this.w,this.h);
					clearCtx(this.ctx);
					this.ctx.putImageData(tmpimg,this.x,this.y);
					if (this.userfunc!=undefined) this.userfunc();
				//}
				//console.log(this.curframe);
			}				
		},
		frame:function(idx){
			if (this.anictx!=undefined){
				this.curframe=idx;
				tmpimg=this.anictx.ctx.getImageData(0,this.h*idx,this.w,this.h);
				clearCtx(this.ctx);
				this.ctx.putImageData(tmpimg,this.x,this.y);
			}
		}
	};
    prevState=!aniobj.enabled;
	aniobj.enable = function(s){
			if (s==undefined) s=false;
			//if (aniobj.tmrid!=undefined) return;
			if (s==false && prevState==true) {
				prevState=false;
                clearInterval(aniobj.tmrid);
				delete aniobj.tmrid;
				if (aniobj.iscoin){
					aniobj.frame((cr>=aniobj.bet?1:0));
				} else {
					aniobj.frame(0);
				}
			} else if(s==true && prevState==false) {
				aniobj.tmrid=setInterval(function() {aniobj.tmrfunc();},aniobj.interval);
                prevState=true;
			}
			aniobj.enabled=s;
		};
	
	aniobj.init = function(){
		if (aniobj.visible==false) return;
		/*if (aniobj.skipframes!=-666) {
			while(inArray(aniobj.curframe,aniobj.skipframes.split(";")))  {
				aniobj.curframe++; if (aniobj.curframe>aniobj.frames) aniobj.curframe=0;
			}
		}*/
		var tmpimg=aniobj.anictx.ctx.getImageData(0,aniobj.h*aniobj.curframe,aniobj.w,aniobj.h);
		aniobj.ctx.putImageData(tmpimg,aniobj.x,aniobj.y);
		//aniobj.drawText();
	}
	
	aniobj.drawText = function(){
		aniobj.ctx.fillStyle = "#f8e000";
		aniobj.ctx.font = "normal normal 15pt AGFriquer";
		var txt_x=(aniobj.x+aniobj.w/2)-(aniobj.ctx.measureText(aniobj.bet.toString()+plustext).width/2);
		if (aniobj.bet>0) aniobj.ctx.fillText(aniobj.bet.toString()+plustext, txt_x, aniobj.y+72);	
	}
	
	img=loadImage(fi,function(){
			aniobj.init();
			aniobj.enable(aniobj.enabled);
			},
		aniobj.anictx.ctx);
	//console.log(img);
	
	aniobj = (function() {
		aniobj.drawText();
		return aniobj;
	})();	

    setInterval(function(){
        if (cr>=aniobj.bet) aniobj.active=true; else aniobj.active=false;
        if (cr==0){
            if (coin[1].enabled==true && coin[2].enabled==false && coin[3].enabled==false && coin[4].enabled==false && coin[5].enabled==false) return;

            aniobj.enable(false);
            for (var i=5;i>=2;i--){
                if (cr < coin[i].bet) {
                    coin[i].enable(false);
                    coin[i].frame(0);
                }
            }
            coin[1].enable(true);
            return;
        }

        if (aniobj.enabled && cr>=aniobj.bet) {
            return;
        } else if (aniobj.enabled && cr<aniobj.bet && cr!=0){
            aniobj.enable(false);
            for (var i=5;i>=1;i--){
                if (cr < coin[i].bet) {
                    coin[i].enable(false);
                    coin[i].frame(0);
                } else if (cr >= coin[i].bet) {
                    coin[i].enable(true);
                    return;
                }
            }
        }
        if (aniobj.iscoin){
            if (cr!=0) aniobj.frame((cr>=aniobj.bet?1:0));
        } else {
            aniobj.frame(0);
        }
    },100);

	return aniobj;
}

/**
 * @method loadImage
 * загружает изображение из файла
 * @param fi {String}   - имя файла, для загрузки изображения
 * @param func {String} - (опционально) функция, которая вызовется сразу после загрузки изображения
 * @param ctx {Context} - (опционально) контекст, для размещения загруженного изображения
 * @return {Object}     - {	id:		uid,
 *                          file:	fi,
 *                          img:	img_arr[uid]
 *                        }
 */
function loadImage(fi, func, ctx){
	var uid=getUUID();
	img_arr[uid]=new Image();
	if (func!=undefined) {
		img_arr[uid].onload = function(){
			if (ctx!=undefined) ctx.drawImage(img_arr[uid],0,0);
			func();
		}
	}
	img_arr[uid].src = fi;
	var ret={	id:		uid,
				file:	fi,
				img:	img_arr[uid]
			}
	return ret;
}

/**
 * @method loadImageTmp
 * загружает временное изображение из файла (не сохраняется в массиве изображений img_arr[])
 * @param fi {String}         - имя файла
 * @param func {String}       - (опционально) пользовательская функция, вызываемая при загрузке изображения
 * @param ctx {Context}       - (опционально) контекст для помещения изображения
 * @return {Image}
 */
function loadImageTmp(fi, func, ctx){
	img=new Image();
	if (func!=undefined) {
		img.onload = function(){
			if (ctx!=undefined) ctx.drawImage(img,0,0);
			func();
		};
	}
	img.src = fi;
	return img;
}

/******************************************************************

	Вспомогательные функции

*******************************************************************
**/



function isImage(obj){
	return (obj instanceof Image);
}
function isContext(obj){
	return (obj instanceof CanvasRenderingContext2D);
}