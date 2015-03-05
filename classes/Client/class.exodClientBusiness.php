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
	 * @param $folder_id
	 *
	 * @return exodFile[]|exodFolder[]
	 */
	public function listFolder($folder_id) {
		$folder_id = htmlspecialchars_decode($folder_id);
		$this->setRequestType(self::REQ_TYPE_GET);
		$ressource = $this->getExodApp()->getRessource() . '/files/getByPath(\'' . $folder_id . '\')/children';
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
		$this->setRessource($this->getExodApp()->getRessource() . '/files/getByPath(\'' . $path . '\')');

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
		$this->setRequestType(self::REQ_TYPE_GET);
		$this->setRessource($this->getExodApp()->getRessource() . '/files/getByPath(\'' . dirname($path) . '\')');
		$this->request();

		$folder = new exodFolder();
		$folder->loadFromStdClass(json_decode($this->getResponseBody()));
		$path = ltrim($path, '/');
		$this->setRequestType(self::REQ_TYPE_PUT);
		$this->setRessource($this->getExodApp()->getRessource() . '/files/' . $folder->getId() . '/children/' . basename(rawurlencode($path)));
		//		$this->setRessource($this->getExodApp()->getRessource() . '/Files/getByPath("' . $path . '")');
		//		throw new ilCloudException(-1, $this->getRessource());
		//		$this->setRequestContentType('application/json');
		//		$req = array( 'name' => basename($path) );
		//		$this->setRequestBody(json_encode($req));
		//		$this->setRequestContentLength(strlen($this->getRequestBody()));
		$this->request();

		//		var_dump($this); // FSX
		//
		//		echo $this->getAccessToken();
		//

		return false;
	}


	/**
	 * @param $location
	 * @param $local_file_path
	 *
	 * @return bool
	 * @throws ilCloudException
	 */
	public function uploadFile($location, $local_file_path) {
		$location = ltrim($location, '/');
		$this->setRequestType(self::REQ_TYPE_GET);
		$dirname = dirname($location);
		if ($dirname == '.') {
			$dirname = '/';
		}
		$this->setRessource($this->getExodApp()->getRessource() . '/files/getByPath(\'' . rawurlencode($dirname) . '\')');
		//		throw new ilCloudException(-1, $this->getRessource());
		$this->request();
		//

		$folder = new exodFolder();
		$folder->loadFromStdClass(json_decode($this->getResponseBody()));

		$name = rawurlencode(basename($location));
		$content = file_get_contents($local_file_path);
		$this->setRequestType(self::REQ_TYPE_PUT);
		$this->setRessource($this->getExodApp()->getRessource() . '/Files/' . $folder->getId() . '/children/' . $name . '/content');
		//		throw new ilCloudException(-1, $this->getRessource());
		$this->setRequestBody($content);
		$this->setRequestContentType('text/plain');
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
}


?>
