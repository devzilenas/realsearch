<?

/**
 * Works with object ranks.
 *
 * @version 0.1.2
 */
class Rankenstein {
	/**
	 * Reranks items after delete.
	 *
	 * @todo refactor: add filter.
	 *
	 * @param Object $obj Object which is deleted.
	 *
	 * @return void
	 */
	public static function has_deleted($obj) {
		$cl    = get_class($obj);
		$query = vsprintf('
			UPDATE   '.$cl::tableName().'
			SET      rank   = rank-1
			WHERE    attached_to = %s AND attached_id = %s AND rank > %s
			ORDER BY rank DESC', 
			array_map("Dbobj::eq", array($obj->attached_to, $obj->attached_id, $obj->rank)));
		$cl::u($query);
	}

	/**
	 * Make new rank.
	 *
	 * @param string $cl Class name.
	 *
	 * @param SqlFilter $filter Filter for limiting the set of objects.
	 * @return integer
	 */
	public static function newRank($cl, SqlFilter $filter) {
		$filter->setWhat("rank+1 as newrank");
		$filter->setFrom($cl::tableName()." o");
		$filter->setOrderBy("rank DESC");
		$filter->setLimit(1);
		/** Starting rank is 1 */
		$ret = 1;
		if($r = current($cl::find($filter))) {
			$ret = $r->newrank;
		}
		return $ret;
	}

	/**
	 * Object rank up+ or down-.
	 *
	 * @param Object $obj Object having rank.
	 *
	 * @param string $ud Direction in which to move rank.
	 *
	 * @return void
	 */
	public static function move($obj, $ud) {
		$cl     = get_class($obj);
		$filter = $cl::newFilter(
			array($cl => array('id', 'rank')));
		$filter->setFrom(array($cl => 'o'));
		$filter->setOrderBy("o.rank ASC");
		$filter->setLimit(1);
		$a   = array_map("Dbobj::e", array(
			$obj->rank, $obj->attached_to, $obj->attached_id));
		$ord = 'ASC'; 

		/** When down, then increase rank. */
		if ($ud == 'down') {
			$filter->setWhere(
				vsprintf("rank > '%d' AND attached_to = '%s' AND attached_id = '%s'", $a));
			$ord = 'ASC';
		} elseif ($ud == 'up') {
			$filter->setWhere(
				vsprintf("rank < '%d' AND attached_to = '%s' AND attached_id = '%s'", $a));
			$ord = 'DESC';
		}
		$filter->setOrderBy("rank $ord");

		if($dnp = current($cl::find($filter))) {
			//2. swap
			$cl::update($obj->id, array('rank'), array('rank' => $dnp->rank));
			$cl::update($dnp->id, array('rank'), array('rank' => $obj->rank));
		}
	}

}

