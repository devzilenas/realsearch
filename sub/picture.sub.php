<?
if(Req::isNew('Picture')) {
	$picture = new Picture();
?>

<form enctype="multipart/form-data" method="post" action="?pictures">
	<?= Form::action("create") ?>
<? if(isset($_GET['attached_to'], $_GET['attached_id'])) { ?>
	<?= Form::inputHidden('attached_to', $_GET['attached_to']) ?>
	<?= Form::inputHidden('attached_id', $_GET['attached_id']) ?>
<? } ?>
	<b><?= t("Picture") ?></b>
	<input type="file" name="picture" accept="image/jpeg" /><br />

	<?= Form::validation("picture_caption", "caption") ?>
	<?= Form::label("Caption (optional)", "picture_caption") ?>
	<?= Form::inputHtml("text", "picture[caption]", $picture->caption, array('id' => "picture_caption")) ?><br />

	<?= Form::submit(t("Upload")) ?> <?= Html::a('?'.c2u($_GET['attached_to'])."=".$_GET['attached_id']."&view", "Cancel") ?>
</form>
<? } ?>

<?
if(Req::isView('Picture')) {
	$picture = Picture::load($_GET['picture']);
?>
<p>
<b><?= t("Picture").":" ?></b>

<?= Html::img($picture->src(), '') ?>

</p>
<? } ?>

<?
if(Req::isEdit('Picture')) {
	$picture = Picture::load($_GET['picture']);
?>

<p>
<a href="?<?=c2u($picture->attached_to)?>=<?=$picture->attached_id?>&edit">Back</a>
</p>
<div>
<form method="post" action="?picture=<?= $picture->id ?>">
	<?= Form::action("update") ?>

	<?= Form::validation("picture_caption", "caption") ?>
	<?= Form::label("Caption", "picture_caption") ?>
	<?= Form::inputHtml("text", "picture[caption]", $picture->caption, array('id' => "picture_caption")) ?>

	<?= Form::submit(t("Update")) ?>
</form>

<?= Html::img($picture->src(), '') ?>
</div> 

<div>
<table>
	<caption>Thumbnails</caption>
	<tbody>
<? foreach($picture->thumbnails() as $thumb) { ?>
		<tr><td><?= $thumb->name() ?><td><?= Html::img($thumb->src(), '') ?>
<? } ?>
	</tbody>
</table>

</div>

<b>Delete picture</b>
<form method="post" action="?picture=<?= $picture->id ?>">
	<?= Form::action("delete") ?>
	<input type="submit" value="<?= t("Delete") ?>" />
</form>

<? } ?>

