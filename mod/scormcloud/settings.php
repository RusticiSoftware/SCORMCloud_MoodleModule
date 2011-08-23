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

$settings->add(new admin_setting_heading('scormcloud_method_heading', get_string('generalconfig', 'scormcloud'),
                   get_string('explaingeneralconfig', 'scormcloud'), 'Missing Text'));

$settings->add(new admin_setting_configtext('scormcloud_serviceurl', get_string('serviceurl', 'scormcloud'),
                   get_string('serviceurl_desc', 'scormcloud'), null));

$settings->add(new admin_setting_configtext('scormcloud_appid', get_string('appid', 'scormcloud'),
                   get_string('appid_desc', 'scormcloud'), null));

$settings->add(new admin_setting_configtext('scormcloud_secretkey', get_string('secretkey', 'scormcloud'),
                   get_string('secretkey_desc', 'scormcloud'), null));

?>
