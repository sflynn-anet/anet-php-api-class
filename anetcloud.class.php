<?php
/**
 * Atlantic.Net Cloud VPS Hosting API Class
 *
 * @category	PHP API Class
 * @package		AnetCloud
 * @author		Stephen Flynn <sflynn@datai.net>, <sflynn@atlantic.net>
 * @link  		https://github.com/sflynn-anet/anet-php-api-class
 * @version		1.1
 * 
 *
 * LICENSE: Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 */

 
 class AnetCloud {

	// Timeout for the API requests in seconds
	const TIMEOUT = 5;
	
	/**
	 * API Connection URL
	 */
	private $_apiurl = 'https://cloudapi.atlantic.net';
	
	/**
	 * API Version String
	 */
	private static $_apiver	= '2010-12-30';
	
	/**
	 * Atlantic.Net Datacenter Locations
	 */
	private static $_locations = array(
		'USEAST1',      // Orlando, FL
		'USEAST2',      // New York, NY
		'USCENTRAL1',   // Dallas, TX
		'USWEST1',      // San Francisco, CA
		'CAEAST1',      // Toronto, ON
		'EUWEST1'       // London, UK
	);
	
	/**
	 * API Authentication Key variable
	 */
	private $_apikey;

	/**
	 * API Authentication Private Key variable
	 */
	private $_apipkey;
 
	/**
	 * Default Constructor
	 *
	 * @param string $apikey API Authentication Key
	 * @param string $apipkey API Authentication Private Key
	 * @return NULL
	 * 
	 */
	
    public function __construct($apikey=NULL, $apipkey=NULL) {
    
		/** Set the API key **/
		if($apikey == NULL) {
			exit('You must provide a valid API key');
		} else {
			$this->set_apikey($apikey);	
		}
		
		/** Set the API Private key **/
		if($apikey == NULL) {
			exit('You must provide a valid API Private key');
		} else {
			$this->set_apipkey($apipkey);	
		}
    }
	
	/**
	 * Set API Authentication Key
	 *
	 * Can be used to reset the API key being uses after the Class is already called
	 *
	 * @access public
	 * @param string $apikey 
	 * @return bool
	 *
	 */
	public function set_apikey($apikey=NULL) {
		
		if($apikey == NULL) { return false; }
		
		$this->_apikey = trim($apikey);	
		return true;
	}
	
	/**
	 * Set API Authentication Private Key
	 *
	 * Can be used to reset the API private key being used after the Class is already called
	 *
	 * @access public
	 * @param string $apipkey
	 * @return bool
	 *
	 */
	public function set_apipkey($apipkey=NULL) {
		
		if($apipkey == NULL) { return false; }
		
		$this->_apipkey = trim($apipkey);	
		return true;
	}
	
	/**
	 * Set API URL
	 *
	 * Can be used to reset the API connection URL after the Class is already called
	 *
	 * @access public
	 * @param string $apiurl 
	 * @return bool
	 *
	 */
	public function set_apiurl($apiurl=NULL) {
		
		if($apiurl == NULL) { return false; }
		
		$this->_apiurl = trim($apiurl);	
		return true;
	}
	
	/** MISC FUNCTIONS **/
	
	/**
	 * Get Locations
	 *
	 * Return the list of Datacenter Locations
	 *
	 * @access public
	 * @param string $output Format of output response - Accepts 'json' or 'array' 
	 * @return mixed
	 *
	 */
	public function get_locations($output='array') {
		
		if($output=='json') {
			return json_encode(self::$_locations);
		} else {
			return self::$_locations;
		}
		
	}
	
	
	/** INSTANCE FUNCTIONS **/
	
	/**
	 * List VPS Instances
	 *
	 * This method enables the client to retrieve the list of currently active cloud servers.
	 *
	 * @access public
	 * @param string $output Format of output response - Accepts 'json' or 'array' 
	 * @return mixed
	 */
	public function get_instances($output='array') {
		
		$data['Action'] = 'list-instances';
		
		return $this->http_post($data, $output);
		
	}
	
	/**
	 * Describe VPS Instance
	 *
	 * This method enables the you to retrieve the details of a specific Cloud Server.
	 *
	 * @access public
	 * @param int VPS Instance ID 
	 * @param string Format of output response - Accepts 'json' or 'array'
	 * @return mixed
	 *
	 */
	public function get_instance_details($instanceID, $output='array') {
		
		$data['Action'] = 'describe-instance';
		$data['instanceid'] = $instanceID;
		
		return $this->http_post($data, $output);
	}
	
	/**
	 * Run Instance
	 *
	 * This method enables you to create new Cloud Servers by specifying a flexible set of configuration parameters.
	 *
	 * @access public
	 * @param array 
	 * @return output
	 */
	public function create_instance($setData, $output='array') {
		$dataError = false;
		
		$data['Action'] = 'run-instance';
		
		
		// Cleanup all the submitted data values
		$setData = array_map('trim', $setData);
		
		// Error Checking
		if( !isset($setData['servername']) || empty($setData['servername']) ) { $dataError = true; $dataErrorName[]='servername'; }
		if( !isset($setData['imageid']) || empty($setData['imageid']) ) { $dataError = true; $dataErrorName[]='imageid'; }
		if( !isset($setData['planname']) || empty($setData['planname']) ) { $dataError = true; $dataErrorName[]='planname'; }
		if( !isset($setData['vm_location']) || empty($setData['vm_location']) ) { $dataError = true; $dataErrorName[]='vm_location'; }
		
		if($dataError) {
			foreach($dataErrorName as $e) {
				echo "You must submit a value for '$e'".PHP_EOL;
			}
			exit();
		}
		
		/** Set Require Attributes **/
		$data['servername'] = $setData['servername'];
		$data['imageid'] = $setData['imageid'];
		$data['planname'] = $setData['planname'];
		$data['vm_location'] = $setData['vm_location'];
		
		/** Option Attributes **/
		if( isset($setData['enablebackup']) && !empty($setData['enablebackup']) ) {
			$data['enablebackup'] = $setData['enablebackup'];
		}
		
		if( isset($setData['cloneimage']) && !empty($setData['cloneimage']) ) {
			$data['cloneimage'] = $setData['cloneimage'];
		}
		
		if( isset($data['serverqty']) && !empty($data['serverqty']) ) {
			$data['serverqty'] = $setData['serverqty'];
		} else {
			$data['serverqty'] = 1;
		}
		
		if( isset($setData['key_id']) && !empty($setData['key_id']) ) {
			$data['key_id'] = $setData['key_id'];
		}

		return $this->http_post($data, $output);
	}
	
	/**
	 * Reboot Instance
	 *
	 * This method enables the you to restart a specific cloud server or multiple cloud servers at one time.
	 *
	 * @access public
	 * @param String 
	 * @return output
	 */
	public function reboot_instance($instanceID=NULL, $rebootType='soft', $output='array') {
		$data['Action'] = 'reboot-instance';
		
		// Set the Instance ID to reboot
		if( !isset($instanceID) || is_null($instanceID) ) {
			exit("You must set the instance ID to reboot." . PHP_EOL);
		} else {
			$data['instanceid'] = $instanceID;
		}
		
		// Set the REBOOT TYPE value
		$data['reboottype'] = $rebootType;
		
		return $this->http_post($data, $output);
		
	}
	
	/**
	 * Terminate Instance
	 *
	 * This method enables the you to remove a specific cloud server or multiple cloud servers at one time.
	 *
	 * @access public
	 * @param String 
	 * @return output
	 */
	public function delete_instance($instanceID=NULL, $output='array') {
		$data['Action'] = 'terminate-instance';
		
		// Set the Instance ID to delete
		if( !isset($instanceID) || is_null($instanceID) ) {
			exit("You must set the instance ID to delete." . PHP_EOL);
		} else {
			$data['instanceid'] = $instanceID;
		}
		
		return $this->http_post($data, $output);
	}
	
	/** IMAGE FUNCTIONS **/
	
	/**
	 * Describe Image
	 *
	 * This method enables the client to retrieve the description of all available cloud images 
	 * or the description of a specific cloud image by providing the image id (e.g. ubuntu-14.04_64bit)
	 *
	 * @access public
	 * @param String 
	 * @return output
	 */
	public function get_images($output='array') {
		$data['Action'] = 'describe-image';

		return $this->http_post($data, $output);
	}
	
	/**
	 * Describe Image Detail
	 *
	 * This method enables the client to retrieve the description of all available cloud images 
	 * or the description of a specific cloud image by providing the image id (e.g. ubuntu-14.04_64bit)
	 *
	 * @access public
	 * @param String 
	 * @return output
	 */
	public function get_image($imageID, $output='array') {
		$data['Action'] = 'describe-image';
		$data['imageid'] = trim($imageID);
		
		return $this->http_post($data, $output);
	}
	
	/** PLAN FUNCTIONS **/
	
	
	/**
	 * Describe Plans
	 *
	 * This method enables the client to retrieve a list of available cloud server plans, 
	 * narrow the listing down optionally by server platform (Windows, Linux, etc ), 
	 * or get information about just one specific plan (e.g. L which represents the large plan)
	 *
	 * @access public
	 * @param String 
	 * @return output
	 */
	public function get_plans($output='array') {
		$data['Action'] = 'describe-plan';

		return $this->http_post($data, $output);
	}
	
	/**
	 * Describe Plan Details
	 *
	 * This method enables the client to retrieve a list of available cloud server plans, 
	 * narrow the listing down optionally by server platform (Windows, Linux, etc ), 
	 * or get information about just one specific plan (e.g. L which represents the large plan)
	 *
	 * @access public
	 * @param String 
	 * @return output
	 */
	public function get_plan($name=NULL, $platform=NULL, $output='array') {
		$data['Action'] = 'describe-plan';
		
		if($name == NULL || $platform == NULL) {
			exit('You must provide the service plan name and platform');
		} else {
			$data['plan_name'] = trim($name);
			$data['platform'] = trim($platform);			
		}

		return $this->http_post($data, $output);
	}
	
	/**
	 *  SSH Keys
	 *  
	 *  This method enables the client to retrieve the details of all SSH Keys that they have added to their account. 
	 *  The client can then specify an SSH Key to embed into their Cloud Server at the time of creation.
	 *  
	 *  
	 */
	public function get_ssh_keys($output='array') {
		$data['Action'] = 'list-sshkeys';

		$response = $this->http_post($data, $output);
		$ssh_keys = $response['list-sshkeysresponse']['KeysSet'];

		return $ssh_keys;
	}
	
	
	/**
     * GLOBAL API CALL
     * HTTP POST a specific task with the supplied data
     */
    private function http_post($data, $output='array', $verifySSL=false)
    {
		
		// Set the API version into the data request array
		$data['Version'] = self::$_apiver;
		
		// Set the API key into the data request array
        $data['ACSAccessKeyId']	= $this->_apikey;
		
		// Set the API response format (JSON or XML)
		$data['Format'] = 'json';
		# $data['Format'] = 'xml';
		
		// Generate the timestamp string
		$cTime = time();
		$data['Timestamp'] = $cTime;
		
		// Generate the random GUID string
		$rGUID = $this->gen_guid(false);
		$data['Rndguid'] = $rGUID;
		
		// Generate the signature string
		$signature = $this->gen_signature($cTime, $rGUID);
		$data['Signature'] = $signature;
		
		// Construct the cURL POST data format
		$postData = http_build_query($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_URL, $this->_apiurl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::TIMEOUT);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
		
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $verifySSL);
		## curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $verifySSL);


        $http_result = curl_exec($ch);
        $error       = curl_error($ch);
        $http_code   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
		
		
		if ($http_code != 200) {
			
			print("HTTP cURL ERROR".PHP_EOL);
			
			print_r($error);
			print_r($http_result);
			print_r($http_code);
			            
        } else {
            
			if($data['Format'] == 'json') {
				
				// Send output based on requested format
				if($output == 'array') {
					return json_decode($http_result, true);		
				} else {
					return $http_result;
				}
			}
        }
		
    }
	
	private function gen_guid($doBrackets=true) {
		
		if (function_exists('com_create_guid')) {
			return com_create_guid();
		} else {
			
			mt_srand((double)microtime()*10000); //optional for php 4.2.0 and up.
			$charid = strtoupper(md5(uniqid(rand(), true)));
			
			$uuid = NULL;
			$hyphen = chr(45);	// "-"
			
			if($doBrackets) 
				$uuid = chr(123);	// "{"
			
			$uuid .= substr($charid, 0, 8).$hyphen
				  .substr($charid, 8, 4).$hyphen
				  .substr($charid,12, 4).$hyphen
				  .substr($charid,16, 4).$hyphen
				  .substr($charid,20,12);
				
			if($doBrackets) 
				$uuid .= chr(125);// "}"
			
			return $uuid;
			
		}
	}
	
	private function gen_signature($epochtime, $rndguid) {
		
		$s = hash_hmac('sha256', $epochtime . $rndguid, $this->_apipkey, true);
		$result = base64_encode($s);
		
		return $result;
	}

 }
?>