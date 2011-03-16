<?php

require_once("../../config.php");
require_once('SCORMAPI/ScormEngineService.php');
require_once('SCORMAPI/ServiceRequest.php');
require_once('SCORMAPI/CourseData.php');


global $CFG;

$action = $_GET['action'];
$courseid = $_GET['courseid'];

//is current user an admin?
$isAdmin = false;
if(user_has_role_assignment($USER->id,1))
{
   	$isAdmin = true;
}
if(user_has_role_assignment($USER->id,2))
{
   	$isAdmin = true;
}
if(user_has_role_assignment($USER->id,3))
{
   	$isAdmin = true;
}

if ($isAdmin) {

	$ScormService = new ScormEngineService($CFG->scormcloud_serviceurl,$CFG->scormcloud_appid,$CFG->scormcloud_secretkey);
	
	if(isset($action) && isset($courseid))
	{
		switch($action)
		{
			case 'deleteall':
				$courseService = $ScormService->getCourseService();
				$courseService->DeleteCourse($courseid, 'false');
				echo '<div class="alert">All versions of course '.$courseid.' deleted.</div><br><br>';
				break;
			default:
				break;
		}
		
	}
	
	echo '<a href="cloudcourses.php">Back to Course List</a>' ;

}

?>