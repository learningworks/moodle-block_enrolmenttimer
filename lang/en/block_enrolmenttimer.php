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
 * Lang File
 *
 * @package    block_enrolmenttimer
 * @copyright  LearningWorks Ltd 2016
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Enrolment Timer';
$string['enrolmenttimer'] = 'Enrolment Timer';
$string['enrolmenttimer:addinstance'] = 'Add a new Enrolment Timer block';
$string['expirytext'] = 'until your enrollment expires';
$string['noDateSet'] = 'Your enrolment does not expire';
$string['instance_title'] = 'Set the title of this block instance';
$string['settings_notifications_alert'] = 'Alert Email Notifications Settings';
$string['settings_notifications_completion'] = 'Completion Email Notifications Settings';
$string['settings_notifications_defaults'] = 'Set defaults for instance settings';
$string['settings_general'] = 'General Settings';
$string['displayUnitLabels'] = 'Display Unit Labels';
$string['displayUnitLabels_help'] = 'Displays each unit below the main counter';
$string['displayTextCounter'] = 'Display Text Counter';
$string['displayTextCounter_help'] = 'Displays the text counter that sits below the main counter';
$string['forceTwoDigits'] = 'Force 2 Digits';
$string['forceTwoDigits_help'] = 'Forces the main countdown timer to always show atleast 2 digits (eg 01 hours left)';
$string['displayNothingNoDateSet'] = 'Hide block (No End Date Set)';
$string['displayNothingNoDateSet_help'] = 'Hides the block for users that have no end date set, If disabled, a message will be shown to those students';

$string['forceDefaults'] = 'Force Default Values';
$string['forceDefaults_help'] = 'Disables the ability for teachers to change the settings for each block instance';
$string['activecountdown'] = 'Actively count down';
$string['activecountdown_help'] = 'Actively count down the remaining time the student has to access the course using javascript';
$string['viewoptions'] = 'Increments Shown';
$string['viewoptions_desc'] = 'Select the increments to show in the block';

$string['daystoalertenrolmentend'] = 'Days to alert on';
$string['daystoalertenrolmentend_help'] = 'The amount of days before the enrollment ends at which to send the alert email';
$string['timeleftmessagechk'] = 'Enable Time Warning Email';
$string['timeleftmessagechk_help'] = 'Enables/Disables alert email';
$string['timeleftmessage'] = 'Time Remaining Warning Message';
$string['timeleftmessage_help'] = 'Email that will be sent to the student advising how much time they have left on the course eg 10 days left. Here you can use the following customisations; [[user_name]] [[course_name]] [[days_to_alert]]';
$string['emailsubject'] = 'Email Subject';
$string['emailsubject_help'] = 'Subject of the email that will be sent to the user';
$string['emailsubject_expiring_default'] = 'Enrolment Expiring';
$string['emailsubject_completion_default'] = 'Course Completed';

$string['completionsmessagechk'] = 'Enable Completion Email';
$string['completionsmessagechk_help'] = 'Enables/Disables the completion email';
$string['completionsmessage'] = 'Course Completion Email';
$string['completionsmessage_help'] = 'Email that will be sent congratulating the student on completing of the course. Here you can use the following customisations; [[user_name]] [[course_name]]';
$string['completionpercentage'] = 'Notification percentage';
$string['completionpercentage_help'] = 'This is the percentage the student must aquire in the \'Course Total\' for the completion email to be sent to them';

$string['key_years'] = 'years';
$string['key_months'] = 'months';
$string['key_weeks'] = 'weeks';
$string['key_days'] = 'days';
$string['key_hours'] = 'hours';
$string['key_minutes'] = 'minutes';
$string['key_seconds'] = 'seconds';

$string['privacy:metadata'] = 'The Enrolment Timer block only processes user data.';