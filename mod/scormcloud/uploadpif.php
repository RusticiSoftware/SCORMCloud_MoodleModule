<?php

	require_once("../../config.php");
	
	global $CFG;

	require_once('SCORMAPI/ScormEngineService.php');
	require_once('SCORMAPI/ServiceRequest.php');
	require_once('SCORMAPI/CourseData.php');
	require_once('SCORMAPI/UploadService.php');

	$id = required_param('id', PARAM_RAW);
	$mode = optional_param('mode', PARAM_RAW);
	if($mode == null)
	{
		$mode = 'new';
	}
	
	$id = str_replace('%7C','|',$id);

	$ScormService = new ScormEngineService($CFG->scormcloud_serviceurl,$CFG->scormcloud_appid,$CFG->scormcloud_secretkey);
	$uploadService = $ScormService->getUploadService();

	//echo $uploadService->GetUploadLink($CFG->wwwroot.'/mod/scormcloud/importcallback.php?courseid='.$id);

	echo '<br><br>';
	echo '<table style="width:100%">';
	echo '<tr><td style="text-align:center">';
	if($mode == 'new' || $mode == null)
	{
		echo '<h2>There is no SCORM package for this course yet. <br>Please upload one to get started.</h2>';
	}else{
		echo '<h2>Select a package for this updated version.</h2>';
	}
	echo '</td></tr>';
	echo '<tr><td style="text-align:center">';
	echo '<form id="uploadform" action="'.$uploadService->GetUploadLink($CFG->wwwroot.'/mod/scormcloud/importcallback.php?courseid='.$id.'&mode='.$mode).'" method="post" ';
	echo 'enctype="multipart/form-data">';
	echo '<label for="file">Filename:</label>';
	echo '<input type="file" name="filedata" id="file" /> ';
	echo '<input type="submit" id="submit" name="submit" value="Submit" />';
	echo '</form>';
	echo '</td></tr>';
	echo '</table>';
	
	echo '<script type="text/javascript" ';
    echo "src=\"{$CFG->wwwroot}/mod/scormcloud/scripts/jquery-1.3.2.min.js\"></script>\n";
	echo '<script type="text/javascript" >';
	echo '$("#uploadform").submit(function(){';
	echo '$("input[type=submit]", this).attr("disabled", "disabled");';
	echo '});';
	echo '</script>';
?>