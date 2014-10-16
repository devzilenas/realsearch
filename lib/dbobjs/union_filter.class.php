<?

/**
 * Make SQL query with UNION.
 *
 * Filter takes other filters for union.
 *
 * This query can have order and limit.
 *
 * in a search value has to appear in every table by which we search
 *
 *
 * or 
 *

SELECT vt.oid

FROM values_text vt

JOIN values_numeric vn
ON (vt.oid = vn.oid)

JOIN  values_boolean vb
ON (vn.oid = vb.oid)

WHERE (vt.name = 'city' AND vt.value LIKE 'Vilnius') AND ( (vn.name = 'area' AND  vn.value > '50') OR (vn.name = 'rooms' AND  vn.value > '2' AND  vn.value < '3') ) AND ( (vb.name = 'has_separate_wc' AND vb.value = 1) )
 * 
 * 
SELECT *, COUNT(oid)=3 FROM 

(

SELECT * FROM

(
(SELECT vt.oid AS oid, 'ValueText' as source FROM values_text vt WHERE (vt.name = 'city' AND vt.value LIKE 'Vilnius') )

UNION (SELECT vn.oid AS oid, 'ValueNumeric' as source FROM values_numeric vn WHERE (vn.name = 'area' AND  vn.value > '50') OR (vn.name = 'rooms' AND  vn.value > '2' AND  vn.value < '3')    )

UNION (SELECT vb.oid AS oid, 'ValueBoolean' as source FROM values_boolean vb   WHERE (vb.name = 'has_separate_wc' AND vb.value = 1))

)
 as t1

GROUP BY oid, source
) as t2

GROUP BY oid

 * 
 *
 *
 */
class UnionFilter extends Filter {

	/** Array of filters to use in union */
	private $m_filters  = array();

	private $m_distinct = FALSE;

	/**
	 * Setter for m_distinct.
	 *
	 * @param $distinct
	 */
	public function set_distinct($distinct) {
		$this->m_distinct = $distinct;
	}

	/**
	 * Getter for m_distinct
	 *
	 * @return boolean
	 */
	public function distinct() {
		return $this->m_distinct;
	}

	/**
	 * Setter for filters.
	 *
	 * @param array $filters.
	 *
	 * @return void.
	 */
	public function set_filters(array $filters) {
		$this->m_filters = $filters;
	}

	/**
	 * Getter for filters.
	 *
	 * @return array
	 */
	public function &filters() {
		return $this->m_filters;
	}

	public function __construct(array $filters) {
		$this->set_filters($filters);
	}

	/**
	 * Return UNION select.
	 *
	 * @return string
	 */
	public function makeSQL() { 
		$filters = $this->filters();
		$outs = array();
		foreach($filters as $filter) {
			$outs[] = '('.$filter->makeSQL().')';
		}

		$distinct = $this->distinct() ? ' DISTINCT ' : '';

		$query = join(' ', array(
			join('UNION '.$distinct, $outs),
			self::makeOrderByStr($this->order),
			self::makeLimitStr($this->limit, $this->offset)));

		return $query;
	}

}

