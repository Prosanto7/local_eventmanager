<?php

$services = [
    'Event Manager Custom Web Services' => [
        'functions' => ['local_eventmanager_get_event_details'],
        'restrictedusers' => 0,
        'enabled' => 1,
        'shortname' => 'eventmanagercustomws',
    ],
];

$functions = [
    'local_eventmanager_get_event_details' => [
        'classname'   => 'local_eventmanager_external',
        'methodname'  => 'get_event_details',
        'classpath'   => 'local/eventmanager/externallib.php',
        'description' => 'Retrieve details of an event.',
        'type'        => 'read'
    ]
];