<?

class ApiManager {

	const API_KEY_LENGTH = 32;

	/**
	  * @param string $key
	  *
	  * @return boolean
	  */
	public static function is_valid_api_key($key) {
		return strlen($key) == self::API_KEY_LENGTH;
	}

	public static function generate_api_key() {
		return substr(
				str_replace('.', '', uniqid("", TRUE).uniqid("", TRUE)), 0, self::API_KEY_LENGTH);
	}

}

