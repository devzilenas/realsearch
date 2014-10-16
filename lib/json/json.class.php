<?

/**
 * Json.
 *
 * @version 0.1.1
 *
 * @todo review
 */

/**
 * Json pair.
 */
class Jpair {
	private $m_name;  //Json type <string>
	private $m_value; //Json type <value>


	/**
	 * Setter for $m_name.
	 *
	 * @return void
	 */
	private function setName($name) {
		$this->m_name = $name;
	}

	/**
	 * Getter for $m_name.
	 * 
	 * @return string
	 */
	public function name() {
		return $this->m_name;
	}

	/**
	 * Setter for $m_value.
	 *
	 * @return void
	 */
	private function setValue($value) {
		$this->m_value = $value;
	}

	/**
	 * Getter for $m_value.
	 *
	 * @return string
	 */
	public function value() {
		return $this->m_value;
	}

	public function __construct($name, $value) {
		$this->setName($name);
		$this->setValue($value);
	}

	/**
	 * Converts value to Json value.
	 *
	 * @param mixed $v Value to convert.
	 *
	 * @return mixed
	 */
	private static function c2v($v) {
		$ret = NULL;
		//Check if value is numeric
		if(is_numeric($v)) {
			$ret = $v;
		} else if(is_bool($v)) {
			$ret = ($v ? 'true' : 'false');
		} else if(NULL === $v) {
			$ret = 'null';
		} else {
			$ret = Dbobj::edq($v);
		}
		return $ret;
	}

	/**
	 * Returns value as quoted and escaped string.
	 *
	 * @return string
	 */
	public function valueStr() {
		return self::c2v($this->value());
	}

}
/**
 * Json object.
 */
class Jobj {
	private $m_classname = '';
	private $m_pairs     = array();

	/**
	 * Getter for m_classname.
	 *
	 * @return string
	 */
	public function classname() {
		return $this->m_classname;
	}

	/**
	 * Setter for m_class.
	 *
	 * @param string $classname Class name
	 */
	private function set_classname($classname) {
		$this->m_classname = $classname;
	}
	
	/**
	 * Returns reference to pairs array.
	 *
	 * @return array
	 */
	public function &pairs() {
		return $this->m_pairs;
	}

	/**
	 * Setter for m_pairs.
	 *
	 * @param array $pairs
	 */
	private function setPairs(array $pairs) {
		$this->m_pairs = $pairs;
	}

	/**
	 * Converts array data to pairs.
	 *
	 * @param array $data Data to convert to pairs.
	 *
	 * @return void
	 */
	private function d2p(array $data) { 
		$ret = array();
		foreach($data as $str => $value) {
			$ret[] = new Jpair($str, $value);
		}
		return $ret;
	}

	/**
	 * @param string $classname Classname of objects.
	 * @param array $data
	 */
	public function __construct($classname, array $data) {
		$this->set_classname($classname);
		$this->addPairs($this->d2p($data));
	}

	/**
	 * Adds pairs at once.
	 *
	 * @param array $pairs Pairs of type Jpair.
	 *
	 * @return void
	 */
	public function addPairs(array $pairs) {
		foreach($pairs as $pair)
			$this->addPair($pair);
	}

	/**
	 * Adds value to pairs.
	 *
	 * @param string $str Index name.
	 *
	 * @param mixed $value Value.
	 *
	 * @return void
	 */
	public function addPair(Jpair $pair) {
		$pairs = &$this->pairs();
		array_push($pairs, $pair);
	}

	/**
	 * Pairs as string.
	 *
	 * @return string
	 */
	public function to_s() {
		$out = '';
		$pairs = &$this->pairs();
		$outs  = array(); 
		foreach($pairs as $pair) {
			$outs[] = Dbobj::dq($pair->name()).' : '.$pair->valueStr() ;
		}
		return '{'.join(', ', $outs).'}';
	}

}

class Json {

	/**
	 * Converts jcollections to string.
	 *
	 * @param array $jcols
	 *
	 * @param string $name Name of collection.
	 *
	 * @return string
	 */
	public static function jcols2str(array $jcols, $name) {
		return Dbobj::dq($name).' : {'.join(',',$jcols).'}';
	}
	/**
	 * Converts array of Jobjs to json collection.
	 *
	 * @param array $pairs Data array to convert from.
	 *
	 * @param string $name Name of the collection
	 *
	 * @return string
	 */
	public static function jobjs2jc(array $jobjs, $name) {
		$out = '' ;
		if(is_array($jobjs)) {
			$outs = array();
			foreach($jobjs as $jobj) 
				$outs[] = $jobj->to_s(); 
			$out = Dbobj::dq($name).' : ['."\r\n".join(",\r\n", $outs).']';
		}
		return $out;
	}

}

