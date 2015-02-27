<?php
require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/classes/Auth/Response/class.exodAuthResponseFactory.php');
require_once('./Modules/Cloud/exceptions/class.ilCloudException.php');
require_once('./Services/Authentication/classes/class.ilSession.php');
require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/classes/Auth/class.exodBearerToken.php');
require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/classes/Auth/class.exodCurl.php');

/**
 * Class exodAuth
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class exodAuth {

	const EXOD_AUTH_BEARER = 'exod_auth_bearer';
	const EXOD_CALLBACK_URL = 'exod_callback_url';
	/**
	 * @var exodBearerToken
	 */
	protected $exod_bearer_token;
	/**
	 * @var exodApp
	 */
	protected $exod_app;
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
	 * @throws ilCloudException
	 */
	public function __construct(exodApp $app) {
		$this->setExodApp($app);
		$this->setExodBearerToken($app->getExodBearerToken());
		$this->setResponse(exodAuthResponseFactory::getResponseInstance($this->getExodApp()));
	}


	/**
	 * @return exodApp
	 */
	public function getExodApp() {
		return $this->exod_app;
	}


	/**
	 * @param exodApp $exod_app
	 */
	public function setExodApp($exod_app) {
		$this->exod_app = $exod_app;
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
		ilSession::set(self::EXOD_CALLBACK_URL, $callback_url);
		$auth_url = $this->exod_app->getAuthUrl();
		$client_id = $this->exod_app->getClientId();
		$response_type = $this->exod_app->getResponseType();
		$redirect_uri = urlencode($this->exod_app->getRedirectUri());

		$auth_url = "{$auth_url}?client_id={$client_id}&response_type={$response_type}&redirect_uri={$redirect_uri}";

		header("Location: " . $auth_url);
	}


	public function redirectToObject() {
		$this->loadToken();
		$this->storeTokenToSession();
		ilUtil::redirect(ilSession::get(self::EXOD_CALLBACK_URL));
	}


	protected function storeTokenToSession() {
		ilSession::set(self::EXOD_AUTH_BEARER, serialize($this->getExodBearerToken()));
	}


	public function loadTokenFromSession() {
		$exod_bearer_token = unserialize(ilSession::get(self::EXOD_AUTH_BEARER));
		if ($exod_bearer_token instanceof exodBearerToken) {
			$this->setExodbearerToken($exod_bearer_token);
		}
	}


	/**
	 * @throws ilCloudException
	 */
	protected function loadToken() {
		$this->response->loadFromRequest(array( 'code' ));
		if ($this->response->getCode()) {
			$this->exod_app->buildURLs();
			$code = $this->response->getCode();
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $this->exod_app->getTokenUrl());
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"Content-Type: application/x-www-form-urlencoded",
			));
			$client_id = $this->exod_app->getClientId();
			$redirect_uri = $this->exod_app->getRedirectUri();
			$client_secret = $this->exod_app->getClientSecret();
			$ressource_uri = $this->exod_app->getRessourceUri();
			$body = "code={$code}&client_id={$client_id}&redirect_uri={$redirect_uri}&grant_type=authorization_code&client_secret={$client_secret}&resource={$ressource_uri}&";

			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

			$curl_exec = $this->execute($ch);
			$this->response->loadFromResponse($curl_exec);

			$exodBearerToken = new exodBearerToken();
			$exodBearerToken->setAccessToken($this->getResponse()->getAccessToken());
			$exodBearerToken->setRefreshToken($this->getResponse()->getRefreshToken());
			$exodBearerToken->setValidThrough($this->getResponse()->getExpiresOn());
			$this->setExodBearerToken($exodBearerToken);
		} else {
			throw new ilCloudException(ilCloudException::UNKNONW_EXCEPTION, 'No Code received');
		}
	}


	/**
	 * @param exodBearerToken $exodBearerToken
	 *
	 * @return bool
	 */
	public function refreshToken(exodBearerToken &$exodBearerToken) {
		$this->exod_app->buildURLs();
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->exod_app->getTokenUrl());
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Content-Type: application/x-www-form-urlencoded",
		));
		$client_id = $this->exod_app->getClientId();
		$ressource_uri = $this->exod_app->getRessourceUri();
		$refresh_token = $exodBearerToken->getRefreshToken();
		$client_secret = $this->getExodApp()->getClientSecret();

		$body = "client_secret={$client_secret}&client_id={$client_id}&grant_type=refresh_token&refresh_token={$refresh_token}&resource={$ressource_uri}";

		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

		$curl_exec = $this->execute($ch);
		$this->response->loadFromResponse($curl_exec);

		$exodBearerToken->setRefreshToken($this->response->getRefreshToken());
		$exodBearerToken->setAccessToken($this->response->getAccessToken());
		$exodBearerToken->setValidThrough($this->response->getExpiresOn());
		$this->setExodBearerToken($exodBearerToken);
	}


	/**
	 * @return exodBearerToken
	 */
	public function getExodBearerToken() {
		return $this->exod_bearer_token;
	}


	/**
	 * @param exodBearerToken $exod_bearer_token
	 */
	public function setExodBearerToken($exod_bearer_token) {
		$this->exod_bearer_token = $exod_bearer_token;
	}


	/**
	 * @param $ch
	 *
	 * @return mixed
	 * @throws ilCloudException
	 */
	protected function execute($ch) {
		$curl_exec = curl_exec($ch);
		$curl_errno = curl_errno($ch);
		if ($curl_errno) {
			throw new ilCloudException(- 1, exodCurl::getErrorText($ch) . ': ' . curl_error($ch));
		}

		return $curl_exec;
	}
}

?>
