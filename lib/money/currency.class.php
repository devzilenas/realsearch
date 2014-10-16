<?
class Currency {

const AED = 'AED';
const AFN = 'AFN';
const ALL = 'ALL';
const AMD = 'AMD';
const ARS = 'ARS';
const AUD = 'AUD';
const AZN = 'AZN';
const BAM = 'BAM';
const BDT = 'BDT';
const BGN = 'BGN';
const BHD = 'BHD';
const BYR = 'BYR';
const BOB = 'BOB';
const BRL = 'BRL';
const CAD = 'CAD';
const CHF = 'CHF';
const CLP = 'CLP';
const CNY = 'CNY';
const COP = 'COP';
const CZK = 'CZK';
const DKK = 'DKK';
const DZD = 'DZD';
const EGP = 'EGP';
const ETB = 'ETB';
const EUR = 'EUR';
const GBP = 'GBP';
const GEL = 'GEL';
const GNF = 'GNF';
const HKD = 'HKD';
const HRK = 'HRK';
const HUF = 'HUF';
const IDR = 'IDR';
const YER = 'YER';
const ILS = 'ILS';
const INR = 'INR';
const IQD = 'IQD';
const IRR = 'IRR';
const ISK = 'ISK';
const JOD = 'JOD';
const JPY = 'JPY';
const KES = 'KES';
const KGS = 'KGS';
const KRW = 'KRW';
const KWD = 'KWD';
const KZT = 'KZT';
const LBP = 'LBP';
const LKR = 'LKR';
const LTL = 'LTL';
const LVL = 'LVL';
const MAD = 'MAD';
const MDL = 'MDL';
const MGA = 'MGA';
const MYR = 'MYR';
const MKD = 'MKD';
const MNT = 'MNT';
const MXN = 'MXN';
const MZN = 'MZN';
const NOK = 'NOK';
const NZD = 'NZD';
const PAB = 'PAB';
const PEN = 'PEN';
const PHP = 'PHP';
const PKR = 'PKR';
const PLN = 'PLN';
const QAR = 'QAR';
const RSM = 'RSM';
const RON = 'RON';
const RSD = 'RSD';
const RUB = 'RUB';
const SAR = 'SAR';
const SEK = 'SEK';
const SGD = 'SGD';
const SYP = 'SYP';
const THB = 'THB';
const TJS = 'TJS';
const TMT = 'TMT';
const TND = 'TND';
const TWD = 'TWD';
const TZS = 'TZS';
const UAH = 'UAH';
const UYU = 'UYU';
const USD = 'USD';
const UZS = 'UZS';
const VEF = 'VEF';
const VND = 'VND';
const XAF = 'XAF';
const XDR = 'XDR';
const XOF = 'XOF';
const ZAR = 'ZAR';
	
	public $m_name;

	/**
	 * Setter for m_name
	 *
	 * @param string $name
	 *
	 * @return void
	 */
	public function set_name($name) {
		if(self::is_valid_short_name($name)) {
			$this->m_name = $name;
		}
	}

	/**
	 * Getter for m_name
	 *
	 * @return string
	 */
	public function name() {
		return $this->m_name;
	}

	/**
	 * Returns short names for currencies.
	 * 
	 * @return array
	 */
	public static function short_names() {
		return array(
self::AED,	self::AFN,	self::ALL,	self::AMD,	self::ARS,	self::AUD,	self::AZN,	self::BAM,	self::BDT,	self::BGN,	self::BHD,	self::BYR,	self::BOB,	self::BRL,	self::CAD,	self::CHF,	self::CLP,	self::CNY,	self::COP,	self::CZK,	self::DKK,	self::DZD,	self::EGP,	self::ETB,	self::EUR,	self::GBP,	self::GEL,	self::GNF,	self::HKD,	self::HRK,	self::HUF,	self::IDR,	self::YER,	self::ILS,	self::INR,	self::IQD,	self::IRR,	self::ISK,	self::JOD,	self::JPY,	self::KES,	self::KGS,	self::KRW,	self::KWD,	self::KZT,	self::LBP,	self::LKR,	self::LTL,  self::LVL,	self::MAD,	self::MDL,	self::MGA,	self::MYR,	self::MKD,	self::MNT,	self::MXN,	self::MZN,	self::NOK,	self::NZD,	self::PAB,	self::PEN,	self::PHP,	self::PKR,	self::PLN,	self::QAR,	self::RSM, self::RON,	self::RSD,	self::RUB,	self::SAR,	self::SEK,	self::SGD,	self::SYP,	self::THB,	self::TJS,	self::TMT,	self::TND,	self::TWD,	self::TZS,	self::UAH,	self::UYU,	self::USD,	self::UZS,	self::VEF,	self::VND,	self::XAF,	self::XDR,	self::XOF,	self::ZAR);
	}

	/**
	 * Tells if currency is valid.
	 *
	 * @param string $name Currency name.
	 *
	 * @return boolean
	 */
	public static function is_valid($name) {
		return self::is_valid_short_name($name);
	}

	/**
	 * Checks short name for validity.
	 *
	 * @param string $short
	 *
	 * @return boolean
	 */
	private static function is_valid_short_name($short) {
		return in_array($short, self::short_names());
	}

	/**
	 * @param string $short Short name for currency.
	 */
	public function __construct($short) {
		if(!self::is_valid($short)) {
			$short = Config::base_currency();
		}
		$this->set_name($short);
	} 

	public function __toString() {
		return $this->name();
	}
}

