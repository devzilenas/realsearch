<?
if(Req::is_view_my_wallet()) {
	$w = Req::out('wallet');
?>

<p>Wallet amount left: <span class="large"><?= $w->left() ?></span></p>

<? $wls = $w->last_lines(); ?>
<? include 'sub/wallet_last_lines.sub.php'; ?>

<? } ?>

