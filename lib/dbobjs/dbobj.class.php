<?

/**
 * Base for objects saveable to database.
 *
 * @version 0.1.6
 *
 * - filters
 * - virtual fields
 * - money type
 *
 * @todo change mysql to mysqli
 */
class Dbobj {

	protected $dbd       = array(); //data from table
	protected $d         = array(); //other data, not from table
	/** Names of changed fields */
	private   $m_changed = array();

	/**
	 * Gets first object
	 *
	 * @return self
	 */
	public static function first() {
		$cl = get_called_class();
		$filter = self::newFilter(array($cl => array('id')));
		$filter->setFrom(array($cl => 't'));
		$filter->setLimit(1);
		$filter->setOrderBy('t.id ASC');
		return current(self::find($filter));
	}

	/**
	 * Loads virtual fields data for the object.
	 *
	 * @param array $fs (optional) Virtual fields to load.
	 */
	public static function load_virtual_fields(array $fs = array()) {
		$values = Serializator::get_values($this->id);
		Serializator::build_o($values, $this);
	}

	/**
	 * Has virtual fields?
	 *
	 * @return boolean
	 */
	public static function has_virtual_fields() {
		$fields = static::fields();
		$has    = FALSE;
		foreach($fields as $field) {
			if($field->is_virtual()) {
				$has = TRUE;
				break;
			}
		}
		return $has;
	}

	/**
	 * Returns virtual fields.
	 *
	 * @return array
	 */
	public static function virtual_fields() {
		return array_filter(static::fields(),
			function($field) {
				return $field->is_virtual();
			}
		);
	}

	/**
	 * Returns non virtual fields.
	 *
	 * @return array
	 */
	public static function non_virtual_fields() {
		return array_filter(static::fields(), 
			function($field) {
				return !$field->is_virtual();
			}
		);
	}

	/**
	 * Sets m_changed to initial value.
	 * 
	 * @return void
	 */
	private function init_changed() {
		$this->m_changed = array();
	}
	
	/**
	 * Getter for m_changed.
	 *
	 * @return array
	 */
	protected function &changed() {
		return $this->m_changed;
	}

	/**
	 * Tell that value of $fieldname changed.
	 *
	 * @param string $fieldname
	 *
	 * @return void
	 */
	private function has_changed($fieldname) { 
		$ch = &$this->changed();
		$ch[$fieldname] = 1;
	}

	/**
	 * Returns names of "dirty" fields.
	 * 
	 * @return array
	 */
	protected function dirty() {
		return array_keys($this->changed());
	}

	/**
	 * Returns fields that have changed.
	 *
	 * @return Field[]
	 */
	public function dirty_fields() {
		$dirty = $this->dirty();
		$ret = array();
		foreach($dirty as $name) {
			$ret[] = self::field($name);
		}
		return $ret;
	}

	/**
	 * Update query.
	 *
	 * @param string $query Update query to execute.
	 *
	 * @return number of rows updated
	 */
	public static function u($query) {
		 mysql_query($query) 
			or die(t("Not updated!") . mysql_error());
		 return mysql_affected_rows();
	}

	/**
	 * Updates object's data.
	 *
	 * @return void
	 */
	private function upd() {

		/** Touch */
		if(self::field('touched_on')) {
			$this->touched_on = time();
		}

		$dirty_fields = $this->dirty();
		/** fields to update */
		$upd_fields   = array();
		$data         = array();
		$vdata        = array();
		$ret          = NULL;

		/** update non virtual fields */
		if(!empty($dirty_fields)) {
			foreach($dirty_fields as $name) {
				$field = self::field($name);
				if(!$field->is_virtual()) {
					$data[$name]  = $this->$name;
					$upd_fields[] = $name; 
				}
			}

			if(!empty($upd_fields)) {
				/** update non virtual fields */
				$ret = self::update($this->id, $upd_fields, $data);
			}

			if(method_exists($this, 'after_update')) {
				$this->after_update();
			}

		}
		return $ret;
	}

	/**
	 * Saves object to database.
	 *
	 * @return mixed
	 */
	public function save() {
		return $this->isNew() ? $this->insert() : $this->upd();
	}

	/**
	 * Tells whether object equals given object.
	 *
	 * @param self $o
	 *
	 * @return boolean
	 */
	public function equals($o) {
		if(isset($this->id) && isset($o->id)) {
		   	return $this->id == $o->id;
		} else {
			return FALSE;
		}
	}

	/**
	 * Validates if field contains valid date.
	 *
	 * @return string
	 */
	public function validateIsDate($field) {
		$val = $this->$field;
		$d   = explode('-',$val);
		if (!(count($d)==3 && checkdate($d[1],$d[2],$d[0]))) 
			return sprintf( t(get_called_class())." ".t("%s has to be a valid date"), t($field));
	}

	/**
	 * Validates field value for not being empty.
	 *
	 * @param string $name Field name.
	 *
	 * @return string|NULL
	 */
	public function validateNotEmpty($name) {
		if($field = self::field($name)
			&& $field->istext() 
			&& '' === trim($this->$name)) { 
			return sprintf(t(get_called_class())." ".t("field \"%s\" can not be empty!"), t($field));
		}
	}

	/**
	 * Validates field value for being numeric.
	 *
	 * @param string $name Field name.
	 *
	 * @return string|NULL
	 */
	public function validateNumeric($name) {
		if (!is_numeric($this->$field)) {
		   return t(get_called_class()." value has to be numeric");
		}
	}

	/**
	 * Is object new: is not in database.
	 *
	 * @return boolean
	 */
	public function isNew() {
		return !isset($this->id);
	}

	/**
	 * Returns table name of the object.
	 *
	 * @return string
	 */
	public static function tableName() {
		if(property_exists(get_called_class(), 'table')) {
			return static::$table;
		} else {
		    return pluralize(c2u(get_called_class()));
		}
	}

	/**
	 * Get field by name.
	 *
	 * @param string $name Field name.
	 *
	 * @return Field
	 */
	public static function field($name) {
		return afopm(static::fields(), "name", $name);
	}

	/**
	 * Returns format for field.
	 *
	 * @param string $name Name of the field.
	 *
	 * @return string
	 */
	public static function fformat($name) {
		return self::field($name)->format();
	}

	/**
	 * Field accepts NULL value?
	 *
	 * @return boolean
	 */
	private static function fieldAcceptsNULL($name) {
		$field = self::field($name);
		return TRUE;
	}

	/**
	 * Returns field names.
	 *
	 * @return array
	 */
	public static function fieldNames() {
		return arrayV(static::fields(), "name");
	}

	/**
	 * Is given field name valid field?
	 *
	 * @param string $name Field name.
	 *
	 * @return boolean
	 */
	public static function isField($name) {
		return in_array($name, self::fieldNames());
	}

	protected static function filterFields($fields = array()) {
		$retVal = array();
		foreach($fields as $fieldName) {
			$retVal[] = self::isField(end(explode('.', $fieldName)));//fieldname may be with alias
		}
		return $retVal;
	}

	/**
	 * @todo refactor 
	 */
	public function __set($name, $val) {
		$changed = TRUE;
		if (self::isField($name)) { 
			$field = self::field($name);
			/** Is new value */
			$formatted_value = $val;

			if(NULL !== $formatted_value) {
				if(is_string($val) && '' == trim($val)) {
					/** all '' values become NULL */
					$formatted_value = NULL;
				} else if($field->istext()) {
					$formatted_value = (string)$val;
				} else if($field->isnumeric()) {
					$formatted_value = (real)$val;
				} else if($field->isboolean()) {
					$formatted_value = (boolean)$val;
				}
			}

			if(array_key_exists($name, $this->dbd)) {
				$changed = $this->dbd[$name] !== $formatted_value;
			}
			$this->dbd[$name] = $formatted_value;
		} else {
			/** Is new value */
			if(array_key_exists($name, $this->d)) {
				$changed = $this->d[$name] !== $val;
			}
			$this->d[$name] = $val;
		}

		if(!$this->isNew() && $changed) {
			$this->has_changed($name);
		}

	}

	public function __get($name) {
		if (isset($this->$name)) { 
			if (self::isField($name)) {
				return $this->dbd[$name];
			} else {
				return $this->d[$name];
			}
		}
	}

	public function __isset($name) {
		return isset($this->dbd[$name]) || isset($this->d[$name]);
	}

	/**
	 *
	 * @return array|NULL
	 */
	private static function fromSQL($data) {
		$d   = new static();
		$ret = NULL;
		if (is_array($data)) {
			foreach($data as $field => $value) {
				$field = end(explode('.',$field)); 
				$d->$field = $value;
			}
			$ret = $d;
		}
		return $ret;
	}

	/**
	 * Sets object's properties with data.
	 *
	 * @param array $data Associative array of object data.
	 *
	 * @param array $fields Fieldnames of fields to take from $data.
	 *
	 * @return void
	 */
	public function ff(array $data, array $fields) {
		if(empty($fields)) {
			foreach($data as $name => $value) {
				if('' === trim($value)) {
					$value = NULL;
				}
				$this->$name = $value;
			}
		} else {
			foreach($fields as $field) {
				$name = $field->name();
				if(array_key_exists($name, $data)) {
					$value = $data[$name];
					if('' == trim($value)) {
						$value = NULL;
					} else if ($field->isboolean() && (1 == $value || 0  == $value)) {
						/** @todo should it be int or boolean? */
						$value = (int)$value;
					}
					$this->$name = $value;
				}
			}
		}
	}

	/**
	 * Shortcut for ff(Form::discard_empty($data), $fields)
	 */
	public function ffde(array $data, array $fields) {
		return self::ff(Form::discard_empty($data), $fields);
	}

	/**
	 * Shortcut for ff(Form::discard_en($data), $fields)
	 */
	public function ffden(array $data, array $fields = array()) {
		return self::ff(Form::discard_en($data), $fields);
	}

	/**
	 * Updates object data in database with data "submitted" from form.
	 *
	 * @todo review
	 *
	 * @param array $data Assoc array.
	 *
	 * @param array $fields Fields to update.
	 *
	 * @return boolean TRUE on success.
	 */
	public function updateFromForm(array $data, array $fields) {
		if (!$this->isNew()) {
			$updateFields = array();
			$updateData   = array();

			foreach($fields as $field) {
				if (isset($data[$field]) && $this->$field !== $data[$field]) { 
					$updateFields[]     = $field; 
					$updateData[$field] = $data[$field];
				}
			}

			if(count($updateFields) > 0)
				return self::update($this->id, $updateFields, $updateData);
		}
	}

	/**
	 *
	 * Creates a new object or modifies existing object with data (usually from form submitted data).
	 *
	 * @return mixed New object or modified object.
	 *
	 * @param $data array Associative array 'data field name' => 'data'
	 *
	 * @param $fields array Names of data fields to copy to the object. 
	 *
	 * @param $obj mixed Object to modify.
	 *
	 * @return self
	 */
	public static function fromForm($data, $fields = NULL, $obj = NULL) {
		$d = (NULL === $obj ? new static() : $obj);
		if (is_array($data)) {
			foreach ($data as $name => $value) {
				if (is_array($fields) && !empty($fields)) {
					if (in_array($name, $fields)) {
						$d->$name = $value;
					}
				} else $d->$name = $value;
			}
		}
		return $d;
	}

	/**
	 * Delete object.
	 *
	 * @return boolean
	 */
	public function d() {
		$ret = FALSE;
		if(!$this->isNew()) {
			$id  = $this->id;
		   	$ret = self::del($id);
			if(method_exists($this, 'after_delete')) {
				$this->after_delete($id);
			}
		}
		return $ret;
	}

	/**
	 * Execute DELETE query.
	 *
	 * @param integer $id Id of the object.
	 *
	 * @return boolean 
	 */
	public static function del($id) { 
		$query = sprintf("DELETE FROM ".self::tableName()." 
			WHERE id = '%d' LIMIT 1",
			mysql_real_escape_string($id));
		mysql_query($query) or die(t("Record not deleted!") . mysql_error());
		return TRUE;
	} 

	/**
	 * Deletes matching rows.
	 *
	 * @param array $where Array of "field" => "value" pairs.
	 *
	 * @return void
	 */
	public static function delWhere($where) { 
		if (is_array($where)) {
			$fieldNames = array_keys($where);
			foreach($fieldNames as $name) {
				if (self::isField($name)) {
					$field = self::field($name);
					$wheres[] = self::iq($field->name())."=".self::q($field->format());
					$values[] = $where[$field->name()];
				}
			}

			$query = vsprintf("
				DELETE FROM ".self::tableName()."
				WHERE ".join(' AND ', $wheres), array_map('mysql_real_escape_string', $values));
			mysql_query($query) or die(t("Records not deleted!") . mysql_error());
		}

	}

	/**
	 * Updates row's data.
	 *
	 * @param integer $id Id of the row to update.
	 *
	 * @param array $fieldNames (optional) Names of the fields to update.
	 *
	 * @param array $data (optional) Array of "fieldname" => "data".
	 *
	 * @return boolean|NULL
	 */
	public static function update($id, array $fieldNames = array(), array $data = array()) {
		$values = array();
		$set    = array();
		
		foreach($fieldNames as $name) {
			if (self::isField($name)) {
				$field = self::field($name);
				if (NULL === $data[$name]) {
					$set[]    = self::iq($name).'=%s'; 
					$values[] = 'NULL';
				} else {
					$set[]    = self::iq($name).'='.self::q($field->format());
					$values[] = Dbobj::e($data[$name]);
				}
			}
		}

		/** add id to the end of values array */
		$values[] = (int)$id;
		$query = vsprintf("UPDATE ".self::tableName()." SET ".implode(',', $set)." 
				WHERE id='%d' LIMIT 1", $values);

		if (mysql_query($query))
		   	return TRUE;
		else 
			self::diem(t("Object not updated!"), $query);
	}

	/**
	 * Puts object data into database table.
	 * 
	 * @return integer|NULL Returns id of the object. NULL if no insert made.
	 */ 
	public function insert() {
		$do_insert = TRUE;
		if (method_exists($this, 'beforeInsert')) {
			$do_insert = $this->beforeInsert();
		} 

		if(FALSE !== $do_insert) {

			/** Touch */
			if(self::field('touched_on')) {
				$this->touched_on = time();
			}

			$fields  = array();
			$nvfields = self::non_virtual_fields();
			$values  = array();
			$formats = array();
			foreach ($nvfields as $field) {
				$name = $field->name();
				if (isset($this->$name)) {
					$fields[]  = self::iq($name);
					$values[]  = $this->$name;
					$formats[] = self::q($field->format());
				}
			}
			$query  = vsprintf("INSERT INTO ".self::tableName()."(".implode(',', $fields).")
					VALUES(".implode(',', $formats).")", array_map("self::e", $values));
			mysql_query($query) or die(t("Object not created!") . mysql_error());
			$insId = mysql_insert_id();
			$this->id = $insId; 
		
			/** @todo review: change to after_insert */
			if (method_exists($this, 'afterInsert')) {
				$this->afterInsert($insId, $this);
			}
			return $insId;
		} else {
			return NULL;
		}
	}

	/**
	 * Executes query and returns array of objects.
	 *
	 * @param string $query
	 *
	 * @return array
	 */
	private static function findBySql($query) {
		if(!$result = mysql_query($query)) {
			debug_print_backtrace();
			die("Query: $query " . t("Object not found!") . mysql_error());
		}

		$objs = array();
		while ($row = mysql_fetch_assoc($result)) {
			$o = new static();
			$o->ffden($row);
			/** Must tell that object values are not dirty after load from db */
			$o->not_dirty();
			$objs[] = $o; 

		}
		return $objs;
	}

	/**
	 * Executes sql from $filter and returns objects set.
	 *
	 * @param Filter|SqlFilter $filter
	 *
	 * @return array|NULL
	 */
	public static function find($filter) {
		return self::findBySql($filter->makeSQL());
	}

	/**
	 * Checks whether row with @param $id exists.
	 * 
	 * @param integer $id
	 *
	 * return boolean
	 */
	public static function exists($id) {
		if (!is_numeric($id)) return FALSE;
		$cl = get_called_class();
		$filter = self::newFilter(array($cl => array('id')));
		$filter->setFrom(array($cl => 't'));
		$filter->setWhere(array($cl.'id' => (int)$id)); 
		$filter->setLimit(1);
		$obj = self::find($filter);
		return is_array($obj) && count($obj) == 1;
	}

	/**
	 * Loads object data from database.
	 *
	 * @param integer $id
	 *
	 * @param array $fields Fields which data to load.
	 *
	 * @return self|NULL
	 */
	public static function load($id, $fields = array()) {
		$cl = get_called_class();
		$filter = $cl::newFilter(array($cl => "*"));
		$filter->setFrom(array($cl => 't'));
		$filter->setWhere(array("$cl.id" => $id));
		$whats = array();
		if (!empty($fields) && is_array($fields)) {
			foreach($fields as $name)
				$whats[] = $name;
			$filter->setWhat(array($cl => $whats));
		}
		$obj = self::find($filter);
		return (is_array($obj) && !empty($obj)) ? $obj[0] : NULL;
	}

	/**
	 * Loads self from database by field data.
	 *
	 * @param array $fv Associative array 'field name' => 'value'.
	 * 
	 * @return self|FALSE
	 */
	public static function load_by($fv) {
		$filter = self::newFilter();
		$filter->setWhere($fv);
		$filter->setLimit(1);
		return current(self::find($filter));
	}

	public static function unionFilters($filters) { 
		$sqls = array();
		foreach ($filters as $f) {
			$tmp_filter = $f; 
			$tmp_filter->setLimit(NULL);
			$sqls[] = $tmp_filter->makeSQL();
		}
		$query = '('.join(' UNION ', $sqls).') as t1 ';
		return $query;
	}

	// ACCEPTS
	//      1. $filter
	//      2. array($filter, $filter, ...) - when UNION
	public static function cnt($filter) {
		if (is_array($filter)) { //2
			$query = 'SELECT COUNT(*) as cnt FROM '.self::unionFilters($filter);
		} else {
			$values = array();
			if ('' != $filter->getCount() && is_string($filter->what)) {
				$filter->setWhat($filter->getCount().','.$filter->what);
			} else {
				$filter->setWhat("COUNT(*) cnt");
			}

			$tmp_filter = $filter;
			$tmp_filter->order   = '';
			$tmp_filter->groupBy = '';
			$tmp_filter->limit   = '';
			$tmp_filter->offset  = '';
			$tmp_filter->setLimit(NULL);

			if (NULL != $filter->getCountFilter()) {
				/** has count filter */
				$tmp_filter = $filter->getCountFilter();
			}

			$query = $tmp_filter->makeSQL();
		}
		$result = mysql_query($query) or die($query . t("Objects count failed!"). mysql_error());

		// Some queries might not return any value.
		$ret = mysql_num_rows($result) == 0 ? mysql_num_rows($result) : mysql_fetch_object($result)->cnt; 

		return $ret;
	} 

	/**
	 * Escapes and quotes string.
	 *
	 * @param string $str String to escape&quote.
	 *
	 * @return string
	 */
	public static function eq($str) {
		return self::quote(self::e($str));
	}

	/**
	 * Escapes string.
	 *
	 * @param string $str
	 *
	 * @return string
	 */
	public static function e($str) {
		return mysql_real_escape_string($str);
	}

	/**
	 * Escapes string and adds double quotes to it.
	 *
	 * @param string $str
	 *
	 * @return string
	 */
	public static function edq($str) {
		return self::dq(self::e($str));
	}

	/**
	 * Adds single-quote to string.
	 *
	 * @param string $str
	 *
	 * @return string
	 */
	public static function q($str) {
		return self::quote($str);
	}

	/**
	 * Adds double-quote to string.
	 *
	 * @param string $str
	 *
	 * @return string
	 */
	public static function dq($str) {
		return '"'.$str.'"';
	}

	/**
	 * Adds single-quote to string.
	 *
	 * @param string $str
	 *
	 * @return string
	 */
	protected static function quote($str) {
		return "'$str'";
	}

	/**
	 * Makes new filter.
	 *
	 * @param mixed $what 
	 *
	 * @return Filter
	 */
	public static function newFilter($what = NULL) {
		$cl = get_called_class();
		if (NULL === $what) {
			$what = array($cl => array("*"));
		}
		$filter = new Filter($what);
		$filter->setFrom(array($cl => 't1'));
		return $filter;
	}

	/**
	 * Dies with message and prints executed query wich did not succeed .
	 *
	 * @param string $message A message to output.
	 *
	 * @param string $query The failed query.
	 *
	 * @return void
	 */
	private static function diem($message, $query) {
		die ($message.PHP_EOL. "Mysql error!  ".mysql_error().PHP_EOL."Actual query: ".$query.PHP_EOL);
	}

	/**
	 * Converts time (Unix timestamp) to date time format.
	 *
	 * @param integer $tm Unix timestamp to convert.
	 * 
	 * @return string
	 */
	public static function toDateTime($tm) {
		return self::toDate($tm).' '.self::toTime($tm);
	}

	/**
	 * Converts time (Unix timestamp) to date.
	 *
	 * @param integer $tm (optional) Unix timestamp to convert. If not given then assumes $tm = time().
	 * 
	 * @return string
	 */
	public static function toDate($tm=NULL) {
		if (NULL === $tm) $tm = time();
		return date("Y-m-d", $tm);
	}

	/**
	 * Converts time (Unix timestamp) to time only. Default output format is "H:i:s".
	 *
	 * @param integer $tm Unix timestamp to convert. If not given then asumes $tm = time().
	 * 
	 * @return string
	 */
	public static function toTime($tm=NULL) {
		if (NULL === $tm) $tm = time();
		return date("H:i:s", $tm);
	}

	/**
	 * Parses time from string and returns time string.
	 *
	 * @param string $field Name of the field containing the value.
	 *
	 * @return string
	 */
	public function asTime($field) {
		return self::toTime(strtotime($this->$field));
	}

	/**
	 * Parses time from string and returns timestamp.
	 *
	 * @param string $field Name of the field containing the value.
	 * 
	 * @return integer
	 */
	public function astm($field) {
		return strtotime($this->$field);
	}

	/**
	 * Returns object data as string. Use for output.
	 *
	 * @return string
	 */
	public function to_s() {
		if(isset($this->id)) {
			return ucfirst(c2u(get_called_class(),' '))."#".$this->id;
		}
	}

	/**
	 * Checks whether a row with values exists in the table.
	 *
	 * @param array $data Associative array 'field name' => 'data' to use in WHERE clause.
	 *
	 * @return boolean
	 */
	public static function exists_by(array $data) {
		$ret = FALSE;
		if(is_array($data)) {
			$cl = get_called_class();
			$filter = self::newFilter();
			$filter->setFrom(array($cl => 't'));
			$where = array();
			foreach($data as $field => $value) {
				if (self::isField($field)) {
					$where["$cl.$field"] = self::e($value);
				}
			}
			$filter->setWhere($where);
			$filter->setLimit(1);
			$obj = self::find($filter); 
			$ret = count($obj)==1;
		}
		return $ret;
	}

	/**
	 * Returns ClassName.field
	 * 
	 * @param string $field Field name.
	 *
	 * @return string
	 */
	public function cl($field) {
		return get_called_class().".$field";
	}

	/**
	 * Sets the field with datetime value.
	 *
	 * @param string $field
	 *
	 * @param integer $time Unix timestamp
	 *
	 * @return void
	 */
	public function setDateTime($field, $time) {
		if(self::isField($field)) {
			$this->$field = self::toDateTime($time);
		}
	}

	/**
	 * Returns serializable data in field=>value array.
	 *
	 * @return array
	 */
	public function sd() {
		$ret    = array();
		$fields = static::serializeableFields(); 
		foreach($fields as $field) 
			if(self::isField($field)) 
				$ret[$field] = $this->$field; 
		return $ret;
	}


	/**
	 * Converts object to Jobj.
	 *
	 * @return Jobj 
	 */
	public function to_jobj($mapping = NULL) {
		$sd = array();
		if(NULL !== $mapping) {
			foreach($mapping as $from => $to) {
				/** if from is function */
				$sd[$to] = method_exists($this, $from) ? 
							   $this->{$from}() : $this->$from;
			}
		} else {
			$sd = $this->sd();
		}
		return new Jobj(get_called_class(), $sd);
	}

	/**
	 * Returns array of class' serializable to json fields.
	 *
	 * @return array
	 */
	protected static function serializeableFields() {
		$cl = get_called_class();
		if(method_exists($cl, 'serializeableFields')) {
			return static::fieldNames();
		} else {
			self::fieldNames();
		}
	}

	/**
	 * Attach object to object.
	 *
	 * @param mixed $o Object to attach to.
	 *
	 * @param boolean $save (optional) Whether to save after attachment.
	 */
	public function attach_to($o, $save = FALSE) {
		$this->attached_to = get_class($o);
		$this->attached_id = $o->id;
		if($save) {
			$this->save();
		}
	}

	/**
	 * Makes objects values not dirty.
	 */
	public function not_dirty() {
		$this->init_changed();
	}

	/**
	 * Quotes identifier according to MySQL rules.
	 *
	 * @param string $name
	 *
	 * @return string
	 */
	public function iq($name) {
		return '`'.$name.'`';
	}

	/**
	 * Get filter for some objects.
	 *
	 * @param integer $how_many
	 *
	 * @return self[]
	 */
	public static function filter_some($how_many = 20) {
		$filter = self::newFilter();
		$filter->setLimit($how_many);
		return $filter;
	}

	/**
	 * Check if is attached to the given object.
	 *
	 * @param mixed $o
	 *
	 * @return boolean
	 */
	public function is_attached_to($o) {
		return $this->attached_to === get_class($o) && $this->attached_id == $o->id;
	}

	/**
	 * Returns attached object.
	 *
	 * @param mixed $o
	 *
	 * @return self
	 */
	public static function get_attached_to($o) {
		$cl  = get_class($o);
		$ccl = get_called_class(); 
		$ret = self::load_by(array(
			"$ccl.attached_to" => $cl,
			"$ccl.attached_id" => $o->id));

		return $ret;
	}

	/**
	 * As base currency.
	 *
	 * @param string $field_name
	 *
	 * @return Monia
	 */
	public function asbc($field_name) {
		$amount = new Monia(Config::base_currency(), $this->$field_name);
	}

	/**
	 * As currency.
	 *
	 * @param string $field_name
	 *
	 * @param Currency $currency (optional)
	 *
	 * @return Monia
	 */
	public function asc($field_name, Currency $currency = NULL) {
		$base_currency = method_exists(get_called_class(),'base_currency') ?
			static::base_currency()
		  : Config::base_currency();

		$amount = new Monia($base_currency, $this->$field_name);
		if(NULL !== $currency) {
			$amount = $amount->converted_to($currency);
		}
		return $amount;
	}

	/**
	 * As money
	 *
	 * @param string $field_name
	 *
	 * @param Currency $currency (optional)
	 *
	 * @return string
	 */
	public function asm($field_name, Currency $currency = NULL) {
		if(NULL == $currency) {
			$currency = Config::base_currency();
		}
		return self::format_money(self::asc($field_name, $currency));
	}

	/**
	 * Format money.
	 *
	 * @param Monia $amount
	 *
	 * @param string $thousands_separator (optional)
	 *
	 * @return string
	 */
	public static function format_money(Monia $amount, $thousands_separator = ' ', $cents_separator = ',', $cents_precision = 0, $show_sign = FALSE) {
		$sw = sprintf("%s", $amount->whole());

		/** format in groups */ 
		$i = 1;
		$s = array();
		foreach(str_split(strrev($sw)) as $ch) {
			$s[] = $ch;
			if($i % 3 == 0) {
				$s[] = $thousands_separator;
			}
			$i++;
		}
		if($cents_precision == 0) {
			$cents_str = '';
		} else {
			$cents_str = $cents_separator.str_pad(substr($amount->cents(), 0, $cents_precision), $cents_precision, '0');
		}
		$sign = $show_sign && $amount->as_f() > 0 ? '+' : '' ;
		return $sign.join('',array_reverse($s)).$cents_str.' '.$amount->currency()->name();
	}

	/**
	 * Get object in xml.
	 *
	 * @param Field[] $fields (optional)
	 *
	 * @return string
	 */
	public function as_xml(array $fields = array()) { 
		$fields = static::editable_fields();
		$ret = '';

		foreach($fields as $field) {
			$name  = $field->name();
			$value = $this->$name; 
			if(!empty($value)) { 
				$ret .= '<f name="'.so($name).'">'.so($value).'</f>';
			}
		}

		$clp = c2u(get_called_class());
		return sprintf('<%2$s id="%1$d">%3$s</%2$s>', $this->id, $clp, $ret);
	}

	/**
	 * Gives array of fields by array of fields names.
	 *
	 * @param array $names
	 *
	 * @return Field[]
	 */
	public static function fields_by_names(array $names) {
		$ret = array();
		foreach($names as $name) {
			if(self::is_field($name)) {
				$ret[] = self::field($name);
			}
		}
		return $ret;
	}

	/**
	 * Tells whether field exists as field.
	 *
	 * @param string $name
	 *
	 * @return boolean
	 */
	public static function is_field($name) {
		return self::field($name);
	}

	/**
	 * Toggles field value.
	 *
	 * @param string $name
	 *
	 * @return void
	 */
	public function toggle($name) {
		$this->$name = !($this->$name);
		$this->save();
	}

}

