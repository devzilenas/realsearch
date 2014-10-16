<?

/** 
 * Authentication request processing.
 *
 * @version 0.0.2
 */
class RequestAuth extends Request {

	public static function is_account_my() {
		return isset($_GET['account'], $_GET['my']);
	}

	public static function is_create_user() {
		return self::isCreate() && isset($_GET['user']);
	}

	public static function is_activate_user() {
		return isset($_GET['activate'], 
			         $_GET['id'],
					 $_GET['aid']);
	}

	public static function is_action_login() {
		return self::isAction("login");
	}

	public static function is_logout() {
		return self::isAction('logout');
	}

	public static function is_login() {
		return isset($_GET['login']);
	}

	public static function process() { 

		if(self::is_create_user()) {
			self::process_create_user();
		}

		if(self::is_activate_user()) {
			self::process_activate_user();
		}

		if(self::is_action_login()) {
			self::process_login();
		}

		if(self::is_logout()) {
			self::process_logout();
		}

	}

	/**
	 * My account.
	 * 
	 * @return
	 */
	private static function process_account_my() {
		include_once 'auth/sub/account.sub.php';
	}

	/**
	 * Create user.
	 *
	 * @return void
	 */
	private static function process_create_user() {
		$user             = $_POST['user'];
		$_SESSION['user'] = $user;

		if ($user['password'] == $user['password_confirm']) {
			if (Login::create_user($user['login'], $user['password'], $user['email'])) {
				Request::r2b();
			} else {
				Logger::undefErr(t("User not created!"));
				Request::hlexit("?user&new");
			}
		} else {
			Logger::err('PASS_MATCH', t("Passwords don't match!"));
			Request::hlexit("?user&new");
		}
	}

	/**
	 * Activate user.
	 *
	 */
	private static function process_activate_user() {
		if(User::activate($_GET['id'], urldecode($_GET['aid']))) {
			Logger::info(t("User activated! You can login now."));
		} else {
			Logger::undefErr(t("User not activated!"));
		}
		Request::r2b();
	}

	/**
	 * Logs user in and redirects to Config::BASE.
	 *
	 * @return void
	 */
	private static function process_login() {
		if (isset($_POST['user'], $_POST['user']['login'], $_POST['user']['password'])) {
			if(!($user = User::load_by(array(
						'User.login'  => $_POST['user']['login'],
						'User.phash'  => Crypt::genPhash($_POST['user']['password']),
						'User.active' => 1)))) {
				Logger::undefErr(t("Login failed!"));
			} else {
				Login::log_user_in($user);
				Logger::info(t("Successfuly logged in!"));
			}
			Request::r2b();
		}
	}

	/**
	 * Logout.
	 *
	 * @return void
	 */
	private static function process_logout() {
		if(Login::is_logged_in()) {
			$user = Login::user();
			Login::logout($user);
			Logger::info(t("Bye bye!"));
		} else {
			Logger::undefErr("Not logged out!");
		}
		Request::r2b();
	}

}

