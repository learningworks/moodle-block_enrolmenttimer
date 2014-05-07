<?php
 
class block_enrolmenttimer_edit_form extends block_edit_form {
 
    protected function specific_definition($mform) {
        global $CFG;

        $forceDefaults = get_config('enrolmenttimer', 'forceDefaults');
        if($forceDefaults == 1){

        }else{        
            require_once($CFG->dirroot . '/blocks/enrolmenttimer/locallib.php');

            $mform->addElement('header', 'configheader', get_string('enrolmenttimer', 'block_enrolmenttimer'));

            $mform->addElement('text', 'instance_title', get_string('instance_title', 'block_enrolmenttimer'));
    	    $mform->setDefault('instance_title', get_string('enrolmenttimer', 'block_enrolmenttimer'));
    	    $mform->setType('instance_title', PARAM_MULTILANG);

            $mform->addElement('text', 'instance_completionpercentage', get_string('completionpercentage', 'block_enrolmenttimer'));
            $mform->setDefault('instance_completionpercentage', get_config('enrolmenttimer', 'completionpercentage'));
            $mform->setType('instance_completionpercentage', PARAM_MULTILANG);
            $mform->addHelpButton('instance_completionpercentage', 'completionpercentage', 'block_enrolmenttimer');

            $mform->addElement('advcheckbox', 'instance_activecountdown', get_string('activecountdown', 'block_enrolmenttimer'), get_string('activecountdown_help', 'block_enrolmenttimer'));
            $mform->setDefault('instance_activecountdown', get_config('enrolmenttimer', 'activecountdown'));

            $select = $mform->addElement('select', 'instance_viewoptions', get_string('viewoptions', 'block_enrolmenttimer'), getPossibleUnits());
            $select->setMultiple(true);
            $select->setSelected(get_config('enrolmenttimer', 'viewoptions'));

        }

        

        // // A sample string variable with a default value.
        // $mform->addElement('text', 'config_text', get_string('blockstring', 'block_enrolmenttimer'));
        // $mform->setDefault('config_text', 'default value');
        // $mform->setType('config_text', PARAM_TEXT);        
 
    }
}