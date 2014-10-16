<?

/**
 * HTML base tags generator.
 *
 * @version 0.0.1
 */
class Html {
	/**
	 * Makes link with image.
	 *
	 * @param string $href
	 *
	 * @param string $src Image source.
	 *
	 * @param string $alt Image alternative text.
	 *
	 * @param string $text (optional) Link text.
	 *
	 * @return string
	 */
	public static function ai($href, $src, $alt, $text = '') {
		return self::a($href, self::img($src, $alt).$text);
	}

	/**
	 * Makes link to base.
	 *
	 * @param string $contents
	 */
	public static function ab($contents) {
		return self::a('', $contents);
	}

	/**
	 * Makes link.
	 *
	 * @param string $href
	 *
	 * @param string $contents
	 */
	public static function a($href, $contents, $attributes = array()) { 
		return '<a href="'.Request::base().'/'.$href.'" '.Form::make_attrs($attributes).'>'.$contents.'</a>';
	}

	/**
	 * Returns string alt="$value" title="$value"
	 *
	 * @param string $value
	 */
	public static function alt_title($value) {
		return sprintf('alt="%1$s" title="%1$s"', $value);
	}

	/**
	 * Returns img html string.
	 *
	 * @param string src
	 *
	 * @param string $alt (optional) Alt.
	 *
	 * @param array $attrs (optional) Attributes.
	 *
	 * @return string
	 */
	public static function img($src, $alt = '', $attrs = array()) {
		return sprintf('<img '.self::alt_title($alt).' src="%s"  '.Form::make_attrs($attrs).' />', $src);
	}

	/**
	 * Outputs field data according to fields type.
	 *
	 * @param mixed $value
	 *
	 * @param Field $field
	 *
	 * @return string
	 */
	public static function ot($value, Field $field) {
		$ret = '';
		if(NULL === $value) {
			$ret = '';
	   	} else if($field->isnumeric()) {
			$ret = sprintf($field->format(), $value);
		} else if($field->isboolean()) {
			$ret = $value ? "Yes" : "No";
		} else if($field->istext()) {
			$ret = $value;
		}
		return $ret;
	}

	/**
	 * Returns clearing element: <div class="clear">&nbsp;</div>
	 *
	 * @return string
	 */
	public static function clears() {
		return '<div class="clear">&nbsp;</div>';
	}

}

