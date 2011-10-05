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

require_once("lib.php");
require_once('locallib.php');

require_once('SCORMCloud_PHPLibrary/ScormEngineService.php');
require_once('SCORMCloud_PHPLibrary/ServiceRequest.php');
require_once('SCORMCloud_PHPLibrary/CourseData.php');
require_once('SCORMCloud_PHPLibrary/UploadService.php');

global $log;

$mode = optional_param('mode', 'import', PARAM_ALPHA);
$courseId = required_param('moodleid', PARAM_INT);
$scormcloudid = required_param('courseid', PARAM_INT);
$title = optional_param('title', '', PARAM_RAW);
$success = required_param('success', PARAM_ALPHA);

$log->logInfo('Creating ScormService : '.$CFG->scormcloud_serviceurl.' - '.$CFG->scormcloud_appid.' - '.$CFG->scormcloud_secretkey);
$ScormService = scormcloud_get_service();
$courseService = $ScormService->getCourseService();


if ($success == 'true')
{
	$log->logDebug('Import successful.');
	
	if ($mode == 'new') {
		$log->logDebug('Not an update, initializing Moodle scormcloud instance.');
		
		$scormcloud = new stdClass();
		$scormcloud->id = $scormcloudid;
		$scormcloud->course = $courseId;
		$scormcloud->name = $title;
		$scormcloud->timecreated = time();
		$scormcloud->scoreformat = '0';
		
		scormcloud_update_instance($scormcloud);
	}
	
	echo '<script>self.parent.location.reload();</script>';
	
} else {
	echo 'There was an error uploading your package. Please try again.';

}

?>