<?php
defined('MOODLE_INTERNAL') || die();

$capabilities = [
    'local/eventmanager:manageevents' => [
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => [
            'manager' => CAP_ALLOW,
        ]
    ]
];
