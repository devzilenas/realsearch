<?

/**
 * This is your API configuration.
 *
 */
class ConfigApi3 {
	const MY_API_KEY  = '51b563f25a68491361877251b563f25a';
	const DATA_SOURCE = 'http://localhost/realsearch/api.php';

	/**
	 * Get your api key.
	 *
	 * @return string
	 */
	public static function api_key() {
		return self::MY_API_KEY;
	}

	/**
	  * Get data source.
	  *
	  * @return string
	  */
	public static function data_source() {
		return self::DATA_SOURCE;
	}

}

