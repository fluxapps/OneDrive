<?php

/**
 * Class exodAppBusiness
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class exodAppBusiness extends exodApp {

	/**
	 * @var int
	 */
	protected $type = self::TYPE_BUSINESS;
	/**
	 * @var string
	 */
	protected $tenant_id = '7b1ce663-ae89-4a2a-b34a-79142338f3f2';
	/**
	 * @var string
	 */
	protected $tenant_name = 'phzh';


	public function buildURLs() {
		$this->setBaseUrl('https://login.windows.net/' . $this->getTenantId() . '/oauth2/');
		$this->setAuthUrl($this->getBaseUrl() . 'authorize');
		$this->setTokenUrl($this->getBaseUrl() . 'token');
		$this->setRessourceUri('https://' . $this->getTenantName() . '-my.sharepoint.com/');
		$this->setRessource('https://' . $this->getTenantName() . '-my.sharepoint.com/_api/v1.0/me');
	}


	/**
	 * @return string
	 */
	public function getTenantId() {
		return $this->tenant_id;
	}


	/**
	 * @param string $tenant_id
	 */
	public function setTenantId($tenant_id) {
		$this->tenant_id = $tenant_id;
	}


	/**
	 * @return string
	 */
	public function getTenantName() {
		return $this->tenant_name;
	}


	/**
	 * @param string $tenant_name
	 */
	public function setTenantName($tenant_name) {
		$this->tenant_name = $tenant_name;
	}
}


/**
 * Class exodApp
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
abstract class exodApp {

	const TYPE_BUSINESS = 1;
	const TYPE_PUBLIC = 2;
	/**
	 * @var string
	 */

	protected $base_url = '';
	/**
	 * @var string
	 */
	protected $auth_url = '';
	/**
	 * @var string
	 */
	protected $token_url = '';
	/**
	 * @var string
	 */
	protected $client_id = '1bcd072d-05ec-4d61-89d7-bd8514172987';
	/**
	 * @var string
	 */
	protected $response_type = self::RESP_TYPE_CODE;
	/**
	 * @var string
	 */
	protected $redirect_uri = 'https://rel44.local/od_oauth.php';
	/**
	 * @var string
	 */
	protected $client_secret = 'LG5cK4keVdLN18HsG6wu6RfwGRcK1/rbquDG1bDI3LA=';
	/**
	 * @var string
	 */
	protected $ressource_uri = '';
	/**
	 * @var string
	 */
	protected $ressource = '';
	const RESP_TYPE_CODE = 'code';
	/**
	 * @var int
	 */
	protected $type = self::TYPE_BUSINESS;


	abstract public function buildURLs();


	/**
	 * @return string
	 */
	public function getRedirectUri() {
		return $this->redirect_uri;
	}


	/**
	 * @param string $redirect_uri
	 */
	public function setRedirectUri($redirect_uri) {
		$this->redirect_uri = $redirect_uri;
	}


	/**
	 * @return string
	 */
	public function getBaseUrl() {
		return $this->base_url;
	}


	/**
	 * @param string $base_url
	 */
	public function setBaseUrl($base_url) {
		$this->base_url = $base_url;
	}


	/**
	 * @return string
	 */
	public function getAuthUrl() {
		return $this->auth_url;
	}


	/**
	 * @param string $auth_url
	 */
	public function setAuthUrl($auth_url) {
		$this->auth_url = $auth_url;
	}


	/**
	 * @return string
	 */
	public function getTokenUrl() {
		return $this->token_url;
	}


	/**
	 * @param string $token_url
	 */
	public function setTokenUrl($token_url) {
		$this->token_url = $token_url;
	}


	/**
	 * @return string
	 */
	public function getClientId() {
		return $this->client_id;
	}


	/**
	 * @param string $client_id
	 */
	public function setClientId($client_id) {
		$this->client_id = $client_id;
	}


	/**
	 * @return string
	 */
	public function getResponseType() {
		return $this->response_type;
	}


	/**
	 * @param string $response_type
	 */
	public function setResponseType($response_type) {
		$this->response_type = $response_type;
	}


	/**
	 * @return string
	 */
	public function getClientSecret() {
		return $this->client_secret;
	}


	/**
	 * @param string $client_secret
	 */
	public function setClientSecret($client_secret) {
		$this->client_secret = $client_secret;
	}


	/**
	 * @return string
	 */
	public function getRessourceUri() {
		return $this->ressource_uri;
	}


	/**
	 * @param string $ressource_uri
	 */
	public function setRessourceUri($ressource_uri) {
		$this->ressource_uri = $ressource_uri;
	}


	/**
	 * @return string
	 */
	public function getRessource() {
		return $this->ressource;
	}


	/**
	 * @param string $ressource
	 */
	public function setRessource($ressource) {
		$this->ressource = $ressource;
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
