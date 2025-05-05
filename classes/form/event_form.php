<?php

namespace local_eventmanager\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class event_form extends \moodleform {
    public function definition() {
        $mform = $this->_form;

        // Title
        $mform->addElement('text', 'title', get_string('eventtitle', 'local_eventmanager'));
        $mform->setType('title', PARAM_TEXT);
        $mform->addRule('title', null, 'required', null, 'client');

        // Description (Editor)
        $mform->addElement('editor', 'description', get_string('eventdesc', 'local_eventmanager'));
        $mform->setType('description', PARAM_RAW);

        // Category
        $mform->addElement('text', 'category', get_string('eventcategory', 'local_eventmanager'));
        $mform->setType('category', PARAM_TEXT);
        $mform->addRule('category', null, 'required', null, 'client');

        // Event Date
        $mform->addElement('date_selector', 'eventdate', get_string('eventdate', 'local_eventmanager'));
        $mform->addRule('eventdate', null, 'required', null, 'client');

        // Hidden ID for edit mode
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        // Action buttons (Save/Cancel)
        $this->add_action_buttons(true, get_string('savechanges'));
    }
}
