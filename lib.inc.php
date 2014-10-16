<?

/**
 * Various functions that do not fit anywhere else.
 *
 * @version 0.1.1
 */

/**
 * Array find object with property or method value.
 *
 * @param array $arr
 *
 * @param string $pm Property or method name.
 *
 * @param mixed $value
 */
function afopm(array $arr, $pm, $value) {
	$ret = NULL;
	foreach($arr as $o) {
		if((property_exists($o, $pm) && $o->$pm == $value) 
		    || (isset($o->$pm) && $o->$pm == $value) ) {
			$ret = $o;
			break;
		} else if(method_exists($o, $pm) && $o->$pm() == $value) {
			$ret = $o;
			break;
		}
	}
	return $ret;
}

/**
 * @deprecated 
 * @todo remove
 */
function pluralize($str) { 
	return Language::pluralize($str);
}

/**
 * Gets values of objects in array by field.
 *
 * @param array $arr
 *
 * @param string $field Field name.
 *
 * @return array
 */
function arrayV(array $arr, $field) {
	$ret = array();
	foreach($arr as $e) {
		if(property_exists($e, $field) || isset($e->$field)) {
			$ret[] = $e->$field;
		} else if(method_exists($e, $field)) {
			$ret[] = $e->$field();
		}
	}
	return $ret;
}

/**
 * Translation function.
 *
 * @param string $str String to translate.
 *
 * @return string
 */
function t($str) {
	$str = trim($str);
	return class_exists('Language') ? Language::t($str) : $str;
}

/**
 * Safe output.
 *
 * @param string $str String to make safe.
 *
 * @return string
 */
function so($str) {
	return htmlspecialchars($str);
}

/**
 * Safe string or default value.
 *
 * @param string $str String to make safe.
 *
 * @param string $ifempty Default value.
 *
 * @return string
 */
function od($str, $ifempty) {
	return so(('' != $str) ? $str : $ifempty);
}

/**
 * Translate CamelCase to under_score.
 * Makes "someCamelCase" to "some_camel_case" and
 * "SomeCamelCase" to "some_camel_case".
 *
 * @param string $str Camel cased string to underscore.
 *
 * @param string $separator (optional) Separator to use.
 *
 * @return string
 */
function c2u($str, $separator = NULL) {
	if(NULL === $separator) {
		$separator = '_';
	}
	/** 
	 * look for [A-Z] that are not preceeded '(?<!)' by the start of the string symbol and that are followed by '(?=)' */
	return strtolower(preg_replace('#((?<!^)[A-Z](?=[a-z]))#',"$separator$1",$str));
}

/**
 * Shortname for Language::pluralize(c2u())
 *
 * @return string
 */
function c2up($str) {
	return Language::pluralize(c2u($str));
}

/**
 * Returns array without object with name()='id'.
 *
 * @param array $arr
 */
function noid(array $arr = array()) {
	return array_filter($arr, function($el){ return $el->name() != "id";});
}

/**
 * Remove keys which have no value.
 *
 * @param array $array
 *
 * @return array
 */
function ne(array $array) {
	return array_filter($array,
		function($value) {
			return !empty($value);
		}
	);
}

/**
 * Returns array with elements which name is not equal to given.
 *
 * @param array $arr
 *
 * @param array $names
 *
 * @return array
 */
function anne(array $arr, array $names) {
	$i   = 0;
	$ret = array();
	foreach($arr as $el) {
		if(!in_array($el->name(), $names)) {
			$ret[] = $el;
		}
	}
	return $ret;
}

/**
 * Filters out fields with given names
 *
 * @param Field[] $fields
 *
 * @param array $names
 *
 * @return Field[]
 */
function fan(array $fields, array $names) {
	$ret = array();
	foreach($fields as $field) {
		if(!in_array($field->name(), $names)) {
			$ret[] = $field;
		}
	}
	return $ret;
}

/**
 * Filters out fields that have empty value.
 *
 * @param mixed $o
 *
 * @param Field[] $fields
 *
 * @return Field[]
 */
function fef($o, array $fields) {
	$ret = array();
	foreach($fields as $field) {
		$name  = $field->name();
		$value = $o->$name;
		if(!empty($value)) {
			$ret[] = $field;
		}
	}
	return $ret;
}

/**
 * Echoes string along with line number.
 *
 * @param string $what
 *
 * @return void
 */
function echol($what) {
	echo "Line:".__LINE__."@:".__FILE__."@";
	echo $what;
}

/**
 * Converst string: each object identificator converted to link to view that object.
 */
function convert($str) {
	global $DBOBJS;
	$classes = $DBOBJS;
	return preg_replace_callback('@('.join('|', $classes).')#([\d]+)@',
		function($matches) {
			return sprintf('<a href="?%2$s=%3$d&view">%1$s</a>', $matches[0], c2u($matches[1]), $matches[2]);
		}, $str); 
}

