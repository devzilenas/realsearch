<?

/**
 * SQL query builder.
 *
 * @version 0.1.1
 *
 * @todo review
 */
class SqlFilter extends Filter {
	public $on   = '';
	public $join = '';  
	public $from = ''; //@todo review

	public function setOn($on) {
		$this->on = $on;
	}

	public function setJoin($join) {
		$this->join = $join;
	}

	public function __construct($what = '') {
		$this->setWhat($what);
	}

	public function setWhat($what = '') {
		$this->what = $what;
	}

	private static function makeWhereStr($str) {
		return (!empty($str)) ? "WHERE $str" : $str;
	}

	public function makeSQL() {
		return 'SELECT '.$this->what.' FROM '.join(' ', array($this->from, $this->join, $this->on, self::makeWhereStr($this->where), self::makeGroupByStr($this->groupBy), self::makeHavingStr($this->having), self::makeOrderByStr($this->order), self::makeLimitStr($this->limit, $this->offset)));
	}
}

