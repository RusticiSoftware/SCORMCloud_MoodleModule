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