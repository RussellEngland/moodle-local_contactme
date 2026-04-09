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

namespace local_contactme\hook;

use stdClass;

/**
 * Hook after a contact message has been saved and sent.
 *
 * @package     local_contactme
 * @copyright   2026 Russell England <http://www.russellengland.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[\core\attribute\label('Allows plugins or features to perform actions after a contact message has been saved and sent.')]
#[\core\attribute\tags('local_contactme')]
class after_message_sent {
    /**
     * Constructor for the hook.
     *
     * @param stdClass $contact The contact message instance.
     */
    public function __construct(
        /** @var stdClass The contact message instance */
        public readonly stdClass $contact,
    ) {
    }
}
