<?

/**
 * Login.
 *
 * @version 0.0.2
 *
 * @todo review
 */
class Login {

	/**
	 * Returns logged in user.
	 *
	 * @return User
	 */
	public static function user() {
		return User::load(self::logged_id());
	}

	/**
	 * Logs user in.
	 *
	 * @param User $user
	 */
	public static function log_user_in(User $user) {
		$user->sid = session_id(); 
		if($user->save()) {
			setcookie("user_id" , $user->id, 0);
			setcookie("user_sid", $user->sid, 0);
		}
	}

	/**
	 * Logs user out.
	 *
	 * @param User $user
	 *
	 * @return void
	 */
	public static function logout(User $user) {
		$user->sid = NULL;
		if($user->save()) {
			setcookie('user_id',  '', time() - 3600*24*31*12 - 1);
			setcookie('user_sid', '', time() - 3600*24*31*12 - 1);
		}
	}

	/**
	 * Tells whether current user (from session) is logged in.
	 *
	 * @done rename to is_logged_in
	 *
	 * @return boolean
	 */
	public static function is_logged_in() {
		return (isset($_COOKIE['user_id'], 
			          $_COOKIE['user_sid'])
			    && User::exists_by(array(
			       'id'  => $_COOKIE['user_id'],
				   'sid' => $_COOKIE['user_sid'])));
	}

	/**
	 * Returns logged user id.
	 *
	 * @return string
	 */
	public static function logged_id() {
		return $_COOKIE['user_id'];
	}

	/**
	 * Creates user.
	 *
	 * @param string $login
	 *
	 * @param string $password
	 *
	 * @param string $email
	 *
	 * @return integer
	 *
	 * @todo review
	 */
	public static function create_user($login, $password, $email) {
		$user_id = FALSE;
		if ($err = self::badLogin($login)) {
			$_SESSION['user']['login'] = '' ;
			Logger::err('BAD_LOGIN', t("User name")."'" . htmlspecialchars($login)."' ".t("not allowed!"));
		} elseif ($err = User::exists_by(array('login' => $login))) {
			$_SESSION['user']['login'] = '' ;
			Logger::err('INUSE_LOGIN', t("User name")." '".htmlspecialchars($login)."' ".t("already registered. Choose other name!"));
		} elseif ($err = self::badPassword($password)) {
			Logger::err('BAD_PASS', t("Password")." '".htmlspecialchars($password)."' ".t("unsuitable"));
		} elseif ($err = self::badEmail($email)) {
			$_SESSION['user']['email'] = '' ;
			Logger::err('BAD_EMAIL', t("E-mail")." '" . htmlspecialchars($email)."' ".t("not allowed!"));
		} elseif ($err = User::exists_by(array('email' => $email))) {
			$_SESSION['user']['email'] = '' ;
			Logger::err('INUSE_EMAIL', t("Address")." ".htmlspecialchars($email)." ".t(" already registered. Provide another!"));
		} 
		if (!$err) {
			unset($_SESSION['user']);//clear
			$user = User::fromForm(array(
						'login' => $login,
						'email' => $email,
						'phash' => Crypt::genPhash($password),
						'aid'   => Crypt::genAid()));
			if ($user_id = $user->insert()) {  
				self::sendActivation($user_id);
			} else {
				Logger::err("NEW_USER_FAIL", t("User not created!"));
			}
		}
		return $user_id;
	}

	private static function sendActivation($id) {
		if ($user = User::load($id)) {
			$subject    = t("Activation");
			$message    = "?activate&id=".urlencode($user->id)."&aid=".urlencode($user->aid);
			$headers    = "From: localhost@example.com" . "\r\n".
						  "X-Mailer: PHP/".phpversion();

			echo "Activation by e-mail not enabled. Use provided link! ";
			echo('<a href="'.Request::base().$message.'">Click this link to activate your user account!</a>');exit;
			/*
			if (!empty($user->email)) { 
				mail($user->email, $subject, wordwrap($message,70));
			}*/
		}
	}

	private static function badPassword($pass) {
		return empty($pass);
	}

	private static function badEmail($email) {
		return empty($email);
	}

	private static function badLogin($login) {
		return empty($login);
	}

}

