<?

/**
 * Saves object's data to database.
 * @version 0.1.0
 *
 * @todo Field's "id" value should not be saved.
 */
class Serializator {

	/**
	 * Saves object's data accross values tables.
	 *
	 * @param mixed $o
	 */
	public static function save_o($o) {
		self::update_o($o);
	}

	/**
	 * Updates objects data accross values tables.
	 *
	 * @param Object $o
	 *
	 * @param Field[] $fields Fieldnames of fields to update.
	 */
	public static function update_o($o, array $fields = array()) { 
		//saved values
		$osv = self::get_values($o->id); 
		//new values
		$nvs = self::to_values($o, $fields);

		$updates_all_fields = empty($fields);

		if($updates_all_fields) {
			/** delete values that are gone */
			foreach($osv as $v) {
				if(!afopm($nvs, 'name', $v->name)) {
					$v->d();
				}
			}
		}

		//add new values
		foreach($nvs as $v) {
			if($ov = afopm($osv, 'name', $v->name)) {
				/** found */
				if(NULL === $v->value) {
					$ov->d();
				} else {
					//@todo change to: 1) delete old value; 2) insert new value.
					$ov->value = $v->value;
					$ov->save();
				}
			} else if(NULL !== $v->value) {
				$v->insert();
			}
		}
	}

	/**
	 * Deletes objects data from properties tables.
	 *
	 * @param integer $oid Object id.
	 *
	 * @todo add attached_to
	 *
	 * @return void
	 */
	public static function delete_o($oid) {
		ValueNumeric::delWhere(array('oid' => $oid));
		ValueText::delWhere(array('oid' => $oid));
		ValueBoolean::delWhere(array('oid' => $oid));
	}

	/**
	 * Returns object from database. Objects data = virtual values + values.
	 *
	 * @param integer $id Id of the object.
	 *
	 * @param string $class Class name of the object.
	 *
	 * @return Object
	 */
	public static function get_obj($id, $class) {
		$obj    = $class::load($id);
		$values = self::get_values($id);
		$obj    = self::build_o($values, $obj);
		return $obj;
	}
	
	/**
	 * Converts objects data to values.
	 *
	 * @param Object $object
	 *
	 * @param array $fields (optional) Names of fields to convert.
	 *
	 * @return array
	 */
	public static function to_values($object, array $fields = array()) {
		$nvalues = array();
		$fs      = $fields;
		if(empty($fs)) {
			$cl = get_class($object);
			$fs = $cl::fields();
		}

		foreach($fs as $field) {
			if(isset($object->{$field->name()})) {
				$value = $object->{$field->name()};
				if(is_numeric($value)) {
					$nvalue = new ValueNumeric();
				} else if(is_bool($value)) {
					$nvalue = new ValueBoolean();
				} else if(is_string($value)) {
					$nvalue = new ValueText();
				}
				$nvalue->value = $value;
				$nvalue->name  = $field->name();
				$nvalue->oid   = $object->id;
				$nvalues[]     = $nvalue; 
			}
		}
		return $nvalues;
	}

	/**
	 * Builds object from values.
	 *
	 * @param array $values
	 *
	 * @param Object $o
	 *
	 * @return Object
	 */
	public static function build_o(array $values, $o) {
		$has_values = count($values) > 0;
		foreach($values as $value) {
			$field = $value->name;
			$vval  = $value->value;
			if(is_a($value, 'ValueNumeric')) {
				$val = (float)$vval;
			} else if(is_a($value, 'ValueBoolean')) {
				$val = (bool)$vval;
			} else if(is_a($value, 'ValueText')) {
				$val = (string)$vval;
			}
			$o->$field = $val;
		}
		/** Make object fields not dirty because after object load object is not dirty. */
		if($has_values) {
			$o->not_dirty();
		}
		return $o;
	}

	/**
	 * Gets values for oid.
	 *
	 * @param integer $oid Object id.
	 *
	 * @return array
	 */
	private static function get_values($oid) {
		$filter = new SqlFilter("*");
		$filter->setWhere("oid = ".$oid);

		/** Get numeric data */
		$filter->setFrom(ValueNumeric::tableName()." vn");
		$vn = ValueNumeric::find($filter); 

		/** Get boolean data */
		$filter->setFrom(ValueBoolean::tableName()." vb");
		$vb = ValueBoolean::find($filter);

		/** Get text data */
		$filter->setFrom(ValueText::tableName()." vt");
		$vt = ValueText::find($filter);

		return array_merge($vn, $vb, $vt);
	}

}

class Search {
	private $m_fields = array();

	/** fields to search data within */ 
	private $m_searchables;

	/**
	 * @param callable $sp
	 */
	function __construct($searchables) {
		$this->set_searchables($searchables);
	}

	/**
	 * Setter for m_searchables.
	 *
	 * @param array $searchables
	 */
	public function set_searchables(array $searchables) {
		$this->m_searchables = $searchables;
	}

	/**
	 * Returns the number of different types in search.
	 *
	 * @return integer
	 */
	private function types_count() {
		$fields = $this->fields(); 
		$types  = array();
		foreach($fields as $field) {
			$types[] = $field->type();
		}
		return count(array_unique($types));
	}

	/**
	 * Returns fields that represent types used in search.
	 *
	 * @return array
	 */
	private function fields_distinct_types() {
		$fields = $this->fields();
		$types  = array();
		$ret    = array();
		foreach($fields as $field) {
			if(!in_array($field->type(), $types)) {
				$ret[]   = $field;
				$types[] = $field->type();
			}
		}
		return $ret;
	}

	 /**
SELECT r.*

FROM reals r

WHERE r.id IN (

SELECT oid as id FROM (

SELECT v1.*, 'values_boolean' as source

FROM values_boolean v1

WHERE ((v1.name = 'is_sold' AND v1.value = FALSE) OR (v1.name = 'has_separate_wc' AND v1.value = TRUE))

GROUP BY v1.oid

HAVING count(v1.oid)=2

UNION 

SELECT v2.*, 'values_text' as source

FROM values_text v2

WHERE ((v2.name = 'city' AND v2.value LIKE 'Siauliai'))

GROUP BY v2.oid

HAVING count(v2.id)=1
) as vals

GROUP BY oid
HAVING COUNT(*) = 2 )
	 *
	 *
	 *
	 */

	public function make_filter_joins() {
		$types_count   = $this->types_count();
		if(0 == $types_count) { 
			/** no search values specified */ 
			$filter = Real::filter_some();
		} else {
			$dfields       = $this->fields_distinct_types();
			$first_field   = array_shift($dfields);
			$first_type    = self::value_class($first_field);
			$other_dfields = $dfields;

			$filter = new SqlFilter("t.*, v1.oid"); 
			$rtable = Real::tableName();

			/** Set first type */ 
			$filter->setFrom($first_type::tableName()." v1");
			$wheres = array();

			$first_fields = $this->fields_by_type($first_field->type());
			$wheres[] = $this->make_where($first_field->type(), "v1");
			$joins = array();

			/** set other types */
			if(!empty($other_dfields)) {
				$i = 2;
				foreach($other_dfields as $dtype) {
					$other_type = self::value_class($dtype);
					$table = "v$i";
					$joins[] = "JOIN ".$other_type::tableName()." $table"." ON $table.oid = v1.oid";
					$wheres[] = $this->make_where($dtype->type(), $table);
					$i++;
				}
			}
			/** add Real data table */
			$joins[] = " JOIN $rtable t ON t.id = v1.oid ";

			$filter->setJoin(join(' ', $joins));
			$filter->setWhere(join(' AND ', $wheres));
			$filter->setGroupBy('t.id');
			$filter->setHaving(sprintf("COUNT(*) = %d", count($this->fields())));

			$count_filter = clone $filter;
			$count_filter->setWhat("COUNT(*) as cnt");
			$filter->set_count_filter($count_filter);
		}

		return $filter;
	}

	/**
	 * Returns value class for field.
	 *
	 * @param Field $field
	 *
	 * @return string
	 */
	private static function value_class($field) {
		$ret = NULL;
		if($field->isnumeric()) {
			$ret = 'ValueNumeric';
		} else if($field->istext()) {
			$ret = 'ValueText'; 
		} else if($field->isboolean()) {
			$ret = 'ValueBoolean';
		}
		return $ret;
	}

	/**
	 * Gets wheres for fields which have the same type as given field.
	 */
	private function get_wheres_for($dfield) {
		$fields = $this->fields();
	}

	/**
	 * Makes wheres for fields with given types.
	 *
	 * @param integer $type
	 *
	 * @param string $t Table name.
	 * 
	 * @return string
	 */
	private function make_where($type, $t) {
		$fields = self::fields_by_type($type);
		$wheres = array();

		foreach($fields as $field) {
			$name = Dbobj::eq($field->name());

			/** Text fields */
			if($field->type() == Field::T_TEXT) {

				$wheres[] = sprintf(
					"($t.name = %s AND $t.value LIKE %s)",
					$name,
					Dbobj::eq($field->value()));

			}

			/** Numeric fields */
			if($field->type() == Field::T_NUMERIC) {
				$wheres_tmp = array();

				if($field->get_min() !== $field->get_max()) {
					if(NULL !== $field->get_min()) {
						$wheres_tmp[] = " $t.value >= ".Dbobj::eq($field->get_min()); 
					}

					if(NULL !== $field->get_max()) {
						$wheres_tmp[] = " $t.value <= ".Dbobj::eq($field->get_max());
					} 
					$wheres[] = sprintf("($t.name = %s AND %s)", 
						$name,
						join(' AND ', $wheres_tmp));
				} else {
					$wheres[] = sprintf("($t.name = %s AND $t.value = {$field->format()})", $name, $field->get_min());
				}
			}

			/** Boolean fields */
			if(Field::T_BOOLEAN == $field->type()) {

				if(TRUE === $field->value() || FALSE === $field->value()) {
					$val = $field->value() ? "TRUE" : "FALSE";
				   $wheres[] = sprintf("($t.name = %s AND $t.value = %s)", $name, $val);
				}

			}

		}

		/**
		 * @todo make work
		 
		 SELECT t . * , v1.oid
FROM values_boolean v1
JOIN reals t ON t.id = v1.oid
WHERE (
(
v1.name =  'is_sold'
AND v1.value = 
FALSE
)
OR (
v1.name =  'has_separate_wc'
AND v1.value = 
TRUE
)
)
LIMIT 0 , 30
		 */

		return '('.join(' OR ', $wheres).')';
	}

	/**
	 * Returns filters for search.
	 *
	 * @return SqlFilter[]
	 */
	public function make_filter() { 
		$filter = new Filter("r.*");
		$filter->setFrom(array("Real" => "r"));

		/** Make select for each data type */ 
		$selects = array();

		$types_count = $this->types_count();

		if(0 == $types_count) { 
			/** no search values specified */ 
			$filter = Real::filter_some();
		} else {
			$dfields = $this->fields_distinct_types();

			if(!empty($dfields)) {
				$i = 1;
				foreach($dfields as $dtype) {
					$cl    = self::value_class($dtype);
					$table = "v$i";
					$tmp_filter = new SqlFilter("v$i.*, '$cl' as source");
					$tmp_filter->setFrom($cl::tableName()." v$i");
					$tmp_filter->setWhere($this->make_where($dtype->type(), $table));
					$tmp_filter->setGroupBy("v$i.oid");
					$tmp_filter->setHaving( sprintf("COUNT(v$i.oid)=%d", count($this->fields_by_type($dtype->type()))) );
					$selects[] = $tmp_filter->makeSQL();
					$i++;
				}
			} 
		}

		$where_str = '';
		if(!empty($selects)) {
			$where_str = sprintf('r.id IN(SELECT oid as id FROM ( %s ) as vals GROUP BY oid HAVING COUNT(*) = %d )', 
				join(' UNION ', $selects), 
				$types_count); 
			$filter->setWhere($where_str);
		}

		return $filter;
	}

	public function searchables() {
		return $this->m_searchables; 
	}

	/**
	 * Returns fields that have given type.
	 *
	 * @param integer $type
	 *
	 * @return array
	 */
	public function fields_by_type($type) {
		$ret    = array();
		$fields = $this->fields(); 
		foreach($fields as $field) {
			if($field->type() === $type) {
				$ret[] = $field;
			}
		}
		return $ret;
	}

	/**
	 * Getter for m_fields.
	 *
	 * @return array
	 */
	private function &fields() {
		return $this->m_fields;
	}

	/**
	 * Gets field by name.
	 *
	 * @return Field Reference to field.
	 */
	public function &field($name) {
		$ret    = NULL;
		$fields = &$this->fields();
		$found  = FALSE;
		$searchables = $this->searchables();
		/** loop through fields and return field with name */
		foreach($fields as &$field) {
			if($field->name() === $name) {
				$ret   =& $field;
				$found = TRUE;
				break;
			}
		}

		/** if not found then take field specification from searchables. */
		if(!$found) {
			$ret = afopm($searchables, 'name', $name);
			array_push($this->fields(), $ret);
		}

		return $ret;
	}

	/**
	 * Adds min value to field.
	 *
	 * @param string $name Name of the field.
	 *
	 * @param string $value Value of the field.
	 */
	public function add_min($name, $value) {
		$field =& $this->field($name);
		$field->set_min($value);
	}

	/**
	 * Adds max value to field.
	 *
	 * @param string $name Name of the field.
	 *
	 * @param string $value Value of the field.
	 */
	public function add_max($name, $value) {
		$field =& $this->field($name);
		$field->set_max($value);
	}

	public function unset_min_max($name, $value) {
		$field =& $this->field($name);
		$field->set_max(NULL);
		$field->set_min(NULL);
	}

	/**
	 * Adds value to field.
	 *
	 * @param string $name Name of the field.
	 *
	 * @param string $value Value of the field.
	 *
	 * @return void
	 */
	public function add_value($name, $value) {
		$field =& $this->field($name);
		$field->set_value($value);
	}

	/**
	 * Setter for fields.
	 *
	 * @param Field[] $fields
	 *
	 * @return void
	 */
	public function set_fields(array $fields) {
		$this->m_fields = $fields;
	}
}

/**
 * Makes full search.
 *
 * Searches for objects:
 * Field value equals
 * Field value not equals
 * Field value is between
 * Field value is less than
 * Field value is more than
 * Field value exists
 */
class Searchable {
	/**
	 * Constructs search clause.
	 *
	 * @param Field[] $fields 
	 *
	 * @param array $from_data (optional) Data to make search.
	 */
	public static function make_search(array $fields, array $from_data = array()) {
		$s = new Search($fields);
		foreach($from_data as $name => $value) {
			/**
			 * Skip all data that is equal ''
			 */
			if('' === $value) {
				continue;
			}
			/**
			 * 1. If there is only min, then add it.
			 * 2. If min and max equal then add value and unset min max.
			/** is it min? */
			if(strrpos($name, "_min")) {
				$s->add_min(substr($name, 0, strlen($name) - strlen("_min")) , $value);
			/** is it max? */
			} else if(strrpos($name, "_max")) {
				$s->add_max(substr($name, 0, strlen($name) - strlen("_max")), $value);
			} else {
				$s->add_value($name, $value);
			}
		}
		return $s;
	}

}

