<?

class SearchValue extends Dbobj implements DbobjInterface {

	public static function fields() {
		return array(
			new Field('id', Field::T_NUMERIC, "%d"),
			new Field('search_agent_id', Field::T_NUMERIC, "%d"),
			new Field('search_field', Field::T_TEXT),
			new Field('search_value', Field::T_TEXT)
		);
	}

}
