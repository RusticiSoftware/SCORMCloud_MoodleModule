<html>
<head>
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
require_once('constants.php');
require_once("locallib.php");
require_once("lib.php");
require_once('SCORMCloud_PHPLibrary/ScormEngineService.php');
require_once('SCORMCloud_PHPLibrary/ServiceRequest.php');
require_once('SCORMCloud_PHPLibrary/CourseData.php');

global $CFG;
global $DB;

$courseid = required_param('courseid', PARAM_INT);
$mode = optional_param('mode', 'launch', PARAM_ALPHA);

$userid = $USER->id;
$regid = '';

require_login($courseid);
if (!scormcloud_hascapabilitytolaunch($courseid)) {
    error("You do not have permission to launch this course.");
}

$ScormService = scormcloud_get_service();
$regService = $ScormService->getRegistrationService();

$cm = scormcloud_get_coursemodule($courseid);
$cmid = $cm->instance;
$scormcloud = $DB->get_record(SCORMCLOUD_TABLE, array('id' => $cmid));

$log->logDebug('Checking for Moodle registration.');
// Check to see if there is an initial registration
if (!$regs = $DB->get_records_select('scormcloud_registrations','userid='.$userid.' AND scormcloudid='.$cmid)) {
	$log->logInfo("Registration does not exist in Moodle for course $courseid and user $userid; creating.");

	$regid = md5(uniqid());
	$reg = array();
	$reg['scormcloudid'] = $cmid;
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

echo get_string("launchmessage","scormcloud");

$url = '';
if ($mode == 'preview') {
	$courseService = $ScormService->getCourseService();
	$url = $courseService->GetPreviewUrl($scormcloud->cloudid, $CFG->wwwroot . '/mod/scormcloud/courseexit.php');
} else {
	$url = $regService->GetLaunchUrl($regid, $CFG->wwwroot . '/mod/scormcloud/courseexit.php?id=' . $regid);
}

echo '<script>window.open("'. $url .'",null,"width=1000,height=800");</script>';

echo '<script>';
echo 'function RollupRegistration(regid) {';
echo PHP_EOL;
echo 'window.frames[0].document.location.href = "rollupregistration.php?regid="+regid;';
echo PHP_EOL;
echo '}';
echo PHP_EOL;
echo 'setInterval("RollupRegistration(\"'.$regid.'\")",30000);';
echo PHP_EOL;
echo '</script>';

?>
</head>
<frameset onunload="" rows="*,0">
	<frame id="rollupreg" src="blank.html" />
	<frame id="blank" src="blank.html" />
</frameset>
</html>
