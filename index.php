<?php
	/*
	*	INDEX.PHP
	*   Основной файл клиента колеса. Содержит элементы canvas для отображения графики
	*   плюс все необходимые ссылки на ява-скрипт движок, и инициализационные php файлы
	*												   					08/01/13
	*	Copyright (c) seriy-coder
	*	for Capitan
	*	Mysterion Live 2013 O
	*	[seriy-coder@ya.ru]
	*/
	require_once("inc/cookie.php");
	require_once("inc/debug.php");
	require_once("inc/application.php");
	startApp();
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<meta http-equiv="Cache-Control" content="no-cache" />

<script src="js/lib/jquery-1.9.1.js"></script>
<script src="js/routines.js"></script>
<script src="js/ws.js"></script>
<script src="js/graphics_base.js"></script>
<script src="js/game_basics.js"></script>
<title>Mysterion Roulette Remote</title>

<link rel="icon" href="/favicon.png" type="image/png" />
<link rel="shortcut icon" href="/favicon.png" type="image/png" />

<script type="css">
#anicanvas,#helptextcanvas {
	font-family: Palatino;
	src: url('fonts/Palatino.eot');
    src: local('Palatino'), url('fonts/Palatino.eot')  format('embedded-opentype'),
		url('fonts/Palatino.woff') format('woff'),
		url('fonts/Palatino.ttf') format('truetype'),
		url('fonts/Palatino.svg') format('svg');	   
}

#disablecanvas,#helpcanvas{
  font-family: AGFriquer;
	src: url('fonts/AGFriquer.eot');
    src: local('AGFriquer'), url('fonts/AGFriquer.eot')  format('embedded-opentype'),
		url('fonts/AGFriquer.woff') format('woff'),
		url('fonts/AGFriquer.ttf') format('truetype'),
		url('fonts/AGFriquer.svg') format('svg');
}

#tooltip {
	width: 200px;
	position: absolute;
	z-index: 10;
	border: 1px solid #1593db;
	background-color: #e5f5fe;
	font: 1em verdana;
	color: #000;
	padding: 5px;
	opacity: 0.75;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
}

</script>

</head>

<body id="main" bgcolor="#006400">
<section class="container"><script src="js/lib/jquery.modal-box.js"></script>
<script type="text/javascript">
var msg_win;

function LogOff(){
	$.ajax({
		type: "POST",
		url: "/game-root/inc/auth.php?action=exit",
		data: { "id":$("#user").val(),
				"pass":$("#pass").val(),
				"answer":$("#answer").val()},
		dataType: "script",
		success: function(data) {
			//
		},
		error: function() {
			//
			console.log("An error occured when AJAX request was called in auth() function in login.php...");
		}
	});
}
function show_msg(descr,titl,size){
	if(titl==undefined) {titl='Ошибка.'}
        if(size==undefined) {size={w:500,h:250};}
	if (msg_win==undefined){
	msg_win=$('.boxx')._modal_box({
			title:titl,
			description: descr,
			bg_color: "rgba(0,0,0,0)",
                        width:size.w,
                        height:size.h
		});
	msg_win.show_me();		
	} else {
		msg_win.modify_me(titl,descr);
	}
}
function hide_msg(){
	try {
	    msg_win.hide_me();
	} catch(e) {
	    log(e,log_warn);
	}
}
</script>
<?php
    get_remote_addr();
?>
<img class="boxx" src="img/sensor.png" width="1024" height="768" id="sensor" usemap="#sensor" 
     onMouseup="javascript:areaMU(this);" style="position:absolute; left:0; top:0; z-index:10; 
     user-select:none; -webkit-user-select: none; -moz-user-select: none;"/>

<canvas width="1024" height="768" id="text" style="position:absolute;left:0;top:0; 
        z-index:5; user-select:none; -webkit-user-select: none; -moz-user-select: none;" ></canvas>
<canvas width="1024" height="768" id="anim" style="position:absolute;left:0;top:0; 
        z-index:5; user-select:none; -webkit-user-select: none; -moz-user-select: none;" ></canvas>
<canvas width="1024" height="710" id="prev" style="position:absolute;left:0;top:0; 
        z-index:7; display:none; user-select:none; -webkit-user-select: none; -moz-user-select: none;" ></canvas>
<canvas width="1024" height="768" id="coin" style="position:absolute;left:0;top:0; 
        z-index:4;" ></canvas>
<canvas width="1024" height="768" id="push" style="position:absolute;left:0;top:0; 
        z-index:3;" ></canvas>
<canvas width="1024" height="768" id="buttons" style="position:absolute;left:0;top:0; 
        z-index:2;" ></canvas>
<canvas width="1024" height="768" id="active" style="position:absolute;left:0;top:0; 
        z-index:1; display:none;" ></canvas>
<canvas width="1024" height="768" id="inactive" style="position:absolute;left:0;top:0; 
        z-index:1;" ></canvas>
<canvas width="1024" height="768" id="lighted" style="position:absolute;left:0;top:0; 
        z-index:1; display:none;" ></canvas>
<canvas width="1024" height="768" id="temp" style="position:absolute;left:0;top:0; 
        z-index:1; display:none;" ></canvas>
<!--canvas width="1024" height="768" id="coincanvas" style="position:absolute;left:0;top:0; 
        z-index:1; display:none;" ></canvas-->
<canvas width="40" height="592" id="nums" style="position:absolute;left:0;top:0; 
        z-index:1; display:none;" ></canvas>

<map id="mapsensor" name="sensor" style="-webkit-user-select: none; -moz-user-select: none; user-select:none;"><area shape="circle" coords="41,40,29" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="btn_f5" />
<area shape="rect" coords="640,625,704,681" onmousedown="javascript:areaMD(this);" onmouseup="javascript:areaMU(this);" onclick="javascript:areaClick(this);" onmouseover="javascript:areaMM(this);" style="display:block;"  id="btn_coin_5"/><area shape="rect" coords="576,625,640,681" onmousedown="javascript:areaMD(this);" onmouseup="javascript:areaMU(this);" onclick="javascript:areaClick(this);" onmouseover="javascript:areaMM(this);" style="display:block;"  id="btn_coin_4"/><area shape="rect" coords="512,625,576,681" onmousedown="javascript:areaMD(this);" onmouseup="javascript:areaMU(this);" onclick="javascript:areaClick(this);" onmouseover="javascript:areaMM(this);" style="display:block;"  id="btn_coin_3"/><area shape="rect" coords="448,625,512,681" onmousedown="javascript:areaMD(this);" onmouseup="javascript:areaMU(this);" onclick="javascript:areaClick(this);" onmouseover="javascript:areaMM(this);" style="display:block;"  id="btn_coin_2"/><area shape="rect" coords="384,625,448,681" onmousedown="javascript:areaMD(this);" onmouseup="javascript:areaMU(this);" onclick="javascript:areaClick(this);" onmouseover="javascript:areaMM(this);" style="display:block;"  id="btn_coin_1"/><area shape="rect" coords="317,625,381,681" onmousedown="javascript:areaMD(this);" onmouseup="javascript:areaMU(this);" onclick="javascript:areaClick(this);" onmouseover="javascript:areaMM(this);" style="display:block;" id="btn_coin_0"/>
<area shape="rect" coords="640,515,880,562" id="tab25_26_27_28_29_30_31_32_33_34_35_36" style="display:block;" onClick="javascript:areaClick(this);" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onMouseover="javascript:areaMM(this);" />
<area shape="rect" coords="388,515,628,562" id="tab13_14_15_16_17_18_19_20_21_22_23_24" style="display:block;" onClick="javascript:areaClick(this);" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onMouseover="javascript:areaMM(this);" />
<area shape="rect" coords="135,515,375,562" id="tab1_2_3_4_5_6_7_8_9_10_11_12" style="display:block;" onClick="javascript:areaClick(this);" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onMouseover="javascript:areaMM(this);" />
<area shape="rect" coords="765,569,880,616" id="tab19_20_21_22_23_24_25_26_27_28_29_30_31_32_33_34_35_36" style="display:block;" onClick="javascript:areaClick(this);" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onMouseover="javascript:areaMM(this);" />
<area shape="rect" coords="641,569,756,616" id="tab1_3_5_7_9_11_13_15_17_19_21_23_25_27_29_31_33_35" style="display:block;" onClick="javascript:areaClick(this);" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onMouseover="javascript:areaMM(this);" />
<area shape="rect" coords="388,569,503,616" id="tab2_4_6_8_10_11_13_15_17_20_22_24_26_28_29_31_33_35" style="display:block;" onClick="javascript:areaClick(this);" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onMouseover="javascript:areaMM(this);" />
<area shape="rect" coords="512,569,627,616" id="tab1_3_5_7_9_12_14_16_18_19_21_23_25_27_30_32_34_36" style="display:block;" onClick="javascript:areaClick(this);" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onMouseover="javascript:areaMM(this);" />
<area shape="rect" coords="259,569,374,616" id="tab2_4_6_8_10_12_14_16_18_20_22_24_26_28_30_32_34_36" style="display:block;" onClick="javascript:areaClick(this);" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onMouseover="javascript:areaMM(this);" />
<area shape="rect" coords="135,569,250,616" id="tab1_2_3_4_5_6_7_8_9_10_11_12_13_14_15_16_17_18" style="display:block;" onClick="javascript:areaClick(this);" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onMouseover="javascript:areaMM(this);" />
<area shape="rect" coords="881,693,938,752" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="btn_help" />
<area shape="rect" coords="953,693,1011,752" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="btn_exit" />
<area shape="poly" coords="77,343,87,441,103,483,118,489,118,243,96,271" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab0" />
<area shape="rect" coords="240,144,276,205" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" />
<area shape="rect" coords="283,144,319,205" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" />
<area shape="rect" coords="704,143,740,204" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" />
<area shape="rect" coords="747,143,783,204" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" />
<area shape="rect" coords="662,143,698,204" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" />
<area shape="rect" coords="619,143,655,204" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" />
<area shape="rect" coords="578,143,614,204" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" />
<area shape="rect" coords="536,143,572,204" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" />
<area shape="rect" coords="493,143,529,204" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" />
<area shape="rect" coords="408,143,444,204" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" />
<area shape="rect" coords="451,143,487,204" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" />
<area shape="rect" coords="324,143,360,204" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" />
<area shape="rect" coords="367,143,403,204" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" />
<area shape="rect" coords="367,19,403,80" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" />
<area shape="rect" coords="324,19,360,80" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" />
<area shape="rect" coords="451,19,487,80" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" />
<area shape="rect" coords="408,19,444,80" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" />
<area shape="rect" coords="493,19,529,80" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" />
<area shape="rect" coords="536,19,572,80" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" />
<area shape="rect" coords="578,19,614,80" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" />
<area shape="rect" coords="619,19,655,80" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" />
<area shape="rect" coords="662,19,698,80" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" />
<area shape="rect" coords="747,19,783,80" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" />
<area shape="rect" coords="704,19,740,80" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" />
<area shape="rect" coords="283,20,319,81" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" />
<area shape="rect" coords="240,20,276,81" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" />
<area shape="poly" coords="535,86,535,137,734,137,719,124,719,98,730,86" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" />
<area shape="poly" coords="381,116,416,136,527,136,527,87,328,87,349,104" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" />
<area shape="poly" coords="168,107,185,137,402,138,347,111,313,86,188,86" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" />
<area shape="poly" coords="835,204,850,199,822,143,803,143,787,143,787,204" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" />
<area shape="poly" coords="890,42,856,24,830,78,845,82,854,92,909,68" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" />
<area shape="poly" coords="827,20,787,19,788,79,802,79,822,79,852,21" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" />
<area shape="poly" coords="886,185,909,155,855,130,844,141,828,142,858,199" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" />
<area shape="poly" coords="727,107,741,137,833,137,852,108,836,86,744,86" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" />
<area shape="poly" coords="920,106,911,73,857,99,859,112,857,124,912,149" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="race26" />
<area shape="poly" coords="211,18,184,19,200,78,216,81,235,78,233,19" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" />
<area shape="poly" coords="205,206,235,204,234,143,218,143,200,143,185,204" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" />
<area shape="poly" coords="147,194,179,205,196,143,183,142,171,137,128,175" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" />
<area shape="poly" coords="152,28,125,47,172,85,181,81,195,81,179,19" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" />
<area shape="poly" coords="105,79,100,110,159,109,163,97,167,90,122,51" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" />
<area shape="poly" coords="104,142,120,172,169,135,162,126,159,113,98,112" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" />
<area shape="circle" coords="984,634,29" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="btn_replay" />
<area shape="circle" coords="984,562,29" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="btn_undoall" />
<area shape="circle" coords="984,490,29" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="btn_undoone" />
<area shape="circle" coords="33,380,29" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="btn_previous" />
<area shape="rect" coords="894,425,936,498" id="tab1_4_7_10_13_16_19_22_25_28_31_34" style="display:block;" onClick="javascript:areaClick(this);" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onMouseover="javascript:areaMM(this);" />
<area shape="rect" coords="894,332,936,405" id="tab2_5_8_11_14_17_20_23_26_29_32_35" style="display:block;" onClick="javascript:areaClick(this);" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onMouseover="javascript:areaMM(this);" />
<area shape="rect" coords="894,236,936,309" id="tab3_6_9_12_15_18_21_24_27_30_33_36" style="display:block;" onClick="javascript:areaClick(this);" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onMouseover="javascript:areaMM(this);" />
<area shape="rect" coords="644,215,686,238" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab25_26_27"/>
<area shape="rect" coords="685,215,708,238" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab25_26_27_28_29_30"/>
<area shape="rect" coords="644,309,686,333" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab26_27"/>
<area shape="rect" coords="644,237,686,310" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab27" />
<area shape="rect" coords="685,237,708,310" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab27_30"/>
<area shape="rect" coords="685,309,708,333" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab26_27_29_30"/>
<area shape="rect" coords="748,309,771,333" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab29_30_32_33"/>
<area shape="rect" coords="748,237,771,310" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab30_33"/>
<area shape="rect" coords="707,237,749,310" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab30" />
<area shape="rect" coords="707,309,749,333" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab29_30"/>
<area shape="rect" coords="748,215,771,238" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab28_29_30_31_32_33"/>
<area shape="rect" coords="707,215,749,238" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab28_29_30"/>
<area shape="rect" coords="770,215,812,238" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab31_32_33"/>
<area shape="rect" coords="811,215,834,238" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab31_32_33_34_35_36"/>
<area shape="rect" coords="770,309,812,333" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab32_33"/>
<area shape="rect" coords="770,237,812,310" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab33" />
<area shape="rect" coords="811,237,834,310" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab33_36"/>
<area shape="rect" coords="811,309,834,333" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab32_33_35_36"/>
<area shape="rect" coords="833,237,875,310" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab36" />
<area shape="rect" coords="833,309,875,333" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab35_36"/>
<area shape="rect" coords="833,215,875,238" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab34_35_36"/>
<area shape="rect" coords="833,404,875,428" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab34_35"/>
<area shape="rect" coords="833,332,875,405" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab35" />
<area shape="rect" coords="811,404,834,428" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab31_32_34_35"/>
<area shape="rect" coords="811,332,834,405" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab32_35" />
<area shape="rect" coords="770,332,812,405" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab32" />
<area shape="rect" coords="770,404,812,428" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab31_32"/>
<area shape="rect" coords="707,404,749,428" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab28_29"/>
<area shape="rect" coords="707,332,749,405" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab29" />
<area shape="rect" coords="748,332,771,405" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab29_32"/>
<area shape="rect" coords="748,404,771,428" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab28_29_31_32"/>
<area shape="rect" coords="685,404,708,428" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab25_26_28_29"/>
<area shape="rect" coords="685,332,708,405" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab26_29"/>
<area shape="rect" coords="644,332,686,405" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab26" />
<area shape="rect" coords="644,404,686,428" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab25_26"/>
<area shape="rect" coords="644,427,686,500" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab25" />
<area shape="rect" coords="685,427,708,500" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab25_28"/>
<area shape="rect" coords="748,427,771,500" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab28_31"/>
<area shape="rect" coords="707,427,749,500" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab28" />
<area shape="rect" coords="770,427,812,500" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab31" />
<area shape="rect" coords="811,427,834,500" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab31_34" />
<area shape="rect" coords="833,427,875,500" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab34" />
<area shape="rect" coords="392,215,434,238" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab13_14_15"/>
<area shape="rect" coords="433,215,456,238" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab13_14_15_16_17_18"/>
<area shape="rect" coords="392,309,434,333" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab14_15" />
<area shape="rect" coords="392,237,434,310" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab15" />
<area shape="rect" coords="433,237,456,310" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab15_18"/>
<area shape="rect" coords="433,309,456,333" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab14_15_17_18"/>
<area shape="rect" coords="496,309,519,333" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab17_18_20_21"/>
<area shape="rect" coords="496,237,519,310" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab18_21"/>
<area shape="rect" coords="455,237,497,310" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab18" />
<area shape="rect" coords="455,309,497,333" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab17_18"/>
<area shape="rect" coords="496,215,519,238" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab16_17_18_19_20_21"/>
<area shape="rect" coords="455,215,497,238" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab16_17_18"/>
<area shape="rect" coords="518,215,560,238" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab19_20_21"/>
<area shape="rect" coords="559,215,582,238" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab19_20_21_22_23_24"/>
<area shape="rect" coords="518,309,560,333" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab20_21"/>
<area shape="rect" coords="518,237,560,310" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab21" />
<area shape="rect" coords="559,237,582,310" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab21_24"/>
<area shape="rect" coords="559,309,582,333" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab20_21_23_24"/>
<area shape="rect" coords="622,309,645,333" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab23_24_26_27" />
<area shape="rect" coords="622,237,645,310" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab24_27"/>
<area shape="rect" coords="581,237,623,310" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab24" />
<area shape="rect" coords="581,309,623,333" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab23_24"/>
<area shape="rect" coords="622,215,645,238" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab22_23_24_25_26_27"/>
<area shape="rect" coords="581,215,623,238" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab22_23_24"/>
<area shape="rect" coords="581,404,623,428" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab22_23"/>
<area shape="rect" coords="581,332,623,405" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab23" />
<area shape="rect" coords="622,332,645,405" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab23_26"/>
<area shape="rect" coords="622,404,645,428" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab22_23_25_26"/>
<area shape="rect" coords="559,404,582,428" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab19_20_22_23"/>
<area shape="rect" coords="559,332,582,405" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab20_23"/>
<area shape="rect" coords="518,332,560,405" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab20" />
<area shape="rect" coords="518,404,560,428" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab19_20"/>
<area shape="rect" coords="455,404,497,428" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab16_17"/>
<area shape="rect" coords="455,332,497,405" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab17" />
<area shape="rect" coords="496,332,519,405" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab17_20"/>
<area shape="rect" coords="496,404,519,428" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab16_17_19_20"/>
<area shape="rect" coords="433,404,456,428" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab13_14_16_17"/>
<area shape="rect" coords="433,332,456,405" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab14_17"/>
<area shape="rect" coords="392,332,434,405" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab14" />
<area shape="rect" coords="392,404,434,428" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab13_14"/>
<area shape="rect" coords="392,427,434,500" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab13" />
<area shape="rect" coords="433,427,456,500" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab13_16"/>
<area shape="rect" coords="496,427,519,500" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab16_19"/>
<area shape="rect" coords="455,427,497,500" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab16" />
<area shape="rect" coords="518,427,560,500" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab19" />
<area shape="rect" coords="559,427,582,500" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab19_22"/>
<area shape="rect" coords="622,427,645,500" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab22_25"/>
<area shape="rect" coords="581,427,623,500" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab22" />
<area shape="rect" coords="329,427,371,500" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab10" />
<area shape="rect" coords="370,427,393,500" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab10_13"/>
<area shape="rect" coords="307,427,330,500" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab7_10" />
<area shape="rect" coords="266,427,308,500" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab7" />
<area shape="rect" coords="203,427,245,500" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab4" />
<area shape="rect" coords="244,427,267,500" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab4_7" />
<area shape="rect" coords="118,427,141,500" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab0_1" />
<area shape="rect" coords="181,427,204,500" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab1_4" />
<area shape="rect" coords="140,427,182,500" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab1" />
<area shape="rect" coords="140,404,182,428" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab1_2" />
<area shape="rect" coords="140,332,182,405" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab2" />
<area shape="rect" coords="181,332,204,405" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab2_5" />
<area shape="rect" coords="118,332,141,405" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab0_2" />
<area shape="rect" coords="181,404,204,428" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab1_2_4_5" />
<area shape="rect" coords="118,404,141,428" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab0_1_2" />
<area shape="rect" coords="244,404,267,428" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab4_5_7_8" />
<area shape="rect" coords="244,332,267,405" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab5_8"/>
<area shape="rect" coords="203,332,245,405" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab5" />
<area shape="rect" coords="203,404,245,428" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab4_5"/>
<area shape="rect" coords="266,404,308,428" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab7_8"/>
<area shape="rect" coords="266,332,308,405" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab8" />
<area shape="rect" coords="307,332,330,405" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab8_11"/>
<area shape="rect" coords="307,404,330,428" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab7_8_10_11"/>
<area shape="rect" coords="370,404,393,428" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab10_11_13_14"/>
<area shape="rect" coords="370,332,393,405" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab11_14"/>
<area shape="rect" coords="329,332,371,405" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab11" />
<area shape="rect" coords="329,404,371,428" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab10_11"/>
<area shape="rect" coords="329,215,371,238" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab10_11_12" />
<area shape="rect" coords="370,215,393,238" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab10_11_12_13_14_15"/>
<area shape="rect" coords="329,309,371,333" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab11_12"/>
<area shape="rect" coords="329,237,371,310" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab12" />
<area shape="rect" coords="370,237,393,310" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab12_15"/>
<area shape="rect" coords="370,309,393,333" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab11_12_14_15"/>
<area shape="rect" coords="307,309,330,333" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab8_9_11_12"/>
<area shape="rect" coords="307,237,330,310" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab9_12"/>
<area shape="rect" coords="266,237,308,310" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab9" />
<area shape="rect" coords="266,309,308,333" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab8_9"/>
<area shape="rect" coords="307,215,330,238" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab7_8_9_10_11_12" />
<area shape="rect" coords="266,215,308,238" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab7_8_9"/>
<area shape="rect" coords="203,215,245,238" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab4_5_6" />
<area shape="rect" coords="244,215,267,238" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab4_5_6_7_8_9" />
<area shape="rect" coords="203,309,245,333" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab5_6"/>
<area shape="rect" coords="203,237,245,310" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab6" />
<area shape="rect" coords="244,237,267,310" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab6_9" />
<area shape="rect" coords="244,309,267,333" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab5_6_8_9" />
<area shape="rect" coords="118,309,141,333" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab0_2_3" />
<area shape="rect" coords="181,309,204,333" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab2_3_5_6" />
<area shape="rect" coords="118,215,141,238" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab0_1_2_3" />
<area shape="rect" coords="118,237,141,310" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="0_3">
<area shape="rect" coords="181,237,204,310" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab3_6">
<area shape="rect" coords="140,237,182,310" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab3" >
<area shape="rect" coords="140,309,182,333" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab2_3">
<area shape="rect" coords="181,215,204,238" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab1_2_3_4_5_6">
<area shape="rect" coords="140,215,182,238" onMousedown="javascript:areaMD(this);" onMouseup="javascript:areaMU(this);" onClick="javascript:areaClick(this);" onMouseover="javascript:areaMM(this);" style="display:block;" id="tab1_2_3">

<!-- helper objects -->
<area shape="rect" coords="80,331,132,404" style="display:block;" id="h0" />
<area shape="rect" coords="858,78,900,151"  style="display:block;" id="h26" />

<area shape="rect" coords="67,215,119,500" style="display:block;" id="help0" />

<!-- progress bar helper objects -->
<area shape="rect" coords="975,128,1008,134" id="progress_13" />
<area shape="rect" coords="975,121,1008,127" id="progress_12" />
<area shape="rect" coords="975,114,1008,120" id="progress_11" />
<area shape="rect" coords="975,107,1008,113" id="progress_10" />
<area shape="rect" coords="975,100,1008,106" id="progress_9" />
<area shape="rect" coords="975,93,1008,99" id="progress_8" />
<area shape="rect" coords="975,86,1008,92" id="progress_7" />
<area shape="rect" coords="975,79,1008,85" id="progress_6" />
<area shape="rect" coords="975,72,1008,78" id="progress_5" />
<area shape="rect" coords="975,65,1008,71" id="progress_4" />
<area shape="rect" coords="975,58,1008,64" id="progress_3" />
<area shape="rect" coords="975,51,1008,57" id="progress_2" />
<area shape="rect" coords="975,44,1008,50" id="progress_1" />
<area shape="rect" coords="975,37,1008,43" id="progress_0" />
<area shape="rect" coords="975,156,1008,162" id="progress_17" />
<area shape="rect" coords="975,149,1008,155" id="progress_16" />
<area shape="rect" coords="975,142,1008,148" id="progress_15" />
<area shape="rect" coords="975,135,1008,141" id="progress_14" />
<area shape="rect" coords="975,268,1008,274" id="progress_33" />
<area shape="rect" coords="975,261,1008,267" id="progress_32" />
<area shape="rect" coords="975,254,1008,260" id="progress_31" />
<area shape="rect" coords="975,247,1008,253" id="progress_30" />
<area shape="rect" coords="975,240,1008,246" id="progress_29" />
<area shape="rect" coords="975,233,1008,239" id="progress_28" />
<area shape="rect" coords="975,226,1008,232" id="progress_27" />
<area shape="rect" coords="975,219,1008,225" id="progress_26" />
<area shape="rect" coords="975,212,1008,218" id="progress_25" />
<area shape="rect" coords="975,205,1008,211" id="progress_24" />
<area shape="rect" coords="975,198,1008,204" id="progress_23" />
<area shape="rect" coords="975,191,1008,197" id="progress_22" />
<area shape="rect" coords="975,184,1008,190" id="progress_21" />
<area shape="rect" coords="975,177,1008,183" id="progress_20" />
<area shape="rect" coords="975,170,1008,176" id="progress_19" />
<area shape="rect" coords="975,163,1008,169" id="progress_18" />
<area shape="rect" coords="975,282,1008,288" id="progress_35" />
<area shape="rect" coords="975,275,1008,281" id="progress_34" />
</map>
<script type="text/javascript">
console.log('HERE2!');
function addNum(){
	var i=getRandom(0,36);
	console.log('adding number: '+i);
	addNumber(i);
}
function addNum2(){
	var i= +prompt('Enter number between 0 and 36:','0');
	if (i>=0 && i<=36){
		console.log('adding number: '+i);
		addNumber(i);
	}
}
xctx=getCtx('anim');
r=64;st=320; yy=624;
var coin=Array();
var cr=0;
var winval=-1;
var oldwinval=-1;
var winnum=-1;
var oldwinnum=-1;
var oldcr=-1;
var plustext=<?php get_bet_value(0) ?>;
var curr_bet=<?php get_bet_value(2) ?>;
var lang=<?php get_lang() ?>;
coin[0]= loadAniImage('img/chip_x.png',{x:st, 	y:yy, w:64, h:56,bet:<?php get_bet_value(1) ?>, framesCount:5, iscoin:false, interval:120, skipFrames:"0", enable:false, visible:true},xctx);
coin[1]= loadAniImage('img/chip1.png',{x:st+(r*1), y:yy, w:64, h:56,bet:<?php get_bet_value(2) ?>, framesCount:10, skipFrames:"0", interval:100, visible:true},xctx);
coin[2]= loadAniImage('img/chip2.png',{x:st+(r*2), y:yy, w:64, h:56, bet:<?php get_bet_value(3) ?>, framesCount:10, skipFrames:"0", interval:100, enable:false, visible:true},xctx);
coin[3]= loadAniImage('img/chip3.png',{x:st+(r*3), y:yy, w:64, h:56, bet:<?php get_bet_value(4) ?>, framesCount:10, skipFrames:"0", interval:100, enable:false, visible:true},xctx);
coin[4]= loadAniImage('img/chip4.png',{x:st+(r*4), y:yy, w:64, h:56, bet:<?php get_bet_value(5) ?>, framesCount:10, skipFrames:"0", interval:100, enable:false, visible:true},xctx);
coin[5]= loadAniImage('img/chip5.png',{x:st+(r*5), y:yy, w:64, h:56, bet:<?php get_bet_value(6) ?>, framesCount:10, skipFrames:"0", interval:100, enable:false, visible:true},xctx);

console.log('HERE!');
console.log(coin);
</script>
<script src="js/application.js"></script>
<script src="js/inputs.js"></script>
<?php
$dbg='<input type="button" onClick="changeGameState(! game_state);" value="Enable/Disable Screen" style="position: absolute; left: 1024px;">
<input type="button" onClick="cr= +prompt(\'Enter current credit:\',\'100\');" value="Set Credit!" style="position: absolute; left: 1024px; top:70px;" />
<input type="button" onClick="addNum();" value="Add number (random)" style="position: absolute; left: 1024px; top:115px;" />
<input type="button" onClick="startCountdown();" value="Start Countdown!" style="position: absolute; left: 1024px; top:155px;" />
<input type="button" onClick="addNum2();" value="Add number" style="position: absolute; left: 1024px; top:185px;" />';
if (getDmodeLevel()==2) {
    echo $dbg;
} else if (getDmodeLevel()==1) {
    $lst=getDmodeList();
    if (in_array($_SERVER['REMOTE_ADDR'],$lst)){
        echo $dbg;
    }
}
?>
</body>
</html>
