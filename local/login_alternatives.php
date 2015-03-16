<?php

// We're going to use phpCAS::getUser() so we need it to be available.
require_once("../auth/bathcas/auth.php");
$authplug = new auth_plugin_bathcas();
$casUsername = $authplug->username();

// Might need to put some sort of assertion in here to check we actually got a username.

// Set up variables we'll use to record the state.
$notRegistered = false;

// Grab pertinent LDAP attributes for the user and use to check their status.
$attributes = array("accountState");
$ldapc = ldap_connect("ldap.bath.ac.uk");

// Might need a check in here that it actually worked...

$sr = ldap_search($ldapc, "ou=people,o=bath.ac.uk", "uid=".$casUsername, $attributes);

if (ldap_count_entries($ldapc, $sr) == 1) {
	$values = ldap_get_values($ldapc, ldap_first_entry($ldapc, $sr), "accountState");
	foreach($values as $value){
		if (strtoupper($value) == "NOTREGISTERED") {
			$notRegistered = true;
		}
	}
}

// Do we need a check in case we *didn't* get exactly one result from the LDAP?...

ldap_close($ldapc);

// Depending on what we found, display one of two messages.

if ($notRegistered) { 

echo'
<h2>You are Not Registered</h2>
<div class="subcontent loginsub">
<p>Whilst you have successfully logged into the system,<br />
you need to complete Registration, including paying any<br />
outstanding fees, in order to have full access to your<br />
account (including access to Moodle).</p>
<p><a href="http://www.bath.ac.uk/registration-on-line/">Registration
On-Line</a></p>
</div>
    ';

} else {

echo'
<h2>You do not have permission to use Moodle</h2>
<div class="subcontent loginsub">
<p>Whilst you have successfully logged into the system,<br />
this account ('.$casUsername.') does not have permission to use Moodle.<br />
Please email e-Learning (e-learning@bath.ac.uk) for support.</p>
</div>
	';

}
?>
