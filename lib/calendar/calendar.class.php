<?
include_once 'calendar_date.class.php';

/**
 * Calendar generator.
 *
 * @version
 */
class Calendar {

	/**
	 * Generate month days.
	 *
	 * @param string $date Date in format "Y-m-d".
	 *
	 * @return array
	 */
	private function month($date) {
		$tmp     = explode('-', $date, 3);
		$month  = $tmp[1]; 
		$year   = $tmp[0];
		$begin = mktime(0, 0, 0, $month, 1, $year);
		$end = date("t", $begin);
		$monthFirstDay = date("N", $begin);

		$calendar   = array();
		$weekDay = $monthFirstDay;
		$weekCounter = 0;
		for($i = 1; $i <= $end ; $i++) {
			$calendar[$weekCounter*7+$weekDay-1] = $i;
			$weekDay++;
			if ($weekDay > 7) {
				$weekDay = 1;
				$weekCounter++;
			}
		}
		return $calendar;
	}

	/**
	 * Make month's calendar.
	 *
	 * @param string $date Date in format "Y-m-d".
	 *
	 * @param string $callback (optional) Callback function to call for each day.
	 *
	 * @param array $flagDays  
	 *
	 * @return string Html output for calendar.
	 */
	public static function to_s($date, $callback='self::day', $flagDays = array()) {
		$out = "";
		$calendar = self::month($date);
		$tmp      = explode('-', $date, 3);
		$month    = $tmp[1]; 
		$year     = $tmp[0];
		$begin    = mktime(0, 0, 0, $month, 1, $year);
		$row      = "";
		$out = '
			<table style="font-size:12px">
			<tr>
			<td colspan=7 align="middle">
				<a href="?ondate='.date("Y-m-d",strtotime($date.'-1 month')).'"><img src="media/img/left8.png" /></a>
				<a href="?ondate='.date("Y-m-d").'">'.t("Today").'</a>
				<a href="?ondate='.date("Y-m-d",strtotime($date.'+1 month')).'"><img src="media/img/right8.png" /></a>
			</td>
			</tr>
			<tr>
			<td colspan=7 align=middle style="font-weight:bold">'.date("F Y", $begin).' m.</td>
		</tr>
		<tr>
			<td>'.t("Mon").'</td>
				<td>'.t("Tue").'</td>
				<td>'.t("Wed").'</td>
				<td>'.t("Thu").'</td>
				<td>'.t("Fri").'</td>
				<td>'.t("Sat").'</td>
				<td>'.t("Sun").'</td>
			</tr>';
		$j = 0;
		for($i = 0; $i <= 41; $i++) {
			if (!isset($calendar[$i])) {
				$val = "&nbsp;";
			} else {
				if (is_callable($callback)) {
					$val = call_user_func($callback, mktime(0, 0, 0, $month, $calendar[$i], $year), $flagDays);
				} else {
					$val = $calendar[$i];
				}
			}
			$row .= "<td>$val</td>";
			if ($j == 6) {
				$out .= "<tr>$row</tr>";
				$row  = '';
				$j    = 0;
			} else {
				$j++;
			}
		}
		$out .= '<tr><td colspan="7" align="middle">'.t("Today is").'<br />'.Dbobj::toDate().'</td></tr>';
		$out .= "</table>";
		return $out;
	}

	/**
	 * Generate a link for the date.
	 *
	 * @param integer $date
	 *
	 * @return string
	 */
	public static function day($date) {
		$d = date("j", $date);
		if (Dbobj::toDate($date) === Dbobj::toDate(mktime())) {
			$d = '<span class="today">'.$d.'</span>';
		}
		return '<a href="?ondate='.Dbobj::toDate($date).'">'.$d.'</a>';
	}

}

