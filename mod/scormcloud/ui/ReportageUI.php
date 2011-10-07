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

class ReportageUI
{
	private $_reportingService;
	private $_reportingServiceAuth;
	
	private $_userSummarySettings;
	private $_userActivitiesSettings;
	private $_courseSummarySettings;
	private $_courseListSettings;
	private $_learnerListSettings;
	
	public function __construct($reportingService, $reportingServiceAuth, $scormcloud, $user)
	{
		$this->_reportingService = $reportingService;
		$this->_reportingServiceAuth = $reportingServiceAuth;
		
		$this->_userSummarySettings = new WidgetSettings();
		$this->_userSummarySettings->setShowTitle(false);
		$this->_userSummarySettings->setScriptBased(true);
		$this->_userSummarySettings->setEmbedded(true);
		$this->_userSummarySettings->setVertical(false);
		$this->_userSummarySettings->setDivname('UserSummary');
		$this->_userSummarySettings->setCourseId($scormcloud->cloudid);
		$this->_userSummarySettings->setLearnerId($user->username);

		$this->_userActivitiesSettings = new WidgetSettings();
		$this->_userActivitiesSettings->setShowTitle(true);
		$this->_userActivitiesSettings->setScriptBased(true);
		$this->_userActivitiesSettings->setEmbedded(true);
		$this->_userActivitiesSettings->setDivname('UserActivities');
		$this->_userActivitiesSettings->setCourseId($scormcloud->cloudid);
		$this->_userActivitiesSettings->setLearnerId($user->username);

		$this->_courseSummarySettings = new WidgetSettings();
		$this->_courseSummarySettings->setShowTitle(true);
		$this->_courseSummarySettings->setScriptBased(true);
		$this->_courseSummarySettings->setEmbedded(true);
		$this->_courseSummarySettings->setVertical(true);
		$this->_courseSummarySettings->setCourseId($scormcloud->cloudid);
		$this->_courseSummarySettings->setDivname('CourseSummary');

		$this->_courseListSettings = new WidgetSettings();
		$this->_courseListSettings->setShowTitle(true);
		$this->_courseListSettings->setScriptBased(true);
		$this->_courseListSettings->setEmbedded(true);
		$this->_courseListSettings->setExpand(true);
		$this->_courseListSettings->setDivname('CourseListData');
		$this->_courseListSettings->setCourseId($scormcloud->cloudid);

		$this->_learnerListSettings = new WidgetSettings();
		$this->_learnerListSettings->setShowTitle(true);
		$this->_learnerListSettings->setScriptBased(true);
		$this->_learnerListSettings->setEmbedded(true);
		$this->_learnerListSettings->setExpand(false);
		$this->_learnerListSettings->setDivname('LearnersListData');
		$this->_learnerListSettings->setCourseId($scormcloud->cloudid);	
	}
	
	public function getUserSummaryUrl()
	{
		return $this->_reportingService->GetWidgetUrl($this->_reportingServiceAuth, 'learnerSummary', $this->_userSummarySettings);
	}
	
	public function getUserActivitiesUrl()
	{
		return $this->_reportingService->GetWidgetUrl($this->_reportingServiceAuth, 'learnerCourseActivities', $this->_userActivitiesSettings);
	}
	
	public function getCourseSummaryUrl()
	{
		return $this->_reportingService->GetWidgetUrl($this->_reportingServiceAuth, 'courseSummary', $this->_courseSummarySettings);
	}
	
	public function getCourseListUrl()
	{
		return $this->_reportingService->GetWidgetUrl($this->_reportingServiceAuth, 'courseActivities', $this->_courseListSettings);
	}
	
	public function getLearnerListUrl()
	{
		return $this->_reportingService->GetWidgetUrl($this->_reportingServiceAuth, 'learnerRegistration', $this->_learnerListSettings);
	}
}