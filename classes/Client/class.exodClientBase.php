<?php


/**
 * Class exodClientBase
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
abstract class exodClientBase {

	const DEBUG = true;
	const REQ_TYPE_GET = 'GET';
	const REQ_TYPE_POST = 'POST';
	const REQ_TYPE_DELETE = 'DELETE';
	const REQ_TYPE_PUT = 'PUT';
	/**
	 * @var exodApp
	 */
	protected $exod_app;
	/**
	 * @var string
	 */
	protected $ressource = '';
	/**
	 * @var string
	 */
	protected $request_type = self::REQ_TYPE_GET;
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
	protected $response_mime_type = '';
	/**
	 * @var string
	 */
	protected $response_status = '';
	/**
	 * @var int
	 */
	protected $response_content_size = 0;
	/**
	 * @var string
	 */
	protected $request_body = '';
	/**
	 * @var string
	 */
	protected $response_body = '';
	/**
	 * @var int
	 */
	protected $request_content_length = 0;
	/**
	 * @var string
	 */
	protected $request_content_type = '';
	/**
	 * @var string
	 */
	protected $request_etag = '';
	/**
	 * @var string
	 */
	protected $request_file_path = '';


	/**
	 * @param exodApp $exodApp
	 */
	public function __construct(exodApp $exodApp) {
		$this->setExodApp($exodApp);
		$this->setAccessToken($exodApp->getExodBearerToken()->getAccessToken());
		$this->setRefreshToken($exodApp->getExodBearerToken()->getRefreshToken());
	}


	/**
	 * @return stdClass
	 * @throws ilCloudException
	 */
	protected function getResponseJsonDecoded() {
		$this->request();

		return json_decode($this->getResponseBody());
	}


	/**
	 * @return string
	 * @throws ilCloudException
	 */
	protected function getResponseRaw() {
		$this->request();

		return $this->getResponseBody();
	}


	/**
	 * @return mixed
	 * @throws ilCloudException
	 */
	protected function request() {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->getRessource());
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->getRequestType());
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSLVERSION, 3);

		$headers = array(
			"Authorization: Bearer " . $this->getAccessToken(),
		);

		switch ($this->getRequestType()) {
			case self::REQ_TYPE_GET:
				curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
				break;
			case self::REQ_TYPE_PUT:
				curl_setopt($ch, CURLOPT_PUT, true);
				$fh_res = fopen($this->getRequestFilePath(), 'r');
				curl_setopt($ch, CURLOPT_INFILE, $fh_res);
				curl_setopt($ch, CURLOPT_INFILESIZE, filesize($this->getRequestFilePath()));
				break;
			case self::REQ_TYPE_DELETE:
				$headers[] = 'if-match: ' . $this->getRequestEtag() . '';
				break;
		}

		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$resp_orig = curl_exec($ch);
		if (! $resp_orig) {
			throw new ilCloudException(- 1, curl_error($ch));
		}
		$this->setResponseBody($resp_orig);
		$this->setResponseMimeType(curl_getinfo($ch, CURLINFO_CONTENT_TYPE));
		$this->setResponseContentSize(curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD));
		$this->setResponseStatus(curl_getinfo($ch, CURLINFO_HTTP_CODE));
		$resp = json_decode($resp_orig);

		if ($this->getResponseStatus() == 401) {
			throw new ilCloudException(998, 'token invalid');
		}

		if ($resp->error) {
			if (self::DEBUG) {
				throw new ilCloudException(- 1, print_r($resp, true));
			} else {

				throw new ilCloudException(- 1, $resp->error->message);
			}
		}
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
	 * @return string
	 */
	public function getRequestType() {
		return $this->request_type;
	}


	/**
	 * @param string $request_type
	 */
	public function setRequestType($request_type) {
		$this->request_type = $request_type;
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
	public function getResponseMimeType() {
		return $this->response_mime_type;
	}


	/**
	 * @param string $response_mime_type
	 */
	public function setResponseMimeType($response_mime_type) {
		$this->response_mime_type = $response_mime_type;
	}


	/**
	 * @return string
	 */
	public function getResponseStatus() {
		return $this->response_status;
	}


	/**
	 * @param string $response_status
	 */
	public function setResponseStatus($response_status) {
		$this->response_status = $response_status;
	}


	/**
	 * @return int
	 */
	public function getResponseContentSize() {
		return $this->response_content_size;
	}


	/**
	 * @param int $response_content_size
	 */
	public function setResponseContentSize($response_content_size) {
		$this->response_content_size = $response_content_size;
	}


	/**
	 * @return string
	 */
	public function getRequestBody() {
		return $this->request_body;
	}


	/**
	 * @param string $request_body
	 */
	public function setRequestBody($request_body) {
		$this->request_body = $request_body;
	}


	/**
	 * @return string
	 */
	public function getResponseBody() {
		return $this->response_body;
	}


	/**
	 * @param string $response_body
	 */
	public function setResponseBody($response_body) {
		$this->response_body = $response_body;
	}


	/**
	 * @return int
	 */
	public function getRequestContentLength() {
		return $this->request_content_length;
	}


	/**
	 * @param int $request_content_length
	 */
	public function setRequestContentLength($request_content_length) {
		$this->request_content_length = $request_content_length;
	}


	/**
	 * @return string
	 */
	public function getRequestContentType() {
		return $this->request_content_type;
	}


	/**
	 * @param string $request_content_type
	 */
	public function setRequestContentType($request_content_type) {
		$this->request_content_type = $request_content_type;
	}


	/**
	 * @return string
	 */
	public function getRequestEtag() {
		return $this->request_etag;
	}


	/**
	 * @param string $request_etag
	 */
	public function setRequestEtag($request_etag) {
		$this->request_etag = $request_etag;
	}


	/**
	 * @return string
	 */
	public function getRequestFilePath() {
		return $this->request_file_path;
	}


	/**
	 * @param string $request_file_path
	 */
	public function setRequestFilePath($request_file_path) {
		$this->request_file_path = $request_file_path;
	}
}

?>
