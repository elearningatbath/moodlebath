<?php

/**
 * @author Martin Dougiamas
 * @author Jerome GUTIERREZ
 * @author I�aki Arenaza
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodle multiauth
 *
 * Authentication Plugin: CAS Authentication
 *
 * Authentication using CAS (Central Authentication Server).
 *
 * 2006-08-28  File created.
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->dirroot.'/auth/cas/auth.php');

/**
 * CAS authentication plugin.
 */
class auth_plugin_bathcas extends auth_plugin_cas {

    /**
     * Constructor.
     */
    function auth_plugin_bathcas() {
        $this->authtype = 'bathcas';
        $this->roleauth = 'auth_bathcas';
        $this->errorlogtag = '[AUTH BATHCAS] ';
        $this->init_plugin($this->authtype);
    }

    /**
     * Authenticates user against CAS
     * Returns true if the username and password work and false if they are
     * wrong or don't exist.
     *
     * @param string $username The username (with system magic quotes)
     * @param string $password The password (with system magic quotes)
     * @return bool Authentication success or failure.
     */
    function user_login ($username, $password) {
        // bath mod to bind user against accessControl: Moodle: in LDAP
            if(!$this->get_userinfo($username)){
                return false;
            }
        //
        $this->connectCAS();
        return phpCAS::isAuthenticated() && (trim(core_text::strtolower(phpCAS::getUser())) == $username);
    }
    
    function get_userinfo($username) {
    	//$textlib = textlib_get_instance();
    	$extusername = core_text::convert($username, 'utf-8', $this->config->ldapencoding);
    
    	$ldapconnection = $this->ldap_connect();
    	if(!($user_dn = $this->ldap_find_userdn($ldapconnection, $extusername))) {
    		return false;
    	}
    
    	$search_attribs = array();
    	$attrmap = $this->ldap_attributes();
    	foreach ($attrmap as $key => $values) {
    		if (!is_array($values)) {
    			$values = array($values);
    		}
    		foreach ($values as $value) {
    			if (!in_array($value, $search_attribs)) {
    				array_push($search_attribs, $value);
    			}
    		}
    	}
    
    	//Grab accesscontrol to test Moodle eligibility - al412
    	array_push($search_attribs, 'accesscontrol');
    
    	if (!$user_info_result = ldap_read($ldapconnection, $user_dn, '(objectClass=*)', $search_attribs)) {
    		return false; // error!
    	}
    
    	$user_entry = ldap_get_entries_moodle($ldapconnection, $user_info_result);
    	if (empty($user_entry)) {
    		return false; // entry not found
    	}
    
    	$result = array();
    	 
    	//Unpick the array of arrays to ascertain if the user has Moodle access rights - al412
    	$moodleaccess = false;
    	//Pick a way through the arrays of arrays returned from LDAP to set $moodleaccess - al412
    	foreach($user_entry as $key => $array_1){
    		if($key == "accesscontrol"){
    			foreach($array_1 as $key_2 => $array_2){
    				foreach($array_2 as $key_3 => $value){
    					if(preg_match('/Moodle/', $value)){
    						$moodleaccess = true;
    					}
    				}
    			}
    		}
    	}
    	//Set a boolean flag to return, picked up in create_user_record - al412
    	$result['access'] = $moodleaccess;
    
    	foreach ($attrmap as $key => $values) {
    		if (!is_array($values)) {
    			$values = array($values);
    		}
    		$ldapval = NULL;
    		foreach ($values as $value) {
    			$entry = array_change_key_case($user_entry[0], CASE_LOWER);
    			if (($value == 'dn') || ($value == 'distinguishedname')) {
    				$result[$key] = $user_dn;
    				continue;
    			}
    			if (!array_key_exists($value, $entry)) {
    				continue; // wrong data mapping!
    			}
    			if (is_array($entry[$value])) {
    				$newval = core_text::convert($entry[$value][0], $this->config->ldapencoding, 'utf-8');
    			} else {
    				$newval = core_text::convert($entry[$value], $this->config->ldapencoding, 'utf-8');
    			}
    			if (!empty($newval)) {
    				// favour ldap entries that are set
    				$ldapval = $newval;
    			}
    		}
    		if (!is_null($ldapval)) {
    			$result[$key] = $ldapval;
    		}
    	}
    
    	$this->ldap_close();
    	return $result;
    }
    /*
     *  bath function for information on non logged in user
    */
    function username() {
    	$this->connectCAS();
    	return phpCAS::getUser();
    }
    
    
    /**
    * Prints a form for configuring this authentication plugin.
     *
    * This function is called from admin/auth.php, and outputs a full page with
    * a form for configuring this plugin.
    *
    * @param array $page An object containing all the data for this page.
    */
    function config_form($config, $err, $user_fields) {
    global $CFG, $OUTPUT;
    
    if (!function_exists('ldap_connect')) {
    // Is php-ldap really there?
                echo $OUTPUT->notification(get_string('auth_ldap_noextension', 'auth_ldap'));
    
    // Don't return here, like we do in auth/ldap. We cas use CAS without LDAP.
    // So just warn the user (done above) and define the LDAP constants we use
    // in config.html, to silence the warnings.
    if (!defined('LDAP_DEREF_NEVER')) {
    define ('LDAP_DEREF_NEVER', 0);
    }
    if (!defined('LDAP_DEREF_ALWAYS')) {
    define ('LDAP_DEREF_ALWAYS', 3);
    }
    }
    
    	include($CFG->dirroot.'/auth/bathcas/config.html');
    }
    
}
