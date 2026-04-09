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

namespace local_contactme;

/**
 * Library of functions for local_contactme
 *
 * @package     local_contactme
 * @copyright   2026 Russell England <http://www.russellengland.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class messages {
    /**
     * Send the contact me message to admins and/or users with the notification capability
     *
     * @global moodle_database $DB
     * @param stdclass $contact object for contact details and message
     */
    public function send_message($contact) {
        global $DB;

        $contact->id = $DB->insert_record('local_contactme', $contact);

        $viewurl = new \moodle_url('/local/contactme/view.php');
        $viewlink = \html_writer::link($viewurl, $viewurl);

        $body = new \stdClass();
        $body->firstname = format_string($contact->firstname);
        $body->lastname = format_string($contact->lastname);
        $body->email = format_string($contact->email);
        $body->phone = format_string($contact->phone);
        $body->subject = format_string($contact->subject);
        $body->message = format_string($contact->message);
        $body->viewlink = $viewlink;

        $userfrom = \core_user::get_noreply_user();
        $userfrom->firstname = $body->firstname;
        $userfrom->lastname = $body->lastname;
        $userfrom->email = $body->email;

        $admins = [];
        if (get_config('local_contactme', 'includeadmins')) {
            // Get all the admins.
            $admins = get_admins();
        }

        // Get users with this capability.
        $cap = 'local/contactme:notification';
        $context = \context_system::instance();
        $userstonotify = get_users_by_capability($context, $cap, 'u.*', null, null, null, null, array_keys($admins));

        // Join them together.
        $userstonotify = array_merge($userstonotify, $admins);

        // Send the message to selected users.
        $message = new \core\message\message();
        $message->component = 'local_contactme';
        $message->name = 'notification';
        $message->userfrom = $userfrom;
        $message->subject = get_string('contactsubject', 'local_contactme', $body);
        $message->fullmessageformat = FORMAT_PLAIN;
        $message->fullmessage = get_string('fullmessageplain', 'local_contactme', $body);
        $message->fullmessagehtml = '';
        $message->smallmessage = get_string('smallmessage', 'local_contactme', $body);
        $message->notification = 1;

        foreach ($userstonotify as $userto) {
            $message->userto = $userto;
            message_send($message);
        }

        // Dispatch hook.
        $hook = new \local_contactme\hook\after_message_sent($contact);
        \core\di::get(\core\hook\manager::class)->dispatch($hook);
    }
}
