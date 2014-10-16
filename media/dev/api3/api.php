<?
/**
 * API. Help: http://localhost/realsearch/?help_api or file "readme.1st" included with api.
 *
 * @author Marius Žilėnas
 * @copyright 2013, Marius Žilėnas
 *
 * @version 0.0.3
 */

include_once 'config.class.php';

class Picture extends StdClass {

	/**
	 * Returns base64 encoded file.
	 *
	 * @return string
	 */
	public function file64() {
		return $this->encoded_base64;
	}

	/**
	 * Gives file decoded.
	 *
	 * @return string
	 */
	public function decoded_file() {
		return base64_decode($this->file64());
	}

	/**
	  * Convert pictures xml to pictures objects.
	  *
	  * @param string $x XML with pictures.
	  *
	  * @return Picture[]
	  */
	private static function x2ps($x) { 
		$xps = simplexml_load_string($x);
		$ps  = array();

		/** Pictures */
		foreach($xps->picture as $xp) {
			$picture = new static(); 
			foreach($xp->f as $field) {
				$d = (string)$field;
				$n = (string)$field['name'];
				$picture->$n = $d;
			}

			/** thumbnails */
			$ts = array();
			if(isset($xp->thumbnails)) {
				foreach($xp->thumbnails->thumbnail as $thumbnail) {
					$t = new Thumbnail();
					$t->encoded_base64 = (string)$thumbnail;
					$t->filename       = sprintf("%s_%s", $thumbnail['name'], $picture->filename);
					$ts[] = $t;
				}
			}

			if(!empty($ts)) {
				$picture->thumbnails = $ts;
			}
			$ps[] = $picture;
		}

		return $ps;
	}
}

class Thumbnail extends Picture {}

class Real extends StdClass {
	/**
	  * Converts xml data to reals.
	  * 
	  * @param string $rd
	  *
	  * @return Real[]
	  */
	private static function x2rs($rd) { 
		$rxml  = simplexml_load_string($rd);
		$reals = array();
		foreach($rxml->real as $rx) {
			$r = new static();
			foreach($rx->f as $field) {
				$name     = (string)$field['name'];
				$r->$name = (string)$field;
			}
			$reals[] = $r;
		}

		return $reals;
	}
}

class Api {

	/**
	  * Converts object to xml.
	  *
	  * @param mixed $object_data
	  *
	  * @param string $oname
	  *
	  * @return string
	  */
	private static function o2x(array $object_data, $oname) {
		$xs = array();
		foreach($object_data as $field => $value) {
			$xs[] = sprintf('<f name="%s">%s</f>', $field, $value);
		}
		return sprintf('<%1$s>%2$s</%1$s>', $oname, join('', $xs));
	}

	public static function api_key() {
		return ConfigApi3::api_key();
	}

	/**
	  * Creates real.
	  *
	  * @param Real $real
	  *
	  * @return boolean
	  */
	public static function create_real(Real $real) { 
		$data = get_object_vars($real);
		$x    = self::o2x($data, 'real');
		$xs   = sprintf('<%1$s>%2$s</%1$s>', 'reals', $x);
		$url  = ConfigApi3::data_source();
		$pd   = http_build_query(array(
					'api_key' => self::api_key(),
					'action'  => 'create',
					'reals'   => $xs
					));

		$opts = array(
				'http' => array(
					'method' => 'POST',
					'header' => 'Content-type: application/x-www-form-urlencoded',
					'content'=> $pd));
		$context = stream_context_create($opts);

		$result  = file_get_contents($url, FALSE, $context);

		return $result;
	}

	/**
	  * Gets reals by ids.
	  * 
	  * @param array $ids
	  *
	  * @return Real[]
	  */ 
	public static function get_reals(array $ids) {
		$query = http_build_query(array('real' => '', 'ids[]' => join(',',$ids)));
		$url = ConfigApi3::data_source().'?'.$query;
		return Real::x2rs(file_get_contents($url));
	}

	/**
	  * Updates real
	  * 
	  * @param Real $real
	  */
	public static function update_real(Real $real) {
		$data = get_object_vars($real);
		$x    = self::o2x($data, 'real');
		$xs   = sprintf('<%1$s>%2$s</%1$s>', 'reals', $x);
		$url  = ConfigApi3::data_source();
		$pd   = http_build_query(array(
					'api_key' => self::api_key(),
					'action'  => 'update',
					'reals'   => $xs));

		$opts = array(
				'http' => array(
					'method' => 'POST',
					'header' => 'Content-type: application/x-www-form-urlencoded',
					'content'=> $pd));
		$context = stream_context_create($opts);

		$result  = file_get_contents($url, FALSE, $context);

		return $result;
	}

	/**
	  * Delete reals 
	  *
	  * @param array $ids
	  *
	  * @return void
	  */
	public static function delete_reals(array $ids) {
		$url  = ConfigApi3::data_source().'?real';
		$pd   = http_build_query(array(
					'api_key' => self::api_key(),
					'action'  => 'delete',
					'real'    => '',
					'ids[]'   => join(',', $ids)));

		$opts = array(
				'http' => array(
					'method' => 'POST',
					'header' => 'Content-type: application/x-www-form-urlencoded',
					'content'=> $pd));
		$context = stream_context_create($opts);

		$result  = file_get_contents($url, FALSE, $context);

		return $result;
	}

	/**
	  * Gets pictures for real.
	  *
	  * @param integer $id
	  *
	  * @return Picture[]
	  */
	public static function get_pictures_for_real($id) {
		$query = http_build_query( array(
					'real'     => $id,
					'pictures' => '',
					'api_key'  => self::api_key()
					));
		$url = ConfigApi3::data_source().'?'.$query;
		$x   = file_get_contents($url);
		return Picture::x2ps($x);
	}

	/**
	  * Creates picture.
	  *
	  * @param Picture $picture
	  *
	  * @return void
	  */
	public static function create_picture_for_real(Picture $picture) {
		return self::create_pictures_for_real(array($picture));
	}

	/**
	 * Creates pictures for real.
	 *
	 * @param Picture[] $pictures
	 *
	 * @return void
	 */
	public static function create_pictures_for_real(array $pictures) {
		$url  = ConfigApi3::data_source();

		$oxs = array();
		foreach($pictures as $p) {
			$data  = get_object_vars($p);
			$oxs[] = self::o2x($data, 'picture');
		}
		$xs = sprintf('<%1$s>%2$s</%1$s>', 'pictures', join('', $oxs));

		$pd = http_build_query(array(
			'api_key'  => self::api_key(),
			'action'   => 'create',
			'pictures' => $xs));

		$opts = array(
				'http' => array(
					'method' => 'POST',
					'header' => 'Content-type: application/x-www-form-urlencoded',
					'content'=> $pd));

		$context = stream_context_create($opts);

		$result = file_get_contents($url, FALSE, $context);

		return $result;
	}

	/**
	 * Deletes pictures.
	 *
	 * @param Picture[] $pictures
	 *
	 * @return void
	 */
	public static function delete_pictures(array $ids) {
		$url  = ConfigApi3::data_source().'?picture';
		$pd   = http_build_query(array(
					'api_key' => self::api_key(),
					'action'  => 'delete',
					'picture' => '',
					'ids[]'   => join(',', $ids)));

		$opts = array(
				'http' => array(
					'method' => 'POST',
					'header' => 'Content-type: application/x-www-form-urlencoded',
					'content'=> $pd));
		$context = stream_context_create($opts);

		$result  = file_get_contents($url, FALSE, $context);

		return $result;
	}

}

