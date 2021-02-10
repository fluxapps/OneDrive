<?php

namespace srag\Plugins\OneDrive\EventLog;

use ilCloudException;

/**
 * Class EventType
 * @package srag\Plugins\OneDrive\EventLog
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
class EventType
{
    const UPLOAD_STARTED = 'upload_started';
    const UPLOAD_COMPLETE = 'upload_complete';
    const UPLOAD_ABORTED = 'upload_aborted';
    const OBJECT_DELETED = 'object_deleted';
    const OBJECT_RENAMED = 'object_renamed';

    protected static $types = [
        self::UPLOAD_STARTED,
        self::UPLOAD_COMPLETE,
        self::UPLOAD_ABORTED,
        self::OBJECT_DELETED,
        self::OBJECT_RENAMED
    ];
    /**
     * @var string
     */
    protected $type;

    /**
     * EventType constructor.
     * @param string $type
     */
    protected function __construct(string $type)
    {
        $this->type = $type;
    }

    public static function uploadStarted() : self
    {
        return new self(self::UPLOAD_STARTED);
    }
    public static function uploadComplete() : self
    {
        return new self(self::UPLOAD_COMPLETE);
    }
    public static function uploadAborted() : self
    {
        return new self(self::UPLOAD_ABORTED);
    }
    public static function objectDeleted() : self
    {
        return new self(self::OBJECT_DELETED);
    }
    public static function objectRenamed() : self
    {
        return new self(self::OBJECT_RENAMED);
    }

    /**
     * @param string $type
     * @return static
     * @throws ilCloudException
     */
    public static function fromValue(string $type) : self
    {
        if (!in_array($type, self::$types)) {
            throw new ilCloudException('OneDrive EventLog: unknown object type "' . $type . '"');
        }
        return new self($type);
    }

    public function value() : string
    {
        return $this->type;
    }
}
