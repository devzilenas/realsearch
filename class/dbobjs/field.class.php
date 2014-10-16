<?
/**
 * Field for data
 */
class Field extends DbObj {

	protected static $FIELDS = array(
		'id'    => '%d',
		'oid'   => '%d',
		'name'  => '%s',
		'value' => '%s');

	const T_TEXT    = 1;
	const T_NUMERIC = 2;
	const T_BOOLEAN = 3;

	private $m_type ;
	/** Internal name */
	private $m_name ;
	/** Name to show to user */
	private $m_out_name;

	private $m_value;
	private $m_min  ;
	private $m_max  ;
	private $m_format;
	private $m_is_virtual;
	private $m_is_searchable;

	function __construct($name, $type, $format = NULL, $virtual = NULL, $searchable = NULL, $out_name = NULL) {
		$this->set_name($name);
		$this->set_type($type);

		if(NULL !== $format) {
			$this->set_format($format);
		}

		if(NULL !== $virtual) {
			$this->set_virtual($virtual);
		}

		if(NULL !== $searchable) {
			$this->set_searchable($searchable);
		}

		if(NULL !== $out_name) {
			$this->set_out_name($out_name);
		} 
	}

	/**
	 * Setter for m_out_name.
	 *
	 * @param string
	 *
	 * @return void
	 */
	public function set_out_name($out_name) {
		$this->m_out_name = $out_name;
	}

	/**
	 * Getter for m_out_name
	 *
	 * @return string
	 */
	public function out_name() {
		return $this->m_out_name;
	}

	/**
	 * Setter for m_searchable.
	 *
	 * @param boolean $searchable
	 *
	 * @return void
	 */
	public function set_searchable($searchable) { 
		$this->m_is_searchable = (boolean)$searchable;
	} 

	/**
	 * Getter for m_is_searchable.
	 *
	 * @return boolean
	 */
	public function is_searchable() {
		return $this->m_is_searchable;
	}

	/**
	 * Setter for m_virtual.
	 *
	 * @param boolean $virtual
	 *
	 * @return void
	 */
	public function set_virtual($virtual) {
		$this->m_is_virtual = (boolean)$virtual;
	}

	/**
	 * Getter for m_is_virtual.
	 *
	 * Virtual field has no column for object. Virtual field's data is stored in one of the Value tables.
	 *
	 */
	public function is_virtual() {
		return TRUE === $this->m_is_virtual;
	}

	/**
	 * Is numeric field?
	 *
	 * @return boolean
	 */
	public function isnumeric() {
		return $this->type() == self::T_NUMERIC;
	}

	/**
	 * Is field text?
	 *
	 * @return boolean
	 */
	public function istext() {
		return $this->type() == self::T_TEXT;
	}

	/**
	 * Is field boolean.
	 *
	 * @return boolean
	 */
	public function isboolean() {
		return $this->type() == self::T_BOOLEAN;
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
	 * Returns type name.
	 *
	 * @return string
	 */
	public function type_name() {
		return self::tn($this);
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
	 * @param string $value
	 */
	public function set_max($value) {
		$this->m_max = $this->converted_value($value);
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
	 * Setter for m_min.
	 *
	 * @param string $value
	 */
	public function set_min($value) {
		$this->m_min = $this->converted_value($value);
		$this->set_value_if_min_max_equal();
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
			$this->set_min($value);
		/** is it max? */
		} else if(strrpos($name, "_max")) {
			$this->set_max($value);
		} else {
			$this->set_value($name, $value);
		}
	}

	/**
	 * Setter for m_format.
	 *
	 * @param string $format
	 *
	 * @return void
	 */
	public function set_format($format) {
		$this->m_format = $format;
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

	/**
	 * Getter for field format.
	 *
	 * @return string
	 */
	public function format() { 
		$ret = '%s';
		if(NULL !== $this->m_format) {
			$ret = $this->m_format;
		} else {
			switch($this->type()) {
				case self::T_TEXT:
					$ret = '%s';
					break;
				case self::T_NUMERIC:
					$ret = '%d';
					break;
				case self::T_BOOLEAN:
					$ret = '%d';
					break;
			}
		}
		return $ret;
	}

	/**
	 * Get type name for field.
	 *
	 * @param Field $field
	 *
	 * @return string
	 */
	public static function tn(Field $field) {
		$ret = 'undefined';
		switch($field->type()) {
			case self::T_TEXT:
				$ret = 'text';
				break;
			case self::T_NUMERIC:
				$ret = 'numeric';
				break;
			case self::T_BOOLEAN:
				$ret = 'boolean';
				break;
		}
		return $ret;
	}

	public function __toString() {
		return $this->name;
	}

	/**
	 * Sets value if min and max equal.
	 *
	 * @return void
	 */
	private function set_value_if_min_max_equal() {
		if($this->get_min() == $this->get_max() && NULL !== $this->get_max() && NULL !== $this->get_min() ) {
			$this->set_value($this->get_min());
			/** unset min max */
			$this->m_min = NULL;
			$this->m_max = NULL;
		}
	}

}

