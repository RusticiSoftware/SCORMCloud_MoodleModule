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
<html>
<head>
<?php

require_once("../../config.php");
require_once("locallib.php");
require_once("lib.php");
require_once('SCORMAPI/ScormEngineService.php');
require_once('SCORMAPI/ServiceRequest.php');
require_once('SCORMAPI/CourseData.php');


global $CFG;

	$courseid = required_param('courseid', PARAM_INT);
    require_login();
	
    $ScormService = new ScormEngineService($CFG->scormcloud_serviceurl,$CFG->scormcloud_appid,$CFG->scormcloud_secretkey);
    error_log('creating registration cloud service');
    $courseService = $ScormService->getCourseService();

	echo get_string("launchmessage","scormcloud");
	echo '<script>window.open("'.$courseService->GetPreviewUrl($courseid, $CFG->wwwroot . '/mod/scormcloud/courseexit.php') . '",null,"width=1000,height=800");</script>';

	echo '<script>';
	echo 'function RollupRegistration(regid) {';
	echo PHP_EOL;
	//echo 'alert(window.frames[0].document.location.href);';
	echo 'window.frames[0].document.location.href = "rollupregistration.php?regid="+regid;';
	//echo 'alert(regid);';
	echo PHP_EOL;
	echo '}';
	echo PHP_EOL;
	echo 'setInterval("RollupRegistration(\"'.$regid.'\")",30000);';
	echo PHP_EOL;
	
	echo '</script>';

?>
</head>
<frameset onunload="" rows="*,0">
<frame id="rollupreg" src="blank.html" />
<frame id="blank" src="blank.html" />
</frameset>
</html>
