<?php
//Capabilities for the block
$capabilities = array(

  'block/bath_oue:view' => array(
      'riskbitmask' => RISK_PERSONAL,
      'captype' => 'read',
      'contextlevel' => CONTEXT_COURSE,
      'legacy' => array(
        'guest'          => CAP_PREVENT,
        'student'        => CAP_ALLOW,
        'teacher'        => CAP_PREVENT,
        'editingteacher' => CAP_PREVENT,
        'coursecreator'  => CAP_PREVENT,
        'manager'          => CAP_ALLOW
        )
    ),
      'block/bath_oue:myaddinstance' => array(
      'riskbitmask' => RISK_PERSONAL,
      'captype' => 'read',
      'contextlevel' => CONTEXT_COURSE,
      'legacy' => array(
        'guest'          => CAP_PREVENT,
        'student'        => CAP_ALLOW,
        'teacher'        => CAP_ALLOW,
        'editingteacher' => CAP_ALLOW,
        'coursecreator'  => CAP_ALLOW,
        'manager'          => CAP_ALLOW
        )
    ),
      'block/bath_oue:addinstance' => array(
      'riskbitmask' => RISK_PERSONAL,
      'captype' => 'read',
      'contextlevel' => CONTEXT_COURSE,
      'legacy' => array(
        'guest'          => CAP_PREVENT,
        'student'        => CAP_ALLOW,
        'teacher'        => CAP_ALLOW,
        'editingteacher' => CAP_ALLOW,
        'coursecreator'  => CAP_ALLOW,
        'manager'          => CAP_ALLOW
        )
    ),
); 
