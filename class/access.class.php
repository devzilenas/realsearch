<?

/**
 * Access control.
 *
 * @version 0.0.3
 */
class Access {

	/**
	 * Can the user view object?
	 *
	 * @param User $user
	 *
	 * @param mixed $o
	 *
	 * return @boolean
	 */
	public static function can_view(User $user, $o) {
		$ret = TRUE;
		if(method_exists($o, 'can_view')) {
			$ret = $o->can_view($user);
		}
		return $ret;
	}

	/**
	 * Can the user edit object?
	 *
	 * @param User $user
	 *
	 * @param mixed $o
	 *
	 * @return boolean
	 */
	public static function can_edit(User $user, $o) {
		return $user->is_admin || self::is_owner($user, $o);
	}

	/**
	 * Can the user update object?
	 *
	 * @param User $user
	 *
	 * @param mixed $o
	 * 
	 * @return boolean
	 */
	public static function can_update(User $user, $o) {
		return self::can_edit($user, $o);
	}

	/**
	 * Can the user delete object?
	 *
	 * @param User $user
	 *
	 * @param mixed $o
	 * 
	 * @return boolean
	 */
	public static function can_delete(User $user, $o) {
		return $user->is_admin || self::is_owner($user, $o);
	}

	/**
	 * Make user the owner of object.
	 *
	 * @param User $who
	 *
	 * @param mixed @what
	 *
	 * @return boolean
	 */
	public static function owns(User $who, $what) {
		$what->user_id = $who->id;
	}

	/**
	 * Checks if user is owner of the object.
	 *
	 * @param User $who 
	 *
	 * @param mixed $of
	 *
	 * @return boolean
	 */
	public static function is_owner(User $who, $of) {
		return $who->id == $of->user_id || ('User' == get_class($of) && $who->id == $of->id);
	}

	/**
	 * Can view real?
	 *
	 * @param User $user
	 *
	 * @param Real $real
	 *
	 * @return boolean
	 */
	public static function can_view_real(User $user, Real $real) {
	}

}

