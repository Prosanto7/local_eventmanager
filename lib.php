<?php
// File: local/eventmanager/lib.php

defined('MOODLE_INTERNAL') || die();

/**
 * Hook to inject content into the page footer across the whole site.
 *
 * @return string HTML to append to the footer.
 */
function local_eventmanager_before_footer() {
    // die('This is a test message from the local_eventmanager plugin.');
    // \core\notification::add('A Test Message', \core\output\notification::NOTIFY_SUCCESS);

    // global $USER, $PAGE;

    // // Only show to logged-in users (optional)
    // if (isloggedin() && !isguestuser()) {
    //     $username = fullname($USER);
    //     return html_writer::div(
    //         get_string('footerwelcome', 'local_eventmanager', $username),
    //         'local-eventmanager-footer',
    //         ['style' => 'text-align:center; padding: 10px; font-size: 0.9rem; color: gray;']
    //     );
    // }
    // return '';
}
