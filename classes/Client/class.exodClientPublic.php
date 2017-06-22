<?php
// http://onedrive.github.io/misc/appfolder.htm
require_once('class.exodClientBase.php');
require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/classes/Client/class.exodPathPublic.php');

/**
 * Class exodClientPublic
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class exodClientPublic extends exodClientBase {

	/**
	 * @param $path
	 *
	 * @return exodFile[]|exodFolder[]
	 */
	public function listFolder($path) {
		$this->setRequestType(self::REQ_TYPE_GET);
		$exodPath = exodPathPublic::getInstance($path);
		$ressource = $this->getExodApp()->getRessource() . '/drive/special/approot:'
		             . $exodPath->getFullPath() . ':/children';
		//		$ressource = $this->getExodApp()->getRessource() . 'drive/special/approot:/children';
		//			throw new ilCloudException(-1, $ressource );
		$this->setRessource($ressource);

		$response = $this->getResponseJsonDecoded();
		//var_dump($response ); // FS
		//		exit;
		//		throw new ilCloudException(- 1, print_r($response, true));

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
		$this->setRessource($this->getExodApp()->getRessource() . '/drive/special/approot:' . rawurlencode($path));
		$this->setRequestContentType(exodCurl::JSON);
		$this->request();
		//		throw new ilCloudException(-1, $this->getRessource());

		$file = new exodFile();
		$file->loadFromStdClass($this->getResponseJsonDecoded());
		$this->setRessource($file->getContentUrl());
		$this->request();

		//		header("Content-type: " . $this->getResponseMimeType());
		header("Content-type: application/octet-stream");
		header('Content-Description: File Transfer');
		header('Content-Disposition: attachment; filename='
		       . str_replace(' ', '_', basename($file->getName())));
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
		$exodPath = exodPathPublic::getInstance($path);
		$ressource = $this->getExodApp()->getRessource() . '/drive/special/approot:' . $exodPath->getDirname();
		$this->setRessource($ressource);
		$this->request();

		$folder = new exodFolder();
		$folder->loadFromStdClass(json_decode($this->getResponseBody()));
		$this->setRequestType(self::REQ_TYPE_POST);
		$this->setRequestContentType(exodCurl::JSON);
		$this->setPostfields(array("name" => $exodPath->getBasename(), "folder" => new stdClass()));
		$this->setRessource($this->getExodApp()->getRessource() . '/drive/items/' . $folder->getFullId()
		                    . '/children');

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
		$exodPath = exodPathPublic::getInstance($location);
		$ressource = $this->getExodApp()->getRessource() . '/drive/special/approot:' . $exodPath->getDirname();
		$this->setRessource($ressource);
		$this->request();

		$folder = new exodFolder();
		$folder->loadFromStdClass(json_decode($this->getResponseBody()));

		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$this->setRequestType(self::REQ_TYPE_PUT);
		$this->setRessource($this->getExodApp()->getRessource() . '/drive/items/' . $folder->getFullId()
		                    . '/children/' . rawurlencode($exodPath->getBasename()) . '/content');
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
		$this->setRessource($this->getExodApp()->getRessource() . '/drive/special/approot:'
		                    . rawurlencode($path));
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
		//			throw new ilCloudException(-1, 'lorem');
		$exodPath = exodPathPublic::getInstance($path);
		$ressource = $this->getExodApp()->getRessource() . '/drive/special/approot:'
		             . $exodPath->getFullPath();
		$this->setRessource($ressource);
		try {
			$this->request();
		} catch (ilCloudException $e) {
			return false;
		}

		return true;
	}
}

