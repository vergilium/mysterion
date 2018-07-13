<?php
/*
*
*	auth.php
*		модуль авторизации клиента.
*		содержит весь функционал авторизации
*
*
*/

//error_reporting(-1);
//ini_set('display_errors', 'On');

require_once("cookie.php");
require_once("../../sql.php");
require_once("func.php");
require_once("debug.php");

// константы, для понятной передачи "статуса" логина
define("USER_NOT_FOUND",0);
define("WRONG_PASSWORD",-1);
define("USER_FOUND",1);

$sess=new SESSION;
$SQ=new SQL_QUERY;

// получаем все переданные переменные
$user=$_POST['id'];
$pass=$_POST['pass'];
$answ=$_POST['answer'];
// авторизация проходит в несколько этапов. получаем текущий этап
$act=$_GET['action'];

// функция проверки существования пользователя (не до конца доработана)
function getRegUser($user){
	global $SQ;
	
	$regusr=$SQ->get_terminal($user);
	if (is_array($regusr)==true){
		return USER_FOUND;
	} else {
		return USER_NOT_FOUND;
	}
}

// функция проверки ответного кода
function checkAnswer($user, $answ){
if (getDmodeLevel()==0){
    $memansw=get_mem($user);

    if ($answ==$memansw){
            return true;
    } else {
            return false;
    }
    } else {
            return true;
    }
}

// этап проверки пользователя/терминала
if ($act=="enter"){
	if ($user==''){
		echo("alert('ID не может быть пустым!');");
		exit();
	}
	$u_code=getRegUser($user);
	// пользователь найден
	if ($u_code==USER_FOUND) {
		echo("$('#connect').removeAttr('disabled');");
		if (getDmodeLevel!=2){
			$pass=generatePassword(4);
			set_mem($user,$pass);
			//sendSMS("+380631635202","Ваш персональный код:$pass");
                        echo("alert('Password:'+$pass);"
                                . "$('#user').keypad('hide');");
		}
	//  пользователь не найден. выводим причину:
	} elseif ($u_code==USER_NOT_FOUND) {
		echo("alert('Терминал не зарегистрирован!');");
	} else {
		echo("alert('Имя пользователя или пароль не верны!');");
	}
	
// этап проверки ответного кода
} else if ($act=='login'){
	$u_code=getRegUser($user);
	$r_answ=checkAnswer($user, $answ);
        
	if ($u_code==USER_FOUND && $r_answ==true){
		$sess->set_auth();
		$sess->save_data('TRM',$user);
                echo("location.href = 'http://'+document.domain+'/';");
                
	} elseif ($r_answ==false) {
		echo("alert('Не верный код подтверждения!');");
	} else {
		echo("alert('Ошибка авторизации!');");
	}
// завершение сеанса работы терминала
} else if ($act=='exit'){
		$sess->session_end();
		echo("location.href = 'http://'+document.domain+'/';");
}
?>