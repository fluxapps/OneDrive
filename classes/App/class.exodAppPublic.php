<?php
require_once('class.exodApp.php');
require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/classes/App/class.exodTenant.php');

/**
 * Class exodAppPublic
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class exodAppPublic extends exodApp {

	/**
	 * @var int
	 */
	protected $type = self::TYPE_PUBLIC;


	public function buildURLs() {
		$this->setBaseUrl('https://login.live.com/');
		$this->setAuthUrl($this->getBaseUrl() . 'oauth20_authorize.srf');
		$this->setTokenUrl($this->getBaseUrl() . 'oauth20_token.srf');
		$this->setRessourceUri('https://api.onedrive.com');
		$this->setRessource('https://api.onedrive.com/v1.0');
	}


	/**
	 * @param exodBearerToken $exod_bearer_token
	 * @param                 $client_id
	 * @param                 $client_secret
	 *
	 * @return exodAppPublic
	 */
	public static function getInstance(exodBearerToken $exod_bearer_token, $client_id, $client_secret) {
		self::$instance = new self($exod_bearer_token, $client_id, $client_secret);

		return self::$instance;
	}


	protected function initRedirectUri() {
		$this->setRedirectUri($this->getHttpPath()
		                      . 'Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/redirect.php');
	}
}

?>
