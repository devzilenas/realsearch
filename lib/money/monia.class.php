<?
/**
 * @author Marius Žilėnas
 *
 * @version 0.0.2
 */

/**
 * Money.
 */
class Monia {

	private $m_amount  ;
	private $m_currency;

	const DEFAULT_CENTS = 2;

	/**
	 * Returns monia converted to currency by todays rate.
	 *
	 * @param Currency $currency
	 *
	 * @return Monia
	 */
	public function converted_to(Currency $currency) {
		$rate = ExchangeRate::get_rate(date("Y-m-d"), $this->currency(), $currency); 
		return $rate->changer($this, $currency);
	}

	/**
	 * Whole part.
	 *
	 * @return real
	 */
	public function whole() {
		return $this->amount() - $this->cents();
	}

	/**
	 * Cents part.
	 *
	 * @return real
	 */
	public function cents() {
		return $this->amount() - (int)$this->amount();
	}

	/**
	 * Float representation of amount.
	 *
	 * @return Real
	 */
	public function as_f() {
		return $this->amount();
	}

	/**
	 * String representation of money.
	 *
	 * @param integer $precision (optional)
	 *
	 * @return string
	 */
	public function to_s($precision = 0) {
		return sprintf("%.{$precision}f %s", $this->as_f(), $this->currency()->name());
	}

	/**
	 * String representation of money
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->to_s(self::DEFAULT_CENTS);
	}

	public function __construct(Currency $c, $amount) {
		$this->set_currency($c);
		$this->set_amount($amount);
	}

	/**
	 * Set amount.
	 *
	 * @param mixed $amount
	 *
	 * @return void
	 */
	public function set_amount($amount) {
		$this->m_amount = (real)$amount;
	}

	/**
	 * Getter for m_amount.
	 *
	 * @return Real
	 */
	public function amount() {
		return $this->m_amount;
	}
	
	/**
	 * Setter for m_currency.
	 *
	 * @param Currency $currency
	 *
	 * @return void
	 */
	public function set_currency(Currency $currency) {
		$this->m_currency = $currency;
	}

	/**
	 * Getter for m_currency.
	 *
	 * @return Currency
	 */
	public function currency() {
		return $this->m_currency;
	}

}

