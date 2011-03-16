<?php

/*
==============================================================================
	
	Copyright (c) 2009 Rustici Software
	
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

==============================================================================
*/

require_once("../../config.php");
global $CFG;

require_once($CFG->dirroot.'/lib/datalib.php');
require_once($CFG->dirroot.'/lib/accesslib.php');
require_once("lib.php");
require_once('SCORMAPI/ScormEngineService.php');
require_once('SCORMAPI/ServiceRequest.php');
require_once('SCORMAPI/CourseData.php');

$regid = required_param("regid");

//scormcloud_write_log($regxml);
if($regid != null){
	scormcloud_write_log('rollupregistration.php called : regid = ' . $regid);

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
				}
			
			scormcloud_write_log('attempting to update local scormcloud_registrations table');
			
			//repopulate the $reg we got above to update it here...
			$reg->completion = $results->registrationreport->complete;
			$reg->satisfaction = $results->registrationreport->success;
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
}

?>