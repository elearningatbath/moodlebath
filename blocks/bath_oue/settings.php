<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
	$settings->add(new admin_setting_configtextarea('bath_oue/global_error_msg',get_string('global_error_msg','block_bath_oue'),get_string('global_error_msg_desc','block_bath_oue'),''));
	
}
