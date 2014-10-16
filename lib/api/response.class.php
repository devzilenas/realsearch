<?

/**
 * This class is for response data of API operation.
 *
 * It contains json objects as well as response information.
 */
class Response { 
	/** @type string Holds redirect address. */
	private $m_redirect;
	/** @type boolean Response success. */
	private $m_success ; 

	/** @type string Message. */
	private $m_message ;

	/** 
	 * @type array Array of arrays
	 *
	 * Array index tells objects class and key holds Jobj.
	 */
	private $m_jobjs = array();

	const DEFAULT_MSG_SUCCESS = 'Success! :)';
	const DEFAULT_MSG_NOSUCC  = 'Something went wrong?!';

	/**
	 * Setter for m_jobjs.
	 *
	 * @param array $jobjs
	 */
	public function add_jobjs(array $jobjs) { 
		foreach($jobjs as $jobj) {
			$this->m_jobjs[$jobj->classname()][] = $jobj; 
		}
	}
	/**
	 * Getter for m_jobjs.
	 *
	 * @return array
	 */
	public function &jobjs() {
		return $this->m_jobjs;
	}
	/**
	 * Builds from jobjs.
	 */
	private function buildJobjs() {
	}

	/**
	 *  {
	 * 		"DbObjs": [
	 * 			"classname" : [objects],
	 * 			"classname" : [objects],
	 * 			"classname" : [objects],
	 * 			"classname" : [objects],
	 * 		],
	 * 		"Response": object
  	 *	}
	 */

	public function to_jobj() {

		//Response fields.
		$sd = array(
			'redirect' => $this->redirect(),
			'success'  => $this->success(),
			'message'  => $this->message());
		$rjobj = new Jobj(get_called_class(), $sd);
		$rstr  = $rjobj->to_s();

		// Jobjs
		$alljobjs = $this->jobjs();

		$jcs   = array(); //collection of jobjs

		foreach($alljobjs as $classname => $jobjs) 
			if(is_array($jobjs)) 
				$jcs[] = Json::jobjs2jc($jobjs, $classname); 

		$dbobjsstr = Json::jcols2str($jcs, 'DbObjs');
		$out = '{'.$dbobjsstr."\r\n, \"Response\" : $rstr}";
		return $out;
	}

	/**
	 * Constructs response.
	 *
	 * @param boolean $success
	 */ 
	public static function respond($success, $msg = NULL) {
		return (TRUE === $success) ? 
			self::successful($msg) : 
			self::unsuccessful($msg);
	}

	/**
	 * Constructs success response.
	 *
	 * @param string $msg Message.
	 *
	 * @return Response
	 */
	public static function successful($msg = NULL) {
		if(NULL === $msg) $msg = self::DEFAULT_MSG_SUCCESS;
		$response = new self();
		$response->set_success(TRUE);
		$response->set_message(t($msg));
		return $response;
	}

	/**
	 * Constructs no success response.
	 *
	 * @param string $msg Message.
	 *
	 * @return Response
	 */
	public static function unsuccessful($msg = NULL) { 
		if(NULL === $msg) $msg = self::DEFAULT_MSG_NOSUCC;
		$response = new self();
		$response->set_success(FALSE);
		$response->set_message(t($msg));
		return $response;
	}

	/**
	 * Getter for m_message.
	 *
	 * @return string
	 */ 
	public function message() {
		return $this->m_message;
	}

	/**
	 * Setter for m_message.
	 *
	 * @param string $value Message.
	 */
	public function set_message($value) {
		$this->m_message = $value;
	}

	/**
	 * Getter for m_redirect.
	 *
	 * @return string
	 */
	public function redirect() {
		return $this->m_redirect;
	}

	/**
	 * Setter for m_redirect.
	 *
	 * @param string $value Redirect address.
	 */
	public function set_redirect($value) { 
		$this->m_redirect = $value;
	}

	/** 
	 * Getter for m_success.
	 *
	 * @return boolean
	 */
	public function success() {
		return $this->m_success;
	}

	/**
	 * Setter for m_success.
	 * 
	 * @param boolean $value
	 */
	public function set_success($value) { 
		$this->m_success = $value;
	}
}

