<?php
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
		if (!empty($this->config->title)) {
			$this->title = $this->config->title;
		} else {
			$this->title = get_string('enrolmenttimer', 'block_enrolmenttimer');
		}

		if(!empty($this->config->instance_completionpercentage)) {
			$this->completionpercentage = $this->config->instance_completionpercentage;
		} else {
			$this->completionpercentage = get_config('enrolmenttimer', 'completionpercentage');
		}

		if(!empty($this->config->instance_activecountdown)) {
			$this->activecountdown = $this->config->instance_activecountdown;
		} else {
			$this->activecountdown = get_config('enrolmenttimer', 'activecountdown');
		}

		if(!empty($this->config->instance_viewoptions)) {
			$this->viewoptions = $this->config->instance_viewoptions;
		} else {
			$this->viewoptions = get_config('enrolmenttimer', 'viewoptions');
		}
	}//closing specialization

	public function cron() {
	    mtrace( "block/enrolmenttimer - Cron is beginning" );
	    global $DB; // Global database object
 
	    // Get the instances of the block
	    $instances = $DB->get_records( 'block_instances', array('blockname'=>'enrolmenttimer') );
	 
	    // Iterate over the instances
	    foreach ($instances as $instance) {
	        // Recreate block object
	        $block = block_instance('enrolmenttimer', $instance);
	 		
	        $context = $block
	        $course = $DB->get_content('course', array('id'=>));
	        $users = 
	        //mtrace($block->page);
	    }

	    mtrace('ending.................');
	    return true;

	}//closing cron()

    public function get_content() {
	    global $COURSE, $USER, $DB, $CFG;
	    require_once($CFG->dirroot . '/blocks/enrolmenttimer/locallib.php');

	    if ($this->content !== null) {
	    	return $this->content;
	    }

	    $this->page->requires->js('/blocks/enrolmenttimer/scripts/jquery-1.10.2.min.js');
	    $this->page->requires->js('/blocks/enrolmenttimer/scripts/scripts.js');

	    $timeLeft = getEnrolmentPeriodRemaining($this->viewoptions);
	    $this->content = new stdClass;
	    $this->content->text .= '<div';
		    if($this->activecountdown == 1){
		    	$this->content->text .= ' class="active"';
		    }
	    $this->content->text .= '>';

	    if(!$timeLeft){
	    	$this->content->text .= 'You have no enrollment end time set.';
	    }else{
	    	//$this->content->text .= 'You have ';
	    	$counter = 1;
	    	$text = '';

	    	$this->content->text .= '<div class="timer-wrapper">';
	    	foreach($timeLeft as $unit => $count){
    			$stringCount = (string)$count;
    			$countLength = strlen($stringCount);
		    	
		    	$this->content->text .= '<div class="timerNum" data-id="'.$unit.'">';
		    	for ($i=0; $i < $countLength; $i++) { 
		    		$this->content->text .= '<span class="timerNumChar" data-id="'.$i.'">'.$stringCount[$i].'</span>';
		    	}
		    	$this->content->text .= '</div>';
		    	
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
		    $this->content->text .= '<p class="text-desc">'.$text.'</p>';
		    $this->content->text .= '<p class="sub-text">until your enrollment expires</p>';
	    }   

	    $this->content->text .= '</div>';
	    
	    $this->content->footer = '';

	    return $this->content;
  	}//closing get_content()
}//closing class definition