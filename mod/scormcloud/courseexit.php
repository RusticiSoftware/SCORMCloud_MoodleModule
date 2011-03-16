<?php

global $CFG;

require_once("../../config.php");
require_once($CFG->dirroot.'/lib/datalib.php');
require_once($CFG->dirroot.'/lib/accesslib.php');
require_once("lib.php");
require_once('SCORMAPI/ScormEngineService.php');
require_once('SCORMAPI/ServiceRequest.php');
require_once('SCORMAPI/CourseData.php');

$regid = optional_param("id");

//require login on this page? not much could happen on this page if not...

//scormcloud_write_log($regxml);
if($regid != null){
	scormcloud_write_log('courseexit called : regid = ' . $regid);

	if ($reg = get_record("scormcloud_registrations", "regid", "$regid")) {
		scormcloud_write_log('Found scormcloud_registration '.$regid);
		if($scormcloud = get_record("scormcloud", "id", "$reg->scormcloudid")){
			scormcloud_write_log('Found scormcloud id '.$reg->scormcloudid);
			$courseid = $scormcloud->course;
			//echo $courseid;
			//Get the results from the cloud
			$ScormService = new ScormEngineService($CFG->scormcloud_serviceurl,$CFG->scormcloud_appid,$CFG->scormcloud_secretkey);
			$regService = $ScormService->getRegistrationService();
			$resultsxml = $regService->GetRegistrationResult($regid, 0, 'xml');
			$results = simplexml_load_string($resultsxml);

			scormcloud_write_log('updating Moodle gradebook '.$reg->userid.' - '.$courseid.' - '.$results->registrationreport->score);
			
			if(scormcloud_grade_item_update($reg->userid,$reg->scormcloudid,$results->registrationreport->score))
			{
				//success
				scormcloud_write_log('moodle gradebook was updated');
			}else{
				scormcloud_write_log('there was an error updating the moodle gradebook - trying again');
				if(scormcloud_grade_item_update($reg->userid,$reg->scormcloudid,$results->registrationreport->score))
				{
					//success
					scormcloud_write_log('moodle gradebook was updated this time');
				}else{
					scormcloud_write_log('there was an error updating the moodle gradebook again...');
				}
			}
			
			scormcloud_write_log('attempting to update local scormcloud_registrations table');
			
			//repopulate the $reg we got above to update it here...
			scormcloud_write_log('$results->registrationreport->complete='.$results->registrationreport->complete);
			switch($results->registrationreport->complete)
			{
				case 'complete':
					$completion = 1;
					break;
				case 'incomplete':
					$completion = 2;
					break;
				default:
					$completion = 0;
					break;
			}
			$reg->completion = $completion;
			
			scormcloud_write_log('$results->registrationreport->success='.$results->registrationreport->success);
			switch($results->registrationreport->success)
			{
				case 'passed':
					$success = 1;
					break;
				case 'failed':
					$success = 2;
					break;
				default:
					$success = 0;
					break;
			}
			$reg->satisfaction = $success;
			
			$reg->totaltime = $results->registrationreport->totaltime;
			$reg->score = $results->registrationreport->score;
			if ($result = update_record('scormcloud_registrations', $reg))
			{
				scormcloud_write_log('scormcloud_registrations updated');
			}else{
				scormcloud_write_log('error updating scormcloud_registrations');
			}
		}
	}else{
		scormcloud_write_log('ERROR : get_record("scormcloud_registrations", "regid", "'.$regid.'")');
	}
	echo '<h2>Saving results...</h2>';
	echo '<script>setTimeout("window.opener.parent.location = window.opener.parent.location;window.close();",5000);</script>';
} else {
	echo '<script>window.opener.parent.location = window.opener.parent.location;window.close()</script>';
}
?>
