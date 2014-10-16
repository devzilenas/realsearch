<?

/**
 * Makes searches with search agents.
 */
class SearchManager {

	public static function search_made_for(User $user, SearchAgent $sa) {
		$w = WalletManager::wallet_for($user);
		$w->take(
			Config::amount_for_search_agent_run(),
			sprintf("SearchAgent#$sa->id, %s",$sa->name),
			$sa
		);
	}

	public static function can_run_search_for(User $user) {
		return WalletManager::left($user)->as_f() > Config::AMOUNT_SEARCH_AGENT_RUN;
	}

	/**
	 * Tells whether can activate search agent.
	 *
	 * @return boolean
	 */
	public static function can_activate(SearchAgent $sa) {
		$user   = Login::user();
		$wallet = WalletManager::wallet_for($user);
		$ret = FALSE;
		if($wallet->left()->as_f() > Config::AMOUNT_SEARCH_AGENT_RUN) {
			$ret = TRUE;
		}
		return $ret;
	}

	/**
	 * Makes search (not run) for seach agent and returns object set.
	 *
	 * @param SearchAgent $sa
	 *
	 * @return ObjSet
	 */
	public static function make_search_for_search_agent(SearchAgent $sa) {
		/** Check if SearchAgent can be run */
		$searchable = $sa->searchable   ;
		$svs        = $sa->load_values();
		$od         = array()           ;

		foreach($svs as $sv) {
			$od[$sv->search_field] = $sv->search_value;
		}

		/** Add touched_on */
		if($searchable::field('touched_on')) {
			$od['touched_on_min'] = $sa->last_run_on;
		}

		$fields    = noid($searchable::fields());
		$s         = Searchable::make_search($fields, $od);
		$filter    = $s->make_filter();
		$filter->setLimit(1);
		return new ObjSet($searchable, $filter, NULL, 1);
	}

}

