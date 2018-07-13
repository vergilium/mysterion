<?php
/*
*
*	login.php
*		файл отображения окна авторизации
*
*
*/
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Cache-Control" content="no-cache" />
<title>Mysterion Roulette Remote LogIn</title>

<link rel="icon" href="/favicon.png" type="image/png" />
<link rel="shortcut icon" href="/favicon.png" type="image/png" />

<script src="js/lib/jquery-1.9.1.js"></script>
<script src="js/lib/hoverIntent.js"></script>
<script src="js/lib/tooltip.js"></script>
<script src="js/ws.js"></script>
<link type="text/css" rel="stylesheet" href="style/tooltip.css">

<style type="text/css">
body {
	/*******************************Фоновое изображение******************************/
	background: url(img/bggrid.png) top, -webkit-linear-gradient(left, #600, #100);	/* Chrome 10+, Safari 5.1+ */
	background: url(img/bggrid.png) top, -ms-linear-gradient(left, #600, #100);		/*IE10+*/
	background: url(img/bggrid.png) top, -moz-linear-gradient(left, #600, #100);		/*Firefox3.6+*/
	background: url(img/bggrid.png) top, -o-linear-gradient(left, #600, #100); 		/* Opera 11.10+ */
	background: url(img/bggrid.png) top, linear-gradient(left, #600, #100);			/* CSS3 */
	background-color:#300;																/*для старых или других браузеров*/
	/*text-shadow:#666 1px 1px 1px;*/	
	}
table {
	font-family: AGFriquer;
	src: url('fonts/AGFriquer.eot');
    src: local('AGFriquer'), url('fonts/AGFriquer.eot')  format('embedded-opentype'),
		url('fonts/AGFriquer.woff') format('woff'),
		url('fonts/AGFriquer.ttf') format('truetype'),
		url('fonts/AGFriquer.svg') format('svg');
	font-size:20px;
	text-shadow: 3px 3px 3px #000000;
	position: relative;
	zoom: 1;	
}

/***  KEYPAD STYLES  ***/
.keypad-popup.darkKeypad { background: #333; } 
.darkKeypad .keypad-key, .darkKeypad .keypad-special { width: 2.75em; height: 2em; background: transparent; 
    color: #fff; border: 0.125em outset #fff; font-weight: bold; } 
.darkKeypad .keypad-key-down { border: 0.125em inset #fff; } 
.darkKeypad .keypad-shift, .darkKeypad .keypad-back, 
.darkKeypad .keypad-clear, .darkKeypad .keypad-close, 
.darkKeypad .keypad-enter { width: 4.25em; } 
.darkKeypad .keypad-spacebar { width: 17.75em; } 
.darkKeypad .keypad-space { width: 2.75em; } 
.darkKeypad .keypad-half-space { width: 1.375em; }
/*.keypad-key[name="0"] { width: 4.25em; }*/
/***  END KEYPAD STYLES  ***/

</style>

<script type="text/javascript">
function auth(act) {
$.ajax({
    type: "POST",
    url: "/game-root/inc/auth.php?action="+act,
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

// функция принимает элемент, который необходимо центрировать
function alignCenter(elem) {
  elem.css({ // назначение координат left и top
    left: ($(window).width() - elem.width()) / 2 + 'px',
    top: ($(window).height() - elem.height()) / 2 + 'px'
  })
};
}
</script>

<link href="style/jquery.keypad.alt.css" rel="stylesheet" type="text/css"> 
<script type="text/javascript" src="js/lib/jquery.keypad.js"></script> 

<script type="text/javascript"> 
var qwertyLayout = [
'qwertyuiop' + $.keypad.BACK + $.keypad.HALF_SPACE + '789'
  ,
$.keypad.HALF_SPACE + 'asdfghjkl' + $.keypad.ENTER + $.keypad.SPACE + '456'
  ,
$.keypad.SPACE + 'zxcvbnm' + $.keypad.SPACE +
  $.keypad.SPACE + $.keypad.SPACE + $.keypad.SPACE + '123',
  $.keypad.HALF_SPACE + $.keypad.SPACE + $.keypad.SPACE_BAR + $.keypad.SPACE + $.keypad.SPACE + $.keypad.SPACE + $.keypad.SPACE + $.keypad.HALF_SPACE + '0']	

var keypadLayout = [
'789',
'456',
'123',
'0' + $.keypad.ENTER]	
  
$(function () { 
$.keypad.setDefaults({prompt: '',keypadClass: 'darkKeypad'});
$.keypad.setDefaults($.keypad.regional['']);
$.keypad.setDefaults({keypadOnly: false,
	layout: qwertyLayout});
	
$('#pass').keypad();
$('#user').keypad({
	onKeypress: function(key, value, inst) {
		if (key=="\r")
		{
			$('#user').keypad('hide');
			auth('enter');
		}
    } 
	});
$('#answer').keypad({
    onKeypress: function(key, value, inst) { 
		if (key=="\r")
		{
			$('#answer').keypad('hide');
			auth('login');
		}
	},
    layout: keypadLayout
    });
});
</script>

</head>

<body bgcolor="#006400" text="#e8a048">

<div id="cent" style="position:absolute; top:50%; left:50%; margin-left:-181px; margin-top:-216px;">
<table border="0" align="center">
  <tr align="center">
  	<td colspan="3">
    	<img src="img/logo_m.png"  />
        <br />
        <h3>Добро Пожаловать!</h3>
    </td>
  </tr>
  <tr>
    <td><label>ID:</label></td>
    <td><input type="text" name="id" id="user" align="right" /></td>
	<td><img src="img/help.png" class="linktip" title="Введите Ваш идентификатор, указанный на карте." /></td>
  </tr>
  <tr style="display:none;">
    <td><label>Пароль:</label></td>
    <td><input type="password" name="pass" id="pass" /></td>
	<td><img src="img/help.png" class="linktip" title="Введите Ваш пароль, указанный при регистрации." /></td>	
  </tr>
  <tr align="right">
    <td>&nbsp;</td>
    <td><input type="button" name="login" value="Получить код" onclick="auth('enter');" /></td>
	<td>&nbsp;</td>
  </tr>
  <tr>
    <td><label>Код подтверждения:</label></td>
    <td><input type="text" name="answer" id="answer" /></td>
	<td><img src="img/help.png" class="linktip" title="Введите код подтверждения. Он был выслан на мобильный телефон, указанный при регистрации, сразу после нажатия кнопки 'Получить код'.<br />Обычно код приходит в течение 1-5 минут.<br />Код четырехзначный цифровой." /></td>	
  </tr>
  <tr align="right">
    <td>&nbsp;</td>
    <td><input type="submit" id="connect" name="connect" value="Войти" disabled="disabled" onclick="auth('login');" /></td>
	<td>&nbsp;</td>	
  </tr>
  <tr>
    <td colspan="1" align="left">
    	<img src="img/m_logo.png" />
    </td>
    <td colspan="2" valign="bottom" align="right">
    	<code><font size="1"><?php require_once("version.php"); ?></font></code>
    </td>
    <!--td colspan="1" valign="bottom" align="right">
          <img width="64px" src="img/html5-small.png" />
    </td-->
  </tr>
</table>
</div>
<script type="text/javascript">
	//alignCenter("cent");
</script>
</body>
</html>