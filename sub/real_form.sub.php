<div id="ddd"></div>
<? 
$fields_groups = Real::fields_groups()  ;

foreach($fields_groups as $fg) {
;	$provider    = $fg['provider'];
	$group_title = $fg['title'];
	$rfds        = Real::$provider();
	$istrs       = array();
	$attrs       = array();
	foreach($rfds as $rfd) {
		$name  = $rfd->name();
		$value = $real->$name;
		if(NULL !== $value) {
			$rfd->set_value($value);
		}
		if(Config::is_dropdown_enabled()) {
			$attrs = array('onkeyup' => "values_for('real_$name', 'Real', '$name')", 'autocomplete' => 'off');
		}
		$istrs[] = Form::sfi($rfd, 'Real', FALSE, $attrs);
	} 
	
	echo '<span class="larger bl">'.so($group_title).'</span>';
	echo join('<br />', $istrs);
}

echo Html::clears();

