<?

/**
 * The class name speaks for itself.
 *
 * Picture manager.
 *
 * @author Marius Žilėnas
 * @copyright 2013, Marius Žilėnas
 *
 * @version 0.0.2
 */
class PictureManager {

	/**
	 * Gets size for thumbnail if not found then small is returned.
	 *
	 * @param string $name
	 *
	 * @return array
	 */

	public static function thumbnail_size($name) {
		$sizes = self::thumbnails_sizes();
		return (!isset($sizes[$name])) ? current($sizes) : $sizes[$name];
	}

	/**
	 * Sizes for thumbnails.
	 *
	 * @return array
	 */
	public static function thumbnails_sizes() {
		return array(
			"small" => array(120, 90),
			"large" => array(180, 150),
			"xx-large" => array(400, 400));
	}

	/**
	 * Returns for upload dir.
	 *
	 * @return string
	 */
	public static function upload_dir() {
		return join('/',
			array('.', 'media', 'upload', ''));
	}

	/**
	 * Returns pictures' storage directory.
	 *
	 * @return string
	 */
	public static function store_dir() {
		return self::upload_dir().join('/', array('p', ''));
	}

	/**
	 * Returns thumbnails' storage dir.
	 *
	 * @return string
	 */
	public static function thumbnails_store_dir() {
		return self::upload_dir().join('/', array("t", ''));
	}

	/**
	 * Move file to store.
	 *
	 * @param Picture $picture
	 *
	 * @return boolean
	 */
	public static function store(Picture $picture) {
		return move_uploaded_file(
			$picture->tmp_name, 
			$picture->src());
	}

	/**
	 * Picture encoded with base64
	 *
	 * @param Picture $picture
	 *
	 * @return string
	 */
	public static function as_base64(Picture $picture) {
		$ret = NULL;
		if(file_exists($picture->src())) {
			$ret = file_get_contents($picture->src());
		}
		return base64_encode($ret);
	}

	/**
	 * Store picture from base64 string.
	 *
	 * @param string $encoded
	 *
	 * @return void
	 */
	public static function store64($picture, $encoded) {
		return file_put_contents($picture->src(), base64_decode($encoded));
	}

	/**
	 * Returns thumbnail src.
	 *
	 * @param string $name Name of thumbnail.
	 *
	 * @param Picture $picture
	 *
	 * @return string
	 */
	public static function thumbnail_src($name, Picture $picture) {
		return self::thumbnails_store_dir().$name."_".$picture->filename;
	}

	/**
	 * Makes thumbnail for picture.
	 *
	 * @param Picture $picture
	 *
	 * @param string $name
	 *
	 * @return boolean FALSE on error.
	 */
	public static function make_thumbnail(Picture $picture, $name) {
		$ret = FALSE;
		list($tw, $th) = self::thumbnail_size($name);
		$tumbim_src    = self::thumbnail_src($name, $picture);
		$tumbim = imagecreatetruecolor($tw, $th);
		$origim = @imagecreatefromjpeg($picture->src());
		if($origim) {
			$ret = TRUE;
			list($ow, $oh) = getimagesize($picture->src());
			imagecopyresampled($tumbim, $origim, 0, 0, 0, 0, $tw, $th, $ow, $oh);

			/** Apply effects on thumbnails */
			self::apply_effects($tumbim);

			imagejpeg($tumbim, $tumbim_src);

			imagedestroy($tumbim);
			imagedestroy($origim);
		}
		return $ret;
	}

	/**
	 * Makes thumbnails for picture.
	 *
	 * @return void
	 */
	public static function make_thumbnails(Picture $picture) {
		foreach(array_keys(self::thumbnails_sizes()) as $name) {
			self::make_thumbnail($picture, $name);
		}
	}

	/**
	 * Destroys files for picture: picture file and thumbnails.
	 *
	 * @param Picture $picture
	 *
	 * @return void
	 */
	public static function destroy_files(Picture $picture) {
		self::destroy_picture_file($picture);
		self::destroy_thumbnails($picture);
	}

	/**
	 * Deletes picture file.
	 *
	 * @param Picture $picture
	 *
	 * @return void
	 */
	private static function destroy_picture_file(Picture $picture) { 
		if(file_exists($picture->src())) {
			unlink($picture->src());
		}
	}

	/**
	 * Deletes thumbnails.
	 *
	 * @param Picture $picture
	 *
	 * @return void
	 */
	private static function destroy_thumbnails(Picture $picture) {
		$thumbs = $picture->thumbnails();
		foreach($thumbs as $thumb) {
			$src = $thumb->src();
			if(file_exists($src)) {
				unlink($src);
			}
		}
	} 

	/**
	 * Apply effects on image
	 *
	 * @param mixed $im Image resource.
	 *
	 * @return void
	 */
	private static function apply_effects($im) {
		PictureEffect::round_corners($im);
	}

	/**
	 * Thumbnail cornered 
	 *
	 * @return string
	 */
	public static function thumbnail_default_name() {
		return 'real_item_thumbnail120x90.png';
	}

	public static function thumbnail_cornered() {
		return self::image_path(self::thumbnail_cornered_name());
	}

	public static function thumbnail_cornered_name() {
		return 'cornered_'.self::thumbnail_default_name();
	}

	/**
	 * Returns base dir for images
	 *
	 * @return string
	 */
	public static function image_base() {
		return 'media/img/';
	}

	/**
	 * Returns full image path.
	 *
	 * @param string $name Image file name.
	 *
	 * @return string
	 */
	public static function image_path($name) {
		return self::image_base().$name;
	}

}

