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
 * Main file to display the block.
 * @package    block_enrolmenttimer
 * @copyright  LearningWorks Ltd 2016
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Class block_enrolmenttimer
 *
 * @package    block_enrolmenttimer
 * @copyright  LearningWorks Ltd 2016
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_enrolmenttimer extends block_base {
    /**
     * Initialize the block.
     */
    public function init() {
        $this->title = get_string('enrolmenttimer', 'block_enrolmenttimer');
    }

    /**
     * Tell Moodle we have a config file.
     * @return bool
     */
    public function has_config() {
        return true;
    }

    /**
     * Tell Moodle where this block can be.
     * @return array
     */
    public function applicable_formats() {
        return array(
               'site-index' => false,
              'course-view' => true,
        'course-view-social' => true,
                      'mod' => true
        );
    }

    /**
     * Tell Moodle we have some specializations.
     */
    public function specialization() {

        $this->title = get_string('enrolmenttimer', 'block_enrolmenttimer');

        $this->completionpercentage = get_config('enrolmenttimer', 'completionpercentage');

        $this->activecountdown = get_config('enrolmenttimer', 'activecountdown');

        $this->viewoptions = get_config('enrolmenttimer', 'viewoptions');

    }

    /**
     * Get the output of the block since it has been called on a course page.
     * @return stdClass|stdObject
     */
    public function get_content() {
        global $CFG;
        require_once($CFG->dirroot . '/blocks/enrolmenttimer/locallib.php');

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->page->requires->js_call_amd('block_enrolmenttimer/scripts', 'initialise');

        $timeleft = block_enrolmenttimer_get_remaining_enrolment_period($this->viewoptions);
        $this->content->text .= '<div';
        if ($this->activecountdown == 1) {
            $this->content->text .= ' class="active"';
        }
        $this->content->text .= '>';

        if (!$timeleft) {
            $displaynothingnodateset = get_config('enrolmenttimer', 'displayNothingNoDateSet');
            if ($displaynothingnodateset == 1) {
                $this->content->text = '';
                return $this->content;
            } else {
                $this->content->text .= '<p class="noDateSet">'.get_string('noDateSet', 'block_enrolmenttimer').'</p></div>';
                return $this->content;
            }
        } else {
            $counter = 1;
            $text = '';
            $force2digits = get_config('enrolmenttimer', 'forceTwoDigits');
            $displaylabels = get_config('enrolmenttimer', 'displayUnitLabels');
            $displaytextcounter = get_config('enrolmenttimer', 'displayTextCounter');

            $this->content->text .= '<hr>';
            $this->content->text .= '<div class="visual-counter">';
            $this->content->text .= '<div class="timer-wrapper"';
            if ($force2digits == 1) {
                $this->content->text .= ' data-id="force2" ';
            }
            $this->content->text .= '>';
            foreach ($timeleft as $unit => $count) {
                $stringcount = (string)$count;
                $countlength = strlen($stringcount);

                if ($displaylabels == 1) {
                    $this->content->text .= '<div class="numberTypeWrapper">';
                }

                $this->content->text .= '<div class="timerNum" data-id="'.$unit.'">';

                if ($countlength == 1 && $force2digits == 1) {
                    $this->content->text .= '<span class="timerNumChar" data-id="0">0</span>';
                    $this->content->text .= '<span class="timerNumChar" data-id="1">'.$stringcount.'</span>';
                } else {
                    for ($i = 0; $i < $countlength; $i++) {
                        $this->content->text .= '<span class="timerNumChar" data-id="'.$i.'">'.$stringcount[$i].'</span>';
                    }
                }

                $this->content->text .= '</div>';

                if ($displaylabels == 1) {
                    $this->content->text .= '<p>'.$unit.'</p></div>';
                }

                if ($counter != count($timeleft)) {
                    $this->content->text .= '<div class="seperator">:</div>';
                }

                $text .= '<span class="'.$unit.'">'.$count.'</span> ';
                if ($count > 1) {
                    $text .= $unit.' ';
                } else {
                    $text .= rtrim($unit, "s").' ';
                }
                $counter++;
            }
            $this->content->text .= '</div>';
            $this->content->text .= '</div>';
            $this->content->text .= '<hr>';
            $this->content->text .= '<div class="text-wrapper">';
            $this->content->text .= '<p class="text-desc"';
            if ($displaytextcounter == 0) {
                $this->content->text .= ' style="display: none;"';
            }
            $this->content->text .= '>'.$text.'</p>';
            $this->content->text .= '<p class="sub-text">'.get_string('expirytext', 'block_enrolmenttimer').'</p>';
            $this->content->text .= '</div>';
        }
        $this->content->text .= '</div>';
        $this->content->footer = '';
        return $this->content;
    }
}