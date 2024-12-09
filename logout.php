<?php
//para cerrar sesion 
session_start();
session_unset();
session_destroy();
setcookie("user", $_COOKIE["user"], time()-3600*24);
header("Location:inicio.php");
exit();
?>