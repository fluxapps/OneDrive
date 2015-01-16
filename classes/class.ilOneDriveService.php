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
	 * @var exodAuth
	 */
	protected $auth;


	/**
	 * @return exodAuth
	 */
	public function getAuth() {
		if (!$this->auth) {
			$this->auth = exodAuthFactory::getAuthInstance(ilOneDrivePlugin::getInstance()->getApp());
		}

		return $this->auth;
	}


	/**
	 * @param string $callback_url
	 */
	public function authService($callback_url = "") {
		$this->getAuth()->authenticate(htmlspecialchars_decode($callback_url));
	}


	public function afterAuthService() {
		$exodAuth = exodAuth::getInstanceFromSession();
		$this->getPluginObject()->setAccessToken($exodAuth->getResponse()->getAccessToken());
		$this->getPluginObject()->setRefreshToken($exodAuth->getResponse()->getAccessToken());
		$this->getPluginObject()->setValidThrough($exodAuth->getResponse()->getExpiresOn());

		$this->getPluginObject()->doUpdate();

		//$this->createFolder($this->getPluginObject()->getCloudModulObject()->getRootFolder());
		return true;
	}


	/**
	 * @param ilCloudFileTree $file_tree
	 * @param string          $parent_folder
	 */
	public function addToFileTree(ilCloudFileTree  &$file_tree, $parent_folder = "/") {
		$client = $this->getClient();

		foreach ($client->listFolder($parent_folder) as $item) {
			if ($item instanceof exodFolder) {
				//				$file_tree->addFolderToService($item->getId(), $item->getName());
			}
			$size = ($item instanceof exodFile) ? $size = $item->getSize() : NULL;

			//			echo '<pre>' . print_r($item, 1) . '</pre>';
			$file_tree->addNode($item->getFullPath(), $item->getId(), ($item instanceof
				exodFolder), strtotime($item->getDateTimeLastModified()), $size);
		}
		$file_tree->setLoadingOfFolderComplete($parent_folder);
	}


	/**
	 * @param null            $path
	 * @param ilCloudFileTree $file_tree
	 */
	public function getFile($path = NULL, ilCloudFileTree $file_tree = NULL) {
		$this->getClient()->deliverFile($path);
	}


	/**
	 * @param null            $path
	 * @param ilCloudFileTree $file_tree
	 */
	public function createFolder($path = NULL, ilCloudFileTree $file_tree = NULL) {
		$path = ilCloudUtil::joinPaths($file_tree->getRootPath(), $path);

		var_dump($file_tree->getNodeFromId('root')); // FSX

		$this->getClient()->createFolder($path);

		return true;
	}


	/**
	 * @param null            $path
	 * @param ilCloudFileTree $file_tree
	 */
	public function deleteItem($path = NULL, ilCloudFileTree $file_tree = NULL) {
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


	public function getRootId($root_path) {
		return '';
	}


	/**
	 * @return exodClientBusiness
	 */
	protected function getClient() {
		$client = exodClientFactory::getClientInstance($this->getPluginHookObject()->getApp(), $this->getPluginObject()->getTokenObject());

		return $client;
	}
}

?>