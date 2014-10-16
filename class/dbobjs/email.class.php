<?

/**
 * Emails
 */
class Email extends Dbobj implements DbobjInterface {
	/**
	 * Fields
	 *
	 * @return Field[]
	 */
	public static function fields() {
		return array(
			new Field('id', Field::T_NUMERIC, "%d"),
			new Field('subject', Field::T_TEXT),
			new Field('message', Field::T_TEXT),
			new Field('is_html', Field::T_BOOLEAN),
			new Field('is_sent', Field::T_BOOLEAN),
			new Field('to_', Field::T_TEXT)
		);
	}

	/**
	 * Make email as HTML document
	 */
	public function as_html() {
		$str = sprintf("<html><head><title>Sent to %s</title></head><body>
			%s 
			</body></html>", so($this->to_), $this->message);

		return $str;
	}

	/**
	 * Marks email message as sent.
	 *
	 * @return void
	 */
	public function sent() {
		$this->is_sent = TRUE;
		$this->save();
	}
}
