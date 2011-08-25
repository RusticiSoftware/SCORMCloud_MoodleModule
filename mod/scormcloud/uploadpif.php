<?php

/*
 *   Copyright 2011 Rustici Software.
 *
 *   This file is part of the SCORM Cloud Module for Moodle.
 *   https://github.com/RusticiSoftware/SCORMCloud_MoodleModule
 *   http://scorm.com/moodle/
 *
 *   The SCORM Cloud Module is free software: you can redistribute it and/or
 *   modify it under the terms of the GNU General Public License as published
 *   by the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   The SCORM Cloud Module is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with the SCORM Cloud Module.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once("../../config.php");

global $CFG;

require_once('locallib.php');

require_once('SCORMCloud_PHPLibrary/ScormEngineService.php');
require_once('SCORMCloud_PHPLibrary/ServiceRequest.php');
require_once('SCORMCloud_PHPLibrary/CourseData.php');
require_once('SCORMCloud_PHPLibrary/UploadService.php');

$id = required_param('id', PARAM_RAW);
$mode = optional_param('mode', null, PARAM_RAW);
if($mode == null)
{
	$mode = 'new';
}

$id = str_replace('%7C','|',$id);

$ScormService = scormcloud_get_service();
$uploadService = $ScormService->getUploadService();

echo '<html>';
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
