<?php
	require_once("pf-inetcontrol.inc");
	require_once("pfsense-utils.inc");
	require_once("filter.inc");
	require_once("shaper.inc");

	// variables de autenticacion y LDAP
	$ldap['user']              = $_POST["usuario"];
	$ldap['pass']              = $_POST["clave"];
	$ldap['host']              = '10.10.0.250'; // nombre del host o servidor
	$ldap['port']              = 389; // puerto del LDAP en el servidor
	$ldap['dn']                = 'uid='.$ldap['user'].',ou=usuarios,dc=centro,dc=com'; // modificar respecto a los valores del LDAP
	$ldap['base']              = 'ou=grupos,dc=centro,dc=com';
	$ldap['filter']            = "(cn=PROFESORES)";
	$ldap['searchdn']          = 'uid=joindomain,ou=usuarios,dc=centro,dc=com';
	$ldap['searchpass']          = 'joindomain';
	// conexion a ldap
	$ldap['conn'] = ldap_connect( $ldap['host'], $ldap['port'] );
	ldap_set_option($ldap['conn'], LDAP_OPT_PROTOCOL_VERSION, 3);

	$is_valid_user = false;
	// match de usuario y password
	$ldap['bind'] = ldap_bind( $ldap['conn'], $ldap['dn'], $ldap['pass'] );
	if ($ldap['bind']){
		if ( ldap_bind( $ldap['conn'], $ldap['searchdn'], $ldap['searchpass']) ) {
			// pertenencia a grupo
			if ($result = ldap_search($ldap['conn'], $ldap['base'], $ldap['filter'], array("memberUid"))) {
				$entries = ldap_get_entries($ldap['conn'], $result);
				for ($i=0; $i<$entries[0]["memberuid"]['count'];$i++) {
					$uid = $entries[0]["memberuid"][$i];
					if ( $uid == $ldap['user'] ) {
						$is_valid_user = true;
						break ;
					}
				}
			}
		}
		ldap_unbind($ldap['conn']);
	}
	if ( $is_valid_user ) {
		//session_start();
		//session_cache_limiter('nocache,private');
		//$_SESSION['usuario']=$_POST["usuario"];
		//$_SESSION['clave']=$_POST["clave"];
		//$_SESSION['usuario_fecha']= date("Y-n-j H:i:s");
    		//$pag=$_SERVER['PHP_SELF'];
		$lan_ip=getIP();
		if ( easyrule_block_alias_test($lan_ip, 24) ) {
			$action = 'block';
		} else {
		        $action = 'allow';
		}
		if ( easyrule_block_host($action, $lan_ip, 24) ) {
			// load success page
	        	print "<body onload=\"window.location='success-".$action.".html';\">";
        		print "</body>";
        		exit();
		}
	}
	print "<body onload=\"window.location='error.html';\">";
        print "</body>";
        exit();

?>
