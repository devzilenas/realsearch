<?

class Field {
	const T_TEXT    = 1;
	const T_NUMERIC = 2;
	const T_BOOLEAN = 3;

	private $m_type ;
	private $m_name ;
	private $m_value;
	private $m_min  ;
	private $m_max  ;

	function __construct($name, $type) {
		$this->set_name($name);
		$this->set_type($type);
	}

	/**
	 * Check if type is valid.
	 *
	 * @param integer $type
	 *
	 * @return boolean
	 */ 
	private function type_valid($type) {
		return in_array($type, array(self::T_TEXT, self::T_NUMERIC, self::T_BOOLEAN));
	}

	/**
	 * Getter for type.
	 *
	 * @return integer
	 */
	public function type() {
		return $this->m_type;
	}

	/**
	 * Setter for type.
	 *
	 * @param integer $type
	 *
	 * @return void
	 */
	public function set_type($type) {
		$this->m_type = self::type_valid($type) ? $type : self::T_TEXT;
	}

	/**
	 * Getter for m_max.
	 *
	 * @return string
	 */
	public function get_max() {
		return $this->m_max;
	}

	/**
	 * Setter for m_max.
	 *
	 * @todo convert value by type
	 *
	 * @param string $value
	 */
	public function set_max($value) {
		if(is_numeric($value)) {
			$this->m_max = $this->converted_value($value);
		}
		$this->set_value_if_min_max_equal();
	}

	/**
	 * Getter for m_min.
	 *
	 * @return string
	 */
	public function get_min() {
		return $this->m_min;
	}


	/**
	 * Getter for m_name.
	 *
	 * @return string
	 */
	public function name() {
		return $this->m_name;
	}

	/**
	 * Setter for name.
	 *
	 * @param string $value
	 */
	public function set_name($name) {
		$this->m_name = $name;
	}

	/**
	 * Getter for m_value.
	 * 
	 * @return string
	 */
	public function value() {
		return $this->m_value;
	}

	/**
	 * Setter for m_value.
	 *
	 * @param string $value
	 */
	public function set_value($value) {
		$this->m_value = $this->converted_value($value);
	}

	/**
	 * Sets value with field name.
	 *
	 * @param string $name
	 *
	 * @param mixed $value
	 */
	public function set_fv($name, $value) { 
		/** is it min? */
		if(strrpos($name, "_min")) {
			$this->set_min(substr($name, 0, strlen($name) - strlen("_min")) , $value);
		/** is it max? */
		} else if(strrpos($name, "_max")) {
			$this->set_max(substr($name, 0, strlen($name) - strlen("_max")), $value);
		} else {
			$this->set_value($name, $value);
		}

	}

	/**
	 * Converts value by field's type.
	 */
	private function converted_value($value) {
		$val = NULL;
		switch($this->type()) {
		case self::T_NUMERIC:
			$val = (real)$value;
			break;
		case self::T_BOOLEAN:
			$val = (boolean)$value;
			break;
		case self::T_TEXT:
			$val = (string)$value;
		default:
			$val = (string)$value;
		}
		return $val;
	}
}

