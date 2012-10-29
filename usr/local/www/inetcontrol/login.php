<?php 
// function authenticate
function authenticate($user, $password) { 
	$ldap_host = "10.10.0.250";
	$ldap_dn = "uid=" . $user .",ou=usuarios,dc=centro,dc=com";
	echo $ldap_dn;
	$ldap = ldap_connect($ldap_host);
	ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
	// verify user and password
	if($bind = @ldap_bind($ldap, $ldap_dn, $password)) {
		// valid
		// check presence in groups 
		return true ;
	} else {
		// invalid name or password
		return false;
	} 
}

// check to see if user is logging out
if(isset($_GET['out'])) {
    // destroy session
    session_unset();
    $_SESSION = array();
    unset($_SESSION['user'],$_SESSION['access']);
    session_destroy();
}
// check to see if login form has been submitted
if(isset($_POST['userLogin'])){
    // run information through authenticator
    if(authenticate($_POST['userLogin'],$_POST['userPassword']))
    {
        // authentication passed
        header("Location: index.php");
        die();
    } else {
        // authentication failed
        $error = 1;
    }
}
// output error to user
if (isset($error)) echo "Login failed: Incorrect user name, password, or rights<br />";
// output logout success
if (isset($_GET['out'])) echo "Logout successful<br />";
?>
<form method="post" action="login.php">
    User: <input type="text" name="userLogin" /><br />
    Password: <input type="password" name="userPassword" /><br />
    <input type="submit" name="submit" value="Submit" />
</form>


