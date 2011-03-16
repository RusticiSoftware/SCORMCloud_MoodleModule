<?php
/*
==============================================================================
	
	Copyright (c) 2009 Rustici Software
	
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

==============================================================================
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
						 'timecreated' => date(),
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
						 'timecreated' => date(),
						 'scoreformat' => '0');
		scormcloud_update_instance($scormcloud);
		scormcloud_write_log('scormcloud_mod import complete');
	
	}	
	
	echo '<script>window.parent.location=window.parent.location;</script>';
}else{
	echo 'There was an error uploading your package. Please try again.';
	
}

?>