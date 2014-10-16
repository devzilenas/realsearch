<?

/**
 * Prints session data.
 *
 * @param boolean $override Overrides the configuration check.
 *
 * @return void
 */
function print_session_debug($override = FALSE) {
	if ($override || Config::$SESSION_SHOW) {
		echo '<p>'.t('Session contents').':</p>'; print_r($_SESSION);
		if(isset($_REQUEST['clear_session'])) session_destroy();
			else echo '<a href="?clear_session">'.t("clear session").'</a>';
	}
}

/**
 * Returns value if has it in session.
 *
 * @param string $name
 *
 * @param string $field
 *
 * @return mixed|NULL
 */
function hasV($name, $field) {
	if (isset($_SESSION[$name]) && is_array($_SESSION[$name]) && isset($_SESSION[$name][$field])) return $_SESSION[$name][$field];
	else return NULL;
}

/**
 * Gets data from session and unsets it.
 *
 * @param string $data
 *
 * @param string $field
 *
 * @param string $alternative (optional) Alternative if no value found.
 *
 * @return mixed
 */
function s($data, $field, $alternative='') {
	$ret = $alternative;
	if(isset($_SESSION[$data]) && isset($_SESSION[$data][$field])) {
		$val = $_SESSION[$data][$field];
		unset($_SESSION[$data][$field]);
		$ret = $val;
	} 
	return $ret;
}

