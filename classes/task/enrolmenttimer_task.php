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
 * Scheduled task for enrolment timer
 * @package    block_enrolmenttimer
 * @copyright  LearningWorks Ltd 2016
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_enrolmenttimer\task;
defined('MOODLE_INTERNAL') || die();
if (!defined('__ROOT__')) {
    define('__ROOT__', dirname(dirname(__FILE__)));
}
require_once(__ROOT__.'../../../../config.php');

/**
 * Class enrolmenttimer_task
 * @package    block_enrolmenttimer
 * @copyright  LearningWorks Ltd 2016
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enrolmenttimer_task extends \core\task\scheduled_task {
    /**
     * Return the name of the Scheduled task.
     * @return string
     */
    public function get_name() {
        // Shown in admin screens.
        return get_string('pluginname', 'block_enrolmenttimer');
    }

    /**
     * The task that is run during crom.
     * @return bool
     */
    public function execute() {
        global $CFG, $DB;
        mtrace( "block/enrolmenttimer - Cron is beginning" );
        require_once($CFG->dirroot . '/blocks/enrolmenttimer/locallib.php');

        if (get_config('enrolmenttimer', 'timeleftmessagechk') == 0 && get_config('enrolmenttimer', 'completionsmessagechk') == 0) {
            // Neither of the cron emails are set to run so exit.
            mtrace('- no mail sent');
            return true;
        }

        // Get the instances of the block.
        $instances = $DB->get_records( 'block_instances', array('blockname' => 'enrolmenttimer') );

        // Will never return null, otherwise we wouldn't be in the cron method.

        if ($this->get_minute() == '*') {
            $crontime = 60;
        } else if (explode('*/', $this->get_minute())) {
            $arr = explode('*/', $this->get_minute());
            $crontime = $arr[1] * 60;
        } else {
            $crontime = $this->get_minute() * 60;
        }

        // Iterate over the instances.
        foreach ($instances as $instance) {
            // Recreate block object.
            $block = block_instance('enrolmenttimer', $instance);

            // Get courseid of the course this instance is on.
            $contextid = $block->instance->parentcontextid;
            $blockcontext = \context::instance_by_id($contextid);
            $courseid = $blockcontext->instanceid;

            // Get enrolled users from the course.
            $course = $DB->get_record('course', array('id' => $courseid));
            $coursecontext = \context_course::instance($course->id);
            $users = get_role_users(5, $coursecontext);
            // Loop through - check days left alert and completion.
            foreach ($users as $user) {
                // Send Notification Emails.
                if (get_config('enrolmenttimer', 'timeleftmessagechk') == 1) {
                    $records = block_enrolmenttimer_get_enrolment_records($user->id, $course->id);

                    if (isset($records[$user->id])) {
                        $record = $records[$user->id];
                        if (is_object($record) && $record->timeend != 0 ) {
                            // Calculate timestamp at which to alert the user.
                            $enrolmentend = (int)$record->timeend;
                            $enrolmentalertperiod = (int)get_config('enrolmenttimer', 'daystoalertenrolmentend') * 86400; // Daystoalert meant to be hours?.
                            $enrolmentalerttime = $enrolmentend - $enrolmentalertperiod;

                            if (!$DB->record_exists('block_enrolmenttimer', array('enrolid' => $record->id))) {
                                $object = new \stdClass();
                                $object->enrolid = $record->id;
                                $object->alerttime = $enrolmentalerttime;
                                $object->sent = false;
                                $DB->insert_record('block_enrolmenttimer', $object);
                            }
                        } else if ($record->timeend == 0) {
                            $timeend = $DB->get_record('enrol', array('enrol' => 'self', 'id' => $record->enrolid), 'enrolenddate');
                            if (isset($timeend->enrolenddate) && (int) $timeend->enrolenddate > 0) {
                                // INSERT INTO DB.
                                if (!$DB->record_exists('block_enrolmenttimer', array('enrolid' => $record->id))) {
                                    $object = new \stdClass();
                                    $object->enrolid = $record->id;
                                    $object->alerttime = $timeend->enrolenddate;
                                    $object->sent = false;
                                    $DB->insert_record('block_enrolmenttimer', $object);
                                }
                            }
                        }
                    }
                }

                // Send Completion Emails.
                if (get_config('enrolmenttimer', 'completionsmessagechk') == 1) {
                    $completion = $DB->get_record('course_completions', array('userid' => $user->id, 'course' => $course->id));
                    if ($completion != false && ($completion->timecompleted != null || $completion->reaggregate != null)) {
                        if ($completion->timecompleted != null) {
                            // Set by user completing course.
                            $completion = $completion->timecompleted;
                        } else if ($completion->reaggregate != null) {
                            // Set by admin override.
                            $completion = $completion->reaggregate;
                        } else {
                            // Moves to the next loop in the foreach.
                            continue;
                        }

                        if ($completion > (time() - $crontime)) {
                            // Send the email to the user.
                            $from = \core_user::get_support_user();
                            $subject = get_config('enrolmenttimer', 'completionemailsubject');
                            $body = get_config('enrolmenttimer', 'completionsmessage');

                            // Personalise subject words.
                            $body = str_replace("[[user_name]]", $user->firstname, $body);
                            $body = str_replace("[[course_name]]", $course->fullname, $body);

                            $textonlybody = strip_tags($body);
                            email_to_user($user, $from, $subject, $textonlybody, $body);
                        }
                    }
                }
            }
        }

        // ITERATE THROUGH ALL UNSENT EMAILS WITH A ALERT TIME OF LESS THAN NOW.

        $time = time();
        $sql = "SELECT * FROM {block_enrolmenttimer} WHERE sent = false AND alerttime < $time";
        $emailstosend = $DB->get_records_sql($sql);

        foreach ($emailstosend as $key => $value) {

            $enrolinstance = $DB->get_record('user_enrolments', array('id' => $value->enrolid));
            $user = $DB->get_record('user', array('id' => $enrolinstance->userid));
            $enrol = $DB->get_record('enrol', array('id' => $enrolinstance->enrolid));
            $course = $DB->get_record('course', array('id' => $enrol->courseid));
            mtrace("prepping mail to learner.");
            $from = \core_user::get_support_user();
            $subject = get_config('enrolmenttimer', 'enrolmentemailsubject');
            $body = get_config('enrolmenttimer', 'timeleftmessage');

            // Personalise subject words.
            $body = str_replace("[[user_name]]", $user->firstname, $body);
            $body = str_replace("[[course_name]]", $course->fullname, $body);
            $body = str_replace("[[days_to_alert]]", get_config('enrolmenttimer',
                'daystoalertenrolmentend'), $body);

            $textonlybody = strip_tags($body);
            $value->sent = email_to_user($user, $from, $subject, $textonlybody, $body);
            $DB->update_record('block_enrolmenttimer', $value);
        }
        return true;
    }
}