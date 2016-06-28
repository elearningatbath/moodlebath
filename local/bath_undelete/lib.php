<?php 
defined('MOODLE_INTERNAL') || die();
global $CFG;

function get_hidden_user_details($username){
	
	global $DB;
	$user = $DB->get_record('user',array('username' => $username));
	if($user){
			return $user;
	}
}
function restore_user($username){
	
	global $DB;
	if($username !== ''){
		$DB->set_field('user','deleted','0',array('username'=>$username));
	}	
}
