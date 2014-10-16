<?

/**
 * Form tags generator.
 * @version 0.1.1
 */ 
class Form {

	/**
	 * Make submit input.
	 *
	 * @param string $value
	 *
	 * @param array $attributes Attributes in associative array "attribute name" => "value".
	 *
	 * @return string
	 */
	public static function submit($value, $attributes = array()) {
		return '<input type="submit" value="'.$value.'" '.self::make_attrs($attributes).' />';
	}

	/**
	 * Makes attributes.
	 *
	 * @param array $attributes Attributes in associative array.
	 *
	 * @return string
	 */
	public static function make_attrs(array $attributes) {
		$rets = array();
		foreach($attributes as $attr_name => $attr_value) {
			$rets[] = $attr_name.'="'.so($attr_value).'"';
		}
		return join(' ', $rets);
	}

	/**
	 * Make input.
	 *
	 * @todo make attributes optional. Attributes should be passed in array $attrs.
	 *
	 * @param string $type
	 *
	 * @param string $name
	 *
	 * @param string (optional) $value
	 *
	 * @param array (optional) $attrs Other attributes in associative array.
	 *
	 * @return string
	 */
	public static function inputHtml($type, $name, $value='', array $attrs = array()) {
		return '<input type="'.$type.'" name="'.$name.'" value="'.$value.'" '.self::make_attrs($attrs).' />';
	}

	/**
	 * Returns form input hidden field.
	 *
	 * @param string $name Name of the field.
	 *
	 * @param string $value Value of the field.
	 */
	public static function inputHidden($name, $value) {
		return self::inputHtml("hidden", $name, so($value));
	}

	/**
	 * @todo change usage to inputHidden
	 * @deprecated
	 */
	public static function hiddenInput($name, $value) {
		return self::inputHtml("hidden", $name, $value);
	}

	/**
	 * Generates hidden input for action.
	 *
	 * @param string $name
	 *
	 * @return string
	 */
	public static function action($name) {
		return self::hiddenInput("action", $name);
	}

	/**
	 * Generates hidden input for update action.
	 *
	 * @return string
	 */
	public static function actionUpdate() {
		return self::action("update");
	}

	/**
	 * Generates hidden input for delete action.
	 *
	 * @return string
	 */
	public static function actionDelete() {
		return self::action("delete");
	}

	/**
	 * Generates hidden input for create action.
	 *
	 * @return string
	 */
	public static function actionCreate() {
		return self::action('create');
	}

	/**
	 * Generates options tags for select.
	 *
	 * @param array $options Associative array of options.
	 *
	 * @param string $selected (optional) Selected value.
	 *
	 * @return string
	 */
	public static function optionsA(array $options, $selected = NULL) {
		$out = '';
		foreach($options as $key => $val) {
			$sel = (NULL !== $selected && $val==$selected ? 'selected' : '');
			$out .= '<option '.$sel.' value="'.$val.'">'.$key.'</option>';
		}
		return $out;
	}

	/**
	 * Generates options tags for select.
	 *
	 * @param array $options Array of values for options.
	 *
	 * @param string $selected (optional) Selected value.
	 * 
	 * @return string
	 */
	public static function options(array $options, $selected = NULL) {
		$out = '';
		foreach($options as $opt) {
			$sel = ($opt==$selected ? 'selected' : '');
			$out .= "<option $sel value=\"$opt\">$opt</option>";
		}
		return $out;
	}
	
	/**
	 * Generates label.
	 *
	 * @param string $txt Text for label.
	 *
	 * @param string $for
	 *
	 * @param array $attributes (optional)
	 *
	 * @return string
	 */
	public static function label($txt, $for, array $attributes = array()) {
		return '<label for="'.$for.'" '.self::make_attrs($attributes).'>'.$txt.'</label>';
	}

	/**
	 * Outputs validation results if any.
	 *
	 * @param string $name
	 *
	 * @param string $field
	 *
	 * @return string|NULL
	 */
	public static function validation($name, $field) {
		if($v = hasV($name, $field)) {
			unset($_SESSION[$name][$field]);
			return '<span class="error">'.$v.'</span>';
		}
	}

	/**
	 * Generates select.
	 *
	 * @todo add id
	 *
	 * @param string $name
	 *
	 * @param string $options
	 *
	 * @param array $attributes (optional) Other attributes.
	 *
	 * @return string
	 */
	public static function select($name, $options, array $attributes = array()) {
		return '<select name="'.$name.'" '.self::make_attrs($attributes).'>'.$options.'</select>';
	}

	/**
	 * Generates "yes/no" select.
	 *
	 * @param string $name Name of the select.
	 *
	 * @param string $selected (optional) Selected value.
	 *
	 * @param array $attributes (optional) Other attributes.
	 *
	 * @return string
	 */
	public static function select_yesno($name, $selected = NULL, array $attributes = array()) {
		return self::select($name,
				self::optionsA(array(
					""    => '',
					"Yes" => 1,
					"No"  => 0), $selected), $attributes);
	}

	/**
	 * Output for date selection.
	 *
	 * @param integer $time Time.
	 *
	 * @param array $range (optional) Range for year.
	 *
	 * @param string $name (optional) Name for select.
	 *
	 * @return string
	 */
	public static function dateSel($time, $range = NULL, $name = 'date') {
		if(NULL === $range) $range = range(date("Y", $time) - 3, date("Y", $time) + 3);

		return '
			<select name="'.$name.'[Y]">
				'.self::options($range, date("Y", $time)).'
			</select> - 
			<select name="'.$name.'[m]">
				'.self::options(range(1,12), date("m", $time)).'
			</select> -
			<select name="'.$name.'[d]">
				'.self::options(range(1,31), date("d", $time)).'
			</select>';
	}

	/**
	 * Html for time selection.
	 *
	 * @param integer $time Time.
	 *
	 * @param string $name (optional) Name for select.
	 *
	 * @return string
	 */
	public static function timeSel($time, $name = 'time') {
		return '
			<select name="'.$time.'[H]">
				'.self::options(range(0,23), date("H", $time)).'
			</select> : 
			<select name="'.$time.'[i]">
				'.self::options(range(0,59), date("i", $time)).'
			</select>';
	}

	/**
	 * Remove keys which value is === '' or === NULL.
	 *
	 * @param array $data Associative array 'key' => 'value'.
	 *
	 * @return array
	 */
	public static function discard_en(array $data) {
		return array_filter($data,
			function($value) {
				return NULL !== $value && '' !== $value;
			});
	}

	/**
	 * Remove keys which value is ===''. Used for form data.
	 *
	 * @param array $data Associative array 'key' => 'value'.
	 *
	 * @return array
	 */
	public static function discard_empty(array $data) {
		return array_filter($data,
			function($value) {
				return '' !== $value;
			});
	}

	/**
	 * Generates full form field.
	 *
	 * @param Field $field
	 *
	 * @param mixed $o Object.
	 *
	 * @return string
	 */
	public static function ff(Field $field, $o) {
		$ret   = '';
		$str   = '';

		$name  = $field->name();
		$value = $o->$name;
		$on    = c2u(get_class($o));
		$iname = $on.'['.$name.']';
		$id    = $on."_".$name;
		$label = self::label(ucfirst(Language::beautify($name)), $id); 
		if($field->istext() || $field->isnumeric()) {	
			$str = self::inputHtml("text", $iname, $value, array("id" => $id));
		} else if($field->isboolean()) {
			$str = self::select_yesno($iname, $value, array("id" => $id));
		}

		$ret = $label.$str;

		return $ret;
	}

	/**
	 * Returns search field label and input for field (with min max).
	 *
	 * @param Field $field
	 *
	 * @param string $scl Searchable object classname.
	 *
	 * @param boolean $show_min_max TRUE when show min max fields for text input.
	 *
	 * @param Array $attrs (optional) Array of attributes.
	 *
	 * @return string
	 */
	public static function sfi($field, $scl, $show_min_max = FALSE, array $attrs = array()) {
		$name       = $field->name() ;
		$value      = $field->value();
		$searchable = c2u($scl);
		$min        = NULL;
		$max        = NULL;
		$attributes = $attrs;

		if($field->isnumeric()) {
			if(NULL !== $field->get_min() || NULL !== $field->get_max()) {
				$min   = $field->get_min();
				$max   = $field->get_max();
			} else if($show_min_max) {
				$min = $value;
				$max = $value;
			}
		}

		$srbl  = strtolower($searchable);
		$id    = $srbl.'_'.$name;
		$attributes['id'] = $id;

		$istr  = '';
		$label = self::label(ucfirst(Language::beautify($name)), $id); 

		if($field->isnumeric()) {
			if($show_min_max) {
				$attributes['size'] = 12;
				$istr = "Min ".self::inputHtml("text", sprintf("%s[%s_min]", $srbl,$name), $min, $attributes)." Max ".self::inputHtml("text", sprintf("%s[%s_max]", $srbl, $name), $max, $attributes);
			} else {
				$istr = self::inputHtml("text", sprintf("%s[%s]", $srbl, $name), $value, $attributes);
			}
		} else if($field->isboolean()) {
			$istr = self::select_yesno(sprintf("%s[%s]", $srbl, $name), $value, $attributes);
		} else {
			$istr = self::inputHtml(
				"text",
				sprintf("%s[%s]", $srbl, $name),
				$value, 
				$attributes);
		}

		return $label.$istr;
	}

}

