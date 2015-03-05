<?php
require_once("./Modules/Cloud/classes/class.ilCloudPluginService.php");
require_once('./Modules/Cloud/exceptions/class.ilCloudException.php');
require_once("./Modules/Cloud/classes/class.ilCloudUtil.php");
require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/classes/Auth/class.exodAuthFactory.php');
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
	 * @return exodClientBusiness
	 */
	protected function getClient() {
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
	 */
	public function authService($callback_url = "") {
		$this->getAuth()->authenticate(htmlspecialchars_decode($callback_url));
	}


	public function afterAuthService() {
		$exodAuth = $this->getApp()->getExodAuth();
		$exodAuth->loadTokenFromSession();
		$this->getPluginObject()->storeToken($exodAuth->getExodBearerToken());

		$this->createFolder($this->getPluginObject()->getCloudModulObject()->getRootFolder()); // TODO

		return true;
	}


	/**
	 * @param ilCloudFileTree $file_tree
	 * @param string          $parent_folder
	 */
	public function addToFileTree(ilCloudFileTree  &$file_tree, $parent_folder = "/") {
		foreach ($this->getClient()->listFolder($parent_folder) as $item) {
			if ($item instanceof exodFolder) {
				// $file_tree->addFolderToService($item->getId(), $item->getName());
			}
			$size = ($item instanceof exodFile) ? $size = $item->getSize() : NULL;
			$is_Dir = $item instanceof exodFolder;
			//			throw new ilCloudException(-1, $item->getFullPath());
			$file_tree->addNode($item->getFullPath(), $item->getId(), $is_Dir, strtotime($item->getDateTimeLastModified()), $size);
			//			echo "!";
		}
		$file_tree->setLoadingOfFolderComplete($parent_folder);
		//		$file_tree->clearFileTreeSession();
	}


	/**
	 * @param null            $path
	 * @param ilCloudFileTree $file_tree
	 */
	public function getFile($path = NULL, ilCloudFileTree $file_tree = NULL) {
		$this->getClient()->deliverFile($path);
	}


	/**
	 * @param                 $file
	 * @param                 $name
	 * @param string          $path
	 * @param ilCloudFileTree $file_tree
	 *
	 * @return mixed
	 */
	public function putFile($file, $name, $path = '', ilCloudFileTree $file_tree = NULL) {
		$path = ilCloudUtil::joinPaths($file_tree->getRootPath(), $path);
		if ($path == '/') {
			$path = '';
		}

		return $this->getClient()->uploadFile($path . "/" . $name, $file);
	}


	/**
	 * @param null            $path
	 * @param ilCloudFileTree $file_tree
	 *
	 * @return bool
	 */
	public function createFolder($path = NULL, ilCloudFileTree $file_tree = NULL) {
		if ($file_tree instanceof ilCloudFileTree) {
			$path = ilCloudUtil::joinPaths($file_tree->getRootPath(), $path);
		}
		if ($path != '/') {
			$this->getClient()->createFolder($path);
		}

		return true;
	}


	/**
	 * @param null            $path
	 * @param ilCloudFileTree $file_tree
	 *
	 * @return bool
	 */
	public function deleteItem($path = NULL, ilCloudFileTree $file_tree = NULL) {
		$path = ilCloudUtil::joinPaths($file_tree->getRootPath(), $path);

		return $this->getClient()->delete($path);
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

	//
	//	public function getRootId($root_path) {
	//		return '';
	//	}
}

?>