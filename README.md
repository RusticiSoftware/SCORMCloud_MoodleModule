# License
>   Copyright 2010-2012 Rustici Software. 
>   
>   The SCORM Cloud Module is free software: you can redistribute it and/or
>   modify it under the terms of the GNU General Public License as published
>   by the Free Software Foundation, either version 3 of the License, or
>   (at your option) any later version.
>   
>   The SCORM Cloud Module is distributed in the hope that it will be useful,
>   but WITHOUT ANY WARRANTY; without even the implied warranty of
>   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
>   GNU General Public License for more details.
>   
>   You should have received a copy of the GNU General Public License
>   along with the SCORM Cloud Module.  If not, see <http://www.gnu.org/licenses/>.

## SCORM Cloud Module for Moodle
This is the source code repository for the [SCORM Cloud Module for Moodle](http://scorm.com/moodle/).

### Installation
* Obtain a copy of the module (this repository).
* Obtain a copy of the format. [Moodle Format Repo](https://github.com/RusticiSoftware/SCORMCloud_MoodleFormat)
	* You can download a zip file of both from our [GitHub downloads page](https://github.com/RusticiSoftware/SCORMCloud_MoodleModule/downloads), or you can clone the Git repositories.
	* If you download a zip file, extract it after you finish.
* Upload/copy the directories in the module to the installation directory of your Moodle instance.
	* format --> course/format folder in Moodle
	* module mod/scormcloud --> mod folder in Moodle
* Log in with an administrator account and Moodle will show you a list of modules to be upgraded/installed. Click upgrade.
* To use and configure the module, you'll need to [sign up for a SCORM Cloud account](https://cloud.scorm.com/sc/guest/SignUpForm).
* When prompted to configure the SCORM Cloud Module, enter the information available on your 'Apps' page on the SCORM Cloud website.
	* The Service URL is http://cloud.scorm.com/EngineWebServices/
	
### Usage
Create a course as usual in Moodle, but select the SCORM Cloud format option from the dropdown. After you finish the standard Moodle course configuration, the SCORM Cloud module will display a form for you to upload your SCORM course to the Cloud.

You can also use SCORM Cloud courses within other Moodle course formats like "weekly" by selecting "SCORM Cloud Course" from the "Add an activity" dropdown on the course outline page.

### Contributing
While we at Rustici Software have developed the initial version of this Moodle module and welcome questions and suggestions, it is intended to be a community resource. If you'd like to help us make it better, send us a bug report or some code (whether that be by GitHub pull request or a patch over email).

### Help
If you have any questions or comments about the Moodle module or SCORM Cloud itself, please don't hesitate to contact us at [support@scorm.com](mailto:support@scorm.com).