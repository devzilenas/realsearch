<?

/**
 * User class.
 *
 * @version 0.1.1 
 *
 */
class User extends Dbobj implements DbobjInterface {

	public static function fields() {
		return array(
			new Field('id', Field::T_NUMERIC, "%d", FALSE, FALSE),
			new Field('login', Field::T_TEXT),
			new Field('phash', Field::T_TEXT),
			new Field('sid', Field::T_TEXT),
			new Field('email', Field::T_TEXT),
			new Field('aid', Field::T_TEXT),
			new Field('is_active', Field::T_BOOLEAN),
			new Field('time_zone', Field::T_TEXT),
			new Field('currency', Field::T_TEXT),
			new Field('api_key', Field::T_TEXT)
		);
	}

	/**
	 * Activates user.
	 *
	 * @return boolean
	 */
	public static function activate($id, $aid) {
		$ret = FALSE;
		if ($user = self::load($id)) {
			$ret = $user->aid === $aid && !$user->is_active && self::update($id,
					array('active', 'aid'),
					array('active' => 1, 'aid' => ''));
		}
		return $ret;
	}

}

