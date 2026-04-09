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
 * Responded page
 *
 * @package     local_contactme
 * @copyright   2026 Russell England <http://www.russellengland.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

$title = get_string('viewmessages', 'local_contactme');

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/contactme/responded.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title($title);
$PAGE->set_heading($title);

require_login();

require_capability('local/contactme:respond', $context);

$id = required_param('id', PARAM_INT);

$responded = $DB->get_record('local_contactme', ['id' => $id], '*', MUST_EXIST);

$respondedform = new \local_contactme\form\responded_form();

$respondedform->set_data($responded);

if ($data = $respondedform->get_data()) {
    // Lets do it.
    $hasresponded = required_param('responded', PARAM_BOOL);
    if (empty($responded->responded) && $hasresponded) {
        // First time responding so record the time and user.
        $responded->responded = true;
        $responded->respondedby = $USER->id;
        $responded->timeresponded = time();
    }
    $responded->respondednote = optional_param('respondednote', '', PARAM_TEXT);

    $DB->update_record('local_contactme', $responded);

    // Inform the user.
    redirect(new moodle_url('/local/contactme/view.php'), get_string('responseupdated', 'local_contactme', $responded->subject));
}

echo $OUTPUT->header();

$templatedata = ['form' => $respondedform->render()];
echo $OUTPUT->render_from_template('local_contactme/respondedform', $templatedata);

echo $OUTPUT->footer();
