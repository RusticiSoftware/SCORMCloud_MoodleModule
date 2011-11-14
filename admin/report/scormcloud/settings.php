<?php  // $Id: settings.php,v 1.1.2.3 2008/11/29 14:30:58 skodak Exp $
$ADMIN->add('reports', new admin_externalpage('reportscormcloud', 'SCORM Cloud Reports', "$CFG->wwwroot/$CFG->admin/report/scormcloud/index.php",'report/scormcloud:view'));
?>
