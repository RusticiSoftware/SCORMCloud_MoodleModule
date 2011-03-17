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
    require_once('../../../config.php');
	global $CFG;
	
	require_once("../lib.php");
    require_once('../locallib.php');

	require_once('../SCORMAPI/ScormEngineService.php');
	require_once('../SCORMAPI/ServiceRequest.php');
	require_once('../SCORMAPI/CourseData.php');
	require_once('../SCORMAPI/ServiceRequest.php');

//This page needs to check for login and check permissions

//require_login($course->id, false, $cm);

//if (has_capability('moodle/course:manageactivities', $context)) 
//{
		$regid = required_param('regid', PARAM_RAW);
		$htmlHead = '
		<script type="text/javascript" src="../scripts/jquery-1.3.2.min.js"></script>
		<script type="text/javascript">
		    $(document).ready(function(){

		    });
		    </script>
		<style>

		#column_headers {position:relative; font-weight:bold; border-bottom:1px solid #4171B5; padding: 3px 0px; margin-top:10px;}
		.headertitle {position:relative; width:300px; font-size:medium;}
		.headersatisfied {position:absolute; top:3px; left:350px; font-size:medium;}
		.headercompleted {position:absolute; top:3px; left:450px; font-size:medium;}
		.headerattempts {position:absolute; top:3px; left:550px; font-size:medium;}
		.headersuspended {position:absolute; top:3px; left:625px; font-size:medium;}

		.activityReportHeader {font-size:1.2em; position:relative;}
		.launchHistoryLink {position:absolute; right:25px; top:0px;}
		.launchHistoryLink img {margin-left:10px; vertical-align:top;}

		.activity{position:relative; width:100%; border-bottom:1px dotted #4171B5; padding-top:5px; margin-bottom:2px;}
		.activityData{width:90%; display:none; border-top:1px dotted #4171B5; padding:5px 20px;}


		.coursetitle {position:relative; width:300px;height:20px; font-size:1.00em;}
		.satisfaction {position:absolute; top:5px; left:350px; font-size:small;}
		.completion {position:absolute; top:5px; left:450px; font-size:small;}
		.attempts {position:absolute; top:5px; left:550px; font-size:small;}
		.suspended {position:absolute; top:5px; left:625px; font-size:small;}


		.div_detail_arrows {position:absolute; top:3px; right:25px; width:22px; height:22px; overflow:hidden; cursor:pointer;}
		.img_detail_arrows {position:absolute; }
		.detailsTopLabel{font-size:1.00em; font-weight:bold; position: relative; margin-bottom:5px; width:98%; padding-left:3px; }


		.table_details {border-spacing:0; width:100%; padding:5;}
		table.table_details td {vertical-align:top;}
		.td_objectives {width:250px; padding-right:2px; border:1 }
		.td_runtimeDetails {width:250px;}
		.td_runtimeObjectives {width:250px;}
		.tr_space {height:10px;}
		td.dotted {border-bottom:1px dotted #4171B5;}
		td.intLblWidth {width:110px;}
		.actObjectiveData {position: relative; }
		.actRuntimeData {position:relative;}

		.interactionsTable {display:none;}
		.sub_detail_arrows {margin-left:5px; width:16px; height:16px; overflow:hidden; position:absolute; left:90px; top:1px; cursor:pointer;}
		.comment_arrow {left:190px;}
		.learnerComments {display:block;}
		.lmsComments {display:block;}

		.actDetailsPropLbl{font-weight:lighter; font-size:.80em; margin: 5 5 5 5;}
		.actDetailsPropVal{font-weight:bold; font-size:.80em; margin: 5 5 5 5;}

		.margin5 {margin-left:5px;}
		.margin20 {margin-left:10px;}
		.bold {font-weight:bold;}
		.hidden {visibility:hidden;}

		.passed {color:green;}
		.failed {color:red;}
		.completed {color:green;}
		.incomplete {color:red;}

		</style>';
	echo $htmlHead;
	echo "<div class='activityReportHeader'>Course Activity Report";
	echo '<div class="launchHistoryLink"><a href="cloudLaunchHistoryReport.php?regid='.$regid.'">Launch History Report</a></div></div>';


	//Get the results from the cloud
	$ScormService = new ScormEngineService($CFG->scormcloud_serviceurl,$CFG->scormcloud_appid,$CFG->scormcloud_secretkey);
	$regService = $ScormService->getRegistrationService();
	//get the full results report
	$resultXmlString = $regService->GetRegistrationResult($regid, 2, 0);

	//echo $resultXmlString.'<br/>';

	$resXml = simplexml_load_string($resultXmlString);

	$rootActivities = $resXml->xpath('//registrationreport/activity');


	$rootActivity = $rootActivities[0];
	//echo $rootActivity.'<br/>';

	echo '<div id="column_headers">';
		echo "<div class='headertitle' >Learning Object Name</div>";
		echo "<div class='headersatisfied'>Satisfaction</div>";
		echo "<div class='headercompleted'>Completion</div>";
		echo "<div class='headerattempts'>Attempts</div>";
		echo "<div class='headersuspended'>Suspended</div>";
	echo '</div>';

	scormcloud_displayActivity($rootActivity,0,0);


//}

?>
