<?php  // $Id: view.php,v 1.6 2007/09/03 12:23:36 jamiesensei Exp $
/**
 * This page prints a particular instance of rusticiscormengine
 *
 * @author
 * @version $Id: view.php,v 1.6 2007/09/03 12:23:36 jamiesensei Exp $
 * @package rusticiscormengine
 **/


    require_once("../../config.php");
    require_once("lib.php");
    require_once("locallib.php");

	require_once('SCORMAPI/ScormEngineService.php');
	require_once('SCORMAPI/ServiceRequest.php');
	require_once('SCORMAPI/CourseData.php');

    $id = optional_param('id', 0, PARAM_INT); // Course Module ID, or
    $a  = optional_param('a', 0, PARAM_INT);  // scormcloud ID

    if (!empty($id)) {
        if (! $cm = get_coursemodule_from_id('scormcloud', $id)) {
            error("Course Module ID was incorrect");
        }
        if (! $course = get_record("course", "id", $cm->course)) {
            error("Course is misconfigured");
        }
        if (! $scormcloud = get_record("scormcloud", "id", $cm->instance)) {
            error("Course module is incorrect");
        }
    } else if (!empty($a)) {
        if (! $scormcloud = get_record("scormcloud", "id", $a)) {
            error("Course module is incorrect");
        }
        if (! $course = get_record("course", "id", $scormcloud->course)) {
            error("Course is misconfigured");
        }
        if (! $cm = get_coursemodule_from_instance("scormcloud", $scormcloud->id, $course->id)) {
            error("Course Module ID was incorrect");
        }
    } else {
        error('A required parameter is missing');
    }

	$module = $course->format;
    
	require_login($course->id);

    add_to_log($course->id, "scormcloud", "view", "view.php?id=$cm->id", "$scormcloud->id");

/// Print the page header
    $strscormclouds = get_string("modulenameplural", "scormcloud");
    $strscormcloud  = get_string("modulename", "scormcloud");

    $navlinks = array();
    $navlinks[] = array('name' => $strscormclouds, 'link' => "index.php?id=$course->id", 'type' => 'activity');
    $navlinks[] = array('name' => format_string($scormcloud->name), 'link' => '', 'type' => 'activityinstance');

    $navigation = build_navigation($navlinks);

    print_header_simple(format_string($scormcloud->name), "", $navigation, "", "", true,
                  update_module_button($cm->id, $course->id, $strscormcloud), navmenu($course, $cm));

/// Print the main part of the page

     $moduleformat = 'scormcloud_course_format_display';
 if (function_exists($moduleformat)) {
     $moduleformat($USER,$course);
 } else { 
     notify('The module '. $module. ' does not support this stuff...yet...');
 }
	
/*
 		echo '<script language="JavaScript">';
        echo '        function resize_iframe() {';
        echo '               var height=window.innerWidth;';
        echo '               if (document.body.clientHeight)';
        echo '                {';
        echo '                       height=document.body.clientHeight;';
        echo '               }';
        echo '               document.getElementById("frmRusticiPlayer").style.height=parseInt(height-document.getElementById("frmRusticiPlayer").offsetTop-8)+"px"; ';    
        echo '        }';
        //echo '        window.onresize=resize_iframe; ';
        echo '</script>';
        
        //echo '<iframe id="frmRusticiPlayer" onload="resize_iframe()" src="http://10.0.1.200/SCORMEngine/MoodleNET/default.aspx?configuration=MoodlePackageId|'.$scormcloud->id.'!MoodleURL|'.urlencode($CFG->wwwroot).'!UserId|'.$user->id.'&registration=UserId|'.$user->id.'!MoodlePackageId|'.$scormcloud->id.'" width="100%" frameborder="0" />';
        $iframecode = '<iframe id="frmRusticiPlayer" onload="resize_iframe()" src="'.$CFG->engineurl;'default.aspx';
        $iframecode .= '?configuration=MoodlePackageId|'.$scormcloud->id.'!MoodleURL|'.urlencode($CFG->wwwroot).'!UserId|'.$USER->id;
		$iframecode .= '&registration=UserId|'.$USER->id.'!MoodlePackageId|'.$scormcloud->id.'"';
		$iframecode .= ' width="100%" height="600" frameborder="0" />';

		echo $iframecode;

*/

/// Finish the page
    print_footer($course);
?>
