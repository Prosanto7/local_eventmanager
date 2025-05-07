<?php

require_once($CFG->libdir . "/externallib.php");

class local_eventmanager_external extends external_api {

    /**
     * Returns description of the parameters for the web service.
     *
     * @return external_function_parameters
     */
    public static function get_event_details_parameters() {
        return new external_function_parameters([
            'eventid' => new external_value(PARAM_INT, 'The ID of the event')
        ]);
    }

    /**
     * The function to retrieve event details.
     *
     * @param int $eventid
     * @return array
     * @throws \moodle_exception
     */
    public static function get_event_details($eventid) {
        global $DB;

        // Validate parameters.
        $params = self::validate_parameters(self::get_event_details_parameters(), ['eventid' => $eventid]);

        // Fetch event details from the database.
        $event = $DB->get_record('local_eventmanager', ['id' => $params['eventid']], '*', MUST_EXIST);

        // Return event details.
        return [
            'id' => $event->id,
            'name' => $event->title,
            'description' => $event->description,
            'timestart' => userdate($event->eventdate)
        ];
    }

    /**
     * Returns description of the result value for the web service.
     *
     * @return external_single_structure
     */
    public static function get_event_details_returns() {
        return new external_single_structure([
            'id' => new external_value(PARAM_INT, 'The ID of the event'),
            'name' => new external_value(PARAM_TEXT, 'The name of the event'),
            'description' => new external_value(PARAM_RAW, 'The description of the event'),
            'timestart' => new external_value(PARAM_TEXT, 'The start time of the event'),
        ]);
    }
}