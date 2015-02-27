<?php

/**
 * Class exodAuthResponse
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class exodAuthResponse {

	/**
	 * @var string
	 */
	protected $code = '';
	/**
	 * @var string
	 */
	protected $token_type = 'Bearer';
	/**
	 * @var string
	 */
	protected $expires_in = '';
	/**
	 * @var string
	 */
	protected $expires_on = '';
	/**
	 * @var string
	 */
	protected $not_before = '';
	/**
	 * @var string
	 */
	protected $resource = '';
	/**
	 * @var string
	 */
	protected $access_token = '';
	/**
	 * @var string
	 */
	protected $refresh_token = '';
	/**
	 * @var string
	 */
	protected $scope = '';
	/**
	 * @var string
	 */
	protected $id_token = '';
	/**
	 * @var string
	 */
	protected $error = '';
	/**
	 * @var string
	 */
	protected $error_description = '';
	/**
	 * @var exodAuthResponse
	 */
	protected static $instance;


	/**
	 * @return exodAuthResponse
	 */
	public static function getInstance() {
		if (!isset(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	const REQ_TYPE_GET = 1;
	const REQ_TYPE_POST = 2;
	const REQ_TYPE_BOTH = 3;


	/**
	 * @param array $fields
	 * @param int   $request_type
	 *
	 * @throws ilCloudException
	 */
	public function loadFromRequest($fields = array( 'code' ), $request_type = self::REQ_TYPE_GET) {
		$arr = array();
		switch ($request_type) {
			case self::REQ_TYPE_GET:
				$arr = $_GET;
				break;
			case self::REQ_TYPE_POST:
				$arr = $_POST;
				break;
			case self::REQ_TYPE_BOTH:
				$arr = $_REQUEST;
				break;
		}
		foreach ($fields as $field) {
			$this->{$field} = $arr[$field];
		}
		$this->checkError();
	}


	/**
	 * @param $response
	 *
	 * @throws ilCloudException
	 */
	public function loadFromResponse($response) {
		$response = json_decode($response);
		if (json_last_error()) {
			throw new ilCloudException(- 1, 'Wrong response from Server');
		}

		foreach ($response as $k => $field) {
			$this->{$k} = $response->$k;
		}
		$this->checkError();
	}


	/**
	 * @return string
	 */
	public function getCode() {
		return $this->code;
	}


	/**
	 * @param string $code
	 */
	public function setCode($code) {
		$this->code = $code;
	}


	/**
	 * @return string
	 */
	public function getTokenType() {
		return $this->token_type;
	}


	/**
	 * @param string $token_type
	 */
	public function setTokenType($token_type) {
		$this->token_type = $token_type;
	}


	/**
	 * @return string
	 */
	public function getExpiresIn() {
		return $this->expires_in;
	}


	/**
	 * @param string $expires_in
	 */
	public function setExpiresIn($expires_in) {
		$this->expires_in = $expires_in;
	}


	/**
	 * @return string
	 */
	public function getExpiresOn() {
		return $this->expires_on;
	}


	/**
	 * @param string $expires_on
	 */
	public function setExpiresOn($expires_on) {
		$this->expires_on = $expires_on;
	}


	/**
	 * @return string
	 */
	public function getNotBefore() {
		return $this->not_before;
	}


	/**
	 * @param string $not_before
	 */
	public function setNotBefore($not_before) {
		$this->not_before = $not_before;
	}


	/**
	 * @return string
	 */
	public function getResource() {
		return $this->resource;
	}


	/**
	 * @param string $resource
	 */
	public function setResource($resource) {
		$this->resource = $resource;
	}


	/**
	 * @return string
	 */
	public function getAccessToken() {
		return $this->access_token;
	}


	/**
	 * @param string $access_token
	 */
	public function setAccessToken($access_token) {
		$this->access_token = $access_token;
	}


	/**
	 * @return string
	 */
	public function getRefreshToken() {
		return $this->refresh_token;
	}


	/**
	 * @param string $refresh_token
	 */
	public function setRefreshToken($refresh_token) {
		$this->refresh_token = $refresh_token;
	}


	/**
	 * @return string
	 */
	public function getScope() {
		return $this->scope;
	}


	/**
	 * @param string $scope
	 */
	public function setScope($scope) {
		$this->scope = $scope;
	}


	/**
	 * @return string
	 */
	public function getIdToken() {
		return $this->id_token;
	}


	/**
	 * @param string $id_token
	 */
	public function setIdToken($id_token) {
		$this->id_token = $id_token;
	}


	/**
	 * @return string
	 */
	public function getError() {
		return $this->error;
	}


	/**
	 * @param string $error
	 */
	public function setError($error) {
		$this->error = $error;
	}


	/**
	 * @return string
	 */
	public function getErrorDescription() {
		return $this->error_description;
	}


	/**
	 * @param string $error_description
	 */
	public function setErrorDescription($error_description) {
		$this->error_description = $error_description;
	}


	protected function checkError() {
		if ($this->getError()) {
			throw new ilCloudException(ilCloudException::UNKNONW_EXCEPTION, $this->getErrorDescription());
		}
	}
}

/**
 * Class exodAuthResponseBusiness
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class exodAuthResponseBusiness extends exodAuthResponse {

}

?>
