<?
	$fields = ContactInfo::editable_fields();
	$str    = '';
	foreach($fields as $field) {
		$str .= Form::ff($field, $ci).'<br />';
	}
	echo $str;
?>


