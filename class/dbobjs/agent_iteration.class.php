<?  
class AgentIteration extends Dbobj implements DbobjInterface 
{
	public static function fields() {
		return array(
			new Field('id', Field::T_NUMERIC, "%d"), 
			new Field('name', Field::T_TEXT),
			new Field('iteration', Field::T_NUMERIC, "%d")
		);
	}

	/**
	 * Returns iteration for $name.
	 *
	 * @param string
	 *
	 * @return integer
	 */
	public static function current_for($name) {
		$ret = 0;
		if($it = self::iteration_for($name)) {
			$ret = $it->iteration;
		}	
		return $ret;
	}

	/**
	 * Gets next iteration number for current iteration.
	 *
	 * @param string $name
	 *
	 * @return integer
	 */
	public static function make_next_for($name) {
		$it = self::current_for($name) + 1;
		return $it;
	}

	/**
	 * Returns iteration for $name.
	 *
	 * @param string $name
	 *
	 * @return self
	 */
	private static function iteration_for($name) {
		if(!$it = self::load_by(array(
			"AgentIteration.name" => $name))) {
			$it = new static(); 
			$it->iteration = 0;
			$it->save();
		}
		return $it;
	}

}
