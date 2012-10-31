<?php
echo "iniciando";
// variables de autenticacion y LDAP
    $ldap['user']              = $_POST["usuario"];
    $ldap['pass']              = $_POST["clave"];
    $ldap['host']              = '10.10.0.250'; // nombre del host o servidor
    $ldap['port']              = 389; // puerto del LDAP en el servidor
    $ldap['dn']                = 'uid='.$ldap['user'].',ou=usuarios,dc=centro,dc=com'; // modificar respecto a los valores del LDAP
    $ldap['base']              = ' ';
	// conexion a ldap
     $ldap['conn'] = ldap_connect( $ldap['host'], $ldap['port'] );
     ldap_set_option($ldap['conn'], LDAP_OPT_PROTOCOL_VERSION, 3);

	// match de usuario y password
     $ldap['bind'] = ldap_bind( $ldap['conn'], $ldap['dn'], $ldap['pass'] );
	
	
     if ($ldap['bind']){
		
    session_start();



    session_cache_limiter('nocache,private');    



    $_SESSION['usuario']=$_POST["usuario"];



    $_SESSION['clave']=$_POST["clave"];

    $_SESSION['usuario_fecha']= date("Y-n-j H:i:s");

        

    $pag=$_SERVER['PHP_SELF'];
	
	echo "<head><meta http-equiv=\"refresh\" content=\"0; url=asegurada.php\"></head>";
			
		}
	else{
			//echo "LDAP bind failed...";
	        print "<body onload=\"window.location='error.html';\">";
        	print "</body>";
        	exit();
   	}    

?>
