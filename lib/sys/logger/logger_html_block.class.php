<?

/**
 * Html for the logger output.
 *
 * @version 0.1.1
 */
class LoggerHtmlBlock {

	/**
	 * Output messages.
	 *
	 * @return void
	 */
	public static function messages() {
		$val = '';

		while ($error = Logger::nextErr()) {
			$val .= '<li class="error">'.so($error->msg).'</li>';
		}

		while ($info = Logger::nextInfo()) {
			$val .= '<li class="info">'.so($info->msg).'</li>';
		}

		if (!empty($val)) {
			echo '<div id="messages"><ul>'.$val.'</ul></div>';
		}

	}

}

