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


	$id = required_param('id', PARAM_RAW);
	$mode = optional_param('mode', PARAM_RAW);
	

if ($_FILES["file"]["error"] > 0)
  {
	echo "Error: " . $_FILES["file"]["error"];
  error_log("Error: " . $_FILES["file"]["error"]);
  }
else
  {
	scormcloud_write_log('Creating ScormService : '.$CFG->scormcloud_serviceurl.' - '.$CFG->scormcloud_appid.' - '.$CFG->scormcloud_secretkey);
	$ScormService = new ScormEngineService($CFG->scormcloud_serviceurl,$CFG->scormcloud_appid,$CFG->scormcloud_secretkey);
	$courseService = $ScormService->getCourseService();
	$uploadService = $ScormService->getUploadService();
	
	$ids = explode('|',$id);
	$courseId = $ids[0];
	$scormcloudid = $ids[1];

		$target_path = $_FILES["file"]["tmp_name"] . '.zip'; 
		//echo $target_path;
		$tempFile = $_FILES["file"]["tmp_name"];
		scormcloud_write_log('tempFile : '.$tempFile);
		move_uploaded_file($_FILES['file']['tmp_name'], $target_path);
		$absoluteFilePathToZip = $target_path;
		scormcloud_write_log('absoluteFilePathToZip : '.$absoluteFilePathToZip);
		//now upload the file and save the resulting location
		scormcloud_write_log('scormcloud_mod attempting to upload to SCORMCloud...');
		$location = $uploadService->UploadFile($absoluteFilePathToZip,null);
		scormcloud_write_log('scormcloud_mod finished uploading to SCORMCloud...');
		
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
			$importResult = new ImportResult($result);
			//add it to the scormcloud table too...
			$scormcloud = array('id' => $scormcloudid,
							 'course' => $courseId,
							 'name' => $importResult->getTitle(),
							 'timecreated' => date(),
							 'scoreformat' => '0');
			scormcloud_update_instance($scormcloud);
			scormcloud_write_log('scormcloud_mod import complete');
		}
		
		echo '<script>window.parent.location=window.parent.location;</script>';

  }

?>