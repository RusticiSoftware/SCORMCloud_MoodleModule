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
    $ids = explode('|',$id);
    $courseId = $ids[0];
    $scormcloudid = $ids[1];
    require_login($courseId);
    $coursecontext = get_context_instance(CONTEXT_COURSE, $courseId);
    require_capability('moodle/course:manageactivities', $coursecontext);

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
							 'timecreated' => time(),
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
							 'timecreated' => time(),
							 'scoreformat' => '0');
			scormcloud_update_instance($scormcloud);
			scormcloud_write_log('scormcloud_mod import complete');
		}
		
		echo '<script>window.parent.location=window.parent.location;</script>';

  }

?>