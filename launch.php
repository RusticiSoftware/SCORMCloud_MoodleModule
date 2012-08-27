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
require_once("locallib.php");
require_once("lib.php");
require_once('SCORMCloud_PHPLibrary/ScormEngineService.php');
require_once('SCORMCloud_PHPLibrary/ServiceRequest.php');
require_once('SCORMCloud_PHPLibrary/CourseData.php');

global $CFG;
global $DB;

$id = required_param('courseid', PARAM_INT); // This is not the Moodle courseid it's the scormcloud->id.
$mode = optional_param('mode', 'launch', PARAM_ALPHA);

$userid = $USER->id;
$regid = '';

$scormcloud = $DB->get_record('scormcloud', array('id' => $id));

require_login($scormcloud->course);
if (!scormcloud_hascapabilitytolaunch($scormcloud->course)) {
    error("You do not have permission to launch this course.");
}
$PAGE->requires->js('/mod/scormcloud/request.js', true);

    $ScormService = scormcloud_get_service();
$regService = $ScormService->getRegistrationService();

$log->logDebug('Checking for Moodle registration.');
// Check to see if there is an initial registration
if (!$regs = $DB->get_records_select('scormcloud_registrations','userid='.$userid.' AND scormcloudid='.$id)) {
	$log->logInfo("Registration does not exist in Moodle for course $scormcloud->course and scormcloudcourse $id and user $userid; creating.");

	$regid = md5(uniqid());
	$reg = array();
	$reg['scormcloudid'] = $id;
	$reg['userid'] = $userid;
	$reg['regid'] = $regid;
	$reg['lastaccess'] = time();
		
	$DB->insert_record('scormcloud_registrations', $reg, $returnid=true, $primarykey='id') ;
	$log->logInfo("Moodle registration $regid created.");
		
	$user = scormcloud_get_user_data($userid);

	$learnerId = $user->username ;
	$learnerFirstName = $user->firstname ;
	$learnerLastName = $user->lastname ;
	
	$regService->CreateRegistration($regid, $scormcloud->cloudid, $learnerId, $learnerFirstName, $learnerLastName);
	$log->logInfo("Cloud registration created for Moodle registration $regid.");
} else {
	$regid = current($regs)->regid;
	$log->logInfo("Moodle registration exists, using registration $regid.");
}

$url = '';
if ($mode == 'preview') {
	$courseService = $ScormService->getCourseService();
	$url = $courseService->GetPreviewUrl($scormcloud->cloudid, $CFG->wwwroot . '/mod/scormcloud/courseexit.php');
} else {
	$url = $regService->GetLaunchUrl($regid, $CFG->wwwroot . '/mod/scormcloud/courseexit.php?id=' . $regid);
}
$PAGE->set_pagelayout('popup');
echo $OUTPUT->header();

// TODO: ideally this stuff should use js as per standard moodle guidelines.
echo '<script>window.open("'. $url .'",null,"width=1000,height=800");</script>';
echo '<script>setInterval("RollupRegistration(\"'.$regid.'\")",30000);</script>';
echo '<p>'.get_string("launchmessage","scormcloud").'</p>';
echo $OUTPUT->footer();
