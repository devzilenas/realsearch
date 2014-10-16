
<? $user = Login::user(); ?>
<form method="post" action="?tz">
	<?= Form::action("currency_set") ?>
	<?= Form::label(t("Currency"), "currency_id") ?>
	<?= Form::select("currency", Form::options(arrayV(ExchangeRator::currencies(), "name"), $user->currency), array("id" => "currency_id")) ?>
	<?= Form::submit(t("Set")) ?>
</form>

