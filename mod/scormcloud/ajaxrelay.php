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

	$proxy_url = isset($_GET['proxy_url'])?$_GET['proxy_url']:false;
	//error_log("ajaxrelay.php proxy_url = $proxy_url");
	if (!$proxy_url) {
	    header("HTTP/1.0 400 Bad Request");
	    echo "proxy.php failed because proxy_url parameter is missing";
	}
	
	// Set your return content type
	header('Content-type: text/html');
	
	// Get that website's content
	if($_POST)
	{
		$content = fetch_url($proxy_url, $_POST);
		error_log('submitting with POST');
	}else{
		$content = simple_get($proxy_url);
		error_log('submitting with GET');
	}
	// If there is something, read and return
	if ($content) {
	    echo $content;
	}else{
		echo 'There was a problem opening the url';
	}

	function simple_get($url)
	{
		// Get that website's content
		$handle = fopen($url, "r");

		// If there is something, read and return
		$buffer = '';
		if ($handle) {
		    while (!feof($handle)) {
		        $buffer .= fgets($handle, 4096);
				//error_log($buffer);
		    }
		    fclose($handle);
		}
		return $buffer;
	}

	function fetch_url($url, $postParams = null, $timeout = 60) {
	        $ch = curl_init();
			error_log('fetch_url:'.$url);
	        // set up the request
	        curl_setopt($ch, CURLOPT_URL, $url);

	        
	        if (isset($postParams)) {
				// make sure we submit this as a post
		        curl_setopt($ch, CURLOPT_POST, true);
	            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	            curl_setopt($ch, CURLOPT_POSTFIELDS, $postParams);
	        }else{
				curl_setopt($ch, CURLOPT_POST, false);
	            curl_setopt($ch, CURLOPT_POSTFIELDS, "");
	        }

	        // make sure problems are caught
	        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
	        // return the output
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	        // set the timeouts
	        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
	
	        // set the PHP script's timeout to be greater than CURL's
	        set_time_limit($timeout + 5);
	
	        $result = curl_exec($ch);
			error_log('curl_errno($ch):'.curl_errno($ch));
			error_log('$curl_result:'.$result);
	        // check for errors
	        if (0 == curl_errno($ch)) {
	            curl_close($ch);
	            return $result;
	        } else {
	            echo 'Request failed. '.curl_error($ch);
	            curl_close($ch);
	        }
	    }
?>