<?

/**
 * Runs recurring tasks.
 *
 * @version 0.0.3
 */
class Runner {

	const PREFIX = 'runner_';

	public static $m_start_time = NULL;
	public static $m_stop_time  = NULL;

	/**
	 * Must stop running?
	 *
	 * @return boolean
	 */
	public static function must_stop_running() {
		return (integer)self::time_running() >= Config::RUNNER_MAX_RUNNING_TIME;
	}

	/**
	 * Time running
	 *
	 * @return integer|NULL
	 */
	public static function time_running() {
		$start = self::$m_start_time;
		$now   = time();
		$ret   = NULL === $start ? NULL : $now - $start;
		return $ret;
	}

	/**
	 * Start running.
	 *
	 * @return void
	 */
	public static function start() {
		self::$m_start_time = time();
	}

	/**
	 * Stop running.
	 *
	 * @return void
	 */
	public static function stop() {
		self::$m_stop_time = time();
	}

	/**
	 * Directory name where to store files.
	 *
	 * @return string
	 */
	public static function idir() {
		return Config::DIR_TMP.'/runner/';
	}

	/**
	 * Returns full path to identifier.
	 *
	 * @return string
	 */
	private static function ipath($name) {
		return self::idir().$name;
	}

	private static function make_searches() {
		/** Make each search. Create e-mail for results. */
		/** Foreach user that has funds and active searches */
		/** User can activate search if has enough funds to run search. Search run once = -1 RSM. */

		/** Go through each search **/
		$filter = SearchAgent::newFilter(
			array('SearchAgent' => array('*')));
		$filter->setFrom(
			array("SearchAgent" => 'sa'));
		$filter->setWhere(sprintf(
			'   is_active                   = 1 
			AND IFNULL(is_running,0)        = 0 
			AND IFNULL(is_run,0)            = 0 
			AND %d - IFNULL(last_run_on,0) >= %d', $_SERVER['REQUEST_TIME'], Config::SEARCH_AGENT_RUN_EACH_S));
		$filter->setOrderBy('sa.id ASC');
		$filter->setLimit(10)           ;
		$sas_list = new ObjSet('SearchAgent', $filter, NULL, 10);

		if($sas_list->cnt() == 0) {
			/** No agents to run found */
			/** No unrun SearchAgents. Update ALL. */
			if(Dbobj::u("UPDATE ".SearchAgent::tableName()." SET is_running = 0, is_run = 0 WHERE is_active = 1 AND IFNULL(is_running,0) != 1")) {
				/** reset */
				$sas_list = new ObjSet('SearchAgent', $filter, NULL, 10);
			}
		}

		while($sas_list->loadNextPage()) {
			while(!self::must_stop_running() && $sa = $sas_list->getNextObj()) {
				$user = User::load($sa->user_id);
				if($user && SearchManager::can_run_search_for($user)) {
					SearchManager::search_made_for($user, $sa);
					$os         = SearchManager::make_search_for_search_agent($sa);
					$searchable = $sa->searchable;
					$sa->start_running();
					$os->loadNextPage();
					if($os->count_objects_loaded() > 0) {
						ob_start();
						include 'sub/email_search_agent.sub.php';
						$msg = ob_get_clean();
						/** Put found o to email */
						$e          = new Email();
						$e->subject = t("Notification: reals found");
						$e->message = $msg;
						$user       = User::load($sa->user_id);
						$e->to_     = $user->email;
						$e->save();
					}
					$sa->stop_running();
				}
			}
		}
	}

	public static function send_emails() {
		EmailManager::send_unsent();
	}

	/**
	 * On each call.
	 */
	public static function on_each_call() {
		$date = date("Y-m-d");
		if(!SyncerRate::rates_are_synced_for($date)) {
			SyncerRate::sync_for($date);
		}

		self::make_searches();
		self::send_emails();

	}

	/**
	 * Syncs exchange rates.
	 */
	public static function sync_exchange_rates() {
		$date = date("Y-m-d");
		if(!ExchangeRate::load_by(array("ExchangeRate.ratedate" => $date))) {
			SyncerRate::sync_for($date);
		}
	}

	private static function identifier_min15($time) {
		$quarter    = floor(date("i", $time) / 15);
		$identifier = self::PREFIX."min15_".date("YmdH").$quarter;
		return $identifier;
	}

	private static function identifier_daily($time) {
		$identifier = self::PREFIX."daily_".date("Ymd");
		return $identifier;
	}

	/** Tells whether min15 task is already run */
	private static function is_run_min15($identifier) {
		return file_exists(self::ipath($identifier));
	}

	private static function is_run_daily($identifier) {
		return file_exists(self::ipath($identifier));
	}

	private static function make_run_min15($identifier) {
		touch(self::ipath($identifier));
	}

	private static function make_run_daily($identifier) {
		touch(self::ipath($identifier));
	}

	public static function run_tasks() {

		self::start();

		$identifier_min15 = self::identifier_min15($_SERVER['REQUEST_TIME']);
		$identifier_daily = self::identifier_daily($_SERVER['REQUEST_TIME']);

		if(!self::is_run_daily($identifier_daily)) {
			self::daily($identifier_daily);
			self::make_run_daily($identifier_daily);
		}

		if(!self::is_run_min15($identifier_min15)) {
			self::min15($identifier_min15);
			self::make_run_min15($identifier_min15);
		}

		self::stop();
	}

	public static function min15() {
	}

	public static function daily() {
	}

}

