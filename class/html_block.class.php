<?
/**
 * Generates various HTML blocks.
 */
class HtmlBlock {

	/**
	 * Converts associative array to string.
	 *
	 * @todo Extract method to the LinkBuilder class.
	 *
	 * @param array a
	 */
	private static function a2a(array $a) {
		$rets = array();
		foreach($a as $k => $v) {
			$s = is_numeric($k) ? $v : "$k=$v";
			$rets[] = $s;
		}
		return join('&', $rets);
	}

	/**
	 * Generates search agents list.
	 *
	 * @param mixed $filter
	 *
	 * @return void
	 */
	public static function search_agents_list($filter, $href) {
		$search_agents = new ObjSet('SearchAgent', $filter, Request::get0('page'));

		echo ObjSetHtml::makeListHeader($search_agents, self::a2a($href));
		echo self::search_agents($search_agents);
	}

	/**
	 * Makes list of search agents.
	 * 
	 * @param ObjSet $sas
	 *
	 * @return string
	 */ 
	private static function search_agents(ObjSet $sas) {
		$sas->loadNextPage();
		$saa = array();

		while($sa = $sas->getNextObj()) {
			$activation_form = sprintf('
				<form class="inl" method="post" action="?search_agent=%d">
				%s
				%s
				</form>',
				$sa->id, Form::action("toggle_is_active"), Form::submit(t($sa->is_active ? "Deactivate" : "Activate"))
			);
			$saa[] = '<li>'.$activation_form.' '.Html::a("?search_agent=$sa->id&view", $sa).'</li>';
		}
		return '<ul>'.join('', $saa).'</ul>';
	}

	/**
	 * Generates reals list with headers.
	 *
	 * @param mixed $filter (optional) Filter.
	 *
	 * @return void
	 */
	public static function realsList($filter = NULL, $href) {
		if(NULL == $filter) {
			$filter = Real::newFilter(array("Real" => "*"));
			/** Show active reals */
			$filter->setWhere(array("Real.is_active" => 1));
		}
		$rpp   = Session::g0d('reals_per_page', Config::REALS_PER_PAGE);
		$reals = new ObjSet("Real", $filter, Request::get0('page'), $rpp);
		include 'sub/reals_per_page.sub.php';
		echo ObjSetHtml::makeListHeader($reals, self::a2a($href)); 
		echo HtmlBlock::reals($reals);
	}

	/**
	 * Makes list.
	 *
	 * @param ObjSet $reals
	 *
	 * @return string
	 */
	private static function reals(ObjSet $reals) {
		$reals->loadNextPage();
		/** Inactive reals */
		$irs = array();
		/** Active reals */
		$ars  = array();
		$arsi = 0;
		$irsi = 0;
		$currency = (Login::is_logged_in()) ? new Currency(Login::user()->currency) : Config::base_currency();
		while($real = $reals->getNextObj()) {
			$picstr = '';
			if($picture = Picture::get_main_for($real)) { 
				$picstr = Html::ai("?real=$real->id&view", $picture->thumbnail("small")->src(), $picture->filename);
			} else {
				$picstr = Html::ai("?real=$real->id&view",PictureManager::thumbnail_cornered(), "real item");
			}
			/** Build html for list item */
			$price = $real->asm('price', $currency);

			$rii   = array();
			$rii[] = t("Location:").' <b>'.$real->full_address_str().'</b>';
			if(NULL !== $real->year_of_construction) {
				$rii[] = t("Year:").' <b>'.sprintf(Real::fformat('year_of_construction'), $real->year_of_construction).'</b>';
			}
			$rii[] = t("Area:").' <b>'.sprintf(Real::fformat('rooms')." rooms, ".Real::fformat('area')." m&sup2;", $real->rooms, $real->area).'</b>';
			$rii[] = t("Price:").' <b>'.$price.'</b>';
			$s   = '<span class="real_item"><span class="real_item_picture">'.$picstr.'<a href="?real='.$real->id.'&view"></a></span><span class="real_item_info">'.join('<br />', $rii).'</span></span>';
			
			if($real->is_active) {
				$arsi++;
				$ars[] = $s;
				if($arsi % 2 == 0) {
					$ars[] = Html::clears();
				}
			} else {
				$irsi++;
				$irs[] = $s; 
				if($irsi % 2 == 0) {
					$irs[] = Html::clears();
				}
			}
		}
		if(!empty($ars)) {
			echo '<span class="real_items">'.join('', $ars).'</span>';
		}

		if(!empty($irs)) {
			echo Html::clears();
			echo '<p>Not active reals:</p>';
			echo '<span class="real_items">'.join('', $irs).'</span>';
		}
		echo Html::clears();
	}

	/**
	 * Returns HTML for real price property.
	 *
	 * @param Real $real
	 *
	 * @param Field $field
	 *
	 * @param Currency $currency
	 *
	 * @return string|NULL
	 */
	public static function real_price_property(Real $real, Field $field, Currency $currency) {
		$name  = $field->name();
		$value = $real->$name;
		return empty($value) ? NULL : '<tr><td>'.ucfirst(Language::beautify($name)).'<td>'.so($real->asm($name, $currency));
	}
	
	/**
	 * Returns HTML for real property.
	 *
	 * @param Real $real
	 *
	 * @param Field $field 
	 *
	 * @return string|NULL
	 */
	public static function real_property(Real $real, Field $field) { 
		$name     = $field->name(); 
		$out_name = NULL === $field->out_name() ? $name : $field->out_name();
		$value    = $real->$name;
		$out      = NULL;
		if(NULL !== $value) {
			if($name == 'touched_on') {
				$value = Dbobj::toDateTime($value);
				$fout  = $value;
			} else {
				$fout = Html::ot($value, $field);
			}
			$out = '<tr><td>'.ucfirst(Language::beautify($out_name)).'<td>'.so($fout);
		}
		return $out;
	}

	/**
	 * Generates link for real search.
	 *
	 * @return string
	 */
	public static function link_for_real_search() {
		$ret = '';
	}
}

