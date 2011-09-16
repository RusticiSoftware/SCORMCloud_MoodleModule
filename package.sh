#!/bin/bash
#

cd mod
datestr=`date "+%Y%m%d"`
fname="mod_scormcloud-2.x-${datestr}00.zip"
zip -r $fname scormcloud -x scormcloud/SCORMCloud_PHPLibrary/.git\* scormcloud/.buildpath scormcloud/.project scormcloud/.settings\*
mv $fname ..
cd ..

cd course-format
fname="format_scormcloud-2.x-${datestr}00.zip"
zip -r $fname scormcloud
mv $fname ..
cd ..
