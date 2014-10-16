<?

/**
 * Setup for money.
 *
 * @version 0.0.2
 */

class InstallMonia extends InstallB {
	/**
	 * Create tables.
	 *
	 * @return void
	 */
	public static function createTables() {
		self::createTableExchangeRates();

		self::createTableWallets();
		self::createTableWalletLines();
	}

	private static function createTableExchangeRates() {
		$table = ExchangeRate::tableName();
		mysql_query("
			CREATE TABLE IF NOT EXISTS $table (
				id            INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
				ratedate      DATE,
				from_currency CHAR(3),
				to_currency   CHAR(3),
				quantity      INTEGER,
				rate          DECIMAL(12,5))")
			or self::dieTNC($table); 
	}

	private static function createTableWallets() {
		$table = Wallet::tableName();

		mysql_query("
			CREATE TABLE IF NOT EXISTS $table (
				id       INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
				user_id  INTEGER,
				name     VARCHAR(255),
				currency CHAR(3)
		)") or self::dieTNC($table); 
	}

	private static function createTableWalletLines() {
		$table = WalletLine::tableName();

		mysql_query("
			CREATE TABLE IF NOT EXISTS $table (
				id          INTEGER NOT NULL AUTO_INCREMENT PRIMARY KEY,
				wallet_id   INTEGER,
				amount      DECIMAL(12,5),
				amount_left DECIMAL(12,5),
				attached_to VARCHAR(255),
				attached_id INTEGER,
				what        VARCHAR(255),
				on_         DATETIME, 
				currency    CHAR(3)
			)") or self::dieTNC($table);
	}
	
}


