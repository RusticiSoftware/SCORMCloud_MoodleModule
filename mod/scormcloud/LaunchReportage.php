<?php

require_once('../../config.php');
global $CFG;

require_once("lib.php");
require_once('locallib.php');

require_once('SCORMAPI/ScormEngineService.php');
require_once('SCORMAPI/ServiceRequest.php');
require_once('SCORMAPI/CourseData.php');
require_once('SCORMAPI/ServiceRequest.php');
require_once('SCORMAPI/ReportingService.php');

$courseid = required_param('courseid', PARAM_RAW);

$ScormService = new ScormEngineService($CFG->scormcloud_serviceurl,$CFG->scormcloud_appid,$CFG->scormcloud_secretkey);
$rptService = $ScormService->getReportingService();
//echo '<script type="text/javascript">window.location = "'.$rptService->LaunchCourseReport($courseid).'";</script>';
echo '<script type="text/javascript">window.location = "'.$rptService->LaunchReportage().'";</script>';

//echo $rptService->LaunchCourseReport($courseid);
//<iframe src='http://dev.cloud.scorm.com/Reportage/scormreports/widgets/summary/SummaryWidget.php?standalone=true&showTitle=true&srt=allLearnersAllCourses&appId=brian&linkPage=reportage.php%3F&standalone=true&public=true&pubNavPermission=NONAV' frameborder='0' width='1080' height='480'></iframe>

//http://dev.cloud.scorm.com/Reportage/reportage.php?appId=brian&public=true&pubNavPermission=NONAV
?>