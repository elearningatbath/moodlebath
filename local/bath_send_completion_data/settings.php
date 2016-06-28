<?php
defined('MOODLE_INTERNAL') || die;
if ($hassiteconfig) { // needs this condition or there is error on login page
    $ADMIN->add('root', new admin_category('bath_send_completion_data',
        get_string('pluginname', 'local_bath_send_completion_data')
        ));

    $settings = new admin_settingpage('local_bath_send_completion_data', get_string('pluginname', 'local_bath_send_completion_data'));
    $ADMIN->add('localplugins', $settings);

    //local_bath_send_completion_data | courselist
    $settings->add(new admin_setting_configtext('local_bath_send_completion_data/courselist',
        get_string('courselist', 'local_bath_send_completion_data'), '', '', PARAM_RAW));

    //local_bath_send_completion_data | sas1bemail
    $settings->add(new admin_setting_configtext('local_bath_send_completion_data/sas1bemail',
        get_string('sas1bemail', 'local_bath_send_completion_data'), '', isset($CFG->supportemail) ? $CFG->supportemail : null, PARAM_RAW));
}
