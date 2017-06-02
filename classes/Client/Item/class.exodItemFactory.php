<?php
require_once('class.exodFolder.php');
require_once('class.exodFile.php');
require_once('class.exodItemCache.php');

/**
 * Class exodItemFactory
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class exodItemFactory {

	/**
	 * @param stdClass $response
	 *
	 * @return exodFolder[]|exodFile[]
	 */
	public static function getInstancesFromResponse($response) {
		$return = array();
		if (count($response->value) == 0 OR !$response instanceof stdClass) {
			return $return;
		}

		foreach ($response->value as $item) {
			if ($item->type == 'Folder') {
				$exid_item = new exodFolder();
				$exid_item->loadFromStdClass($item);
				exodItemCache::store($exid_item);
				$return[] = $exid_item;
			} else {
				$exid_item = new exodFile();
				$exid_item->loadFromStdClass($item);
				exodItemCache::store($exid_item);
				$return[] = $exid_item;
			}
		}

		return $return;
	}
}

