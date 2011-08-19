<?php

$settings->add(new admin_setting_heading('scormcloud_method_heading', get_string('generalconfig', 'scormcloud'),
                   get_string('explaingeneralconfig', 'scormcloud')));

$settings->add(new admin_setting_configtext('scormcloud_serviceurl', get_string('serviceurl', 'scormcloud'),
                   get_string('serviceurl_desc', 'scormcloud')));

$settings->add(new admin_setting_configtext('scormcloud_appid', get_string('appid', 'scormcloud'),
                   get_string('appid_desc', 'scormcloud')));

$settings->add(new admin_setting_configtext('scormcloud_secretkey', get_string('secretkey', 'scormcloud'),
                   get_string('secretkey_desc', 'scormcloud')));

?>
