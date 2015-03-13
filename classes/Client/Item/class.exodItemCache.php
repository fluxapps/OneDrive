<?php

/**
 * Class exodItemCache
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class exodItemCache {

	/**
	 * @var array
	 */
	protected static $instances = array();


	/**
	 * @param exodItem $exodItem
	 */
	public static function store(exodItem $exodItem) {
		self::$instances[$exodItem->getId()] = $exodItem;
	}


	/**
	 * @param $id
	 *
	 * @return bool
	 */
	public static function exists($id) {
		return (self::$instances[$id] instanceof exodItem);
	}


	/**
	 * @param $id
	 *
	 * @return exodItem
	 */
	public static function get($id) {
		if (self::exists($id)) {
			return self::$instances[$id];
		}

		return NULL;
	}
}

?>
