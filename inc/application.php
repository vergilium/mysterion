<?php
/*********************************
*
* application.php
* 	так-называемый "инициализационный"
*	php файл. Содержит функции инициализации приложения
*	не доступные извне
*
**********************************
*/

//require_once("../sql.php");
require_once("debug.php");
/*
получение IP сервера, клиента и указание порта вебсокет подключения:
c_ip	: IP клиента
trm		: айди терминала
s_ip	: IP вебсокет сервера
s_port	: порт вебсокет сервера
-----------------------------------------------
($_SERVER['SERVER_ADDR']),   192.168.10.43
*/
function get_remote_addr(){
	$sess=new SESSION;
	if (getDmodeLevel()==0){
            $trm=$sess->get_data('TRM');
        } else {
            $trm='of0001';
        }
	echo "<input id='c_ip' type='hidden' value='".$_SERVER['REMOTE_ADDR']."' />
<input id='trm' type='hidden' value='$trm' />
<input id='s_ip' type='hidden' value='192.168.156.10' />
<input id='s_port' type='hidden' value='16669' />\n
";
}
// настройки ставок
function get_bet_value($idx){
	$betarr=array("''",-1,1,5,10,25,100);
	echo $betarr[$idx];
}
// язык (русский - 'ru',английский - '')
function get_lang(){
	echo "'ru'";
}
// задаём критерии по максимальным и минимальным ставкам (пока не реализовано!)
function get_max_bet($mbType){
	$all_bets=array("maxbet1_36"=>"25",
					"maxbet1_18"=>"50",
					"maxbet1_12"=>"75",
					"maxbet1_9"=>"100",
					"maxbet1_6"=>"150",
					"maxbet1_3"=>"300",
					"maxbet1_2"=>"450",
					"minbet1_3"=>"3",
					"minbet1_2"=>"5");
	echo $all_bets[$mbType];
}

// функция инициализации приложения
function startApp(){
	$sess = new SESSION;
	$dmode=getDmodeLevel();
		
	// выясняем какой задан "уровень" отладки
	switch ($dmode){
		// никакого. работаем как положено
		case 0:
			if (!$sess->auth()) {
				require_once("login.php");
				exit();
			}		
			break;
		// режим отладки для конкретных адресов, прописанных в файле
		case 1:
			$lst=getDmodeList();
			for ($i=0;$i<count($lst);$i++){
				if ($lst[$i]==$_SERVER['REMOTE_ADDR']) return;				
			}
			if (!$sess->auth()) {
				require_once("login.php");
				exit();
			}			
			break;
		// режим отладки для всех. никто не запрашивает авторизацию
		case 2:
			break;
		default:
			echo('<b><font color="red">Error in debug.php file! Restart the Server, and refresh screen!</font></b>');
	}
}

// получение кредита для текущего терминала
function get_curr_credit(){
	$SQ=new SQL_QUERY;
	if (getDmodeLevel()==0){
            $sess=new SESSION;
            $trm=$sess->get_data('TRM');
        } else {
            $trm='of0001';
        }
	$regusr=$SQ->get_terminal($trm);
	echo (empty($regusr['term_credit']{0})==false) ? $regusr['term_credit'] : '0';
}
?>