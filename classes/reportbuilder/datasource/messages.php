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

declare(strict_types=1);

namespace local_contactme\reportbuilder\datasource;

use core_reportbuilder\datasource;
use local_contactme\reportbuilder\local\entities\message;

/**
 * Messages datasource
 *
 * @package     local_contactme
 * @copyright   2026 Russell England <http://www.russellengland.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class messages extends datasource {
    /**
     * Return user friendly name of the report source
     *
     * @return string
     */
    public static function get_name(): string {
        return get_string('reportname', 'local_contactme');
    }

    /**
     * Initialise report
     */
    protected function initialise(): void {
        $messageentity = new message();
        $tablealias = $messageentity->get_table_alias('local_contactme');

        $this->set_main_table('local_contactme', $tablealias);
        $this->add_entity($messageentity);

        // Add report elements from each of the entities we added to the report.
        $this->add_all_from_entities();
    }

    /**
     * Return the columns that will be added to the report upon creation
     *
     * @return string[]
     */
    public function get_default_columns(): array {
        return [
            'message:firstname',
            'message:lastname',
            'message:email',
            'message:phone',
            'message:subject',
            'message:message',
            'message:timecreated',
        ];
    }

    /**
     * Return the column sorting that will be added to the report upon creation
     *
     * @return int[]
     */
    public function get_default_column_sorting(): array {
        return [
            'message:timecreated' => SORT_ASC,
        ];
    }

    /**
     * Return the filters that will be added to the report upon creation
     *
     * @return string[]
     */
    public function get_default_filters(): array {
        return [
            'message:firstname',
            'message:lastname',
            'message:email',
            'message:phone',
            'message:subject',
            'message:message',
            'message:timecreated',
        ];
    }

    /**
     * Return the conditions that will be added to the report upon creation
     *
     * @return string[]
     */
    public function get_default_conditions(): array {
        return [
        ];
    }

    /**
     * Validates access to view this report
     *
     * @return bool
     */
    protected function can_view(): bool {
        return has_capability('local_contactme/viewmessages', \context_system::instance());
    }
}
