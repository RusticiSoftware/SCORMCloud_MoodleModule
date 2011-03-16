<?php
require_once("../../config.php");
require_once('SCORMAPI/ScormEngineService.php');
require_once('SCORMAPI/ServiceRequest.php');
require_once('SCORMAPI/CourseData.php');
require_once('lib.php');
require_once('locallib.php');


global $CFG;

$id = required_param('id', PARAM_RAW);
$mode = required_param('mode', PARAM_RAW);

$ScormService = new ScormEngineService($CFG->scormcloud_serviceurl,$CFG->scormcloud_appid,$CFG->scormcloud_secretkey);
$regService = $ScormService->getRegistrationService();

//notify($mode);
switch($mode)
{
	case "reset":
		$regService->ResetRegistration($id);
		//reset gradebook too...
		scormcloud_grade_item_reset($id);
		break;
	case "delete":
		$regService->DeleteRegistration($id,'false');
		delete_records("scormcloud_registrations", "regid", "$id");
		break;
	default:
		break;
}

echo '<script>self.parent.location=self.parent.location;self.parent.tb_remove();</script>';

?>
<html>
<head> 


	</head>
<body>
Registration resetting...
</body>

</html>