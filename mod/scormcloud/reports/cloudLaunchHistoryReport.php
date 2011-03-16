<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

	<title>Registration List Sample</title>
	
</head>

<body>
<?php
/*
==============================================================================
	
	Copyright (c) 2009 Rustici Software
	
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

==============================================================================
*/
require_once('../../../config.php');
global $CFG;

require_once("../lib.php");
require_once('../locallib.php');

require_once('../SCORMAPI/ScormEngineService.php');
require_once('../SCORMAPI/ServiceRequest.php');
require_once('../SCORMAPI/CourseData.php');
require_once('../SCORMAPI/RegistrationService.php');
require_once('../SCORMAPI/RegistrationSummary.php');

$regid = required_param("regid");

$headertext = '
<script type="text/javascript" src="../scripts/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){

    });
    </script>
<script type="text/javascript">
        var extConfigurationString = "";
        var reportsHelperUrl = "LaunchHistoryHelper.php";
    </script>
<link rel="Stylesheet" href="LaunchHistoryControlResources/styles/LaunchHistoryReport.css"  type="text/css"/>
<script type="text/javascript" src="LaunchHistoryControlResources/scripts/LaunchHistoryReport.js"></script>
<style>

#column_headers {position:relative; font-weight:bold; border-bottom:1px solid #4171B5; padding: 3px 0px; margin-top:20px;}
.headerLaunchTime {position:relative; width:300px; font-size:110%;}
.headerExitTime {position:absolute; top:3px; left:200px; font-size:110%;}
.headerDuration {position:absolute; top:3px; left:400px; font-size:110%;}
.headerSatisfaction {position:absolute; top:3px; left:525px; font-size:110%;}
.headerCompletion {position:absolute; top:3px; left:650px; font-size:110%;}



.activityReportHeader {font-size:150%; position:relative;}
.launchHistoryLink {position:absolute; right:25px; top:0px;}
.launchHistoryLink img {margin-right:10px; vertical-align:top;}

td.launch_headerName {
color:#0077CC;
font-size:120%;
font-weight:bold;
padding-bottom:0;
width:154px;
}
td.launch_index {width:120px;}

#historyInfo {margin-top:10px; margin-left:50px;}

.instance_info_reg_fields_title, .score_fields_title {font-size:90%;}
.info_label {font-size:90%;}

</style>';

echo $headertext;

$ScormService = new ScormEngineService($CFG->scormcloud_serviceurl,$CFG->scormcloud_appid,$CFG->scormcloud_secretkey);

$regService = $ScormService->getRegistrationService();

//show Launch History
$resultArray = $regService->GetLaunchHistory($regid);

echo '<div id="historyInfo">';
	echo "<table>";
	echo "<tr><td class='launch_headerName' colspan='2'>Launch Instances</td>";
	echo "<td class='launch_time'>Launch Time</td>";
	echo "<td class='launch_duration'>Duration</td></tr></table>";
	echo '<div id="historyDetails" class="history_details" runat="server">';

echo "<div class='launch_list'>";
$idx = 1;
foreach($resultArray as $result)
{
$lid = 	$result->getId();

echo "<div class='LaunchPlaceHolder' id='launch_".$result->getId()."' regid='".$regid."'>";

echo "<div class='hide_show_div' >";
	echo "<table>";
	echo "<tr><td class='launch_listPrefix'>+</td>";
	echo "<td class='launch_index'>".$idx.".</td>";
	echo "<td class='launch_time'>".scormcloud_formatHistoryTime($result->getLaunchTime())."</td>";
	echo "<td class='launch_duration'><script>document.write(fmtDuration(".(scormcloud_convertTimeToInt($result->getExitTime()) - scormcloud_convertTimeToInt($result->getLaunchTime()))* 1000 ."))</script></td>";
	echo "</tr></table>";
echo "</div>";

echo "<div class='launch_activity_list'><div id='receiver' class='div_receiver'></div></div>";
echo "</div>";

$idx++;
}
echo "  </div>";
echo '</div></div>';
?>
</body>
</html>