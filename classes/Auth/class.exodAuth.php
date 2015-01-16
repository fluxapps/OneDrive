<?php
require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/classes/Auth/Response/class.exodAuthResponseFactory.php');
require_once('./Modules/Cloud/exceptions/class.ilCloudException.php');
require_once('./Services/Authentication/classes/class.ilSession.php');

/**
 * Class exodAuth
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class exodAuth {

	/**
	 * @var exodApp
	 */
	protected $app;
	/**
	 * @var exodAuthResponse
	 */
	protected $response;
	/**
	 * @var exodAuth
	 */
	protected static $instance;


	/**
	 * @param exodApp $app
	 *
	 * @return exodAuth
	 */
	public static function getInstance(exodApp $app) {
		if (!isset(self::$instance)) {
			self::$instance = new self($app);
		}

		return self::$instance;
	}


	/**
	 * @return exodAuth
	 * @throws ilCloudException
	 */
	public static function getInstanceFromSession() {
		$obj = unserialize(ilSession::get('exod_auth'));
		if (!$obj instanceof exodAuth) {
			throw new ilCloudException(ilCloudException::UNKNONW_EXCEPTION, 'No Auth in Session');
		}

		return $obj;
	}



	/**
	 * @param exodApp $app
	 *
	 * @throws ilCloudException
	 */
	protected function __construct(exodApp $app) {
		$this->setApp($app);
		$this->setResponse(exodAuthResponseFactory::getResponseInstance($this->getApp()));
	}


	/**
	 * @return exodApp
	 */
	public function getApp() {
		return $this->app;
	}


	/**
	 * @param exodApp $app
	 */
	public function setApp($app) {
		$this->app = $app;
	}


	/**
	 * @return exodAuthResponse
	 */
	public function getResponse() {
		return $this->response;
	}


	/**
	 * @param exodAuthResponse $response
	 */
	public function setResponse($response) {
		$this->response = $response;
	}


	/**
	 * @param $callback_url
	 */
	public function authenticate($callback_url) {
		ilSession::set('exod_callback_url', $callback_url);
		$this->app->buildURLs();
		$auth_url = $this->app->getAuthUrl();
		$client_id = $this->app->getClientId();
		$response_type = $this->app->getResponseType();
		$redirect_uri = urlencode($this->app->getRedirectUri());

		$auth_url = "{$auth_url}?client_id={$client_id}&response_type={$response_type}&redirect_uri={$redirect_uri}";

		header("Location: " . $auth_url);
	}


	public function redirectToObject() {
		$this->getTokens();
		$this->storeToSession();
		ilUtil::redirect(ilSession::get('exod_callback_url'));
	}


	protected function storeToSession() {
		ilSession::set('exod_auth', serialize($this));
	}


	/**
	 * @return array
	 * @throws ilCloudException
	 */
	protected function getTokens() {
		$this->response->loadFromRequest([ 'code' ]);
		if ($this->response->getCode()) {
			$this->app->buildURLs();
			$code = $this->response->getCode();
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $this->app->getTokenUrl());
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, [
				"Content-Type: application/x-www-form-urlencoded",
			]);
			$client_id = $this->app->getClientId();
			$redirect_uri = $this->app->getRedirectUri();
			$client_secret = $this->app->getClientSecret();
			$ressource_uri = $this->app->getRessourceUri();
			$body = "code={$code}&client_id={$client_id}&redirect_uri={$redirect_uri}&grant_type=authorization_code&client_secret={$client_secret}&resource={$ressource_uri}&";

			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

			$this->response->loadFromResponse(json_decode(curl_exec($ch)));

			return [ 'access_token' => $this->response->getAccessToken(), 'refresh_token' => $this->response->getRefreshToken() ];
		} else {
			throw new ilCloudException(ilCloudException::UNKNONW_EXCEPTION, 'No Code received');
		}
	}
}

?>
