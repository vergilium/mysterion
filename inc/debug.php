<?php 
// debug.php - debug functions file
// DO NOT MODIFY!! THIS FILE AUTOMATICALLY CREATED BY WebSocketServer FOR DEBUG ROUTINES
// DLVL -->
function getDmodeLevel() { 
 return 0; 
}; 
// <-- DLVL
// DLST -->
function getDmodeList() { 
 $list=array();
 return $list; 
};
// <-- DLST
if (isset($_GET['get_dmode'])){
 echo(getDmodeLevel());
};
?>