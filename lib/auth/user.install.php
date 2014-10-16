<?

/**
 * Setup for user data.
 * @version 0.1.2
 */
class InstallUser {

	/**
	 * Create tables.
	 *
	 * @return void
	 */
	public static function createTables() {
		self::createTableUsers();
		self::afterCreateTableUsers();
	}
	
	/**
	 * Create user table.
	 *
	 * @return void
	 */
	private static function createTableUsers() {
		$table = User::tableName();
		mysql_query("
			CREATE TABLE IF NOT EXISTS $table (
				id        INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
				login     VARCHAR(255),
				phash     VARCHAR(32),
				sid       VARCHAR(32),
				email     VARCHAR(255),
				aid       VARCHAR(32),
			 	is_admin  TINYINT(1),
				active    TINYINT(1),
				time_zone VARCHAR(255),
				currency  VARCHAR(3),
				api_key   VARCHAR(32)
				)"
			) or self::dieTNC($table);
	}

	/**
	 * Make user.
	 *
	 * @return void
	 */
	private static function generateUser($login) {
		return User::fromForm( array(
					'login' => $login,
					'email' => $login.'@example.com',
					'phash' => Crypt::genPhash($login),
					'aid'   => Crypt::genAid()));
	}

	/**
	 * Setup demo user.
	 *
	 * @return void
	 */
	private static function afterCreateTableUsers() { 
		if (!self::userOk('demo')) {
			$user = self::generateUser('demo');
			if ($user_id = $user->insert()) {
				Logger::info(sprintf(t('Created user - login: %1$s, password: %1$s!'), 'demo'));
				User::activate($user_id, $user->aid);
			}
		}
	}

	/**
	 * Check if the user is already in database.
	 *
	 * @param string $login Login of the user.
	 *
	 * @return boolean
	 */
    public static function userOk($login) {
		return FALSE !== User::load_by(array('User.login' => $login));
	}

}

