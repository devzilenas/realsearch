<?

class WalletLine extends Dbobj implements DbobjInterface {

	public static function fields() {
		return array(
			new Field('id', Field::T_NUMERIC, "%d"),
			new Field('wallet_id', Field::T_NUMERIC, "%d"),
			new Field('amount', Field::T_NUMERIC, "%.2f"),
			new Field('currency', Field::T_TEXT),
			new Field('attached_to', Field::T_TEXT),
			new Field('attached_id', Field::T_NUMERIC, "%d"),
			/** Amount left in wallet at that time */
			new Field('amount_left', Field::T_NUMERIC, "%.2f"),
			new Field('what', Field::T_TEXT, "%s"),
			new Field('on_', Field::T_TEXT, "%s")
		);
	}

	/**
	 *
	 * @return Monia
	 */
	public function asc($field_name, Currency $currency = NULL) {
		if(NULL === $currency && Currency::is_valid($this->currency)) {
			$curr = new Currency($this->currency);
		} else {
			$curr = $currency;
		}

		return parent::asc($field_name, $curr);
	}

	public static function base_currency() {
		return Config::base_currency_wallet();
	}

	public function beforeInsert() {
		if('' == trim($this->on_)) {
			$this->on_ = self::toDateTime($_SERVER['REQUEST_TIME']);
		}

		if( !Currency::is_valid($this->currency) ) {
			$this->currency = self::base_currency()->name();
		}

	}

}

