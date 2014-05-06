<?php
    $capabilities = array(
 
    // NOT REQUIRED - This block will not be allowed to be placed on /my/ pages
    // 'block/enrolmenttimer:myaddinstance' => array(
    //     'captype' => 'write',
    //     'contextlevel' => CONTEXT_SYSTEM,
    //     'archetypes' => array(
    //         'user' => CAP_ALLOW
    //     ),
 
    //     'clonepermissionsfrom' => 'moodle/my:manageblocks'
    // ),
 
    'block/enrolmenttimer:addinstance' => array(
        'riskbitmask' => RISK_SPAM | RISK_XSS,
 
        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW
        ),
 
        'clonepermissionsfrom' => 'moodle/site:manageblocks'
    ),
);
