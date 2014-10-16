<?
if(empty($_REQUEST)) {
	$filter = NULL;
	$a      = array("?reals", "list");
	/** If my reals */
	if(Login::is_logged_in() && Req::is_my_reals()) {
		$filter = Real::newFilter();
		$filter->setWhere(array(
			"Real.user_id" => Login::logged_id()));
		$filter->setOrderBy("is_active DESC");
		$a[] = "my";
	}
	echo HtmlBlock::realsList($filter, $a);
}
?>

