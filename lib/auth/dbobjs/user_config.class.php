<?

/**
 * User configuration.
 */
class UserConfig extends Dbobj implements DbobjInterface {

	/**
	 * Fields.
	 *
	 * @return Field[]
	 */
	public static function fields() {
		return array(
			new Field("id", Field::T_NUMERIC, "%d", FALSE, FALSE),
			new Field("user_id", Field::T_NUMERIC, "%d", FALSE, FALSE),
			new Field("reals_per_page", Field::T_NUMERIC, "%d", FALSE, FALSE)
		);
	}

	/**
	 * Reals per page.
	 *
	 * @param User $user
	 *
	 * @return integer
	 */
	public static function get_reals_per_page(User $user) {
		$ret = Config::REALS_PER_PAGE;
		if($o = self::load_by(array(
			"UserConfig.user_id" => $user->id))) {
			return $o->reals_per_page;
		}
		return $ret;
	}

}
