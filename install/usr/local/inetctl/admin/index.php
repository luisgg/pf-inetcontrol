<?php
header("Expires: Tue, 01 Jul 2001 06:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require_once("pf-inetcontrol.inc");

$lanip=getLAN();

echo '<center><table cellspacing="0" cellpadding="0">';
echo '<tr><td>';
echo '<center><img src="images/lock.png" alt="lock"></center>';
echo '</td>';
echo '<td><center><h1>Acceso a Internet</h1></center>';
echo '<center><div class="text_mat"><font color=FF0000>Accediendo desde el '.getAula($lanip)."</font></div></td>";
echo '</tr></table></center>';

?>
<table>
<tr><td></td></tr>
</table>
<center><hr><br>

<table cellspacing="0" cellpadding="0">
<tr><td colspan="2" align="center" class="texto_items"> 

<div>
<?php
$locktype = easyrule_block_alias_type(getIP(), 24);
if ( $locktype == 0) {
	$imagefile = "images/green.png";
	$text = "Internet HABILITADO";
} elseif (  $locktype == 1 ){
	$imagefile = "images/red.png";
	$text = "Internet DESHABILITADO";
} else {
	$imagefile = "images/yellow.png";
	$text = "Internet LIMITADO";
}

echo '<img src="'. $imagefile .'" alt="status">';
echo '<br><br>';
echo '<center><div class="text_mat"><font color=FF0000>'.$text.'</font></div>';

?>
</div>
</td>

<td width="10"></td>
<td width="1" bgcolor="#CCCCCC"></td>
<td width="10"></td>

<td>
<table>

<form action="auth.php" method="POST">

<tr><td class="texto_items">Usuario</td><td><input class="texto_inputs" type="text" name="usuario" size="20"  class="imputbox"></td><tr>
<tr><td class="texto_items">Clave</td><td><input class="texto_inputs" type="password" name="clave" size="20"  class="imputbox"></td></tr>

<?php
if ( $status=easyrule_block_alias_test(getIP(), 24) ) {
	echo '<tr><td class="texto_items"><input class="texto_inputs" type="checkbox" name="semiblock" value="YES" class="imputbox" checked ></td><td>Permitir acceso a servicios web del centro</td></tr>';
	echo '<tr><td colspan="2" align="center">';
	echo '<input name="submit" type="submit" value="  DESHABILITAR INTERNET  " class="botones">';
	} else {
	echo '<tr><td colspan="2" align="center">';
	echo '<input name="submit" type="submit" value="  HABILITAR INTERNET  " class="botones">';
}
?>

</td>
</tr>

</table>

</td></tr>
</table>

<br>


