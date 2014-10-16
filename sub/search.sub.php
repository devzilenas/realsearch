<? if(Req::is_search()) { ?>

<? if(Login::is_logged_in()) { ?>
<p>
	<a href="?reals&list&my">My reals</a> <a href="?search_agents&list&my">My search agents</a>
</p>
<? } ?>
<? $fields = fan(Real::searchable_fields(), array('touched_on'))?>
<form method="get" action="">
	<?= Form::inputHidden("real_search", '') ?>
	<input type="hidden" name="searchable" value="Real">
<?
$fields_groups = Real::fields_groups();
foreach($fields_groups as $fg) {
	$provider    = $fg['provider'];
	$group_title = $fg['title'];
	$rfds     = Real::$provider();
	$istrs    = array_map(
		function ($el) {
			return Form::sfi($el, 'Real', TRUE);
		}, $rfds);
	$fields = anne($fields, arrayV($rfds, 'name'));
	echo '<span class="larger bl">'.so($group_title).'</span>';
	echo join('<br />', $istrs);
}
?>
<?
$istrs = array();
foreach($fields as $field) {
	$istrs[] = Form::sfi($field, 'Real', TRUE);
}
if(!empty($istrs)) { ?>
<span class="larger bl">Other information</span>
<?= join('<br />', $istrs) ?>
<? } ?>

	<br/>
	<input type="submit" value="Search!" />
</form>
<? } ?>

