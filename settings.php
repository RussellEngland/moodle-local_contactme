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
 * Plugin administration pages are defined here.
 *
 * @package     local_contactme
 * @category    admin
 * @copyright   2026 Russell England <http://www.russellengland.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    // Add as a menu item to site admin reports.
    $ADMIN->add(
        'reports',
        new admin_externalpage(
            'local_contactme_view',
            get_string('menutitle', 'local_contactme'),
            "$CFG->wwwroot/local/contactme/view.php",
            'local/contactme:viewmessages'
        )
    );

    $settings = new admin_settingpage('local_contactme', new lang_string('pluginname', 'local_contactme'));
    $ADMIN->add('localplugins', $settings);

    if ($ADMIN->fulltree) {
        // Option to send to all admins?
        $settings->add(new admin_setting_configcheckbox(
            'local_contactme/includeadmins',
            new lang_string('settings:includeadmins', 'local_contactme'),
            new lang_string('settings:includeadmins_desc', 'local_contactme'),
            0 // Default no.
        ));
    }
}
