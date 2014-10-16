<?

class Thumbnail {

	private $m_src;
	private $m_name;

	function __construct($src, $name) {
		$this->set_src($src)  ;
		$this->set_name($name);
	}

	public function set_name($name) {
		$this->m_name = $name;
	}

	public function name() {
		return $this->m_name;
	}
	
	public function set_src($src) {
		$this->m_src = $src;
	}

	public function src() {
		return $this->m_src;
	}

	/**
	 * Returns thumbnail encoded with base64.
	 *
	 * @return string
	 */
	public function as_base64() {
		$ret = NULL;
		if(file_exists($this->src())) {
			$ret = file_get_contents($this->src());
		}
		return base64_encode($ret);
	}
}

