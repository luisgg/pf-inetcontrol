<!-- NOTE: Redirection requires a symlink pf-inetctl/images in pf-inetctl-error document root -->
<html><body>
<center>
<?php
require_once("pf-inetcontrol.inc");
$locktype = easyrule_block_alias_type(getIP(), 24);
$bottomtext="";
if ( $locktype == 0) {
        $imagefile = "/images/pf-inetctl-green.png";
	$headtext = "Acceso a Internet permitido";
} elseif (  $locktype == 1 ){
        $imagefile = "/images/pf-inetctl-red.png";
	$headtext = "Oops! Acceso a Internet deshabilitado";
} else {
        $imagefile = "/images/pf-inetctl-yellow.png";
	$headtext = "Oops! Navegaci&oacute;n restringida a los servicios web del centro";
	$bottomtext = '<a href="http://www.ausiasmarch.net">Web instituto</a><br /><a href="http://moodle.ausiasmarch.net">Moodle</a>';
}
echo "<h1>".$headtext."</h1>";
echo '<img src="'.$imagefile.'" alt="Bloqueo de internet">';
echo "<p><br />".$bottomtext."</p>";
?>
</center>
</body></html>
