<?php
$string['pluginname'] = 'Grade export block V2';
$string['gradeout_v2'] = 'Grade export (QA53)';
$string['conf_courses'] = 'This must be a comma separated series of <b>moodle course id numbers</b> of all those courses which are to be parsed for qa53 grading. e.g. 123456,123457,123458 (<b>NOT</b> idnumber, samis code,anything else)';
$string['courses'] = 'QA53 courses:';
$string['conf_passmark'] = 'This is the mark which must be achieved for the student record to be updated with a \'P\'';
$string['passmark'] = 'Passmark:';
$string['conf_cron'] = 'If this is set to zero (or not set), nothing will happen. It defines the minimum period in seconds between processing new submissions, but this is limited by the frequency with which the moodle cron is called.';
$string['cron'] = 'Cron:';
$string['warn_user'] = 'Email address(es - csv) to warn in case SAS1B not set.';
$string['warnees'] = 'SAS1B warn:';
$string['warn_convenor'] = 'QA53 convenor';
$string['warn_from'] = 'QA53 moodle integration';
$string['warn_subject'] = 'SAS1B NOT DONE!!!!!!!!';
$string['warn_messagetext'] = 'Please ensure that SAS1B is run ASAP for the cohort:';

$string['gradeout_v2:addinstance'] = 'Add a new Grade export (QA53) block';