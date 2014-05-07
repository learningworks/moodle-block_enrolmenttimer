<?php
class block_enrolmenttimer extends block_base {
    public function init() {
        $this->title = get_string('enrolmenttimer', 'block_enrolmenttimer');
    }//closing init()

    public function has_config() {
    	return true;
    }//closing has_config()

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
	    $instances = $DB->get_records( 'block_instance', array('blockid'=>'enrolmenttimer') );
	 
	    // Iterate over the instances
	    foreach ($instances as $instance) {
	        // Recreate block object
	        $block = block_instance('enrolmenttimer', $instance);
	 
	        // $block is now the equivalent of $this in 'normal' block
	        // EG $someconfigitem = $block->config->item2;
	        
	        //new way to pull config var's
	    	//$allowHTML = get_config('simplehtml', 'Allow_HTML');

	        // do something
	    }

	    return true;

	}//closing cron()

    public function get_content() {
	    global $COURSE, $USER, $DB, $CFG;
	    require_once($CFG->dirroot . '/blocks/enrolmenttimer/locallib.php');

	    if ($this->content !== null) {
	    	return $this->content;
	    }

	    $timeLeft = getEnrolmentPeriodRemaining($this->viewoptions);
	    $this->content = new stdClass;
	    $this->content->text .= '<p';
		    if($this->activecountdown == 1){
		    	$this->content->text .= ' class="active"';
		    }
	    $this->content->text .= '>';

	    if(!$timeLeft){
	    	$this->content->text .= 'You have no enrollment end time set.';
	    }else{
	    	$this->content->text .= 'You have ';
	    	$count = 1;
	    	echo $count . ' -- ' . count($timeLeft); 
	    	foreach($timeLeft as $unit => $count){
		    	if($count == count($timeLeft)){
		    		$this->content->text .= ' and ';
		    	}

		    	$this->content->text .= '<span class=".'.$unit.'">'.$count.'</span> ';
		    	if($count > 1){
		    		$this->content->text .= $unit.' ';
		    	}else{
		    		$this->content->text .= rtrim($unit, "s").' ';
		    	}

		    	$count++;
		    }
		    $this->content->text .= ' left to access this course.';
	    }   

	    $this->content->text .= '</p>';
	    $this->content->footer = '';

	    return $this->content;
  	}//closing get_content()
}//closing class definition