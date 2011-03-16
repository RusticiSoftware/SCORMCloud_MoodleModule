<?php

    require_once('../../../config.php');
    require_once($CFG->dirroot.'/lib/statslib.php');
    require_once($CFG->libdir.'/adminlib.php');

	require_once('../../../mod/scormcloud/SCORMAPI/ScormEngineService.php');
	require_once('../../../mod/scormcloud/SCORMAPI/ServiceRequest.php');
	require_once('../../../mod/scormcloud/SCORMAPI/CourseData.php');
	require_once('../../../mod/scormcloud/SCORMAPI/ServiceRequest.php');
	require_once('../../../mod/scormcloud/SCORMAPI/ReportingService.php');

	$dateRangeType = optional_param('drt',null, PARAM_RAW);
	$dateRangeStart = optional_param('drs',null, PARAM_RAW);
	$dateRangeEnd = optional_param('dre',null, PARAM_RAW);
	$dateCriteria = optional_param('dc',null, PARAM_RAW);

    admin_externalpage_setup('reportscormcloud');
    admin_externalpage_print_header();
	//include jquery for our use
	echo '<script type="text/javascript" ';
	echo "src=\"{$CFG->wwwroot}/mod/scormcloud/scripts/jquery-1.3.2.min.js\"></script>\n";
	//include jquery.thickbox for our use
	echo '<script type="text/javascript" ';
	echo "src=\"{$CFG->wwwroot}/mod/scormcloud/scripts/thickbox-compressed.js\"></script>\n";
	echo '<link rel="stylesheet" ';
	echo "href=\"{$CFG->wwwroot}/mod/scormcloud/scripts/thickbox.css\" type=\"text/css\" media=\"screen\" />\n";
	//include our rustici.moodle.cloud.js file
	echo '<script type="text/javascript" ';
	echo "src=\"{$CFG->wwwroot}/mod/scormcloud/scripts/jquery-ui-1.7.2.custom.min.js\"></script>\n";
	echo '<script type="text/javascript" ';
	echo "src=\"{$CFG->wwwroot}/mod/scormcloud/scripts/daterangepicker.jQuery.js\"></script>\n";
	echo '<link rel="stylesheet" ';
	echo "href=\"{$CFG->wwwroot}/mod/scormcloud/css/ui-lightness/jquery-ui-1.7.2.custom.css\" type=\"text/css\" media=\"screen\" />\n";
	echo '<link rel="stylesheet" ';
	echo "href=\"{$CFG->wwwroot}/mod/scormcloud/css/ui.daterangepicker.css\" type=\"text/css\" media=\"screen\" />\n";
	//Tipsy Includes
	echo '<script type="text/javascript" ';
	echo "src=\"{$CFG->wwwroot}/mod/scormcloud/scripts/jquery.tipsy.js\"></script>\n";
	echo '<link rel="stylesheet" ';
	echo "href=\"{$CFG->wwwroot}/mod/scormcloud/css/tipsy.css\" type=\"text/css\" media=\"screen\" />\n";
	echo '<div class="mod-scormcloud">';
	//Reportage Includes
	echo '<script type="text/javascript" ';
	echo "src=\"http://cloud.scorm.com/Reportage/scripts/reportage.combined.nojquery.js\"></script>\n";
	echo '<link rel="stylesheet" ';
	echo "href=\"{$CFG->wwwroot}/mod/scormcloud/css/reportage.combined.css\" type=\"text/css\" media=\"screen\" />\n";
	echo '<div class="mod-scormcloud">';

//Check for some defaults to set the form up
	$ScormService = new ScormEngineService($CFG->scormcloud_serviceurl,$CFG->scormcloud_appid,$CFG->scormcloud_secretkey);
	$rptService = $ScormService->getReportingService();
	$rptAuth = $rptService->GetReportageAuth('FREENAV',true);
	
	
	if(!isset($dateRangeType))
	{
		$dateRangeType = 'all';
	}
	if(!isset($dateRangeStart))
	{
		$dateRangeStart = '2009-01-01';
	}
	if(!isset($dateRangeEnd))
	{
		$dateRangeEnd = date("Y-m-d");
	}
	if(!isset($dateCriteria))
	{
		$dateCriteria = 'completed';
	}
	
//Report banner SCORM Cloud branded?
	echo '<table style="width:100%">
			<tr>
				<td style"width:70%">
					<h1>SCORM Cloud Analytics</h1>
				</td>
				<td style="text-align:right;font-size:medium;">
					<a id="CloudConsoleLink" href="https://accounts.scorm.com/scorm-cloud-manager/public/console-login" 
						target="_blank" title="Open the SCORM Cloud Management Console in a new window.">SCORM Cloud Management Console</a>
					&nbsp;&nbsp;|&nbsp;&nbsp;
					<a id="ReportageLink" href="'.$rptService->LaunchReportage($rptAuth).'" 
						target="_blank" title="Open the SCORM Reportage Console in a new window.">SCORM Reportage</a>
				</td>
			</tr>
			</table>
	<br/>';
//Report Date Selector Section
/*
	echo '<div style="border:1px #CCCCCC dotted;padding:10px 10px 10px 10px;">';
	echo '<table style=""><tr><td style="width:260px;text-align:center">';
	echo '<div id="DateTypeSelectorDiv" style="font-size:large;">';
	echo 'Date Range Type<br/>';
	echo '<select id="DateRangeType" style="font-size:large;width:250px;">';
	echo '<option value="all">No Date Filter</option>';
	echo '<option value="mtd">Current Month To Date</option>';
	echo '<option value="ytd">Current Year To Date</option>';
	echo '<option value="selection">Choose Date Range...</option>';
	echo '</select>';
	echo '</div>';
	echo '</td><td style="width:250px" id="DateSelectorColumn">';
	echo '<div id="DateSelectorDiv" style="font-size:large;">';
	echo '&nbsp;&nbsp;&nbsp;&nbsp;From&nbsp;&nbsp;<input type="text" id="DateRangeStart" value="'.$dateRangeStart.'" style="font-size:large;frameborder:0;width:130px;" />';
	echo '<br/>';
	echo '&nbsp;&nbsp;&nbsp;&nbsp;To&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" id="DateRangeEnd" value="'.$dateRangeEnd.'" style="font-size:large;width:130px;" /></large></div>';
	echo '</td><td style="width:250px">';
	echo '<div id="DateCriteriaDiv" style="font-size:large;padding-left:10px">Results Based On';
	echo '<br/><select id="DateCriteria" style="font-size:large;width:200px">';
	echo '<option value="launched">Launch Date</option>';
	echo '<option value="completed">Completion Date</option>';
	echo '</select></div>';
	echo '</td><td>';
	echo '<input type="button" id="ApplyChanges" value="Update Reports" style="font-size:large;" />';
	echo '</td></tr></table>';
	echo '</div>';
	echo '<br/>';
	*/
	echo '<div style="border:1px #CCCCCC dotted;padding:10px 10px 10px 10px;text-align:left;">';
	echo '<div style="font-size:x-large;">Showing&nbsp;&nbsp;';
	echo '<input type="text" id="newPicker" style="font-size:1.2em;font-weight:bold;border:0;width:600px;cursor:hand;" ';
	if(isset($dateRangeStart) && isset($dateRangeEnd))
	{
		echo 'value="'.$dateRangeStart.' to '.$dateRangeEnd.'"';
	}
	echo ' title="Click to change date range settings..." />';
	//echo '<br>';
	//echo '<small>reset</small></div>';
	echo '<input type="hidden" id="CurrentDateRange" />';
	echo '</div>';
	echo '<br/>';
	echo '<hr/>';
	echo '<br/>';
	//  AppId Summary Report

	

	$dateOptions = new DateRangeSettings($dateRangeType,$dateRangeStart,$dateRangeEnd,$dateCriteria);

	$sumWidgetSettings = new WidgetSettings($dateOptions);
	$sumWidgetSettings->setShowTitle(true);
	$sumWidgetSettings->setScriptBased(true);
	$sumWidgetSettings->setEmbedded(true);
	$sumWidgetSettings->setVertical(true);
	$sumWidgetSettings->setDivname('TotalSummary');
	
	$coursesWidgetSettings = new WidgetSettings($dateOptions);
	$coursesWidgetSettings->setShowTitle(true);
	$coursesWidgetSettings->setScriptBased(true);
	$coursesWidgetSettings->setEmbedded(true);
	$coursesWidgetSettings->setExpand(false);
	$coursesWidgetSettings->setDivname('CourseListDiv');
	
	$learnersWidgetSettings = new WidgetSettings($dateOptions);
	$learnersWidgetSettings->setShowTitle(true);
	$learnersWidgetSettings->setScriptBased(true);
	$learnersWidgetSettings->setEmbedded(true);
	$learnersWidgetSettings->setExpand(false);
	$learnersWidgetSettings->setDivname('LearnersListDiv');

	$summaryUrl = $rptService->GetWidgetUrl($rptAuth,'allSummary',$sumWidgetSettings);
	$coursesUrl = $rptService->GetWidgetUrl($rptAuth,'allCourses',$coursesWidgetSettings);
	$learnersUrl = $rptService->GetWidgetUrl($rptAuth,'allLearners',$learnersWidgetSettings);
	
	echo '<table><tr style="padding-bottom:10px;"><td colspan="2">';
	echo '<div id="TotalSummary">Loading Summary...</div>';
	echo '<br></td></tr>';
	echo '<tr><td style="vertical-align:top;">';
	//  All Courses Detail Widget
	echo '<div id="CourseListDiv" style="border:1px #CCCCCC solid;margin:10px 10px 10px 10px">Loading All Courses...</div>';
	echo '</td></tr><tr><td style="vertical-align:top;">';
	//All Learners Detail Widget
	echo '<div id="LearnersListDiv" style="border:1px #CCCCCC solid;margin:10px 10px 10px 10px">Loading All Learners...</div>';
	echo '</td></tr></table>';
	//Load 'em Up...
	echo '<script type="text/javascript">';
	echo '$(document).ready(function(){';
	echo '	loadScript("'.$summaryUrl.'");';
	echo '	loadScript("'.$coursesUrl.'");';
	echo '	loadScript("'.$learnersUrl.'");';
	echo '  $("#newPicker").daterangepicker({
										presets: {dateRange: "Pick a date range..."},
										onClose: function(){ChangeDateRange("selection",$("#newPicker").val());},
										onOpen: function(){$("#CurrentDateRange").val($("#newPicker").val());},
										dateFormat: $.datepicker.ATOM,
										rangeSplitter: " to "
			});';
	echo '	$("#newPicker").tipsy({fade: true});';
	echo '	$("#CloudConsoleLink").tipsy({fade: true});';
	echo '	$("#ReportageLink").tipsy({fade: true});';
	echo '});';
	echo 'function ChangeDateRange(DateRangeType,NewDateRange){';
	echo '		if(NewDateRange != $("#CurrentDateRange").val()){
					DateSelections = NewDateRange.split(" to ");
					if(DateSelections[1] != null)
					{
						DateRangeStart = DateSelections[0];
						DateRangeEnd = DateSelections[1];
					}else{
						DateRangeStart = NewDateRange;
						DateRangeEnd = NewDateRange;
					}
					var baseurl = window.location.href.replace(window.location.search,"");
					if(DateRangeType == "all")
					{
						window.location = baseurl;
					}else{
						window.location = baseurl + "?drt="+DateRangeType+"&drs="+DateRangeStart+"&dre="+DateRangeEnd+"&dc=launched";
					}
				}
			}
	</script>';	
	
	
//now add the javascript and jquery for the report selectors	
echo '<script type="text/javascript">
		$(function(){';
if(isset($dateCriteria))
{
	echo '$("#DateCriteria").val("'.$dateCriteria.'");';
}else{
	echo '$("#DateCriteria").val("launched");';
}
if(isset($dateRangeType))
{
	echo '$("#DateRangeType").val("'.$dateRangeType.'");';
	if($dateRangeType=="selection")
	{
		echo '$("#DateSelectorColumn").show();';
	}else{
		echo '$("#DateSelectorColumn").hide();';
	}
}else{
	echo '$("#DateRangeType").val("all");';
	echo '$("#DateSelectorColumn").hide();';
}
echo '		$("#DateRangeStart").datepicker({ dateFormat: "yy-mm-dd" });
			$("#DateRangeEnd").datepicker({ dateFormat: "yy-mm-dd" });
			$("#ApplyChanges").click(function(){UpdateSummaryWidget($("#DateRangeType").val(),$("#DateRangeStart").val(),$("#DateRangeEnd").val(),$("#DateCriteria").val());});';

echo '		$("#DateRangeType").click(
				function(){
					if($(this).val()=="selection")
					{
						$("#DateSelectorColumn").show();
					}else{
						$("#DateSelectorColumn").hide();
					}
					});
					
			$("#CourseRegistrationsDiv").hide();
			$("#LearnerRegistrationsDiv").hide();
		});
		
		function UpdateSummaryWidget(DateRangeType,DateRangeStart,DateRangeEnd,DateCriteria)
		{
			var baseurl = window.location.href.replace(window.location.search,"");
			if(DateRangeType == "all")
			{
				window.location = baseurl;
			}else{
				window.location = baseurl + "?drt="+DateRangeType+"&drs="+DateRangeStart+"&dre="+DateRangeEnd+"&dc="+DateCriteria;
			}
		}
		
		
		</script>';
		
admin_externalpage_print_footer();
?>