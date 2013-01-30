<?php
$ADMIN->add('reports', new admin_externalpage('reportscormcloud', 'SCORM Cloud Reports', "$CFG->wwwroot/$CFG->admin/report/scormcloud/index.php",'report/scormcloud:view'));
?>