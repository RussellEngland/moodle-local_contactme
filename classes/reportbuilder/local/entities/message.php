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

namespace local_contactme\reportbuilder\local\entities;

use lang_string;
use stdClass;
use core_reportbuilder\local\entities\base;
use core_reportbuilder\local\filters\{date, text, boolean_select};
use core_reportbuilder\local\helpers\format;
use core_reportbuilder\local\report\{column, filter};

/**
 * Messages entity
 *
 * @package     local_contactme
 * @copyright   2026 Russell England <http://www.russellengland.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class message extends base {
    /**
     * Database tables that this entity uses
     *
     * @return string[]
     */
    protected function get_default_tables(): array {
        return [
            'local_contactme',
        ];
    }

    /**
     * The default title for this entity
     *
     * @return lang_string
     */
    protected function get_default_entity_title(): lang_string {
        return new lang_string('entityname', 'local_contactme');
    }

    /**
     * Initialise the entity
     *
     * @return base
     */
    public function initialise(): base {
        $columns = $this->get_all_columns();
        foreach ($columns as $column) {
            $this->add_column($column);
        }

        // All the filters defined by the entity can also be used as conditions.
        $filters = $this->get_all_filters();
        foreach ($filters as $filter) {
            $this
                ->add_filter($filter)
                ->add_condition($filter);
        }

        return $this;
    }

    /**
     * Returns list of all available columns
     *
     * @return column[]
     */
    protected function get_all_columns(): array {
        global $DB;

        $tablealias = $this->get_table_alias('local_contactme');

        $columnnames = [
            'firstname' => new lang_string('firstname'),
            'lastname' => new lang_string('lastname'),
            'phone' => new lang_string('phone'),
            'subject' => new lang_string('subject'),
            'message' => new lang_string('enquiry', 'local_contactme'),
            'respondednote' => new lang_string('respondednote', 'local_contactme'),
        ];
        foreach ($columnnames as $columnname => $columnstring) {
            $fieldsql = "{$tablealias}.{$columnname}";
            if ($DB->get_dbfamily() === 'oracle') {
                $fieldsql = $DB->sql_order_by_text($fieldsql, 1024);
            }
            $type = in_array($columnname, ['message', 'respondednote']) ? column::TYPE_LONGTEXT : column::TYPE_TEXT;
            $columns[] = (new column(
                $columnname,
                $columnstring,
                $this->get_entity_name()
            ))
                ->set_type($type)
                ->add_field($fieldsql, $columnname)
                ->set_is_sortable(true)
                ->add_callback(static function ($columntext, stdClass $message): string {
                    if ($columntext === null) {
                        return '';
                    }

                    return format_text($columntext);
                });
        }

        $fieldsql = "{$tablealias}.email";
        if ($DB->get_dbfamily() === 'oracle') {
            $fieldsql = $DB->sql_order_by_text($fieldsql, 1024);
        }
        $columns[] = (new column(
            'email',
            new lang_string('email'),
            $this->get_entity_name()
        ))
            ->set_type(column::TYPE_TEXT)
            ->add_field($fieldsql, 'email')
            ->add_field("{$tablealias}.subject")
            ->set_is_sortable(true)
            ->add_callback(static function ($email, stdClass $message): string {
                if ($email === null) {
                    return '';
                }

                $email = format_string($email);
                $subject = get_string('regarding', 'local_contactme', format_string($message->subject));

                return obfuscate_mailto($email, $email, false, $subject);
            });

        $fieldsql = "{$tablealias}.responded";
        if ($DB->get_dbfamily() === 'oracle') {
            $fieldsql = $DB->sql_order_by_text($fieldsql, 1024);
        }
        $columns[] = (new column(
            'responded',
            new lang_string('responded', 'local_contactme'),
            $this->get_entity_name()
        ))
            ->set_type(column::TYPE_BOOLEAN)
            ->add_field($fieldsql, 'responded')
            ->set_is_sortable(true)
            ->add_callback([format::class, 'boolean_as_text']);

        $timecolumns = [
            'timecreated' => new lang_string('timecreated', 'core_reportbuilder'),
            'timeresponded' => new lang_string('timeresponded', 'local_contactme'),
        ];
        foreach ($timecolumns as $timename => $timestring) {
            $columns[] = (new column(
                $timename,
                $timestring,
                $this->get_entity_name()
            ))
                ->set_type(column::TYPE_TIMESTAMP)
                ->add_fields("{$tablealias}.{$timename}")
                ->set_is_sortable(true)
                ->add_callback([format::class, 'userdate']);
        }

        return $columns;
    }

    /**
     * Return list of all available filters
     *
     * @return filter[]
     */
    protected function get_all_filters(): array {
        global $DB;

        $tablealias = $this->get_table_alias('local_contactme');

        $columnnames = [
            'firstname' => new lang_string('firstname'),
            'lastname' => new lang_string('lastname'),
            'email' => new lang_string('email'),
            'phone' => new lang_string('phone'),
            'subject' => new lang_string('subject'),
            'message' => new lang_string('enquiry', 'local_contactme'),
            'respondednote' => new lang_string('respondednote', 'local_contactme'),

        ];
        foreach ($columnnames as $columnname => $columnstring) {
            // Content.
            $filters[] = (new filter(
                text::class,
                $columnname,
                $columnstring,
                $this->get_entity_name(),
                $DB->sql_cast_to_char("{$tablealias}.{$columnname}")
            ));
        }

        $filters[] = (new filter(
            boolean_select::class,
            'responded',
            new lang_string('responded', 'local_contactme'),
            $this->get_entity_name(),
            "{$tablealias}.responded"
        ));

        $timecolumns = [
            'timecreated' => new lang_string('timecreated', 'core_reportbuilder'),
            'timeresponded' => new lang_string('timeresponded', 'local_contactme'),
        ];
        foreach ($timecolumns as $timename => $timestring) {
            $filters[] = (new filter(
                date::class,
                $timename,
                $timestring,
                $this->get_entity_name(),
                "{$tablealias}.{$timename}"
            ))
                ->set_limited_operators([
                    date::DATE_ANY,
                    date::DATE_RANGE,
                    date::DATE_LAST,
                    date::DATE_CURRENT,
                ]);
        }

        return $filters;
    }

    /**
     * Check permission
     *
     * @return bool
     */
    public function is_available(): bool {
        return has_capability('local/contactme:viewmessages', context_system::instance());
    }
}
