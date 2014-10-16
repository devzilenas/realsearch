<?
/**
 * TODO: clean-up
 */
include 'includes.php';
include 'lib\test\test.class.php';
DB::connect();

//include 'test/api3/api.php';
//include 'class/picture_effect.class.php';


/*
class TestPictureEffect extends Test {

	public static function test_round_corners() {
		$corner_ratio = 0.2;
		$test_im_src = 'test/thumbnail120x90.jpg'; 
		$im = PictureEffect::round_corners($test_im_src);
		header('Content-Type: image/jpeg');
		imagejpeg($im);
		imagedestroy($im);
	}
}

//DB::connect();
class TestApi extends Test {

	public static function test_delete_picture() {
		print_r(Api::delete_pictures(array(75,76)));
	}

	public static function test_create_picture_for_real() {
		$picture = new Picture();

		$picture->attached_to   = 'Real';
		$picture->attached_id   = 5;
		$picture->type          = 'image/jpeg';
		$picture->original_name = 'untitled.jpg'; 
		$picture->caption       = 'caption';
		$picture->encoded_base64= file_get_contents('test/picture.b64');
		print_r(Api::create_picture_for_real($picture));
	}

	public static function test_get_pictures_for_real() {
		Api::get_pictures_for_real(1);
	}

	public static function test_get_reals_simple() {
		$query = http_build_query(array('real' => '', 'ids[]' => '1,2'));
		$url = Config::base().'/api.php?'.$query;
		$xml = file_get_contents($url);
		echo $xml;
	}

	public static function test_get_reals_2() {
		$reals = Api::get_reals(array(1,2));
	}

	public static function test_create_real() {
		$real = new Real(); 
		$real->city        = 'Vilnius';
		$real->has_parking = 1 ;
		$real->area        = 62;
		$real->is_sold     = 0 ;
		echo Api::create_real($real);
	}
	
	public static function test_delete_reals() {
		echo Api::delete_reals(array(47, 48, 49, 50, 51, 52));
	}

	public static function test_update_real() {
		$real          = new Real();
		$real->id      = 1;
		$real->price   = rand(100000, 200000);

		echo Api::update_real($real);
	} 
}

class TestLib extends Test {
	public static function test_convert() {
		$str = '';
	}
}

class TestWallet extends Test {
	public static function test_put() {
		$w = Wallet::load(1);
		$w->put(new Monia(Config::base_currency(), 20), 'Test');
	}

	public static function test_left() {
	}
}
*/
class TestCurrencyExchange extends Test {

	public static function test_change_not_required() {
		$rate = ExchangeRate::get_rate("2013-06-07", new Currency('EUR'), new Currency('EUR'));
		$money = new Monia(new Currency('EUR'), 100);

		$changed = $rate->changer($money, new Currency('EUR'));
		self::expected(100.0, $changed->as_f(), "100 EUR must be equal to 100 EUR at the rate EUR TO LTL 1:3.4528");
	}

	public static function test_change_has_rate() {
		$rate = ExchangeRate::get_rate("2013-06-07", new Currency('EUR'), new Currency('LTL'));
		$money = new Monia(new Currency('EUR'), 100);
		$changed = $rate->changer($money, new Currency('LTL'));
		self::expected(345.28, $changed->as_f(), "100 EUR must be equal to 345.28 LTL at the rate EUR TO LTL 1:3.4528");
	}

	/** Calculates rate */
	public static function test_change_back_rate() {
		$rate = ExchangeRate::get_rate("2013-06-07", new Currency('LTL'), new Currency('EUR'));
		$money = new Monia(new Currency('LTL'), 345.28);

		$changed = $rate->changer($money, new Currency('EUR'));
		self::expected(100.0, $changed->as_f(), "345.28 LTL must be equal to 100.0 EUR at the rate EUR TO LTL 1:3.4528");
	}

	/** Rate not found take last known */
	public static function test_change_last_known() {
		$rate = ExchangeRate::get_rate(date("Y-m-d"), new Currency('LTL'), new Currency('RUB'));
		$money = new Monia(new Currency('LTL'), 100);

		$changed = $rate->changer($money, new Currency('RUB'));
		echo $changed->as_f();
		//self::expected(100.0, $changed->as_f(), "345.28 LTL must be equal to 100.0 EUR at the rate EUR TO LTL 1:3.4528");
	}


}

/**
 * Tests real search.
 */
class TestRealSearch extends Test {

	/**
	 * Test object saving accross properties tables.
	 */
	public static function test_save_o() { 
		$o = new StdClass();
		$o->id          = 1;
		$o->rooms       = 2;
		$o->has_parking = TRUE;
		$o->street      = 'Highway str.';
		Serializator::save_o($o);
	}

	/**
	 * Test object deleting from properties tables.
	 */
	public static function test_delete_o() {
	}

	/**
	 * Test object update accross properties tables.
	 */
	public static function test_update_o() {
	}

	/**
	 * Test conversion to values.
	 *
	 * @return void
	 */
	public static function test_o_to_values() {
		$o = new StdClass();
		$o->id          = 1;
		$o->rooms       = 2;
		$o->has_parking = TRUE;
		$o->street      = 'Highway str.';
		$ps = Serializator::to_values($o, array('id', 'rooms', 'has_parking', 'street'));
	}

	/**
	 * Test Dbobj conversion to values.
	 *
	 * @return void
	 */
	public static function test_dbobj_to_values() {
		$r = new Real();
		$r->street      = 'Highway';
		$r->rooms       = 2;
		$r->has_parking = TRUE;
		$r->is_sold     = FALSE;
		$r->save();

		$ps = Serializator::to_values($r, array('rooms', 'has_parking', 'street', 'is_sold'));
		foreach($ps as $p) $p->save();
	}

	/**
	 * Test conversion from values to Dbobj.
	 *
	 * @return void
	 */
	public static function test_from_values_to_real() {
		$o = Serializator::get_obj(2, 'Real');
		var_dump($o);
		//self::expected($value, $got, "Must serialize.");
	}

	/**
	 * Test search by values.
	 *
	 * @return void
	 */
	public static function test_search() {
		$f1 = new Field('street',      Field::T_TEXT);
		$f1->set_value('aaa');
		$f2 = new Field('has_parking', Field::T_BOOLEAN);
		$f2->set_value(TRUE);
		$f3 = new Field('rooms',       Field::T_NUMERIC);
		$f3->set_min(2);
		$f3->set_max(3);
		$af = array($f1, $f2, $f3);
		$search = new Search();
		$search->set_fields($af);
		$filters = $search->make_filters();

		$uf  = new UnionFilter($filters);
		$uf->set_distinct(TRUE);

		echo $uf->makeSQL();
	}

}

class TestRealApi extends Test {
	public static function test_create_real() {
		$xmls = urlencode("<reals><real><f name=\"my_real_id\">myreal".rand()."</f><f name=\"is_active\">1</f><f name=\"is_sold\">0</f><f name=\"city\">Vilnius</f><f name=\"area\">60.1</f><f name=\"rooms\">3</f></real><real><f name=\"my_real_id\">myreal".rand()."</f><f name=\"is_active\">1</f><f name=\"is_sold\">1</f><f name=\"city\">Kaunas</f><f name=\"area\">60.1</f><f name=\"rooms\">3</f></real></reals>"); 
		echo '<form method="post" action="api.php"><input type="hidden" name="action" value="create" /><input type="hidden" name="reals" value="'.$xmls.'" /><input type="submit" /></form>';

	}

	public static function test_update_reals() {
		$xmls = urlencode("<reals><real><f name=\"id\">46</f><f name=\"my_real_id\">myrealxxxx</f><f name=\"is_active\">0</f><f name=\"is_sold\">1</f><f name=\"city\">Klaipeda</f><f name=\"area\">60.1</f><f name=\"rooms\">3</f></real><real><f name=\"id\">45</f><f name=\"my_real_id\">myrealyyyy</f><f name=\"is_active\">0</f><f name=\"is_sold\">1</f><f name=\"city\">Kaunas</f><f name=\"area\">59.9</f><f name=\"rooms\">3</f></real></reals>");
		echo '<form method="post" action="api.php"><input type="hidden" name="action" value="update" /><input type="hidden" name="reals" value="'.$xmls.'" /><input type="submit" /></form>';
	}
}

/*
class TestJson extends Test {
	public static function test_convert_from_array_to_json() {
		$tdata = array(
			'string1' => 'string',
			'number2' => 10.3,
			'false3'  => false,
			'true4'   => true,
			'null5'   => NULL);
		$jo = new Jobj('SomeClass', $tdata);

		$ds = '{"string1" : "string", "number2" : 10.3, "false3" : false, "true4" : true, "null5" : null}';

		expected($ds, $jo->to_s(), "Must convert to valid json data structure:  $ds ");
	}

	public static function test_convert_from_object_to_json() {
		$obj           = new Talker();
		$obj->id       = 1;
		$obj->nickname = 'Nickname1';

		$jo = $obj->to_jobj();

		$ds = '{"id" : 1, "nickname" : "Nickname1", "user_id" : null}';

		expected($ds, $jo->to_s(), "Must convert to valid json data structure:  $ds ");
	}

	public static function test_convert_array_of_objs_to_json() {
		$jobjs         = array();

		$obj           = new Talker();
		$obj->id       = 1;
		$obj->nickname = 'Nickname1';
		$jobjs[]       = $obj->to_jobj();

		$obj           = new Talker();
		$obj->id       = 2;
		$obj->nickname = 'Nickname2';
		$jobjs[]       = $obj->to_jobj(); 

		echo PHP_EOL;
		echo Json::jo2jc($jobjs, 'talkers');

		$ds = '{"id" : 1, "nickname" : "Nickname1", "user_id" : null}';

	}
}

//DB::test_connect();

class TestChart extends Test {
	public static function test_draw() { 
		$chart = new Chart(array(
			39.6, 36.8, 37.7, 36.9, 36.6, 36.7,
			36.3, 36.8, 36.7, 36.9, 37.6, 36.7,
			36.3, 36.8, 36.7, 36.9, 37.6, 36.7,
			36.3, 36.8, 36.7, 36.9, 37.6, 36.7,
			36.6, 37.8, 37.7, 36.9, 36.6, 36.7, 36.9), 600, 600, 35, 41, 36.6);
		echo '<img src="data:image/png;base64,'.$chart->toImage64().'" />';
	}
}
 */
class TestSQL extends Test {

	private static function getDbObjFilter1($name) {
		$filter = $name::newFilter(array($name => '*'));
		$filter->setLimit(1); 
		return $filter;
	}

	private static function getDbObj($name) {
		return current($name::find(self::getDbObjFilter1($name)));
	}

	private static function getC() {
		global $DBOBJS;
		return $DBOBJS[0];
	}

# ------------ FIND ONLY ONE ----------- 
	public static function test_eq() {
		$a = array('2000-10-10', 1);
		$got = vsprintf("%s %s", array_map("Dbobj::eq", $a));
		expected("'2000-10-10' '1'", $got, 'Went wrong?');
	}
	
	public static function test_find_one_two_objects() {
		$name   = self::getC();
		$firsto = self::getDbObj($name);
		expected(1, count($firsto), 'Has to find only one '.$name);

		$filter->setLimit(2);
		$twoos  = $name::find($filter);
		expected(2, count($twoos), "Has to find two ".pluralize($name));
	}

	public static function test_find_with_attribute() {
		$name    = self::getC();

		$filtero = self::getDbObjFilter1($name);
		$filtero->setWhat(array($name => array($fieldl)));

		$fns     = $name::fieldNames();
		$fieldl  = $fns[0];
		$fieldnl = $fns[1];

		$o = current($name::find($filtero));
		expected(TRUE , isset($o->$fieldl), "Has to have loaded field ".$fieldl);
		expected(FALSE, isset($o->$fieldnl), "Should not have loaded field ".$fieldnl);
	}

	public static function test_exists() {
		$name = self::getC(); 
		$o    = self::getDbObj($name);
		expected(TRUE, $name::exists($o->id), "Has to find $name with id=$o->id"); 
	}

	public static function test_load() {
		$name = self::getC(); 
		$o    = self::getDbObj($name);
		$ol   = $name::load($o->id);
		expected($o->id, $ol->id, "Has to load one $name with id=$o->id. Got id=$ol->id");
	}
}


//TestPictureEffect::run(TRUE);
//TestApi::run();
//TestLib::run();
//TestWallet::run();
//TestRealApi::run();
//SyncerRate::sync_for("2013-05-23");
TestCurrencyExchange::run();
//TestRealSearch::run();
//TestJson::run();
//TestSQL::run();
//TestChart::run();
