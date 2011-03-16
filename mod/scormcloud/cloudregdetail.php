<?php


require_once("../../config.php");
require_once('SCORMAPI/ScormEngineService.php');
require_once('SCORMAPI/ServiceRequest.php');
require_once('SCORMAPI/CourseData.php');
require_once('SCORMAPI/ServiceRequest.php');

global $CFG;

//is current user an admin?
$isAdmin = false;
if(user_has_role_assignment($USER->id,1))
{
   	$isAdmin = true;
}
if(user_has_role_assignment($USER->id,2))
{
   	$isAdmin = true;
}
if(user_has_role_assignment($USER->id,3))
{
   	$isAdmin = true;
}

echo '<html>';
echo '<head>';
echo '<LINK href="scormcloud.css" rel="stylesheet" type="text/css">';
echo '<script type="text/javascript" ';
echo "src=\"{$CFG->wwwroot}/mod/scormcloud/scripts/jquery-1.3.2.min.js\"></script>\n";
echo '<script type="text/javascript" ';
echo "src=\"{$CFG->wwwroot}/mod/scormcloud/scripts/jquery.xmldom-1.0.min.js\"></script>\n";
echo '<script type="text/javascript" ';
echo "src=\"{$CFG->wwwroot}/mod/scormcloud/scripts/rustici.webcontrols.activityreport.js\"></script>\n";
echo '</head>';
echo '<body>';

echo '<div class="scormcloud-admin-page">';


if ($isAdmin) {

	$regid = $_GET['regid'];

	//echo $regid;

	$ScormService = new ScormEngineService($CFG->scormcloud_serviceurl,$CFG->scormcloud_appid,$CFG->scormcloud_secretkey);

	$regService = $ScormService->getRegistrationService();

	if(isset($regid)){
		$regResults = $regService->GetRegistrationResult($regid,'2','xml');
		//$dataUrl = $regService->GetRegistrationResultUrl($regid,'2','0');
		echo '<div id="regResults">'.$regResults.'</div>';
		//echo "<script>BuildRegistrationDetail('".$regResults."');</script>";
		
	}else{
		//$allResults = $regService->GetRegistrationList(null,null);
	}

	echo '<div id="AdminHeader">';
	echo '<div style="float:left"><h2>Registration Detail</h2></div><div style="float:right"><a href="cloudregistrations.php" >Return to Registration List</a></div>';
	echo '</div>';
	echo '<br><br><br><br><br>';
	echo '<div id="report"/>';
}
echo '</div>';
echo '</body>';
echo '</html>';

?>

	<script type="text/javascript" >

	$(document).ready(
	        function() {
				//alert('here');
	            //$.ajax({
	            //    	url: "<?php echo $dataUrl ?>", 
	            //    	dataType: "xml",
				//		type: "GET",
				//	
		        //    	success: function(xml, status){
				//			alert(xml);
				//		}
				//});
				
				BuildActivityReport($.xmlDOM('<?php echo $regResults ?>'));
	    });
	



	</script>


	<style type="text/css">

	    /* CSS Values for the Report */
	    .activityTitle { color: blue; font-size: 110% }
	    .dataValue {font-weight: bold }
	    #report li {list-style: none; padding: 1px }
	    #report ul { margin-top: 0; margin-bottom: 0px; font-size: 10pt; }

	</style>

	</head>
	<body>
	    
	</body>
	</html>