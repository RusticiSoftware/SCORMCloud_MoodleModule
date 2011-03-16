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

//get the courseid and userid

//require login on this page

	$courseid = required_param('courseid', PARAM_INT);
	$userid = required_param('userid', PARAM_INT);
	
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
