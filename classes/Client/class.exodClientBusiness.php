<?php
require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/classes/Auth/class.exodBearerToken.php');
require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/classes/Client/Item/class.exodItemFactory.php');
require_once('class.exodClientBase.php');

/**
 * Class exodClientBusiness
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class exodClientBusiness extends exodClientBase {

	/**
	 * @param $id
	 *
	 * @return exodFile[]|exodFolder[]
	 */
	public function listFolder($id) {
		$id = rawurlencode($id);
		$this->setRequestType(self::REQ_TYPE_GET);
		$ressource = $this->getExodApp()->getRessource() . '/files/getByPath(\'' . $id . '\')/children';
		$this->setRessource($ressource);
		$response = $this->getResponseJsonDecoded();
		//var_dump($response ); // FS
		//		exit;
		//		throw new ilCloudException(-1, $response );
		return exodItemFactory::getInstancesFromResponse($response);
	}


	/**
	 * @param $id
	 *
	 * @return exodFile
	 * @throws ilCloudException
	 */
	public function getFileObject($id) {
		$this->setRequestType(self::REQ_TYPE_GET);
		$ressource = $this->getExodApp()->getRessource() . '/files/' . $id . '';
		$this->setRessource($ressource);
		//		throw new ilCloudException(-1, $ressource );
		$this->request();

		$n = new exodFile();
		$n->loadFromStdClass($this->getResponseJsonDecoded());

		return $n;
	}


	/**
	 * @param $path
	 *
	 * @return bool
	 */
	public function folderExists($path) {
		return $this->itemExists($path);
	}


	/**
	 * @param $path
	 *
	 * @return bool
	 */
	public function fileExists($path) {
		return $this->itemExists($path);
	}


	/**
	 * @param $path
	 *
	 * @return exodFile
	 * @throws ilCloudException
	 */
	public function deliverFile($path) {
		$this->setRequestType(self::REQ_TYPE_GET);
		$this->setRessource($this->getExodApp()->getRessource() . '/files/getByPath(\'' . rawurlencode($path) . '\')');
		//		throw new ilCloudException(-1, $this->getRessource());

		$file = new exodFile();
		$file->loadFromStdClass($this->getResponseJsonDecoded());
		$this->setRessource($file->getContentUrl());

		//		header("Content-type: " . $this->getResponseMimeType());
		header("Content-type: application/octet-stream");
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
		$this->setRequestType(self::REQ_TYPE_GET);
		$this->setRessource($this->getExodApp()->getRessource() . '/files/getByPath(\'' . rawurlencode(dirname($path)) . '\')');
		$this->request();

		$folder = new exodFolder();
		$folder->loadFromStdClass(json_decode($this->getResponseBody()));
		$path = ltrim($path, '/');
		$this->setRequestType(self::REQ_TYPE_PUT);
		$this->setRessource($this->getExodApp()->getRessource() . '/files/' . $folder->getId() . '/children/' . rawurlencode(basename($path)));

		$this->request();

		return true;
	}


	/**
	 * @param $location
	 * @param $local_file_path
	 *
	 * @return bool
	 * @throws ilCloudException
	 */
	public function uploadFile($location, $local_file_path) {
		$this->setRequestType(self::REQ_TYPE_GET);
		$dirname = dirname($location);
		if ($dirname == '.') {
			$dirname = '/';
		}
		$this->setRessource($this->getExodApp()->getRessource() . '/files/getByPath(\'' . rawurlencode($dirname) . '\')');
		$this->request();

		$folder = new exodFolder();
		$folder->loadFromStdClass(json_decode($this->getResponseBody()));
		$name = rawurlencode(basename($location));

		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$this->setRequestType(self::REQ_TYPE_PUT);
		$this->setRessource($this->getExodApp()->getRessource() . '/Files/' . $folder->getId() . '/children/' . $name . '/content');
		$this->setRequestFilePath($local_file_path);
		$request_content_type = finfo_file($finfo, $local_file_path);
		$this->setRequestContentType($request_content_type);
		$this->request();

		return true;
	}


	/**
	 * @param $path
	 *
	 * @return bool
	 */
	public function delete($path) {
		$this->setRessource($this->getExodApp()->getRessource() . '/files/getByPath(\'' . rawurlencode($path) . '\')');
		$this->setRequestType(self::REQ_TYPE_GET);
		$this->request();

		$folder = new exodFile();
		$folder->loadFromStdClass(json_decode($this->getResponseBody()));

		$this->setRequestEtag($folder->getETag());
		$this->setRequestType(self::REQ_TYPE_DELETE);
		$this->request();

		return true;
	}


	/**
	 * @param $path
	 *
	 * @return bool
	 */
	protected function itemExists($path) {
		$path = rawurlencode($path);
		$ressource = $this->getExodApp()->getRessource() . '/files/getByPath(\'' . $path . '\')';
		$this->setRessource($ressource);
		try {
			$this->request();
		} catch (ilCloudException $e) {
			return false;
		}

		return true;
	}
}


?>
