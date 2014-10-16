<?

class Wallet extends Dbobj implements DbobjInterface {

	public static function fields() {
		return array(
			new Field('id', Field::T_NUMERIC, "%d"),
			new Field('user_id', Field::T_NUMERIC, "%d"),
			new Field('name', Field::T_TEXT),
			new Field('currency', Field::T_TEXT)
		);
	}

	/**
	 * Returns last lines from wallet.
	 *
	 * @param integer $lines (optional) Lines number
	 *
	 * @return WalletLine[]
	 */
	public function last_lines($lines = 10) {
		$filter = WalletLine::newFilter();
		$filter->setFrom(array("WalletLine" => "wl"));
		$filter->setOrderBy("wl.id DESC");
		$filter->setLimit($lines);
		return WalletLine::find($filter);
	}

	/**
	 * Returns amount left.
	 *
	 * @param User $user
	 *
	 * @return float
	 */
	public function left() {
		$filter = new SqlFilter("SUM(amount) as `left`");
		$filter->setFrom(WalletLine::tableName()." wl");
		$filter->setWhere(sprintf("wl.wallet_id = %d", $this->id));
		$filter->setGroupBy("wl.wallet_id");
		$amount = NULL;
		if($wl = current(WalletLine::find($filter))) {
			$amount = $wl->left;
		}
		return new Monia(Config::base_currency_wallet(), $amount);
	}

	/**
	 * Takes amount of money from the wallet.
	 *
	 * @param Monia $amount
	 *
	 * @param string $what
	 *
	 * @param mixed $ao Attached object
	 *
	 * @return boolean False if not enough in wallet.
	 */
	public function take(Monia $amount, $what, $ao = NULL) { 
		$a = abs($amount->as_f());
		if($a > $this->left()->as_f()) {
			$ret = FALSE;
		} else {
			$wl = $this->new_wallet_line();
			if(NULL !== $ao) {
				$wl->attach_to($ao);
			}
			$wl->what        = $what;
			$wl->amount_left = -$a + $this->left()->as_f();
			$wl->amount      = -$a  ;
			$wl->save()             ;
			$ret             = TRUE ;
		}
		return $ret;
	}

	/**
	 * Puts amount of money to the wallet.
	 *
	 * @param Monia $amount
	 *
	 * @param string $what
	 *
	 * @return void
	 */
	public function put(Monia $amount, $what) {
		$wl = $this->new_wallet_line();
		$wl->amount = $amount->as_f();
		$wl->what   = $what;
		$wl->amount_left = $wl->amount + $this->left()->as_f();
		$wl->save();
	}

	public function new_wallet_line() {
		$wl            = new WalletLine();
		$wl->wallet_id = $this->id;
		return $wl;
	}

	/**
	 * Returns wallet for User.
	 *
	 * @param User $user
	 *
	 * @return self
	 */
	public function wallet_for(User $user) {
		return Wallet::load_by(array('user_id' => $user->id));
	}

	/**
	 * Base currency
	 *
	 * @return Currency
	 */
	public static function base_currency() {
		return new Currency(Config::base_currency_wallet());
	}

	/**
	 * 
	 * @return Monia
	 */
	public function asc($field_name, Currency $currency = NULL) {
		$ret = NULL;
		if(NULL === $currency) {
			/** Has currency */
			if(Currency::is_valid($this->currency)) {
				$curr = new Currency($this->currency);
			} else {
				$ret  = Config::base_currency_wallet();
			}
		}
		$ret = parent::asc($field_name, new Currency($this->currency));
	}

}

