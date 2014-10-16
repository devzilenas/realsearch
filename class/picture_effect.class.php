<?

/**
 * Picture effects.
 *
 * @author Marius Žilėnas <mzilenas@gmail.com>
 * @copyright 2013 Marius Žilėnas
 *
 * @version 0.0.1
 */
class PictureEffect {

	/**
	 * Returns image with rounded corners.
	 *
	 * @param mixed $im Image to round.
	 *
	 * @return mixed
	 */
	public static function round_corners($im, $corner_ratio = Config::PICTURE_CORNER_RATIO) {
		$w = imagesx($im);
	   	$h = imagesy($im);
		$r = ceil(sqrt($h*$h + $w*$w)/2*$corner_ratio);

		$cr    = imagecreatetruecolor($r, $r); 

		$black = imagecolorallocate($cr, 0, 0, 0); 
		$white = imagecolorallocate($cr, 255, 255, 255);
		imagecolortransparent($cr, $black);

		imagefill($cr, 0, 0, $white);
		imagefilledarc($cr, $r, $r, 2*$r+1, 2*$r+1, 180, 270, $black, IMG_ARC_PIE);

		// top left
		imagecopymerge($im, $cr, 0, 0, 0, 0, $r, $r, 100);

		// top right
		$cr = imagerotate($cr, 90, $black);
		imagecopymerge($im, $cr, 0, $h-$r, 0, 0, $r, $r, 100);

		// bottom right
		$cr = imagerotate($cr, 90, $black);
		imagecopymerge($im, $cr, $w-$r, $h-$r, 0, 0, $r, $r, 100);

		// bottom left
		$cr = imagerotate($cr, 90, $black);
		imagecopymerge($im, $cr, $w-$r, 0 , 0, 0, $r, $r, 100);
		
		imagedestroy($cr);

		return $im;
	}

}

