<?

/**
 * Logger for messages.
 * 
 * @version 0.1.1
 */
class Logger {

	/**
	 * Log info message.
	 *
	 * @param string $msg
	 *
	 * @return void
	 */
	public static function info($msg) {
		$info = new InfoMsg($msg);
		self::add($info);
	}

	/**
	 * Log undefined error.
	 *
	 * @param string $msg
	 *
	 * @return void
	 */
	public static function undefErr($msg) {
		self::err('UNDEF', $msg);
	}

	/**
	 * Log error message.
	 *
	 * @todo: rename all calls Logger::err to Logger::error
	 *
	 * @param integer $id Id of the error message type.
	 *
	 * @param string $msg Info message.
	 *
	 * @return void
	 */
	public static function error($id, $msg) {
		return self::err($id, $msg);
	}

	/**
	 * Log error message.
	 * @todo rename to error.
	 *
	 * @param integer $id Id of the error message type.
	 *
	 * @param string $msg Message.
	 * 
	 * @return void
	 */
	public static function err($id, $msg) {
		$tmp = array();
		if (is_array($msg)) {
			$tmp = $msg;
		} else if (is_string($msg)) {
			$tmp[] = $msg;
		}
		foreach ($tmp as $m) {
			$err = new ErrMsg($id, $m);
			self::add($err);
		}
	}

	/**
	 * Get next error.
	 *
	 * @return Error
	 */
	public static function nextErr() { 
		if (self::ok()) {
			return $_SESSION['MSG']->shiftErr();
		}
	}

	/**
	 * Get next info message.
	 *
	 * @return Info
	 */
	public static function nextInfo() {
		if (self::ok()) {
			return $_SESSION['MSG']->shiftInfo();
		}
	}

	/**
	 * Add log message to the queue.
	 *
	 * @param Msg $msg
	 *
	 * @return void
	 */
	private static function add($msg) {
		if (!self::ok()) {
			$queue = new MsgQueue();
			$_SESSION['MSG'] = $queue;
		}
		$queue = $_SESSION['MSG'];
		$queue->add($msg);
	}

	private static function ok() { 
		return isset($_SESSION['MSG']) && ($_SESSION['MSG'] instanceof MsgQueue);
	}

}

/**
 * Queue for messages.
 */
class MsgQueue {
	private $queue = array();

	/**
	 * Add message.
	 *
	 * @param Msg $msg
	 */
	public function add($msg) {
		$type = get_class($msg);
		if (!isset($this->queue[$type]) || !is_array($this->queue[$type])) $this->queue[$type] = array();
		array_push($this->queue[$type], $msg);
	}

	/**
	 * Get error message from the queue.
	 *
	 * @return Error
	 */
	public function shiftErr() {
		return $this->shift('ErrMsg');
	}

	/**
	 * Get info message from the queue.
	 *
	 * @return Info
	 */
	public function shiftInfo() {
		return $this->shift('InfoMsg');
	}

	private function shift($type) {
		if (isset($this->queue[$type]) && is_array($this->queue[$type])) {
			return array_shift($this->queue[$type]);
		} else {
			return NULL;
		}
	}
}

/**
 * Message.
 */
class Msg {
	public $id;
	public $msg;
}

/**
 * Error message.
 */
class ErrMsg extends Msg {

	public function __construct($id, $msg) {
		$this->id  = $id;
		$this->msg = $msg;
	}

	/**
	 * Error message types.
	 */
	public static $ID = array(
			'BAD_PASS'    => 'Password unsuitable!',
			'BAD_EMAIL'   => 'E-mail unsuitable!',
			'BAD_LOGIN'   => 'User name unsuitable!',
			'PASS_MATCH'  => 'Passwords don\'t match!',
			'INUSE_EMAIL' => 'E-mail already in use!',
			'INUSE_LOGIN' => 'User name already in use!',
			'LOGIN_FAIL'  => 'Not logged in!',
			'NEW_USER_FAIL' => 'User not created!',
			'UNDEF'       => 'Error!',
			'NO_VAL'      => 'Provide value'
		); 
}

/**
 * Info message.
 */
class InfoMsg extends Msg {

	public function __construct($msg) {
		$this->msg = $msg;
	}

	public static $ID = array(
		'UNDEF' => 'A msg the usr.');
}

