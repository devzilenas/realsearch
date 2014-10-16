<?
$strs = array();
foreach($wls as $wl) {
	$strs[] = sprintf('<tr><td>%s<td>%s<td>%s<td>%s', so($wl->on_), Dbobj::format_money($wl->asc('amount_left'), ' ', ',', 2, TRUE), Dbobj::format_money($wl->asc('amount'), ' ', ',', 2, TRUE), convert(so($wl->what)));
}
if(!empty($strs)) {
	echo sprintf('<table><caption>Wallet last lines</caption><colgroup><col class="cgD"><thead><tr><th>On<th>Amount left<th>Amount<th>What</thead><tbody>%s</tbody></table>', join('', $strs));
} else {
	echo "No last lines!";
}
?>
