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