<?php
require_once("../../config.php");
require_once("lib.php");

$cssurl = $CFG->wwwroot . '/mod/scormcloud/packageprops.css';
//echo $cssurl;
$newpath = str_replace('http://','https://',$CFG->scormcloud_serviceurl); //set to https
$newpath = str_replace('EngineWebServices','',$newpath); //remove EngineWebServices
$newpath = str_replace('cloud','accounts',$newpath); //change cloud to accounts

$newpath .= 'scorm-cloud-manager/public/signup-embedded';

//echo $newpath;
echo '<script language="javascript">window.location.href = "'.$newpath.'";</script>';

?>