<?php
require_once("../../config.php");
require_once('SCORMAPI/ScormEngineService.php');
require_once('SCORMAPI/ServiceRequest.php');
require_once('SCORMAPI/CourseData.php');


global $CFG;

$id = required_param('id', PARAM_INT);   // course id

$ScormService = new ScormEngineService($CFG->scormcloud_serviceurl,$CFG->scormcloud_appid,$CFG->scormcloud_secretkey);
$courseService = $ScormService->getCourseService();
//echo $courseService->GetPropertyEditorUrl($id, null, null);
$cssurl = $CFG->wwwroot . '/mod/scormcloud/packageprops.css';
//echo $cssurl;
echo '<script language="javascript">window.location.href = "'.$courseService->GetPropertyEditorUrl($id, $cssurl, null).'";</script>';

?>