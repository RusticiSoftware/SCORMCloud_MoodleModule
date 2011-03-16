<?php

/*
==============================================================================
	
	Copyright (c) 2009 Rustici Software
	
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

==============================================================================
*/
//LaunchHistoryHelper.php

//This file handles the ajax calls for the launch history report.

require_once('../../../config.php');
global $CFG;

require_once("../lib.php");
require_once('../locallib.php');

require_once('../SCORMAPI/ScormEngineService.php');
require_once('../SCORMAPI/ServiceRequest.php');
require_once('../SCORMAPI/CourseData.php');
require_once('../SCORMAPI/RegistrationService.php');
require_once('../SCORMAPI/RegistrationSummary.php');


//I tried to use required_param here, but couldn't get it to work with the jquery call... rolled back to this
$launchid = $_GET['launchId'];
$regid = $_GET['regId'];

$ScormService = new ScormEngineService($CFG->scormcloud_serviceurl,$CFG->scormcloud_appid,$CFG->scormcloud_secretkey);
$regService = $ScormService->getRegistrationService();

$launchResponse = $regService->GetLaunchInfo($launchid);

$launchXml = simplexml_load_string($launchResponse);
$launch = $launchXml->launch;

$launchInfo = simplexml_load_string("<LaunchInfo/>");
$launchInfo->addAttribute("launch_history_id",$launchid);

// If we don't have an exit_time then the launch terminated without sending a final
// post (or it is still in progress ...)
$launchInfo->addAttribute("clean_termination",(strlen($launch->exit_time) > 0)? "true" : "false");

if (count($launch->log->children()) > 0){
    
    $node1 = dom_import_simplexml($launchInfo);
    $dom_sxe = dom_import_simplexml($launch->log);
    $node2 = $node1->ownerDocument->importNode($dom_sxe, true);
    $node1->appendChild($node2);
    $launchInfo = simplexml_import_dom($node1); 
    
    $regStat = $launchInfo->addChild("RegistrationStatusOnExit");
    $regStat->addAttribute("completion_status", $launch->completion);
    $regStat->addAttribute("success_status", $launch->satisfaction);
    $regStat->addAttribute("score", $launch->measure_status == "1" ? ($launch->normalized_measure * 100)."%" : "unknown");
    $regStat->addAttribute("total_time_tracked", $launch->experienced_duration_tracked);
    
}

$launchArray = $regService->GetLaunchHistory($regid);
//echo var_dump($launchArray).'<br/>';

foreach ($launchArray as $launchEntry){
    if ($launchEntry->getId() != $launchid && strcmp($launchEntry->getLaunchTime(),$launch->launch_time) < 0 && strlen($launchEntry->getCompletion()) > 0 ){
        $entryTimes[] = $launchEntry->getLaunchTime();
    }
}
$regEntryStat = $launchInfo->addChild("RegistrationStatusOnEntry");

if (count($entryTimes) == 0){

    $regEntryStat->addAttribute("completion_status", "unknown");
    $regEntryStat->addAttribute("success_status", "unknown");
    $regEntryStat->addAttribute("score", "unknown");
    $regEntryStat->addAttribute("total_time_tracked", 0);

} else {
    rsort($entryTimes,SORT_STRING);
    $indx = 0;
    while ($launchArray[$indx]->getLaunchTime() != $entryTimes[0] && count($launchArray) > $indx + 1){
        $indx++;
    }
    
    $regEntryStat->addAttribute("completion_status", $launchArray[$indx]->getCompletion());
    $regEntryStat->addAttribute("success_status", $launchArray[$indx]->getSatisfaction());
    $regEntryStat->addAttribute("score", $launchArray[$indx]->getMeasureStatus() == "1" ? ($launchArray[$indx]->getNormalizedMeasure() * 100)."%" : "unknown");
    $regEntryStat->addAttribute("total_time_tracked", $launchArray[$indx]->getExperiencedDurationTracked());
}

echo $launchInfo->asXML();



?>