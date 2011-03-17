<?php

/* Software License Agreement (BSD License)
 * 
 * Copyright (c) 2010-2011, Rustici Software, LLC
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the <organization> nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL Rustici Software, LLC BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

?>
<?php

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