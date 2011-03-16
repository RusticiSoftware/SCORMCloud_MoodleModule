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

$ScormService = new ScormEngineService($CFG->scormcloud_serviceurl,$CFG->scormcloud_appid,$CFG->scormcloud_secretkey);

$courseService = $ScormService->getCourseService();
//echo gettype($courseService);
$allResults = $courseService->GetCourseList();
echo '<div id="AdminHeader">';
echo '<h2>All Courses</h2>';
echo '</div>';
echo '<table border="0" cellpadding="5" id="CourseListTable" >';
echo '<tr><th style="text-align:center">Course Id</th><th>Title</th><th style="text-align:center">Versions</th><th style="text-align:center">Registrations</th><th></th></tr>';
foreach($allResults as $course)
{
	echo '<tr><td style="text-align:center">';
	echo $course->getCourseId();
	echo '</td><td>';
	echo $course->getTitle();
	echo '</td><td style="text-align:center">';
	echo $course->getNumberOfVersions();
	echo '</td><td style="text-align:center">';
	echo '<a href="cloudregistrations.php?courseid='.$course->getCourseId().'">'.$course->getNumberOfRegistrations().'</a>';
	echo '</td><td style="text-align:center">';
	echo '<a href="cloudcourseapi.php?action=deleteall&courseid='.$course->getCourseId().'">Delete All Versions</a>';
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