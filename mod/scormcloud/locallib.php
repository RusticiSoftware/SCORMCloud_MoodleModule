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
 * logic, should go here. Never include this file from your lib.php!
 *
 * @package   mod_scormcloud
 * @copyright 2011 Rustici Software
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once('lib.php');
require_once('lib/KLogger.php');

require_once('SCORMCloud_PHPLibrary/ScormEngineService.php');
require_once('SCORMCloud_PHPLibrary/ServiceRequest.php');
require_once('SCORMCloud_PHPLibrary/CourseData.php');

global $log;
$log = new KLogger('/tmp', KLogger::DEBUG);

/**
 * Handles the display of the SCORM Cloud course format.
 *
 * @param unknown_type $user
 * @param unknown_type $course
 */
function scormcloud_course_format_display($user, $course)
{
	global $CFG;
	global $log;

	$courseid = $course->id;

	$strupdate = get_string('update');
	$strmodule = get_string('modulename','scormcloud');
	$context = get_context_instance(CONTEXT_COURSE, $courseid);

	if ($scormclouds = get_all_instances_in_course('scormcloud', $course)) {
		notify("Clouds!");

		$scormcloud = get_scormcloud_instance($course);

		if ($courseExists = scormcloud_course_exists_on_cloud($scormcloud->id)){
			notify("Course exists on Cloud!");
		} else {
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
	} else {
		if (has_capability('moodle/course:manageactivities', $context)) {

			if (has_capability('moodle/course:update', $context)) {
				// Create a new activity
				redirect($CFG->wwwroot.'/course/mod.php?id='.$course->id.'&amp;section=0&sesskey='.sesskey().'&amp;add=scormcloud');
			} else {
				notify('Could not find a scormcloud course here');
			}
		}
	}
}

function get_scormcloud_instance($course)
{
	global $DB;

	if ($course->format == "weeks" || $course->format == "topics") {
		$id = optional_param('id', 0, PARAM_INT);

		if (!empty($id)) {
			if (! $cm = get_coursemodule_from_id('scormcloud', $id)) {
				error("Course Module ID was incorrect");
			}
		}

		$scormcloud = $DB->get_record("scormcloud","id","$cm->instance");
	} else {
		$scormcloud = current($scormclouds);
	}

	return $scormcloud;
}

function scormcloud_course_exists_on_cloud($courseid)
{
	global $CFG;
	global $log;
	
	$log->logInfo('URL: '.$CFG->scormcloud_serviceurl.', AppID: '.$CFG->scormcloud_appid.', Key: '.$CFG->scormcloud_secretkey);

	$ScormService = new ScormEngineService($CFG->scormcloud_serviceurl, $CFG->scormcloud_appid, $CFG->scormcloud_secretkey);
	$courseService = $ScormService->getCourseService();

	$allResults = $courseService->GetCourseList($courseid);

	$courseExists = false;
	foreach ($allResults as $course)
	{
		if ($course->getCourseId() == $courseid)
		{
			$courseExists = true;
			break;
		}
	}

	return $courseExists;
}