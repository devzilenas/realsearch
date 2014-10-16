<? 
if(Request::isNew('ContactInfo')) {
	$ci = new ContactInfo();
	Access::owns(Login::user(), $ci);
?>
<form method="post" action="?contact_info">
	<?= Form::action("create") ?>
	<? if(isset($_GET['attached_to'], $_GET['attached_id'])) { ?>
		<?= Form::inputHidden("attached_to", $_GET['attached_to']) ?>
		<?= Form::inputHidden("attached_id", $_GET['attached_id']) ?>
	<? } ?> 
<? include_once 'sub/contact_info_form.sub.php' ?>
<?= Form::submit("Save") ?>
</form>
<? } ?>

<? if(Request::isView('ContactInfo')) { 
	$fields = ContactInfo::editable_fields();
	$ci     = Request::out('contact_info');
?>
<? if(Access::is_owner(Login::user(), $ci)) { ?> 
<a href="?contact_info&edit"><img src="media/img/edit.png" />Edit</a>
<? } ?>
<table>
	<thead>
	<tr><th>Contact info<th>
	</thead>
	<tbody>
	<? foreach($fields as $field) {
		$name  = $field->name();
		$value = $ci->$name;
		if(!empty($value)) { ?>
			<tr><td><?= ucfirst(Language::beautify($name)) ?><td><?= so(Html::ot($ci->$name, $field)) ?>
		<? } ?>
	<? } ?>
	</tbody> 
</table>

<? } ?>

<? if(Request::isEdit('ContactInfo')) { 
	$ci = Req::out('contact_info');
?>
<form method="post" action="?contact_info=<?=$ci->id?>">
<?= Form::action("update") ?>

<? include_once 'sub/contact_info_form.sub.php' ?>

<?= Form::submit("Save") ?> <?= Html::a("?contact_info&view", t("Cancel")) ?></a>
<? } ?>

