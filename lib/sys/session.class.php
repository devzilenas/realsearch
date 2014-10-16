<?

/**
 * Getter for values from session.
 * @version 0.1.1
 */
class Session {

	/**
	 * Returns array from session or empty array when not found.  
	 * @return array
	 */
	public static function gSessionArray($name) {
		return ((isset($_SESSION[$name]) && is_array($_SESSION[$name])) ? $_SESSION[$name] : array());
	}

	/**
	 * Session value get and unset.
	 *
	 * @param string $name
	 *
	 * @return mixed
	 */
	public static function sgu($name) {
		$val = $_SESSION[$name];
		unset($_SESSION[$name]);
		return $val;
	}

	/**
	 * Gets value from session or 0.
	 *
	 * @param string $name 
	 *
	 * @return mixed
	 */
	public static function g0($name) {
		return isset($_SESSION[$name]) ? $_SESSION[$name] : 0;
	}

	/**
	 * Gets value or default.
	 *
	 * @param string $name
	 *
	 * @param mixed $default
	 *
	 * @return integer
	 */
	public static function g0d($name, $default) {
		$v = self::g0($name);
		if(0 === $v) {
			$v = $default;
		}
		return $v;
	}
}

