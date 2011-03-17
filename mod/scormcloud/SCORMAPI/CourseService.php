<?php

/* Software License Agreement (BSD License)
 * 
 * Copyright (c) 2010-2011, Rustici Software, LLC
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the <organization> nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL Rustici Software, LLC BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

?>
<?php

require_once 'ServiceRequest.php';
require_once 'CourseData.php';
require_once 'Enums.php';
require_once 'UploadService.php';

/// <summary>
/// Client-side proxy for the "rustici.course.*" Hosted SCORM Engine web
/// service methods.  
/// </summary>
class CourseService{
	
	private $_configuration = null;
	
	public function __construct($configuration) {
		$this->_configuration = $configuration;
		//echo $this->_configuration->getAppId();
	}
	
	/// <summary>
    /// Import a SCORM .pif (zip file) from the local filesystem.
    /// </summary>
    /// <param name="courseId">Unique Identifier for this course.</param>
    /// <param name="absoluteFilePathToZip">Full path to the .zip file</param>
    /// <param name="itemIdToImport">ID of manifest item to import. If null, root organization is imported</param>
    /// <param name="permissionDomain">An permission domain to associate this course with, 
    /// for ftp access service (see ftp service below). 
    /// If the domain specified does not exist, the course will be placed in the default permission domain</param>
    /// <returns>List of Import Results</returns>
    public function ImportCourse($courseId, $absoluteFilePathToZip, $itemIdToImport = null)
    {
    	$uploadService = new UploadService($this->_configuration);
    	$location = $uploadService->UploadFile($absoluteFilePathToZip);
    	
    	$importException = null;
    	$response = null;
    	try {
    		$response = $this->ImportUploadedCourse($courseId, $location);
    	} catch (Exception $ex) {
    		$importException = $ex;
    	}
    	
    	$uploadService->DeleteFile($location);
    	
    	if($importException != null){
    		throw $importException;
    	}
    	
    	return $response;
    }
    
    /// <summary>
    /// Import new version of an existing course from a SCORM .pif (zip file)
    /// on the local filesystem.
    /// </summary>
    /// <param name="courseId">Unique Identifier for this course.</param>
    /// <param name="absoluteFilePathToZip">Full path to the .zip file</param>
    /// <returns>List of Import Results</returns>
    public function VersionCourse($courseId, $absoluteFilePathToZip)
    {
        $request = new ServiceRequest($this->_configuration);
        $params = array('courseid' => $courseId);
		$request->setMethodParams($params);
        $request->setFileToPost($absoluteFilePathToZip);
        $response = $request->CallService("rustici.course.versionCourse");
        return $response;
    }
     /// <summary>
     /// Import a SCORM .pif (zip file) from an existing .zip file on the
     /// Hosted SCORM Engine server.
     /// </summary>
     /// <param name="courseId">Unique Identifier for this course.</param>
     /// <param name="path">The relative path (rooted at your specific appid's upload area)
     /// where the zip file for importing can be found</param>
     /// <param name="fileName">Name of the file, including extension.</param>
     /// <param name="itemIdToImport">ID of manifest item to import</param>
     /// <param name="permissionDomain">An permission domain to associate this course with, 
     /// for ftp access service (see ftp service below). 
     /// If the domain specified does not exist, the course will be placed in the default permission domain</param>
     /// <returns>List of Import Results</returns>
     public function ImportUploadedCourse($courseId, $path, $permissionDomain = null)
     {

        $request = new ServiceRequest($this->_configuration);
		$params = array('courseid'=>$courseId,
						'path'=>$path);

       // if (!is_null($itemIdToImport))
		//{
//			$params[] = 'itemid' => $itemIdToImport;
		//}
        
         //if (!String.IsNullOrEmpty(permissionDomain))
         //    request.Parameters.Add("pd", permissionDomain);
		$request->setMethodParams($params);
         $response = $request->CallService("rustici.course.importCourse");
         //return ImportResult->ConvertToImportResults($response);
		error_log('rustici.course.importCourse : '.$response);
		return $response;
     }

    /// <summary>
    /// Import new version of an existing course from a SCORM .pif (zip file) from 
    /// an existing .zip file on the Hosted SCORM Engine server.
    /// </summary>
    /// <param name="courseId">Unique Identifier for this course.</param>
    /// <param name="domain">Optional security domain for the file.</param>
    /// <param name="fileName">Name of the file, including extension.</param>
    /// <returns>List of Import Results</returns>
    public function VersionUploadedCourse($courseId, $path, $permissionDomain = null)
    {

        $request = new ServiceRequest($this->_configuration);
       	$params = array('courseid'=>$courseId,
						'path'=>$path);
		$request->setMethodParams($params);
		
       	$response = $request->CallService("rustici.course.versionCourse");
		error_log('rustici.course.versionCourse : '.$response);
        //return ImportResult->ConvertToImportResults($response);
    }

    public function Exists($courseId) {
        $request = new ServiceRequest($this->_configuration);
        $params = array('courseid'=>$courseId);
        $request->setMethodParams($params);
        $response = $request->CallService("rustici.course.exists");
        $xml = simplexml_load_string($response);
        error_log($xml->result);
        return ($xml->result == 'true');
    }

    /// <summary>
    /// Retrieve a list of high-level data about all courses owned by the 
    /// configured appId.
    /// </summary>
 	/// <param name="courseIdFilterRegex">Regular expresion to filter the courses by ID</param>
    /// <returns>List of Course Data objects</returns>
    public function GetCourseList($courseIdFilterRegex = null)
    {
        $request = new ServiceRequest($this->_configuration);

		if(isset($courseIdFilterRegex))
		{
			$params = array('filter'=>$courseIdFilterRegex);
			$request->setMethodParams($params);
		}

        $response = $request->CallService("rustici.course.getCourseList");
		$CourseDataObject = new CourseData(null);
        return $CourseDataObject->ConvertToCourseDataList($response);
    }
   /// <summary>
    /// Delete the specified course
    /// </summary>
    /// <param name="courseId">Unique Identifier for the course</param>
    /// <param name="deleteLatestVersionOnly">If false, all versions are deleted</param>
    public function DeleteCourse($courseId, $deleteLatestVersionOnly)
    {
        $request = new ServiceRequest($this->_configuration);
       	$params = array('courseid'=>$courseId);
        if (isset($deleteLatestVersionOnly) && !$deleteLatestVersionOnly)
		{ 
            $params['versionid'] = 'latest';
		}
		$request->setMethodParams($params);
        $response = $request->CallService("rustici.course.deleteCourse");
		return $response;
    }

    /// <summary>
    /// Delete the specified version of a course
    /// </summary>
    /// <param name="courseId">Unique Identifier for the course</param>
    /// <param name="versionId">Specific version of course to delete</param>
    public function DeleteCourseVersion($courseId, $versionId)
    {
        $request = new ServiceRequest($this->_configuration);
		$params = array('courseid' => $courseId,
						'versionid' => $versionId);
       	$request->setMethodParams($params);
        $response = $request->CallService("rustici.course.deleteCourse");
		return $response;
    }
	 /// <summary>
        /// Get the Course Metadata in XML Format
        /// </summary>
        /// <param name="courseId">Unique Identifier for the course</param>
        /// <param name="versionId">Version of the specified course</param>
        /// <param name="scope">Defines the scope of the data to return: Course or Activity level</param>
        /// <param name="format">Defines the amount of data to return:  Summary or Detailed</param>
        /// <returns>XML string representing the Metadata</returns>
	    public function GetMetadata($courseId, $versionId, $scope, $format)
	    {
			$enum = new Enum();
            $request = new ServiceRequest($this->_configuration);
			$params = array('courseid'=>$courseId);
            
            if ($versionId != 0)
            {
                $params['versionid'] = $versionId;
            }
            $params['scope'] = $enum->getMetadataScope($scope);
            $params['format'] = $enum->getDataFormat($format);
			
			$request->setMethodParams($params);
			
            $response = $request->CallService("rustici.course.getMetadata");
            
            // Return the subset of the xml starting with the top <object>
            return $response;
	    }
	
	    /// <summary>
        /// Get the url that can be opened in a browser and used to preview this course, without
        /// the need for a registration.
        /// </summary>
        /// <param name="courseId">Unique Course Identifier</param>
        /// <param name="versionId">Version Id</param>
        public function GetPreviewUrl($courseId, $redirectOnExitUrl, $cssUrl = null)
        {
            $request = new ServiceRequest($this->_configuration);
            $params = array('courseid' => $courseId);
            if(isset($redirectOnExitUrl))
			{
                $params['redirecturl'] = $redirectOnExitUrl;
			}
            if(isset($cssUrl))
			{
                $params['cssurl'] = $cssUrl;
			} 
			$request->SetMethodParams($params);
				
            return $request->ConstructUrl("rustici.course.preview");
        }

        /// <summary>
        /// Gets the url to view/edit the package properties for this course.  Typically
        /// used within an IFRAME
        /// </summary>
        /// <param name="courseId">Unique Identifier for the course</param>
        /// <returns>Signed URL to package property editor</returns>
        /// <param name="notificationFrameUrl">Tells the property editor to render a sub-iframe
        /// with the provided url as the src.  This can be used to simulate an "onload"
        /// by using a notificationFrameUrl that's the same domain as the host system and
        /// calling parent.parent.method()</param>
        public function GetPropertyEditorUrl($courseId, $stylesheetUrl, $notificationFrameUrl)
        {
            // The local parameter map just contains method methodParameters.  We'll
            // now create a complete parameter map that contains the web-service
            // params as well the actual method params.
			$request = new ServiceRequest($this->_configuration);

            $parameterMap = array('courseid' => $courseId);

            if(isset($notificationFrameUrl)){
                $parameterMap['notificationframesrc'] = $notificationFrameUrl;
			}
            if(isset($stylesheetUrl)){
                $parameterMap['stylesheet'] = $stylesheetUrl;
			}

            $request->setMethodParams($parameterMap);
            return $request->ConstructUrl("rustici.course.properties");
        }
}
/*

    /// <summary>
    /// Retrieve the list of course attributes associated with this course.  If
    /// multiple versions of the course exist, the attributes of the latest version
    /// are returned.
    /// </summary>
    /// <param name="courseId">Unique Identifier for the course</param>
    /// <returns>Dictionary of all attributes associated with this course</returns>
    public Dictionary<string, string> GetAttributes(string courseId)
    {
        return GetAttributes(courseId, Int32.MinValue);
    }

    /// <summary>
    /// Retrieve the list of course attributes associated with a specific version
    /// of the specified course.
    /// </summary>
    /// <param name="courseId">Unique Identifier for the course</param>
    /// <param name="versionId">Specific version the specified course</param>
    /// <returns>Dictionary of all attributes associated with this course</returns>
    public Dictionary<string, string> GetAttributes(string courseId, int versionId)
    {
        ServiceRequest request = new ServiceRequest(configuration);
        request.Parameters.Add("courseid", courseId);
        if (versionId != Int32.MinValue)
            request.Parameters.Add("versionid", versionId);
        XmlDocument response = request.CallService("rustici.course.getAttributes");

        // Map the response to a dictionary of name/value pairs
        Dictionary<string, string> attributeDictionary = new Dictionary<string, string>();
        foreach (XmlElement attrEl in response.GetElementsByTagName("attribute"))
        {
            attributeDictionary.Add(attrEl.Attributes["name"].Value, attrEl.Attributes["value"].Value);
        }
            
        return attributeDictionary;
    }

    /// <summary>
    /// Update the specified attributes (name/value pairs)
    /// </summary>
    /// <param name="courseId">Unique Identifier for the course</param>
    /// <param name="versionId">Specific version the specified course</param>
    /// <param name="attributePairs">Map of name/value pairs</param>
    /// <returns>Dictionary of changed attributes</returns>
    public Dictionary<string, string> UpdateAttributes(string courseId, int versionId, 
        Dictionary<string,string> attributePairs)
    {
        ServiceRequest request = new ServiceRequest(configuration);
        request.Parameters.Add("courseid", courseId);
        if (versionId != Int32.MinValue)
        {
            request.Parameters.Add("versionid", versionId);
        }
            
        foreach (string key in attributePairs.Keys)
        {
            if (!String.IsNullOrEmpty(attributePairs[key]))
            {
                request.Parameters.Add(key, attributePairs[key]); 
            }
        }

        XmlDocument response = request.CallService("rustici.course.updateAttributes");

        // Map the response to a dictionary of name/value pairs.  This list
        // should contain only those values that have changed.  If a param was 
        // specified who's value is the same as the current value, it will not
        // be included in this list.
        Dictionary<string, string> attributeDictionary = new Dictionary<string, string>();
        foreach (XmlElement attrEl in response.GetElementsByTagName("attribute"))
        {
            attributeDictionary.Add(attrEl.Attributes["name"].Value, attrEl.Attributes["value"].Value);
        }
            
        return attributeDictionary;
    }

    /// <summary>
    /// Update the specified attributes (name/value pairs) for the specified
    /// course.  If multiple versions of the course exist, only the latest
    /// version's attributes will be updated.
    /// </summary>
    /// <param name="courseId">Unique Identifier for the course</param>
    /// <param name="attributePairs">Map of name/value pairs</param>
    /// <returns>Dictionary of changed attributes</returns>
    public Dictionary<string, string> UpdateAttributes(string courseId, Dictionary<string, string> attributePairs)
    {
        return UpdateAttributes(courseId, Int32.MinValue, attributePairs);
    }
	

 

    /// <summary>
    /// Update course files only.  One or more course files can be updating them by
    /// including them in a .zip file and sending updates via this method
    /// </summary>
    /// <param name="courseId">Unique Identifier for the course</param>
    /// <param name="versionId">Specific version of the course</param>
    /// <param name="absoluteFilePathToZip">Full path to the .zip file</param>
    public void UpdateAssets(string courseId, int versionId, string absoluteFilePathToZip)
    {
        ServiceRequest request = new ServiceRequest(configuration);
        request.Parameters.Add("courseid", courseId);
        if (versionId != Int32.MinValue)
        {
            request.Parameters.Add("versionid", versionId);
        }
        request.FileToPost = absoluteFilePathToZip;
        request.CallService("rustici.course.updateAssets");
    }

    /// <summary>
    /// Update course files only.  One or more course files can be updating them by
    /// including them in a .zip file and sending updates via this method.  I
    /// </summary>
    /// <param name="courseId">Unique Identifier for the course</param>
    /// <param name="absoluteFilePathToZip">Full path to the .zip file</param>
    /// <remarks>If multiple versions of a course exist, only the latest version's assets will
    /// be updated.</remarks>
    public void UpdateAssets(string courseId, string absoluteFilePathToZip)
    {
        UpdateAssets(courseId, Int32.MinValue, absoluteFilePathToZip);
    }

    /// <summary>
    /// Update course files only.  One or more course files can be updating them by
    /// including them in a .zip file and sending updates via this method.  The
    /// specified file should already exist in the upload domain space.
    /// </summary>
    /// <param name="courseId">Unique Identifier for this course.</param>
    /// <param name="versionId">Specific version of the course</param>
    /// <param name="domain">Optional security domain for the file.</param>
    /// <param name="fileName">Name of the file, including extension.</param>
    public void UpdateAssetsFromUploadedFile(string courseId, int versionId, string domain, string fileName)
    {
        // If null domain id provided, hard-code this to "default"
        string path = (String.IsNullOrEmpty(domain) ? "default" : domain) + "/" + fileName;

        ServiceRequest request = new ServiceRequest(configuration);
        request.Parameters.Add("courseid", courseId);
        if (versionId != Int32.MinValue)
        {
            request.Parameters.Add("versionid", versionId);
        }
        request.Parameters.Add("path", path);
        request.CallService("rustici.course.updateAssets");
    }

    /// <summary>
    /// Update course files only.  One or more course files can be updating them by
    /// including them in a .zip file and sending updates via this method.  The
    /// specified file should already exist in the upload domain space.  
    /// </summary>
    /// <param name="courseId">Unique Identifier for this course.</param>
    /// <param name="domain">Optional security domain for the file.</param>
    /// <param name="fileName">Name of the file, including extension.</param>
    /// <remarks>If multiple versions of a course exist, only the latest version's assets will
    /// be updated.</remarks>
    public void UpdateAssetsFromUploadedFile(string courseId, string domain, string fileName)
    {
        UpdateAssetsFromUploadedFile(courseId, Int32.MinValue, domain, fileName);
    }

    /// <summary>
    /// Delete one or more files from the specified course directory
    /// </summary>
    /// <param name="courseId">Unique Identifier for this course.</param>
    /// <param name="versionId">Version ID of the specified course</param>
    /// <param name="relativeFilePaths">Path of each file to delete realtive to the course root</param>
    /// <returns>Map of results as a Dictionary of booleans</returns>
    public Dictionary<string, bool> DeleteFiles(string courseId, int versionId, Collection<string> relativeFilePaths)
    {
        ServiceRequest request = new ServiceRequest(configuration);
        request.Parameters.Add("courseid", courseId);
        if (versionId != Int32.MinValue)
        {
            request.Parameters.Add("versionid", versionId);
        }

        foreach (string fileName in relativeFilePaths)
        {
            request.Parameters.Add("path", fileName);
        }

        XmlDocument response = request.CallService("rustici.course.deleteFiles");

        Dictionary<string, bool> resultsMap = new Dictionary<string, bool>();
        foreach (XmlElement attrEl in response.GetElementsByTagName("result"))
        {
            resultsMap.Add(attrEl.Attributes["path"].Value, 
                Convert.ToBoolean(attrEl.Attributes["deleted"].Value));
        }

        return resultsMap;
    }

    /// <summary>
    /// Delete one or more files from the specified course directory. 
    /// </summary>
    /// <param name="courseId">Unique Identifier for this course.</param>
    /// <param name="relativeFilePaths">Path of each file to delete realtive to the course root</param>
    /// <returns>Map of results as a Dictionary of booleans</returns>
    /// <remarks>If  multiple versions of a course exist, only files from the latest version
    /// will be deleted.</remarks>
    public Dictionary<string, bool> DeleteFiles(string courseId, Collection<string> relativeFilePaths)
    {
        return DeleteFiles(courseId, Int32.MinValue, relativeFilePaths);
    }
	

    /// <summary>
    /// Get the file structure of the given course.
    /// </summary>
    /// <param name="courseId">Unique Identifier for this course.</param>
    /// <param name="versionId">Version ID of the specified course</param>
    /// <returns>XML String of the hierarchical file structure of the course</returns>
    public string GetFileStructure(string courseId, int versionId)
    {
        ServiceRequest request = new ServiceRequest(configuration);
        request.Parameters.Add("courseid", courseId);
        if (versionId != Int32.MinValue)
        {
            request.Parameters.Add("versionid", versionId);
        }
        XmlDocument response = request.CallService("rustici.course.getFileStructure");
        
        // Return the subset of the xml starting with the top <dir>
        return response.ChildNodes[1].InnerXml;
    }

    /// <summary>
    /// Get the file structure of the given course.
    /// </summary>
    /// <param name="courseId">Unique Identifier for this course.</param>
    /// <returns>XML String of the hierarchical file structure of the course</returns>
    /// <remarks>If multiple versions of the course exist, the latest version's
    /// files structure will be retured.</remarks>
    public string GetFileStructure(string courseId)
    {
        return GetFileStructure(courseId, Int32.MinValue);
    }

   
    /// <summary>
    /// Get the url that points directly to a course asset
    /// </summary>
    /// <param name="courseId">Unique Course Identifier</param>
    /// <param name="path">Path to asset from root of course</param>
    /// <param name="versionId">Specific Version</param>
    /// <returns>HTTP Url to Asset</returns>
    public String GetAssetUrl(String courseId, String path, int versionId)
    {
        ServiceRequest request = new ServiceRequest(configuration);
        request.Parameters.Add("courseid", courseId);
        request.Parameters.Add("path", path);
        if (versionId != Int32.MinValue)
        {
            request.Parameters.Add("versionid", versionId);
        }

        return request.ConstructUrl("rustici.course.getAssets");
    }

    /// <summary>
    /// Get the url that points directly to a course asset
    /// </summary>
    /// <param name="courseId">Unique Course Identifier</param>
    /// <param name="path">Path to asset from root of course</param>
    /// <returns>HTTP Url to Asset</returns>
    public String GetAssetUrl(String courseId, String path)
    {
        return GetAssetUrl(courseId, path, Int32.MinValue);
    }


*/
?>
