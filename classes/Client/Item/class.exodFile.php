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
			return NULL;
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
