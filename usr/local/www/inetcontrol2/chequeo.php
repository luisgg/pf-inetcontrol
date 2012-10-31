<?php
session_start();

session_cache_limiter('nocache,private');  

if (!isset($_SESSION['usuario']) && !isset($_SESSION['clave'])){

session_destroy();
	echo "<head><meta http-equiv=\"refresh\" content=\"0; url=error-sesion.html\"></head>";
exit;

}

?>

