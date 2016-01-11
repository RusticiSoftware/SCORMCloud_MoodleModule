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
 * Library of interface functions and constants for module scormcloud
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 * All the scormcloud specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package   mod_scormcloud
 * @copyright 2011 Rustici Software
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function scormcloud_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_INTRO: return false;
        default: return null;
    }
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $scormcloud An object from the form in mod_form.php
 * @return int The id of the newly inserted scormcloud record
 */
function scormcloud_add_instance($scormcloud, $mform=null) {
    global $DB;

    $scormcloud->timecreated = time();

    if ($mform) {
        $packagefile = $mform->save_temp_file('packagefile');
        if ($packagefile !== false) {
            require_once('locallib.php');
            $cloud = scormcloud_get_service();
            $course_service = $cloud->getCourseService();

            $scormcloud->cloudid = scormcloud_gen_uuid();

            $results = $course_service->ImportCourse($scormcloud->cloudid, $packagefile);
            unlink($packagefile);

            $result = current($results);
            if (!$result->getWasSuccessful()) {
                print_error('importerror', 'scormcloud', $result->getMessage());
            }

            $scormcloud->name = $result->getTitle();
        }
    }

    return $DB->insert_record('scormcloud', $scormcloud);
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $scormcloud An object from the form in mod_form.php
 * @return boolean Success/Fail
 */
function scormcloud_update_instance($scormcloud, $mform=null) {
    global $DB;

    $scormcloud = $DB->get_record('scormcloud', array('id' => $scormcloud->instance));
    $scormcloud->timemodified = time();

    if ($mform) {
        $packagefile = $mform->save_temp_file('packagefile');
        if ($packagefile !== false) {
            require_once('locallib.php');
            $cloud = scormcloud_get_service();
            $course_service = $cloud->getCourseService();

            // PHP lib is somewhat out-of-sync with Cloud API. versionCourse no longer exists,
            // use ImportCourse instead which auto-versions.
            $results = $course_service->ImportCourse($scormcloud->cloudid, $packagefile);
            unlink($packagefile);

            $result = current($results);
            if (!$result->getWasSuccessful()) {
                print_error('importerror', 'scormcloud', $result->getMessage());
            }

            $scormcloud->name = $result->getTitle();
        }
    }

    return $DB->update_record('scormcloud', $scormcloud);
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function scormcloud_delete_instance($id) {
    global $DB;

    if (! $scormcloud = $DB->get_record('scormcloud', array('id' => $id))) {
        return false;
    }

    $DB->delete_records('scormcloud', array('id' => $scormcloud->id));

    return true;
}

/**
 * Return a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return null
 * @todo Finish documenting this function
 */
function scormcloud_user_outline($course, $user, $mod, $scormcloud) {
    $return = new stdClass;
    $return->time = 0;
    $return->info = '';
    return $return;
}

/**
 * Print a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @return boolean
 * @todo Finish documenting this function
 */
function scormcloud_user_complete($course, $user, $mod, $scormcloud) {
    return true;
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in scormcloud activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @return boolean
 * @todo Finish documenting this function
 */
function scormcloud_print_recent_activity($course, $viewfullnames, $timestart) {
    return false;  // True if anything was printed, otherwise false.
}

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @return boolean
 * @todo Finish documenting this function
 **/
function scormcloud_cron () {
    return true;
}

/**
 * Must return an array of users who are participants for a given instance
 * of scormcloud. Must include every user involved in the instance,
 * independient of his role (student, teacher, admin...). The returned
 * objects must contain at least id property.
 * See other modules as example.
 *
 * @param int $scormcloudid ID of an instance of this module
 * @return boolean|array false if no participants, array of objects otherwise
 */
function scormcloud_get_participants($scormcloudid) {
    return false;
}


function scormcloud_install() {
    return true;
}

/**
 * Execute post-uninstall custom actions for the module
 * This function was added in 1.9
 *
 * @return boolean true if success, false on error
 */
function scormcloud_uninstall() {
    return true;
}

function scormcloud_get_user_data($uid) {
    global $DB;

    return $DB->get_record('user', array('id' => $uid));
}
