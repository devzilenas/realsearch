<?

/**
 * This is API configuration.
 */
class ConfigApi3 {

	/** You should not leave those below empty: */
	/** API key: get it from Real website. */
	const MY_API_KEY  = '';
	/** API data source: this should be http://www.realsearch.com/ */
	const DATA_SOURCE = '';
	/** end */

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

