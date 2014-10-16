<? $user = Login::user(); ?>
<form method="post" action="?tz">
	<?= Form::action("time_zone_set") ?>
	<?= Form::label(t("Time zone"), "tz_id") ?>
	<?= Form::select("time_zone", Form::options(timezone_identifiers_list(DateTimeZone::ALL), $user->time_zone), array("id" => "tz_id")) ?>
	<?= Form::submit(t("Set")) ?>
</form>

