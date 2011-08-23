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

require_once('../../config.php');
global $CFG;

require_once("lib.php");
require_once('locallib.php');

require_once('SCORMCloud_PHPLibrary/ScormEngineService.php');
require_once('SCORMCloud_PHPLibrary/ServiceRequest.php');
require_once('SCORMCloud_PHPLibrary/CourseData.php');
require_once('SCORMCloud_PHPLibrary/UploadService.php');

global $log;

$courseid = required_param('courseid', PARAM_RAW);
$mode = optional_param('mode', 'import', PARAM_RAW);
$location = required_param('location', PARAM_RAW);
$success = required_param('success', PARAM_RAW);

$log->logInfo('Creating ScormService : '.$CFG->scormcloud_serviceurl.' - '.$CFG->scormcloud_appid.' - '.$CFG->scormcloud_secretkey);
$ScormService = new ScormEngineService($CFG->scormcloud_serviceurl,$CFG->scormcloud_appid,$CFG->scormcloud_secretkey);
$courseService = $ScormService->getCourseService();

$id = str_replace('%7C','|',$courseid);
$ids = explode('|',$id);
$courseId = $ids[0];
$scormcloudid = $ids[1];

if ($success == 'true')
{
	echo '<h2>Importing Package...</h2>';

	if ($mode == 'update')
	{
		$result = $courseService->UpdateAssetsFromUploadedFile($scormcloudid, $location);
		$log->logInfo('UpdateAssets complete. Result = '.$result);
		echo $result;
	} else {
		$log->logInfo('scormcloud_mod attempting to import to SCORMCloud...');
		$results = $courseService->ImportUploadedCourse($scormcloudid, $location, null);
		$log->logInfo('scormcloud_mod cloud import complete. parsing results...');
		$log->logInfo(count($results).' results.');
		
		$result = current($results);
		if (!$result->getWasSuccessful()) {
			error($result->getMessage());
		}
		
		$log->logInfo('importResult='.$result->getTitle());
		$scormcloud = new stdClass();
		$scormcloud->id = $scormcloudid;
		$scormcloud->course = $courseId;
		$scormcloud->name = $result->getTitle();
		$scormcloud->timecreated = time();
		$scormcloud->scoreformat = '0';
		
		scormcloud_update_instance($scormcloud);
		$log->logInfo('scormcloud_mod import complete');
	}

	echo '<script>window.location.reload();</script>';
} else {
	echo 'There was an error uploading your package. Please try again.';

}

?>