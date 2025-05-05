<?php
require('../../config.php');
use local_eventmanager\form\event_form;

require_login();

$context = context_system::instance();
require_capability('local/eventmanager:manageevents', $context);

$PAGE->set_context($context);

$id = optional_param('id', 0, PARAM_INT);
$event = $id ? $DB->get_record('local_eventmanager', ['id' => $id], '*', MUST_EXIST) : null;

$mform = new event_form();

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/local/eventmanager/index.php'), get_string('cancel_form', 'local_eventmanager'));
} else if ($data = $mform->get_data()) {
    $record = (object)[
        'title' => $data->title,
        'description' => $data->description['text'],
        'format' => $data->description['format'],
        'category' => $data->category,
        'eventdate' => $data->eventdate,
        'timecreated' => time()
    ];
    if ($data->id) {
        $record->id = $data->id;
        $DB->update_record('local_eventmanager', $record);
    } else {
        $DB->insert_record('local_eventmanager', $record);
    }
    redirect(new moodle_url('/local/eventmanager/index.php'), get_string('updatethanks', 'local_eventmanager'));
}

if ($event) {
    $event->description = [
        'text' => $event->description,
        'format' => $event->format
    ];
    $mform->set_data($event);
}

$PAGE->set_url(new moodle_url('/local/eventmanager/edit.php', ['id' => $id]));
$PAGE->set_title('Edit Event');
$PAGE->set_heading('Edit Event');

echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();