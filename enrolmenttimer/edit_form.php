<?php
 
class block_enrolmenttimer_edit_form extends block_edit_form {
 
    protected function specific_definition($mform) {
 
        $mform->addElement('header', 'configheader', get_string('enrolmenttimer', 'block_enrolmenttimer'));

        $canEditInstanceTitles = get_config('enrolmenttimer', 'editInstanceTitles');
        if($canEditInstanceTitles){        
            
            $mform->addElement('text', 'instance_title', get_string('instance_title', 'block_enrolmenttimer'));
    	    $mform->setDefault('instance_title', get_string('enrolmenttimer', 'block_enrolmenttimer'));
    	    $mform->setType('instance_title', PARAM_MULTILANG);
        }

        $mform->addElement('advcheckbox', 'instance_activecountdown', get_string('activecountdown', 'block_enrolmenttimer'), get_string('activecountdown_help', 'block_enrolmenttimer'));
        $mform->setDefault('instance_activecountdown', get_config('enrolmenttimer', 'activecountdown'));

        // // A sample string variable with a default value.
        // $mform->addElement('text', 'config_text', get_string('blockstring', 'block_enrolmenttimer'));
        // $mform->setDefault('config_text', 'default value');
        // $mform->setType('config_text', PARAM_TEXT);        
 
    }
}