<form method="post" action="?rpp">
	<?= Form::action("set_reals_per_page"); ?>

	<?= Form::label("Reals per page", "rpp") ?>
	<?= Form::select("reals_per_page", Form::options(array(10, 20, 50, 200), Session::g0d('reals_per_page', Config::REALS_PER_PAGE)), array("id" => "rpp")) ?>
	<?= Form::submit("Set") ?>
</form>
