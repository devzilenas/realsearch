<?
$user = Login::user();
if(!ApiManager::is_valid_api_key($user->api_key)) { ?>
<form method="post" action="?api_key_request">
	<?= Form::action("api_key_request") ?>
	<?= Form::submit("Request api key") ?>
</form>
<? } else { ?>
<p>Your api key: <?= so(Login::user()->api_key) ?></p>
<? } ?>

To learn more <a href="?help_api">about API</a>.
