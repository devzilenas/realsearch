<?
/**
 * Html for objects set.
 *
 * @version 0.1.3
 */
class ObjSetHtml {

	/**
	 * Generate object set header.
	 *
	 * @param ObjSet $list Object set of items.
	 *
	 * @param string $url Url base.
	 *
	 * @return string
	 */
	public static function makeListHeader(ObjSet $list, $url) {
		$links = self::getListLinks($list);
		$out   = array();
		$lprev = t("Previous").Html::img("media/img/left8.png", t('Previous'));
		$lnext = Html::img("media/img/right8.png", t("Next")).t("Next");
		$i    = 0;
		$cp   = $list->loadedPage();
		$out[] = ($list->hasPrev()) ? Html::a($url.'&page='.$list->prevI(), t('Previous')) : t('Previous');
		foreach($links as $link) {
			$out[] = ($link == $cp ? sprintf('<span class="larger">%d</span>', $link+1) : Html::a($url.'&page='.$link, $link+1));
			$i++;
		}
		$out[] = ($list->hasNext()) ? Html::a($url.'&page='.$list->nextI(), t('Next')) : t("Next");
		return "<p>".join('&nbsp;',$out).'</p>';
	}

	/**
	 * Makes next,prev links for objects set.
	 *
	 * @param ObjSet $list
	 *
	 * @return array
	 */
	private static function getListLinks($list) {
		/**
		 * Make list pages. 
		 */
		$cp    = $list->loadedPage();
		$lp    = $list->totalPages();
		$links = array();

		array_push($links, $cp);

		$i = $cp;
		$j = 0;
		while($i+1 < $lp && $j < 4) {
			$i++; $j++;
			array_push($links, $i);
		}

		$i = $cp;
		$j = 4;
		while($i-1 >= 0 && $j > 0 ) {
			$i--; $j--;
			array_unshift($links,$i);
		}
		
		return $links;

	}

}

