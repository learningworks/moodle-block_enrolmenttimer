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
 * Lib File
 *
 * @package    block_enrolmenttimer
 * @copyright  LearningWorks Ltd 2016
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Checks the timeleft on enrolment in a given course
 *
 * @param String $unitstoshow
 * @return array from the XML file
 */
function block_enrolmenttimer_get_remaining_enrolment_period($unitstoshow) {
    global $COURSE, $USER, $DB;

    $context = context_course::instance($COURSE->id);

    if (has_capability('moodle/site:config', $context)) {
        $record = 0;
    } else {
        $records = block_enrolmenttimer_get_enrolment_records($USER->id, $COURSE->id);
        if (isset($records[$USER->id])) {
            $record = $records[$USER->id];
        } else {
            $record = 0;
        }
    }

    if (!is_object($record)) {
        return false;
    } else if ($record->timeend == 0 ) {
        $timeend = $DB->get_record('enrol', array('enrol' => 'self', 'id' => $record->enrolid), 'enrolenddate');
        if (isset($timeend->enrolenddate) && (int) $timeend->enrolenddate > 0) {
            $timedifference = (int) $timeend->enrolenddate - time();
            $result = array();

            if (empty($unitstoshow)) {
                // They have not selected any, so show all.
                $unitstoshow = block_enrolmenttimer_get_units();
            } else {
                // Have the selected units, but we only have id's for their values.
                $unitstoshow = block_enrolmenttimer_sort_units_to_show($unitstoshow);
            }

            foreach ($unitstoshow as $text => $unit) {
                if ($timedifference > $unit) {
                    $count = floor($timedifference / $unit);
                    $result[$text] = $count;
                    $timedifference = $timedifference - ($count * $unit);
                }
            }

            return $result;
        } else {
            return false;
        }
    } else {
        $timedifference = (int)$record->timeend - time();
        $result = array();

        if (empty($unitstoshow)) {
            // They have not selected any, so show all.
            $unitstoshow = block_enrolmenttimer_get_units();
        } else {
            // Have the selected units, but we only have id's for their values.
            $unitstoshow = block_enrolmenttimer_sort_units_to_show($unitstoshow);
        }

        foreach ($unitstoshow as $text => $unit) {
            if ($timedifference > $unit) {
                $count = floor($timedifference / $unit);
                $result[$text] = $count;
                $timedifference = $timedifference - ($count * $unit);
            }
        }

        return $result;
    }
}

/**
 * Return enrolment records.
 * @param int $userid
 * @param int $courseid
 * @return array
 */
function block_enrolmenttimer_get_enrolment_records($userid, $courseid) {
    global $DB;

    $sql = '
        SELECT ue.userid, ue.id, ue.timestart, ue.timeend, ue.enrolid
        FROM {user_enrolments} ue
        JOIN {enrol} e on ue.enrolid = e.id
        WHERE ue.userid = ? AND e.courseid = ?
    ';
    return $DB->get_records_sql($sql, array($userid, $courseid));
}

/**
 * Return the values for different periods.
 * @return array
 */
function block_enrolmenttimer_get_units() {
    return array (
        get_string('key_years', 'block_enrolmenttimer')     => 31536000,
        get_string('key_months', 'block_enrolmenttimer')    => 2592000,
        get_string('key_weeks', 'block_enrolmenttimer')     => 604800,
        get_string('key_days', 'block_enrolmenttimer')      => 86400,
        get_string('key_hours', 'block_enrolmenttimer')     => 3600,
        get_string('key_minutes', 'block_enrolmenttimer')   => 60,
        get_string('key_seconds', 'block_enrolmenttimer')   => 1
    );
}

/**
 * Sorting the units we will show the user.
 * @param array $idstring
 * @return array
 */
function block_enrolmenttimer_sort_units_to_show($idstring) {
    $idarray = explode(',', $idstring);
    // Array of array positions eg 1,2,3 tha where selected on the settings menu.

    $units = block_enrolmenttimer_get_units();
    $unitkeys = array_keys($units);
    $output = array();

    foreach ($idarray as $key => $value) {
        $unitkey = $unitkeys[$value];
        $output[$unitkey] = $units[$unitkey];
    }

    return $output;
}