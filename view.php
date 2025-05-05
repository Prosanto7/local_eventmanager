<?php
require('../../config.php');
require_login();

$id = required_param('id', PARAM_INT);
$event = $DB->get_record('local_eventmanager', ['id' => $id], '*', MUST_EXIST);

$PAGE->set_context(context_system::instance());
$PAGE->set_url(new moodle_url('/local/eventmanager/view.php', ['id' => $id]));
$PAGE->set_title($event->title);
$PAGE->set_heading($event->title);

echo $OUTPUT->header();
echo format_text($event->description);
echo html_writer::tag('p', "Category: " . $event->category);
echo html_writer::tag('p', "Date: " . userdate($event->eventdate));
echo $OUTPUT->footer();