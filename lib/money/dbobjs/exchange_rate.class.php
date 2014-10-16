<?
class ExchangeRate extends Dbobj implements DbobjInterface {

	public static function fields() {
		return array(
			new Field('id', Field::T_NUMERIC, "%d", FALSE, FALSE),
			new Field('ratedate', Field::T_TEXT),
			new Field('from_currency', Field::T_TEXT),
			new Field('to_currency', Field::T_TEXT),
			new Field('quantity', Field::T_NUMERIC, "%d"),
			new Field('rate', Field::T_NUMERIC, "%.5f") 
		);
	}

	/**
	 * Gets rate.
	 *
	 * @param string $date
	 *
	 * @param Currency $from
	 *
	 * @param Currency $to
	 *
	 * @return self|NULL
	 */
	public static function get_rate($date, Currency $from, Currency $to) {
		$ret  = NULL;
		$rate = NULL;

		/** source and destination currencies are the same? */
		if(!$rate && $from->name() == $to->name()) {
			$nr = new ExchangeRate();
			$nr->ratedate      = $date;
			$nr->from_currency = $from->name();
			$nr->to_currency   = $to->name();
			$nr->quantity      = 1;
			$nr->rate          = 1;
			$ret               = $nr;
			$rate              = $nr;
		}

		$filter = ExchangeRate::newFilter(); 
		$filter->setWhere(array(
			'ExchangeRate.ratedate'      => $date,
			'ExchangeRate.from_currency' => $from->name(),
			'ExchangeRate.to_currency'   => $to->name()));
		$filter->setLimit(1);

		if(!$rate) {
			/** exists? */
			if($rate = current(ExchangeRate::find($filter))) {
				$ret  = $rate;
			}
		}

		if(!$rate) {
			$filter->setWhere(array(
				'ExchangeRate.ratedate'      => $date,
				'ExchangeRate.from_currency' => $to->name(),
				'ExchangeRate.to_currency'   => $from->name()));
			/** exists inverted? */
			if($rate = current(ExchangeRate::find($filter))) {
				$nr = new ExchangeRate();
				$nr->ratedate      = $date;
				$nr->from_currency = $from->name();
				$nr->to_currency   = $to->name();
				$nr->quantity      = 1.0;
				$nr->rate          = (1.0/$rate->rate)/$rate->quantity;
				$ret = $nr;
			}
		}

		/** not found rate */
		if(!$rate) {
			/** No rate found look for the last known rate. */
			$filter->setWhere(array(
				'ExchangeRate.from_currency' => $from->name(),
				'ExchangeRate.to_currency'   => $to->name()));
			$filter->setOrderBy('ratedate DESC');
			if($rate = current(ExchangeRate::find($filter))) { 
				$ret = $rate;
			} else {
				/** look for last inverted rate */
				$filter->setWhere(array(
					'ExchangeRate.from_currency' => $to->name(),
					'ExchangeRate.to_currency'   => $from->name()));
				$filter->setOrderBy('ratedate DESC');
				if($rate = current(ExchangeRate::find($filter))) { 
					$ret = $rate;
				}
			}
			if(!$rate) {
				/**
				 * no rate could be found. Make 1:1 rate. 
				 * @todo add error message "No rate found" to the log
				 */
				$nr = new ExchangeRate();
				$nr->ratedate      = $date;
				$nr->from_currency = $from->name();
				$nr->to_currency   = $to->name();
				$nr->quantity      = 1;
				$nr->rate          = 1;
				$ret = $nr;
			}
		}
		return $ret;
	}

	/**
	 * Uses rate but calculates rate on-fly if needed.
	 *
	 * @param Monia $amount
	 *
	 * @param Currency $currency
	 *
	 * @return Monia|NULL
	 */
	public function changer(Monia $amount, Currency $currency) {
		$ret = NULL;
		/** Has rate. */
		if($amount->currency()->name() == $this->from_currency && $this->to_currency == $currency->name()) {
			$ret = new Monia($currency, ($amount->as_f() * $this->quantity)/$this->rate);
		}
		return $ret;
	}

}
