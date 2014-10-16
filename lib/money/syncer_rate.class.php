<?

/**
 * Syncs exchange rates.
 *
 * @version 0.0.2
 */
class SyncerRate {

	const FILES_LOCATION = 'media/rates/';

	/**
	 * Are rates synced for date.
	 *
	 * @param string $date
	 *
	 * @return boolean
	 */
	public static function rates_are_synced_for($date) {
		return self::sync_file_exists_for($date);
	}

	/**
	 * Loads string from file.
	 *
	 * @param string $date
	 *
	 * @return string
	 */
	private static function load_from_file_for($date) {
		return file_get_contents(self::filename_for($date));
	}

	/**
	 * Loads string from webservice.
	 *
	 * @param string $date
	 *
	 * @return void
	 */
	private static function load_from_webservice_for($date) {
		$http_context = Config::http_context(); 
		$source       = sprintf("http://webservices.lb.lt/ExchangeRates/ExchangeRates.asmx/getExchangeRatesByDate?Date=%s", $date);
		if($str = file_get_contents($source, FALSE, stream_context_create($http_context))) {
			file_put_contents(self::filename_for($date), $str);
		}
		return $str;
	}

	/**
	 * Tells whether already exists file.
	 *
	 * @param string $date
	 *
	 * @return boolean
	 */
	private static function sync_file_exists_for($date) {
		return file_exists(self::filename_for($date));
	}

	/**
	 * Returns filename for date.
	 *
	 * @param string $date
	 *
	 * @return string
	 */
	public static function filename_for($date) {
		return sprintf(self::FILES_LOCATION.'rates%s.xml', $date);
	}

	/**
	 * Returns string for rates information.
	 *
	 * @param string $date
	 *
	 * @return string
	 */
	private static function rates_for($date) {
		return self::sync_file_exists_for($date) ?
				 self::load_from_file_for($date) : 
			     self::load_from_webservice_for($date);
	}

	/**
	 * Gets rates for date and puts them to database.
	 *
	 * @param string $date Format is ISO date("Y-m-d").
	 * 
	 * @return void
	 */
	public static function sync_for($date) {
		$rates  = array();
		$ccl    = get_called_class();
		if($xrs = simplexml_load_string(self::rates_for($date))) { 
			foreach($xrs->item as $xr) {
				$rate = new ExchangeRate();

				$rate->ratedate      = $date;
				$rate->from_currency = 'LTL';
				$rate->to_currency   = (string)$xr->currency;
				$rate->quantity      = (integer)$xr->quantity;
				$rate->rate          = (real)  $xr->rate;

				$rates[] = $rate;
			}
		}

		/** delete rates for that date */
		if(!empty($rates)) {
			ExchangeRate::delWhere(array("ratedate" => $date));
		}

		foreach($rates as $rate) {
			$rate->save();
		}

	}

}

