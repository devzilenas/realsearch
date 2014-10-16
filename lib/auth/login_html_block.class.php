<?

/**
 * Html for user.
 *
 * @version 0.0.2
 *
 * @todo add validation as in dbobjs' forms.
 * @done put html code to sub/login.sub.php.
 */
class LoginHtmlBlock {

	public static function hello($user) {
		return '<p>'.t("Hello").' <b>'.so($user->login).'</b></p>';
	}

}

