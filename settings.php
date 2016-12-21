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
 * Admin Settings
 *
 * @package    block_enrolmenttimer
 * @copyright  LearningWorks Ltd 2016
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    require_once($CFG->dirroot . '/blocks/enrolmenttimer/locallib.php');

    // General Settings.
    $settings->add(new admin_setting_heading('notificationsheadinggeneral',
        get_string('settings_general', 'block_enrolmenttimer'), ''));

    $settings->add(new admin_setting_configcheckbox(
        'enrolmenttimer/displayNothingNoDateSet',
        get_string('displayNothingNoDateSet', 'block_enrolmenttimer'),
        get_string('displayNothingNoDateSet_help', 'block_enrolmenttimer'),
        1
    ));

    $settings->add(new admin_setting_configcheckbox(
        'enrolmenttimer/displayUnitLabels',
        get_string('displayUnitLabels', 'block_enrolmenttimer'),
        get_string('displayUnitLabels_help', 'block_enrolmenttimer'),
        0
    ));

    $settings->add(new admin_setting_configcheckbox(
        'enrolmenttimer/forceTwoDigits',
        get_string('forceTwoDigits', 'block_enrolmenttimer'),
        get_string('forceTwoDigits_help', 'block_enrolmenttimer'),
        1
    ));

    $settings->add(new admin_setting_configcheckbox(
        'enrolmenttimer/displayTextCounter',
        get_string('displayTextCounter', 'block_enrolmenttimer'),
        get_string('displayTextCounter_help', 'block_enrolmenttimer'),
        1
    ));

    $settings->add(new admin_setting_configcheckbox(
        'enrolmenttimer/activecountdown',
        get_string('activecountdown', 'block_enrolmenttimer'),
        get_string('activecountdown_help', 'block_enrolmenttimer'),
        1
    ));

    $options = array_keys(block_enrolmenttimer_get_units());
    $settings->add(new admin_setting_configmultiselect(
        'enrolmenttimer/viewoptions',
        get_string('viewoptions', 'block_enrolmenttimer'),
        get_string('viewoptions_desc', 'block_enrolmenttimer'),
        array_keys($options),
        array_values($options)
    ));

    // Enrolment Ending Alert Settings.
    $settings->add(new admin_setting_heading('notificationsheadingalert',
        get_string('settings_notifications_alert', 'block_enrolmenttimer'), ''));

    $settings->add(new admin_setting_configcheckbox(
        'enrolmenttimer/timeleftmessagechk',
        get_string('timeleftmessagechk', 'block_enrolmenttimer'),
        get_string('timeleftmessagechk_help', 'block_enrolmenttimer'),
        0
    ));

    $settings->add(new admin_setting_configtext(
        'enrolmenttimer/daystoalertenrolmentend',
        get_string('daystoalertenrolmentend', 'block_enrolmenttimer'),
        get_string('daystoalertenrolmentend_help', 'block_enrolmenttimer'),
        '10'
    ));

    $settings->add(new admin_setting_configtext(
        'enrolmenttimer/enrolmentemailsubject',
        get_string('emailsubject', 'block_enrolmenttimer'),
        get_string('emailsubject_help', 'block_enrolmenttimer'),
        get_string('emailsubject_expiring_default', 'block_enrolmenttimer')
    ));

    $settings->add(new admin_setting_confightmleditor(
        'enrolmenttimer/timeleftmessage',
        get_string('timeleftmessage', 'block_enrolmenttimer'),
        get_string('timeleftmessage_help', 'block_enrolmenttimer'),
        ''
    ));

    // Course Completed Message Settings.
    $settings->add(new admin_setting_heading('notificationsheadingcompletion',
        get_string('settings_notifications_completion', 'block_enrolmenttimer'), ''));

    $settings->add(new admin_setting_configcheckbox(
        'enrolmenttimer/completionsmessagechk',
        get_string('completionsmessagechk', 'block_enrolmenttimer'),
        get_string('completionsmessagechk_help', 'block_enrolmenttimer'),
        0
    ));

    $settings->add(new admin_setting_configtext(
        'enrolmenttimer/completionpercentage',
        get_string('completionpercentage', 'block_enrolmenttimer'),
        get_string('completionpercentage_help', 'block_enrolmenttimer'),
        '100'
    ));

    $settings->add(new admin_setting_configtext(
        'enrolmenttimer/completionemailsubject',
        get_string('emailsubject', 'block_enrolmenttimer'),
        get_string('emailsubject_help', 'block_enrolmenttimer'),
        get_string('emailsubject_completion_default', 'block_enrolmenttimer')
    ));


    $settings->add(new admin_setting_confightmleditor(
        'enrolmenttimer/completionsmessage',
        get_string('completionsmessage', 'block_enrolmenttimer'),
        get_string('completionsmessage_help', 'block_enrolmenttimer'),
        ''
    ));
}