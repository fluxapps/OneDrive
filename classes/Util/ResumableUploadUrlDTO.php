<?php

/**
 * Class ResumableUploadUrlDTO
 * @author Theodor Truffer <tt@studer-raimann.ch>
 */
class ResumableUploadUrlDTO
{
    /**
     * @var string
     */
    protected $upload_url;
    /**
     * @var string
     */
    protected $expiration_date_time;

    /**
     * ResumableUploadUrlDTO constructor.
     * @param string $upload_url
     * @param string $expiration_date_time
     */
    public function __construct(string $upload_url, string $expiration_date_time)
    {
        $this->upload_url = $upload_url;
        $this->expiration_date_time = $expiration_date_time;
    }

    public static function fromStdClass(stdClass $stdClass) : self
    {
        return new self($stdClass->uploadUrl, $stdClass->expirationDateTime);
    }

    public function toStdClass() : stdClass
    {
        $std_class = new stdClass();
        $std_class->uploadUrl = $this->getUploadUrl();
        $std_class->expirationDateTime = $this->getExpirationDateTime();
        return $std_class;
    }

    public function toJson() : string
    {
        return json_encode($this->toStdClass());
    }

    /**
     * @return string
     */
    public function getUploadUrl() : string
    {
        return $this->upload_url;
    }

    /**
     * @return string
     */
    public function getExpirationDateTime() : string
    {
        return $this->expiration_date_time;
    }


}
