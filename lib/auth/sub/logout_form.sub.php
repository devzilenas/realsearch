<form method="post" action="?logout" class="inl">
	<?= Form::action("logout") ?>
	<?= Form::label(t("You can logout"), "logout") ?>
	<?= Form::submit('Logout', array("id" => "logout", "class" => "inl")) ?>
</form>

