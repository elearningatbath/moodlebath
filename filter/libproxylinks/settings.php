<?php

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    $settings->add(new admin_setting_configtext('filter_libproxylinks/proxylink', get_string('proxylink_label','filter_libproxylinks'),
                       get_string('proxylink_desc','filter_libproxylinks'), ''));
}
