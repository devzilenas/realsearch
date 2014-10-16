<?
/**
 * Base class for install.
 *
 * @version 0.0.1
 */
class InstallB {
	/**
	 * Die. Table not created.
	 */
	protected static function dieTNC($name) {
		return die(t("Table").' '.t($name).' '.t('not created'). mysql_error());
	}

	/**
	 * Destroy session data.
	 */
	public static function dsd() {
		if(''==session_id()) {
			session_start();
			session_destroy();
		}
	}
}


