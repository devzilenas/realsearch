<? $ci = Req::out('contact_info'); ?>

<div>
<table>

<caption>Contacts</caption>
<tbody>
<? if($ci->name) { ?>
<tr><td>Name<td><?= so($ci->name) ?>
<? } ?>

<? if($ci->{"e-mail"}) { ?>
<tr><td>E-mail<td><?= so($ci->{"e-mail"}) ?>
<? } ?>

<? if($ci->mobile) { ?>
<tr><td>Mobile<td><?= so($ci->mobile) ?>
<? } ?>
</tbody>

</table>

</div>

