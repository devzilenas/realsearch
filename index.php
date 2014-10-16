<?
/**
 * The Real Search.
 *
 * @author Marius Žilėnas <mzilenas@gmail.com>
 * @copyright 2013 Marius Žilėnas
 *
 * @version 0.2.29
 */
include 'includes.php';
DB::connect();
session_start();

Runner::on_each_call();

Req::process();
?>

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" type="text/css" href="css/style.css">
	<? if(Config::is_dropdown_enabled()) { ?>
		<script type="text/javascript" src="javascript/lib.js"></script>
	<? } ?>
		<title>Real Search</title>
	</head>
	<body>
<?= LoggerHtmlBlock::messages() ?>

<p class="meniu inl">
<a href="?reals&list">Reals</a> 
<a href="?search">Search</a>

<? if(!Login::is_logged_in()) { ?>
	<a href="?login">Login</a>
<? } ?>

<? if(Login::is_logged_in()) { ?>
	<a href="?real&new">New real</a>
<? }?>

<a href="?help">Help</a>
<a href="?plans">Plans</a>

<? if(Login::is_logged_in()) { ?>
	<span class="small">You are logged in as: <a href="?account&my"><?= so(Login::user()->login) ?></a></span>
<? } ?>
</p>
<?= Html::clears() ?>

<? include 'sub/default.sub.php';     ?>

<? include 'auth/sub/login.sub.php'   ?>
<? include 'sub/real.sub.php'         ?>
<? include 'sub/contact_info.sub.php' ?>
<? include 'sub/search.sub.php'       ?>
<? include 'sub/picture.sub.php'      ?>
<? include 'sub/help.sub.php'         ?>
<? include 'sub/help_api.sub.php'     ?>
<? include 'sub/plans.sub.php'        ?>
<? include 'sub/search_agent.sub.php' ?>
<? include 'auth/sub/account.sub.php' ?>
<? include 'sub/wallet.sub.php'       ?>

<?= Html::clears() ?>

<p>2013 &copy; <a href="mailto:mzilenas@gmail.com">Marius Žilėnas</a></p>

</body>
</html>

