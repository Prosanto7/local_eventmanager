<?php
require('../../config.php');
require_login();

$context = context_system::instance();
$canmanage = has_capability('local/eventmanager:manageevents', $context);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/eventmanager/index.php'));
$PAGE->set_title('Event Manager');
$PAGE->set_heading('Event Manager');

$events = $DB->get_records('local_eventmanager', null, 'eventdate ASC');

// Prepare data for Mustache
$templatecontext = [
    'heading' => get_string('institutionevents', 'local_eventmanager'),
    'canmanage' => $canmanage,
    'newevent' => get_string('newevent', 'local_eventmanager'),
    'newurl' => new moodle_url('/local/eventmanager/edit.php'),
    'events' => !empty($events),
    'list' => []
];

foreach ($events as $event) {
    $item = [
        'title' => format_string($event->title),
        'eventdate' => userdate($event->eventdate),
        'viewurl' => new moodle_url('/local/eventmanager/view.php', ['id' => $event->id])
    ];

    if ($canmanage) {
        $item['canmanage'] = true;
        $item['editurl'] = new moodle_url('/local/eventmanager/edit.php', ['id' => $event->id]);
        $item['deleteurl'] = new moodle_url('/local/eventmanager/delete.php', ['id' => $event->id]);
    }

    $templatecontext['list'][] = $item;
}


echo $OUTPUT->header();
$PAGE->requires->js_call_amd('local_eventmanager/deleteconfirm', 'init');
echo $OUTPUT->render_from_template('local_eventmanager/eventlist', $templatecontext);
echo $OUTPUT->footer();
