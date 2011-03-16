<?php

class ImportResult{
	
	private $_title = "";
	private $_wasSuccessful = false;
	private $_message = "";
	private $_parserWarnings = array();
	
	/// <summary>
    /// Xml Constructor that takes the response XML as returned by one of the import-related
    /// web services.
    /// </summary>
    /// <param name="irXml">importresult Element</param>
    public function __construct($irXml)
    {
		$xml = simplexml_load_string($irXml);
        $this->_wasSuccessful = $xml["successful"].Value;

        foreach($xml->ChildNodes as $node)
        {
            switch ($node->Name)
            {
                case "title" :
                    $this->title = $node->InnerText;
                    break;
                case "message" :
                    $this->message = $node->InnerText;
                    break;
                case "parserwarnings":
                    foreach ($node->ChildNodes as $w)
                    {
                        $this->_parserWarnings[] = $w->InnerText;
                    }
                    break;
                default:
                    break;
            }
        }
    }

    /// <summary>
    /// Helper method that takes the entire web service response document and
    /// returns a List of one or more ImportResults.
    /// </summary>
    /// <param name="xmlDoc"></param>
    /// <returns></returns>
    public static function ConvertToImportResults($xmlDoc)
    {
        $allResults = array();
        
        $importResults = $xmlDoc->importresult;
        foreach ($importResults as $result)
        {
            $allResults[] = new ImportResult($result);
        }

        return $allResults;
    }

    /// <summary>
    /// The Title of the course that was imported as derived from the manifest
    /// </summary>
    public function getTitle()
    {
        return $this->_title;
    }

    /// <summary>
    /// Indicates whether or not the import had any errors
    /// </summary>
    public function getWasSuccessful()
    {
        return $this->_wasSuccessful;
    }

    /// <summary>
    /// More information regarding the success or failure of the import.
    /// </summary>
    public function getMessage()
    {
        return $this->_message;
    }

    /// <summary>
    /// Warnings issued during import process related to the structure of the manifest.
    /// </summary>
    public function getParserWarnings()
    {
        return $this->_parserWarnings;
    }
	
}

?>