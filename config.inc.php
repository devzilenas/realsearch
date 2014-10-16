<?
/**
 * Class for configuration.
 */
class Config { 
# ------------------------------------------------------
# ---------- APPLICATION RELATED -----------------------
# ------------------------------------------------------
	/** Aplication name */
	const APP                     = 'realsearch';

	/** Temporary directory */
	const DIR_TMP                 = 'tmp/';

	/** Default timezone */
	const TZ                      = 'Europe/Vilnius';

	/** Reals per page */
	const REALS_PER_PAGE          = 10;

	/** Base currency */
	const BASE_CURRENCY           = 'LTL';
	const BASE_CURRENCY_WALLET    = 'RSM';

	/** Proxy */
	const HTTP_PROXY              = 'tcp://proxy.adm.lg:8080';

	/** Timeout for HTTP calls to other service */
	const HTTP_TIMEOUT            = 5;

	/** Plans */
	const PLAN_PRICE_BETTER_BASE  = 2.49;
	const PLAN_PRICE_BEST_BASE    = 7.99;

	/** Amounts */
	const AMOUNT_SEARCH_AGENT_RUN = 1; //RSM's for one activated run of search agent
	const AMOUNT_REAL_CREATE      = 4500; //2*24*30*3 = 3 months. 2 searches per hour.
	const AMOUNT_REAL_100_VIEWS   = 4500; //

	/** Corner ratio */
	const PICTURE_CORNER_RATIO    = 0.05;

	/** Runner */
	const RUNNER_MAX_RUNNING_TIME = 3; // In seconds.


	/** SearchAgent */
	const SEARCH_AGENT_RUN_EACH_S = 900; // 15 min * 60 = 900 seconds 
	/** Email */
	const EMAIL_FROM              = 'localhost@example.com';

	/** Drop down */
	const DROPDOWN_ENABLED        = FALSE;

# ------------------------------------------------------
# ---------- INSTALLATION RELATED ----------------------
# ------------------------------------------------------
	const IS_PRODUCTION         = FALSE;

# ---------- BASE LOCATION -----------------------------
	const BASE                  = "http://localhost";

# ---------- DATABASE CONFIGURATION --------------------
	public static $DB_NAME      = self::APP;
	public static $DB_HOST      = 'localhost';
	public static $DB_USER      = 'root';
	public static $DB_PASSWORD  = '';

	public static $SESSION_SHOW = FALSE;
# ---------- TEST DATABASE -----------------------------
	const DB_TEST_POSTFIX ='_test';

	/**
	 * Returns test database name.
	 *
	 * @return string
	 */
	public static function db_test() {
		return self::APP.self::DB_TEST_POSTFIX;
	}

	/**
	 * Returns full path to application.
	 *
	 * @return string
	 */
	public static function base() {
		return self::BASE.'/'.self::APP;
	}

	/**
	 * Get base currency.
	 *
	 * @return Currency
	 */
	public static function base_currency() {
		return new Currency(self::BASE_CURRENCY);
	}

	/**
	 * Get base currency for wallet.
	 *
	 * @return Currency
	 */
	public static function base_currency_wallet() {
		return new Currency(self::BASE_CURRENCY_WALLET);
	}

	/**
	 * Get context for http calls.
	 *
	 * @return array
	 */
	public static function http_context() {
		$ret = array(
			"http" => array(
				"proxy"   => self::HTTP_PROXY,
				"timeout" => self::HTTP_TIMEOUT));
		return $ret;
	}

	/**
	 * Get plan price.
	 *
	 * @return Monia
	 */
	public static function plan_price_for($which, Currency $currency) {
		/** default */
		$amount = 0.99;

		if('BETTER' === $which) {
			$amount = Config::PLAN_PRICE_BETTER_BASE;
		} else if('BEST' === $which) {
			$amount = Config::PLAN_PRICE_BEST_BASE;
		}
		$price = new Monia(Config::base_currency(), $amount);
		return $price->converted_to($currency);
	} 

	/**
	 * Get amount for created real.
	 *
	 * @return Monia
	 */
	public static function amount_for_real_create() {
		return new Monia(new Currency('RSM'), self::AMOUNT_REAL_CREATE);
	}

	public static function amount_for_search_agent_run() {
		return new Monia(new Currency('RSM'), self::AMOUNT_SEARCH_AGENT_RUN);
	}

	/**
	 * Is production?
	 *
	 * @return boolean
	 */
	public static function is_production() {
		return defined('self::IS_PRODUCTION') && self::IS_PRODUCTION;
	}

	/**
	 * Is dropdown enabled?
	 */
	public static function is_dropdown_enabled() {
		return self::DROPDOWN_ENABLED;
	}

}

