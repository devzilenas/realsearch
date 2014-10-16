<?

/** This is an example for API usage */

/** 1. Load real, change it && update it. */
/** Get real by id */
$real_id = 1;

if($reals = Api::get_reals(1)) {
	foreach($reals as $real) {
		/** Change real */
		$real->city = 'Some other city';

		/** Update real */
		Api::update_real($real);
	}
}

/** 2. Delete real */

$real_id = 1;

/** Delete real */
Api::delete_reals(array($real_id));



