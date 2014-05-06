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

	    $timeLeft = getEnrolmentPeriodRemaining($COURSE, $USER, $DB);
	    $this->content = new stdClass;
	    $this->content->text = '<p>';

	    foreach($timeLeft as $unit => $count){
	    	$this->content->text .= '<span class=".'.$unit.'">'.$timeLeft[$unit].'</span> ';
	    	if($timeLeft[$unit] > 1){
	    		$this->content->text .= $unit.'s ';
	    	}else{
	    		$this->content->text .= $unit.' ';
	    	}
	    }	   

	    $this->content->text .= '</p>';
	    $this->content->footer = '';

	    return $this->content;
  	}//closing get_content()
}//closing class definition