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
 * @copyright  2014 Aaron Leggett - LearningWorks Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Checks the timeleft on enrolment in a given course
 *
 * @param string $filepath - filepath of the XML file to read in
 * @return array from the XML file
 */
function enrolmenttimer_get_enrolment_period_remaining($unitsToShow){
	global $COURSE, $USER, $DB, $CFG;

 	$context = context_course::instance($COURSE->id);
	
	if(has_capability('moodle/site:config', $context)){
		$record = 0;
	}else{
		$records = enrolmenttimer_get_enrolment_records($USER->id, $COURSE->id);
		if(isset($records[$USER->id])){
			$record = $records[$USER->id];
		}else{
			$record = 0;
		}
	}

	// $record = array();
 	// $record['timeend'] = 1434238823;
 	// var_dump($record);

	if(!is_object($record) || $record->timeend == 0 ){
		return false;
	}else{
		$timeDifference = (int)$record->timeend - time();
		
		$tokens = array (
	        31536000 => 'years',
	        2592000 => 'months',
	        604800 => 'weeks',
	        86400 => 'days',
	        3600 => 'hours',
	        60 => 'minutes',
	        1 => 'seconds'
	    );

	    $result = array();

    	if(empty($unitsToShow)){
    		//they have not selected any, so show all
    		$unitsToShow = enrolmenttimer_get_possible_units();
    	}else{
    		//have the selected units, but we only have id's for their values
    		$unitsToShow = enrolmenttimer_get_selected_units_text_values($unitsToShow);
    	}

	    foreach($tokens as $unit => $text){
	    	if(in_array($text, $unitsToShow)){
		    	if($timeDifference > $unit){
		    		$count = floor($timeDifference/$unit);
		    		$result[$text] = $count;
		    		$timeDifference = $timeDifference-($count*$unit); 
		    	}
		    }
	    }

		return $result;
	}
}

function enrolmenttimer_get_enrolment_records($userid, $courseid){
	global $DB;

	$sql = '
	    	SELECT ue.userid, ue.id, ue.timestart, ue.timeend
	      	FROM mdl_user_enrolments ue
	      	JOIN mdl_enrol e on ue.enrolid = e.id
	     	WHERE ue.userid = ? AND e.courseid = ?
	    ';
	return $DB->get_records_sql($sql, array($userid, $courseid));
}

function enrolmenttimer_get_possible_units(){
	return array('years', 'months', 'weeks', 'days', 'hours', 'minutes', 'seconds');
}

function enrolmenttimer_get_selected_units_text_values($idstring){
	$idarray = explode(',', $idstring);
	$possibleUnits = enrolmenttimer_get_possible_units();
	$output = array();
	foreach($idarray as $key => $value){
		array_push($output, $possibleUnits[$value]);
	}

	return $output;
}