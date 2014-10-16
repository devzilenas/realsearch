<? if(Login::is_logged_in() && RequestAuth::is_account_my()) { ?>
	<? include 'auth/sub/logout_form.sub.php' ?><br />

<h3>Contact information: </h3>
<p>
<a href="?contact_info&view"><img src="media/img/disc.png" />View</a> or <a href="?contact_info&edit"><img src="media/img/edit.png" />Edit</a></p>

<h3>Api</h3>
<? include_once 'auth/sub/api_key.sub.php'; ?>

<h3>Time zone</h3>
<? include_once 'auth/sub/timezones_form.sub.php' ?>

<h3>Money</h3>
<? include_once 'auth/sub/monia.sub.php' ?>

<h3>Your wallet</h3>
<?= Html::a("?wallet&view&my", t("View wallet")) ?>

<? } ?>

