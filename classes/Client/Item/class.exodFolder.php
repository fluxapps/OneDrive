<?php
require_once('class.exodItem.php');

/**
 * Class exodFolder
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class exodFolder extends exodItem {

	/**
	 * @var int
	 */
	protected $type = self::TYPE_FOLDER;
	/**
	 * @var int
	 */
	protected $child_count = 0;


	/**
	 * @return int
	 */
	public function getChildCount() {
		return $this->child_count;
	}


	/**
	 * @param int $child_count
	 */
	public function setChildCount($child_count) {
		$this->child_count = $child_count;
	}


	/**
	 * @return int
	 */
	public function getType() {
		return $this->type;
	}


	/**
	 * @param int $type
	 */
	public function setType($type) {
		$this->type = $type;
	}
}

?>
