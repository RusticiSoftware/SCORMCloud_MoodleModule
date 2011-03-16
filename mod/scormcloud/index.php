<?php // $Id: index.php,v 1.25.2.4 2008/02/20 06:18:52 moodler Exp $

    require_once("../../config.php");
    require_once("locallib.php");

    $id = required_param('id', PARAM_INT);   // course id

    if (!empty($id)) {
        if (! $course = get_record("course", "id", $id)) {
            error("Course ID is incorrect");
        }
    } else {
        error('A required parameter is missing');
    }

    require_course_login($course);

    add_to_log($course->id, "scormcloud", "view all", "index.php?id=$course->id", "");

    $strscormcloud = get_string("modulename", "scormcloud");
    $strscormclouds = get_string("modulenameplural", "scormcloud");
    $strweek = get_string("week");
    $strtopic = get_string("topic");
    $strname = get_string("name");
    $strsummary = get_string("summary");
    $strreport = get_string("report",'scormcloud');
    $strlastmodified = get_string("lastmodified");

    $navlinks = array();
    $navlinks[] = array('name' => $strscormclouds, 'link' => '', 'type' => 'activity');
    $navigation = build_navigation($navlinks);

    print_header_simple("$strscormclouds", "", $navigation,
                 "", "", true, "", navmenu($course));

    if ($course->format == "weeks" or $course->format == "topics") {
        $sortorder = "cw.section ASC";
    } else {
        $sortorder = "m.timemodified DESC";
    }

    if (! $scormclouds = get_all_instances_in_course("scormcloud", $course)) {
        notice(get_string('thereareno', 'moodle', $strscormclouds), "../../course/view.php?id=$course->id");
        exit;
    }

    if ($course->format == "weeks") {
        $table->head  = array ($strweek, $strname);
        $table->align = array ("center", "left", "left", "left");
    } else if ($course->format == "topics") {
        $table->head  = array ($strtopic, $strname);
        $table->align = array ("center", "left", "left", "left");
    } else {
        $table->head  = array ($strlastmodified, $strname);
        $table->align = array ("left", "left", "left", "left");
    }

    foreach ($scormclouds as $scormcloud) {

        $context = get_context_instance(CONTEXT_MODULE,$scormcloud->coursemodule);
        $tt = "";
        if ($course->format == "weeks" or $course->format == "topics") {
            if ($scormcloud->section) {
                $tt = "$scormcloud->section";
            }
        } else {
            $tt = userdate($scormcloud->timemodified);
        }
        $report = '&nbsp;';
        $reportshow = '&nbsp;';
        if (has_capability('mod/scormcloud:viewreport', $context)) {
	/*
            $trackedusers = scormcloud_get_count_users($scormcloud->id, $scormcloud->groupingid);
            if ($trackedusers > 0) {
                $reportshow = '<a href="report.php?id='.$scormcloud->coursemodule.'">'.get_string('viewallreports','scormcloud',$trackedusers).'</a></div>';
            } else {
                $reportshow = get_string('noreports','scormcloud');
            }
*/
			$reportshow = '<a href="'.$CFG->wwwroot.'/mod/scormcloud/LaunchReportage.php?courseid='.$scormcloud->id.'" target="_blank" >View Reports for this Course</a>';
        } else if (has_capability('mod/scormcloud:viewscores', $context)) {
	/*
            require_once('locallib.php');
            $report = scormcloud_grade_user($scormcloud, $USER->id);
            $reportshow = get_string('score','scormcloud').": ".$report;
*/
        }
        if (!$scormcloud->visible) {
           //Show dimmed if the mod is hidden
           $table->data[] = array ($tt, "<a class=\"dimmed\" href=\"view.php?id=$scormcloud->coursemodule\">".format_string($scormcloud->name)."</a>");
        } else {
           //Show normal if the mod is visible
           $table->data[] = array ($tt, "<a href=\"view.php?id=$scormcloud->coursemodule\">".format_string($scormcloud->name)."</a>");
        }

    }

    echo "<br />";

    print_table($table);

    print_footer($course);

?>
