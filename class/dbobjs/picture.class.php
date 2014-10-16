<?

class Picture extends Dbobj implements DbobjInterface {

	const NAME_MY_ID = 'my_picture_id';

	public static function fields() {
		return array(
			new Field('id', Field::T_NUMERIC, "%d", FALSE, FALSE),
			new Field('user_id', Field::T_NUMERIC, "%d", FALSE, FALSE),
			new Field(self::NAME_MY_ID, Field::T_TEXT, "%s", FALSE, FALSE),
			new Field('type', Field::T_TEXT),
			new Field('original_name', Field::T_TEXT),
			new Field('filename', Field::T_TEXT),
			new Field('dir', Field::T_TEXT),
			new Field('attached_to', Field::T_TEXT),
			new Field('attached_id', Field::T_NUMERIC),
			new Field('rank', Field::T_NUMERIC, "%d", FALSE, FALSE),
			new Field('caption', Field::T_TEXT)
		);

	}

	/**
	 * Returns all editable fields. Not editable are fields: id, user_id, etc.
	 *
	 * @return Field[]
	 */
	public static function editable_fields() {
		return array_filter(self::fields(),
			function($el) {
				return FALSE === array_search(
					$el->name(),
					array('id', 'user_id', Picture::NAME_MY_ID, 'rank', 'dir'));
			}
		);
	}

	/**
	 * Returns api editable fields.
	 *
	 * @return Field[]
	 */
	public static function api_editable_fields() {
		return array_merge(self::editable_fields(), array(
			new Field('encoded_base64', Field::T_TEXT)
		));
	}


	/**
	 * Validation.
	 *
	 * @return array
	 */
	public function hasValidationErrors() {
		$validation = array();
		return $validation;
	}

	/**
	 * Getter for name under which picture is stored.
	 */
	public function make_store_name() {
		return $this->id."_".$this->original_name;
	}
	
	/**
	 * Getter for picture source.
	 *
	 * @return string
	 */
	public function src() {
		return PictureManager::store_dir().$this->filename;
	}

	/**
	 * Gets pictures for object.
	 *
	 * @param string $attached_to Class name of the picture owner.
	 *
	 * @param integer $attached_id Id of the picture owner.
	 *
	 * @return Picture[]
	 */
	public static function pictures($attached_to, $attached_id) {
		$filter = self::newFilter();
		$filter->setWhere( array(
			"Picture.attached_to" => $attached_to,
			"Picture.attached_id" => $attached_id));
		$filter->setOrderBy("rank ASC");
		return self::find($filter);
	}

	/**
	 * Gets last picture for object.
	 *
	 * @param mixed $o
	 *
	 * @return Picture|FALSE
	 */
	public static function last($o) {
		$filter = self::newFilter();
		$filter->setWhere(array(
			'Picture.attached_id' => $o->id,
			'Picture.attached_to' => get_class($o)));
		$filter->setOrderBy("id DESC");
		$filter->setLimit(1);
		return current(self::find($filter));
	} 

	/**
	 * Gets thumbnail src for image thumbnail. If thumbnail doesn't exist it is created.
	 *
	 * @todo add check for storage limit exceeded
	 *
	 * @param string $name Name for thumbnail.
	 *
	 * @return Thumbnail|NULL
	 */
	public function thumbnail($name) {
		$has_thumbnail = TRUE;
		$ret           = NULL;
		$thumb_src     = PictureManager::thumbnail_src($name, $this);

		if(!file_exists($thumb_src)) {
			$has_thumbnail = PictureManager::make_thumbnail($this, $name);
		}

		$ret = $has_thumbnail ? new Thumbnail($thumb_src, $name) : new Thumbnail('', $name);
	
		return $ret;
	}

	/**
	 * Gets list of all thumbnails.
	 *
	 * @return Thumbnail[]
	 */
	public function thumbnails() {
		$ret = array();
		foreach(PictureManager::thumbnails_sizes() as $name => $sizes) {
			if($thumb = self::thumbnail($name)) {
				$ret[] = $thumb;
			}
		}
		return $ret;
	}

	/**
	 * After delete.
	 *
	 * @param integer $id Id of deleted object.
	 *
	 * @return void
	 */
	public function after_delete($id) {
		PictureManager::destroy_files($this);
		Rankenstein::has_deleted($this);
	}

	/**
	 * After insert.
	 *
	 * @return void
	 */
	public function afterInsert($id, $o) {
		/** Save new name */
		$o->filename = $o->make_store_name();
		$o->save();
	}

	/**
	 * Get picture with lowest rank. This picture is main.
	 *
	 * @param mixed $oa Object to which picture is attached.
	 *
	 * @return self
	 */
	public static function get_main_for($oa) {
		$filter = self::newFilter();
		$filter->setWhere(array(
			'Picture.attached_id' => $oa->id,
			'Picture.attached_to' => get_class($oa)));
		$filter->setOrderBy("rank ASC");
		$filter->setLimit(1);
		return current(self::find($filter));
	}

	/**
	 * Before insert.
	 *
	 * @return void
	 */
	protected function beforeInsert() {
		$filter = new SqlFilter();
		$filter->setWhere(
			vsprintf(
				'attached_to = %s AND attached_id = %s',
				array_map(
					"Dbobj::eq", 
					array(
						$this->attached_to,
					   	$this->attached_id))));
		$this->rank = Rankenstein::newRank(get_called_class(), $filter);

	}

	/**
	 * Picture encoded with base64
	 *
	 * @return string
	 */
	public function as_base64() {
		return PictureManager::as_base64($this);
	}

	/**
	 * Picture as xml string. It returns picture and its thumbnails coded with base64.
	 *
	 * @return string
	 */
	public function as_xml() {
		$fields   = static::editable_fields();
		$fields[] = self::field(self::NAME_MY_ID);
		$ret      = '';

		foreach($fields as $field) {
			$name  = $field->name();
			$value = $this->$name;
			if(!empty($value)) {
				$ret .= '<f name="'.so($name).'">'.so($value).'</f>';
			}
		}

		/** picture as base64 */
		$p64 = $this->as_base64();
		$ret .= '<f name="encoded_base64">'.$p64.'</f>';

		/** thumbnails */
		$txs = array();
		$thumbnails = $this->thumbnails();
		foreach($thumbnails as $thumbnail) {
			$txs[$thumbnail->name()] = $thumbnail->as_base64();
		}

		$txml = '';

		foreach($txs as $tname => $tx) {
			$txml .= sprintf('<thumbnail name="%s">%s</thumbnail>', $tname, $tx);
		}

		if('' != $txml) {
			$ret .= '<thumbnails>'.$txml.'</thumbnails>';
		}

		$clp = c2u(get_called_class());
		return sprintf('<%2$s id="%1$d">%3$s</%2$s>', $this->id, $clp, $ret); 
	}

}

