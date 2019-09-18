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
 * Upgrade script
 *
 * @package    block_enrolmenttimer
 * @copyright  LearningWorks Ltd 2016
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

/**
 * When upgrading plugin, execute the following code.
 *
 * @param int $oldversion - previous version of plugin (from DB).
 */
function xmldb_block_enrolmenttimer_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2017083000) {

        // Define table block_enrolmenttimer to be created.
        $table = new xmldb_table('block_enrolmenttimer');

        // Adding fields to table block_enrolmenttimer.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('enrolid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('alerttime', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('sent', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table block_enrolmenttimer.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for block_enrolmenttimer.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Enrolmenttimer savepoint reached.
        upgrade_block_savepoint(true, 2017083000, 'enrolmenttimer');
    }

    // Add future upgrade points here.

    return true;
}