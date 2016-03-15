<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings for component 'auth_bathcas', language 'en'.
 *
 * @package   auth_bathcas
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['accesCAS'] = 'CAS users';
$string['accesNOCAS'] = 'other users';
$string['auth_bathcas_auth_user_create'] = 'Create users externally';
$string['auth_bathcas_baseuri'] = 'URI of the server (nothing if no baseUri)<br />For example, if the CAS server responds to host.domaine.fr/CAS/ then<br />cas_baseuri = CAS/';
$string['auth_bathcas_baseuri_key'] = 'Base URI';
$string['auth_bathcas_broken_password'] = 'You cannot proceed without changing your password, however there is no available page for changing it. Please contact your Moodle Administrator.';
$string['auth_bathcas_cantconnect'] = 'LDAP part of CAS-module cannot connect to server: {$a}';
$string['auth_bathcas_casversion'] = 'CAS protocol version';
$string['auth_bathcas_certificate_check'] = 'Select \'yes\' if you want to validate the server certificate';
$string['auth_bathcas_certificate_path_empty'] = 'If you turn on Server validation, you need to specify a certificate path';
$string['auth_bathcas_certificate_check_key'] = 'Server validation';
$string['auth_bathcas_certificate_path'] = 'Path of the CA chain file (PEM Format) to validate the server certificate';
$string['auth_bathcas_certificate_path_key'] = 'Certificate path';
$string['auth_bathcas_create_user'] = 'Turn this on if you want to insert CAS-authenticated users in Moodle database. If not then only users who already exist in the Moodle database can log in.';
$string['auth_bathcas_create_user_key'] = 'Create user';
$string['auth_bathcasdescription'] = 'This method uses a CAS server (Central Authentication Service) to authenticate users in a Single Sign On environment (SSO). You can also use a simple LDAP authentication. If the given username and password are valid according to CAS, Moodle creates a new user entry in its database, taking user attributes from LDAP if required. On following logins only the username and password are checked.';
$string['auth_bathcas_enabled'] = 'Turn this on if you want to use CAS authentication.';
$string['auth_bathcas_hostname'] = 'Hostname of the CAS server <br />eg: host.domain.fr';
$string['auth_bathcas_hostname_key'] = 'Hostname';
$string['auth_bathcas_changepasswordurl'] = 'Password-change URL';
$string['auth_bathcas_invalidcaslogin'] = 'Sorry, your login has failed - you could not be authorised';
$string['auth_bathcas_language'] = 'Select language for authentication pages';
$string['auth_bathcas_language_key'] = 'Language';
$string['auth_bathcas_logincas'] = 'Secure connection access';
$string['auth_bathcas_logout_return_url_key'] = 'Alternative logout return URL';
$string['auth_bathcas_logout_return_url'] = 'Provide the URL that CAS users shall be redirected to after logging out.<br />If left empty, users will be redirected to the location that moodle will redirect users to';
$string['auth_bathcas_logoutcas'] = 'Select \'yes\' if you want to logout from CAS when you disconnect from Moodle';
$string['auth_bathcas_logoutcas_key'] = 'CAS logout option';
$string['auth_bathcas_multiauth'] = 'Select \'yes\' if you want to have multi-authentication (CAS + other authentication)';
$string['auth_bathcas_multiauth_key'] = 'Multi-authentication';
$string['auth_bathcasnotinstalled'] = 'Cannot use CAS authentication. The PHP LDAP module is not installed.';
$string['auth_bathcas_port'] = 'Port of the CAS server';
$string['auth_bathcas_port_key'] = 'Port';
$string['auth_bathcas_proxycas'] = 'Select \'yes\' if you use CAS in proxy-mode';
$string['auth_bathcas_proxycas_key'] = 'Proxy mode';
$string['auth_bathcas_server_settings'] = 'CAS server configuration';
$string['auth_bathcas_text'] = 'Secure connection';
$string['auth_bathcas_use_cas'] = 'Use CAS';
$string['auth_bathcas_version'] = 'CAS protocol version to use';
$string['CASform'] = 'Authentication choice';
$string['noldapserver'] = 'No LDAP server configured for CAS! Syncing disabled.';
$string['pluginname'] = 'BathCAS server (SSO)';

/*Bath specific strings*/
$string['not_registered'] = '
<div class="subcontent loginsub">
<p>Whilst you have successfully logged into the system,you need to complete Registration, including paying any outstanding fees, in order to have full access to youraccount (including access to Moodle).</p>
<p><a href="http://www.bath.ac.uk/registration-on-line/">RegistrationOn-Line</a></p></div>';
$string['registered_no_moodle'] = '
<div class="subcontent loginsub">
<p>Whilst you have successfully logged into the system,this account <strong>({$a})</strong> does not have permission to use Moodle.Please email e-Learning (<a href="mailto:e-learning@bath.ac.uk">e-learning@bath.ac.uk</a>) for support.</p>
</div>';
