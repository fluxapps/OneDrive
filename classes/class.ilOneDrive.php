<?php
require_once('Customizing/global/plugins/Modules/Cloud/CloudHook/OneDrive/vendor/autoload.php');

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


    /**
     *
     */
	public function create() {
		global $ilDB;
		$ilDB->insert($this->getTableName(), $this->getArrayForDb());
	}

	/**
	 * @return exodBearerToken
	 */
	public function getTokenObject() {
	    return exodBearerToken::findOrGetInstanceForUser($this->getOwnerId());
	}


	/**
	 * @return exodAppBusiness
	 * @throws ilCloudException
	 */
	public function getExodApp() {
		$exodBearerToken = $this->getTokenObject();
		$inst = ilOneDrivePlugin::getInstance()->getExodApp($exodBearerToken);

        if (!$inst->isTokenValid()) {
            if (!$inst->checkAndRefreshToken()) {
                global $DIC;
                if ($DIC->user()->getId() == $this->getOwnerId()) {
                    if ($this->getCloudModulObject()->getAuthComplete()) {
                        $this->getCloudModulObject()->setAuthComplete(false);
                        $this->getCloudModulObject()->update();
                        throw new ilCloudException(ilCloudException::AUTHENTICATION_FAILED, 'Authentifizierung fehlgeschlagen. Bitte laden Sie die Seite erneut, um an die Authentifizierungsstelle weitergeleitet zu werden.');
                    }
                    return $inst;
                } else {
                    throw new ilCloudException(ilCloudException::AUTHENTICATION_FAILED, 'Der Ordner kann zur Zeit nur vom Besitzer geöffnet werden.');

                }
            }
        }
        return $inst;
	}


	/**
	 * @return bool
	 */
	public function read() {
		global $ilDB;

		$set = $ilDB->query('SELECT * FROM ' . $this->getTableName() . ' WHERE id = '
		                    . $ilDB->quote($this->getObjId(), 'integer'));
		$rec = $ilDB->fetchObject($set);
		if ($rec == null) {
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
		$ilDB->update($this->getTableName(), $this->getArrayForDb(), array(
			'id' => array(
				'integer',
				$this->getObjId(),
			),
		));
	}


	public function doDelete() {
		global $ilDB;

		$ilDB->manipulate('DELETE FROM ' . $this->getTableName() . ' WHERE ' . ' id = '
		                  . $ilDB->quote($this->getObjId(), 'integer'));
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
			'id'                 => array(
				'text',
				$this->getObjId(),
			),
			'public_link'        => array(
				'text',
				$this->getPublicLink(),
			),
			'max_file_size'      => array(
				'integer',
				$this->getMaxFileSize(),
			),
		);
	}
}
