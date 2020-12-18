<?php

use League\OAuth2\Client\Token\AccessToken;

/**
 * Class exodToken
 *
 * @author Theodor Truffer <tt@studer-raimann.ch>
 */
class exodToken extends ActiveRecord
{

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
     * @db_length           4000
     */
    public $access_token;
    /**
     * @var string
     *
     * @db_has_field        true
     * @db_fieldtype        text
     * @db_length           4000
     */
    public $refresh_token;
    /**
     * @var int
     *
     * @db_has_field        true
     * @db_fieldtype        integer
     * @db_length           8
     */
    public $valid_through;


    /**
     * @param AccessToken $token
     */
    public function storeUserToken(League\OAuth2\Client\Token\AccessToken $token)
    {
        $this->setAccessToken($token->getToken());
        $this->setRefreshToken($token->getRefreshToken());
        $this->setValidThrough($token->getExpires());
        $this->store();
    }


    /**
     * @param $user_id
     *
     * @return ownclOAuth2UserToken
     */
    public static function getUserToken($user_id = 0)
    {
        if (!$user_id) {
            global $ilUser;
            $user_id = $ilUser->getId();
        }

        $token = self::where(array('user_id' => $user_id))->first();
        if (!$token) {
            $token = new self();
            $token->setUserId($user_id);
        }

        return $token;
    }


    /**
     *
     */
    public function flushTokens()
    {
        if ($this->getAccessToken() || $this->getRefreshToken() || $this->getValidThrough()) {
            $this->setAccessToken('');
            $this->setRefreshToken('');
            $this->setValidThrough(0);
            $this->update();
        }
    }


    /**
     * @return bool
     */
    public function isExpired()
    {
        return ((int) $this->getValidThrough() != 0) && ($this->getValidThrough() <= time());
    }


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }


    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->user_id;
    }


    /**
     * @param int $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }


    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->access_token;
    }


    /**
     * @param string $access_token
     */
    public function setAccessToken($access_token)
    {
        $this->access_token = $access_token;
    }


    /**
     * @return string
     */
    public function getRefreshToken()
    {
        return $this->refresh_token;
    }


    /**
     * @param string $refresh_token
     */
    public function setRefreshToken($refresh_token)
    {
        $this->refresh_token = $refresh_token;
    }


    /**
     * @return int
     */
    public function getValidThrough()
    {
        return $this->valid_through;
    }


    /**
     * @param int $valid_through
     */
    public function setValidThrough($valid_through)
    {
        $this->valid_through = $valid_through;
    }


    static function returnDbTableName()
    {
        return self::DB_TABLE_NAME;
    }
}
