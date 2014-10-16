<?php 

/**
 * Request processor.
 */
class Req extends Request implements ReqInterface {

	public static function is_api_values_for() {
		return self::isApi() && isset($_GET['values_for'], $_GET['value']);
	}

	public static function is_api_key_request() {
		return self::isAction('api_key_request');
	}

	public static function is_view_my_wallet() {
		return self::isView('Wallet') && isset($_GET['my']);
	}

	/** Reals api. */
	public static function is_api_create_reals() {
		return self::isAction('create') && isset($_POST['reals']);
	}

	public static function is_api_fields_real() {
		return isset($_GET['real'], $_GET['fields']);
	}

	public static function is_api_get_reals() {
		return isset($_GET['real'], $_GET['ids']) || isset($_GET['searchable'], $_GET['real']);
	}

	public static function is_api_update_reals() {
		return self::isAction('update') && isset($_POST['reals']);
	} 

	public static function is_api_delete_reals() {
		return self::isDelete('Real') && isset($_POST['ids']);
	}

	/** Pictures api. */

	public static function is_api_create_pictures() {
		return self::isAction("create") && isset($_POST['pictures']);
	}

	public static function is_api_get_pictures_for_real() {
		return isset($_GET['pictures'], $_GET['real']);
	}

	public static function is_api_update_pictures() {
		return self::isAction("update") && isset($_POST['pictures']);
	}

	public static function is_api_delete_pictures() {
		return self::isDelete("Picture") && isset($_POST['ids']);
	}

	public static function is_help() {
		return isset($_GET['help']);
	}

	public static function is_help_api() {
		return isset($_GET['help_api']);
	}

	public static function is_plans() {
		return isset($_GET['plans']);
	}

	public static function is_set_reals_per_page() {
		return self::isAction('set_reals_per_page');
	}

	public static function isApiSearchFieldNames() {
		return self::isApi() && isset($_GET['field_names']) && isset($_GET['search_str']);
	}

	public static function is_create_picture() {
		return self::isAction('create') && isset($_FILES['picture']);
	}

	public static function is_update_picture() {
		return self::isAction('update') && isset($_POST['picture']);
	}

	public static function is_picture_rank_change() {
		return self::isAction("rank_change") && isset($_GET['picture']);
	}

	public static function is_delete_picture() {
		return self::isDelete('Picture');
	}

	public static function is_create_real() {
		return self::isCreate() && isset($_POST['real']);
	}

	public static function is_edit_real() {
		return self::isEdit('Real');
	} 

	public static function is_update_real() {
		return self::isAction('update') && isset($_POST['real']);
	} 

	public static function is_toggle_activation() {
		return self::isAction('toggle_activation') && isset($_GET['real']);
	}

	public static function is_my_reals() {
		return self::isList('Real') && isset($_GET['my']);
	}

	public static function is_my_search_agents() {
		return self::isList('SearchAgent') && isset($_GET['my']);
	}

	public static function is_search() {
		return isset($_GET['search']);
	}

	public static function is_real_search() {
		return isset($_GET['real_search']);
	}

	public static function is_save_searchable() {
		return self::isAction("save") && isset($_POST['searchable']);
	}

	public static function is_create_contact_info() {
		return self::isCreate() && isset($_POST['contact_info']);
	}

	public static function is_edit_contact_info() {
		return isset($_GET['contact_info'], $_GET['edit']);
	}

	public static function is_update_contact_info() {
		return self::isAction('update') && isset($_POST['contact_info']);
	}

	public static function is_time_zone_set() {
		return self::isAction('time_zone_set');
	}

	public static function is_currency_set() {
		return self::isAction('currency_set');
	}

	/** Search agent */
	public static function is_search_agent_edit_search_values() {
		return isset($_GET['search_agent'], $_GET['edit_sv']);
	}

	public static function is_update_search_agent() {
		return self::isAction('update') && isset($_POST['search_agent']);
	}

	public static function is_delete_search_agent() {
		return self::isDelete('SearchAgent');
	}

	/**
	 * Is search agent values update
	 *
	 * @return boolean
	 */
	public static function is_update_search_agent_values() {
		return isset($_GET['search_agent']) && self::isAction('update_search_values');
	} 

	/**
	 * Is toggle search agent activation?
	 *
	 * @return boolean
	 */
	public static function is_toggle_search_agent_is_active() {
		return self::isAction('toggle_is_active') && isset($_GET['search_agent']);
	}

	/**
	  * Processes request to search field names.
	  * @return void
	  */
	private static function process_api_search_field_names() {
		$search_str  = $_GET['search_str'];
		$searchables = Real::fields();
		$mapping     = array(
				'name' => 'name',
				'type' => 'type'
		);

		$sjobjs = array();

		foreach($searchables as $field) {
			/** Search field names */
			if(FALSE !== stripos($field->name(), $search_str)) {
				$sjobjs[] = $field->to_jobj($mapping);
			}
		}

		$response = Response::successful();
		$response->add_jobjs($sjobjs);
		echo $response->to_jobj(); 
	}

	/**
	 * Save search.
	 * 
	 * @return void
	 */
	private static function process_save_searchable() {
		$searchable = $_POST['searchable'];
		if(isset($_POST[c2u($searchable)])) {
			$od         = $_POST[c2u($searchable)];
			$fields     = $searchable::editable_fields();

			/** Save search */
			if(!empty($od)) {
				$sa = new SearchAgent();
				$sa->searchable  = $searchable;
				$sa->name = date(time());

				Access::owns(Login::user(), $sa);

				if($sa->save()) {
					/** Let's save values */
					$values = $sa->make_values($od);
					$sa->set_values($values);
					self::hlexit("?search_agent=$sa->id&view");
					Logger::info(t("Saved!"));
				}
			}
		} else {
			self::error();
		}
	}

	/**
	 * Make real search.
	 */
	private static function process_real_search() { 
		$searchable      = $_GET['searchable'];
		$idx             = c2u($searchable);
		$od              = array();
		$fields          = noid($searchable::fields());

		if(isset($_GET[$idx])) {
			$od = Form::discard_empty($_GET[$idx]);
		}

		$default_search  = array('is_active' => 1);
		$od = array_merge($od, $default_search);

		$s = Searchable::make_search($fields, $od);
		$f = $s->make_filter();

		$search_strs = array("?real_search&searchable=real");
		foreach($od as $field => $v) {
			if(in_array($field, array_keys($default_search))) {
				continue;
			}
			$key = strtolower($searchable).'['.$field.']';
			$search_strs[$key] = urlencode($v); 
		}

		self::set_out('filter', $f);
		self::set_out('search_strs', $search_strs);
	}

	/**
	 * Process update real.
	 *
	 * @return void
	 */
	private static function process_update_real() {
		if($real = Real::load($_GET['real'])) {
			if(Access::can_update(Login::user(), $real)) {
				$real->ff($_POST['real'], noid(Real::fields()));
				if($validation = $real->hasValidationErrors()) {
					self::saveToSession('Real', $_POST['real']);
					Logger::undefErr(array_values($validation));
					Request::hlexit("?real=$real->id&edit");
				} else {
					$real->save();
					Serializator::update_o($real);
					Logger::info(t("Real updated!"));
					Request::hlexit("?real=$real->id&view");
				}
			} else {
				self::access_denied();
			}
		}
	}

	/**
	 * Creates picture and attaches it to the object.
	 *
	 * @return void
	 */
	private static function process_create_picture() {
		$picture          = new Picture();

		$picture->caption = $_POST['picture']['caption'];

		if(isset($_POST['attached_to'], $_POST['attached_id'])) {
			$cl = $_POST['attached_to'];
			if($ao = $cl::load($_POST['attached_id'])) {
				if(Access::can_edit(Login::user(), $ao)) {
					$picture->attach_to($ao);
				} else {
					self::access_denied();
				}
			}
		}

		/** Make user owner of this picture */
		Access::owns(Login::user(), $picture);

		$pd = $_FILES['picture'];
		if(UPLOAD_ERR_OK == $pd['error']) {
			$picture->original_name = $pd['name'];
			$picture->type = $pd['type'];
			$picture->tmp_name = $pd['tmp_name'];
			if($picture->insert() && PictureManager::store($picture)) {
				PictureManager::make_thumbnails($picture);
				Logger::info(t("Stored successfuly"));
				Request::hlexit("?".c2u($cl)."=$ao->id&view&cp=$picture->id");
			} else {
				Logger::error(t("Not stored!"));
				Request::hlexit("?picture&new");
			}
		}

	}

	/**
	 * Changes picture's rank.
	 *
	 * @return void
	 */
	private static function process_picture_rank_change() {
		$err = TRUE;
		if($picture = Picture::load($_GET['picture'])) {
			if(Access::can_edit(Login::user(), $picture)) {
				Rankenstein::move($picture, $_POST['direction']);
				Logger::info(t("Success!"));
				$err = FALSE;
				Request::hlexit("?real=$picture->attached_id&edit");
			}
		}

		if($err) {
			self::error();
		}
	}

	/**
	 * Process: update picture.
	 *
	 * @return void
	 */
	private static function process_update_picture() {
		$err = TRUE;
		if($picture = Picture::load($_GET['picture'])) {
			if(Access::can_edit(Login::user(), $picture)) {
				$picture->ff($_POST['picture'], array(Picture::field("caption")));
				if($validation = $picture->hasValidationErrors()) {
					self::saveToSession("Picture", $_POST['picture']);
					Logger::undefErr(array_values($validation));
				} else {
					$picture->save();
					Logger::info(t("Picture updated"));
				}
				Request::hlexit("?picture=$picture->id&edit");
				$err = FALSE;
			}
		}

		if($err) {
			self::error();
		}
	}

	/**
	 * Process: delete picture.
	 *
	 * @return void
	 */
	private static function process_delete_picture() {
		if($picture = Picture::load($_GET['picture'])) {
			if(Access::can_delete(Login::user(), $picture)) {
				$attached_id = $picture->attached_id;
				$attached_to = c2u($picture->attached_to);
				if($picture->d()) {
					Logger::info(t("Picture deleted!"));
				} else {
					Logger::undefErr(t("Picture delete error!"));
				}
				self::hlexit("?$attached_to=$attached_id&view");
			} else {
				self::access_denied();
			}
		}
	}

	/**
	 * Process create real.
	 *
	 * @return void
	 */
	private static function process_create_real() {
		$real = new Real();
		$real->ffde($_POST['real'], Real::editable_fields());
		$user = Login::user();

		/** Make user owner of this real */
		Access::owns($user, $real);

		if($validation = $real->hasValidationErrors()) {
			$SESSION['real_validation'] = $validation;
			self::saveToSession('Real', $_POST['real']);
			Logger::undefErr(array_values($validation));
			Request::hlexit("?real&new");
		} else {
			$real->save();

			/** Log action */
			$a = LoggerAction::new_action('create');
			$a->attach_to($real, TRUE);

			/** Add real search monia for the action */
			$w = WalletManager::wallet_for($user);
			$w->put(Config::amount_for_real_create(), "Real#$real->id create");

			Logger::info(t("Real saved!"));
			Request::hlexit("?real=$real->id&view");
		}
	}

	/**
	 * Process view real
	 *
	 * @return void
	 */
	private static function process_view_real() {
		if(Real::exists($_GET['real'])) {
			$real = Serializator::get_obj($_GET['real'], 'Real'); 

			/** Load contact info */
			$ci = new ContactInfo();
			if(!($ci = ContactInfo::get_attached_to(User::load($real->user_id)))) {
				$u = User::load($real->user_id);
				$ci->{"e-mail"} = $u->email;
				$ci->name       = $u->login;
			}

			self::set_out('contact_info', $ci);
			self::set_out('real', $real);
			self::set_out('pictures', Picture::pictures("Real", $real->id));
			/** Log action */
			$a       = LoggerAction::new_action('view');
			$a->attach_to($real, TRUE);

		} else {
			self::error();
		}
	}

	/**
	 * Toggles real activation property.
	 *
	 * @return void
	 */
	private static function process_toggle_activation() {
		if($real = Real::load($_GET['real'])) {
			if(Access::can_edit(Login::user(), $real)) {
				$was_active = $real->is_active;
				$real->is_active = !$real->is_active;
				$msg = ($was_active) ? t("Real deactivated!") : t("Real activated!");
				$real->save();
				Logger::info($msg);
			} else {
				self::access_denied();
			}
		} else {
			Logger::undefErr(t("Error!"));
		}
		Request::hlexit("?real=$real->id&edit");
	}

	/**
	 * Process set reals per page.
	 *
	 * @return void
	 */
	private static function process_set_reals_per_page() {
		$rpp = Config::REALS_PER_PAGE;
		if(is_numeric($_POST['reals_per_page'])) {
			$rpp = abs($_POST['reals_per_page']);
		}
		$_SESSION['reals_per_page'] = $rpp;
		Request::hlexit("?reals&list");
	}

	/**
	 * Process edit real.
	 *
	 * @return void
	 */
	private static function process_edit_real() {
		$error = TRUE;

		/** Check permissions */
		if($real = Real::load($_GET['real'])) {
			if(Access::can_edit(Login::user(), $real)) {
				$error = FALSE;
			}
		}

		if($error) {
			self::access_denied();
		}

	}

	/**
	 * View contact info.
	 *
	 * @return void
	 */
	private static function process_view_contact_info() {
		if($ci = ContactInfo::load($_GET['contact_info'])) {
			if(Access::can_view(Login::user(), $ci)) {
				self::set_out('contact_info', $ci);
			} else {
				self::access_denied();
			}
		} else {
			if($ci = ContactInfo::load_by(array(
				/** has contact info */
				"ContactInfo.user_id" => Login::user()->id))) {
				self::set_out('contact_info', $ci);
			} else { 
				/** has no contact info */
				$ci = new ContactInfo();
				Access::owns(Login::user(), $ci);
				self::set_out('contact_info', $ci);
			}
		}
	}

	/**
	 * Create contact info.
	 *
	 * @return void
	 */
	private static function process_create_contact_info() {
		$ci = new ContactInfo();
		$ci->ffde($_POST['contact_info'], ContactInfo::editable_fields());
		if(isset($_POST['attached_to'], $_POST['attached_id'])) {
			$cl = $_POST['attached_to'];
			if($ao = $cl::load($_POST['attached_id'])) {
				if(Access::can_edit(Login::user(), $ao)) {
					Access::owns(Login::user(), $ci);
					$ci->attach_to($ao, TRUE);
				} else {
					self::access_denied();
				}
			}
		}
	}

	/**
	 * Edit contact info.
	 *
	 * @return void
	 */
	private static function process_edit_contact_info() {
		if($ci = ContactInfo::load_by(array("ContactInfo.user_id" => Login::logged_id()))) {
			if(!Access::can_edit(Login::user(), $ci)) {
				self::access_denied();
			} else { 
				self::set_out('contact_info', $ci);
			}
		} else {
			/** has no contact info */
			Request::hlexit("?contact_info&new&attached_to=User&attached_id=".Login::user()->id);
		}
	}

	/**
	 * Update contact info.
	 *
	 * @return void
	 */
	private static function process_update_contact_info() {
		$err = TRUE;
		if($ci = ContactInfo::load($_GET['contact_info'])) {
			if(Access::can_edit(Login::user(), $ci)) {
				$ci->ff($_POST['contact_info'], ContactInfo::editable_fields());
				if($validation = $ci->hasValidationErrors()) {
					self::saveToSession("ContactInfo", $_POST['contact_info']);
					Logger::undefErr(array_values($validation));
				} else {
					$ci->save();
					Logger::info(t("Updated"));
				}
				Request::hlexit("?contact_info=$ci->id&edit");
				$err = FALSE;
			}
		}

		if($err) {
			self::error();
		}
	}

	private static function process_time_zone_set() {
		$u = Login::user();
		if(isset($_POST['time_zone'])) {
			$u->time_zone = $_POST['time_zone'];
			$u->save();
			Logger::info(t("Success"));
			self::hlexit("?account&my");
		} else {
			self::error();
		}
	}

	private static function process_currency_set() {
		$u = Login::user();
		if(isset($_POST['currency']) && Currency::is_valid($_POST['currency'])) {
			$u->currency = $_POST['currency'];
			$u->save();
			Logger::info(t("Success"));
			self::hlexit("?account&my");
		} else {
			self::error();
		}
	}

	/**
	 * API: reals get.
	 * 
	 * @return void
	 */
	private static function process_api_get_reals() {
		$filter = Real::newFilter();

		/** if ids */
		if(isset($_GET['ids']) && is_array($_GET['ids'])) {
			$ids    = $_GET['ids'];
			$filter = new Filter(array('Real' => '*'));
			$filter->setFrom(array('Real' => 'r'));
			$filter->setWhere(sprintf("r.id IN (%s)", join(',', $ids)));
		}

		/** if search */
		if(isset($_GET['searchable'])) {
			$od     = isset($_GET['real']) && is_array($_GET['real']) ? $_GET['real'] : array();
			$fields = noid(Real::fields());
			$s      = Searchable::make_search($fields, $od);
			$filter = $s->make_filter();
		}

		$page = NULL;
		if(isset($_GET['page'])) {
			$page = (int)$_GET['page'];
		}

		$reals = new ObjSet('Real', $filter, $page);

		echo $reals->as_xml();
	}

	/**
	 * Process api values for. Only if dropdown enabled (experimental feature).
	 *
	 * @return void
	 */
	private static function process_api_values_for() {
		$values_of  = c2u($_GET['values_of']);
		$values_for = $_GET['values_for'];
		$value      = $_GET['value'];
		$str        = '';
		/** Only real allowed. */
		if('real' != $values_of) {
			self::error();
		} else {
			/** Get values for */
			$jobjs    = Real::values_for($values_for, $value);
			$response = Response::successful();
			$response->add_jobjs($jobjs);
			echo $response->to_jobj(); 
		}
	}

	/**
	 * Real fields.
	 *
	 * @return void
	 */
	private static function process_api_fields_real() {
		$str = '';
		$fields = Real::editable_fields();
		foreach($fields as $field) {
			$name = $field->name();
			$type = $field->type_name();
			$str .= sprintf('<f name="%s" type="%s" />', so($name), so($type));
		}
		echo '<fields>'.$str.'</fields>';
	}

	/**
	 * Create reals from xml data. API calls use api_key (secret).
	 *
	 * @return void
	 */
	private static function process_api_create_reals() {
		if($user = User::load_by(array(
						'User.api_key' => $_POST['api_key']))) { 

			$rd    = $_POST['reals'];
			$rxml  = simplexml_load_string($rd);
			$reals = array();

			foreach($rxml->real as $rx) {
				$real = new Real();
				$data = array();
				foreach($rx->f as $field) {
					$data[(string)$field['name']] = (string)$field;
				}
				$real->ffde($data, Real::editable_fields());

				/** Make user owner of this real */
				Access::owns($user, $real);

				$reals[] = $real;
			}

			$successful_ids   = array();
			$unsuccessful_ids = array(); 

			$i = 0;
			foreach($reals as $real) {
				if($validation = $real->hasValidationErrors()) {
					$err_msgs = array_values($validation);
					$myrid = isset($real->{Real::NAME_MY_ID}) ? $real->{Real::NAME_MY_ID} : $i;
					if(!empty($myrid)) {
						$unsuccessful_ids[$myrid] = join(';', $err_msgs);
					}
				} else {
					$id    = $real->save();
					$myrid = isset($real->{Real::NAME_MY_ID}) ? $real->{Real::NAME_MY_ID} : $id;
					$successful_ids[$myrid] = $id;
				}
			}
			$i++;
		}

		$unsidstr = '';
		$sucidstr = '';
		foreach($successful_ids as $my_real_id => $sucid ) {
			$sucidstr .= sprintf('<real id="%d" my_real_id="%s" status="success" id="%d" />', $sucid, $my_real_id, $id);
		}

		foreach($unsuccessful_ids as $my_real_id => $msgs) {
			$unsidstr .= sprintf('<real my_real_id="%s" status="fail">%s</real>', $my_real_id, $msgs) ;
		}

		echo '<response>'.$sucidstr.$unsidstr.'</response>';
	} 

	/**
	 * Updates reals from xml data.
	 *
	 * @return void
	 */
	private static function process_api_update_reals() { 
		if($user = User::load_by(array(
						'User.api_key' => $_POST['api_key']))) {

			$rd   = $_POST['reals'];
			
			$rxml = simplexml_load_string($rd);

			$successful_ids   = array();
			$unsuccessful_ids = array(); 

			foreach($rxml->real as $rx) {
				$data = array();
				foreach($rx->f as $field) {
					$data[(string)$field['name']] = (string)$field;
				}

				if(isset($data['id']) && $real = Real::load($data['id'])) {
					$real->ff($data, Real::editable_fields());
					if(!$real->isNew() && Access::can_edit($user, $real)) {
						$real->ff($data, Real::editable_fields());
						if($validation = $real->hasValidationErrors()) {
							$err_msgs  = array_values($validation);
							$unsuccessful_ids[$id] = join(';', $err_msgs);
						} else {
							$successful_ids[] = $real->id;
							$real->save();
						}
					}
				}
			}
		}

		$unsidstr = '';
		$sucidstr = '';

		foreach($successful_ids as $id) {
			$sucidstr .= sprintf('<real id="%d" status="success" />', $id);
		}

		foreach($unsuccessful_ids as $id => $msgs) {
			$unsidstr .= sprintf('<real id="%d" status="fail">%s</real>', $id, $msgs) ;
		}

		echo '<response>'.$sucidstr.$unsidstr.'</response>';
	}

	/**
	 * Api delete reals
	 *
	 * @todo sanitize ids
	 *
	 * @return void
	 */
	private static function process_api_delete_reals() {
		if($user = User::load_by(array(
						'User.api_key' => $_POST['api_key']))) {
			$filter = Real::newFilter();

			$successful_ids   = array();
			$unsuccessful_ids = array(); 

			if(isset($_POST['ids']) && is_array($_POST['ids'])) {
				$ids    = $_POST['ids'];
				$filter = new Filter(
						array('Real' => array('id', 'user_id')));
				$filter->setFrom(
						array('Real' => 'r'));
				$filter->setWhere(
						sprintf("r.id IN (%s) and r.user_id = %d", join(',', $ids), $user->id));
				$reals = Real::find($filter);
				foreach($reals as $real) {
					$id = $real->id;
					if(Access::can_delete($user, $real) && $real->d()) {
						$successful_ids[] = $id;
					} else {
						$unsuccessful_ids[$id] = t("Access denied");
					}
				}
			}
		}

		$unsidstr = '';
		$sucidstr = '';

		foreach($successful_ids as $id) {
			$sucidstr .= sprintf('<real id="%d" status="success" />', $id);
		}

		foreach($unsuccessful_ids as $id => $msg) {
			$unsidstr .= sprintf('<real id="%d" status="fail">%s</real>', $id, $msg) ;
		} 
		echo '<response>'.$sucidstr.$unsidstr.'</response>';
	}

	/**
	 * API: create pictures.
	 *
	 * @return void
	 */
	private static function process_api_create_pictures() {
		if($user = User::load_by(array(
			'User.api_key' => $_POST['api_key']))) {

			$pd       = $_POST['pictures'];
			$pxml     = simplexml_load_string($pd);

			$pictures = array();
			$pds      = array();
			$pcs      = array();
			$successful_ids   = array();
			$unsuccessful_ids = array();

			foreach($pxml->picture as $px) {
				$picture = new Picture();
				$data    = array();
				foreach($px->f as $field) {
					$data[(string)$field['name']] = (string)$field;
				}
				$picture->ffde($data, Picture::api_editable_fields());
				Access::owns($user, $picture);

				$pcs[] = $picture;
			}
		}

		$successful_ids   = array();
		$unsuccessful_ids = array(); 

		/** Batch process pictures */
		foreach($pcs as $picture) {
			if(isset($picture->attached_to, $picture->attached_id)) {
				$ato = $picture->attached_to;
				$ao  = $ato::load($picture->attached_id);
				$myrid = isset($picture->{Picture::NAME_MY_ID}) ? $picture->{Picture::NAME_MY_ID} : NULL;
				if(!Access::can_edit($user, $ao)) {
					$unsuccessful_ids[$picture->{Picture::NAME_MY_ID}] = t("Access denied!");
				} else {
					if($picture->insert() && PictureManager::store64($picture, $picture->encoded_base64)) {
						PictureManager::make_thumbnails($picture);
						$mypid = isset($picture->{Picture::NAME_MY_ID}) ? $picture->{Picture::NAME_MY_ID} : $picture->id;
						$successful_ids[$mypid] = $picture->id;
					}
				}
			}
		}

		$unsidstr = '';
		$sucidstr = '';
		foreach($successful_ids as $my_picture_id => $sucid ) {
			$sucidstr .= sprintf('<picture id="%d" my_picture_id="%s" status="success" />', $sucid, $my_picture_id);
		}

		foreach($unsuccessful_ids as $my_picture_id => $msg) {
			$unsidstr .= sprintf('<picture my_picture_id="%s" status="fail">%s</picture>', $my_picture_id, $msg) ;
		}

		echo '<response>'.$sucidstr.$unsidstr.'</response>';
	}

	/**
	 * API: pictures get.
	 *
	 * @return void
	 */
	private static function process_api_get_pictures_for_real() {
		if($user = User::load_by(array(
						'User.api_key' => $_GET['api_key']))) {
			$filter = new Filter(array("Picture" => "*"));

			if(isset($_GET['real']) && Real::exists($_GET['real'])) {
				$filter->setFrom(array("Picture" => "p"));
				$filter->setWhere(array(
					'Picture.attached_to' => 'Real',
					'Picture.attached_id' => $_GET['real']));
			}

			$page = NULL;
			if(isset($_GET['page'])) {
				$page = (int)$_GET['page'];
			}

			$pictures = new ObjSet('Picture', $filter, $page);

			echo $pictures->as_xml();
		}
	}

	/**
	 * API: update picture.
	 *
	 * @return void
	 */
	private static function process_api_update_pictures() {
		/** @todo remove urldecode, it is only for testing */
		$pd = urldecode($_POST['pictures']);

		$pxml = simplexml_load_string($pd);

		$successful_ids = array();
		$unsuccessful_ids = array();
		$user = Login::user();

		foreach($pxml->picture as $px) {
			$data = array();
			foreach($px->f as $field) {
				$data[(string)$field['name']] = (string)$field; 
			}

			if(isset($data['id']) && $picture = Picture::load($data['id'])) {
				$picture->ff($data, Picture::field('caption'));
				if(!$picture->isNew() && Access::can_edit($user, $picture)) {
					if($validation = $picture->hasValidationErrors()) {
						$err_msgs  = array_values($validation);
						$unsuccessful_ids[$id] = join(';', $err_msgs);
					} else {
						$successful_ids[] = $picture->id;
						$picture->save();
					}
				}
			}
		}

		$unsidstr = '';
		$sucidstr = '';

		foreach($successful_ids as $id) {
			$sucidstr .= sprintf('<picture id="%d" status="success" />', $id);
		}

		foreach($unsuccessful_ids as $id => $msg) {
			$unsidstr .= sprintf('<picture id="%d" status="fail">%s</picture>', $id, $msg) ;
		}

		echo '<response>'.$sucidstr.$unsidstr.'</response>';
	}

	private static function process_api_delete_pictures() {
		if($user = User::load_by(array(
			'User.api_key' => $_POST['api_key']))) {
			$filter = Picture::newFilter();
			$successful_ids   = array();
			$unsuccessful_ids = array();

			if(isset($_POST['ids']) && is_array($_POST['ids'])) {
				$ids    = $_POST['ids'];
				$filter = new Filter(array("Picture" => "*"));
				$filter->setFrom(array("Picture" => "p"));
				$filter->setWhere(sprintf("p.id IN (%s) and p.user_id = %d", join(',', $ids), $user->id));
				$pictures = Picture::find($filter);
				foreach($pictures as $picture) {
					if(Access::can_delete($user, $picture) && $picture->d()) {
						$successful_ids[] = $picture->id;
					} else {
						$unsuccessful_ids[] = t("Access denied!");
					}
				}
			}
		}

		$unsidstr = '';
		$sucidstr = '';

		foreach($successful_ids as $id) {
			$sucidstr .= sprintf('<picture id="%d" status="success" />', $id);
		}

		foreach($unsuccessful_ids as $id => $msg) {
			$unsidstr .= sprintf('<picture id="%d" status="fail">%s</picture>', $id, $msg) ;
		} 
		echo '<response>'.$sucidstr.$unsidstr.'</response>';
	}

	private static function process_edit_search_agent() {
		if($sa = SearchAgent::load($_GET['search_agent'])) {
			if(Access::can_edit(Login::user(), $sa)) {
				self::set_out('search_agent', $sa);
			} else {
				self::access_denied();
			}
		} else {
			self::error();
		}
	}

	/**
	 * View search agent.
	 *
	 * @return void
	 */
	private static function process_view_search_agent() {
		if($sa = SearchAgent::load($_GET['search_agent'])) {
			if(Access::can_view(Login::user(), $sa)) {
				self::set_out('search_agent', $sa);
			} else {
				self::access_denied();
			}
		} 
	}

	/**
	 * Edit search agent search values.
	 *
	 * @return void
	 */
	private static function process_edit_search_agent_values() {
		if($sa = SearchAgent::load($_GET['search_agent'])) {
			if(Access::can_edit(Login::user(), $sa)) {
				$svs = $sa->load_values_as_fields_for('Real');
				self::set_out('search_agent', $sa);
				self::set_out('search_values', $svs);
			} else {
				self::access_denied();
			}
		} else {
			self::error();
		}
	}

	/**
	 * Updates search agent values.
	 *
	 * @return void
	 */
	private static function process_update_search_agent_values() {
		if($sa = SearchAgent::load($_GET['search_agent'])) {
			if(Access::can_update(Login::user(), $sa)) {
				$searchable = $_POST['searchable'];
				$od         = $_POST['search_value'];
				$fields     = $searchable::fields();
				if(!empty($od)) {
					$values = $sa->make_values(Form::discard_empty($od));
					$sa->set_values($values);
					Logger::info(t("Success!"));
					self::hlexit("?search_agent=$sa->id&view");
				}
			} else {
				self::access_denied();
			}
		} else {
			self::error();
		}
	}

	/**
	 * Updates search agent.
	 *
	 * @return void
	 */
	private static function process_update_search_agent() {
		if($sa = SearchAgent::load($_GET['search_agent'])) {
			if(Access::can_update(Login::user(), $sa)) {
				$sa->ff($_POST['search_agent'], SearchAgent::editable_fields());
				if($validation = $sa->hasValidationErrors()) {
					self::saveToSession('SearchAgent', $_POST['search_agent']);
					Logger::undefErr(array_values($validation));
					Request::hlexit("?search_agent=$sa->id&edit");
				} else {
					$sa->save();
					Logger::info(t("Updated"));
					Request::hlexit("?search_agent=$sa->id&view");
				}
			} else {
				self::access_denied();
			}
		} else {
			self::error();
		}
	}

	private static function process_my_search_agents() { 
		$filter = SearchAgent::newFilter();
		$filter->setWhere(array(
			'SearchAgent.user_id' => Login::logged_id()));
		self::set_out('filter', $filter);
	}

	private static function process_delete_search_agent() {
		if($sa = SearchAgent::load($_GET['search_agent'])) {
			if(Access::can_delete(Login::user(), $sa) && $sa->d()) {
				Logger::info(t("Success!"));
				self::hlexit("?search_agents&list&my");
			} else {
				self::access_denied();
			}
		}
	}

	private static function process_toggle_search_agent_is_active() {
		if($sa = SearchAgent::load($_GET['search_agent'])) {
			if(Access::can_edit(Login::user(), $sa)) {
				$was_active = $sa->is_active;
				if(!$was_active) {
					/** Can activate search agent */
					if(SearchManager::can_activate($sa)) {
						Logger::info(t("Activated!"));
						$sa->toggle('is_active');
					} else {
						Logger::error(t("Not enough funds"));
					}
				} else {
					$sa->toggle('is_active');
					Logger::info(t("Deactivated!"));
				}
				Request::hlexit("?search_agents&list&my");
			} else {
				self::access_denied();
			}
		} else {
			self::error();
		}
	}

	private static function process_search_edit() {
		if($sa = SearchAgent::load($_GET['search_agent'])) {
			if(Access::can_delete(Login::user(), $sa)) {
				Req::set_out('search_agent', $sa);
			} else {
				self::access_denied();
			}
		}
	}

	/**
	 * View my wallet.
	 *
	 * @return void
	 */
	private static function process_view_my_wallet() {
		if($w = WalletManager::wallet_for(Login::user())) { 
			self::set_out('wallet', $w);
		} else {
			self::error();
		}
	}

	/**
	  * Shows api key. If doesn't have one, then creates.
	  *
	  * @return void
	  */
	private static function process_api_key_request() {
		$user = Login::user();
		if(!ApiManager::is_valid_api_key($user->api_key)) {
			$user->api_key = ApiManager::generate_api_key();
			$user->save();
			Logger::info(t("Key generated!"));
			self::hlexit("?account&my");
		}
	}

	public static function process($api = FALSE) {

		self::setApi($api);

		/** Authentication */
		RequestAuth::process();

		if(self::isApi()) {

			if(self::is_api_values_for()) {
				if(Config::is_dropdown_enabled()) {
					self::process_api_values_for();
				}
			}

			/** Reals api */
			if(self::is_api_fields_real()) {
				self::process_api_fields_real();
			}

			if(self::is_api_create_reals()) {
				self::process_api_create_reals();
			}

			if(self::is_api_get_reals()) {
				self::process_api_get_reals();
			}

			if(self::is_api_update_reals()) { 
				self::process_api_update_reals();
			}

			if(self::is_api_delete_reals()) {
				self::process_api_delete_reals();
			}

			/** Pictures api */
			if(self::is_api_create_pictures()) {
				self::process_api_create_pictures();
			}

			if(self::is_api_get_pictures_for_real()) {
				self::process_api_get_pictures_for_real();
			}

			if(self::is_api_update_pictures()) {
				self::process_api_update_pictures();
			}

			if(self::is_api_delete_pictures()) {
				self::process_api_delete_pictures();
			}

			if(self::isApiSearchFieldNames()) {
				self::process_api_search_field_names();
			}

		}

		if(self::isView('Real')) {
			self::process_view_real();
		}
		
		if(Login::is_logged_in()) {

			if(self::is_create_picture()) {
				self::process_create_picture();
			}

			if(self::is_update_picture()) {
				self::process_update_picture();
			}

			if(self::is_delete_picture()) {
				self::process_delete_picture();
			}

			if(self::is_create_real()) {
				self::process_create_real();
			}

			if(self::is_edit_real()) {
				self::process_edit_real();
			}

			if(self::is_update_real()) {
				self::process_update_real();
			}

			if(self::is_toggle_activation()) {
				self::process_toggle_activation();
			}

			if(self::is_picture_rank_change()) {
				self::process_picture_rank_change();
			}

			if(self::isDelete('Real')) {
				self::process_delete_real();
			}

			if(self::isView('ContactInfo')) {
				self::process_view_contact_info();
			}

			if(self::is_create_contact_info()) {
				self::process_create_contact_info();
			}

			if(self::is_edit_contact_info()) {
				self::process_edit_contact_info();
			}

			if(self::is_update_contact_info()) {
				self::process_update_contact_info();
			}

			if(self::is_time_zone_set()) {
				self::process_time_zone_set();
			}

			if(self::is_currency_set()) {
				self::process_currency_set();
			}

			if(self::is_save_searchable()) {
				self::process_save_searchable();
			}

			/** Search agent */

			if(self::isView('SearchAgent')) {
				self::process_view_search_agent();
			}

			if(self::isEdit('SearchAgent')) {
				self::process_edit_search_agent();
			}

			if(self::is_search_agent_edit_search_values()) {
				self::process_edit_search_agent_values();
			}

			if(self::is_update_search_agent()) {
				self::process_update_search_agent();
			}

			if(self::is_delete_search_agent()) {
				self::process_delete_search_agent();
			}

			if(self::is_my_search_agents()) {
				self::process_my_search_agents();
			}

			if(self::is_update_search_agent_values()) {
				self::process_update_search_agent_values();
			}

			if(self::is_toggle_search_agent_is_active()) {
				self::process_toggle_search_agent_is_active();
			}

			/** Wallet */
			if(self::is_view_my_wallet()) {
				self::process_view_my_wallet();
			}

			/** Api key */
			if(self::is_api_key_request()) {
				self::process_api_key_request();
			}

		}

		if(self::is_real_search()) {
			self::process_real_search();
		}

		if(self::is_help()) { }
		if(self::is_help_api()) { }

		if(self::is_plans()) {}

		if(self::is_set_reals_per_page()) {
			self::process_set_reals_per_page();
		}

	}

	/**
	 * Access denied.
	 *
	 * @return void
	 */
	private static function access_denied() {
		Logger::error(t("Access denied!"));
		Request::hlexit("?reals&list");
	}

	/**
	 * Error.
	 *
	 * @param string $message (optional) Message.
	 *
	 * @return void
	 */
	private static function error($msg = NULL) {
		Logger::undefErr(t($msg ? $msg : "Error!"));
		Request::r2b();
	}

}

