<?php
/**
 * Library of functions and constants for module rusticiscormengine
 * This file should have two well differenced parts:
 *   - All the core Moodle functions, neeeded to allow
 *     the module to work integrated in Moodle.
 *   - All the rusticiscormengine specific functions, needed
 *     to implement all the module logic. Please, note
 *     that, if the module become complex and this lib
 *     grows a lot, it's HIGHLY recommended to move all
 *     these module specific functions to a new php file,
 *     called "locallib.php" (see forum, quiz...). This will
 *     help to save some memory when Moodle is performing
 *     actions across all modules.
 */

/// (replace rusticiscormengine with the name of your module and delete this line)

//define("RUSTICI_PATH_TO_LOGS","logs/moodlenet.log");  

require_once($CFG->dirroot.'/lib/accesslib.php');
require_once('SCORMAPI/ScormEngineService.php');
require_once('SCORMAPI/ServiceRequest.php');
require_once('SCORMAPI/CourseData.php');

/**
 * Get User Info for GetLearnerInfo implementation
 *
 * @param string $uid UserID for MoodleUser
 * @return object user_item
 */
function scormcloud_get_user_data($uid) {
	global $CFG;

	if(scormcloud_write_log("UserAPI - UID=$uid")) {
    	//write to log...
    }

    return get_record('user','id',$uid,'','','','','*');
}

function scormcloud_get_course_fullname($courseid)
{
	if ( $course = get_record("course", "id", "$courseid")) {
		return $course->fullname;
	}else{
		scormcloud_write_log('ERROR : get_record("course", "id", "'.$courseid.'")');

	}
}

function scormcloud_grade_item_reset($regid)
{
	//echo 'resetting '.$regid;
	if ($reg = get_record("scormcloud_registrations", "regid", "$regid")) {
		//echo 'found reg '.$reg->scormcloudid;
		if($scormcloud = get_record("scormcloud", "id", "$reg->scormcloudid")){
			$courseid = $scormcloud->course;
			//echo '$courseid'.$courseid;
			if(scormcloud_grade_item_update($reg->userid,$courseid,'reset'))
			{
				echo 'reset complete';
			}
		}
	}else{
		scormcloud_write_log('ERROR : get_record("scormcloud_registrations", "regid", "'.$regid.'")');

	}
}
/**
 * Update/create grade item for user/ course
 *
 * @param string uid 
 * @param string pid 
 * @param string rawscore; 'reset' means reset grades in gradebook
 * @return object grade_item
 */
function scormcloud_grade_item_update($uid, $pid, $rawscore) {
    global $CFG;
    
    
    if (!function_exists('grade_update')) { //workaround for buggy PHP versions
        require_once($CFG->libdir.'/gradelib.php');
    }
    
    //if($rawscore<>'reset')
    //{
		//Set RawScore to proper format
	//	$rawscore = floatval($rawscore)*100;
    //}
    
    if(scormcloud_write_log("GradeAPI - UID=$uid - PID=$pid - RAWSCORE=$rawscore")) {
    	//write to log...
    }
    
	if($scormcloud = get_record("scormcloud", "id", "$pid")){
		$courseid = $scormcloud->course;

	    //Get the Moodle Course Name
	    $coursetitle = $scormcloud->name;
    
	    //$sql = "SELECT shortname FROM mdl_course WHERE id = $courseid";

	    //if ($titles = get_records_sql($sql)) {
	    //    foreach ($titles as $title) {
	    //        $coursetitle = $title->shortname;
	    //    }
	    //}
	
		if($rawscore=='reset')
		{
			$grades = 'reset';
		}else{

		//$grades = array();
		//$grades[$uid] = new object();
		//		$grades[$uid]->id         = $uid;
		//		$grades[$uid]->userid     = $uid;
		//		$grades[$uid]->rawgrade = $rawscore;
		$grades = array('userid'=>$uid, 'rawgrade'=>$rawscore);
		
	    $params = array('itemname'=>$coursetitle, 'idnumber'=>$scormcloud->id);


	        $params['gradetype'] = GRADE_TYPE_VALUE;
	        $params['grademax']  = 100;
	        $params['grademin']  = 0;
    
	    }

	    if ($grades  === 'reset') {
	        $params['reset'] = true;
	        $grades = NULL;
	    }
    
	    if(scormcloud_write_log("GradeAPI - UID=$uid - PID=$pid - COURSE=$courseid - RAWSCORE=$rawscore - CourseTitle=$coursetitle")) {
	    	//write to log...
	    }

	    return grade_update('mod/scormcloud', $courseid, 'mod', 'scormcloud', $pid, 0, $grades, $params);
	}else{
		return false;
	}
}

function scormcloud_add_instance($scormcloud) {
/// Given an object containing all the necessary data,
/// (defined by the form in mod.html) this function
/// will create a new instance and return the id number
/// of the new instance.
global $CFG;

//if(scormcloud_write_log("scormcloud_add_instance")) {
    	//write to log...
  // }
    //global $USER;
    
    //$scormcloud->name = "course anme";

    $scormcloud->timemodified = time();
    if($id = insert_record("scormcloud", $scormcloud)){
		
	}
	return $id;
}
/**
* Given an object containing all the necessary data,
* (defined by the form in mod.html) this function
* will update an existing instance with new data.
*
* @param mixed $scormcloud Form data
* @return int
*/
function scormcloud_update_instance($scormcloud) {
    global $CFG;
	$scormcloud->id = $scormcloud->instance;
    if ($result = update_record('scormcloud', $scormcloud)) {
    }

    return $result;
}
/**
* Given an ID of an instance of this module,
* this function will permanently delete the instance
* and any data that depends on it.
*
* @param int $id Scorm instance id
* @return boolean
*/
function scormcloud_delete_instance($id) {

    global $CFG;
    
    //call scormcloud to tell it to delete course $id
   	$ScormService = new ScormEngineService($CFG->scormcloud_serviceurl,$CFG->scormcloud_appid,$CFG->scormcloud_secretkey);
	$courseService = $ScormService->getCourseService();
	$cloudresult = $courseService->DeleteCourse($id); //delete all versions here
    
    if(scormcloud_write_log("scormcloud_delete_instance")) {
    	//write to log...
    }
    //error_log('delete_instance',0);
    if (! $scormcloud = get_record("scormcloud", "id", "$id")) {
      return false;
	}

  $result = true;

	



  # Delete any dependent records here #
    #lams_delete_lesson($USER->username,$lams->learning_session_id);
  if (! delete_records("scormcloud", "id", "$scormcloud->id")) {
     $result = false;
  }
  if (! delete_records("scormcloud_registrations", "scormcloudid", "$scormcloud->id")) {
     $result = false;
  }
  return $result;
}



function scormcloud_get_view_actions() {
    return array('pre-view','view','view all','report');
}

/**
 * Actual implementation of the rest coures functionality, delete all the
 * scorm attempts for course $data->courseid.
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function scormcloud_reset_userdata($data) {
    global $CFG;
    if(scormcloud_write_log("scormcloud_reset_userdata")) {
    	//write to log...
    }
}

/**
 * Write To Log File
 *
 * @param string $message test to write to log
 * 
 * @return bool success
 */
function scormcloud_write_log($message) {
    global $CFG;
	
	$fh = fopen($CFG->dataroot.'scormcloud_mod.log', 'a');
	
	fwrite($fh, '['.date("D dS M,Y h:i a").'] - '.$message."\n");
	
	fclose($fh);

	return true;

}


/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such 
 * as sending out mail, toggling flags etc ... 
 *
 * @uses $CFG
 * @return boolean
 * @todo Finish documenting this function
 **/
function scormcloud_cron () {
    global $CFG;

    return true;
}


/**
 * Execute post-install custom actions for the module
 * This function was added in 1.9
 *
 * @return boolean true if success, false on error
 */
function scormcloud_install() {
     return true;
}

/**
 * Execute post-uninstall custom actions for the module
 * This function was added in 1.9
 *
 * @return boolean true if success, false on error
 */
function scormcloud_uninstall() {
    return true;
}

//////////////////////////////////////////////////////////////////////////////////////
/// Any other scormcloud functions go here.  Each of them must have a name that 
/// starts with scormcloud_
/// Remember (see note in first lines) that, if this section grows, it's HIGHLY
/// recommended to move all funcions below to a new "localib.php" file.


?>
