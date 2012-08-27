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
 * Prints a particular instance of scormcloud
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package   mod_scormcloud
 * @copyright 2011 Rustici Software
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once("lib.php");
require_once("locallib.php");

require_once('SCORMCloud_PHPLibrary/ScormEngineService.php');
require_once('SCORMCloud_PHPLibrary/ServiceRequest.php');
require_once('SCORMCloud_PHPLibrary/CourseData.php');

global $DB;
$id = optional_param('id', 0, PARAM_INT); // Course Module ID, or
$a  = optional_param('a', 0, PARAM_INT);  // scormcloud ID.

if (!empty($id)) {
    $cm = get_coursemodule_from_id('scormcloud', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record("course", array("id" => $cm->course), '*', MUST_EXIST);
    $scormcloud = $DB->get_record('scormcloud', array("id" => $cm->instance), '*', MUST_EXIST);
} else if (!empty($a)) {
    $scormcloud = $DB->get_record('scormcloud', array("id" => $a), '*', MUST_EXIST);
    $course = $DB->get_record("course", array('id' => $scormcloud->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('scormcloud', $scormcloud->id, $course->id, false, MUST_EXIST);
} else {
    error('A required parameter is missing');
}

require_login($course->id);

add_to_log($course->id, "scormcloud", "view", "view.php?id=$cm->id", "$scormcloud->id");

// Print the page header.
$strscormclouds = get_string("modulenameplural", "scormcloud");
$strscormcloud  = get_string("modulename", "scormcloud");

$PAGE->set_url('/mod/scormcloud/view.php', array('id' => $cm->id));
$PAGE->set_title($scormcloud->name);
$PAGE->set_button($OUTPUT->update_module_button($cm->id, 'scormcloud'));
$PAGE->set_heading($course->shortname);
echo $OUTPUT->header();

// Print the main part of the page.
scormcloud_course_format_display($USER, $course);

// Finish the page.
echo $OUTPUT->footer();