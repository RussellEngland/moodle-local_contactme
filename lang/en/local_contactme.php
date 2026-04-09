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
 * Plugin strings are defined here.
 *
 * @package     local_contactme
 * @category    string
 * @copyright   2026 Russell England <http://www.russellengland.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['contactme:deletemessages'] = 'Delete messages';
$string['contactme:notification'] = 'Receive notifications from Contact Me';
$string['contactme:respond'] = 'Add respond note';
$string['contactme:viewmessages'] = 'View messages';
$string['contactsubject'] = 'CONTACT : {$a->subject}';
$string['deletecheck'] = 'Are you sure you want to delete this message? "{$a}"';
$string['deletedmessage'] = 'Deleted message : "{$a}"';
$string['deletemessage'] = 'Delete this message?';
$string['enquiry'] = 'Enquiry';
$string['entityname'] = 'Contact Me Entity';
$string['externalonly'] = 'Only external users can use the contact form';
$string['fullmessageplain'] = 'Contact from

First Name : {$a->firstname}

Last Name : {$a->lastname}

Email : {$a->email}

Phone : {$a->phone}

Subject : {$a->subject}

Message : {$a->message}

View more at : {$a->viewlink}';
$string['menutitle'] = 'View Contact Me Messages';
$string['messageprovider:notification'] = 'Notification of a message sent via the contact me form';
$string['pluginname'] = 'Contact Me';
$string['privacy:metadata'] = "NOTE: The local_contactme table stores personal
    data from external users but these are not linked to internal users.
    So the Privacy API can't be used to delete data.
    Please search and delete data manually via the view contact me messages report.";
$string['regarding'] = 'RE : {$a}';
$string['reportname'] = 'Contact Me Messages Report';
$string['responded'] = 'Responded?';
$string['respondedby'] = 'Responded by';
$string['respondedheading'] = 'Have you responded to this message?';
$string['respondednote'] = 'Responded Note';
$string['responseupdated'] = 'Response updated for "{$a}"';
$string['settings:includeadmins'] = 'Send to all admins?';
$string['settings:includeadmins_desc'] = 'Send contact messages to all admins?';
$string['smallmessage'] = 'Contact from $a->firstname $a->lastname Re: $a->subject';
$string['thankyou'] = 'Thank you for contacting us, we will reply soon';
$string['timeresponded'] = 'Time Responded';
$string['viewmessages'] = 'View contact messages';
$string['welcome'] = 'Please contact us';
