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

namespace local_contactme;

use local_contactme\messages;

/**
 * Tests related to local_contactme messages
 *
 * @package     local_contactme
 * @category    test
 * @copyright   2026 Russell England <http://www.russellengland.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class messages_test extends \advanced_testcase {
    /**
     * PHP Unit - test sending messages with various settings.
     *
     * @return void
     * @covers ::local_contactme_send_message
     */
    public function test_messages(): void {
        // Start fresh.
        $this->resetAfterTest(true);

        // Test the admin messages.
        $this->send_messages(true, false);
        $this->send_messages(false, false);

        // Test users with the capability.
        $this->send_messages(false, false);
        $this->send_messages(false, true);

        // Test both.
        $this->send_messages(true, true);
    }

    /**
     * Test sending a message to admins and/or users with the notification capability
     *
     * @global \moodle_database $DB
     * @param bool $includeadmins - off for no admin message, on for admin message
     * @param bool $includecap - off for no capability message, on for capability message
     * @return void
     */
    private function send_messages(bool $includeadmins, bool $includecap): void {
        global $DB;

        // Set include/exclude admins.
        set_config('includeadmins', $includeadmins, 'local_contactme');

        // Get the current admins - should be 1.
        $admins = get_admins();

        $admincount = 0;
        if ($includeadmins) {
            // Count the admins.
            $admincount = count($admins);
        }

        // Set include user capability.
        $cap = 'local/contactme:notification';
        $systemcontext = \context_system::instance();
        if ($includecap) {
            // Create a role to include the notification capability.
            $role = $this->getDataGenerator()->create_role();
            assign_capability($cap, CAP_ALLOW, $role, $systemcontext);

            // Assign a user to the role.
            $user = $this->getDataGenerator()->create_user();
            role_assign($role, $user->id, $systemcontext);

            // Check only 1 user has the cap.
            $capcount = 1;
        } else {
            // Check nobody has the cap.
            $capcount = 0;
        }

        $userstonotify = get_users_by_capability($systemcontext, $cap, 'u.*', null, null, null, null, array_keys($admins));
        $this->assertCount($capcount, $userstonotify);

        // Core Messaging is not compatible with transactions.
        $this->preventResetByRollback();

        // Save messages to a sink.
        $sink = $this->redirectMessages();

        // Create a contact me message.
        $contact = $this->create_contact();

        // Send it.
        $messages = new \local_contactme\messages();
        $messages->send_message($contact);

        // Get the messages for this component.
        $mymessages = $sink->get_messages_by_component('local_contactme');

        // Close the sink.
        $sink->close();

        $this->assertCount($admincount + $capcount, $mymessages);

        // Either way, there should be a record in local_contactme.
        $recordcount = $DB->count_records('local_contactme');
        $this->assertEquals(1, $recordcount);

        // Delete this test contact.
        $DB->delete_records('local_contactme', ['email' => $contact->email]);

        if ($includecap) {
            // Tidy up.

            delete_user($user);
            delete_role($role);
        }
    }

    /**
     * Creates a test contact
     *
     * @return \stdClass
     */
    private function create_contact() {
        $contact = new \stdClass();
        $contact->timecreated = time();
        $contact->firstname = 'first' . $contact->timecreated;
        $contact->lastname = 'last' . $contact->timecreated;
        $contact->email = 'email' . $contact->timecreated . '@testing.com';
        $contact->phone = '';
        $contact->subject = 'subject' . $contact->timecreated;
        $contact->message = 'message' . $contact->timecreated;

        return $contact;
    }
}
