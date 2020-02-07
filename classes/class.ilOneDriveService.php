<?php
require_once("./Modules/Cloud/classes/class.ilCloudPluginService.php");
require_once('./Modules/Cloud/exceptions/class.ilCloudException.php');
require_once("./Modules/Cloud/classes/class.ilCloudUtil.php");
require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/classes/Client/class.exodClientFactory.php');

/**
 * Class ilExampleCloudService
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class ilOneDriveService extends ilCloudPluginService {

	/**
	 * @return exodAppBusiness
	 */
	public function getApp() {
		return $this->getPluginObject()->getExodApp();
	}


	/**
	 * @return exodClientBusiness|exodClientPublic
	 */
	public function getClient() {
		return $this->getApp()->getExodClient();
	}


	/**
	 * @return exodAuth
	 */
	public function getAuth() {
		return $this->getApp()->getExodAuth();
	}


    /**
     * @param string $callback_url
     *
     * @throws Exception
     */
	public function authService($callback_url = "") {
	    if ($this->getPluginObject()->getTokenObject()->isValid() || $this->getApp()) {
	        global $DIC;
	        $this->afterAuthService();
	        $DIC->ctrl()->redirectToURL(htmlspecialchars_decode($callback_url));
        }

		$this->getAuth()->authenticate(htmlspecialchars_decode($callback_url));
	}


    /**
     * @return bool
     * @throws ilCloudException
     */
    public function afterAuthService() {
        $this->getAuth()->setExodBearerToken($this->getPluginObject()->getTokenObject());
        $this->getPluginObject()->setAllowPublicLinks(true);
		$ilObjCloud = $this->getPluginObject()->getCloudModulObject();
		$this->setAuthComplete();
		$rootFolder = $ilObjCloud->getRootFolder();

		// If root id is either missing or contains unused "root" fix it
		if (empty($ilObjCloud->getRootId()) || $ilObjCloud->getRootId() == "root") {
		    // If the root folder path doesn't even exist in OneDrive -> new object
            if (!$this->getClient()->folderExists($rootFolder)) {
                $rootId = $this->createFolder($rootFolder);
            } else {
                // If the root folder exists but there is no ID -> Old OneDrive object
                // that needs its root id updated
                $rootId = $this->getFolderObjectByPath($rootFolder)->getId();

                if (empty($rootId)) {
                    throw new ilCloudException(ilCloudException::FOLDER_NOT_EXISTING_ON_SERVICE, $rootFolder);
                }
            }
        } else {
		    // Even if a root id is already available, it still needs to be set again
            // to tackle ilObjCloudGUI's afterServiceAuth which always sets it to "root"
		    $rootId = $ilObjCloud->getRootId();
        }
        $this->getPluginObject()->setPublicLink($this->createSharingLink($rootId));
        $this->getPluginObject()->doUpdate();
        global $DIC;
        $DIC->ctrl()->setParameterByClass("ilcloudpluginsettingsgui", "root_id", $rootId);

		return true;
	}


    /**
     * set auth complete for all objects with the same owner
     */
	protected function setAuthComplete()
    {
        global $DIC;
        $DIC->database()->query('
                update il_cld_data 
                set auth_complete = 1
                where id IN (
                    select obj_id from object_data 
                    where service = \'OneDrive\' 
                      and owner_id = ' . $this->getPluginObject()->getCloudModulObject()->getOwnerId() . '
                )
        ');
    }

	/**
	 * @param ilCloudFileTree $file_tree
	 * @param string $parent_folder
	 *
	 * @throws Exception
	 */
	public function addToFileTreeWithId(ilCloudFileTree $file_tree, $parent_id) {
		try {
			$exodFiles = $this->getClient()->listFolder($parent_id);

			foreach ($exodFiles as $item) {
				$size = ($item instanceof exodFile) ? $size = $item->getSize() : null;
				$is_Dir = $item instanceof exodFolder;
				$path = $item->getFullPath();
				$file_tree->addIdBasedNode($path, $item->getId(), $parent_id, $is_Dir, strtotime($item->getDateTimeLastModified()), $size);
			}

		} catch (Exception $e) {
			$this->getPluginObject()->getCloudModulObject()->setAuthComplete(false);
			$this->getPluginObject()->getCloudModulObject()->update();
			throw $e;
		}
	}


    /**
     * @param $root_id
     *
     * @return bool
     * @throws ilCloudException
     */
    public function updateRootFolderPosition($root_id) {
	    $insertionPath = $this->getClient()->receivePathFromId($root_id);

        if ($this->getPluginObject()->getCloudModulObject()->getRootFolder() !== $insertionPath) {
            $this->getPluginObject()->getCloudModulObject()->setRootFolder($insertionPath);
            $this->getPluginObject()->getCloudModulObject()->update();
            return true;
        }

        return false;
    }


	/**
	 * @param null $path
	 * @param ilCloudFileTree $file_tree
	 */
	public function getFileById($id) {
		$this->getClient()->deliverFile($id);
	}


    /**
     * @param                 $file
     * @param                 $name
     * @param                 $parent_id
     *
     * @return mixed
     * @throws ilCloudException
     */
	public function putFileById($file, $name, $parent_id) {
		$return = $this->getClient()->uploadFile($parent_id, $name, $file);

		return $return;
	}


	/**
	 * @param null $path
	 * @param ilCloudFileTree $file_tree
	 *
	 * @return mixed
	 */
	public function createFolder($path = null, ilCloudFileTree $file_tree = null) {
		if ($file_tree instanceof ilCloudFileTree) {
			$path = ilCloudUtil::joinPaths($file_tree->getRootPath(), $path);
		}

		if ($path != '/') {
			return $this->getClient()->createFolderByPath($path);
		}

		return false;
	}


    /**
     * @param $id
     * @param $folder_name
     *
     * @return mixed
     */
    public function createFolderById($id, $folder_name) {
        return $this->getClient()->createFolderById($id, $folder_name);
    }


    /**
     * @param $id
     *
     * @return bool
     * @throws ilCloudException
     */
	public function deleteItemById($id) {
		return $this->getClient()->delete($id);
	}


    /**
     * @param $path
     *
     * @return exodFolder
     * @throws ilCloudException
     */
	public function getFolderObjectByPath($path) {
        return $this->getClient()->getFolderObjectByPath($path);
    }


    public function createSharingLink($id)
    {
        return $this->getClient()->createSharingLink($id);
    }


    public function getRootId($root_path)
    {
        return $this->getPluginObject()->getCloudModulObject()->getRootId();
    }


    /**
	 * @return ilOneDrive
	 */
	public function getPluginObject() {
		return parent::getPluginObject();
	}


	/**
	 * @return ilOneDrivePlugin
	 */
	public function getPluginHookObject() {
		return parent::getPluginHookObject();
	}
}
