<?php
require_once("pf-inetcontrol.inc");

$lanip=getLAN();
$status=easyrule_block_alias_test(getIP(), 24);

echo '<br><br>';
echo '<center><div class="text_mat"><font color=FF0000>Acceso desde el aula '.getAula($lanip)."</font></div>";
if ($status) {
	echo '<center><div class="text_mat"><font color=FF0000>Conexi&oacute;n a internet: HABILITADA</font></div>';
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
if ( $status=easyrule_block_alias_test(getIP(), 24) ) {
	echo '<input name="submit" type="submit" value="  DESHABILITAR INTERNET  " class="botones">';
	} else {
	echo '<input name="submit" type="submit" value="  HABILITAR INTERNET  " class="botones">';
}
?>

</td>
</tr>

</table>

</td></tr>
</table>

<br>
<div class="text_mat"><font color=666666>MiauLDAP | Autenticacion en PHP + LDAP | para quien lo necesite | Venezuela Edo. Trujillo</font>
</div>


