<?
class SearchAgent extends Dbobj implements DbobjInterface {

	public static function fields() {
		return array(
			new Field('id'         , Field::T_NUMERIC , "%d"),
			new Field('user_id'    , Field::T_NUMERIC , "%d"),
			new Field('name'       , Field::T_TEXT)   ,
			new Field('searchable' , Field::T_TEXT)   ,
			new Field('is_active'  , Field::T_BOOLEAN),
			new Field('last_run_on', Field::T_NUMERIC),
			new Field('is_running' , Field::T_BOOLEAN),
			new Field('is_run'     , Field::T_BOOLEAN)
		);
	}

	/**
	 * Number of active agents.
	 *
	 * @param integer $user_id
	 *
	 * @return void
	 */
	public static function agents_active_for($user_id) {
		$filter = SearchAgent::newFilter();
		$filter->setWhere(array(
			'SearchAgent.is_active' => 1,
			'SearchAgent.user_id'   => $user_id
		));
		return self::cnt($filter);
	}

	/**
	 * Validates search agent.
	 *
	 */
	public static function hasValidationErrors() {
		$validation = array();

		return $validation;
	}

	/**
	 * Returns editable fields.
	 *
	 * @return Field[]
	 */
	public static function editable_fields() {
		return array_filter(self::fields(),
			function($el) {
				return FALSE === array_search(
					$el->name(),
					array('id', 'user_id'));
			}
		);
	}

	/**
	 * Save data into values.
	 *
	 * @param array $data Associative array of data: "field_name" => "search_value"
	 */
	public function make_values(array $data) {
		$fields = array();
		foreach($data as $name => $search_value) {
			$orig_name = $name;
			if(strrpos($name, "_min")) {
				$name = substr($name, 0, strlen($name) - strlen("_min"));
			} else if(strrpos($name, "_max")) {
				$name = substr($name, 0, strlen($name) - strlen("_max"));
			}

			$field = afopm($fields, 'name', $name);
			if(!$field) {
				$field = new Field($name, Field::T_TEXT);  
			}

			$field->set_fv($orig_name, $search_value);

			/** Makes field unique */
			$fields[$name] = $field;
		}

		$svs = array();
		foreach($fields as $field) {

			if(NULL !== $field->value()) {
				$sv = new SearchValue();
				$sv->search_field = $field->name();
				$sv->search_value = $field->value();
				$svs[] = $sv;
			} else {
				if(NULL !== $field->get_min()) {
					$sv = new SearchValue();
					$sv->search_field = $field->name().'_min';
					$sv->search_value = $field->get_min();
					$svs[] = $sv;
				}
				if(NULL !== $field->get_max()) {
					$sv = new SearchValue();
					$sv->search_field = $field->name().'_max';
					$sv->search_value = $field->get_max();
					$svs[] = $sv;
				}
			}

			foreach($svs as $sv) {
				if(!$this->isNew()) {
					$sv->search_agent_id = $this->id;
				}
			}
		}

		return $svs;
	}

	/**
	 * Sets values.
	 *
	 * @param SearchValue[] $values
	 *
	 * @return void
	 */
	public function set_values(array $values) {
		/** Remove old values */
		if(!$this->isNew()) {
			SearchValue::delWhere(array(
				'search_agent_id' => $this->id
			));
		}
		/** Filter out empty values */
		$values = array_filter($values,
			function($el) {
				return '' !== "$el->search_value";
			});

		foreach($values as $value) {
			$value->search_agent_id = $this->id;
			$value->save();
		}

	}

	/**
	 * Load data.
	 *
	 * @return SearchValue[]
	 */
	public function load_values() {
		$ret = array();
		if(!$this->isNew()) {
			$filter = SearchValue::newFilter();
			$filter->setFrom(array("SearchValue" => "sv"));
			$filter->setWhere(array(
				'SearchValue.search_agent_id' => $this->id
			));
			$ret = SearchValue::find($filter);
		}
		return $ret;
	}

	/**
	 * Loads values as fields for Class.
	 *
	 * @param string cl Provider of fields.
	 *
	 * @return Field[]
	 */
	public function load_values_as_fields_for($cl) {
		$values = $this->load_values();
		$ret    = array();
		foreach($values as $value) {
			$name  = $value->search_field;
			$m     = NULL;
			if(strrpos($name, "_min")) {
				$name = substr($name, 0, strlen($name) - strlen("_min"));
				$m = '_min';
			} else if(strrpos($name, "_max")) {
				$name = substr($name, 0, strlen($name) - strlen("_max"));
				$m = '_max';
			}

			$field = isset($ret[$name]) ? $ret[$name] : $cl::field($name);

			if(!empty($field)) {
				if($m == '_min') { 
					$field->set_min($value->search_value);
				} else if($m == '_max') {
					$field->set_max($value->search_value);
				} else {
					$field->set_value($value->search_value);
				}
				$ret[$field->name()] = $field;
			}
		}

		return array_values($ret);

	}

	/**
	 * Gets value by name.
	 *
	 * @param string $name
	 *
	 * @return SearchValue
	 */
	public function value_of($name) {
		return SearchValue::load_by(array(
			"SearchValue.search_agent_id" => $this->id));
	}

	public function __toString() {
		$name = $this->name;
		return !empty($name) ? $this->name : $this->to_s();
	}

	/**
	 * Make real search parameters.
	 *
	 * @return array
	 */
	public function make_search($searchable) {
		$values = $this->load_values();
		$params = array();
		$s      = c2u($searchable);
		foreach($values as $value) {
			$params["$s"."[$value->search_field]"] = $value->search_value;
		}
		return http_build_query($params);
	}

	/**
	 * Permissions: can_view.
	 *
	 * @param User $user
	 *
	 * @return boolean
	 */
	public function can_view(User $user) {
		return Access::is_owner($user, $this);
	}

	/**
	 * Update last_run_on. Must be start_running called before.
	 *
	 * @return void
	 */
	public function stop_running() {
		$this->is_running = 0;
		$this->is_run     = 1;
		$this->save();
	}

	/**
	 * Start running.
	 *
	 * @return void
	 */
	public function start_running() {
		$this->is_running  = 1;
		$this->last_run_on = time();
		$this->save();
	}
}

