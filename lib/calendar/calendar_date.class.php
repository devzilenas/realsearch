<?

/**
 * Date manipulation.
 *
 * @version 0.1.1
 */
class CalendarDate  {

	/**
	 * Returns time of month start.
	 *
	 * @param integer $year
	 *
	 * @param integer $month
	 *
	 * @return integer 
	 */
	public static function monthBegin($year, $month) {
		return mktime(0, 0, 0, $month, 1, $year);
	}

	/**
	 * Returns time of month end.
	 *
	 * @param integer $year
	 *
	 * @param integer $month
	 *
	 * @return integer 
	 */
	public static function monthEnd($year, $month) {
		return mktime(0, 0, 0, $month, date("t", self::monthBegin($year, $month)), $year);
	}

	/**
	 * Returns array with begin and end dates for date.
	 * Accepts date in format "Y-m-d"; returns array: arr[0] = month begin date; arr[1] = month end date
	 *
	 * @param $date
	 *
	 * @return array
	 */
	public static function beginEnd($date) {
		$tmp   = explode('-', $date, 3);
		$year  = $tmp[0];
		$month = $tmp[1]; 
		$begin = mktime(0, 0, 0, $month, 1, $year);
		$end   = mktime(0, 0, 0, $month, date("t", $begin), $year);
		return array($begin, $end);
	}

	/**
	 * Explodes date string by "-".
	 *
	 * @param string $date
	 *
	 * @return array
	 */
	private static function e($date) {
		return explode('-', $date, 3);
	}

	/**
	 * Tells if string is valid date.
	 *
	 * @param string $date
	 *
	 * @return boolean
	 */
	public static function isValid($date) { 
		$tmp = self::e($date);
		return count($tmp)==3 ? checkdate($tmp[1], $tmp[2], $tmp[0]) : FALSE;
	}

	/**
	 * Returns string if is valid date.
	 *
	 * @param string $str Date string to check.
	 *
	 * @return string
	 */
	public static function sanitize($str) {
		return self::isValid($str) ? $str : FALSE;
	}
}

