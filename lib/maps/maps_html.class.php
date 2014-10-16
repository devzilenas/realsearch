<?

/**
 * For maps display in HTML.
 *
 * @version 0.0.1
 */
class MapsHtml {

	const PROVIDER = 'https://maps.googleapis.com/maps/api/staticmap?size=500x500&sensor=false';

	/**
	 * Shows map image by address.
	 */
	public static function img_by_address($address) {
		return Html::img(self::PROVIDER.'&markers='.urlencode($address));
	}
}

