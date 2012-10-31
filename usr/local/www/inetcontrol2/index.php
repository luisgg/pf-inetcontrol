<?php
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
return $bytes[0].".".$bytes[1].".".$bytes[0].".0/24";

} 
echo '<br><br>';
echo '<center><div class="text_mat"><font color=FF0000>Desactivar internet para el aula '.getIP()."</font></div>";
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
<input name="submit" type="submit" value="  Entrar  " class="botones">
</td>
</tr>

</table>

</td></tr>
</table>

<br>
<div class="text_mat"><font color=666666>MiauLDAP | Autenticacion en PHP + LDAP | para quien lo necesite | Venezuela Edo. Trujillo</font>
</div>


