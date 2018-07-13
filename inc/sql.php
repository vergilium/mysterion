<?php
$user = "rserver";
$pass = "slkjf;lrgkje095uKJHKjku7y837iKUJHGIDGF";
$serverName = "(local)\RSERVERSQL"; 
$base = "rbase"; 

/* Connect using Windows Authentication. */  
try  
{  
$db = new PDO( "sqlsrv:server=$serverName ; Database=$base", $user, $pass);  
$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION ); 
}  
catch(Exception $e)  
{   
die( print_r( $e->getMessage() ) );   
}  
?>