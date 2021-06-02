<?php

namespace srag\Plugins\OneDrive\EventLog;

use ActiveRecord;
use ilCloudException;
use stdClass;

/**
 * Class EventLogEntry
 * @author Theodor Truffer <tt@studer-raimann.ch>
 */
class EventLogEntryAR extends ActiveRecord
{
    const DB_TABLE_NAME = 'exod_event_log';

    /**
     * @var int
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_primary   true
     * @con_sequence     true
     */
    protected $id;
    /**
     * @var int
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_is_notnull   true
     * @con_length       8
     */
    protected $obj_id;
    /**
     * @var string
     * @con_has_field    true
     * @con_fieldtype    timestamp
     * @con_is_notnull   true
     */
    protected $timestamp;
    /**
     * @var int
     * @con_has_field    true
     * @con_fieldtype    integer
     * @con_length       8
     * @con_is_notnull   true
     */
    protected $user_id;
    /**
     * @var EventType
     * @con_is_unique    true
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_is_notnull   true
     * @con_length       64
     */
    protected $event_type;
    /**
     * @var string
     * @con_is_unique    true
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_is_notnull   true
     * @con_length       512
     */
    protected $path;
    /**
     * @var ObjectType
     * @con_is_unique    true
     * @con_has_field    true
     * @con_fieldtype    text
     * @con_is_notnull   true
     * @con_length       64
     */
    protected $object_type;
    /**
     * @var array
     * @con_is_unique  true
     * @con_has_field  true
     * @con_fieldtype  text
     * @con_length     512
     */
    protected $additional_data = [];

    /**
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getObjId() : int
    {
        return $this->obj_id;
    }

    /**
     * @param int $obj_id
     */
    public function setObjId(int $obj_id)
    {
        $this->obj_id = $obj_id;
    }

    /**
     * @return string
     */
    public function getTimestamp() : string
    {
        return $this->timestamp;
    }

    /**
     * @param string $timestamp
     */
    public function setTimestamp(string $timestamp)
    {
        $this->timestamp = $timestamp;
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
    public function setUserId(int $user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * @return string
     */
    public function getPath() : string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path)
    {
        $this->path = $path;
    }

    /**
     * @return ObjectType
     */
    public function getObjectType() : ObjectType
    {
        return $this->object_type;
    }

    /**
     * @param ObjectType $object_type
     */
    public function setObjectType(ObjectType $object_type)
    {
        $this->object_type = $object_type;
    }

    /**
     * @return array
     */
    public function getAdditionalData() : array
    {
        return $this->additional_data;
    }

    /**
     * @param array $additional_data
     */
    public function setAdditionalData(array $additional_data)
    {
        $this->additional_data = $additional_data;
    }

    public function getConnectorContainerName()
    {
        return self::DB_TABLE_NAME;
    }

    /**
     * @return EventType
     */
    public function getEventType() : EventType
    {
        return $this->event_type;
    }

    /**
     * @param EventType $event_type
     */
    public function setEventType(EventType $event_type)
    {
        $this->event_type = $event_type;
    }

    /**
     * @param $field_name
     * @return mixed|string|null
     */
    public function sleep($field_name)
    {
        switch ($field_name) {
            case 'event_type':
                return $this->event_type->value();
            case 'object_type':
                return $this->object_type->value();
            case 'additional_data':
                return json_encode($this->additional_data);
            default:
                return null;
        }
    }

    /**
     * @param $field_name
     * @param $field_value
     * @return mixed|EventType|ObjectType|null
     * @throws ilCloudException
     */
    public function wakeUp($field_name, $field_value)
    {
        switch ($field_name) {
            case 'event_type':
                return EventType::fromValue($field_value);
            case 'object_type':
                return ObjectType::fromValue($field_value);
            case 'additional_data':
                return json_decode($field_value, true);
            default:
                return null;
        }
    }

}
