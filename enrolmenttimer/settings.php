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
 * Settings used by the enrolmenttimer module, were moved from mod_edit
 *
 * @package    block
 * @subpackage enrolmenttimer
 * @copyright  2014 Aaron Leggett - LearningWorks
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or late
 **/

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    /** Settings */
    $settings->add(new admin_setting_configcheckbox(
        'enrolmenttimer/editinstancetitles', 
        get_string('editinstancetitles', 'block_enrolmenttimer'),
        get_string('editinstancetitles_help', 'block_enrolmenttimer'),
        0
    ));

    $settings->add(new admin_setting_configcheckbox(
    	'enrolmenttimer/timeleftmessagechk', 
    	get_string('timeleftmessagechk', 'block_enrolmenttimer'),
        get_string('timeleftmessagechk_help', 'block_enrolmenttimer'),
        1
    ));

    $settings->add(new admin_setting_configtext(
    	'enrolmenttimer/timeleftmessage', 
    	get_string('timeleftmessage', 'block_enrolmenttimer'),
        get_string('timeleftmessage_help', 'block_enrolmenttimer'), 
        null, 
        PARAM_URL
    ));
    //$settings->disabledIf('enrolmenttimer/timeleftmessage', 'enrolmenttimer/timeleftmessagechk', 'checked');

    $settings->add(new admin_setting_configcheckbox(
    	'enrolmenttimer/completionsmessagechk', 
    	get_string('completionsmessagechk', 'block_enrolmenttimer'),
        get_string('completionsmessagechk_help', 'block_enrolmenttimer'),
        1
    ));

    $settings->add(new admin_setting_configtext(
    	'enrolmenttimer/completionsmessage', 
    	get_string('completionsmessage', 'block_enrolmenttimer'),
        get_string('completionsmessage_help', 'block_enrolmenttimer'), 
        null, 
        PARAM_URL
    ));
}