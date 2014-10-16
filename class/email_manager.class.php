<?

/**
 * Sends emails.
 */
class EmailManager {

	public static function edir() {
		return Config::DIR_TMP.'/emails/';
	}

	/**
	 * Returns path to email file.
	 *
	 * @param string $name
	 *
	 * @return string
	 */
	private static function epath($name) {
		return self::edir().$name;
	}

	/**
	 * Sends all unsent emails.
	 *
	 * @return integer Emails sent.
	 */
	public static function send_unsent() {
		$filter = Email::newFilter();
		$filter->setWhere('IFNULL(is_sent,0) != 1');
		$os = new ObjSet('Email', $filter);
		$i  = 0;
		while($os->loadNextPage()) {
			while($e = $os->getNextObj()) {
				self::send($e);
				$i++;
			}
		}
		return $i;
	}

	public static function send(Email $e) {
		if(Config::is_production()) {
			/** Real email send: through SMTP */ 
			$to         = $e->to_;
			$from       = Config::EMAIL_FROM;
			$subject    = $e->subject;
			$message    = $e->message;
			if($e->is_html) {
				$headers =  'Mime-version: 1.0'."\r\n". 
							'Content-type: text/html;charset=uft-8'."\r\n".
							"To: $to\r\n".
							"From: $from\r\n".
						    'X-Mailer: PHP/'.phpversion();
				mail($e->to_, $e->subject, $e->as_html());
			} else {
				$headers    = "From: $from\r\n".
							  "X-Mailer: PHP/".phpversion();
				mail($e->to_, $e->subject, wordwrap($e->message, 70));
			}
		} else {
			/** Put emails as files to temporary directory */
			$name = sprintf("e%d.html", $e->id);
			file_put_contents(self::epath($name), $e->as_html());
		}
		$e->sent();
	}

}

