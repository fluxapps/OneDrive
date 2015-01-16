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
