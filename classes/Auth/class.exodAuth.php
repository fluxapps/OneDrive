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
	 * @var exodAuthResponseBase
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
	 * @return exodAuthResponseBase
	 */
	public function getResponse() {
		return $this->response;
	}


	/**
	 * @param exodAuthResponseBase $response
	 */
	public function setResponse($response) {
		$this->response = $response;
	}


	/**
	 * @param $callback_url
	 */
	public function authenticate($callback_url) {
		ilSession::set(self::EXOD_CALLBACK_URL, $this->getExodApp()->getHttpPath() . $callback_url);
		$auth_url = $this->generateAuthUrl();

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
			$exodCurl = new exodCurl();
			$exodCurl->setUrl($this->exod_app->getTokenUrl());
			$exodCurl->setContentType(exodCurl::X_WWW_FORM_URL_ENCODED);

			$exodCurl->addPostField('code', $this->response->getCode());
			$exodCurl->addPostField('client_id', $this->exod_app->getClientId());
			$exodCurl->addPostField('redirect_uri', $this->exod_app->getRedirectUri());
			$exodCurl->addPostField('grant_type', 'authorization_code');
			$exodCurl->addPostField('client_secret', $this->getExodApp()->getClientSecret());
			$exodCurl->addPostField('resource', $this->exod_app->getRessourceUri());

			$exodCurl->post();
			$this->response->loadFromResponse($exodCurl->getResponseBody());

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
		//		throw new ilCloudException(-1, 'failed');
		$exodLog = exodLog::getInstance();
		$exodLog->write('refreshing-Token...');
		$this->exod_app->buildURLs();
		$exodCurl = new exodCurl();
		$exodCurl->setUrl($this->exod_app->getTokenUrl());
		$exodCurl->setContentType(exodCurl::X_WWW_FORM_URL_ENCODED);

		$exodCurl->addPostField('client_secret', $this->getExodApp()->getClientSecret());
		$exodCurl->addPostField('client_id', $this->exod_app->getClientId());
		$exodCurl->addPostField('grant_type', 'refresh_token');
		$exodCurl->addPostField('refresh_token', $exodBearerToken->getRefreshToken());
		$exodCurl->addPostField('resource', $this->exod_app->getRessourceUri());
		$exodCurl->addPostField('redirect_uri', $this->exod_app->getRedirectUri());
		$exodCurl->post();

		$this->response->loadFromResponse($exodCurl->getResponseBody());

		$exodBearerToken->setRefreshToken($this->response->getRefreshToken());
		$exodBearerToken->setAccessToken($this->response->getAccessToken());
		$exodBearerToken->setValidThrough($this->response->getExpiresOn());
		$this->setExodBearerToken($exodBearerToken);

		return true;
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
	 * @return string
	 */
	protected function generateAuthUrl() {
		$auth_url = $this->exod_app->getAuthUrl();
		$client_id = $this->exod_app->getClientId();
		$response_type = $this->exod_app->getResponseType();
		$redirect_uri = urlencode($this->exod_app->getRedirectUri());

		return "{$auth_url}?client_id={$client_id}&response_type={$response_type}&redirect_uri={$redirect_uri}";
	}
}
