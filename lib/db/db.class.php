<?
/**
 * Class to manage connection with database.
 *
 * @version 0.1.1
 */
class DB {

	/**
	 * Connect to database.
	 */
	private static function conn() {
		return mysql_connect(
				Config::$DB_HOST,
				Config::$DB_USER,
				Config::$DB_PASSWORD) or die (t("Not connected to database!") . mysql_error());
	}

	/**
	 * Make post connection setup. 
	 */
	private static function postConnect() {
		mysql_query("SET NAMES 'utf8'");
	}

	/**
	 * Connect to database.
	 */
	public static function connect() {

		self::conn();
		self::postConnect();

		mysql_select_db(Config::$DB_NAME) or die(t("Database selection error!") . mysql_error());

		return TRUE;
	}

	/**
	 * Connect to test database.
	 */
	public static function test_connect() {
		if (isset(Config::$DB_TEST)) {
			self::conn();
			self::postConnect();
			mysql_select_db(Config::$DB_TEST);
		}
	}
}

