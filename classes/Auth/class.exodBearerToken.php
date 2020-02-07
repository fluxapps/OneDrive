<?php

/**
 * Class exodBearerToken
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class exodBearerToken extends ActiveRecord {

    const DB_TABLE_NAME = 'cld_cldh_exod_token';
    /**
     * @var int
     *
     * @db_has_field        true
     * @db_fieldtype        integer
     * @db_length           8
     * @db_is_primary       true
     * @con_sequence        true
     */
    public $id;
    /**
     * @var int
     *
     * @db_has_field        true
     * @db_fieldtype        integer
     * @db_length           8
     * @db_is_unique        true
     */
    public $user_id;
    /**
     * @var string
     *
     * @db_has_field        true
     * @db_fieldtype        text
     * @db_length           2000
     */
    public $access_token = '';
    /**
     * @var string
     *
     * @db_has_field        true
     * @db_fieldtype        text
     * @db_length           2000
     */
    public $refresh_token = '';
    /**
     * @var int
     *
     * @db_has_field        true
     * @db_fieldtype        integer
     * @db_length           8
     */
    public $valid_through = 0;


    /**
     * @param int $user_id
     *
     * @return exodBearerToken
     */
    public static function findOrGetInstanceForUser($user_id)
    {
        $self = self::where(['user_id' => $user_id])->first() ?: new self();
        $self->setUserId($user_id);
        return $self;
    }

    /**
     * @return string
     */
    public function getConnectorContainerName()
    {
        return self::DB_TABLE_NAME;
    }


    /**
	 * @return bool
	 */
	public function isValid() {
		if (!$this->getAccessToken()) {
			//			return true;
		}
		if ((int)$this->getValidThrough() <= time() AND $this->getValidThrough() !== null) {
			$exodLog = exodLog::getInstance();
			$exodLog->write('Token no longer valid...');

			return false;
		}

		return true;
	}


	/**
	 * @param \exodAuth $exodAuth
	 * @return bool
	 */
	public function refresh(exodAuth $exodAuth) {
		if (!$this->getRefreshToken()) {
			return false;
		}
		if (!$this->isValid()) {
			$exodAuth->refreshToken($this);

			return true;
		}

		return false;
	}


    /**
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }


    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }


    /**
     * @return int
     */
    public function getUserId() : int
    {
        return $this->user_id;
    }


    /**
     * @param int $user_id
     */
    protected function setUserId(int $user_id)
    {
        $this->user_id = $user_id;
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
	 * @return int
	 */
	public function getValidThrough() {
		return $this->valid_through;
	}


	/**
	 * @param int $valid_through
	 */
	public function setValidThrough($valid_through) {
		$this->valid_through = $valid_through;
	}


    /**
     * reset token data
     */
    public function reset()
    {
        $this->setValidThrough(0);
        $this->setRefreshToken('');
        $this->setAccessToken('');
    }
}
