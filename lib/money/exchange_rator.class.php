<?

class ExchangeRator {

	/**
	 * Returns currencies for which it has rates.
	 *
	 * @return Currency[]
	 */
	public static function currencies() {
		$ret = array();
		foreach(Currency::short_names() as $name) {
			$ret[] = new Currency($name);
		}
		return $ret;
	}
	
}

