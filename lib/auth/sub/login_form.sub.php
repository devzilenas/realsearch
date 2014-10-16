
<form action="?user" method="post">
	<?= Form::action("login") ?>

	<?= Form::label(t("Login"), "user_login") ?>
	<?= Form::inputHtml("text", "user[login]", '', array('id' => "user_login", "class" => "zmogelis")) ?><br />

	<?= Form::label(t("Password"), "user_password") ?>
	<?= Form::inputHtml("password", "user[password]", '', array('id' => 'user_password', "class" => "password")) ?><br />
	<?= Form::submit(t("Login"), array("class" => "submit")) ?>
	<?= ' '.t("or").' '.Html::a("?user&new", t("Create your account")) ?>
</form>

