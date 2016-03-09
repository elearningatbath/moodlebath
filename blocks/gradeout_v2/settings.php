<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

//block_gradeout_v2_courses
    $settings->add(new admin_setting_configtext('block_gradeout_v2_courses',
        get_string('courses', 'block_gradeout_v2'),
        get_string('conf_courses','block_gradeout_v2'),
        '',PARAM_SEQUENCE));

//block_gradeout_v2_warn_user
    $settings->add(new admin_setting_configtext('block_gradeout_v2_warn_user',
        get_string('warnees', 'block_gradeout_v2'),
        get_string('warn_user','block_gradeout_v2'),
        isset($CFG->supportemail) ? $CFG->supportemail : null, PARAM_TEXT));

//block_gradeout_v2_passmark
    $passmarks = array();
    for ($i=70;$i<96;$i++){
        $passmarks[$i] = $i;
    }
    $settings->add(new admin_setting_configselect('block_gradeout_v2_passmark',
        get_string('passmark','block_gradeout_v2'),
        get_string('conf_passmark','block_gradeout_v2'),
        isset($CFG->block_gradeout_v2_passmark) ? $CFG->block_gradeout_v2_passmark : null, $passmarks));

//block_gradeout_v2_cron
    /*	$settings->add(new admin_setting_configtext('block_gradeout_v2_cron',
            get_string('cron', 'block_gradeout_v2'),
            get_string('conf_cron','block_gradeout_v2'),
            0,
            PARAM_INT));*/

}