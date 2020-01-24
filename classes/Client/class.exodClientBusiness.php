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

    /**
     * @param $folder_id
     *
     * @return exodFile[]|exodFolder[]
     * @throws ilCloudException
     */
	public function listFolder($parent_root_id) {
		$this->setRequestType(self::REQ_TYPE_GET);
        $ressource = $this->getExodApp()->getRessource() . '/drive/items/' . $parent_root_id . '/children';
		$this->setRessource($ressource);
		$response = $this->getResponseJsonDecoded();

		return exodItemFactory::getInstancesFromResponse($response);
	}


    /**
     * @param $root_id
     *
     * @return string
     * @throws ilCloudException
     */public function receivePathFromId($root_id) {
        $this->setRequestType(self::REQ_TYPE_GET);
        $ressource = $this->getExodApp()->getRessource() . '/drive/items/' . $root_id;
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
        $ressource = $this->getExodApp()->getRessource() . '/drive/items/' . $id;
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
        $ressource = $this->getExodApp()->getRessource() . '/drive/items/getByPath(\''
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
        $this->setRessource($this->getExodApp()->getRessource() . '/drive/items/'
		                    . $id);

		$file = new exodFile();
		$file->loadFromStdClass($this->getResponseJsonDecoded());
		header('Location: ' . $file->getContentUrl());
		exit;
	}


    /**
     * @param $path
     *
     * @return bool|int
     * @throws ilCloudException
     */
    public function createFolderByPath($path) {
        $exodPath = exodPath::getInstance($path);
        $root_id = true;

        foreach ($exodPath->getParts() as $i => $p) {
            $pathToPart = $exodPath->getPathToPart($i);
            if (!$this->folderExists($pathToPart)) {

                $root_id = $this->createSingleFolderByPath($pathToPart);
            }
        }

        return $root_id;
    }


    /**
     * @param $id
     * @param $folder_name
     *
     * @return mixed
     */
    public function createFolderById($id, $folder_name) {
        return $this->createSingleFolderByRootId($id, $folder_name);
    }


    /**
     * @param $path
     *
     * @return mixed
     * @throws ilCloudException
     */
    protected function createSingleFolderByPath($path) {
        $exodPath = exodPath::getInstance($path);

        $this->setRequestType(self::REQ_TYPE_GET);
        $this->setRessource($this->getExodApp()->getRessource() . '/drive/items/getByPath(\''
            . $exodPath->getParentDirname() . '\')');
        $this->request();

        $folder = new exodFolder();
        $folder->loadFromStdClass(json_decode($this->getResponseBody()));

        $this->setRequestType(self::REQ_TYPE_POST);
        $this->setRequestContentType(exodCurl::JSON);
        $this->setPostfields(
            [
                "name"   => rawurldecode($exodPath->getBaseName()),
                "folder" => new stdClass(),
            ]
        );
        $this->setRessource($this->getExodApp()->getRessource() . '/drive/items/' . $folder->getId()
            . '/children');
        $response = $this->getResponseJsonDecoded();

        return $response->id;
    }


    /**
     * @param $parent_root_id
     * @param $folder_name
     *
     * @return mixed
     * @throws ilCloudException
     */
    protected function createSingleFolderByRootId($parent_root_id, $folder_name) {
        $this->setRequestType(self::REQ_TYPE_POST);
        $this->setRequestContentType(exodCurl::JSON);
        $this->setPostfields(
            [
                "name"   => $folder_name,
                "folder" => new stdClass(),
            ]
        );
        $this->setRessource($this->getExodApp()->getRessource() . '/drive/items/' . $parent_root_id
            . '/children');
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
        $this->setRessource($this->getExodApp()->getRessource() . '/drive/items/' . $parent_folder_id
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
        $this->setRessource($this->getExodApp()->getRessource() . '/drive/items/'
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
        $ressource = $this->getExodApp()->getRessource() . '/drive/items/getByPath(\'' . $path . '\')';
		$this->setRessource($ressource);
		try {
			$this->request();
		} catch (ilCloudException $e) {
			return false;
		}

		return true;
	}


    /**
     * @param $id
     *
     * @return mixed
     * @throws ilCloudException
     */
    public function createSharingLink($id)
    {
        $this->setRequestType(self::REQ_TYPE_POST);
        $this->setRequestContentType(exodCurl::JSON);
        $this->setPostfields(
            [
                "type"  => "view",
                "scope" => "anonymous",
            ]
        );
        $this->setRessource($this->getExodApp()->getRessource() . '/drive/items/' . $id
            . '/createLink');
        $response = $this->getResponseJsonDecoded();

        return $response->link->webUrl;
    }


    /**
     * @param $id
     * @param $title
     *
     * @return bool
     * @throws ilCloudException
     */
    public function renameItemById($id, $title)
    {
        $this->setRequestType(self::REQ_TYPE_PATCH);
        $this->setRequestContentType(exodCurl::JSON);
        $this->setPostfields(
            [
                "name"  => $title,
            ]
        );
        $this->setRessource($this->getExodApp()->getRessource() . '/drive/items/' . $id);
        $this->request();

        return true;
    }


    /**
     * @param $id
     * @param $email
     *
     * @return string
     * @throws ilCloudException
     */
    public function addWritePermissionToFile($id, $email)
    {
        $this->setRequestType(self::REQ_TYPE_POST);
        $this->setRessource($this->getExodApp()->getRessource() . '/drive/items/' . $id . '/invite');
        $this->setRequestContentType(exodCurl::JSON);
        $this->setPostfields([
            'roles' => ['write'],
            'requireSignIn' => true,
            'sendInvitation' => false,
            'message' => '',
            'recipients' => [['email' => $email]]
        ]);
        $this->request();
        return $this->getResponseBody();
    }

}
