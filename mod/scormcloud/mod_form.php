<?php //$Id: mod_form.php,v 1.3 2008/08/10 08:05:15 mudrd8mz Exp $

/**
 * This file defines de main scormcloud configuration form
 * It uses the standard core Moodle (>1.8) formslib. For
 * more info about them, please visit:
 * 
 * http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * The form must provide support for, at least these fields:
 *   - name: text element of 64cc max
 *
 * Also, it's usual to use these fields:
 *   - intro: one htmlarea element to describe the activity
 *            (will be showed in the list of activities of
 *             scormcloud type (index.php) and in the header 
 *             of the scormcloud main page (view.php).
 *   - introformat: The format used to write the contents
 *             of the intro field. It automatically defaults 
 *             to HTML when the htmleditor is used and can be
 *             manually selected if the htmleditor is not used
 *             (standard formats are: MOODLE, HTML, PLAIN, MARKDOWN)
 *             See lib/weblib.php Constants and the format_text()
 *             function for more info
 */

require_once ($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/mod/scormcloud/locallib.php');

require_once('SCORMAPI/ScormEngineService.php');
require_once('SCORMAPI/ServiceRequest.php');
require_once('SCORMAPI/CourseData.php');

class mod_scormcloud_mod_form extends moodleform_mod {

	function definition() {

		global $COURSE;
		$mform    =& $this->_form;

//-------------------------------------------------------------------------------
    /// Adding the "general" fieldset, where all the common settings are showed
        $mform->addElement('header', 'general', get_string('general', 'form'));
    /// Adding the standard "name" field
    $courseid = optional_param("course");
	$update = optional_param("update");
	if(!isset($courseid))
	{
		$cm = get_record("course_modules","id","$update");
		$courseid = $cm->course;
		$scormcloudid = $cm->instance;
	}
		$course = get_record("course", "id", "$courseid");
    
    //echo $course->shortname;
        $mform->addElement('text', 'name', get_string('scormcloudname', 'scormcloud'), $course->shortname);
		$mform->setType('name', PARAM_TEXT);
		$mform->setDefault('name',$course->shortname);
		$mform->addRule('name', null, 'required', null, 'client');
		
		
		if(isset($scormcloudid))
		{
			$mform->addElement('hidden', 'id', $scormcloudid);
		}		 
		$mform->addElement('hidden', 'scoreformat', null);
		$mform->addElement('hidden', 'allowpreview', null);
		$mform->addElement('hidden', 'allowreview', null);
    /// Adding the optional "intro" and "introformat" pair of fields
    	//$mform->addElement('htmleditor', 'intro', get_string('scormcloudintro', 'scormcloud'));
	//	$mform->setType('intro', PARAM_RAW);
	//	$mform->addRule('intro', get_string('required'), 'required', null, 'client');
        //$mform->setHelpButton('intro', array('writing', 'richtext'), false, 'editorhelpbutton');
		
        //$mform->addElement('format', 'introformat', get_string('format'));

//-------------------------------------------------------------------------------
    /// Adding the rest of scormcloud settings, spreeading all them into this fieldset
    /// or adding more fieldsets ('header' elements) if needed for better logic
        //$mform->addElement('static', 'label1', 'scormcloudsetting1', 'Your scormcloud fields go here. Replace me!');

        //$mform->addElement('header', 'scormcloudfieldset', get_string('scormcloudfieldset', 'scormcloud'));
        //$mform->addElement('static', 'label2', 'scormcloudsetting2', 'Your scormcloud fields go here. Replace me!');

//-------------------------------------------------------------------------------
        // add standard elements, common to all modules
		$this->standard_coursemodule_elements();
//-------------------------------------------------------------------------------
        // add standard buttons, common to all modules
        $this->add_action_buttons();

	}
	
	function data_preprocessing(&$default_values) {
        global $COURSE;

        
    }

    function validation($data, $files) {

		global $CFG;

		//$mform = $this->_form;
		//$courseid = $data['course'];                  // Course Module ID
		//$absoluteFilePathToZip = $files['element_name'];
		
		//echo $absoluteFilePathToZip;
		//if ($data = $mform->get_data()) {
			//$pifdir = '';
		    //if ($pifdir = make_upload_directory("$courseid/$CFG->moddata/scormcloud")) {
			
				
				//$mform->save_files($pifdir);
				//$newfilename = $mform->get_new_filename();
			
				//$ScormService = new ScormEngineService('http://174.129.243.98/EngineWebServices/api','brian','GxXrlo5RDGquCRNrdOBpSwHqv7L75oqW7NuwaHr6');
				//$courseService = $ScormService->getCourseService();

				//$absoluteFilePathToZip = $newfilename;
				//$courseService->ImportCourse($courseid, $absoluteFilePathToZip, null, null);
		//	}
		//}        

		$errors = parent::validation($data, $files);

        //$validate = scormcloud_validate($data);

        //if (!$validate->result) {
        //    $errors = $errors + $validate->errors;
        //}

	

        return $errors;
    }
    //need to translate the "options" field.
    function set_data($default_values) {
	/*
        if (is_object($default_values)) {
            if (!empty($default_values->options)) {
                $options = explode(',', $default_values->options);
                foreach ($options as $option) {
                    $opt = explode('=', $option);
                    if (isset($opt[1])) {
                        $default_values->$opt[0] = $opt[1];
                    }
                }
            }
            $default_values = (array)$default_values;
        }
        $this->data_preprocessing($default_values);
*/
        parent::set_data($default_values); //never slashed for moodleform_mod
    }
}


//if ($data = $mform->get_data()) {
//              $mform->save_files($destination_directory);
//              $newfilename = $mform->get_new_filename();
//            }


?>
