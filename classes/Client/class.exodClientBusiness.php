<?php
require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/classes/Auth/class.exodBearerToken.php');
require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/classes/Client/Item/class.exodItemFactory.php');
require_once('class.exodClientBase.php');
require_once('class.exodPath.php');

/**
 * Class exodClientBusiness
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class exodClientBusiness extends exodClientBase {

    public function addWritePermissionToFile($id, $email)
    {
        $this->setRequestType(self::REQ_TYPE_POST);
        $ressource = $this->getExodApp()->getRessource() . '/items/' . $id . '/invite';
        $this->setRessource($ressource);    // TODO: ressource stimmt noch nicht (wahrscheinlich brauchts api v 2)
        $this->setRequestContentType(exodCurl::JSON);
        $this->setPostfields([
            'roles' => ['write'],
            'requiresSignIn' => true,
            'sendInvitation' => false,
            'message' => '',
            'recipients' => [$email]
        ]);
        $this->request();
        return $this->getResponseBody();
    }

    /**
     * @param $folder_id
     *
     * @return exodFile[]|exodFolder[]
     * @throws ilCloudException
     */
	public function listFolder($parent_root_id) {
		$this->setRequestType(self::REQ_TYPE_GET);
        $ressource = $this->getExodApp()->getRessource() . '/files/' . $parent_root_id . '/children';
		$this->setRessource($ressource);
		$response = $this->getResponseJsonDecoded();

		return exodItemFactory::getInstancesFromResponse($response);
	}


	public function receivePathFromId($root_id) {
        $this->setRequestType(self::REQ_TYPE_GET);
        $ressource = $this->getExodApp()->getRessource() . '/files/' . $root_id;
        $this->setRessource($ressource);
        $response = $this->getResponseJsonDecoded();

        return $response->parentReference->path . "/" . $response->name;
    }


    /**
     * @param $id
     *
     * @return exodFile
     * @throws ilCloudException
     */
	public function getFileObject($id) {
		$this->setRequestType(self::REQ_TYPE_GET);
		$ressource = $this->getExodApp()->getRessource() . '/files/' . $id;
		$this->setRessource($ressource);
		$this->request();

		$n = new exodFile();
		$n->loadFromStdClass($this->getResponseJsonDecoded());

		return $n;
	}


    /**
     * @param $path
     *
     * @return exodFolder
     * @throws ilCloudException
     */
    public function getFolderObjectByPath($path) {
        $exodPath = exodPath::getInstance($path);
        $this->setRequestType(self::REQ_TYPE_GET);
        $ressource = $this->getExodApp()->getRessource() . '/files/getByPath(\''
            . $exodPath->getFullPath() . '\')';
        $this->setRessource($ressource);
        $this->request();

        $n = new exodFolder();
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
     * @param $id
     *
     * @return void
     * @throws ilCloudException
     */
	public function deliverFile($id) {
		$this->setRequestType(self::REQ_TYPE_GET);
		$this->setRessource($this->getExodApp()->getRessource() . '/files/'
		                    . $id);
		//		throw new ilCloudException(-1, $this->getRessource());

		$file = new exodFile();
		$file->loadFromStdClass($this->getResponseJsonDecoded());
		$this->setRessource($file->getContentUrl());

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
    public function createFolderByPath($path) {
        $exodPath = exodPath::getInstance($path);

        foreach ($exodPath->getParts() as $i => $p) {
            $pathToPart = $exodPath->getPathToPart($i);
            if (!$this->folderExists($pathToPart)) {

                return $this->createSingleFolderByPath($pathToPart);
            }
        }

        return true;
    }


    public function createFolderById($id, $folder_name) {
        return $this->createSingleFolderByRootId($id, $folder_name);
    }


    protected function createSingleFolderByPath($path) {
        $exodPath = exodPath::getInstance($path);

        $this->setRequestType(self::REQ_TYPE_GET);
        $this->setRessource($this->getExodApp()->getRessource() . '/files/getByPath(\''
            . $exodPath->getParentDirname() . '\')');
        $this->request();

        $folder = new exodFolder();
        $folder->loadFromStdClass(json_decode($this->getResponseBody()));

        $this->setRequestType(self::REQ_TYPE_PUT);
        $this->setRessource($this->getExodApp()->getRessource() . '/files/' . $folder->getId()
            . '/children/' . $exodPath->getBaseName());
        $response = $this->getResponseJsonDecoded();

        return $response->id;
    }

    protected function createSingleFolderByRootId($parent_root_id, $folder_name) {
        $this->setRequestType(self::REQ_TYPE_PUT);
        $this->setRessource($this->getExodApp()->getRessource() . '/files/' . $parent_root_id
            . '/children/' . rawurlencode($folder_name));
        $response = $this->getResponseJsonDecoded();

        return $response->id;
    }


    /**
     * @param $parent_folder_id
     * @param $name
     * @param $local_file_path
     *
     * @return bool
     * @throws ilCloudException
     */
	public function uploadFile($parent_folder_id, $name, $local_file_path) {
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$this->setRequestType(self::REQ_TYPE_PUT);
		$this->setRessource($this->getExodApp()->getRessource() . '/Files/' . $parent_folder_id
            . '/children/' . rawurlencode($name) . '/content');
		$this->setRequestFilePath($local_file_path);
		$request_content_type = finfo_file($finfo, $local_file_path);
		$this->setRequestContentType($request_content_type);
		$this->request();

		return true;
	}


    /**
     * @param $id
     *
     * @return bool
     * @throws ilCloudException
     */
	public function delete($id) {
		$this->setRessource($this->getExodApp()->getRessource() . '/files/'
		                    . $id);
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
