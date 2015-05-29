<?php

/**
 * Class exodItemCache
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class exodItemCache {

	const EXOD_ITEM_CACHE = 'exod_item_cache';
	/**
	 * @var array
	 */
	protected static $instances = array();


	/**
	 * @param exodItem $exodItem
	 */
	public static function store(exodItem $exodItem) {
		$_SESSION[self::EXOD_ITEM_CACHE][$exodItem->getId()] = serialize($exodItem);
	}


	/**
	 * @param $id
	 *
	 * @return bool
	 */
	public static function exists($id) {
		return (unserialize($_SESSION[self::EXOD_ITEM_CACHE][$id]) instanceof exodItem);
	}


	/**
	 * @param $id
	 *
	 * @return exodItem
	 */
	public static function get($id) {
		if (self::exists($id)) {
			return unserialize($_SESSION[self::EXOD_ITEM_CACHE][$id]);
		}

		return NULL;
	}
}

?>
