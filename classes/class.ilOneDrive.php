<?php
require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/classes/Auth/class.exodBearerToken.php');
require_once('./Modules/Cloud/classes/class.ilCloudPlugin.php');
require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/classes/App/class.exodAppBusiness.php');
require_once('./Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/classes/App/class.exodTenant.php');

/**
 * Class ilOneDrive
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class ilOneDrive extends ilCloudPlugin {

	/**
	 * @var exodApp
	 */
	protected static $app_instance;
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
	protected $valid_through = '';
	/**
	 * @var bool
	 */
	protected $allow_public_links = false;
	/**
	 * @var string
	 */
	protected $public_link = '';
	/**
	 * @var int
	 */
	protected $max_file_size = 200;



	public function create() {
		global $ilDB;

		$ilDB->insert($this->getTableName(), $this->getArrayForDb());
	}


	/**
	 * @param exodBearerToken $exodBearerToken
	 */
	public function storeToken(exodBearerToken $exodBearerToken) {
		$this->setAccessToken($exodBearerToken->getAccessToken());
		$this->setRefreshToken($exodBearerToken->getRefreshToken());
		$this->setValidThrough($exodBearerToken->getValidThrough());
		$this->doUpdate();
	}


	/**
	 * @return exodBearerToken
	 */
	public function getTokenObject() {
		$token = new exodBearerToken();
		if ($this->getAccessToken()) {
			$token->setAccessToken($this->getAccessToken());
			$token->setRefreshToken($this->getRefreshToken());
			$token->setValidThrough($this->getValidThrough());
		}

		return $token;
	}


	/**
	 * @return exodAppBusiness
	 */
	public function getExodApp() {
//		if (!isset(self::$app_instance)) {
			$inst = ilOneDrivePlugin::getInstance()->getExodApp($this->getTokenObject());
			if($inst->checkAndRefreshToken()) {
				$this->storeToken($inst->getExodBearerToken());
			}
			self::$app_instance = $inst;
//		}

		return self::$app_instance;
	}


	/**
	 * @return bool
	 */
	public function read() {
		global $ilDB;

		$set = $ilDB->query('SELECT * FROM ' . $this->getTableName() . ' WHERE id = ' . $ilDB->quote($this->getObjId(), 'integer'));
		$rec = $ilDB->fetchObject($set);
		if ($rec == NULL) {
			return false;
		} else {
			foreach ($this->getArrayForDb() as $k => $v) {
				$this->{$k} = $rec->{$k};
			}
		}
		$this->setMaxFileSize(500);

		return true;
	}


	public function doUpdate() {
		global $ilDB;
		$ilDB->update($this->getTableName(), $this->getArrayForDb(), array( 'id' => array( 'integer', $this->getObjId() ) ));
	}


	public function doDelete() {
		global $ilDB;

		$ilDB->manipulate('DELETE FROM ' . $this->getTableName() . ' WHERE ' . ' id = ' . $ilDB->quote($this->getObjId(), 'integer'));
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
	public function getValidThrough() {
		return $this->valid_through;
	}


	/**
	 * @param string $valid_through
	 */
	public function setValidThrough($valid_through) {
		$this->valid_through = $valid_through;
	}


	/**
	 * @return boolean
	 */
	public function isAllowPublicLinks() {
		return $this->allow_public_links;
	}


	/**
	 * @param boolean $allow_public_links
	 */
	public function setAllowPublicLinks($allow_public_links) {
		$this->allow_public_links = $allow_public_links;
	}


	/**
	 * @return int
	 */
	public function getMaxFileSize() {
		return $this->max_file_size;
	}


	/**
	 * @param int $max_file_size
	 */
	public function setMaxFileSize($max_file_size) {
		$this->max_file_size = $max_file_size;
	}


	/**
	 * @return string
	 */
	public function getPublicLink() {
		return $this->public_link;
	}


	/**
	 * @param string $public_link
	 */
	public function setPublicLink($public_link) {
		$this->public_link = $public_link;
	}


	/**
	 * @return array
	 */
	protected function getArrayForDb() {
		return array(
			'id' => array(
				'text',
				$this->getObjId()
			),
			'public_link' => array(
				'text',
				$this->getPublicLink()
			),
			'access_token' => array(
				'text',
				$this->getAccessToken()
			),
			'refresh_token' => array(
				'text',
				$this->getRefreshToken()
			),
			'max_file_size' => array(
				'text',
				$this->getMaxFileSize()
			),
			'valid_through' => array(
				'text',
				$this->getValidThrough()
			),
		);
	}
}

?>