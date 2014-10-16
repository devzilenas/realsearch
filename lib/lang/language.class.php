<?

/**
 * Class to make translations.
 */
class Language {

	const LT = 'lt';
	const RU = 'ru';
	const EN = 'en';
	const DE = 'de';

	/**
	 * Changes <undescore> to <space>.
	 *
	 * "This_is_a_string" becomes "This is a string".
	 *
	 * @param string $str
	 *
	 * @return string
	 */
	public static function beautify($str) { 
		return strtr($str, '_', ' ');
	}

	/**
	 * Pluralizes string.
	 *
	 * @param string $str String to pluralize.
	 *
	 * @return string
	 */
	public static function pluralize($str) {
		$ret = $str;

		/** Rule 1: add s */
		if(substr($str, -1) != 's')
			$ret = $ret.'s';

		/** Exceptions */
		if('person' === strtolower($str)) {
			/** @todo make it return "(Pp)eople" */
			$ret = 'people';
		}

		return $ret;
	}

	/**
	 * Get available languages.
	 *
	 * @return array
	 */
	public static function languages() {
		return array(
				self::LT => 'Lietuvių', 
				self::RU => 'Русский',
				self::DE => 'Deutsch',
				self::EN => 'English');
	}

	public static function setTranslations($language, $base_lang, $translations) {
		global $LANG;
		$LANG[$language][$base_lang] = array_change_key_case($translations);
	}

	/**
	 * Tells whether language is valid.
	 *
	 * @return boolean
	 */
	public static function valid($language) {
		return in_array($language, array_keys(self::languages()));
	}

	/**
	 * Default language for translation.
	 *
	 * @return string
	 */
	public static function d() {
		return self::LT;
	}

	/**
	 * Base language.
	 *
	 * @return string
	 */
	public static function baseLang() {
		return self::EN;
	}

	private static function findStr($language, $base_language, $str) {
		global $LANG;
		if (isset($LANG[$language][$base_language][strtolower($str)])) {
			return $LANG[$language][$base_language][strtolower($str)];
		} else {
			return $str;
		}
	}

	public static function t($str) {
		global $LANG;
		if (UserSession::language() === self::baseLang()) {
			return $str;
		} else if ($str = self::findStr(UserSession::language(), self::baseLang(), $str)) {
			return $str;
		} else {
			return $str;
		}
	}

}

