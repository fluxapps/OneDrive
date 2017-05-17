<?php
require_once('class.exodItem.php');

/**
 * Class exodFolder
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class exodFile extends exodItem {

	/**
	 * @var array
	 */
	protected static $ms_formats = array(
		'doc',
		'docx',
		'dot',
		'dotx',
		'xls',
		'xlsx',
		'xlt',
		'xltx',
		'ppt',
		'pptx',

	);
	/**
	 * @var int
	 */
	protected $type = self::TYPE_FILE;
	/**
	 * @var int
	 */
	protected $size = 0;
	/**
	 * @var string
	 */
	protected $content_url = '';


	/**
	 * @return bool
	 */
	public function isMsFormat() {
		return in_array($this->getSuffix(), self::$ms_formats);
	}


	/**
	 * @return null
	 */
	public function getMsURL() {
		if (!$this->isMsFormat()) {
			return null;
		}

		$re1 = '.*?';
		$re2 = '(\\{.*?\\})';

		if ($c = preg_match_all("/" . $re1 . $re2 . "/is", $this->getETag(), $matches)) {
			$cbraces1 = $matches[1][0];
			$strstr = strstr($this->getContentUrl(), '/_api', true)
			          . '/_layouts/15/WopiFrame.aspx?sourcedoc=' . rawurlencode($cbraces1)
			          . '&file=' . $this->getName() . '&action=default';

			return $strstr;
		} else {
			return null;
		}
	}


	/**
	 * @return mixed
	 */
	public function getSuffix() {
		return pathinfo($this->getName(), PATHINFO_EXTENSION);
	}


	/**
	 * @return int
	 */
	public function getSize() {
		return $this->size;
	}


	/**
	 * @param int $size
	 */
	public function setSize($size) {
		$this->size = $size;
	}


	/**
	 * @return string
	 */
	public function getContentUrl() {
		return $this->content_url;
	}


	/**
	 * @param string $content_url
	 */
	public function setContentUrl($content_url) {
		$this->content_url = $content_url;
	}
}

?>
