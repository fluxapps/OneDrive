<?php
require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/classes/Auth/class.exodBearerToken.php');
require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/classes/Client/Item/class.exodItemFactory.php');

/**
 * Class exodClientBusiness
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class exodClientBusiness extends exodClient {

	/**
	 * @param $folder_id
	 *
	 * @return exodFile[]|exodFolder[]
	 */
	public function listFolder($folder_id) {
		$folder_id = htmlspecialchars_decode($folder_id);
		$this->setRequestType(self::REQ_TYPE_GET);
		$ressource = $this->getApp()->getRessource() . '/files/getByPath(\'' . $folder_id . '\')/children';
		$this->setRessource($ressource);
		$response = $this->getResponseJsonDecoded();

		return exodItemFactory::getInstancesFromResponse($response);
	}


	/**
	 * @param $path
	 *
	 * @return exodFile
	 * @throws ilCloudException
	 */
	public function deliverFile($path) {
		$this->setRequestType(self::REQ_TYPE_GET);
		$this->setRessource($this->getApp()->getRessource() . '/files/getByPath(\'' . $path . '\')');

		$file = new exodFile();
		$file->loadFromStdClass($this->getResponseJsonDecoded());
		$this->setRessource($file->getContentUrl());

		header("Content-type: " . $this->getResponseMimeType());
		header('Content-Description: File Transfer');
		header('Content-Disposition: attachment; filename=' . str_replace(' ', '_', basename($file->getName())));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . $this->getResponseContentSize());
		echo $this->getResponseRaw();
		exit;
	}


	/**
	 * @param $path
	 *
	 * @return bool
	 */
	public function createFolder($path) {
		$this->setRequestType(self::REQ_TYPE_POST);
		$this->setRessource($this->getApp()->getRessource() . '/files/getByPath(\'' . $path . '\')');
		$this->setRequestContentLength(strlen($path));
		$this->setRequestBody(array( 'name' => 'testOrdner' ));
		$this->request();

		echo $this->getAccessToken();

		var_dump($this); // FSX

		return true;
	}
}


/**
 * Class exodClient
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class exodClient {

	const REQ_TYPE_GET = 'GET';
	const REQ_TYPE_POST = 'POST';
	const REQ_TYPE_DELETE = 'DELETE';
	const REQ_TYPE_PUT = 'PUT';
	/**
	 * @var exodApp
	 */
	protected $app;
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
	 * @param exodApp         $app
	 * @param exodBearerToken $token
	 */
	public function __construct(exodApp $app, exodBearerToken $token) {
		$app->buildURLs();
		$this->setApp($app);
		$this->setAccessToken($token->getAccessToken());
		$this->setRefreshToken($token->getRefreshToken());
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

		//		if ($this->getRequestType() == self::REQ_TYPE_POST) {
		//			$value = json_encode($this->getRequestBody());
		//			curl_setopt($ch, CURLOPT_HTTPHEADER, [
		//				"Authorization: Bearer " . $this->getAccessToken(),
		//				"Content-Length " . strlen($value),
		//				'Content-Type: application/json'
		//			]);
		//			curl_setopt($ch, CURLOPT_POSTFIELDS, $value);
		//		}
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
			"Authorization: Bearer " . $this->getAccessToken(),
//			"Content-Length " . $this->getRequestContentLength(),
		]);

		$resp_orig = curl_exec($ch);
		$this->setResponseBody($resp_orig);
		$this->setResponseMimeType(curl_getinfo($ch, CURLINFO_CONTENT_TYPE));
		$this->setResponseContentSize(curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD));
		$this->setResponseStatus(curl_getinfo($ch, CURLINFO_HTTP_CODE));
		$resp = json_decode($resp_orig);
		if ($resp->error) {
			throw new ilCloudException(- 1, $resp->error->message);
		}
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
}

?>
