<?

class Value extends Dbobj {
	public static function fields() {
		return array(
			new Field('value_for', Field::T_TEXT),
			new Field('value_of', Field::T_TEXT),
			new Field('value', Field::T_TEXT),
			new Field('clname', Field::T_TEXT)
		);
	}
}

/**
 *
 * Object has (properties).
 *
 * Property has: type, value.
 */
class ValueNumeric extends Dbobj implements DbobjInterface {
	protected static $table = 'values_numeric'; 

	public static function fields() {
		return array(
			new Field('id', Field::T_NUMERIC),
			new Field('oid', Field::T_NUMERIC),
			new Field('value', Field::T_NUMERIC, '%.3f'),
			new Field('name', Field::T_TEXT));
	}
}

class ValueText extends Dbobj implements DbobjInterface {
	protected static $table = 'values_text';

	public static function fields() {
		return array(
			new Field('id', Field::T_NUMERIC),
			new Field('oid', Field::T_NUMERIC),
			new Field('value', Field::T_TEXT),
			new Field('name', Field::T_TEXT));
	}
}

class ValueBoolean extends Dbobj implements DbobjInterface {
	protected static $table = 'values_boolean';

	public static function fields() {
		return array(
			new Field('id', Field::T_NUMERIC),
			new Field('oid', Field::T_NUMERIC),
			new Field('value', Field::T_BOOLEAN),
			new Field('name', Field::T_TEXT));
	}
}

