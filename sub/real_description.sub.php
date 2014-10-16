<?
$fields = fan(Real::editable_fields(), array('is_active', 'is_sold'));
$currency = (Login::is_logged_in()) ? new Currency(Login::user()->currency) : Config::base_currency();
?>
<table>

	<caption>Real description</caption>
	<tbody>
	<? if($real->has_location_info()) { ?>
		<tr><th colspan="2">Location
		<?= HtmlBlock::real_property($real, Real::field('city')) ?>
		<?= HtmlBlock::real_property($real, Real::field('district')) ?>
		<?= HtmlBlock::real_property($real, Real::field('street')) ?>
	<? } ?>
		<? $fields = anne($fields, array('city', 'district', 'street')) ?>

	<? if($real->has_area_info()) { ?>
		<tr><th colspan="2">Area
		<?= HtmlBlock::real_property($real, Real::field('area')) ?>
		<?= HtmlBlock::real_property($real, Real::field('rooms')) ?>
		<?= HtmlBlock::real_property($real, Real::field('kitchen_area')) ?>
	<? } ?>
		<? $fields = anne($fields, array('area', 'rooms', 'kitchen_area')) ?>

	<? if($real->has_parking_info()) { ?>
		<tr><th colspan="2">Parking
		<?= HtmlBlock::real_property($real, Real::field('has_parking')) ?>
		<?= HtmlBlock::real_price_property($real, Real::field('parking_price'), $currency) ?>
	<? } ?>
		<? $fields = anne($fields, array('has_parking', 'parking_price', 'has_garage', 'garage_price')) ?>
	<? if($real->has_garage_info()) { ?>
		<tr><th colspan="2">Garage
		<?= HtmlBlock::real_property($real, Real::field('has_garage')) ?>
		<?= HtmlBlock::real_price_property($real, Real::field('garage_price'), $currency) ?>
	<? } ?>
		<? $fields = anne($fields, array('has_garage', 'garage_price')) ?>

	<? if(NULL != $real->price) { ?>
		<tr><th colspan="2">Price
		<?= HtmlBlock::real_price_property($real, Real::field('price'), $currency) ?>
	<? } ?>
		<? $fields = anne($fields, array('price')) ?>

		<tr><th colspan="2">Other information
<?
		$str = '';
		foreach($fields as $field) { 
			$name  = $field->name();
			$value = $real->$name;
			if(NULL !== $value) {
				$str .= HtmlBlock::real_property($real, $field);
			}
		}
		echo $str;
?>
	 
	</tbody> 
</table>

