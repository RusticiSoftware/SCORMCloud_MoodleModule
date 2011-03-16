<?php

require_once("../../config.php");
require_once('SCORMAPI/ScormEngineService.php');
require_once('SCORMAPI/ServiceRequest.php');
require_once('SCORMAPI/CourseData.php');


global $CFG;

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

echo '<html>';
echo '<head>';
echo '<LINK href="scormcloud.css" rel="stylesheet" type="text/css">';
echo '</head>';
echo '<body>';

echo '<div class="scormcloud-admin-page">';


if ($isAdmin) {

$courseid = $_GET['courseid'];

$ScormService = new ScormEngineService($CFG->scormcloud_serviceurl,$CFG->scormcloud_appid,$CFG->scormcloud_secretkey);

$regService = $ScormService->getRegistrationService();

if(isset($courseid)){
	$allResults = $regService->GetRegistrationList(null,$courseid);
	$courseService = $ScormService->getCourseService();
	$allCourses = $courseService->GetCourseList();
	$courseTitle = "";
	foreach($allCourses as $course){
		if($course->getCourseId() == $courseid)
		{
			$courseTitle = $course->getTitle();
		}
	}
	
}else{
	$allResults = $regService->GetRegistrationList(null,null);
}

echo '<div id="AdminHeader">';
echo '<div style="float:left"><h2>All Registrations for '.$courseTitle.'</h2></div><div style="float:right"><a href="cloudcourses.php" >Return to Course List</a></div>';
echo '</div>';
echo '<br>';

echo '<table border="0" cellpadding="5" id="RegistrationListTable" >';
echo '<tr><td>Registration Id</td><td>Course Id</td><td>completion</td><td>success</td><td>total time</td><td>score</td></tr>';
foreach($allResults as $result)
{
	echo '<tr><td>';
	//echo '<a href="'.$regService->GetLaunchUrl($result->getRegistrationId(), null).'" target="_blank" >launch</a>';
	//echo '</td><td>';
	echo '<a href="cloudregdetail.php?regid='.$result->getRegistrationId().'">'.$result->getRegistrationId().'</a>';
	echo '</td><td>';
	echo $result->getCourseId();
	echo '</td><td>';
	$regResults = $regService->GetRegistrationResult($result->getRegistrationId(),0,'xml');
	//echo $regResults;
	$xmlResults = simplexml_load_string($regResults);
	echo $xmlResults->registrationreport->complete;
	echo '</td><td>';
	echo $xmlResults->registrationreport->success;
	echo '</td><td>';
	echo $xmlResults->registrationreport->totaltime;
	echo '</td><td>';
	echo $xmlResults->registrationreport->score;
	echo '</td></tr>';
}
echo '</table>';
}else{
	
	echo 'You do not have access to this page. Please contact your system administrator for assistance.';
}


echo '</div>';
echo '</body>';
echo '</html>';

?>