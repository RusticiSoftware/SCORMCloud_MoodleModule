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

// format.php - course format featuring single activity
//              included from view.php

$module = $course->format;
require_once($CFG->dirroot.'/mod/'.$module.'/locallib.php');

$strgroups  = get_string('groups');
$strgroupmy = get_string('groupmy');
$editing    = $PAGE->user_is_editing();

$moduleformat = $module.'_course_format_display';
if (function_exists($moduleformat)) {
	$moduleformat($USER,$course);
} else {
	echo $OUTPUT->notification('The module '. $module. ' does not support single activity course format');
}
