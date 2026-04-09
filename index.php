<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Main contact me page
 *
 * @package     local_contactme
 * @copyright   2026 Russell England <http://www.russellengland.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// No login check because this is for external users only.
// @codingStandardsIgnoreLine
require_once('../../config.php');

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/contactme/index.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('pluginname', 'local_contactme'));
$PAGE->set_heading(get_string('pluginname', 'local_contactme'));

if (isloggedin()) {
    redirect(new moodle_url('/'), get_string('externalonly', 'local_contactme'), null, \core\output\notification::NOTIFY_WARNING);
}

$contactform = new \local_contactme\form\contact_form();

if ($data = $contactform->get_data()) {
    // Lets do it.
    $contact = new stdClass();
    $contact->firstname = required_param('firstname', PARAM_TEXT);
    $contact->lastname = required_param('lastname', PARAM_TEXT);
    $contact->email = required_param('email', PARAM_EMAIL);
    $contact->phone = optional_param('phone', '', PARAM_TEXT);
    $contact->subject = required_param('subject', PARAM_TEXT);
    $contact->message = required_param('message', PARAM_TEXT);
    $contact->timecreated = time();

    $messages = new \local_contactme\messages();
    $messages->send_message($contact);

    // Inform the user.
    redirect(new moodle_url('/'), get_string('thankyou', 'local_contactme'));
}

echo $OUTPUT->header();

$templatedata = ['form' => $contactform->render()];
echo $OUTPUT->render_from_template('local_contactme/contactform', $templatedata);

echo $OUTPUT->footer();
