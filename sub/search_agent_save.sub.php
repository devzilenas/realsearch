<? if(!isset($_GET['search_agent'])) { ?>

<form method="post" action="?search_agent">
	<?= Form::action("save") ?>
<?
if(isset($_GET['searchable'])) {
	$searchable = $_GET['searchable'];
	if(isset($_GET[c2u($searchable)]) && is_array($_GET[c2u($searchable)])) {

		echo Form::inputHidden('searchable', $searchable);

		$sda = Form::discard_empty($_GET[c2u($searchable)]);

		foreach($sda as $sdk => $sdv) {
			echo Form::inputHidden(c2u($searchable).'['.$sdk.']', $sdv);
		}
	}
}
?>
	<?= Form::submit(t("Save current search as search agent")) ?>
</form>

<? } else { ?>
<a href="?search_agent=<?= $_GET['search_agent']; ?>&edit">Edit search agent</a>
<? } ?>

