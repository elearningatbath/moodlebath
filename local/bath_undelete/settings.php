<?php
defined('MOODLE_INTERNAL') || die;
if ($hassiteconfig) { // needs this condition or there is error on login page
    $ADMIN->add('root', new admin_externalpage('bath_undelete',
            get_string('bath_undelete', 'local_bath_undelete'),
            new moodle_url('/local/bath_undelete/index.php')));

}