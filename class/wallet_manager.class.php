<?

class WalletManager {

	/**
	 * Wallet for user. Creates if doesn't have one.
	 *
	 * @param User $user
	 *
	 * @return Wallet
	 */
	public static function wallet_for(User $user) {
		$ret = NULL;
		if($w = Wallet::wallet_for($user)) {
			if(Access::is_owner($user, $w)) {
				$ret = $w;
			}
		} else {
			/** doesn't have one. create. */
			$w = new Wallet();
			Access::owns($user, $w);
			$w->save();
			$ret = $w;
		}
		return $ret;
	}

	/**
	 * See wallet->left
	 */
	public static function left(User $user) {
		$ret = NULL;

		if($w = Wallet::wallet_for($user)) {
			$ret = $w->left();
		}

		return $ret;
	}

}

