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
require_once($CFG->dirroot.'/lib/accesslib.php');
require_once('SCORMAPI/ScormEngineService.php');
require_once('SCORMAPI/ServiceRequest.php');
require_once('SCORMAPI/CourseData.php');
require_once('lib.php');

function scormcloud_course_format_display($user,$course) {
	global $CFG;
   
	$courseid = $course->id;

	$strupdate = get_string('update');
	$strmodule = get_string('modulename','scormcloud');
	$context = get_context_instance(CONTEXT_COURSE,$courseid);

	//  Check to see if we're showing Reportage code or not
	$includeReportage = false;
	if($CFG->scormcloud_appid != 'defaultID')
	{
		$includeReportage = true;
	}
	$ScormService = new ScormEngineService($CFG->scormcloud_serviceurl,$CFG->scormcloud_appid,$CFG->scormcloud_secretkey);
	$rptService = $ScormService->getReportingService();
	$rptAuth = $rptService->GetReportageAuth('FREENAV',true);
	if($includeReportage == false)
	{
		$rptAuth = "false";
	}
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
	echo "src=\"{$CFG->wwwroot}/mod/scormcloud/scripts/rustici.moodle.cloud.js\"></script>\n";
	echo '<script type="text/javascript" ';
	echo "src=\"{$CFG->wwwroot}/mod/scormcloud/scripts/table2CSV.js\"></script>\n";
	if($rptAuth!="false")
	{
		echo '<script type="text/javascript" ';
		echo "src=\"http://cloud.scorm.com/Reportage/scripts/reportage.combined.nojquery.js\"></script>\n";
	}
	echo '<link rel="stylesheet" ';
	echo "href=\"{$CFG->wwwroot}/mod/scormcloud/css/reportage.combined.css\" type=\"text/css\" media=\"screen\" />\n";


	if ($scormclouds = get_all_instances_in_course('scormcloud', $course)) 
	{
		if ($course->format == "weeks" || $course->format == "topics") {
			$id = optional_param('id', 0, PARAM_INT);

		    if (!empty($id)) {
		        if (! $cm = get_coursemodule_from_id('scormcloud', $id)) {
		            error("Course Module ID was incorrect");
		        }
			}
			$scormcloud = get_record("scormcloud","id","$cm->instance");
		}else{
			$scormcloud = current($scormclouds);
		}
		
		if($rptAuth!="false")
		{

		$dateOptions = new DateRangeSettings($dateRangeType,$dateRangeStart,$dateRangeEnd,$dateCriteria);

		$usumWidgetSettings = new WidgetSettings();
		$usumWidgetSettings->setShowTitle(false);
		$usumWidgetSettings->setScriptBased(true);
		$usumWidgetSettings->setEmbedded(true);
		$usumWidgetSettings->setVertical(false);
		$usumWidgetSettings->setDivname('UserSummary');
		$usumWidgetSettings->setCourseId($scormcloud->id);
		$usumWidgetSettings->setLearnerId($user->username);

		$uactWidgetSettings = new WidgetSettings();
		$uactWidgetSettings->setShowTitle(true);
		$uactWidgetSettings->setScriptBased(true);
		$uactWidgetSettings->setEmbedded(true);
		$uactWidgetSettings->setDivname('UserActivities');
		$uactWidgetSettings->setCourseId($scormcloud->id);
		$uactWidgetSettings->setLearnerId($user->username);
				
		$sumWidgetSettings = new WidgetSettings();
		$sumWidgetSettings->setShowTitle(true);
		$sumWidgetSettings->setScriptBased(true);
		$sumWidgetSettings->setEmbedded(true);
		$sumWidgetSettings->setVertical(true);
		$sumWidgetSettings->setCourseId($scormcloud->id);
		$sumWidgetSettings->setDivname('CourseSummary');

		$coursesWidgetSettings = new WidgetSettings();
		$coursesWidgetSettings->setShowTitle(true);
		$coursesWidgetSettings->setScriptBased(true);
		$coursesWidgetSettings->setEmbedded(true);
		$coursesWidgetSettings->setExpand(true);
		$coursesWidgetSettings->setDivname('CourseListData');
		$coursesWidgetSettings->setCourseId($scormcloud->id);

		$learnersWidgetSettings = new WidgetSettings();
		$learnersWidgetSettings->setShowTitle(true);
		$learnersWidgetSettings->setScriptBased(true);
		$learnersWidgetSettings->setEmbedded(true);
		$learnersWidgetSettings->setExpand(false);
		$learnersWidgetSettings->setDivname('LearnersListData');
		$learnersWidgetSettings->setCourseId($scormcloud->id);

		$userSummaryUrl = $rptService->GetWidgetUrl($rptAuth,'learnerSummary',$usumWidgetSettings,$dateOptions);
		$userActivitiesUrl = $rptService->GetWidgetUrl($rptAuth,'learnerCourseActivities',$uactWidgetSettings,$dateOptions);
		$summaryUrl = $rptService->GetWidgetUrl($rptAuth,'courseSummary',$sumWidgetSettings,$dateOptions);
		$coursesUrl = $rptService->GetWidgetUrl($rptAuth,'courseActivities',$coursesWidgetSettings,$dateOptions);
		$learnersUrl = $rptService->GetWidgetUrl($rptAuth,'learnerRegistration',$learnersWidgetSettings,$dateOptions);
		
		//echo $summaryUrl;
		
		}
		
		//**********************************************************
		// Build the User Summary Widget using local Moodle data
		//**********************************************************
        $currentStatus = '';
		if (!$regs = get_records_select('scormcloud_registrations','userid='.$user->id.' AND scormcloudid='.$scormcloud->id)) {
			//there is not currently a registration for this user
			$currentStatus = get_string("noregmessage","scormcloud");
		}else{
			foreach ($regs as $reg) {
				if($reg->scormcloudid==$scormcloud->id)
				{
					$regid = $reg->regid;
					$currentStatus .= '<div class="reportage"><table>   
			               <tbody>
							<tr>
							<td>
							<div class="summary_box med_summary summary_box_moused_off">
							    <table><tbody><tr><td class="stacked_summary_value">
								<span class="summary_value '.scormcloud_getComplVal(strval($reg->completion)).'">'.scormcloud_getComplVal(strval($reg->completion)).'</span>
								</td></tr><tr><td class="col_right">                
								<div class="summary_title">Course Completion</div>            
								</td></tr></tbody></table>
							</div>
							</td>
							<td>
								<div class="summary_box med_summary">
								    <table><tbody><tr>
									<td class="stacked_summary_value">
					                <span class="summary_value '.scormcloud_getSatVal(strval($reg->satisfaction)).'">'.scormcloud_getSatVal(strval($reg->satisfaction)).'</span>
					        		</td></tr><tr>
									<td class="col_right">                
									<div class="summary_title">Course Satisfaction</div>
									</td></tr></tbody></table>
								</div>
							</td></tr>
							<tr><td>
							<div class="summary_box big_summary">
							    <table><tbody><tr>
									<td rowspan="2" class="summary_value">
						                <span class="summary_value 1">'.round($reg->totaltime/60).'</span>
						        	</td>
									<td class="col_right">
						                <div class="summary_title">Total Minutes</div>
									</td></tr>
									</tbody></table>
							</div>
							</td>
							<td>
								<div class="summary_box big_summary">
								    <table><tbody><tr>
										<td rowspan="2" class="summary_value">
							                <span class="summary_value 0%">'.strval($reg->score).'</span><span class="summ_val_smaller">%</span>
						        		</td>
										<td class="col_right">
										<div class="summary_title">Score</div>
										</td></tr></tbody></table></div></td></tr>
						</tbody>
					</table></div>';
					//$currentStatus .= '<li><a href="'.$CFG->wwwroot.'/mod/scormcloud/registrationapi.php?id='.$regid.'&mode=reset&TB_iframe=true" class="thickbox" >Reset This Registration</a>';
					//$currentStatus .= '<li><a href="'.$CFG->wwwroot.'/mod/scormcloud/registrationapi.php?id='.$regid.'&mode=delete&TB_iframe=true" class="thickbox" >Delete This Registration</a>';			
				}
			}
   		}
		
		
		$headertext = '<div class="headingblock header">';
		$headertext .= '<h2>'.format_string($scormcloud->name).' - '.$course->fullname.'</h2></div>';
		$headertext .= '<hr style="width:98%" /><br/>';
		$headertext .= '<div style="font-size:medium"><table style="width:98%"><tr><td>';
		$headertext .= '&nbsp;&nbsp;<a class="thickbox" href="'.$CFG->wwwroot . '/mod/scormcloud/launch.php?courseid='.$scormcloud->id.'&userid='.$user->id.'&TB_iframe=true" target="_blank" >Launch Course</a>';
		$headertext .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		$headertext .= '<a class="thickbox" href="'.$CFG->wwwroot . '/mod/scormcloud/preview.php?courseid='.$scormcloud->id.'&userid='.$user->id.'&TB_iframe=true" target="_blank" >Preview Course</a>';
		$headertext .= '</td><td style="text-align:right">';
		if (has_capability('moodle/course:manageactivities', $context)) //only show if the user is an admin
		{
			$headertext .= '<a class="thickbox" href="'.$CFG->wwwroot.'/mod/scormcloud/uploadpif.php?id='.$scormcloud->id.'&mode=update&TB_iframe=true">Update Package</a>';
			$headertext .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			$headertext .= '<a class="thickbox" href="'.$CFG->wwwroot.'/mod/scormcloud/packageprops.php?id='.$scormcloud->id.'&TB_iframe=true&height=500&width=700" id="lnkPackageProperties">Package Properties</a>';
		}
		$headertext .= '&nbsp;&nbsp;</td></tr></table></div>';
		$headertext .= '<br/><hr style="width:98%"/><br/>';
	    $headertext .= '<div class="content" style="font-size:medium;">' . format_string($course->summary) . '</div>';
		$headertext .= '<br/>';
		$headertext .= '<br/>';
		$headertext .= '<br><fieldset style="padding:10px 10px 10px 10px;">';
		$headertext .= '<legend style="font-size:xx-large;margin:5px 5px 5px 5px;">';
		$headertext .= '&nbsp;&nbsp;Your Current Status&nbsp;&nbsp;</legend>';
		$headertext .= '<table><tr><td style="padding:10px 10px 10px 10px;vertical-align:top;">';
		$headertext .= $currentStatus;
		$headertext .= '</td></tr>';
		
		if($rptAuth!="false")
		{
		//  All Courses Detail Widget
		$headertext .= '<tr><td style="vertical-align:top;padding:10px 10px 10px 10px;">';
		$headertext .= '<div id="UserActivities" style="border:1px #CCCCCC solid;padding:10px 10px 10px 10px">';
		$headertext .= '<div id="UserActivities">Loading Your Activities...</div>';
		$headertext .= '</div></td></tr>';
		}
		
		$headertext .= '</table>';
		$headertext .= '</fieldset>';
		if($rptAuth!="false")
		{
		//Load 'em Up...
		$headertext .= '<script type="text/javascript">';
		$headertext .= '$(document).ready(function(){';
		//$headertext .= '	loadScript("'.$userSummaryUrl.'");';
		$headertext .= '	loadScript("'.$userActivitiesUrl.'");';
		$headertext .= '});';
		$headertext .= '</script>';
		if (has_capability('moodle/course:manageactivities', $context)) //only show if the user is an admin
		{	
			$headertext .= '<br><fieldset style="padding:10px 10px 10px 10px">';
			$headertext .= '<legend style="font-size:xx-large;margin:5px 5px 5px 5px;">';
			$headertext .= '&nbsp;&nbsp;<img src="'.$CFG->wwwroot.'/mod/scormcloud/icon.gif" />';
			$headertext .= '&nbsp;&nbsp;Course Analytics&nbsp;&nbsp;</legend>';
			$headertext .= '<table style="width:90%"><tr style="padding-bottom:10px;"><td colspan="2">';
			$headertext .= '<div id="CourseSummary" style="width:500px;">Loading Summary...</div>';
			$headertext .= '<br></td></tr>';
			$headertext .= '<tr><td style="vertical-align:top;">';
			//  All Courses Detail Widget
			$headertext .= '<div id="CourseListDiv" style="border:1px #CCCCCC solid;padding:10px 10px 10px 10px;width:450px">';
			$headertext .= '<div id="CourseListData" >Loading All Courses...</div>';
			//$headertext .= '<form action="'.$CFG->wwwroot.'/mod/scormcloud/savetoexcel.php" method="post" target="_blank"';
			//$headertext .= "onsubmit=\"$('#datatodisplay').val($('#CourseListDiv .details_table').table2CSV({delivery:'value',header:['Learner','Completed','Passed','AvgScore','AvgTime']}))\" >";
		    //$headertext .= '<input  type="submit" value="Download CSV">';
		    //$headertext .= '<input type="hidden" id="datatodisplay" name="datatodisplay" />';
		    //$headertext .= '</form>';
			$headertext .= '</div>';
			$headertext .= '<br></td>';
			$headertext .= '</tr><tr><td style="vertical-align:top;">';
			//All Learners Detail Widget
			$headertext .= '<div id="LearnersListDiv" style="border:1px #CCCCCC solid;padding:10px 10px 10px 10px;width:450px;">';
			$headertext .= '<div id="LearnersListData">Loading All Learners...</div>';
			//$headertext .= '<form action="'.$CFG->wwwroot.'/mod/scormcloud/savetoexcel.php" method="post" target="_blank"';
			//$headertext .= "onsubmit=\"$('#datatodisplay').val($('#LearnersListDiv .details_table').table2CSV({delivery:'value',header:['Learner','Completed','Passed','AvgScore','AvgTime']}))\" >";
		    //$headertext .= '<input  type="submit" value="Download CSV">';
		    //$headertext .= '<input type="hidden" id="datatodisplay" name="datatodisplay" />';
		    //$headertext .= '</form>';
			$headertext .= '</td></tr></table></fieldset>';
			//Load 'em Up...
			$headertext .= '<script type="text/javascript">';
			$headertext .= '$(document).ready(function(){';
			$headertext .= '	loadScript("'.$summaryUrl.'");';
			$headertext .= '	loadScript("'.$coursesUrl.'");';
			$headertext .= '	loadScript("'.$learnersUrl.'");';
			$headertext .= '});';
			$headertext .= '</script>';
		}
	}
		$regid = '';
	
		if($courseExists = scormcloud_course_exists_on_cloud($scormcloud->id)){
			//$containercontent = '<table cellpadding="20">';
			//$containercontent .= '<tr valign="top"><td>';
			$containercontent = $headertext;
			//$containercontent .= '</td></tr></table>'; //close out the parent table
			print_container($containercontent,false,'scormcloud-container');
	}
	else
	{
		if (has_capability('moodle/course:manageactivities', $context)) {
			echo '<br><br><br>';
			echo '<table style="width:100%">';
			echo '<tr><td style="text-align:center">';
		
			echo '</td></tr>';
			echo '<tr><td style="text-align:center">';
			echo '<div id="UploadFrame"><iframe width="100%" height="500px" style="border:0;" src="' . $CFG->wwwroot . '/mod/scormcloud/uploadpif.php?id=' . $scormcloud->course . '|'.$scormcloud->id.'&mode=new" id="ifmImport" /></div>';
			echo '</td></tr>';
			echo '</table>';
		}
	}
}
else
{
	if (has_capability('moodle/course:manageactivities', $context)) {
		
		if (has_capability('moodle/course:update', $context)) {
            // Create a new activity
            redirect($CFG->wwwroot.'/course/mod.php?id='.$course->id.'&amp;section=0&sesskey='.sesskey().'&amp;add=scormcloud');
        } else {
            notify('Could not find a scormcloud course here');
        }
	}
   }
   echo '</div>';
   
}

function scormcloud_course_exists($courseid)
{
    global $CFG;
    $courseExists = false;
    
    if ($courses = get_records_select('scormcloud','id='.$courseid)) {
    	$courseExists = true;
    }
    
    return $courseExists;
}

function scormcloud_course_exists_on_cloud($courseid)
{
    global $CFG;
    
    $ScormService = new ScormEngineService($CFG->scormcloud_serviceurl,$CFG->scormcloud_appid,$CFG->scormcloud_secretkey);
    $courseService = $ScormService->getCourseService();
    return $courseService->Exists($courseid);
}

function scormcloud_displayActivity($actNode, $actNum, $leftMargin){

    $title = $actNode->title;
    $satisfied = scormcloud_getSatVal2($actNode->progressstatus,$actNode->satisfied);
    $completed = scormcloud_getComplVal2($actNode->attemptprogressstatus,$actNode->completed);
    $attempts = $actNode->attempts;
    $suspended = $actNode->suspended;
    
    echo "<div class='activity'>";
    
    echo "<div class='title' style='margin-left:".$leftMargin."px;'>$title</div>";
    echo "<div class='satisfaction $satisfied'>".get_string("$satisfied", "scormcloud")."</div>";
    echo "<div class='completion $completed'>".get_string("$completed","scormcloud")."</div>";
    echo "<div class='attempts'>$attempts</div>";
    //echo "<div class='suspended'>$suspended</div>";
    //get total time
    echo '<div class="div_detail_arrows" onclick=\'$(this).parent().find("div.activityData").toggle().parent().css("background-color"'.
    	',$(this).parent().find("div.activityData").is(":hidden") ? "#FFFFFF" : "#CEE4F2");'.
    	'$("img",this).css("right",$(this).parent().find("div.activityData").is(":hidden") ? "-22px" : "0px");\'>'.
    	'<img class="img_detail_arrows" src="img/up_down_arrows.gif" /></div>';
    
    echo "<div class='activityData' >";
    echo "<table class='table_details'><tr><td class='td_objectives'>";
    if ($actNode->objectives){
    	scormcloud_displayObjectives($actNode->objectives);
    }
    echo "</td><td class='td_runtime'>";
    if ($actNode->runtime){
    	scormcloud_displayRuntime($actNode->runtime, $actNum);
    }
    echo "</td></tr></table></div>";
    echo "</div>";
    
    $newActNum = 0;
    foreach($actNode->children->activity as $childAct){
    	$newActNum += 1;
    	scormcloud_displayActivity($childAct,$actNum.$newActNum,$leftMargin + 15);
    }
    
}

function scormcloud_displayObjectives($objectives){

    echo "<div class='actObjectiveData'>";
    
    echo "<div class='detailsTopLabel'>Activity Objectives</div>";
    echo "<table class='table_details'>";
    foreach ($objectives->objective as $obj){
    	$id = $obj['id'];
    	$measureStat = $obj->measurestatus;
    	$normMeasure = $obj->normalizedmeasure;
    	$progressstatus = $obj->progressstatus;
    	$satisfiedstatus = $obj->satisfiedstatus;
    	
    	echo "<tr><td><span class='actDetailsPropLbl'>Objective Id: </span></td><td><span class='actDetailsPropVal'>$id</span></td></tr>";
    	echo "<tr><td><span class='actDetailsPropLbl margin5'>Measure Status: </span></td><td><span class='actDetailsPropVal'>$measureStat</span></td></tr>";
    	echo "<tr><td><span class='actDetailsPropLbl margin5'>Normalized Measure: </span></td><td><span class='actDetailsPropVal'>$normMeasure</span></td></tr>";
    	echo "<tr><td><span class='actDetailsPropLbl margin5'>Progress Status: </span></td><td><span class='actDetailsPropVal'>$progressstatus</span></td></tr>";
    	echo "<tr><td><span class='actDetailsPropLbl margin5'>Satisfied Status: </span></td><td><span class='actDetailsPropVal'>$satisfiedstatus</span></td></tr>";
    	echo "<tr class='tr_space'><td></td><td></td></tr>";
    	
    }
    echo "</table>";
    echo '</div>';
    
}
function scormcloud_displayRuntime($rt,$actNum){
    echo "<div class='actRuntimeData'>";
    
    echo "<table class='table_details'><tr>";
    
    if ($rt->objectives->objective){
    	echo "<td class='td_runtimeObjectives'>";
    	echo "<div class='detailsTopLabel'>Runtime Objectives</div>";
    	echo "<table class='table_details'>";
    	foreach ($rt->objectives->objective as $obj){
    		
    		echo "<tr><td><span class='actDetailsPropLbl'>Objective Id:</span></td><td><span class='actDetailsPropVal'>".$obj['id']."</span></td></tr>";
    		echo "<tr><td><span class='actDetailsPropLbl margin5'>Scaled Score:</span></td><td><span class='actDetailsPropVal'>$obj->score_scaled</span></td></tr>";
    		echo "<tr><td><span class='actDetailsPropLbl margin5'>Minimum Score:</span></td><td><span class='actDetailsPropVal'>$obj->score_min</span></td></tr>";
    		echo "<tr><td><span class='actDetailsPropLbl margin5'>Raw Score:</span></td><td><span class='actDetailsPropVal'>$obj->score_raw</span></td></tr>";
    		echo "<tr><td><span class='actDetailsPropLbl margin5'>Maximum Score:</span></td><td><span class='actDetailsPropVal'>$obj->score_max</span></td></tr>";
    		echo "<tr><td><span class='actDetailsPropLbl margin5'>Success Status:</span></td><td><span class='actDetailsPropVal'>$obj->success_status</span></td></tr>";
    		echo "<tr><td><span class='actDetailsPropLbl margin5'>Completion Status:</span></td><td><span class='actDetailsPropVal'>$obj->completion_status</span></td></tr>";
    		echo "<tr><td><span class='actDetailsPropLbl margin5'>Progress Measure:</span></td><td><span class='actDetailsPropVal'>$obj->progress_measure</span></td></tr>";
    		echo "<tr><td><span class='actDetailsPropLbl margin5'>Description:</span></td><td><span class='actDetailsPropVal'>$obj->description</span></td></tr>";
    		echo "<tr class='tr_space'><td></td><td></td></tr>";
    		
    	}
    	echo "</table><br/>";
    	echo "</td>";
    	
    }
    
    
    echo "<td class='td_runtimeDetails'>";
    
    echo "<div class='detailsTopLabel'>Activity Runtime Data</div>";
    
    echo "<table class='table_details'>";
    echo "<tr><td><span class='actDetailsPropLbl'>Completion Status: </span></td><td><span class='actDetailsPropVal'>$rt->completion_status</span></td></tr>";
    echo "<tr><td><span class='actDetailsPropLbl'>Credit: </span></td><td><span class='actDetailsPropVal'>$rt->credit</span></td></tr>";
    echo "<tr><td><span class='actDetailsPropLbl'>Entry: </span></td><td><span class='actDetailsPropVal'>$rt->entry</span></td></tr>";
    echo "<tr><td><span class='actDetailsPropLbl'>Exit: </span></td><td><span class='actDetailsPropVal'>$rt->exit</span></td></tr>";
    echo "<tr><td><span class='actDetailsPropLbl'>Learner Preferences: </span></td><td></td></tr>";
    echo "<tr><td><span class='actDetailsPropLbl margin5'>Audio Level: </span></td><td><span class='actDetailsPropVal'>$rt->audio_level</span></td></tr>";
    echo "<tr><td><span class='actDetailsPropLbl margin5'>Language: </span></td><td><span class='actDetailsPropVal'>$rt->language</span></td></tr>";
    echo "<tr><td><span class='actDetailsPropLbl margin5'>Delivery Speed: </span></td><td><span class='actDetailsPropVal'>$rt->delivery_speed</span></td></tr>";
    echo "<tr><td><span class='actDetailsPropLbl margin5'>Audio Captioning: </span></td><td><span class='actDetailsPropVal'>$rt->audio_captioning</span></td></tr>";
    echo "<tr><td><span class='actDetailsPropLbl'>Location: </span></td><td><span class='actDetailsPropVal'>$rt->location</span></td></tr>";
    echo "<tr><td><span class='actDetailsPropLbl'>Mode: </span></td><td><span class='actDetailsPropVal'>$rt->mode</span></td></tr>";
    echo "<tr><td><span class='actDetailsPropLbl'>Progress Measure: </span></td><td><span class='actDetailsPropVal'>$rt->progress_measure</span></td></tr>";
    echo "<tr><td><span class='actDetailsPropLbl'>Score Scaled: </span></td><td><span class='actDetailsPropVal'>$rt->score_scaled</span></td></tr>";
    echo "<tr><td><span class='actDetailsPropLbl'>Score Raw: </span></td><td><span class='actDetailsPropVal'>$rt->score_raw</span></td></tr>";
    echo "<tr><td><span class='actDetailsPropLbl'>Score Minimum: </span></td><td><span class='actDetailsPropVal'>$rt->score_min</span></td></tr>";
    echo "<tr><td><span class='actDetailsPropLbl'>Score Maximum: </span></td><td><span class='actDetailsPropVal'>$rt->score_max</span></td></tr>";
    echo "<tr><td><span class='actDetailsPropLbl'>Total Time: </span></td><td><span class='actDetailsPropVal'>$rt->total_time</span></td></tr>";
    echo "<tr><td><span class='actDetailsPropLbl'>Time Tracked: </span></td><td><span class='actDetailsPropVal'>$rt->timetracked</span></td></tr>";
    echo "<tr><td><span class='actDetailsPropLbl'>Success Status: </span></td><td><span class='actDetailsPropVal'>$rt->success_status</span></td></tr>";
    echo "</table>";
    
    echo "</td>";
    
    
    echo "<td>";
    
    echo "<div class='detailsTopLabel'>Suspend Data</div>";
    echo "<div class='actDetailsProp'><span class='actDetailsPropVal'>$rt->suspend_data</span></div>";
    echo "<br/>";
    
    echo "<div class='detailsTopLabel'>Static Runtime Data</div>";
    echo "<table class='table_details'>";
    echo "<tr><td><span class='actDetailsPropLbl'>Completion Threshold:</span></td><td><span class='actDetailsPropVal'>".$rt->static->completion_threshold."</span></td></tr>";
    echo "<tr><td><span class='actDetailsPropLbl'>Launch Data:</span></td><td><span class='actDetailsPropVal'>".$rt->static->launch_data."</span></td></tr>";
    echo "<tr><td><span class='actDetailsPropLbl'>Learner Id:</span></td><td><span class='actDetailsPropVal'>".$rt->static->learner_id."</span></td></tr>";
    echo "<tr><td><span class='actDetailsPropLbl'>Learner Name:</span></td><td><span class='actDetailsPropVal'>".$rt->static->learner_name."</span></td></tr>";
    echo "<tr><td><span class='actDetailsPropLbl'>Maximum Time Allowed:</span></td><td><span class='actDetailsPropVal'>".$rt->static->max_time_allowed."</span></td></tr>";
    echo "<tr><td><span class='actDetailsPropLbl'>Scaled Passing Score:</span></td><td><span class='actDetailsPropVal'>".$rt->static->scaled_passing_score."</span></td></tr>";
    echo "<tr><td><span class='actDetailsPropLbl'>Time Limit Action:</span></td><td><span class='actDetailsPropVal'>".$rt->static->time_limit_action."</span></td></tr>";
    echo "</table><br/>";
    
    if ($rt->interactions->interaction){
    	echo '<div class="detailsTopLabel">Interactions<div id="interactionArrowDiv" class="sub_detail_arrows" '.
    		'onclick=\'$("#interactionsTable'.$actNum.'").toggle(); $("img",this).css("right",$("#interactionsTable'.$actNum.'").is(":hidden") ? "-16px" : "0px"); \' >'.
    		'<img id="interaction_arrows" class="img_detail_arrows" src="img/up_down_arrows_sm.gif" />'.
    		'</div></div>';
    	echo "<table id='interactionsTable$actNum' class='interactionsTable table_details'>";
    	foreach ($rt->interactions->interaction as $int){
    		
    		
    		echo "<tr><td class='intLblWidth'><span class='actDetailsPropLbl'>Interaction Id:</span></td><td><span class='actDetailsPropVal'>".$int['id']."</span></td></tr>";
    		echo "<tr><td><span class='actDetailsPropLbl margin5'>Type:</span></td><td><span class='actDetailsPropVal'>$int->type</span></td></tr>";
    		echo "<tr><td><span class='actDetailsPropLbl margin5'>Timestamp:</span></td><td><span class='actDetailsPropVal'>$int->timestamp</span></td></tr>";
    		echo "<tr><td colspan='2'><span class='actDetailsPropLbl margin5'>Objectives:</span></td><td></td></tr>";
    		foreach ($int->objectives->objective as $intObj){
    			echo "<tr><td><span class='actDetailsPropLbl margin20'>Objective Id:</span></td><td><span class='actDetailsPropVal'>".$intObj['id']."</span></td></tr>";
    		}
    		
    		echo "<tr><td colspan='2'><span class='actDetailsPropLbl margin5'>Correct Responses:</span></td></tr>";
    		foreach ($int->correct_responses->response as $intResp){
    			echo "<tr><td><span class='actDetailsPropLbl margin20'>Response Id:</span></td><td><span class='actDetailsPropVal'>".$intResp['id']."</span></td></tr>";
    		}
    		echo "<tr><td><span class='actDetailsPropLbl margin5'>Weighting:</span></td><td><span class='actDetailsPropVal'>$int->weighting</span></td></tr>";
    		echo "<tr><td><span class='actDetailsPropLbl margin5'>Learner Response:</span></td><td><span class='actDetailsPropVal'>$int->learner_response</span></td></tr>";
    		echo "<tr><td><span class='actDetailsPropLbl margin5'>Result:</span></td><td><span class='actDetailsPropVal'>$int->result</span></td></tr>";
    		echo "<tr><td><span class='actDetailsPropLbl margin5'>Latency:</span></td><td><span class='actDetailsPropVal'>$int->latency</span></td></tr>";
    		echo "<tr><td><span class='actDetailsPropLbl margin5'>Description:</span></td><td><span class='actDetailsPropVal'>$int->description</span></td></tr>";
    		echo "<tr class='tr_space'><td colspan='2'></td></tr>";
    		echo "<tr><td class='dotted' colspan='2'></td></tr>";
    		echo "<tr class='tr_space'><td colspan='2'></td></tr>";
    	}
    	echo "</table><br/>";
    }
    
    
    
    //echo "<br/>";
    if ($rt->comments_from_learner->comment){
    	echo '<div class="detailsTopLabel">Comments From Learner<div id="learnerCommentArrowDiv" class="sub_detail_arrows comment_arrow" '.
    		'onclick=\'$("#learnerComments'.$actNum.'").toggle(); $("img",this).css("right",$("#learnerComments'.$actNum.'").is(":hidden") ? "-16px" : "0px");\' >'.
    	
    		'<img id="learnerCommentArrows" class="img_detail_arrows" src="img/up_down_arrows_sm.gif" />'.
    		'</div></div>';
    	echo "<div id='learnerComments$actNum' class='learnerComments'>";
    	foreach ($rt->comments_from_learner->comment as $com){
    		
    		echo "<div class='commentDetail'><span class='actDetailsPropLbl bold'>Date: </span><span class='actDetailsPropVal'>$com->date_time</span></div>";
    		echo "<div class='commentDetail'><span class='actDetailsPropLbl bold'>Location: </span><span class='actDetailsPropVal'>$com->location</span></div>";
    		echo "<div class='commentDetail'><span class='actDetailsPropLbl bold'>Comment: </span><span class='actDetailsPropVal'>$com->value</span></div>";
    		echo "<br/>";
    	}
    	echo "</div><br/>";
    }
    
    //echo "<br/>";
    if ($rt->comments_from_lms->comment){
    	echo '<div class="detailsTopLabel">Comments From LMS<div id="learnerCommentArrowDiv" class="sub_detail_arrows comment_arrow" '.
    		'onclick=\'$("#lmsComments'.$actNum.'").toggle(); $("img",this).css("right",$("#lmsComments'.$actNum.'").is(":hidden") ? "-16px" : "0px");\' >'.
    	
    		'<img id="lmsCommentArrows" class="img_detail_arrows" src="img/up_down_arrows_sm.gif" />'.
    		'</div></div>';
    	echo "<div id='lmsComments$actNum' class='lmsComments'>";
    	foreach ($rt->comments_from_lms->comment as $com){
    		
    		echo "<div class='commentDetail'><span class='actDetailsPropLbl bold'>Date: </span><span class='actDetailsPropVal'>$com->date_time</span></div>";
    		echo "<div class='commentDetail'><span class='actDetailsPropLbl bold'>Location: </span><span class='actDetailsPropVal'>$com->location</span></div>";
    		echo "<div class='commentDetail'><span class='actDetailsPropLbl bold'>Comment: </span><span class='actDetailsPropVal'>$com->value</span></div>";
    		echo "<br/>";
    	}
    	echo "</div><br/>";
    }
    
    echo "</td>";
    echo "</tr></table>";
    
    echo '</div>';
}

function scormcloud_getSatVal($satVal){
    switch($satVal)
    {
    	case '1':
    		return "passed";
    		break;
    	case '2':
    		return "failed";
    		break;
    	default:
    		return "unknown";
    		break;
    }
}

function scormcloud_getComplVal($comVal){
    switch($comVal)
    {
    	case '1':
    		return "complete";
    		break;
    	case '2':
    		return "incomplete";
    		break;
    	default:
    		return "unknown";
    		break;
    }
}

function scormcloud_getSatVal2($satStat,$satVal){
    if ($satStat == 'true'){
    	if ($satVal == 'true'){
    		return "passed";
    	} else {
    		return "failed";
    	}
    	
    } else {
    	return "unknown";
    }
}

function scormcloud_getComplVal2($comStat,$comVal){
    if ($comStat == 'true'){
    	if ($comVal == 'true'){
    		return "completed";
    	} else {
    		return "incomplete";
    	}
    	
    } else {
    	return "unknown";
    }	
}

//input format 2009-08-11T19:01:50.081+0000 
function scormcloud_convertTimeToInt($str){
    //echo 'hour: '.substr($str,11,2).'<br/>';
    //echo 'minute: '.substr($str,14,2).'<br/>';
    return mktime(substr($str,11,2),substr($str,14,2),substr($str,17,2),substr($str,5,2),substr($str,8,2), substr($str,0,4));
}

function scormcloud_formatHistoryTime($timestr){
    //2009-08-19T18:41:33.257+0000
    $dt = substr($timestr,5,2).'/'.substr($timestr,8,2).'/'.substr($timestr,0,4);
    $hr = (int)substr($timestr,11,2);
    if ($hr < 12){
    	$suf = "AM";
    } else {
    	$hr -= 12;
    	$suf = "PM";
    }
    $min = substr($timestr,14,2);
    $sec = substr($timestr,17,2);
    
    return $dt.' '.$hr.':'.$min.':'.$sec.' '.$suf;
}

?>
