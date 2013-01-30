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
require_once('SCORMAPI/ScormEngineService.php');
require_once('SCORMAPI/ServiceRequest.php');
require_once('SCORMAPI/CourseData.php');

require_login();

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

$courseid = optional_param('courseid', 0, PARAM_INT);

$ScormService = new ScormEngineService($CFG->scormcloud_serviceurl,$CFG->scormcloud_appid,$CFG->scormcloud_secretkey);

$regService = $ScormService->getRegistrationService();

if(!empty($courseid)){
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
    $courseTitle = 'all courses';
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