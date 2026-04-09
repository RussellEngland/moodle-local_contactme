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

namespace local_contactme\reportbuilder\local\systemreports;

use core\context\{system};
use local_contactme\reportbuilder\local\entities\message;
use core_reportbuilder\local\entities\user;
use core_reportbuilder\local\helpers\database;
use core_reportbuilder\local\report\{action, column};
use core_reportbuilder\system_report;
use html_writer;
use lang_string;
use moodle_url;
use pix_icon;
use stdClass;

/**
 * Local Contact Me system report class implementation
 *
 * @package    local_contactme
 * @copyright   2026 Russell England <http://www.russellengland.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class messages extends system_report {
    /** @var int $messageid The ID of the current message row */
    private int $messageid;

    /** @var string $subject of the current message row */
    private string $subject;

    /** @var string $responded has anyone responded to the current message row */
    private bool $responded;

    /**
     * Initialise report, we need to set the main table, load our entities and set columns/filters
     */
    protected function initialise(): void {
        // Our main entity, it contains all of the column definitions that we need.
        $messageentity = new message();
        $entityalias = $messageentity->get_table_alias('local_contactme');

        $this->set_main_table('local_contactme', $entityalias);
        $this->add_entity($messageentity);

        $userentity = new \core_reportbuilder\local\entities\user();
        $entityuseralias = $userentity->get_table_alias('user');
        $this->add_entity($userentity
            ->add_joins($userentity->get_joins())
            ->add_join("LEFT JOIN {user} {$entityuseralias}
                ON {$entityuseralias}.id = {$entityalias}.respondedby"));

        // Any columns required by actions should be defined here to ensure they're always available.
        $this->add_base_fields("{$entityalias}.id, {$entityalias}.subject, {$entityalias}.responded");

        // Now we can call our helper methods to add the content we want to include in the report.
        $this->add_columns();
        $this->add_filters();
        $this->add_actions();
    }

    /**
     * Validates access to view this report
     *
     * @return bool
     */
    protected function can_view(): bool {
        return has_capability('local/contactme:viewmessages', \context_system::instance());
    }

    /**
     * Adds the columns we want to display in the report
     *
     * They are provided by the entities we previously added in the {@see initialise} method, referencing each by their
     * unique identifier. If custom columns are needed just for this report, they can be defined here.
     *
     */
    public function add_columns(): void {
        $columns = [
            'message:firstname',
            'message:lastname',
            'message:email',
            'message:phone',
            'message:subject',
            'message:message',
            'message:timecreated',
            'message:responded',
            'message:respondednote',
            'user:fullnamewithlink',
            'message:timeresponded',
        ];
        $this->add_columns_from_entities($columns);

        // Rename the user fullname column.
        $this->get_column('user:fullnamewithlink')
            ->set_title(new lang_string('respondedby', 'local_contactme'));

        $this->set_initial_sort_column('message:timecreated', SORT_DESC);
    }

    /**
     * Adds the filters we want to display in the report
     *
     * They are all provided by the entities we previously added in the {@see initialise} method, referencing each by their
     * unique identifier
     */
    protected function add_filters(): void {
        $filters = [
            'message:firstname',
            'message:lastname',
            'message:email',
            'message:phone',
            'message:subject',
            'message:message',
            'message:timecreated',
            'message:responded',
            'message:respondednote',
            'user:fullname',
            'message:timeresponded',
        ];

        $this->add_filters_from_entities($filters);

        // Rename the user fullname column.
        $this->get_filter('user:fullname')
            ->set_header(new lang_string('respondedby', 'local_contactme'));
    }

    /**
     * Add the system report actions. An extra column will be appended to each row, containing all actions added here
     *
     * Note the use of ":id" placeholder which will be substituted according to actual values in the row
     */
    protected function add_actions(): void {
        $contextsystem = \context_system::instance();

        // Action to respond.
        $this->add_action((new action(
            new moodle_url('/local/contactme/responded.php', ['id' => ':id']),
            new pix_icon('t/edit', ''),
            [],
            false,
            new lang_string('responded', 'local_contactme')
        )));

        // Action to delete message.
        $this->add_action((new action(
            new moodle_url('/local/contactme/view.php', ['delete' => ':id', 'sesskey' => sesskey()]),
            new pix_icon('t/delete', ''),
            [
                'class' => 'text-danger',
                'data-modal' => 'confirmation',
                'data-modal-title-str' => json_encode(['deletemessage', 'local_contactme']),
                'data-modal-content-str' => ':deletestr',
                'data-modal-yes-button-str' => json_encode(['delete', 'core']),
                'data-modal-destination' => ':deleteurl',

            ],
            false,
            new lang_string('delete', 'moodle'),
        ))->add_callback(static function (\stdclass $row) use ($contextsystem): bool {

            // Populate deletion modal attributes.
            $row->deletestr = json_encode([
                'deletecheck',
                'local_contactme',
                $row->subject,
            ]);

            $row->deleteurl = (new moodle_url('/local/contactme/view.php', [
                'delete' => $row->id,
                'confirm' => true,
                'sesskey' => sesskey(),
            ]))->out(false);

            return has_capability('local/contactme:deletemessages', $contextsystem);
        }));
    }

    /**
     * Store the ID of the message within each row
     *
     * @param stdClass $row
     */
    public function row_callback(stdClass $row): void {
        $this->messageid = (int) $row->id;
        $this->subject = (string) $row->subject;
        $this->responded = (bool) $row->responded;
    }

    /**
     * CSS classes to add to the row
     *
     * @param stdClass $row
     * @return string
     */
    public function get_row_class(stdClass $row): string {
        return $row->responded ? 'text-muted' : '';
    }
}
