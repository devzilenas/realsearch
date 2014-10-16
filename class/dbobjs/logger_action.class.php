<?
/**
 * Logs actions
 *
 * @author Marius Žilėnas
 * @copyright 2013, Marius Žilėnas
 *
 * @version 0.0.1
 */
class LoggerAction extends Dbobj implements DbobjInterface {

	public static function fields() {
		return array(
			new Field('id', Field::T_NUMERIC, "%d"),
			new Field('name', Field::T_TEXT, "%s"),
			new Field('ip', Field::T_TEXT, "%s"),
			new Field('user_id', Field::T_NUMERIC, "%d"),
			new Field('on_', Field::T_TEXT, '%s'),
			new Field('attached_to', Field::T_TEXT, '%s'),
			new Field('attached_id', Field::T_NUMERIC, '%d')
		);
	}

	/**
	 * Create new action
	 *
	 * @param string $name Can be 'view', 'create', 'delete', or other string.
	 */
	public static function new_action($name = NULL) {
		$a       = new static();
		$a->on_  = self::toDateTime($_SERVER['REQUEST_TIME']);
		$a->ip   = $_SERVER['REMOTE_ADDR'];
		$a->name = $name;
		if(Login::is_logged_in()) {
			$a->user_id = Login::logged_id();
		}
		return $a;
	}

}
