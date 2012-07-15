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

    require_once('../../config.php');
	global $CFG;
	
	require_once("lib.php");
    require_once('locallib.php');

	require_once('SCORMAPI/ScormEngineService.php');
	require_once('SCORMAPI/ServiceRequest.php');
	require_once('SCORMAPI/CourseData.php');
	require_once('SCORMAPI/UploadService.php');

	$courseid = required_param('courseid', PARAM_RAW);
	$mode = optional_param('mode', PARAM_RAW);
	$location = required_param('location', PARAM_RAW);
	$success = required_param('success', PARAM_RAW);

    require_login();
    $context = get_context_instance(CONTEXT_COURSE, SITEID); //TODO. change to real courseid.
    if (!has_capability('moodle/course:manageactivities', $context)) {
        redirect($CFG->wwwroot);
    }
	error_log('Creating ScormService : '.$CFG->scormcloud_serviceurl.' - '.$CFG->scormcloud_appid.' - '.$CFG->scormcloud_secretkey);
	$ScormService = new ScormEngineService($CFG->scormcloud_serviceurl,$CFG->scormcloud_appid,$CFG->scormcloud_secretkey);
	$courseService = $ScormService->getCourseService();

	$id = str_replace('%7C','|',$courseid);
	$ids = explode('|',$id);
	$courseId = $ids[0];
	$scormcloudid = $ids[1];

	//echo $mode . '<br>';
	//echo $courseId . '<br>';
	//echo $scormcloudid . '<br>';
	//echo $location . '<br>';
	//echo $success . '<br>';
if($success=='true')
{
	echo '<h2>Importing Package...</h2>';

	if($mode == 'update')
	{
		//version the uploaded course
		$result = $courseService->VersionUploadedCourse($scormcloudid, $location, null);
		$importResult = new ImportResult($result);
		//add it to the scormcloud table too...
		$scormcloud = array('id' => $scormcloudid,
						 'course' => $courseId,
						 'name' => $importResult->getTitle(),
						 'timecreated' => time(),
						 'scoreformat' => '0');
		scormcloud_update_instance($scormcloud);
		
	}else{
	
		//import the uploaded course
		scormcloud_write_log('scormcloud_mod attempting to import to SCORMCloud...');
		$result = $courseService->ImportUploadedCourse($scormcloudid, $location, null);
		scormcloud_write_log('scormcloud_mod cloud import complete. parsing results...');
		scormcloud_write_log('result='.$result);
		$irxml = simplexml_load_string($result);
		//$importResults = ImportResult::ConvertToImportResults($irxml);
		scormcloud_write_log('importResult='.$irxml->importresult->title);
		//add it to the scormcloud table too...
		$scormcloud = array('id' => $scormcloudid,
						 'course' => $courseId,
						 'name' => $irxml->importresult->title,
						 'timecreated' => time(),
						 'scoreformat' => '0');
		scormcloud_update_instance($scormcloud);
		scormcloud_write_log('scormcloud_mod import complete');
	
	}	
	
	echo '<script>window.parent.location=window.parent.location;</script>';
}else{
	echo 'There was an error uploading your package. Please try again.';
	
}

?>