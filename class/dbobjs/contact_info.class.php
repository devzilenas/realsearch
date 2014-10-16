<?

class ContactInfo extends Dbobj implements DbobjInterface {
	public static function fields() {
		return array(
			new Field('id', Field::T_NUMERIC, "%d"),
			new Field('user_id', Field::T_NUMERIC, "%d"),
			new Field('name', Field::T_TEXT),
			new Field('surname', Field::T_TEXT),
			new Field('e-mail', Field::T_TEXT),
			new Field('mobile', Field::T_TEXT),
			new Field('land_phone', Field::T_TEXT),
			new Field('attached_to', Field::T_TEXT),
			new Field('attached_id', Field::T_NUMERIC));
	}

	/**
	 * Validation.
	 * 
	 * @return array
	 */
	public function hasValidationErrors() {
		$validation = array();
		return $validation;
	}

	/**
	 * Returns fields for the form.
	 *
	 * @return array
	 */
	public static function editable_fields() {
		return array_filter(self::fields(), function($field) {
			return !in_array($field->name(), array('id', 'user_id', 'attached_to', 'attached_id'));
		});
	}

}

