<?php
require('../../config.php');
require_login();
require_capability('local/eventmanager:manageevents', context_system::instance());

$id = required_param('id', PARAM_INT);
$event = $DB->get_record('local_eventmanager', ['id' => $id], '*', MUST_EXIST);

$DB->delete_records('local_eventmanager', ['id' => $id]);
redirect('index.php');