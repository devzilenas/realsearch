<?
/** Search agent search edit */
if(Req::is_search_agent_edit_search_values()) {
	$sa            = Req::out('search_agent') ;
	$svsf          = Req::out('search_values');

	$fields_groups = Real::fields_groups()  ;
	$fields        = Real::searchable_fields();

?>
<form method="post" action="?search_agent=<?=$sa->id ?>">
	<?= Form::action("update_search_values") ?>
	<?= Form::inputHidden("searchable", 'Real') ?>

<?
foreach($fields_groups as $fg) {
	$provider    = $fg['provider'];
	$group_title = $fg['title'];
	$rfds        = Real::$provider();
	$istrs       = array();
	foreach($rfds as $rfd) {
		if($svf = afopm($svsf, 'name', $rfd->name())) {
			$rfd = $svf;
		}
		$istrs[] = Form::sfi($rfd, 'SearchValue', TRUE);
	}
	echo '<span class="larger bl">'.so($group_title).'</span>';
	echo join('<br />', $istrs);
	$fields = anne($fields, arrayV($rfds, 'name'));
}
?>
<?= Html::clears() ?>
	<?= Form::submit(t("Update!")) ?> <?= Html::a("?search_agent=$sa->id&view", t("Cancel")) ?>
</form> 
<? } ?>

<? if(Req::isView('SearchAgent') && $sa = Req::out('search_agent')) { 
	$fields     = $sa->load_values_as_fields_for('Real');
	$searchable = $sa->searchable;
?>
	<?= Html::a("?search_agent=$sa->id&edit", t("Edit")) ?> or <?= Html::a("?search_agent=$sa->id&edit_sv", "Edit search values") ?> <?= Html::a("?search_agents&list&my", t("Back")) ?>

	<form method="post" action="?search_agent=<?= $sa->id ?>">
		<?= Form::action("delete") ?>
		<?= Form::submit(t("Delete")) ?>
	<form>

	<p>Search agent: <b><?= so($sa->name) ?></b> <?= Html::a("?real_search&searchable=Real&search_agent=$sa->id&".$sa->make_search('Real'), "search", array("target" => "_blank")) ?>

<table>
<thead>
<tr><th>Field<th>Search value
</thead>
<tbody>
<? foreach($fields as $field) { ?>
<? if(NULL !== $field->get_min() || NULL !== $field->get_max()) {
	if(NULL !== $field->get_min()) { ?>
		<tr><td><?= so(ucfirst(Language::beautify($field->name().'_min'))) ?>
			<td><?= so(Html::ot($field->get_min(), $field)) ?>
	<? } 
	if(NULL !== $field->get_max()) { ?>
		<tr><td><?= so(ucfirst(Language::beautify($field->name().'_max'))) ?>
			<td><?= so(Html::ot($field->get_max(), $field)) ?>
	<? } ?>
<? } else if(NULL !== $field->value()) { ?>
		<tr><td><?= so(ucfirst(Language::beautify($field->name()))) ?>
			<td><?= so(Html::ot($field->value(), $field)) ?>
<? } ?>
<? } ?>

</tbody>
</table>
<? } ?>

<? if(Req::isEdit('SearchAgent')) { 
	$sa = Req::out('search_agent');
?>
<form method="post" action="?search_agent=<?= $sa->id ?>">
	<?= Form::action("update") ?>

	<?= Form::validation("search_agent", "name") ?>
	<?= Form::label("Name", "search_agent_name") ?>
	<?= Form::inputHtml("text", "search_agent[name]", $sa->name, array("id" => "search_agent_name")) ?> <br />

	<?= Form::submit(t("Update")) ?> <?= Html::a("?search_agent=$sa->id&view", t("Cancel")) ?>
</form>
<? } ?>

<?
if(Req::is_my_search_agents()) {
	$a = array('?search_agents', 'list', 'my');
	$filter = Req::out('filter');
	$active     = SearchAgent::agents_active_for(Login::user()->id);
?>
	<p>Active agents : <?= $active ?> </p>
<?
	echo HtmlBlock::search_agents_list($filter, $a);
}
?>
