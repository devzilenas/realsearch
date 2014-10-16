<?

/**
 * Base class for request management.
 *
 * @version 0.1.3
 */
class Request {
	/** TRUE when request is to api. */
	private static $m_api;
	/** values that are returned by request */
	public static $m_out = array();

	/**
	 * Setter for m_out.
	 *
	 * @param string $name
	 *
	 * @param mixed $value
	 *
	 * @return void
	 */
	public static function set_out($name, $value) {
		self::$m_out[$name] = $value;
	}

	/**
	 * Getter for m_out.
	 *
	 * @param string (optional) $name
	 *
	 * @return mixed|NULL
	 */
	public static function out($name = NULL) {
		$out = NULL;
		if(NULL === $name) {
			$out = self::$m_out;
		} else {
			if(isset(self::$m_out[$name])) {
				$out = self::$m_out[$name];
			}
		}
		return $out;
	}

	/**
	 * Setter for m_api.
	 *
	 * @param boolean $api
	 */
	public static function setApi($api) { 
		self::$m_api = $api;
	}

	/**
	 * Getter for m_api.
	 *
	 * @return boolean
	 */
	public static function isApi() {
		return self::$m_api;
	}

	/**
	 * Returns base location.
	 *
	 * @return string
	 */
	public static function base() {
		return Config::base();
	}

	/**
	 * Redirects to BASE
	 *
	 * @return void
	 */
	public static function r2b() {
		self::hlexit(self::base());
	}

	/**
	 * Checks whether is action.
	 *
	 * @param string $name Action name
	 *
	 * @return boolean
	 */
	public static function isAction($name) {
		return isset($_POST['action']) && $name === $_POST['action'];
	}

	/**
	 * Checks whether is view.
	 *
	 * @param string $name
	 * 
	 * @return boolean
	 */
	public static function isView($name) {
		return isset($_GET['view']) && isset($_GET[c2u($name)]);
	}

	/**
	 * Checks whether is new.
	 * 
	 * @param string $classname
	 *
	 * @return boolean
	 */
	public static function isNew($classname) {
		return isset($_GET['new']) && isset($_GET[c2u($classname)]);
	}

	/**
	 * Checks whether is form submit to create a new object.
	 *
	 * @return boolean 
	 */
	public static function isCreate() {
		return isset($_POST['action']) && 'create' == $_POST['action'];
	}

	/**
	 * Is it edit action.
	 *
	 * @param string $class_name 
	 *
	 * @return boolean
	 */
	public static function isEdit($class_name) {
		return isset($_GET['edit'], $_GET[strtolower(c2u($class_name))]);
	}

	/**
	 * Is delete action.
	 *
	 * @param string $class_name Class name of object that is deleted.
	 *
	 * @return boolean
	 */
	public static function isDelete($class_name) {
		return self::isAction('delete') && isset($_GET[c2u($class_name)]);
	}

	/**
	 * Is it list action.
	 *
	 * @param string $class_name Class name of objects that are listed.
	 *
	 * @return boolean
	 */
	public static function isList($class_name) {
		return isset($_GET['list'],
		  		     $_GET[c2u(Language::pluralize($class_name))]);
	}

	/**
	 * Returns value from request.
	 *
	 * @param string $name
	 *
	 * @return integer
	 */
	public static function get0($name) {
		return (!empty($_REQUEST[$name])) ? (int)$_REQUEST[$name] : 0;
	}

	/**
	 * Returns value from request.
	 *
	 * @param string $name
	 *
	 * @return mixed|NULL
	 */
	public static function getNull($name) {
		return (!empty($_REQUEST[$name])) ? $_REQUEST[$name] : NULL;
	}

	/**
	  * Gets value as array from GET.
	  *
	  * @param string $name
	  * 
	  * @return array
	  */
	public static function gA($name) {
		return isset($_GET[$name]) && is_array($_GET[$name]) ? $_GET[$name] : array();  
	}

	/**
	 * Gets value from POST.
	 *
	 * @param string $name
	 * 
	 * @return array
	 */
	public static function gPostArray($name) {
		return isset($_POST[$name]) && is_array($_POST[$name]) ? $_POST[$name] : array();
	}

	/**
	 * Redirects and exits.
	 *
	 * @param string location
	 *
	 * @return void
	 */
	public static function hlexit($location) {
		header("Location: ".$location);
		exit;
	}

	/**
	 * Saves object to session. 
	 *
	 * @param $name string Class name of the object wich data is saved.
	 * @param $data array Data to save.
	 *
	 * @return void
	 */
	static function saveToSession($name, array $data) {
		$_SESSION[c2u($name)] = $data;
	}

	/**
	 * Loads object from session and unsets it.
	 *
	 * @param $name string Name of the Class.
	 *
	 * @return array|NULL
	 */
	protected static function loadFromSessionU($name) {
		if(isset($_SESSION[$name])) {
			$n   = c2u($name);
			$ret = $_SESSION[$n];
			unset($_SESSION[$n]);
			return $ret;
		}
	}

	/**
	 * Saves validation data into session.
	 *
	 * @param $class string Name of the class to which object belongs.
	 * @param $validation array Validation information.
	 *
	 * @return void
	 */
	protected static function saveValidation($class, array $validation) {
		$_SESSION[c2u($class).'_validation'] = $validation;
	}

	/**
	 * Loads object data from POST.
	 *
	 * @param $class string Class name of the object.
	 *
	 * @param $fields array Field names to load data from.
	 * 
	 * @return object Object of the class data.
	 */
	protected static function oFromForm($class, array $fields) {
		$cl = c2u($class);
		if(isset($_POST[$cl])) {
			$o = $class::fromForm($_POST[$cl], $fields);
			return $o;
		}
	}
}

