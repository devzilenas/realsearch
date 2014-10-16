<?

/**
 * Base class for testing.
 *
 * @version 0.1.1
 */
class Test {

	/**
	 * Check value.
	 *
	 * @param mixed $value Value to check.
	 *
	 * @param mixed $got Result to check against.
	 *
	 * @param string $msg Message to output.
	 *
	 * @return void
	 */
	public function expected($value, $got, $msg) {
		if ($value !== $got) {
			echo "Expected value:".$value.PHP_EOL;
			echo "Got:".$got.PHP_EOL;
			echo $msg.PHP_EOL;
		}
	} 

	/**
	 * Get test methods.
	 * Test methods start with "test_".
	 *
	 * @return array
	 */
	private function t_methods() {
		return preg_grep("/^test_/", get_class_methods(get_called_class()));
	}

	/**
	 * Run all test methods.
	 *
	 * @param boolean $silent When TRUE then silent mode.
	 *
	 * @return void
	 */
	public function run($silent = FALSE) {
		$test_methods = self::t_methods();
		foreach($test_methods as $method) {
			if(!$silent) {
				echo "Calling ".get_called_class()."::$method".'<br />';
			}
			call_user_func("static::".$method);
		}
	}

}


