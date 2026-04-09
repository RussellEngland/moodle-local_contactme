<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Page for contact messages management
 *
 * @package    local_contactme
 * @copyright   2026 Russell England <http://www.russellengland.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

use local_contactme\reportbuilder\local\systemreports\messages;
use core_reportbuilder\system_report_factory;

$deleteid = optional_param('delete', 0, PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_BOOL);

require_login();

$context = \context_system::instance();

$returnurl = new moodle_url('/local/contactme/view.php');
$PAGE->set_url($returnurl);
$PAGE->add_body_class('limitedwidth');

$title = get_string('viewmessages', 'local_contactme');
$PAGE->set_context($context);
$PAGE->set_pagelayout('report');
$PAGE->set_title($title);
$PAGE->set_heading($title);

require_capability('local/contactme:viewmessages', $context);

if ($deleteid) {
    require_capability('local/contactme:deletemessages', $context);

    if (!$confirm) {
        $message = $DB->get_record('local_contactme', ['id' => $deleteid]);

        echo $OUTPUT->header();
        echo $OUTPUT->heading(get_string('deletemessage', 'local_contactme'));

        echo $OUTPUT->box(get_string('deletecheck', 'local_contactme', $message->subject), 'generalbox');

        echo $OUTPUT->single_button(new moodle_url($returnurl, ['delete' => $message->id, 'confirm' => 1]), get_string('confirm'));

        echo $OUTPUT->action_link($returnurl, get_string('cancel'));

        echo $OUTPUT->footer();

        // Done.
        die();
    } else {
        require_sesskey();

        $subject = $DB->get_field('local_contactme', 'subject', ['id' => $deleteid], MUST_EXIST);

        $DB->delete_records('local_contactme', ['id' => $deleteid]);

        redirect('/local/contactme/view.php', get_string('deletedmessage', 'local_contactme', $subject));
    }
}

echo $OUTPUT->header();

// The report displays a heading.
$report = system_report_factory::create(local_contactme\reportbuilder\local\systemreports\messages::class, $context);

echo $report->output();

echo $OUTPUT->footer();
