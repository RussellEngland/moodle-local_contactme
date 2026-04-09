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

namespace local_contactme\form;

use moodleform;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Responded form
 *
 * @package     local_contactme
 * @copyright   2026 Russell England <http://www.russellengland.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class responded_form extends \moodleform {
    /**
     * Define the form.
     */
    public function definition(): void {
        global $CFG;

        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('text', 'firstname', get_string('firstname'));
        $mform->setType('firstname', PARAM_TEXT);
        $mform->freeze('firstname');

        $mform->addElement('text', 'lastname', get_string('lastname'));
        $mform->setType('lastname', PARAM_TEXT);
        $mform->freeze('lastname');

        $mform->addElement('text', 'email', get_string('email'));
        $mform->setType('email', PARAM_EMAIL);
        $mform->freeze('email');

        $mform->addElement('text', 'phone', get_string('phone'));
        $mform->setType('phone', PARAM_TEXT);
        $mform->freeze('phone');

        $mform->addElement('text', 'subject', get_string('subject'));
        $mform->setType('subject', PARAM_TEXT);
        $mform->freeze('subject');

        $mform->addElement('textarea', 'message', get_string('enquiry', 'local_contactme'));
        $mform->setType('message', PARAM_TEXT);
        $mform->freeze('message');

        $mform->addElement(
            'advcheckbox',
            'responded',
            get_string('responded', 'local_contactme'),
            get_string('responded', 'local_contactme')
        );
        $mform->setType('responded', PARAM_BOOL);
        $mform->setDefault('responded', 0);

        $mform->addElement('textarea', 'respondednote', get_string('respondednote', 'local_contactme'));
        $mform->setType('respondednote', PARAM_TEXT);

        $mform->addElement('submit', 'submitmessage', get_string('submit'));
    }
}
