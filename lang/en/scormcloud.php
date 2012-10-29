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
 * English strings for scormcloud
 *
 * @package   mod_scormcloud
 * @copyright 2011 Rustici Software
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['scormcloud'] = 'SCORM Cloud';

$string['modulename'] = 'SCORM Cloud Course';
$string['modulenameplural'] = 'SCORM Cloud Courses';
$string['pluginname'] = 'SCORM Cloud';
$string['pluginadministration'] = 'SCORM Cloud Administration';
$string['scormcloudfieldset'] = 'Custom example fieldset';
$string['scormcloudintro'] = 'scormcloud Intro';
$string['scormcloudname'] = 'SCORM Package Name';
$string['uploadpackage'] = 'SCORM Package File';
$string['uploadinfo'] = 'Select the SCORM package to upload to the SCORM Cloud. After you click Save, it will be uploaded to the Cloud and the Moodle activity title will be inferred from the package manifest.';

$string['serviceurl'] = 'SCORM Cloud Service Url';
$string['serviceurl_desc'] = 'Enter the SCORM Cloud Service Url provided for your account.';
$string['appid'] = 'AppId';
$string['appid_desc'] = 'Enter the AppId provided for your account.';
$string['secretkey'] = 'Secret Key';
$string['secretkey_desc'] = 'Enter the Secret Key provided for your account.';

$string['explaingeneralconfig'] = 'Enter your SCORM Cloud Engine API information below.<br>If you dont have a key yet, click <a href="https://cloud.scorm.com/sc/guest/SignUpForm" target="_blank">here</a> to sign up and receive your appid and secret key via email.';
$string['generalconfig'] = 'SCORM Cloud API Configuration';

$string['coursemissing'] = 'This course appears to be missing from the SCORM Cloud.';
$string['coursemissing_admin'] = '<p>This course appears to be missing from the SCORM Cloud. This can happen if you delete it from your Course Library in your SCORM Cloud account or if you change the AppID settings your SCORM Cloud plugin settings in your Moodle administration section.</p><p>Update this SCORM Cloud Course and upload a new package to fix this, or simply remove the activity.</p>';

$string['launchmessage'] = 'Your course is being launched in a new window. This window will automatically close once you exit the course.';

$string['estimatedduration'] = "Estimated Duration";

$string['importerror'] = 'There was a problem uploading and importing your course to the SCORM Cloud: $a';

$string['noregmessage'] = 'You have not attempted this course yet.<br/> Click <strong>Launch Course</strong> above to begin.';

$string['completiontext'] = 'completion';
$string['satisfactiontext'] = 'satisfaction';
$string['totaltimetext'] = 'total time';
$string['scoretext'] = 'score';


$string['completed'] = 'completed';
$string['incomplete'] = 'incomplete';
$string['passed'] = 'passed';
$string['failed'] = 'failed';
$string['unknown'] = 'unknown';

$string['scormcloud:launch'] = 'Launch SCORM Cloud courses';
$string['scormcloud:manage'] = 'Manage SCORM Cloud courses';