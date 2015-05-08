<?php

/**
 * Class exodBearerToken
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class exodBearerToken {

	/**
	 * @var string
	 */
	protected $access_token = '';
	/**
	 * @var string
	 */
	protected $refresh_token = '';
	/**
	 * @var int
	 */
	protected $valid_through = 0;


	/**
	 * @return bool
	 */
	public function isValid() {
		if (! $this->getAccessToken()) {
			return true;
		}
		if ((int)$this->getValidThrough() <= time()) {
			$exodLog = exodLog::getInstance();
			$exodLog->write('Token no longer valid...');
			return false;
		}

		return true;
	}


	/**
	 * @return bool
	 */
	public function refresh(exodAuth $exodAuth) {
		if (! $this->getAccessToken()) {
			return false;
		}
		if (! $this->isValid()) {
			$exodAuth->refreshToken($this);

			return true;
		}

		return false;
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
	 * @return int
	 */
	public function getValidThrough() {
		return $this->valid_through;
	}


	/**
	 * @param int $valid_through
	 */
	public function setValidThrough($valid_through) {
		$this->valid_through = $valid_through;
	}
}

?>
