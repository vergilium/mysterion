<?php
$bytehandId = 7175;
$bytehandKey = "3E9FD82047B28EAA";
$bytehandFrom = "Mysterion";

function sendSMS($to, $text)
{
    global $bytehandId;
    global $bytehandKey;
    global $bytehandFrom;

    $result = @file_get_contents('http://bytehand.com:3800/send?id='.$bytehandId.'&key='.$bytehandKey.'&to='.urlencode($to).'&from='.urlencode($bytehandFrom).'&text='.urlencode($text));
    if ($result === false)
        return false;
    else
        return true;
}

function generatePassword($length = 10){
  //$chars = 'abdefhiknrstyzABDEFGHKNQRSTYZ23456789';
  //$chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
  $chars = '0123456789';  
  $numChars = strlen($chars);
  $string = '';
  for ($i = 0; $i < $length; $i++) {
    $string .= substr($chars, rand(1, $numChars) - 1, 1);
  }
  return $string;
}

function get_mem($key){
	// пытаемся взять данные из кэша
	$memcache = new Memcache;
	$memcache->connect('localhost',11211) or die("Can not connect to Memcache!");
	if ($memcache->get($key)) {
		 $get_result = $memcache->get($key);
		 return($get_result);
	}
	else {
		return false;
	}	
}

function set_mem($key,$value){
	$memcache = new Memcache;
	$memcache->connect('localhost',11211) or die("Can not connect to Memcache!");
	if ($value=='kill'){
		$memcache->delete($key);
	} else {
		$memcache->set($key, $value, false, 120);
	}
}
?>