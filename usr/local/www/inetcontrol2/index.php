<?php
require_once("pf-inetcontrol.inc");

function getIP() {
	// $ip;
	if (getenv("HTTP_CLIENT_IP"))
		$ip = getenv("HTTP_CLIENT_IP");
	else if(getenv("HTTP_X_FORWARDED_FOR"))
		$ip = getenv("HTTP_X_FORWARDED_FOR");
	else if(getenv("REMOTE_ADDR"))
		$ip = getenv("REMOTE_ADDR");
	else
		$ip = "UNKNOWN";

	$bytes=explode(".",$ip);
	return $bytes[0].".".$bytes[1].".".$bytes[2].".0";
}

function getLAN() {
	$ip=getIP();
	return $ip."/24";
}

function getAula($ip) {
	$bytes=explode(".",$ip);
	$piso=$bytes[1]-1 ;
	return "Piso: ".$piso." Aula: ".$bytes[2]." (Red: ".$ip.")" ;
}

$lanip=getLAN();
$status=easyrule_block_alias_test(getIP(), 24);

echo '<br><br>';
echo '<center><div class="text_mat"><font color=FF0000>Acceso desde el aula '.getAula($lanip)."</font></div>";
if ($status) {
	echo '<center><div class="text_mat"><font color=FF0000>Conexi&oacutte;n a internet: HABILITADA</font></div>';
} else {
	echo '<center><div class="text_mat"><font color=FF0000>Conexi&oacute;n a internet: DESHABILITADA</font></div>';
}

?>
<table>
<tr><td></td></tr>
</table>
<center><br><br><br><br>

<table cellspacing="0" cellpadding="0">
<tr><td colspan="2" align="center" class="texto_items"> 

<div>
MiauLDAP
<div class="text_mat">by dk4nno</div>
</div>
</td>

<td width="10"></td>
<td width="1" bgcolor="#CCCCCC"></td>
<td width="10"></td>

<td>
<table>

<form action="auth.php" method="POST">

<tr><td class="texto_items">Usuario</td><td><input class="texto_inputs" type="text" name="usuario" size="20"  class="imputbox"></td><tr>
<tr><td class="texto_items">Clave</td><td><input class="texto_inputs" type="password" name="clave" size="20"  class="imputbox"></td>
</tr>
<tr><td colspan="2" align="center">
<div class="text_error"><?echo $error_LDAP;?></div>
<?php
echo '<input name="submit" type="submit" value="  Entrar  " class="botones">';
?>

</td>
</tr>

</table>

</td></tr>
</table>

<br>
<div class="text_mat"><font color=666666>MiauLDAP | Autenticacion en PHP + LDAP | para quien lo necesite | Venezuela Edo. Trujillo</font>
</div>


