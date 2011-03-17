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