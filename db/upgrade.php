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
 * This file keeps track of upgrades to the scormcloud module
 *
 * Sometimes, changes between versions involve alterations to database
 * structures and other major things that may break installations. The upgrade
 * function in this file will attempt to perform all the necessary actions to
 * upgrade your older installation to the current version. If there's something
 * it cannot do itself, it will tell you what you need to do.  The commands in
 * here will all be database-neutral, using the functions defined in DLL libraries.
 *
 * @package   mod_scormcloud
 * @copyright 2011 Rustici Software
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once('../config.php');
require_once($CFG->dirroot . '/mod/scormcloud/lib.php');

/**
 * xmldb_scormcloud_upgrade
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_scormcloud_upgrade($oldversion) {
    global $DB;
    global $CFG;

    $dbman = $DB->get_manager();

    $module = new stdClass();
    require($CFG->dirroot . '/mod/scormcloud/version.php');

    $result = true;

    if ($result && $oldversion < 2011100700) {
        $table = new xmldb_table('scormcloud');
        $field = new xmldb_field('cloudid', XMLDB_TYPE_CHAR, '255', null, 'XMLDB_NULL', null, null, 'id');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $rs = $DB->get_recordset('scormcloud');
        foreach ($rs as $record) {
            if (!isset($record->cloudid) || empty($record->cloudid)) {
                $record->cloudid = $record->id;
                $DB->update_record('scormcloud', $record, true);
            }
        }
        $rs->close();

        $field->setNotNull();
        $dbman->change_field_notnull($table, $field);
    }

    upgrade_mod_savepoint($result, $module->version, 'scormcloud');

    return $result;
}
