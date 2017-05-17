<?php
require_once('class.exodAuthResponseBusiness.php');
require_once('class.exodAuthResponsePublic.php');

/**
 * Class exodAuthResponseFactory
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class exodAuthResponseFactory {

	/**
	 * @param exodApp $app
	 *
	 * @return exodAuthResponseBusiness|exodAuthResponsePublic
	 * @throws ilCloudException
	 */
	public static function getResponseInstance(exodApp $app) {
		switch ($app->getType()) {
			case  exodApp::TYPE_BUSINESS:
				return exodAuthResponseBusiness::getInstance();
				break;
			case  exodApp::TYPE_PUBLIC:
				return exodAuthResponsePublic::getInstance();
				break;
		}
		throw new ilCloudException(ilCloudException::UNKNONW_EXCEPTION, 'No App Type Found');
	}
}

