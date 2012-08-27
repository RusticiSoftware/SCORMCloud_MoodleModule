<?php

require_once("../../config.php");
global $CFG, $log;

require_once($CFG->dirroot.'/lib/datalib.php');
require_once($CFG->dirroot.'/lib/accesslib.php');
require_once("locallib.php");
require_once('SCORMCloud_PHPLibrary/ScormEngineService.php');
require_once('SCORMCloud_PHPLibrary/ServiceRequest.php');
require_once('SCORMCloud_PHPLibrary/CourseData.php');

$regid = required_param("regid", PARAM_ALPHANUM);
require_login();
if ($regid != null) {
    $log->logInfo('rollupregistration.php called : regid = ' . $regid);

    if ($reg = $DB->get_record("scormcloud_registrations", array("regid" => $regid))) {
        $log->logInfo('Found scormcloud_registration '.$regid);
        if ($scormcloud = $DB->get_record("scormcloud", array("id" => $reg->scormcloudid))) {
            if (!scormcloud_hascapabilitytolaunch($scormcloud->course)) {
                error("You do not have permission to launch this course.");
            }
            $log->logInfo('Found scormcloud id '.$reg->scormcloudid);

            // Get the results from the cloud.
            $scormservice = scormcloud_get_service();
            $regservice = $scormservice->getRegistrationService();
            $resultsxml = $regservice->GetRegistrationResult($regid, 0, 'xml');
            $results = simplexml_load_string($resultsxml);

            $log->logInfo('updating Moodle gradebook '.$reg->userid.' - '.$scormcloud->course.' - '.$results->registrationreport->score);

            scormcloud_grade_item_update($reg->userid, $reg->scormcloudid, $results->registrationreport->score);

            $log->logInfo('attempting to update local scormcloud_registrations table');

            // Repopulate the $reg we got above to update it here.
            $reg->completion = scormcloud_getcomp($results->registrationreport->complete);
            $reg->satisfaction = scormcloud_getsat($results->registrationreport->success);
            $reg->totaltime = (int)$results->registrationreport->totaltime;
            $reg->score = (int)$results->registrationreport->score;
            if ($result = $DB->update_record('scormcloud_registrations', $reg)) {
                $log->logInfo('scormcloud_registrations updated');
            } else {
                $log->logInfo('error updating scormcloud_registrations');
            }
        }
    } else {
        $log->logInfo('ERROR : get_record("scormcloud_registrations", "regid", "'.$regid.'")');
    }
}
