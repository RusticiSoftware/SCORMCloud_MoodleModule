<?php

/*
 *   Copyright 2011 Rustici Software.
 *
 *   This file is part of the SCORM Cloud Module for Moodle.
 *   https://github.com/RusticiSoftware/SCORMCloud_MoodleModule
 *   http://scorm.com/moodle/
 *
 *   The SCORM Cloud Module is free software: you can redistribute it and/or
 *   modify it under the terms of the GNU General Public License as published
 *   by the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   The SCORM Cloud Module is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with the SCORM Cloud Module.  If not, see <http://www.gnu.org/licenses/>.
 */


/**
 * Internal library of functions for module scormcloud
 *
 * All the scormcloud specific functions, needed to implement the module
 * logic, should go here.
 *
 * @package   mod_scormcloud
 * @copyright 2011 Rustici Software
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG; //needed as locallib.php is included by add/update_instance functions.
require_once($CFG->dirroot.'/mod/scormcloud/lib.php');
require_once('lib/KLogger.php');

require_once('SCORMCloud_PHPLibrary/ScormEngineService.php');
require_once('SCORMCloud_PHPLibrary/ServiceRequest.php');
require_once('SCORMCloud_PHPLibrary/CourseData.php');
require_once('SCORMCloud_PHPLibrary/ScormEngineUtilities.php');
require_once('ui/ReportageUI.php');

global $log;
$log = new KLogger('/tmp', KLogger::DEBUG);

function scormcloud_get_service() {
    global $CFG;
    global $log;
    $plugin = new stdClass();
    require('version.php');

    $origin = ScormEngineUtilities::getCanonicalOriginString('Rustici Software', 'Moodle', '2.0-' . $plugin->version);

    $log->logDebug("Building ScormEngineService with origin = $origin");
    return new ScormEngineService($CFG->scormcloud_serviceurl, $CFG->scormcloud_appid, $CFG->scormcloud_secretkey, $origin);
}

/**
 * Handles the display of the SCORM Cloud course format.
 *
 * @param unknown_type $user
 * @param unknown_type $course
 */
function scormcloud_course_format_display($user, $course) {
    global $CFG, $DB, $OUTPUT, $log;

    $context = context_course::instance($course->id);

    echo '<script type="text/javascript" ';
    echo "src=\"{$CFG->wwwroot}/mod/scormcloud/scripts/jquery-1.3.2.min.js\"></script>\n";
    // Include jquery.thickbox for our use.
    echo '<script type="text/javascript" ';
    echo "src=\"{$CFG->wwwroot}/mod/scormcloud/scripts/thickbox-compressed.js\"></script>\n";
    echo '<link rel="stylesheet" ';
    echo "href=\"{$CFG->wwwroot}/mod/scormcloud/scripts/thickbox.css\" type=\"text/css\" media=\"screen\" />\n";
    // Include our rustici.moodle.cloud.js file.
    echo '<script type="text/javascript" ';
    echo "src=\"{$CFG->wwwroot}/mod/scormcloud/scripts/rustici.moodle.cloud.js\"></script>\n";
    echo '<script type="text/javascript" ';
    echo "src=\"{$CFG->wwwroot}/mod/scormcloud/scripts/table2CSV.js\"></script>\n";
    echo '<script type="text/javascript" ';
    echo "src=\"//cloud.scorm.com/Reportage/scripts/reportage.combined.nojquery.js\"></script>\n";
    echo '<link rel="stylesheet" ';
    echo "href=\"{$CFG->wwwroot}/mod/scormcloud/css/reportage.combined.css\" type=\"text/css\" media=\"screen\" />\n";
    echo '<link rel="stylesheet" ';
    echo "href=\"{$CFG->wwwroot}/mod/scormcloud/css/scormcloud.css\" type=\"text/css\" media=\"screen\" />\n";

    if ($scormclouds = get_all_instances_in_course('scormcloud', $course)) {
        $scormcloud = get_scormcloud_instance($course);

        $scormservice = scormcloud_get_service();
        $rptservice = $scormservice->getReportingService();

        $rptauth = $rptservice->GetReportageAuth('FREENAV', true);

        $reportageui = new ReportageUI($rptservice, $rptauth, $scormcloud, $user);

        if ($courseexists = scormcloud_course_exists_on_cloud($scormcloud->cloudid)) {
            $containercontext = (object)array('scormcloud' => $scormcloud,
                                    'course' => $course,
                                    'user' => $user,
                                    'context' => $context);
            print_header_container($containercontext);
            print_menu_container($containercontext);

            $regs = $DB->get_records_select('scormcloud_registrations', 'userid='.$user->id.' AND scormcloudid='.$scormcloud->id);
            $log->logDebug('Found ' . count($regs) . ' registrations for user '.$user->id.' on scormcloudid '.$scormcloud->id);

            $currentregistration = null;
            foreach ($regs as $reg) {
                if ($reg->scormcloudid==$scormcloud->id) {
                    $regid = $reg->regid;
                    $currentregistration = $reg;
                }
            }

            print_currentstatus_container($course, $currentregistration, $reportageui);

            if (has_capability('moodle/course:manageactivities', $context)) { // Only show if the user is an admin.
                print_reportage_container($reportageui);
            }
        } else {
            echo '<br><br><br>';
            if (has_capability('moodle/course:manageactivities', $context)) {
                print_message_container(get_string('coursemissing_admin', 'scormcloud'));
            } else {
                print_message_container(get_string('coursemissing', 'scormcloud'));
            }
        }
    } else {
        if (has_capability('moodle/course:manageactivities', $context)) {

            if (has_capability('moodle/course:update', $context)) {
                // Create a new activity.
                redirect($CFG->wwwroot.'/course/mod.php?id='.$course->id.'&amp;section=0&sesskey='.sesskey().'&amp;add=scormcloud');
            } else {
                echo $OUTPUT->notification('Could not find a scormcloud course here');
            }
        }
    }
}

function get_scormcloud_instance($course) {
    global $DB;

    $scormclouds = get_all_instances_in_course('scormcloud', $course);
    if ($course->format == "weeks" || $course->format == "topics") {
        $id = optional_param('id', 0, PARAM_INT);

        if (!empty($id)) {
            if (! $cm = get_coursemodule_from_id('scormcloud', $id)) {
                error("Course Module ID was incorrect");
            }
        }

        $scormcloud = $DB->get_record("scormcloud", array("id" => $cm->instance));
    } else {
        $scormcloud = current($scormclouds);
    }

    return $scormcloud;
}

function scormcloud_course_exists_on_cloud($cloudid) {
    global $CFG, $log;

    $log->logInfo('URL: '.$CFG->scormcloud_serviceurl.', AppID: '.$CFG->scormcloud_appid.', Key: '.$CFG->scormcloud_secretkey);

    $scormservice = scormcloud_get_service();
    $courseservice = $scormservice->getCourseService();

    $allresults = $courseservice->GetCourseList($cloudid);

    $courseexists = false;
    foreach ($allresults as $cloudcourse) {
        if ($cloudcourse->getCourseId() == $cloudid) {
            $courseexists = true;
            break;
        }
    }

    return $courseexists;
}

function scormcloud_get_coursemodule($id) {
    $cms = get_coursemodules_in_course('scormcloud', $id);
    return current($cms);
}

function scormcloud_hascapabilitytoviewcourse($id) {
    $context = context_course::instance($id);
    return has_capability('moodle/course:view', $context);
}

function scormcloud_hascapabilitytolaunch($id) {
    $context = context_module::instance(scormcloud_get_coursemodule($id)->id);
    return has_capability('mod/scormcloud:launch', $context);
}

function scormcloud_hascapabilitytomanage($id) {
    $context = context_module::instance(scormcloud_get_coursemodule($id)->id);
    return has_capability('mod/scormcloud:manage', $context);
}

function scormcloud_getSatVal($val) {
    switch($val) {
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

function scormcloud_getComplVal($val) {
    switch($val) {
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

function scormcloud_getSatVal2($stat, $val) {
    if ($stat == 'true') {
        if ($val == 'true') {
            return "passed";
        } else {
            return "failed";
        }
    } else {
        return "unknown";
    }
}

function scormcloud_getComplVal2($stat, $val) {
    if ($stat == 'true') {
        if ($val == 'true') {
            return "completed";
        } else {
            return "incomplete";
        }
    } else {
        return "unknown";
    }
}
// TODO: there's probably a much better way to do these translations.
function scormcloud_getsat($val) {
    switch((string)$val) {
        case 'passed':
            return 1;
        case 'failed':
            return 2;
        default:
            return 0;
    }
}

function scormcloud_getcomp($val) {
    switch ((string)$val) {
        case 'complete':
            return 1;
        case 'incomplete':
            return 2;
        default:
            return 0;
    }
}
/**
 * Update/create grade item for user/ course
 *
 * @param string uid
 * @param string pid
 * @param string rawscore; 'reset' means reset grades in gradebook
 * @return object grade_item
 */
function scormcloud_grade_item_update($uid, $pid, $rawscore) {
    global $CFG, $DB, $log;
    $rawscore = (int)$rawscore; // Rawscore comes from an xml call - force it as an int to make sure it's correct.
    if (!function_exists('grade_update')) { // Workaround for buggy PHP versions.
        require_once($CFG->libdir.'/gradelib.php');
    }

    $log->logDebug("GradeAPI - UID=$uid - PID=$pid - RAWSCORE=$rawscore");

    if ($scormcloud = $DB->get_record("scormcloud", array('id' => $pid))) {
        $coursetitle = $scormcloud->name;

        if ($rawscore=='reset') {
            $grades = 'reset';
        } else {
            $grades = array('userid' => $uid, 'rawgrade' => $rawscore);

            $params = array('itemname' => $coursetitle, 'idnumber' => $scormcloud->id);
            $params['gradetype'] = GRADE_TYPE_VALUE;
            $params['grademax']  = 100;
            $params['grademin']  = 0;
        }

        if ($grades  === 'reset') {
            $params['reset'] = true;
            $grades = null;
        }

        $log->logDebug("GradeAPI - UID=$uid - PID=$pid - COURSE=$scormcloud->course - RAWSCORE=$rawscore - CourseTitle=$coursetitle");

        return grade_update('mod/scormcloud', $scormcloud->course, 'mod', 'scormcloud', $pid, 0, $grades, $params);
    } else {
        return false;
    }
}

function print_header_container($containercontext) {
    global $OUTPUT;
    $headertext = '<div class="headingblock header"><h2>'.format_string($containercontext->scormcloud->name).' - '.$containercontext->course->fullname.'</h2></div>';
    echo $OUTPUT->container($headertext, 'scormcloud-container');
}

function print_menu_container($containercontext) {
    global $CFG, $OUTPUT;

    $headertext = '<div style="font-size:medium; border: 1px solid #DDDDDD;"><table style="width:100%; margin-bottom: 0;"><tr><td>';
    $headertext .= '&nbsp;&nbsp;<a class="thickbox" href="'.$CFG->wwwroot . '/mod/scormcloud/launch.php?courseid='.
        $containercontext->scormcloud->id.'&userid='.$containercontext->user->id.'&TB_iframe=true" target="_blank" >Launch Course</a>';
    $headertext .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
    $headertext .= '<a class="thickbox" href="'.$CFG->wwwroot . '/mod/scormcloud/launch.php?mode=preview&courseid='.
        $containercontext->scormcloud->id.'&userid='.$containercontext->user->id.'&TB_iframe=true" target="_blank" >Preview Course</a>';
    $headertext .= '</td><td style="text-align:right">';
    if (has_capability('moodle/course:manageactivities', $containercontext->context)) { // Only show if the user is an admin.
        $headertext .= '<a class="thickbox" href="'.$CFG->wwwroot.'/mod/scormcloud/packageprops.php?id='.
            $containercontext->scormcloud->id.'&TB_iframe=true&height=500&width=700" id="lnkPackageProperties">Package Properties</a>';
    }
    $headertext .= '</td></tr></table></div>';
    echo $OUTPUT->container($headertext, 'scormcloud-container');
}

function print_registration_container($reg) {
    $currentstatus = '<div class="reportage"><table>
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
    return $currentstatus;
}

function print_currentstatus_container($course, $registration, $reportageui) {
    global $OUTPUT;
    if ($registration == null) {
        $html = '<div class="content" style="font-size: medium; border: 1px solid #DDDDDD;">' . format_string($course->summary);
        $html .= '<table><tr>';
        $html .= '<td><h1 style="font-size: xx-large;">You have not yet started this course</h3></td>';
        $html .= '</tr></table></div>';
        echo $OUTPUT->container($html, 'scormcloud-container');
        return;
    }

    $headertext = '<div class="content" style="font-size: medium; border: 1px solid #DDDDDD;">' . format_string($course->summary);
    $headertext .= '<table><tr>';
    $headertext .= '<td><h1 style="font-size: xx-large;">Your Current Status</h3>' . print_registration_container($registration) . '</td>';
    $headertext .= '<td style="padding-left: 40px;"><div id="UserActivities" style="border:1px #CCCCCC solid;padding:10px 10px 10px 10px">Loading Your Activities...</div></td>';
    $headertext .= '</tr></table></div>';

    $headertext .= '<script type="text/javascript">';
    $headertext .= '$(document).ready(function(){';
    // $headertext .= '	loadScript("'.$userSummaryUrl.'");';
    $headertext .= '	loadScript("'.$reportageui->getUserActivitiesUrl().'");';
    $headertext .= '});';
    $headertext .= '</script>';
    echo $OUTPUT->container($headertext, 'scormcloud-container');
}

function print_reportage_container($reportageui) {
    global $CFG, $OUTPUT;

    $headertext  = '<div style="border: 1px solid #DDDDDD;">';
    $headertext .= '<fieldset style="padding:10px 10px 10px 10px">';
    $headertext .= '<legend style="font-size:xx-large;margin:5px 5px 5px 5px;">';
    $headertext .= '&nbsp;&nbsp;<img src="'.$CFG->wwwroot.'/mod/scormcloud/pix/icon.gif" />';
    $headertext .= '&nbsp;&nbsp;Course Analytics&nbsp;&nbsp;</legend>';
    $headertext .= '<table style="width:90%"><tr style="padding-bottom:10px;"><td colspan="2">';
    $headertext .= '<div id="CourseSummary" style="width:500px;">Loading Summary...</div>';
    $headertext .= '<br></td></tr>';
    $headertext .= '<tr><td style="vertical-align:top;">';
    //  All Courses Detail Widget.
    $headertext .= '<div id="CourseListDiv" style="border:1px #CCCCCC solid;padding:10px 10px 10px 10px;width:450px">';
    $headertext .= '<div id="CourseListData" >Loading All Courses...</div>';
    $headertext .= '</div>';
    $headertext .= '<br></td>';
    $headertext .= '</tr><tr><td style="vertical-align:top;">';
    // All Learners Detail Widget.
    $headertext .= '<div id="LearnersListDiv" style="border:1px #CCCCCC solid;padding:10px 10px 10px 10px;width:450px;">';
    $headertext .= '<div id="LearnersListData">Loading All Learners...</div>';
    $headertext .= '</td></tr></table></fieldset></div>';
    // Load 'em Up.
    $headertext .= '<script type="text/javascript">';
    $headertext .= '$(document).ready(function(){';
    $headertext .= '	loadScript("'.$reportageui->getCourseSummaryUrl().'");';
    $headertext .= '	loadScript("'.$reportageui->getCourseListUrl().'");';
    $headertext .= '	loadScript("'.$reportageui->getLearnerListUrl().'");';
    $headertext .= '});';
    $headertext .= '</script>';

    echo $OUTPUT->container($headertext, 'scormcloud-container');
}

function print_message_container($message) {
    global $CFG, $OUTPUT;

    $html  = '<div style="width: 600px; margin-left: auto; margin-right: auto;>';
    $html .= '<div style="width: 120px; float: left;"><img src="' . $CFG->wwwroot . '/mod/scormcloud/images/icon_cloud.jpg" style="margin-top: 40px;" /></div>';
    $html .= '<div style="width: 479px; float: right;">';
    $html .= '<h3>SCORM Cloud Plugin</h3>';
    $html .= '<div>'. $message . '</div>';
    $html .= '</div></div>';

    echo $OUTPUT->container($html, 'scormcloud-container');
}

// Generates a v4 (random) UUID.
function scormcloud_gen_uuid() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low".
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

        // 16 bits for "time_mid".
        mt_rand( 0, 0xffff ),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4.
        mt_rand( 0, 0x0fff ) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1.
        mt_rand( 0, 0x3fff ) | 0x8000,

        // 48 bits for "node".
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}
