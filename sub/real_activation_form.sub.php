<form method="post" action="?real=<?= $real->id ?>">
	<?= Form::action("toggle_activation") ?>

<b>Real is <?= $real->is_active ? t("active") : t("not active")?></b>

<? if($real->is_active) { ?>
	<?= Form::submit("Deactivate") ?>
<? } else { ?>
	<?= Form::submit("Activate") ?>
<? } ?>

</form>
