<?php

/*
 *   Copyright 2011 Rustici Software.
 *
 *   This file is part of the SCORM Cloud Module for Moodle.
 *   https://github.com/RusticiSoftware/SCORMCloud_MoodleModule
 *   http://scorm.com/moodle/
 *
 *   The SCORM Cloud Module is free software: you can redistribute it and/or
 *   modify it under the terms of the GNU General Public License as published
 *   by the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   The SCORM Cloud Module is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with the SCORM Cloud Module.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once("../../config.php");
require_once($CFG->dirroot.'/lib/datalib.php');
require_once($CFG->dirroot.'/lib/accesslib.php');
require_once("locallib.php");
require_once('SCORMCloud_PHPLibrary/ScormEngineService.php');
require_once('SCORMCloud_PHPLibrary/ServiceRequest.php');
require_once('SCORMCloud_PHPLibrary/CourseData.php');

global $log;

$regid = optional_param("id", null, PARAM_RAW);

if ($regid != null) {
	$log->logDebug('In courseexit.php, received regid ' . $regid);

	if ($reg = $DB->get_record("scormcloud_registrations", array('regid' => $regid))) {
		$log->logDebug('Found scormcloud_registration for regid ' . $regid);
		if ($scormcloud = $DB->get_record("scormcloud", array('id' => $reg->scormcloudid))) {
			$log->logDebug('Found scormcloud record for id ' . $reg->scormcloudid);
			$courseid = $scormcloud->course;
			
			// Get the results from the cloud
			$ScormService = scormcloud_get_service();
			$regService = $ScormService->getRegistrationService();
			$resultsxml = $regService->GetRegistrationResult($regid, 0, 'xml');
			$results = simplexml_load_string($resultsxml);
			
			$score = $results->registrationreport->score;
			if ($score == 'unknown') {
				$score = 0;
			}

			$log->logDebug('Updating Moodle gradebook ' . $reg->userid . ' - ' . $courseid . ' - ' . $score);
				
			if (scormcloud_grade_item_update($reg->userid, $reg->scormcloudid, $score)) {
				$log->$logInfo('Updated Moodle gradebook for course ' . $courseid);
			} else {
				$log->logWarn('Error updating Moodle gradebook for course ' . $courseid . '. Retrying.');
				if (scormcloud_grade_item_update($reg->userid, $reg->scormcloudid, $score)) {
					$log->logInfo('Updated Moodle gradebook for course ' . $courseid . ' after successful retry.');
				} else {
					$log->logError('Second attempt to update Moodle gradebook for course ' . $courseid . ' failed.');
				}
			}
				
			$log->logDebug('Updating local scormcloud_registrations.');
				
			$log->logDebug('registrationreport->complete = ' . $results->registrationreport->complete);
			switch ($results->registrationreport->complete)
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
				
			$log->logDebug('registrationreport->sucess = ' . $results->registrationreport->success);
			switch ($results->registrationreport->success)
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
			$reg->totaltime = (int)$results->registrationreport->totaltime;
			$reg->score = (int)$results->registrationreport->score;
			
			if ($result = $DB->update_record('scormcloud_registrations', $reg)) {
				$log->logInfo('Updated scormcloud_registration.');
			} else {
				$log->logError('Failed to update scormcloud_registration.');
			}
		}
	} else {
		$log->logError('Failed to retrieve scormcloud_registration for regid ' . $regid . '.');
	}
	
	echo '<h2>Saving results...</h2>';
	echo '<script>setTimeout("window.opener.parent.location = window.opener.parent.location;window.close();", 5000);</script>';
} else {
	echo '<h2>Please wait while the course exists</h2>';
	echo '<script>setInterval("window.opener.parent.location = window.opener.parent.location;window.close();", 500);</script>';
}
?>
