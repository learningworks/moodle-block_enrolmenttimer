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
 * Main file to display the block
 *
 * @package    block_enrolmenttimer
 * @copyright  2014 Aaron Leggett - LearningWorks Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_enrolmenttimer extends block_base {
    public function init() {
        $this->title = get_string('enrolmenttimer', 'block_enrolmenttimer');
    }//closing init()

    public function has_config() {
    	return true;
    }//closing has_config()

    public function applicable_formats() {
	  return array(
	           'site-index' => false,
	          'course-view' => true, 
	   'course-view-social' => true,
	                  'mod' => true
	  );
	}//closing //applicable_formats()

  	public function specialization() {
		/*
		*
		* Use this code to check instance settings before
		* using the admin settings
		*
		*/
		
		// if (!empty($this->config->title)) {
		// 	$this->title = $this->config->title;
		// } else {
			$this->title = get_string('enrolmenttimer', 'block_enrolmenttimer');
		//}

		// if(!empty($this->config->instance_completionpercentage)) {
		// 	$this->completionpercentage = $this->config->instance_completionpercentage;
		// } else {
			$this->completionpercentage = get_config('enrolmenttimer', 'completionpercentage');
		//}

		// if(!empty($this->config->instance_activecountdown)) {
		// 	$this->activecountdown = $this->config->instance_activecountdown;
		// } else {
			$this->activecountdown = get_config('enrolmenttimer', 'activecountdown');
		//}

		// if(!empty($this->config->instance_viewoptions)) {
		// 	$this->viewoptions = $this->config->instance_viewoptions;
		// } else {
			$this->viewoptions = get_config('enrolmenttimer', 'viewoptions');
		//}
	}//closing specialization

	public function cron() {
		global $CFG;
	    mtrace( "block/enrolmenttimer - Cron is beginning" );
	    require_once($CFG->dirroot . '/blocks/enrolmenttimer/locallib.php');
	    global $DB; // Global database object
 
	    if(get_config('enrolmenttimer', 'timeleftmessagechk') == 0 && get_config('enrolmenttimer', 'completionsmessagechk') == 0){
			//neither of the cron emails are set to run so exit
	    	mtrace('- no mail sent');
	    	return true;
	    }

	    // Get the instances of the block
	    $instances = $DB->get_records( 'block_instances', array('blockname'=>'enrolmenttimer') );
	 
	    //get the cron run time 
	    $crontime = $DB->get_record('block', array('name'=>'enrolmenttimer'));
	    //will never return null, otherwise we wouldnt be in the cron method
	    $crontime = $crontime->cron;

	    // Iterate over the instances
	    foreach ($instances as $instance) {
	        // Recreate block object
	        $block = block_instance('enrolmenttimer', $instance);

	        //get courseid of the course this instance is on
	        $contextid = $block->instance->parentcontextid;
	        $blockcontext = context::instance_by_id($contextid);
	        $courseid = $blockcontext->instanceid;

	        //get enrolled users from the course
	        $course = $DB->get_record('course', array('id'=>$courseid));
	        $coursecontext = context_course::instance($course->id);
	        $users = get_role_users(5, $coursecontext);

	        //loop through - check days left alert and completion
	        foreach($users as $user){
	        	//Send Notification Emails
	        	if(get_config('enrolmenttimer', 'timeleftmessagechk') == 1){
		        	$records = block_enrolmenttimer_get_enrolment_records($user->id, $course->id);
		        	if(isset($records[$user->id])){
						$record = $records[$user->id];
						if(is_object($record) || $record->timeend != 0 ){
							//calculate timestamp at which to alert the user
							$enrolmentEnd = (int)$record->timeend;
							$enrolmentAlertPeriod = (int)get_config('enrolmenttimer', 'daystoalertenrolmentend')*$crontime;
							$enrolmentAlertTime = $enrolmentEnd - $enrolmentAlertPeriod;

							//calculate timestamp at which to stop alerting user
							$enrolmentStopAlertPeriod = (int)$enrolmentAlertTime + $crontime;

							if($enrolmentAlertTime < time() && $enrolmentStopAlertPeriod > time()){
								// Send the email to the user
							    $from = core_user::get_support_user();
								$subject = get_config('enrolmenttimer', 'enrolmentemailsubject');
								$body = get_config('enrolmenttimer', 'timeleftmessage');

							    //personalise subject words
								$body = str_replace("[[user_name]]", $user->firstname, $body);
								$body = str_replace("[[course_name]]", $course->fullname, $body);
								$body = str_replace("[[days_to_alert]]", get_config('enrolmenttimer', 'daystoalertenrolmentend'), $body);

							    $textOnlyBody = strip_tags($body);
								email_to_user($user, $from, $subject, $textOnlyBody, $body);
							}
						}
					}
				}

				//Send Completion Emails
				if(get_config('enrolmenttimer', 'completionsmessagechk') == 1){
					$completion = $DB->get_record('course_completions', array('userid'=>$user->id, 'course'=>$course->id));
					if($completion != false && ($completion->timecompleted != NULL || $completion->reaggregate != NULL)){
						if($completion->timecompleted != NULL){
							//set by user completing course
							$completion = $completion->timecompleted;
						}elseif($completion->reaggregate != NULL){
							// set by admin override
							$completion = $completion->reaggregate;
						}else{
							//moves to the next loop in the foreach
							continue;
						}
						
						if($completion > (time()-$crontime)){
							// Send the email to the user
						    $from = core_user::get_support_user();
							$subject = get_config('enrolmenttimer', 'completionemailsubject');
							$body = get_config('enrolmenttimer', 'completionsmessage');


							//personalise subject words
							$body = str_replace("[[user_name]]", $user->firstname, $body);
							$body = str_replace("[[course_name]]", $course->fullname, $body);

							$textOnlyBody = strip_tags($body);
							email_to_user($user, $from, $subject, $textOnlyBody, $body);
						}
					}
				}
	        }
	    }

	    return true;

	}//closing cron()

    public function get_content() {
	    global $COURSE, $USER, $DB, $CFG;
	    require_once($CFG->dirroot . '/blocks/enrolmenttimer/locallib.php');

	    if ($this->content !== null) {
	    	return $this->content;
	    }

	    $this->content = new stdClass;
	    $this->content->text = '';

	    $this->page->requires->js('/blocks/enrolmenttimer/scripts/jquery-1.10.2.min.js');
	    $this->page->requires->js('/blocks/enrolmenttimer/scripts/scripts.js');

	    $timeLeft = block_enrolmenttimer_get_remaining_enrolment_period($this->viewoptions);
	    $this->content->text .= '<div';
		    if($this->activecountdown == 1){
		    	$this->content->text .= ' class="active"';
		    }
	    $this->content->text .= '>';

	    if(!$timeLeft){
	    	$displayNothingNoDateSet = get_config('enrolmenttimer', 'displayNothingNoDateSet');
	    	if($displayNothingNoDateSet == 1){
	    		$this->content->text = '';
	    		return $this->content;
	    	}else{
	    		$this->content->text .= '<p class="noDateSet">'.get_string('noDateSet','block_enrolmenttimer').'</p></div>';
	    		return $this->content;
	    	}
	    }else{
	    	//$this->content->text .= 'You have ';
	    	$counter = 1;
	    	$text = '';
	    	$force2digits = get_config('enrolmenttimer', 'forceTwoDigits');
	    	$displayLabels = get_config('enrolmenttimer', 'displayUnitLabels');
	    	$displayTextCounter = get_config('enrolmenttimer', 'displayTextCounter');

	    	$this->content->text .= '<hr>';
	    	$this->content->text .= '<div class="visual-counter">';
	    	$this->content->text .= '<div class="timer-wrapper"';
	    	if($force2digits == 1){
	    		$this->content->text .= ' data-id="force2" ';
	    	}
	    	$this->content->text .= '>';
	    	foreach($timeLeft as $unit => $count){
    			$stringCount = (string)$count;
    			$countLength = strlen($stringCount);

		    	if($displayLabels == 1){
		    		$this->content->text .= '<div class="numberTypeWrapper">';
		    	}

		    	$this->content->text .= '<div class="timerNum" data-id="'.$unit.'">';
		    	
		    	if($countLength == 1 && $force2digits == 1){
		    		$this->content->text .= '<span class="timerNumChar" data-id="0">0</span>';
		    		$this->content->text .= '<span class="timerNumChar" data-id="1">'.$stringCount.'</span>';
		    	}else{
		    		for ($i=0; $i < $countLength; $i++) { 
			    		$this->content->text .= '<span class="timerNumChar" data-id="'.$i.'">'.$stringCount[$i].'</span>';
			    	}
		    	}
		    	
		    	$this->content->text .= '</div>';
		    	
		    	if($displayLabels == 1){
		    		$this->content->text .= '<p>'.$unit.'</p></div>';
		    	}		    	

		    	if($counter != count($timeLeft)){
		    		$this->content->text .= '<div class="seperator">:</div>';
		    	}

	    		$text .= '<span class="'.$unit.'">'.$count.'</span> ';
		    	if($count > 1){
		    		$text .= $unit.' ';
		    	}else{
		    		$text .= rtrim($unit, "s").' ';
		    	}

		    	$counter++;

		    }
		    $this->content->text .= '</div>';
		    $this->content->text .= '</div>';
		    $this->content->text .= '<hr>';
		    $this->content->text .= '<div class="text-wrapper">';
		    $this->content->text .= '<p class="text-desc"';
			if($displayTextCounter == 0){
    			$this->content->text .= ' style="display: none;"';
    		}
		    $this->content->text .= '>'.$text.'</p>';
		    $this->content->text .= '<p class="sub-text">'.get_string('expirytext', 'block_enrolmenttimer').'</p>';
		    $this->content->text .= '</div>';
	    }   

	    $this->content->text .= '</div>';
	    
	    $this->content->footer = '';

	    return $this->content;
  	}//closing get_content()
}//closing class definition