<? if(Req::is_plans()) { ?>
<p>
</p>
<table>
	<caption>Plans</caption>
	<thead>
		<tr class="ptr"><th>&nbsp;<th>Basic<th>Better<th>Best<th>API
	</thead>
	<tbody>
<? $currency = (Login::is_logged_in()) ? new Currency(Login::user()->currency) : Config::base_currency();
?>
		<tr>
			<td class="cgD">You pay
			<td class="cg1">Nothing
			<td class="cg2"><?= Config::plan_price_for("BETTER", $currency) ?>/month
			<td class="cg3"><?= Config::plan_price_for("BEST", $currency) ?>/month
			<td class="cg4">Nothing
		<tr>
			<td class="cgD">Has search
			<td class="cg1">+
			<td class="cg2">+
			<td class="cg3">+
			<td class="cg4">+
		<tr>
			<td class="cgD">Has e-mail notification
			<td class="cg1">N/A
			<td class="cg2">+
			<td class="cg3">+
			<td class="cg4">N/A
		<tr>
			<td class="cgD">Has search agents
			<td class="cg1">N/A
			<td class="cg2">+
			<td class="cg3">+
			<td class="cg4">N/A
		<tr>
			<td class="cgD">Active search agents
			<td class="cg1">N/A
			<td class="cg2">1
			<td class="cg3">&gt;3
			<td class="cg4">N/A
		<tr>
			<td class="cgD">Ordering
			<td class="cg1"><?= !Login::is_logged_in() ? Html::a("?login", t("Free")) : t("Free") ?>
			<td class="cg2">Order plan
			<td class="cg3">Order plan
			<td class="cg4"><a href="?help_api">Free</a>
	</tbody>
</table>
<dl>
<dt>Basic <dd> Basic plan includes everything you need :) and it is free.
<dt>Better<dd> Better plan includes everything you need plus search agents.
<dt>Best  <dd> Best plan includes everything you need and more search agents.
<dt>API plan <dd> API plan includes API capabilities.
</dl>
<? } ?>

