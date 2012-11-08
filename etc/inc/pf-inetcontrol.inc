<?php
/*
	easyrule.inc.php

	Copyright (C) 2009-2010 Jim Pingle (jpingle@gmail.com)
	Originally Sponsored By Anathematic @ pfSense Forums
	All rights reserved.

	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met:

	1. Redistributions of source code must retain the above copyright notice,
	this list of conditions and the following disclaimer.

	2. Redistributions in binary form must reproduce the above copyright
	notice, this list of conditions and the following disclaimer in the
	documentation and/or other materials provided with the distribution.

	THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
	INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
	AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
	AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
	OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
	SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
	INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
	CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
	ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
	POSSIBILITY OF SUCH DAMAGE.
*/
/*
	pfSense_BUILDER_BINARIES:
	pfSense_MODULE:	filter
*/

$blockaliasname = 'BlockInetLans';
$managedaliasname = 'ManagedInetLans';
$protocols_with_ports = array('tcp', 'udp');
require_once("functions.inc");
require_once("util.inc");
require_once("config.inc");

function easyrule_block_alias_getid() {
	global $blockaliasname, $config;
	if (!is_array($config['aliases']))
		return false;

	/* Hunt down an alias with the name we want, return its id */
	foreach ($config['aliases']['alias'] as $aliasid => $alias)
		if ($alias['name'] == $blockaliasname)
			return $aliasid;

	return false;
}

function managed_alias_getid() {
	global $managedaliasname, $config;
	if (!is_array($config['aliases']))
		return false;

	/* Hunt down an alias with the name we want, return its id */
	foreach ($config['aliases']['alias'] as $aliasid => $alias)
		if ($alias['name'] == $managedaliasname)
			return $aliasid;

	return false;
}

function easyrule_block_alias_del($host, $int = '24') {
	global $blockaliasname, $config;
	/* If the host isn't a valid IP address, bail */
	if (!is_ipaddr($host))
		return false;

	/* If there are no aliases, start an array */
	if (!is_array($config['aliases']['alias']))
		return false;

	$a_aliases = &$config['aliases']['alias'];

	/* Try to get the ID if the alias already exists */
	$id = easyrule_block_alias_getid();
	if ($id === false)
		return false;

	$address_match = false;
	$a_address = explode(" ", $a_aliases[$id]['address']);
	foreach ($a_address as $aliasid => $alias){
		if ($alias == $host . '/' . $int){
			unset($a_address[$aliasid]);
			$address_match = true;
		}
	}
	if ($address_match) {
		if (count($a_address) == 0) {
			$a_aliases[$id]['address'] = '1.2.3.4/32';
		} else {
			$a_aliases[$id]['address'] = implode(" ", $a_address);
		}
		return true ;
	}
	return false;

}

function easyrule_block_alias_test($host, $int = '24') {
	global $blockaliasname, $config;
        /* If the host isn't a valid IP address, bail */
        if (!is_ipaddr($host))
                return false;

        /* If there are no aliases, start an array */
        if (!is_array($config['aliases']['alias']))
		return true;
	$a_aliases = &$config['aliases']['alias'];
	$id = easyrule_block_alias_getid();
	if ($id === false)
		return true;
	if (in_array($host.'/'.$int, explode(" ", $a_aliases[$id]['address'])))
		return false;
	return true;
}

function easyrule_block_alias_add($host, $int = '24') {
	global $blockaliasname, $config;
	/* If the host isn't a valid IP address, bail */
	if (!is_ipaddr($host))
		return false;

	/* If there are no aliases, start an array */
	if (!is_array($config['aliases']['alias']))
		$config['aliases']['alias'] = array();

	$a_aliases = &$config['aliases']['alias'];

	/* Try to get the ID if the alias already exists */
	$id = easyrule_block_alias_getid();
	if ($id === false)
		unset($id);

	$mngid = managed_alias_getid();
	if (!($mngid === false) && $a_aliases[$mngid]) {
		if (!(in_array($host.'/'.$int, explode(" ", $a_aliases[$mngid]['address']))))
			return false;
	} else {
		return false;
	}


	$alias = array();

	
	if (isset($id) && $a_aliases[$id]) {
		/* Make sure this IP isn't already in the list. */
		if (in_array($host.'/'.$int, explode(" ", $a_aliases[$id]['address'])))
			return true;
		/* Since the alias already exists, just add to it. */
		$alias['name']    = $a_aliases[$id]['name'];
		$alias['type']    = $a_aliases[$id]['type'];
		$alias['descr']   = $a_aliases[$id]['descr'];

		$alias['address'] = $a_aliases[$id]['address'] . ' ' . $host . '/' . $int;
	 	$alias['detail']  = $a_aliases[$id]['detail'] . 'Entry added ' . date('r') . '||';
	} else {
                /* Create a new alias with all the proper information */
                $alias['name']    = $blockaliasname;
                $alias['type']    = 'network';
                $alias['descr']   = "Hosts blocked from pf-inetcontrol";

                $alias['address'] = $host . '/' . $int;
                $alias['detail']  = 'Entry added ' . date('r') . '||';
        }
	/* Replace the old alias if needed, otherwise tack it on the end */
	if (isset($id) && $a_aliases[$id])
                $a_aliases[$id] = $alias;
        else
                $a_aliases[] = $alias;

	// Sort list
	$a_aliases = msort($a_aliases, "name");

	return true;
}

function easyrule_block_host($action, $host, $int = '24') {
	global $retval;
	/* Bail if the supplied host is not a valid IP address */
	if (!is_ipaddr($host))
		return false;

	/* Flag whether or not we need to reload the filter */
	$dirty = false;

	/* Attempt to add this host to the alias */
	if ($action == 'block') { 
		if (easyrule_block_alias_add($host, $int))
			$dirty = true;
	} else {
		if (easyrule_block_alias_del($host, $int))
			$dirty = true;
	}

	if (!$dirty)
		return false;

	/* Attempt to add the firewall rule if it doesn't exist.
	 * Failing to add the rule isn't necessarily an error, it may
	 * have been modified by the user in some way. Adding to the
	 * Alias is what's important.
	 */

	/* If needed, write the config and reload the filter */
	if ($dirty) {
		write_config();
		$retval = filter_configure();
//		if (!empty($_SERVER['DOCUMENT_ROOT'])) {
//			header("Location: firewall_aliases.php");
//			exit;
//		} else {
			return true;
//		}
	} else {
		return false;
	}
}

function easyrule_parse_block($int, $src) {
	$action = 'block';
	if (!empty($src) && !empty($int)) {
		if (!is_ipaddr($src)) {
			return "Tried to ".$action." invalid IP: " . htmlspecialchars($src);
		}
		if (easyrule_block_host($action, $src, $int)) {
			return "Network added successfully to ".$action." alias";
		} else {
			return "Failed to add Network to ".$action." alias";
		}
	} else {
		return "Tried to ".$action." but had no host IP or interface";
	}
	return "Unknown error.";
}

function easyrule_parse_pass($int, $src) {
	$action = 'allow';
	if (!empty($src) && !empty($int)) {
		if (!is_ipaddr($src)) {
			return "Tried to ".$action." invalid IP: " . htmlspecialchars($src);
		}
		if (easyrule_block_host($action, $src, $int)) {
			return "Network deleted successfully from alias";
		} else {
			return "Failed to delete Network from alias";
		}
	} else {
		return "Tried to ".$action." but had no host IP or interface";
	}
	return "Unknown error.";
}

function easyrule_parse_status($int, $src) {
	$action = 'check status';
	if (!empty($src) && !empty($int)) {
		if (!is_ipaddr($src)) {
			return "Tried to ".$action." invalid IP: " . htmlspecialchars($src);
		}
		if (easyrule_block_alias_test($src, $int)) {
			return "Current status is ENABLED";
		} else {
			return "Current status is LOCKED";
		}
	} else {
		return "Tried to ".$action." but had no host IP or interface";
	}
	return "Unknown error.";
}

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

?>
