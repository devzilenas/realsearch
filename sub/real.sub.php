<? 
if(Req::isNew('Real')) { 
	$real = new Real();
?>

<h2>New real</h2>
<form method="post" action="?reals&new"> 
	<?= Form::action("create") ?> 
	<? include 'sub/real_form.sub.php'; ?>
	<?= Form::submit("Ok") ?> <?= Html::a('?reals&list', 'Cancel') ?>
</form>

<? } ?>

<?
	/** todo add Req::process_view_real */
if(Req::isView('Real')) { 
	$real     = Req::out('real');
	$pictures = Req::out('pictures');
	$fields   = Real::editable_fields(); 

	/** current picture */
	$cp = NULL;
	if(isset($_GET['cp']) && $pic = Picture::load($_GET['cp'])) {
		if($pic->is_attached_to($real)) {
			$cp = $pic;
		}
	}

	if($cp === NULL) {
		$cp = Picture::get_main_for($real); //set to main picture
	}

?>
<? if(Login::is_logged_in() && Access::can_edit(Login::user(), $real)) { ?>
	<a href="?real=<?= $real->id ?>&edit">Edit</a> 
	<a href="?picture&new&attached_to=Real&attached_id=<?= $real->id ?>">Add picture</a>
<? } ?>
	<a href="?reals&list">Back</a><? /** todo add back to page */ ?>

<? if(!empty($pictures)) { ?>
<? 
	/** find prev, next pictures */
	$prev = NULL;
	$next = NULL;
	$i    = 0;
	foreach($pictures as $p) { 
		if($p->id == $cp->id) {
			if($i > 0) {
				$prev = $pictures[$i-1];
			}
			if($i+1 < count($pictures)) {
				$next = $pictures[$i+1];
			}
			break;
		}
		$i++;
	}
?>
<p>
<? if($prev) { ?>
	<a href="?real=<?= "$real->id&view&cp=$prev->id" ?>"><img src="media/img/left16.png" /><?= t('Previous picture')?></a> 
<? } ?>
<? if($next) { ?>
	<a href="?real=<?= "$real->id&view&cp=$next->id" ?>"><?= t('Next picture')?><img src="media/img/right16.png" /></a>
<? } ?>

<div class="pictures"> 
	<span><?=so($cp->caption)?></span><?= Html::clears() ?>
	<span class="current_picture">
		<?= Html::img($cp->thumbnail("xx-large")->src(), $cp->original_name) ?>
	</span>
	<span class="pictures_thumbnails">
		<ul>
		<? foreach($pictures as $picture) { ?> 
			<li class="picture_thumbnail"><?= Html::ai("?real=$real->id&view&cp=$picture->id", $picture->thumbnail("small")->src(), $picture->original_name) ?></li>
		<? } ?>
		</ul>
	</span>
</div>

</p>
<? } ?>
<?= Html::clears() ?>

<? include_once 'sub/real_description.sub.php'; ?>

<? if($real->has_location_info()) { ?>
<h2><?=t("Location on the map")?></h2><br />
<?= MapsHtml::img_by_address($real->full_address_str()) ?>
<? } ?>

<? include 'sub/real_contact.sub.php' ?>

<? } ?>

<?
if(Req::isEdit('real')) {
	$real = Serializator::get_obj($_GET['real'], 'Real');
	$pictures = Picture::pictures("Real", $real->id);
?>

<h2>Edit real</h2>
<a href="?real=<?=$real->id?>&view">Back</a>
<? include 'sub/real_activation_form.sub.php' ?>
<? if(!empty($pictures)) {
	$i = 0;
?>
<p>
<b>Pictures</b><br />
<span class="small">First picture here is the main picture</span><br />
<? 
foreach($pictures as $picture) {
  $i++;
?>
<div class="inl" style="float:left"><span><?= $i ?>.
	<form method="post" action="?picture=<?= $picture->id ?>" class="inl">
		<?= Form::action("rank_change") ?>
		<?= Form::hiddenInput("direction", "up") ?>
		<?= Form::submit("&lt;") ?>
	</form>
	<form method="post" action="?picture=<?= $picture->id ?>" class="inl">
		<?= Form::action("rank_change") ?>
		<?= Form::hiddenInput("direction", "down") ?>
		<?= Form::submit("&gt;") ?>
	</form>
	<?= Html::ai("?picture=$picture->id&edit", "media/img/edit.png", "Edit picture information", t("edit")) ?>
	</span><br />
	<span>
	<?= Html::ai("?picture=$picture->id&edit", $picture->thumbnail("large")->src(), $picture->filename) ?>
	</span>
</div>
<? } ?>
</p>
<? } ?>
<?= Html::clears() ?>

<form method="post" action="?real=<?= $real->id ?>"> 
	<?= Form::action("update") ?>
	<? include 'sub/real_form.sub.php'; ?>
	<?= Form::submit("Update") ?> <?= Html::a("?real=$real->id&view", t("Cancel")) ?>
</form>

<? } ?>

<? 
if(Req::isList('Real')) { ?>
<?
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

<? if(Req::is_real_search()) { 
	if(Login::is_logged_in()) {
		include_once 'sub/search_agent_save.sub.php';
	} 

	$a = array("?reals", "list");
	echo HtmlBlock::realsList(Req::out('filter'), Req::out('search_strs'));
}
?>

