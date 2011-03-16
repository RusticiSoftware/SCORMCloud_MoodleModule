#SCORMCloud mod for Moodle 1.9#

We have released the first version of our take on a Moodle mod that hooks to the SCORM Cloud. The installation should be as simple as any other mod with the addition of a custom course format and admin reports as well. You will find in the attached ZIP file the folder structure that you would have for your Moodle installation. 

The scormcloud folder in the course/format folder goes in your course/format folder of your Moodle installation, and likewise, the scormcloud folder in the mod folder goes in your Moodle mod folder and the admin/report/scormcloud folder goes in the same directory in your Moodle installtion. Once all folders are in place, you can simple log in as admin and go to the Notifications page to run the installation scripts for the mod.

 

* Download the zip file from our public GitHub repository (https://github.com/RusticiSoftware/SCORMCloud_Moodle19Mod).
* Extract the zip file locally.
* Upload the directories according to the structure in the zip file (course/format to course/format, admin/report/scormcloud to admin/report/scormcloud and scormcloud to the moodleMod folder)
* Log in as admin, go to the Notifications page, and the import scripts will run.
* At this point, the mod will have been included.  The next steps relate to configuring your SCORM Cloud account.
	* Go here to sign up for your Cloud account.  Your AppId and SecretKey will be delivered to you via email.
	* Enter the ServiceUrl (http://cloud.scorm.com/EngineWebServices/) and your AppId and SecretKey under the mod settings.
	* Add a course as you always would in Moodle, but select a type of  Rustici SCORM Cloud Engine during the course creation process.  As you finish, you will have the opportunity to upload the course to the Cloud.
 

That's all it takes... now you can upload content, including SCORM 2004 and AICC content, and play it like any other Moodle content.

Please let us know if you find anything that we missed or did incorrectly, this is an open and free (except for the SCORM Cloud usage) implementation for the community.


If you have any questions or comments about this Moodle mod, please don't hesitate to contact us at support@scorm.com.