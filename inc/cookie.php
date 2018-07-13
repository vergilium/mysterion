<?php
class SESSION
{
	function SESSION()
	{
		//session_start();
	}
	function session_end()
	{
		session_destroy();
	}
	function auth()
	{
		if (isset($_SESSION['game_id']))
		{
			return true;
		} else {
			return false;
		}
	}
	function set_auth()
	{
		$_SESSION["game_id"]='registered_session';
	}
	function save_data($key,$data)
	{
		$_SESSION[$key]=$data;
	}
	function get_data($key)
	{
		if (isset($_SESSION[$key])) return $_SESSION[$key];
	}
}

class COOKIE
{
	function auth()
	{
		if (isset($_COOKIE['game_id']))
		{
			return true;
		} else {
			return false;
		}
	}
	function set_auth()
	{
		setcookie('game_id','registered_session',time()+60*60*24*30);
	}
	function session_end()
	{
		setcookie("game_id", "", time() - 3600);
	}
	function save_data($key, $val)
	{
		setcookie($key,$val,time()+60*60*24*30);
	}
	function get_data($key)
	{
		if (isset($_COOKIE[$key])) return $_COOKIE[$key];
	}
}
?>