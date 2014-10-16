<?

/**
 * Real class.
 */
class Real extends Dbobj implements DbobjInterface {

	const NAME_MY_ID = 'reference_id';

	/**
	 * Fields of real.
	 *
	 * @return Field[]
	 */
	public static function fields() {
		return array(
			new Field('id', Field::T_NUMERIC, "%d", FALSE, FALSE),
			new Field('user_id', Field::T_NUMERIC, "%d", FALSE, FALSE),
			new Field('touched_on', Field::T_NUMERIC, "%d", TRUE, TRUE, 'Updated on'),
			new Field(self::NAME_MY_ID, Field::T_TEXT, "%s", TRUE, TRUE),
			new Field('is_active', Field::T_BOOLEAN, "%d", FALSE, FALSE),
			new Field('is_sold', Field::T_BOOLEAN, NULL, NULL, TRUE),

			/** Location */
			new Field('city', Field::T_TEXT, "%s"),
			new Field('district', Field::T_TEXT, "%s"),
			new Field('street', Field::T_TEXT, "%s"),

			/** Area */
			new Field('area', Field::T_NUMERIC, "%.2f"),
			new Field('rooms', Field::T_NUMERIC, "%d"),
			new Field('price', Field::T_NUMERIC, "%d"),

			/** Floors */
			new Field('floor', Field::T_NUMERIC, "%d"),
			new Field('is_first_floor', Field::T_BOOLEAN, NULL, TRUE),
			new Field('is_last_floor', Field::T_BOOLEAN, NULL, TRUE),
			new Field('is_on_several_floors', Field::T_BOOLEAN, NULL, TRUE),

			/** Parking */
			new Field('has_parking', Field::T_BOOLEAN, NULL, FALSE),
			new Field('parking_price', Field::T_NUMERIC, "%d", TRUE, TRUE), 

			/** Garage */
			new Field('has_garage', Field::T_BOOLEAN, NULL, TRUE, TRUE),
			new Field('garage_price', Field::T_NUMERIC, "%d", TRUE),

			/** Balcony */
			new Field('has_balcony', Field::T_BOOLEAN, NULL, TRUE),
			new Field('balcony_area', Field::T_NUMERIC, "%.2f", TRUE),

			/** Virtuve */
			new Field('kitchen_area', Field::T_NUMERIC, "%.2f", TRUE), 
			/** virtuvė su baldais */
			new Field('has_kitchens', Field::T_BOOLEAN, NULL, TRUE),
			new Field('has_sitting-room_combined_with_kitchen', Field::T_BOOLEAN, NULL, TRUE),


			/** House information */
			new Field('year_of_construction', Field::T_NUMERIC, "%d", FALSE),
			new Field('is_renovated', Field::T_BOOLEAN, NULL, TRUE),
			new Field('house_type', Field::T_TEXT, NULL, TRUE),
			new Field('heating_type', Field::T_TEXT, NULL, TRUE),
			new Field('house_floors', Field::T_NUMERIC, "%d", TRUE),

			/** Other information */

			new Field('has_separate_wc', Field::T_BOOLEAN, NULL, TRUE),
			/** sieninė spinta */
			new Field('has_closet', Field::T_BOOLEAN, NULL, TRUE),
			new Field('has_cellar', Field::T_BOOLEAN, NULL, TRUE),
			new Field('has_dark_closet', Field::T_BOOLEAN, NULL, TRUE)

		);
	}

	/**
	 * Returns location fields.
	 *
	 * @return Field[]
	 */
	public static function fields_location() {
		return array(self::field('city'), self::field('district'), self::field('street'));
	}

	/**
	 * Has location info.
	 *
	 * @return boolean
	 */
	public function has_location_info() {
		return count(fef($this, self::fields_location())) > 0 ;
	}

	/**
	 * Returns area fields.
	 *
	 * @return Field[]
	 */
	public static function fields_area() {
		return array(self::field('area'), self::field('rooms'), self::field('kitchen_area'));
	}

	/**
	 * Has area information.
	 *
	 * @return boolean
	 */
	public function has_area_info() {
		return count(fef($this, self::fields_area())) > 0;
	}

	/**
	 * Returns parking fields.
	 *
	 * @return Field[]
	 */
	public static function fields_parking() {
		return array(self::field("has_parking"), self::field("parking_price"));
	}

	/**
	 * Returns garage fields.
	 *
	 * @return Field[]
	 */
	public static function fields_garage() {
		return array(self::field('has_garage'), self::field('garage_price'));
	}

	/**
	 * Price fields.
	 *
	 * @return Field[]
	 */
	public static function fields_price() {
		return array(self::field('price'));
	}

	/**
	 * House fields.
	 *
	 * @return Field[]
	 */
	public static function fields_house() {
		return self::fields_by_names(array('year_of_construction', 'is_renovated', 'house_type', 'heating_type', 'house_floors'));
	}

	/**
	 * Floor fields
	 *
	 * @return Field[]
	 */
	public static function fields_floor() {
		return self::fields_by_names(array('floor', 'is_first_floor', 'is_last_floor', 'is_on_several_floors'));
	}

	/**
	 * Balcony fields
	 *
	 * @return Field[]
	 */
	public static function fields_balcony() {
		return self::fields_by_names(array('has_balcony', 'balcony_area'));
	}

	/**
	 * Kitchen fields
	 *
	 * @return Field[]
	 */
	public static function fields_kitchen() {
		return self::fields_by_names(array('has_kitchens', 'has_sitting-room_combined_with_kitchen'));
	}

	/**
	 * Other fields
	 *
	 * @return Field[]
	 */
	public static function fields_other() {
		return self::fields_by_names(array('is_sold', 'has_cellar', 'has_dark_closet'));
	}

	/**
	 * Has kitchen information
	 *
	 * @return boolean
	 */
	public static function has_kitchen_info() {
		return count(fef($this, self::fields_kitchen())) > 0;
	}

	/**
	 * Has balcony information
	 *
	 * @return boolean
	 */
	public static function has_balcony_info() {
		return count(fef($this, self::fields_balcony())) > 0;
	}

	/**
	 * Has floor information.
	 *
	 * @return boolean
	 */
	public static function has_floor_info() {
		return count(fef($this, self::fields_floor())) > 0;
	}

	/**
	 * Has parking information?
	 *
	 * @return boolean
	 */
	public function has_parking_info() { 
		return count(fef($this, self::fields_parking())) > 0;
	}

	/**
	 * Has garage information?
	 *
	 * @return boolean
	 */
	public function has_garage_info() {
		return count(fef($this, self::fields_garage())) > 0;
	}

	/**
	 * Has price information?
	 *
	 * @return boolean
	 */
	public function has_price_info() {
		return count(fef($this, self::fields_price())) > 0;
	}

	/**
	 * Has house information?
	 *
	 * @return boolean
	 */
	public function has_house_info() {
		return count(fef($this, self::fields_house())) > 0;
	}

	/**
	 * Has other information?
	 *
	 * @return boolean
	 */
	public function has_other_info() {
		return count(fef($this, self::fields_other())) > 0;
	}

	/**
	 * Helper method for location.
	 */
	public function full_address_str() {
		return join(', ', ne(array($this->city, $this->district, $this->street)));
	}

	/**
	 * Validates real.
	 *
	 * @return array
	 */
	public static function hasValidationErrors() {
		$validation = array();
		return $validation;
	}

	/**
	 * After insert.
	 *
	 * @param integer $id Id of the object.
	 *
	 * @param mixed $o Object.
	 */
	public function afterInsert($id, $o) {
		/** @todo (optional) Before insert delete all object's virtual fields that might be present. */
		Serializator::delete_o($o->id);
		/** Insert virtual fields */
		Serializator::update_o($o, Real::editable_fields());
	}

	/**
	 * After update.
	 *
	 * @return void
	 */
	public function after_update() { 
		/** update virtual fields */
		Serializator::update_o($this, $this->dirty_fields());
	}

	/**
	 * After delete.
	 *
	 * @return void
	 */
	public function after_delete() {
		if(!$this->isNew()) {
			/** delete virtual fields */
			Serializator::delete_o($this->id);
		}
	}

	/**
	 * Make string from real.
	 *
	 * @return string
	 */
	public function to_s() {
		return join(', ', array(so($this->city), so($this->rooms)." rooms", so($this->area)." m&sup2;", so($this->price)." LTL"));
	}

	/**
	 * Returns all editable fields. Not editable are fields: id, user_id, etc.
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
	 * Searchable fields
	 *
	 * @return Field[]
	 */
	public static function searchable_fields() {
		return array_filter(self::fields(),
			function($el) {
				return FALSE === array_search(
					$el->name(),
					array('id', 'user_id', 'is_active'));
			}
		);
	}

	/**
	 * Fields groups
	 *
	 * @return array
	 */
	public static function fields_groups() {
		return array(
			array(
				'provider' => 'fields_location',
				'title'    => 'Location'
			),
			array(
				'provider' => 'fields_price',
				'title'    => 'Price'
			),
			array(
				'provider' => 'fields_area',
				'title'    => 'Area'
			),
			array(
				'provider' => 'fields_parking',
				'title'    => 'Parking'
			),
			array(
				'provider' => 'fields_garage',
				'title'    => 'Garage'
			),
			array(
				'provider' => 'fields_house',
				'title'    => 'House information'
			),
			array(
				'provider' => 'fields_floor',
				'title'    => 'Floor information'
			),
			array(
				'provider' => 'fields_balcony',
				'title'    => 'Balcony information'
			),
			array(
				'provider' => 'fields_kitchen',
				'title'    => 'Kitchen information'
			),
			array(
				'provider' => 'fields_other',
				'title'    => 'Other information'
			)
		);
	}

	/**
	 * Gets values for $name field.
	 *
	 * @param string $name
	 *
	 * @param string $value
	 *
	 * @return 
	 */
	public static function values_for($name, $value) {
		$vs = array();
		$iqname = Dbobj::iq($name);
		$filter = self::newFilter("DISTINCT($iqname)"); 
		$filter->setLimit(20);
		$filter->setWhere("IFNULL($iqname,'') LIKE '$value%%'");
		$reals = Real::find($filter);

		foreach($reals as $real) {
			$v = new Value();
			$v->value = $real->$name;
			$v->value_for = 'real_'.$name;
			$v->value_of  = $name;
			$v->clname    = "Real";
			$vs[] = $v->to_jobj();
		}

		return $vs;
	}
}

